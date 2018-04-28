<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2017/11/10
 * Time: 14:51
 * 华贵人寿保险产品处理：详情，算费，核保，支付，承保，出单等。
 */
namespace App\Http\Controllers\ApiControllers\Curls;

use App\Helper\LogHelper;
use DB;
use App\Models\Insurance;
use App\Models\Tariff;
use App\Helper\ExcelHelper;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Ixudra\Curl\Facades\Curl;
use SoapBox\Formatter\Formatter;
use App\Repositories\RestrictGeneRepository;
use App\Repositories\InsuranceApiFromRepository;
use App\Repositories\InsuranceAttributesRepository;
use App\Http\Controllers\ApiControllers\Curls\WkInsCurlController;
use App\Helper\AesCrypt;
use App\Models\InsOrder;
use App\Models\ApiFrom;
use App\Models\Policy;
use App\Models\Insure;
use App\Models\User;
use App\Models\OrderFinance;
use App\Models\InsApiBrokerage;


class HgInsCurlController
{
    protected $request;
	
    protected $api_m_info;
	
    protected $request_url;
    //接口地址(测试)
    const API_INSURE_URL = 'http://112.74.235.90:8087/dsc/third';
    //Sign=Md5(Base64(xml) + key)
    const API_INSURE_URL_KEY = 'a5f41dd26e8e2db4';
    //测试环境请求支付请求地址：
    const API_PAY_URL = 'https://wwwtest.huaguilife.cn/sinosoft-pay-web-gateway/scanPay/bankpayforchannel';
    // 生产环境查询支付请求地址:
    const API_PAY_URL_PRODUCT = 'http://112.74.228.113/sinosoft-pay-web-gateway/scanPay/bankpayforchannel';
    // 测试环境查询支付请求地址:
    const API_PAY_SEL_URL = 'http://wwwtest.huaguilife.cn/sinosoft-pay-web-gateway/scanPay/queryForPayResult';
    // 生产环境查询支付请求地址:
    const API_PAY_SEL_URL_PRODUCT = 'http://112.74.228.113/sinosoft-pay-web-gateway/scanPay/queryForPayResult';






    /**
     * 初始化
     *
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->sign_help = new RsaSignHelp();
        $this->private_key = file_get_contents('../config/rsa_private_key_1024_pkcs8.pem');
        $this->public_key = file_get_contents('../config/rsa_public_key_1024_pkcs8.pem');
        $this->wk_public_key = file_get_contents('../config/wk_rsa_public_key.pem');
        if(!preg_match("/call_back/",$this->request->url())) {
            $this->original_data = $this->decodeOriginData();
            //业务参数 解析出源数据json字符串
            $repository = new InsuranceApiFromRepository();
            //天眼产品ID
            $insurance_id = isset($this->original_data['ty_product_id']) ? $this->original_data['ty_product_id'] : 0;
            //内部产品唯一码
            $private_p_code = isset($this->original_data['private_p_code']) ? $this->original_data['private_p_code'] : 0;
            if ($insurance_id) {
                $this->bind = $repository->getApiStatusOn($insurance_id);    //获取当前正在启用的API来源信息
            } else {
                $this->bind = $repository->getApiByPrivatePCode($private_p_code);   //通过内部产品唯一码获得相关信息
            }
            if (empty($this->bind)) {
                return ['data' => 'product not exist', 'code' => 400];
            }
        }
    }

    /**
     * 产品详情
     *
     */
    public function getApiOption()
    {
        $insuranceAttributesRepository = new InsuranceAttributesRepository();
        $restrictGeneRepository = new RestrictGeneRepository();
        $input = $this->original_data;
        $insurance_id = $input['ty_product_id'];
        $result['ty_product_id'] = $insurance_id;
        $result['private_p_code'] = $this->bind ->private_p_code;
        $result['bind_id'] = $this->bind ->id;
        //投保属性
        $insurance_attributes = $insuranceAttributesRepository->findAttributesRecursionByBindId($this->bind ->id);
        foreach ($insurance_attributes as &$insurance_attribute) {
            if (isset($insurance_attribute['productAttributes'])) {
                foreach ($insurance_attribute['productAttributes'] as &$insurance_attribute) {
                    if (isset($insurance_attribute['attributeValues'])) {
                        foreach ($insurance_attribute['attributeValues'] as &$value) {
                            unset($value['controlValue']);
                        }
                    }
                    unset($insurance_attribute['apiName']);
                }
            }
        }
        $result['option']['insurance_attributes'] = $insurance_attributes;
        //试算因子对应选项
        $restrict_genes = $restrictGeneRepository->findRestrictGenesRecursionByBindId($this->bind ->id);
        $result['option']['restrict_genes'] = $this->onlyTyKey($restrict_genes);
        //获得默认试算选项
        $result['option']['selected_options'] = $restrictGeneRepository->findDefaultRestrictGenes($this->bind ->id);
        $result['option']['selected_options'] = $this->onlyTyKey($result['option']['selected_options']);
        $result['option']['area'] = json_decode(config('hg_msg.area'),true);//地区信息
        $result['option']['jobs'] = json_decode(config('hg_msg.job'),true);//职业信息
        $result['option']['bank'] = config('hg_msg.bank');//银行信息
        $result['option']['pay_code'] = config('hg_msg.pay_code');//支付状态码
        //todo   保障权益，还没获取，算费价格待定
//        $default_quote = $this->formatQuote($result['option']['selected_options']);
//        dd($default_quote);
//        //获得默认保费及保障内容
//        $result['option']['price'] = $default_quote->price;
        $min_tariff= DB::table('hg_tariff')->min('tariff');
        $min_tariff = 6.2;
//        $results = DB::table('hg_tariff')->orderBy('tariff')->get();
//        $min_tariff = $results->tariff;
        $result['option']['price'] = $min_tariff*10000;//单位为分
//        //保障内容
//        $result['option']['protect_items'] = $default_quote->protect_items;
        $result['option']['protect_items'] = '';
        return ['data'=> $result, 'code'=> 200];
    }

    /**
     * 算费
     *
     */
    public function quote()
    {
        $msg = [];
        $original_data = $this->original_data;
        // 格式化键值对
        $api_data = [];
        $new_val = json_decode($original_data['new_val'], true);
        foreach ($new_val as $item) {
            if (isset($item['value'])) {
                $api_data[$item['key']] = $item['value'];
            } else {
                $api_data[$item['key']] = '';
            }
        }
        $where = [];
        if(isset($api_data['ty_sex'])){
            if($api_data['ty_sex']=='男'){
                $where['sex'] = '0';
            }else{
                $where['sex'] = '1';
            }
        }else{
            $where['sex'] = '';
        }
        if(isset($api_data['ty_pay_way'])){
            if($api_data['ty_pay_way']=='趸交'){
                $where['period'] = '1000';
            }elseif($api_data['ty_pay_way']=='5年'){
                $where['period'] = '5';
            }elseif($api_data['ty_pay_way']=='10年'){
                $where['period'] = '10';
            }elseif($api_data['ty_pay_way']=='20年'){
                $where['period'] = '20';
            }elseif($api_data['ty_pay_way']=='30年'){
                $where['period'] = '30';
            }
        }else{
            $where['period'] = '';
        }
        if(isset($api_data['ty_pay_way'])){
            if(!empty($api_data['ty_birthday'])){
//                $y = explode('-',$api_data['ty_birthday'])[0];
//                $m = explode('-',$api_data['ty_birthday'])[1];
//                $d = explode('-',$api_data['ty_birthday'])[2];
//                $ys = date('Y',time());
//                $ms = date('m',time());
//                $ds = date('d',time());
//                $age_y = $ys-$y;
//                $age_m = $ms<$m ? 1 : 0;
//                $where['age'] = $age_y-$age_m;
                $age = $this->calcAge($api_data['ty_birthday']);
                $where['age'] = $age;
            }
        }else{
            $where['age'] = '';
        }
        //选中项处理
        $selected_options = json_decode($original_data['new_val'], true);
        // 替换"key"为"ty_key"
        foreach ($selected_options as &$item) {
            $item['ty_key'] = $item['key'];
            unset($item['key']);
        }
        //本地数据库算费查询
        $quote = DB::table('hg_tariff')->where($where)->first();
        if(empty($quote)){
            $msg['data']['price'] = '投保年龄不符，请注意出生日期';//最低620元起
            $msg['data']['selected_options'] = $selected_options;
            $msg['code'] = 200;
            return $msg;
        }
        $msg['data']['price'] = $quote->tariff*10000;
        $msg['data']['selected_options'] = $selected_options;
        $msg['code'] = 200;
        return $msg;
    }
    /**
     * 由出生日期获取年龄
     *
     */
    public function calcAge($birthday) {
        $iage = 0;
        if (!empty($birthday)) {
            $year = date('Y',strtotime($birthday));
            $month = date('m',strtotime($birthday));
            $day = date('d',strtotime($birthday));

            $now_year = date('Y');
            $now_month = date('m');
            $now_day = date('d');

            if ($now_year > $year) {
                $iage = $now_year - $year - 1;
                if ($now_month > $month) {
                    $iage++;
                } else if ($now_month == $month) {
                    if ($now_day >= $day) {
                        $iage++;
                    }
                }
            }
        }
        return $iage;
    }

    /**
     * 新单核保接口
     * @url http://112.74.235.90:8087/dsc/third/i/insure/5001
     * todo  没有做重复投保的校验限制
     */
    public function buyIns()
    {
        $input = $this->decodeOriginData(); //解签获取源数据
		$input['insurance_attributes']['ty_toubaoren']['ty_toubaoren_sex']= substr($input['insurance_attributes']['ty_toubaoren']['ty_toubaoren_id_number'],-2,1)%2==0?1:0;//身份证  偶数为女  奇数为男   0男1女
        $input['insurance_attributes']['ty_beibaoren'][0]['ty_beibaoren_sex'] = substr($input['insurance_attributes']['ty_beibaoren'][0]['ty_beibaoren_id_number'],-2,1)%2==0?1:0;
        $input['insurance_attributes']['ty_toubaoren']['ty_toubaoren_area'] =  $input['insurance_attributes']['ty_toubaoren']['ty_toubaoren_area']??"110000,110100,110101";
        $data = $this->ins_key_value_change($input);//内外键值转化
        //解锁接口
        $InsuredCardNo = $data['policyInsuredList'][0]['InsuredCardNo'];//被保人证件号
        $insure_lock_res = Insure::where('card_id',$InsuredCardNo)
            ->where('status','<>','pay_end')
            ->select('union_order_code')
            ->get();
        if(!empty($insure_lock_res)&&count($insure_lock_res)!=0){
            $lock_res = $this->deletePrt($insure_lock_res);//触发解锁
            LogHelper::logSuccess($lock_res,'lock_res_'.$InsuredCardNo);
        }
        //内部交易订单信息
        $TranNo = substr(time().rand(1000, 9999),'0',9);//交易号（流水号）
        $TranDate = date('Ymd',time());//交易日期
        $TranTime = date('His',time());//交易时间
        $ProposalContNo_top = '109017';
        $ProposalPrtNo = $ProposalContNo_top.$TranNo;//投保单号
        $ProposalContNo = $ProposalPrtNo;//保单号
        $PolApplyDate = date('Ymd',time());//投保日期
        $SaleChnl = '05';//渠道代码
        $SaleChnlDetail = '06';
        $SaleChnlOperator = 'wcb';//操作人：旺财保
        $isTparty = 'Y';//是否第三方
        $key = self::API_INSURE_URL_KEY;//测试环境
        $CValiDate = '';
        $order_xml = '<Order>
                        <TranNo>'.$TranNo.'</TranNo>
                        <TranDate>'.$TranDate.'</TranDate>
                        <TranTime>'.$TranTime.'</TranTime>
                        <TranCom>'.$ProposalPrtNo.'</TranCom>
                        <ProposalContNo>'.$ProposalContNo.'</ProposalContNo>
                        <ProposalPrtNo>'.$ProposalContNo.'</ProposalPrtNo>
                        <PolApplyDate>'.$PolApplyDate.'</PolApplyDate>
                        <SaleChnl>'.$SaleChnl.'</SaleChnl>
                        <SaleChnlDetail>'.$SaleChnlDetail.'</SaleChnlDetail>
                        <SaleChnlOperator>'.$SaleChnlOperator.'</SaleChnlOperator>
                        <isTparty>'.$isTparty.'</isTparty>
                    </Order>';
        //投保人信息
        $holder_name = $data['policyHolder']['HolderName'];
        $holder_sex = $data['policyHolder']['HolderSex'];//0男,1女
		if($holder_sex=='1'){
			 $holder_sex = '0';
		}elseif($holder_sex=='0'){
			$holder_sex='1';
		}
		
        $holder_birthday = $data['policyHolder']['HolderBirthday'];
        $holder_card_no = $data['policyHolder']['HolderCardNo'];
		$holder_sex = $this->getUserSex($holder_card_no);
        $holder_data = $this->getMSgByID($holder_card_no);
        if($holder_data['age']>80||$holder_data['age']<18){
            $msg['data'] = '投保人年龄大于80周岁或者小于18周岁！';
            $msg['code'] = 405;
            return $msg;
        }
        $holder_card_type = '0';//仅支持身份证
        $holder_email = $data['policyHolder']['HolderEmail'];
        $holder_mobile = $data['policyHolder']['HolderMobile'];
        $rela_to_insured = $data['policyInsuredList'][0]['InsuredRelation'];//00本人,01父母,02配偶,03子女
        $app_zone = explode(',',$data['policyHolder']['AppZone'])[2]??"";//投保区域,城市名称编码
        $app_tall = "";//投保人身高
        $app_weight = "";//投保人体重 BMI <18或BMI>33拒绝承保
        $app_job_type = explode('-',$data['policyHolder']['AppJobType'])[1]??"";//职业代码
        $app_health_flag = $data['policyHolder']['AppHealthFlag']??"F";//健康告知，有F,无，N,F拒保
        $app_native = 'CHN';//国籍
        $AppProvince = explode(',',$data['policyHolder']['AppZone'])[0]??"";
        $AppCity = explode(',',$data['policyHolder']['AppZone'])[1]??"";
        $AppCounty= explode(',',$data['policyHolder']['AppZone'])[2]??"";
        $AppAddress= $data['policyHolder']['AppAddress'];
        //非必传
        $holder_card_start_date = '2009-01-01';//证件有效期（起）
        $holder_card_end_date = '2019-01-01';//证件有效前（止）
        $app_income = '10';//年收入（万元）, 如果投被保人是本人，必传
        $home_address = '北京市东城区';//投保人通讯地址
        $holder_xml = '<HolderInfo>
                            <HolderName>'.$holder_name.'</HolderName>
                            <HolderSex>'.$holder_sex.'</HolderSex>
                            <HolderBirthday>'.$holder_birthday.'</HolderBirthday>
                            <HolderCardNo>'.$holder_card_no.'</HolderCardNo>
                            <HolderCardType>'.$holder_card_type.'</HolderCardType>
                            <HolderEmail>'.$holder_email.'</HolderEmail>
                            <HolderMobile>'.$holder_mobile.'</HolderMobile>
                            <AppProvince>'.$AppProvince.'</AppProvince>
                            <AppCity>'.$AppCity.'</AppCity>
                            <AppCounty>'.$AppCounty.'</AppCounty>
                            <AppAddress>'.$AppAddress.'</AppAddress>
                            <HolderCardEndDate>2022-01-29</HolderCardEndDate>
                            <RelaToInsured>'.$rela_to_insured.'</RelaToInsured>
                            <AppZone>'.$app_zone.'</AppZone>
                            <AppWeight>'.$app_weight.'</AppWeight>
                            <AppJobType>'.$app_job_type.'</AppJobType>
                            <AppTall>'.$app_tall.'</AppTall>
                            <AppHealthFlag>'.$app_health_flag.'</AppHealthFlag>
                            <AppNative>'.$app_native.'</AppNative>
                       </HolderInfo>';
        //被保人信息
        $InsuredRelation = $data['policyInsuredList'][0]['InsuredRelation'] ??  '00';//被保人与投保人关系//00本人,01父母,02配偶,03子女
        $InsuredCardNo = $data['policyInsuredList'][0]['InsuredCardNo'];	//被保人证件号
		$InsuredSex = $this->getUserSex($InsuredCardNo);
        $InsuredData = $this->getMSgByID($InsuredCardNo);
        if($InsuredData['age']>70||$InsuredData['age']<18){
            $msg['data'] = '投保人年龄大于70周岁或者小于18周岁！';
            $msg['code'] = 405;
            return $msg;
        }
        if($InsuredRelation=='01'&&$InsuredData['age']-$holder_data['age']<18){
            $msg['data'] = '被保人是投保人的父母，投被保人年龄差距不得小于18周岁！';
            $msg['code'] = 405;
            return $msg;
        }
        if($InsuredRelation=='02'&&$InsuredSex==$holder_sex){
            $msg['data'] = '投被保人的关系是配偶，性别必须是一男一女';
            $msg['code'] = 405;
            return $msg;
        }
        if($InsuredRelation=='03'&&$holder_data['age']-$InsuredData['age']<18){
            $msg['data'] = '投保人是被保人的父母，投被保人年龄差距不得小于18周岁！';
            $msg['code'] = 405;
            return $msg;
        }
        $InsuredCardType = '0';//	被保人证件类型		目前仅支持身份证
        $InsuredName = $data['policyInsuredList'][0]['InsuredName'];	//被保人姓名
        $InsuredBirthday = $data['policyInsuredList'][0]['InsuredBirthday'];//	被保人生日		格式:yyyy-MM-dd
        $InsuJobType = explode('-',$data['policyInsuredList'][0]['InsuJobType'])[1]??"";	//被保人职业代码、职业类别编码见接口码表
        $InsuTall = $data['policyInsuredList'][0]['InsuTall'] ;	//被保人身高
        $InsuWeight = $data['policyInsuredList'][0]['InsuWeight'] ;	//被保人体重、BMI <18或BMI>33拒绝承保  渠道前端控制
        $insure_tall = $InsuTall/100;
        $BMI = $InsuWeight/($insure_tall*$insure_tall);
        if($BMI<18||$BMI>33){
            $msg['data'] = '被保人身高体重BMI值超标，不予承保！';
            $msg['code'] = 405;
            return $msg;
        }
        $InsuZone = explode('-',$data['policyInsuredList'][0]['InsuZone'])[2]??"";//被保人区域、录入的城市名称编码，用此来判断是否为一线城市
        $InsuHealthFlag = $data['policyInsuredList'][0]['InsuHealthFlag']??'F';	//被保人健康告知、有：F  无：N//F拒保 可由渠道前端控制
        $InsuIncome = $data['policyInsuredList'][0]['InsuIncome'];//被保人年收入	单位/万元，当前除华贵定期寿险和华贵终身寿险外，其他产品传默认值5
		 if($InsuIncome<=0){
            $msg['data'] = '被保人年收入不符合被保规则，不予承保！';
            $msg['code'] = 405;
            return $msg;
        }
        $InsuNative	= 'CHN';//被保人国籍	传编码，如中国传CHN ，详见接口码表
        //非必传
        $InsuRgtAddress = '中国';//被保人通讯地址
        $InsuredMobile = $data['policyInsuredList'][0]['InsuredMobile'];	//被保人电话号码
        $InsuredEmail = $data['policyInsuredList'][0]['InsuredEmail'];//被保人邮箱
        $InsuredCardStartDate = '';//	被保人证件有效期起期	格式:yyyy-MM-dd
        $InsuredCardEndDate = '';	//被保人证件有效期止期		格式:yyyy-MM-dd
        $InsuProvince = explode('-',$data['policyInsuredList'][0]['InsuZone'])[0]??"";
        $InsuCity = explode('-',$data['policyInsuredList'][0]['InsuZone'])[1]??"";
        $InsuCounty = explode('-',$data['policyInsuredList'][0]['InsuZone'])[2]??"";
        $InsuAddress =  $data['policyInsuredList'][0]['InsuAddress'];
        $insured_xml = '<InsuredInfo>
                            <InsuredBirthday>'.$InsuredBirthday.'</InsuredBirthday>
                            <InsuredCardNo>'.$InsuredCardNo.'</InsuredCardNo>
                            <InsuredCardType>'.$InsuredCardType.'</InsuredCardType>
                            <InsuredName>'.$InsuredName.'</InsuredName>
                            <InsuredRelation>'.$InsuredRelation.'</InsuredRelation>
                            <InsuredSex>'.$InsuredSex.'</InsuredSex>
                            <InsuNative>'.$InsuNative.'</InsuNative>
                            <InsuJobType>'.$InsuJobType.'</InsuJobType>
                            <InsuZone>'.$InsuZone.'</InsuZone>
                            <InsuTall>'.$InsuTall.'</InsuTall>
                            <InsuWeight>'.$InsuWeight.'</InsuWeight>
                            <InsuHealthFlag>'.$InsuHealthFlag.'</InsuHealthFlag>
                            <InsuIncome>'.$InsuIncome.'</InsuIncome>
                            <InsuredEmail>'.$InsuredEmail.'</InsuredEmail>
                            <InsuredMobile>'.$InsuredMobile.'</InsuredMobile>
                            <InsuredName>'.$InsuredName.'</InsuredName>
                            <InsuProvince>'.$InsuProvince.'</InsuProvince>
                            <InsuCity>'.$InsuCity.'</InsuCity>
                            <InsuCounty>'.$InsuCounty.'</InsuCounty>
                            <InsuAddress>'.$InsuAddress.'</InsuAddress>
                            <InsuredCardEndDate>'.$InsuredCardEndDate.'</InsuredCardEndDate>
                        </InsuredInfo>';
        // //受益人信息  todo 当受益人为法定的时候不需要传这个节点与里面的内容，受益人非法定需要传
        // $BnfType = $data['bnf']['BnfType'];//受益人类型		0:生存受益人1:身故受益人
        // $BnfNo = $data['bnf']['BnfNo'];//受益顺序
        // $Name = $data['bnf']['Name'];//受益人姓名
        // $IDNo = $data['bnf']['IDNo'];//证件号
        // $IDType = $data['bnf']['IDType'];//	证件类型	支持身份证之外的证件类型
        // $Address = $data['bnf']['Address'];//受益人地址
        // $Sex = $data['bnf']['Sex'];//受益人性别	0男1女
        // $Birthday = $data['bnf']['Birthday'];//受益人生日格式:yyyy-MM-dd
        // $BnfLot = $data['bnf']['BnfLot'];//受益人比例
        // //非必传
        // $IDValiDateType = '1';//	证件有效期标志		0短期1长期
        // $IDEndDate = '2029-10-10';//	证件有效期
        // $BnfGrade = '';//	受益人级别	需支持多受益顺序
        // $IDAddress = '北京市';//	证件地址
        // $BelongToInsured = '00';//与被保人关系,00本人,01父母,02配偶,03子女,04祖孙,05法定监护人,06其他,09雇佣关系
        // $bnf_info = '<LCBnfs>
                        // <LCBnf>
                            // <BnfType>'.$BnfType.'</BnfType>
                            // <Address>'.$Address.'</Address>
                            // <Sex>'.$Sex.'</Sex>
                            // <Birthday>'.$Birthday.'</Birthday>
                            // <BnfLot>'.$BnfLot.'</BnfLot>
                            // <IDType>'.$IDType.'</IDType>
                            // <BnfNo>'.$BnfNo.'</BnfNo>
                            // <Name>'.$Name.'</Name>
                            // <IDNo>'.$IDNo.'</IDNo>
                        // </LCBnf>                
			        // </LCBnfs>';
        //产品信息和算费参数
        $CValiDate = $data['base']['CValiDate'];
        if((strtotime($CValiDate)-time())>24*3600){
            $SpecifyValiDate = 'Y';//是否可指定生效日期	不传默认是N，第二天生效，Y时能指定生效日
        }else{
            $SpecifyValiDate = 'N';//是否可指定生效日期	不传默认是N，第二天生效，Y时能指定生效日
        }
        $RiskCode = '012C0100';//产品编码华贵产品编码
        $Amnt	 = '10000000';//总保额,单位分
        $Prem = $input['price'];
        $Mult	 = '1';//总份数
        $InsuYear = '105';//保险期间,保五年时传5，依此推；终身时传105
        $quote_selected = json_decode($input['quote_selected']);
        $quote_select_data = [];
        foreach ($quote_selected as $value){
            if($value->ty_key == 'ty_pay_way'){
                $quote_select_data['ty_pay_way'] = $value->value;
            }
            if($value->ty_key == 'ty_duration_period_value'){
                $quote_select_data['ty_duration_period_value'] =  $value->value;
            }
            if($value->ty_key == 'ty_birthday'){
                $quote_select_data['ty_birthday'] = $value->value;
            }
            if($value->ty_key == 'ty_sex'){
                $quote_select_data['ty_sex'] = $value->value;
            }
            if($value->ty_key == 'ty_age'){
                $quote_select_data['ty_age'] = $value->value;
            }
        }
        $PayEndYearFlag = 'Y';//缴费期间标志,M月、D日、Y年、趸交Y
        $InsuYearFlag = 'Y';//保险期间标志,M月、D日、Y年、趸交Y
        if($quote_select_data['ty_pay_way']=="趸交"){
            $PayIntv = '0';//交费方式,1:不定期交,0:趸交,1:月交3:季交6:半年交12:年交
            $PayEndYear = '1000'; //缴费期间,年交时就是几年，例：两年交就是2;趸交时为1000
        }else{
           $PayIntv = '12';//交费方式,1:不定期交,0:趸交,1:月交3:季交6:半年交12:年交
           $PayEndYear = substr($quote_select_data['ty_pay_way'],'0','-3'); //缴费期间,年交时就是几年，例：两年交就是2;趸交时为1000
       }
        //非必传
        $MainRiskCode = '';//主产品编码
        $Years = '';//天数
        $risk_xml = ' <Risks>
                            <Risk>
                              <SpecifyValiDate>'.$SpecifyValiDate.'</SpecifyValiDate>
                              <RiskCode>'.$RiskCode.'</RiskCode><!--华贵终身寿产品编码-->
                              <CValiDate>'.$CValiDate.'</CValiDate><!--生效日期-->
                              <Amnt>'.$Amnt.'</Amnt>
                              <Prem>'.$Prem.'</Prem>
                              <Mult>'.$Mult.'</Mult>
                              <Years>'.$InsuYear.'</Years>
                              <InsuYearFlag>A</InsuYearFlag><!--终身寿固定A-->
                              <InsuYear>105</InsuYear><!--终身寿固定105-->
                              <PayEndYearFlag>'.$PayEndYearFlag.'</PayEndYearFlag><!--交费方式年限标志Y-->
                              <PayEndYear>'.$PayEndYear.'</PayEndYear><!--交费年期，1000：一次交清，5,10,20,30 五年交...-->
                              <PayIntv>'.$PayIntv.'</PayIntv><!--一次交清时为0，年交为12-->
                            </Risk>
                      </Risks>';
        //整个xml报文
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                <Overcoat>
                    <Request>
                       '.$order_xml.'
                        <ApplyInfo>
                           '.$holder_xml.$insured_xml.$risk_xml.'
                        </ApplyInfo>
                    </Request>
                </Overcoat>';
        $xml = $this->compress_xml($xml);
        $asc_crypt = new AesCrypt();
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/insure/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
//        print_r($response);die;
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = preg_replace( "/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/ ", ' ', $return_data);//去掉非法字符
        if($response->status == 200){
            $formatter = Formatter::make($return_data, Formatter::XML)->toArray();
            if($formatter['Head']['Flag'] == '0'){
                $buy_options = [];
                $buy_options['insurance_attributes'] = $input['insurance_attributes'];
                $buy_options['quote_selected'] = json_decode($input['quote_selected'], true);  //算费已选项
                $add_order_res = $this->addOrder($input, $formatter, $buy_options);
                return $add_order_res;
            } else {
                //LogHelper::logError($xml, $formatter['Head']['Desc'], 'Hg', 'buy_ins_error');
                $msg['data'] = $formatter['Head']['Desc'];
                $msg['code'] = 403;
                return $msg;
            }
        } else {
            //LogHelper::logError($xml, $response->content,  'hg', 'insure_issue_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }
	public function getUserSex($idcard) {
    if(empty($idcard)) return null; 
    $sexint = (int) substr($idcard, 16, 1);
    return $sexint % 2 === 0 ? '1' : '0';//0男,1女
	}
    /**
     *
     * 处理核保返回的订单
     *
     */
    public function addOrder($original_data, $return_data, $buy_options)
    {
        try {
            DB::beginTransaction();
            //订单信息
            $order = new InsOrder();
            $order->order_no = $return_data['Body']['ContNo'];
            $order->union_order_code = $return_data['Body']['ContNo']; //外部合并订单号
            $order->create_account_id = $this->request['account_id'];  //代理商account_id
            $api_from_info  = ApiFrom::where('id', $this->bind->api_from_id)->first();
            $order->api_from_uuid = $api_from_info->uuid;//接口来源唯一码（接口类名称）
            $order->api_from_id = $this->bind->api_from_id;
            $order->ins_id = $this->bind->insurance_id;
            $order->p_code = $this->bind->p_code; //产品唯一码
            $order->bind_id =$this->bind->id;
            $order->total_premium = $return_data['Body']['Prem'];  //总保费
            $order->status = 'check_ing'; //待核保状态
            $order->buy_options = json_encode($buy_options, JSON_UNESCAPED_UNICODE);
            $order->by_stages_way = '0年';
            $order->save();
            //投保人信息
            $policy = new Policy();
            $policy->ins_order_id = $order->id;
            $policy->union_order_code = $order->union_order_code;
            $policy->name = $original_data['insurance_attributes']['ty_toubaoren']['ty_toubaoren_name'];
            $policy->phone = $original_data['insurance_attributes']['ty_toubaoren']['ty_toubaoren_phone'];
            $policy->card_type = $original_data['insurance_attributes']['ty_toubaoren']['ty_toubaoren_id_type'];
            $policy->card_id = $original_data['insurance_attributes']['ty_toubaoren']['ty_toubaoren_id_number'];

            $policy->save();
            //被保人信息
            $insures = array();
            foreach ($original_data['insurance_attributes']['ty_beibaoren'] as $k => $v) {
                $insures[$k]['ins_order_id'] = $order->id; //订单号
                //todo
                $insures[$k]['out_order_no'] =  $insures[$k]['union_order_code'] = $order->union_order_code;
                $insures[$k]['premium'] = $return_data['Body']['Prem']; ;    //保费
                $insures[$k]['p_code'] = $this->bind->p_code;    //外部产品码
                $insures[$k]['name'] = $v['ty_beibaoren_name'];
                $insures[$k]['card_type'] = $v['ty_beibaoren_id_type'];
                $insures[$k]['card_id'] = $v['ty_beibaoren_id_number'];
                $insures[$k]['relation'] = 31;
                $insures[$k]['created_at'] = $insures[$k]['updated_at'] = date("Y-m-d H:i:s");
            }
            Insure::insert($insures);
            $return_insures = array();
            foreach ($insures as $ik => $iv) {
                unset($iv['ins_order_id']);
                unset($iv['p_code']);
                unset($iv['created_at']);
                unset($iv['updated_at']);
                $return_insures[$ik] = $iv;
            }
            DB::commit();
            $res['order_list'] = $return_insures;
            $res['total_premium'] = $return_data['Body']['Prem']; ;
            $res['union_order_code'] = $return_data['Body']['ContNo'];
            $res['pay_way'] = [
                'pc'=>[
                    'cardPay'=> '银行卡支付',
                ],
                'mobile'=>[
                    'cardPay'=> '银行卡支付',
                ],
            ];
            $msg = ['data' => $res, 'code' => 200];
            return $msg;
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = ['data' => $e->getMessage(), 'code' => 444];
            LogHelper::logError($e->getMessage(), 'add_order');
            return $msg;
        }
    }
    /**
     *
     * 获取支付方式
     *
     *
     */
    public function getPayWayInfo()
    {
        //todo
        $input = $this->original_data;
        if(empty($input['pay_way'] || $input['union_order_code']))
            return ['data'=>'订单号或支付类型错误', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();
        if(empty($ins_order))
            return ['data'=>'订单号错误', 'code'=> 400];
        if($ins_order->status == 'check_error')
            return ['data'=> $ins_order->insures()->first()->check_error_message, 'code'=> 400];
        return [
            'data'=>[
                'order_code'=>$ins_order->union_order_code,
                'pay_way_data'=> [
                    'banks'=>[
                        ['name'=>'工商银行','code'=>102,'uuid'=>'单笔单日99万'],
                        ['name'=>'农业银行','code'=>103,'uuid'=>'单笔2万元,日累计10万元,单日成功笔数不超过5笔'],
                        ['name'=>'中国银行','code'=>104,'uuid'=>'单笔5万元,日累计10万元,单日成功笔数不超过5笔'],
                        ['name'=>'建设银行','code'=>105,'uuid'=>'单笔5000元,日累计1万,月累计5万'],
                        ['name'=>'中信银行','code'=>302,'uuid'=>'单笔2万,月累计2万'],
                        ['name'=>'光大银行','code'=>303,'uuid'=>'单笔500万'],
                        ['name'=>'华夏银行','code'=>304,'uuid'=>'单笔5万元,日累计10万元,单日成功笔数不超过5笔'],
                        ['name'=>'民生银行','code'=>305,'uuid'=>'单笔200万'],
                        ['name'=>'平安银行','code'=>307,'uuid'=>'单笔100万,日累计200万'],
                        ['name'=>'招商银行','code'=>308,'uuid'=>'单笔单日5万元'],
                        ['name'=>'兴业银行','code'=>300,'uuid'=>'单笔100万'],
                    ]
                ]
            ],
            'code'=> 200];
    }

    /**
     * 支付接口
     *
     * @url 测试环境请求支付请求url：http://wwwtest.huaguilife.cn/sinosoft-pay-web-gateway/scanPay/bankpayforchannel
     *
     */
    public function payIns()
    {
		set_time_limit(0);//永不超时
        $input = $this->decodeOriginData(); //解签获取源数据
        $union_order_code = $input['unionOrderCode'];
        $insure_res = Policy::where('union_order_code',$union_order_code)->first();
        if(empty($insure_res)){
            $msg['data'] = '没有相关的订单信息';
            $msg['code'] = 405;
            return $msg;
        }
        $input['insure_res'] = $insure_res;
        $key = self::API_INSURE_URL_KEY;//测试环境
        $INSURENCE_NO =  $union_order_code;//投保单号
        $OPERATOR = '5001';//渠道编号
        $REAL_AMOUNT = $input['premium']*100;//实付金额  以分为单位 如1元传入100
        $ORIGINAL_AMOUNT = $input['premium']*100;//原始金额  以分为单位
        $ACCOUNT_NO = $input['bank_number']??'6222022002006651860';//银行账号
        $ACCOUNT_NAME = $insure_res['name']??'张伟峰';//银行账户名
        $ID_NO =  $insure_res['card_id']??'440106198309161999';//身份证号
//        $TEL_NO = $insure_res['phone']??'18163228573';//	电话号码
        $TEL_NO = '18163228573';//	电话号码
        $PAY_SN = $OPERATOR.time();//	支付流水号  由调用方生成，用于确定支付唯一性，以及对账，查账使用
        $BANK_CODE = $input['bank_code'];//开户银行编码
        $ACCOUNT_TYPE = '00';//账户类型（固定填为00）
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <BankPay>
                      <INSURENCE_NO>'.$INSURENCE_NO.'</INSURENCE_NO>
                      <OPERATOR>'.$OPERATOR.'</OPERATOR>
                      <REAL_AMOUNT>'.$REAL_AMOUNT.'</REAL_AMOUNT>
                      <ORIGINAL_AMOUNT>'.$ORIGINAL_AMOUNT.'</ORIGINAL_AMOUNT>
                      <ACCOUNT_NO>'.$ACCOUNT_NO.'</ACCOUNT_NO>
                      <ACCOUNT_NAME>'.$ACCOUNT_NAME.'</ACCOUNT_NAME>
                      <ID_NO>'.$ID_NO.'</ID_NO>
                      <TEL_NO>'.$TEL_NO.'</TEL_NO>
                      <PAY_SN>'.$PAY_SN.'</PAY_SN>
                      <BANK_CODE>'.$BANK_CODE.'</BANK_CODE>
                      <ACCOUNT_TYPE>'.$ACCOUNT_TYPE.'</ACCOUNT_TYPE>
                    </BankPay>';
        $xml = $this->compress_xml($xml);
        LogHelper::logSuccess($xml,'pay_xml_'.$union_order_code);
        $asc_crypt = new AesCrypt();
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_PAY_URL_PRODUCT)
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("operator:5001")
            ->withTimeout(60)
            ->post();
        if($response->status == 200){
            $formatter = Formatter::make($response->content, Formatter::XML)->toArray();
            if($formatter['errorCode'] == '0000'){
                try{
                    $msg = ['data'=>'支付成功', 'code' => 200];
                    // $issue_res = $this->doIssue($input);
                    $pay_callback = $this->payCallBack($input);
                    return $msg;
                } catch (\Exception $e) {
                    return $msg = ['data' => $e->getMessage(), 'code' => 400];
                }
            } else {
                LogHelper::logError($formatter['msg'], 'hg', 'insure_issue_error');
                $msg['data'] = $formatter['msg'];
                $msg['code'] = 403;
                return $msg;
            }
        } else {
            LogHelper::logError($xml, $response->content, 'hg', 'insure_issue_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }
    /**
     * 支付结果查询接口
     *
     * @url 测试环境查询支付请求url: http://wwwtest.huaguilife.cn/sinosoft-pay-web-gateway/scanPay/queryForPayResult
     * @param $PAY_SN  支付流水号
     * @return {$code,$msg},支付状态和附加信息
     */
    public function selPayIns($PAY_SN)
    {
        $key = self::API_INSURE_URL_KEY;//测试环境
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                  <QueryPayResult>
                        <paySn>'.$PAY_SN.'</paySn> 
                  </QueryPayResult>';
        $asc_crypt = new AesCrypt();
        $xml = $this->compress_xml($xml);
        $xml = base64_encode($xml);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $xml_base64 = base64_encode($xml_asc);
        dump($xml_base64);
        $response = Curl::to(self::API_PAY_SEL_URL_PRODUCT)
            ->returnResponseObject()
            ->withData($xml_base64)
            ->withHeader("Content-Type:text/plain;operateor:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->content);
//        print_r('status:'.$response->status."<br/>");
//        print_r('content:'.$response->content);
        die;
        if($response->status == 200){
            $formatter = Formatter::make($response->content, Formatter::XML)->toArray();
            if($formatter['package']['header']['responseCode'] == '0000'){
                dd($formatter);
            } else {
                dd($response->content);
                LogHelper::logError($xml, $formatter['package']['header']['errorMessage'], 'hg', 'insure_issue_error');
                $msg['data'] = $formatter['package']['header']['errorMessage'];
                $msg['code'] = 403;
                dd($msg);
                return $msg;
            }
        } else {
            dd($response);
            LogHelper::logError($xml, $response->content, 'hg', 'insure_issue_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }

    /**
     * 支付回调
     *
     */
    public function payCallBack($input)
    {
		set_time_limit(0);//永不超时
        $order = InsOrder::where(['union_order_code'=>$input['unionOrderCode'], 'api_from_uuid'=> 'Hg'])->first();
        $user = User::where('account_id', $order->create_account_id)->first();
        $order->status = 'pay_end';
        $order->save();
        Insure::where('ins_order_id', $order->id)->update(['status'=> 'pay_end']);
            //查佣金比，统计财务
        $by_stages_way = preg_replace('/[^0-9]+/', '', $order->by_stages_way);
        $brokerage = InsApiBrokerage::where([
            ['bind_id', '=', $order->bind_id],
            ['insurance_id', '=', $order->ins_id],
            ['status', '=', 1],
            ['by_stages_way', '=', $by_stages_way],
        ])->first();
        if(!empty($brokerage)){
            $finance = new OrderFinance();
            $finance->order_id = $order->id;
            $finance->insurance_id = $order->ins_id;
            $finance->api_from_id = $brokerage->api_from_id;
            $finance->brokerage_id = $brokerage->id;
            $finance->union_order_code = $order->union_order_code;
            $finance->p_code = $order->p_code;
            $finance->private_p_code = $brokerage->private_p_code;
            $finance->brokerage_for_us = $order->total_premium * $brokerage->ratio_for_us / 100 ;
            $finance->brokerage_for_agency = $order->total_premium * $brokerage->ratio_for_agency / 100;
            $finance->save();
            $data = [
                "notice_type"=> 'pay_call_back',
                'data' => [
                    'status'=>true,
                    'ratio_for_agency'=> $brokerage->ratio_for_agency,
                    'brokerage_for_agency'=> $finance->brokerage_for_agency,
                    'union_order_code' =>$input['unionOrderCode'],
                    'by_stages_way' => $order->by_stages_way,
                    'error_message' => '',
                ]
            ];
        }else{
            $data = [
                "notice_type"=> 'pay_call_back',
                'data' => [
                    'status'=>true,
                    'ratio_for_agency'=> '',
                    'brokerage_for_agency'=> '',
                    'union_order_code' =>$input['unionOrderCode'],
                    'by_stages_way' => $order->by_stages_way,
                    'error_message' => '',
                ]
            ];
        }
        //Todo  回调地址
        $callback_url = $user->call_back_url . '/ins/call_back';
        $response = Curl::to($callback_url)
                        ->returnResponseObject()
                        ->withData($data)
                        ->asJson()
                        ->withTimeout(60)
                        ->post();
        if($response->status!=200){
            return json_encode(['state'=>false, 'failMsg'=>'回调处理失败'], JSON_UNESCAPED_UNICODE);
        }
        $res = json_encode(['state'=>true]);
        return $res;
    }
    /**
     * 回调
     * @return mixed
     */
    public function doExeclMsg()
    {

        //处理华贵信息execl
        $job_path = 'upload/resources/hg_job.xlsx';//工作
        $area_path = 'upload/resources/hg_area.xlsx';//地区
        $bank_path = 'upload/resources/hg_bank.xlsx';//银行
        $pay_code_path = 'upload/resources/hg_pay_code.xlsx';//支付状态码
//        $hg_job_res = $this->doExcel($job_path);
//        $hg_area_res = $this->doExcel($area_path);
//        $hg_bank_res = $this->doExcel($bank_path);
//        $hg_pay_code_res = $this->doExcel($pay_code_path);
    }
    /**
     * 签单（承保）接口，核保通过并支付成功后调用签单接口进行签单。
     * 实际上就是出单接口
     * @url http://112.74.235.90:8087/dsc/third/i/sign/5001
     *
     */
    public function issue()
    {
		$input = $this->decodeOriginData(); //解签获取源数据
        if(!$input['union_order_code'])
            return ['data'=>'订单号不可为空', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();
        if(empty($ins_order))
            return ['data'=>'订单不存在', 'code'=> 400];
		if(empty($input['pay_account'])){
			 return ['data'=>'支付信息不存在', 'code'=> 400];
		}
		$pay_account = json_decode($input['pay_account'],true);
        $bank_info = json_decode(config('hg_msg.bank_info'),true);
		$insure_res = Insure::where('union_order_code',$input['union_order_code'])->select('name','ins_policy_code')->first();
		if(!empty($insure_res['ins_policy_code'])){
			$ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();  //订单
			$return = array();
                //返回结果封装 todo
                $return['status'] = 0;    //出单状态 0：未生效 1：已生效 2：退保中 3：已退保
                $return['policy_order_code'] = $insure_res['ins_policy_code'];   //被保人保单号
                $return['private_p_code'] = $input['private_p_code'];   //产品码
                $return['order_code'] = $input['union_order_code'];   //被保人单号
                $return['start_time'] = $ins_order->start_time;
                $return['end_time'] = $ins_order->end_time;
                return ['data'=> $return, 'code'=> 200];
		}
        //head部分
        $TranDate = date('Ymd',time());//交易日期		格式：YYYYMMDD
        $TranNo = '5100'.time();//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = date('His',time());//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //非必传
        $FuncFlag = '';//	交易类型
        $TranCom = '';//管理公司代码	由华贵人寿核心提供表示电子商务渠道的销售分公司代码
        //body部分
        $AccName =  $insure_res['name'];//	支付账户名		 
        $ProposalPrtNo = $input['union_order_code'];//	投保单号		 
        $AccNo =$pay_account['bank_number'];//银行账号 
        foreach($bank_info as $value){
            if($pay_account['bank_code']==$value['bank_code']){
                $BankInnerCode = $value['bank_inner_code'];//开户银行编码
            }
        }
        $BankCode = $BankInnerCode??$pay_account['bank_code'];
        $Prem = $pay_account['premium']*100;//原始金额  以分为单位 
        $Amnt = '10000000';//保额	数值
        //非必传
        $ContNo = '';//	保单号	 预留字段 暂时不用
        $ContSite = '';//电子保单地址		预留字段 暂不使用
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <ProposalPrtNo>'.$ProposalPrtNo.'</ProposalPrtNo>
                                <AccName>'.$AccName.'</AccName>
                                <AccNo>'.$AccNo.'</AccNo>
                                <BankCode>'.$BankCode.'</BankCode>
                                <Prem>'.$Prem.'</Prem>
                                <Amnt>'.$Amnt.'</Amnt>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        $asc_crypt = new AesCrypt();
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/sign/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
       //print_r($return_data);die;
        $return_data = preg_replace( "/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/ ", ' ', $return_data);//去掉非法字符
        if($response->status == 200){
            $formatter = Formatter::make($return_data, Formatter::XML)->toArray();
            if($formatter['Head']['Flag'] == '0'){
                $ProposalContNo = $formatter['Body']['ProposalContNo'];
                $ContNo = $formatter['Body']['ContNo'];
                $ins_order = InsOrder::where('union_order_code', $ProposalContNo)->first();  //订单
                Insure::where('out_order_no', $ProposalContNo)->update([
                    'ins_policy_code'=>$ContNo,
                    'policy_status' => 6
                ]);
                $return = array();
                //返回结果封装 todo
                $return['status'] = 0;    //出单状态 0：未生效 1：已生效 2：退保中 3：已退保
                $return['policy_order_code'] = $ContNo;   //被保人保单号
                $return['private_p_code'] = $input['private_p_code'];   //产品码
                $return['order_code'] = $ProposalContNo;   //被保人单号
                $return['start_time'] = $ins_order->start_time;
                $return['end_time'] = $ins_order->end_time;
                return ['data'=> $return, 'code'=> 200];
            } else {
                LogHelper::logError($xml, $formatter['Head']['Desc'], 'Hg', 'buy_ins_error');
                $msg['data'] = $formatter['Head']['Desc'];
                $msg['code'] = 403;
                return $msg;
            }
        } else {
            LogHelper::logError($xml, $response->content,  'hg', 'insure_issue_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }
    /**
     * 保单回访接口，签单成功后调用回访接口进行在线回访
     *
     * @url http://112.74.235.90:8087/dsc/third/i/callBack/5001
     * 手册4.1
     */
    public function issueCallBack()
    {
        //head部分
        $TranDate = '20171127';//交易日期		格式：YYYYMMDD
        $TranNo = '123456787';//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = '165855';//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //非必传
        $TranCom = '';//管理公司代码	字符	Y	由华贵人寿核心提供表示电子商务渠道的销售分公司代码
        $FuncFlag = '123456789';//交易类型
        //body部分
        $ContNo = '888050000560678';	//保单号
        $CallFlag = '1';	//回访结果标志,1 否 0 是
        //非必传
        $ContSite = '';	//电子保单地址
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <ContNo>'.$ContNo.'</ContNo>
                                <CallFlag>'.$CallFlag.'</CallFlag>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        dump($xml);
        $asc_crypt = new AesCrypt();
        dump($key);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/callBack/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);
        if($response->status == 200){
            $formatter = Formatter::make($response->content, Formatter::XML)->toArray();
            if($formatter['package']['header']['responseCode'] == '0000'){
                dd($formatter);
            } else {
                dd($response->content);
                LogHelper::logError($xml, $formatter['package']['header']['errorMessage'], 'hg', 'insure_issue_callback_error');
                $msg['data'] = $formatter['package']['header']['errorMessage'];
                $msg['code'] = 403;
                dd($msg);
                return $msg;
            }
        } else {
            dd($response);
            LogHelper::logError($xml, $response->content, 'hg', 'insure_issue_callback_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }

    /**
     * 保单回访查询接口，第三方平台查看当前保单是否已经回访
     *
     * @url http://112.74.235.90:8087/dsc/third/i/queryCall/5001
     *
     */
    public function issueQueryCall()
    {
        //head部分
        $TranDate = '20171127';//交易日期		格式：YYYYMMDD
        $TranNo = '123456787';//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = '165855';//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //非必传
        $TranCom = '';//管理公司代码	字符	Y	由华贵人寿核心提供表示电子商务渠道的销售分公司代码
        $FuncFlag = '123456789';//交易类型
        //body部分
        $ContNo = '888050000560678';	//保单号
        //非必传
        $ContSite = '';	//电子保单地址
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <ContNo>'.$ContNo.'</ContNo>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        dump($xml);
        $asc_crypt = new AesCrypt();
        dump($key);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/queryCall/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);
        if($response->status == 200){
            $formatter = Formatter::make($response->content, Formatter::XML)->toArray();
            if($formatter['package']['header']['responseCode'] == '0000'){
                dd($formatter);
            } else {
                dd($response->content);
                LogHelper::logError($xml, $formatter['package']['header']['errorMessage'], 'hg', 'insure_issue_queryCall_error');
                $msg['data'] = $formatter['package']['header']['errorMessage'];
                $msg['code'] = 403;
                dd($msg);
                return $msg;
            }
        } else {
            dd($response);
            LogHelper::logError($xml, $response->content, 'hg', 'insure_issue_queryCall_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }

    /**
     * 保单查询接口,第三方平台通过客户相关信息查询当前客户在保险公司的保单
     *
     * @url http://112.74.235.90:8087/dsc/third/i/contQuery/5001
     *
     */
    public function contQuery()
    {
        //head部分
        $TranDate = '20171127';//交易日期		格式：YYYYMMDD
        $TranNo = '123456787';//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = '165855';//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //非必传
        $TranCom = '';//管理公司代码	字符	Y	由华贵人寿核心提供表示电子商务渠道的销售分公司代码
        $FuncFlag = '123456789';//交易类型
        //body部分
        $AppntName = '张伟峰';//用户名
        $QueryFlag = '1';//查询标志
        $IDNo = '440106198309161999 ';//用户证件号
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <AppntName>'.$AppntName.'</AppntName>
                                <QueryFlag>'.$QueryFlag.'</QueryFlag>
                                <IDNo>'.$IDNo.'</IDNo>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        dump($xml);
        $asc_crypt = new AesCrypt();
        dump($key);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/contQuery/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);
        if($response->status == 200){
            $formatter = Formatter::make($response->content, Formatter::XML)->toArray();
            if($formatter['package']['header']['responseCode'] == '0000'){
                dd($formatter);
            } else {
                dd($response->content);
                LogHelper::logError($xml, $formatter['package']['header']['errorMessage'], 'hg', 'insure_issue_contQuery_error');
                $msg['data'] = $formatter['package']['header']['errorMessage'];
                $msg['code'] = 403;
                dd($msg);
                return $msg;
            }
        } else {
            dd($response);
            LogHelper::logError($xml, $response->content, 'hg', 'insure_issue_contQuery_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }

    /**
     *保单查询接口,第三方平台通过报单号查询当前客户在保险公司的保单详情
     *
     * @url http://112.74.235.90:8087/dsc/third/i/queryByCont/5001
     *888050000560678
     */
    public function queryByCont()
    {
        //head部分
        $TranDate = '20171127';//交易日期		格式：YYYYMMDD
        $TranNo = '123456787';//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = '165855';//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //非必传
        $TranCom = '';//管理公司代码	字符	Y	由华贵人寿核心提供表示电子商务渠道的销售分公司代码
        $FuncFlag = '123456789';//交易类型
        //body部分
        $ContNo = '888050000560678';//报单号
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <ContNo>'.$ContNo.'</ContNo>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        dump($xml);
        $asc_crypt = new AesCrypt();
        dump($key);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/queryByCont/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);
        if($response->status == 200){
            $formatter = Formatter::make($response->content, Formatter::XML)->toArray();
            if($formatter['package']['header']['responseCode'] == '0000'){
                dd($formatter);
            } else {
                dd($response->content);
                LogHelper::logError($xml, $formatter['package']['header']['errorMessage'], 'hg', 'insure_issue_queryByCont_error');
                $msg['data'] = $formatter['package']['header']['errorMessage'];
                $msg['code'] = 403;
                dd($msg);
                return $msg;
            }
        } else {
            dd($response);
            LogHelper::logError($xml, $response->content, 'hg', 'insure_issue_queryByCont_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }

    /**
     * 新单对账接口,出单完成后，每天第三方平台与华贵网销进行保单财务数据的对账
     *
     * @url http://112.74.235.90:8087/dsc/third/i/newAcc/5001
     *
     */
    public function newAcc(){
        //head部分
        $TranDate = '20171127';//交易日期		格式：YYYYMMDD
        $TranNo = '123456787';//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = '165855';//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //非必传
        $TranCom = '';//管理公司代码	字符	Y	由华贵人寿核心提供表示电子商务渠道的销售分公司代码
        $FuncFlag = '123456789';//交易类型
        //body部分
        $Sumprem = '1000000';//报单号
        $Summany = '3';//报单号
        $Flag = 'M';//报单号
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <Sumprem>'.$Sumprem.'</Sumprem>
                                <Summany>'.$Summany.'</Summany>
                                <Flag>'.$Flag.'</Flag>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        dump($xml);
        $asc_crypt = new AesCrypt();
        dump($key);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        dump($xml_asc);
        $response = Curl::to(self::API_INSURE_URL.'/i/newAcc/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        dump($response);
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);

    }

    /**
     * 解锁接口,客户发起的请求用以删除核保通过后但是未签单的数据（主要考虑到风险保额校验）
     *
     * @url http://112.74.235.90:8087/dsc/third/i/deletePrt/5001
     *
     */
    public function deletePrt($res){
        //head部分
        $TranDate = date('Ymd',time());//交易日期		格式：YYYYMMDD
        $TranNo = '5100'.time();//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = date('His',time());//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //body部分
        $details = '';
        foreach ($res as $PrtNo){
            $detail = '<Detail>
			                <PrtNo>'.$PrtNo['union_order_code'].'</PrtNo>
		                </Detail>';
            $details = $details.$detail;
        }
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>'.$details.'</Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        $asc_crypt = new AesCrypt();
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/deletePrt/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = preg_replace( "/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/ ", ' ', $return_data);//去掉非法字符
        if($response->status == 200){
            $formatter = Formatter::make($return_data, Formatter::XML)->toArray();
            if($formatter['Head']['Flag'] == '0'){
                return ['data'=>'解锁成功', 'code'=> 200];
            } else {
                LogHelper::logError($xml, $formatter['Head']['Desc'], 'Hg', 'buy_ins_error');
                $msg['data'] = $formatter['Head']['Desc'];
                $msg['code'] = 403;
                return $msg;
            }
        } else {
            LogHelper::logError($xml, $response->content,  'hg', 'insure_issue_error');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }

    }



    //==============================================================退保方法==========================================================
    /**
     * 退保试算接口		/i/edorTrial	第三方发起请求计算出当前保单退保能退多少钱
     *
     * @url http://112.74.235.90:8087/dsc/third/i/edorTrial/5001
     *
     */
    public function edorTrial($input){
        //head部分
        $TranDate = date('Ymd',time());//交易日期		格式：YYYYMMDD
        $TranNo = '5100'.time();//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = date('His',time());//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //非必传
        $TranCom = '';//管理公司代码	字符	Y	由华贵人寿核心提供表示电子商务渠道的销售分公司代码
        $FuncFlag = '123456789';//交易类型
        //body部分
        $ContNo = $input['warranty_code'];//报单号 
        $CTDate = date('Y-m-d',time());//	退保日期格式YYYY-MM-DD
        //非必传
        $Name = $input['apply_user_name'];//	申请人姓名 
        $IDType = '0';//	申请人证件类型	0身份证1护照2军人证（军官证）4户口本7出生证 D警官证F港、澳、台通行证
        $IDNo =$input['apply_user_code'];//	申请人证件号 
        $DrwName = $input['apply_user_name'];//	领款人姓名		 
        $DrwIDType = '0';//	领款人证件类型	0身份证1护照2军人证（军官证）4户口本7出生证 D警官证F港、澳、台通行证
        $DrwIDNo = $input['apply_user_code'];//	领款人证件号	 
        $PayAccNo = $input['bank_number'];//	领款账户
        $AccName = $input['apply_user_name'];//	领款账户名
        $BankNo = $input['bank_code'];//	开户行
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <ContNo>'.$ContNo.'</ContNo>
                                <CTDate>'.$CTDate.'</CTDate>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        $asc_crypt = new AesCrypt();
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/edorTrial/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);
    }

    /**
     * 退保请求接口	    /i/edorCT	客户进行退保申请，第三方平台调用华贵网销退保请求接口通知进行退保操作
     *
     * @url http://112.74.235.90:8087/dsc/third/i/edorCT/5001
     *
     */
    public function insureCacel(){
        $input = $this->decodeOriginData(); //解签获取源数据
        $ins_policy_code = $input['warranty_code'];
        $union_order_code = $input['union_order_code'];
        //head部分
        $TranDate = date('Ymd',time());//交易日期		格式：YYYYMMDD
        $TranNo = '5100'.time();//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = date('His',time());//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //非必传
        $TranCom = '';//管理公司代码,由华贵人寿核心提供表示电子商务渠道的销售分公司代码
        $FuncFlag = '';//交易类型
        //body部分
        $ContNo = $ins_policy_code;//保单号 
        $TypeFlag = 'bk';//退保渠道		wc：微信原渠道返回  bk:银行返回
        $ContPrtNo = $union_order_code;//投保单号 		 
        $AccName = $input['apply_user_name'];//退保账户名		银行卡账户名
        $PayAccNo = $input['bank_number'];//退保账户号,保费退返的银行卡号
        $BankCode = $input['bank_code'];//退保账户银行编码	 
        $AppName = $input['apply_user_name'];//申请人姓名	 
        $IDType = '0';//申请人证件类型
        $IDNo = $input['apply_user_code'];//申请人证件号
        //TODO  退保试算接口
        $cacel_quote = $this->edorTrial($input);
        dd($cacel_quote);
        $Prem = '510000';//退保金额，暂时为空，根据退保试算接口得来
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <ContNo>'.$ContNo.'</ContNo>
                                <TypeFlag>'.$TypeFlag.'</TypeFlag>
                                <ContPrtNo>'.$ContPrtNo.'</ContPrtNo>
                                <AccName>'.$AccName.'</AccName>
                                <PayAccNo>'.$PayAccNo.'</PayAccNo>
                                <BankCode>'.$BankCode.'</BankCode>
                                <AppName>'.$AppName.'</AppName>
                                <IDType>'.$IDType.'</IDType>
                                <IDNo>'.$IDNo.'</IDNo>
                                <Prem>'.$Prem.'</Prem>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        dump($xml);die;
        $asc_crypt = new AesCrypt();
        dump($key);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/edorCT/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);
    }

    /**
     * 退保到账回写接口    /i/edorConf	通过第三方退保的保单，第三方平台回写到账状态与到账日期到华贵网销进行财务数据的完整性补充
     *
     * @url http://112.74.235.90:8087/dsc/third/i/edorConf/5001
     *
     */
    public function edorConf(){
        //head部分
        $TranDate = '20171127';//交易日期		格式：YYYYMMDD
        $TranNo = '123456787';//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = '165855';//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //body部分
        $ContNoList = '888050000560678';//	保单集合字节
        $ContNo = '888050000560678';//	保单号	 
        $CTEdorNo = '888050000560678';//	保全受理号
        $CTDate = '2017-11-30';//	退保日期	YYYY-MM-DD
        $CTState = '1';//	退保状态 	0-已退保；1-未退保
        $CTMoney = '510000';//	实际退保金额	单位/分
        $CTType = 'CT';//	退保类型		WT-犹退，CT-退保，XT-协退
        $CTApplyDate = '2017-11-30';//	退保申请日期		 YYYY-MM-DD
        $CTApplyName = '张伟峰';//	退保申请人姓名
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                    <Overcoat>
                          <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>     
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <ContNo>'.$ContNo.'</ContNo>
                                <CTDate>'.$CTDate.'</CTDate>
                          </Body>
                    </Overcoat>';
        $xml = $this->compress_xml($xml);
        dump($xml);
        $asc_crypt = new AesCrypt();
        dump($key);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/edorTrial/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);

    }

    /**
     * 影像件上传接口	第三方平台	/i/upload	客户进行理赔案件的影像件资料的上传，以及补充资料的上传
     *
     * @url http://112.74.235.90:8087/dsc/third/i/upload/5001
     *
     */
    public function imgUpload(){
        //head部分
        $TranDate = '20171127';//交易日期		格式：YYYYMMDD
        $TranNo = '123456787';//交易流水号	格式：yyyymmdd加8位流水号
        $TranTime = '165855';//交易时间		格式：hhmmss
        $TranOperator = 'wcb';//操作人
        $isTparty = 'Y';//是否是第三方标志
        $key = self::API_INSURE_URL_KEY;//测试环境
        //body部分
        $UploadType = '';//影像件上传类型,1、理赔文件上传：LP2、契约文件上传：TB3、保全类上传：BQ
        $BusNo = '';//业务号,1、理赔：立案号2、契约：投保单号3、保全：受理号
        $DocCode = '';//保全时要的业务号,当上传类型为BQ时必传
        $SubType = '';//单证类型51001:理赔申请书50501：理赔身份证明类单证50701：理赔事故证明类单证50801：理赔医疗收据类单证50901：赔款给付协议类单证（个险）
        //此节点可重复添加，支持该类型下多张单证上传
        $ImageUrl = '';//影像文件路径,阿里云返回地址
        $ImageName = '';//影像文件名,影像件名称（业务号+单证类型+当前时间毫秒数后5位,）有且只有一个.符号
        $xml = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
                <Overcoat>
                     <Compensate>
	                    <Head>
                                <TranDate>'.$TranDate.'</TranDate>
                                <TranNo>'.$TranNo.'</TranNo>
                                <TranTime>'.$TranTime.'</TranTime>     
                                <TranOperator>'.$TranOperator.'</TranOperator>                    
                                <isTparty>'.$isTparty.'</isTparty>
                          </Head>
                          <Body>
                                <UploadType></UploadType><!—影像件上传类型-->
                                <BusNo>9000000060</BusNo><!---案件号 -->
                                <DocCode></DocCode><!--保全类型时特殊业务号-->
                                <List>
                                <SubType>51001</SubType><!--单证类型 -->
                                <Pages>
                                    <ImageUrl>http://hgosstest2.oss-cn-szfinance.aliyuncs.com</ImageUrl><!--文件路径 -->
                                    <ImageName>123412341492603907565.tif</ImageName><!--文件名 -->
                                </Pages>
                                <Pages>
                                    <ImageUrl>http://hgosstest2.oss-cn-szfinance.aliyuncs.com</ImageUrl><!--文件路径 -->
                                    <ImageName>123412341492603907565.tif</ImageName><!--文件名 -->
                                </Pages>
                                </List>
                                <List>
                                <SubType>50501</SubType><!--单证类型 -->
                                <Pages>
                                    <ImageUrl>http://hgosstest2.oss-cn-szfinance.aliyuncs.com</ImageUrl><!--文件路径 -->
                                    <ImageName>123412341492603907565.tif</ImageName><!--文件名 -->
                                </Pages>
                                <Pages>
                                    <ImageUrl>http://hgosstest2.oss-cn-szfinance.aliyuncs.com</ImageUrl><!--文件路径 -->
                                    <ImageName>123412341492603907565.tif</ImageName><!--文件名 -->
                                </Pages>
                                </List>
                          </Body>
                    </Compensate>
                </Overcoat>';
        $xml = $this->compress_xml($xml);
        dump($xml);
        $asc_crypt = new AesCrypt();
        dump($key);
        $xml_asc = $asc_crypt->AesEncrypt($xml,$key);
        $response = Curl::to(self::API_INSURE_URL.'/i/edorTrial/5001')
            ->returnResponseObject()
            ->withData($xml_asc)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        print_r($response->status.'<br/>');
        print_r($response->content);
        $return_data = $asc_crypt->AesDecrypt($response->content,$key);
        $return_data = $this->compress_xml($return_data);
        dd($return_data);
    }



//    ==============================================================公共方法==========================================================
    /**
     * 压缩xml : 清除换行符,清除制表符,去掉注释标记
     * @param $string
     * @return 压缩后的$string
     */
    protected function compress_xml($string) {
        $string = str_replace("\r\n", '', $string); //清除换行符
        $string = str_replace("\n", '', $string); //清除换行符
        $string = str_replace("\t", '', $string); //清除制表符
        $pattern = array (
            "/> *([^ ]*) *</", //去掉注释标记
            "/[\s]+/",
            "/<!--[^!]*-->/",
            "/\" /",
            "/ \"/",
            "'/\*[^*]*\*/'"
        );
        $replace = array (
            ">\\1<",
            " ",
            "",
            "\"",
            "\"",
            ""
        );
        return preg_replace($pattern, $replace, $string);
    }

    /**
     * 业务参数 解析出源数据json字符串
     *
     */
    protected function decodeOriginData()
    {
        $input = $this->request->all();
        $original_data_array = $this->sign_help->tyDecodeOriginData($input['biz_content']);
        return $original_data_array;
    }

    /**
     * 测试处理费率表execl
     *
     */
    public function testExcel()
    {
        set_time_limit(0);//永不超时
        $a = ExcelHelper::excelToArray("upload/tariff/hg_tariff.xlsx");
        $title = $a->first()->toArray();
        $tariff = $a->toArray();
        unset($tariff[0]);
        $insert_data = [];
        foreach($tariff as $k => $v){
            foreach($v as $vk => $vv){
                if(!empty($vv)){
                    $insert_data[$k][$title[$vk]] = str_replace(array("\r\n", "\r", "\n"),  '', $vv);
                }
            }
        }
        foreach ($insert_data as $value){
            DB::table('hg_tariff')->insert($value);
        }

    }

    /**
     * 测试处理execl
     * @param $path  文件路径
     * "upload/tariff/hg_tariff.xlsx"
     */
    public function doExcel($path)
    {
        set_time_limit(0);//永不超时
        $a = ExcelHelper::excelToArray($path);
        $title = $a->first()->toArray();
        $content = $a->toArray();
        unset($content[0]);
        $return_data = [];
        foreach($content as $k => $v){
            foreach($v as $vk => $vv){
                if(!empty($vv)){
                    $return_data[$k][$title[$vk]] = str_replace(array("\r\n", "\r", "\n"),  '', $vv);
                }
            }
        }
        return $return_data;
    }

    /**
     * 封装默认试算参数
     * @param $restrict_genes
     * @return array
     */
    protected function encapsulateDefaultData($restrict_genes)
    {
        $data = [];
        foreach ($restrict_genes as $restrict_gene) {
            $data[$restrict_gene['ty_key']] = $restrict_gene['value'];
        }
        $data['productCode'] = $this->api_m_info->p_code;
        return $data;
    }
    /**
     * 转换格式，用于调用接口
     * @param $restrict_genes
     * @param $data
     * @return mixed
     */
    protected function transformToApiWay($restrict_genes, $data)
    {
        foreach ($restrict_genes as $restrict_gene) {
            if (isset($data[$restrict_gene['ty_key']])) {
                foreach ($restrict_gene['values'] as $value) {
                    if ($data[$restrict_gene['ty_key']] == $value['ty_value']) {
                        $data[$restrict_gene['ty_key']] = $value['value'];
                        break;
                    }
                }
                $data[$restrict_gene['key']] = $data[$restrict_gene['ty_key']];
                unset($data[$restrict_gene['ty_key']]);
            }
        }

        return $data;
    }

    /**
     * 去掉不用的Key
     * @param $data
     * @return mixed
     */
    protected function onlyTyKey($data)
    {
        foreach ($data as &$item) {
            if (isset($item['values'])) {
                foreach ($item['values'] as &$value) {
                    unset($value['value']);
                }
            }
            unset($item['key']);
        }

        return $data;
    }

    /**
     * 获取产品保障权益
     * @param $insurance_id
     * @return $result
     */
    protected function getProtectItems($insurance_id)
    {
        $select = [
            'e.id as protectItemId',
            'e.name as name',
            'e.detail as description',
            'd.coverage_jc as coverage_jc',
            'b.coverage_bs as coverage_bs'
        ];

        $result = DB::table('insurance as a')
            ->join('insurance_clause as b', 'a.id', '=', 'b.insurance_id')
            ->join('clause as c', 'b.clause_id', '=', 'c.id')
            ->join('clause_duty as d', 'c.id', '=', 'd.clause_id')
            ->where('d.ins_id', $insurance_id)
            ->join('duty as e', 'd.duty_id', '=', 'e.id')
            ->where('a.id', $insurance_id)
            ->select($select)
            ->get()
            ->toArray();

        foreach ($result as &$protect_item) {
            $protect_item->sort = 0;
            $coverage_bs = explode(',', $protect_item->coverage_bs);
            $coverage_bs = array_shift($coverage_bs);
            $protect_item->defaultValue = $coverage_bs * $protect_item->coverage_jc . '元';
            unset($protect_item->coverage_bs, $protect_item->coverage_jc);
        }
        return $result;
    }

    /**
     * 获取算费结果
     * @param $only_ty_key_array
     * @return $quote
     */
    protected function formatQuote($only_ty_key_array)
    {
        $where = array();
        foreach($only_ty_key_array as $k => $v){
            $where[$v['ty_key']] = $v['value'];
        }
        $quote = DB::table('hg_tariff')->where($where)->first();
        $protect_items = array();
        $items = explode('#', trim($quote->protect_items, "#"));
        foreach($items as $k => $v){
            $item = explode('__', $v);
            $protect_items[$k]['name'] = $item[0];
            $protect_items[$k]['defaultValue'] = $item[1];
            $protect_items[$k]['description'] = $item[2];
        }
        $quote->protect_items = $protect_items;
        return $quote;
    }

    /**
     * 内外键值转换
     * @param $input
     * @return $data
     */
    protected function ins_key_value_change($input){
        $tmp_data = [];
        $original_data = $input;
        $insurance_attributes = (new InsuranceAttributesRepository())->findAttributesRecursionByBindId($this->bind->id);
        $attributes = $original_data['insurance_attributes'];
        foreach ($insurance_attributes as $insurance_attribute) {
            $module_key = $insurance_attribute['module_key'];
            if (isset($attributes[$module_key]) && isset($insurance_attribute['productAttributes'])) {
                if (isset($attributes[$module_key][0])) { // 如果是二维数组
                    foreach ($attributes[$module_key] as $key => $item) {
                        foreach ($insurance_attribute['productAttributes'] as $productAttribute) {
                            $ty_key = $productAttribute['ty_key'];
                            if (isset($attributes[$module_key][$key][$ty_key]) && isset($productAttribute['attributeValues'])) {
                                foreach ($productAttribute['attributeValues'] as $attributeValue) {
                                    $ty_value = $attributeValue['ty_value'];
                                    if ($ty_value == $attributes[$module_key][$key][$ty_key]) {
                                        $attributes[$module_key][$key][$ty_key] = $attributeValue['controlValue'];
                                    }
                                }
                                if ($ty_key == 'ty_beibaoren_occupation') {
                                    $occupation = $attributes[$module_key][$ty_key];
                                    list(,,$code) = explode('-', $occupation);
                                    $attributes[$module_key][$key][$ty_key] = $code;
                                }
                            }
                            $tmp_data[$module_key][$key][$productAttribute['apiName']] = $attributes[$module_key][$key][$ty_key];
                        }
                    }
                } else {
                    foreach ($insurance_attribute['productAttributes'] as $productAttribute) {
                        $ty_key = $productAttribute['ty_key'];
                        if (isset($attributes[$module_key][$ty_key]) && isset($productAttribute['attributeValues'])) {
                            foreach ($productAttribute['attributeValues'] as $attributeValue) {
                                $ty_value = $attributeValue['ty_value'];
                                if ($ty_value == $attributes[$module_key][$ty_key]) {
                                    $attributes[$module_key][$ty_key] = $attributeValue['controlValue'];
                                }
                            }
                            if ($ty_key == 'ty_toubaoren_occupation') {
                                $occupation = $attributes[$module_key][$ty_key];
                                list(,,$code) = explode('-', $occupation);
                                $attributes[$module_key][$ty_key] = $code;
                            }
                        }
                        $tmp_data[$module_key][$productAttribute['apiName']] = $attributes[$module_key][$ty_key];
                    }
                }
            }
        }
        $data = [];
        foreach ($tmp_data as $module_key => $item) {
            if($module_key == 'ty_beibaoren') {
                $data['policyInsuredList'] = $item;//被保人列表
            }elseif ($module_key == 'ty_toubaoren') {
                $data['policyHolder'] = $item;//投保人
            }elseif ($module_key == 'ty_shouyiren') {
                $data['bnf'] = $item;//受益人
            }elseif($module_key == 'ty_base') {
                $data['base'] = $item;//基础信息
            }else{
                array_push($data, $item);
            }
        }
        return $data;
    }

    /**
     * 根据身份证号获取性别，生日，年龄（周岁）
     * @param $id 身份证号
     * @return $data
     */
    public function getMSgByID($id){
        //过了这年的生日才算多了1周岁
        if(empty($id)) return [];
        $date=strtotime(substr($id,6,8));
        //获得出生年月日的时间戳
        $today=strtotime('today');
        //获得今日的时间戳
        $diff=floor(($today-$date)/86400/365);
        //得到两个日期相差的大体年数
        //strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
        $age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;
        //通过身份证号查询出性别与生日
        $birth = strlen($id)==15 ? ('19' . substr($id, 6, 6)) : substr($id, 6, 8);
        $sex = substr($id, (strlen($id)==15 ? -2 : -1), 1) % 2 ? '男' : '女'; //1为男 2为女
        $data = [];
        $data['sex'] = $sex;
        $data['birth'] = $birth;
        $data['age'] = $age;
        return $data;
    }
}