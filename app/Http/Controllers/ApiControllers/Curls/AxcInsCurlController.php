<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2017/11/3
 * Time: 10:51
 */

namespace App\Http\Controllers\ApiControllers\Curls;


use App\Helper\IdentityCardHelp;
use App\Helper\LogHelper;
use DB;
use App\Models\User;
use App\Models\Policy;
use App\Models\Insure;
use App\Models\InsOrder;
use App\Models\Insurance;
use App\Models\ApiFrom;
use App\Models\OrderFinance;
use App\Models\InsApiBrokerage;
use App\Helper\ExcelHelper;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Ixudra\Curl\Facades\Curl;
use SoapBox\Formatter\Formatter;
use App\Repositories\RestrictGeneRepository;
use App\Repositories\InsuranceApiFromRepository;
use App\Repositories\InsuranceAttributesRepository;

class AxcInsCurlController
{
    protected $request;
    protected $api_m_info;
    protected $request_url;
    protected $sign_key;
    protected $code;
    protected $product_codes_ty = ['TP087674', 'TP087675', 'TP087676', 'TP087677', 'TP087678', 'TP087679', 'TP087680',
        'TP087681', 'TP087682', 'TP087683', 'TP087684', 'TP087685'];
    protected $product_codes_gy = ['TP087686', 'TP087687', 'TP087688', 'TP087689'];

    public function __construct(Request $request, $insurance_api_from=null)
    {
        $this->code = 'EC17090110';   //渠道代码
        $this->sign_key  = '123456';  //密钥
        $this->request = $request;
        $this->sign_help = new RsaSignHelp();
        $this->request_url = 'https://antx11.95303.com/com.isoftstone.iics.bizsupport.epartner/amt/processOrder';
        if(!preg_match("/call_back/",$this->request->url())){
            $this->origin_data = $this->decodeOriginData();   //解签获得源数据
            $this->api_m_info = $insurance_api_from;
            $this->p_code = $this->api_m_info->p_code;
        }
    }

    //产品详情
    public function getApiOption()
    {
        $insuranceAttributesRepository = new InsuranceAttributesRepository();
        $restrictGeneRepository = new RestrictGeneRepository();

        $input = $this->origin_data;
        $insurance_id = $input['ty_product_id'];

        $result['ty_product_id'] = $insurance_id;
        $result['private_p_code'] = $this->api_m_info->private_p_code;
        $result['bind_id'] = $this->api_m_info->id;
        //投保属性
        $insurance_attributes = $insuranceAttributesRepository->findAttributesRecursionByBindId($this->api_m_info->id);
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
        $restrict_genes = $restrictGeneRepository->findRestrictGenesRecursionByBindId($this->api_m_info->id);
        $result['option']['restrict_genes'] = $this->onlyTyKey($restrict_genes);
        //获得默认试算选项
        $result['option']['selected_options'] = $restrictGeneRepository->findDefaultRestrictGenes($this->api_m_info->id);
        $result['option']['selected_options'] = $this->onlyTyKey($result['option']['selected_options']);
        $default_quote = $this->formatQuote($result['option']['selected_options']);
        //获得默认保费及保障内容
        $result['option']['price'] = $default_quote->price;
        //保障内容
        $result['option']['protect_items'] = $default_quote->protect_items;
        return ['data'=> $result, 'code'=> 200];
    }

    //算费
    public function quote()
    {
        $msg = [];
        $original_data = $this->origin_data;

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

        //本地数据库算费查询
        if(in_array($this->api_m_info->p_code, $this->product_codes_ty)){
            $quote = DB::table('tariff_axc_ty')->where($api_data)->first();
        } else {
            $quote = DB::table('tariff_axc_gz')->where($api_data)->first();
        }


        //保障内容处理
        $protect_items = explode('#', trim($quote->protect_items, '#'));
        $items = array();
        foreach($protect_items as $k => $v){
            $arr = explode('__', $v);
            $items[$k]['name'] = $arr[0];
            $items[$k]['defaultValue'] = $arr[1];
            $items[$k]['description'] = $arr[2];
        }
        //选中项处理
        $selected_options = json_decode($original_data['new_val'], true);
        // 替换"key"为"ty_key"
        foreach ($selected_options as &$item) {
            $item['ty_key'] = $item['key'];
            unset($item['key']);
        }
        $msg['data']['price'] = $quote->price;
        $msg['data']['selected_options'] = $selected_options;
//        $msg['data']['new_genes'] = $restrict_genes;
        $msg['data']['protect_items'] = $items;
        $msg['code'] = 200;
        return $msg;
    }

    //投保接口
    public function buyIns()
    {
        $input = $this->decodeOriginData(); //解签获取源数据
        $repository = new InsuranceAttributesRepository();
        $tmp_data = $repository->inToOut($this->api_m_info->id, $input['insurance_attributes']);    //转换外部键值对
        $quote_selected = json_decode($input['quote_selected'], true);  //算费已选项
        $api_data = [];
        foreach ($quote_selected as $item) {
            if (isset($item['value'])) {
                $api_data[$item['ty_key']] = $item['value'];
            } else {
                $api_data[$item['ty_key']] = '';
            }
        }
//        dd($quote_selected);
        //本地数据库算费查询
        if(in_array($this->api_m_info->p_code, $this->product_codes_ty)){
            $quote = DB::table('tariff_axc_ty')->where($api_data)->first();
        } else {
            $quote = DB::table('tariff_axc_gz')->where($api_data)->first();
        }

        $ty_duration_period_value = $quote->ty_duration_period_value;   //保障时间
        $ins_start_time = date("Y-m-d H:i:s", strtotime($tmp_data['ty_base']['insBeginDate']));   //起保日期
        $ins_end_time = ($ty_duration_period_value == '1年' ? date("Y-m-d H:i:s", strtotime($ins_start_time." +1 years")) : date("Y-m-d H:i:s", strtotime($ins_start_time." +1 days")));
        $ins_end_time =date("Y-m-d H:i:s", strtotime($ins_end_time." -1 seconds"));   //结束日期
        $productCode = $this->api_m_info->p_code;   //外部产品码
        $code = $this->code;   //渠道代码
        $sign_key  = $this->sign_key;  //密钥

        $insurance = Insurance::where('id', $this->api_m_info->insurance_id)->with(['clauses'])->first();
        //条款信息
        $clause_coverage = DB::table('clause_duty')
            ->select(DB::raw('SUM(coverage_jc) as coverage'), 'clause_id')
            ->where('ins_id', $insurance['id'])
            ->groupBy('clause_id','ins_id')
            ->get();
        foreach($insurance->clauses as $k => $v){
            foreach($clause_coverage as $ck => $cv){
                if($v->id == $cv->clause_id){
                    $insurance->clauses[$k]->coverage = (int)$cv->coverage * 100;
                }
            }
        }

        $productName = $insurance->name;    //产品名称
        $orderId = $code . date('YmdHis') . rand(10, 99) . rand(100, 999); //订单号

        $zipCode = isset($tmp_data['ty_toubaoren']['zipCode']) ?  '<zipCode>'.$tmp_data['ty_toubaoren']['zipCode'].'</zipCode>' : '';   //邮编（意外险）
        $address = isset($tmp_data['ty_toubaoren']['address']) ? '<address>'.$tmp_data['ty_toubaoren']['address'].'</address>' : '';   //地址（意外险）

        $num = count($input['insurance_attributes']['ty_beibaoren']); //被保人数
        $totalAmount = 0;   //总保额
        $totalPremium = $quote->price * $num;   //总保费

        if(in_array($productCode, $this->product_codes_gy)){
            $isGroup = 0;
            $prodNo = '040002';
        } else {
            $isGroup = 1;
            $prodNo = '061001';
        }

        //请求头信息
        $header_xml = '<header>
                        <requestType>APPLICATION</requestType>
                        <from>'.$code.'</from>
                        <sendTime>'.date('Y-m-d H:i:s').'</sendTime>
                        <orderId>'.$orderId.'</orderId>
                        <productCode>'.$productCode.'</productCode>
                        <productName>'.$productName.'</productName>
                        <isGroup>'.$isGroup.'</isGroup>
                    </header>';

        $order_xml = ''; //订单信息
        $item_xml = ''; //条款信息
        $insured_xml = '';  //被保人信息
        $jobCoeff = '<jobCoeff>1</jobCoeff>';   //职业系数

        //职业信息及雇主责任信息
        $customTgt_html = '';

        //雇主责任主体信息
        if(in_array($productCode, $this->product_codes_gy)){
            $employee_xml = ''; //雇员信息
            $lostWages = '<lostWages>1700</lostWages>'; //误工费
            $premium_one = ($ty_duration_period_value == '1年' ? 13200 : 310);
            $premium_two = ($ty_duration_period_value == '1年' ? 2000 : 47);
            $premium_three = ($ty_duration_period_value == '1年' ? 1300 : 30);
            foreach($insurance->clauses as $k => $v){
                $i = 0;
                foreach($quote_selected as $qk => $qv){
                    if(($v['type'] == 'main') || (($v->id == $qv['protectItemId']) && ($qv['value'] == '包含'))){
                        $i = 1;
                    }
                    if(($qv['ty_key'] == 'insure_three') && ($qv['value'] == '不包含')){
                        $lostWages = '';
                        $premium_one = ($ty_duration_period_value == '1年' ? 11500 : 270);
                    }
                }

                if($i == 1){
                    $key = $k+1;
                    $freeCoeff = '';
                    $dayAmt = '';
                    $isMain = ($v['type'] == 'main') ? 1 : 0;
                    $itemCode = $v->clause_code;    //险别代码
                    $itemName = $v->name;   //条款名称
                    $amount = $v->coverage * $num;  //该条款总保额
                    switch ($itemCode){
                        case '040003':
                            $premium = $premium_one * $num;    //该条款总保费
                            $totalAmount += $amount;    //订单总保额
                            $freeCoeff = '<freeCoeff>100免，90%赔付</freeCoeff>';
                            break;
                        case '040227':
                            $premium = $premium_two * $num;     //该条款总保费
                            break;
                        case '040240':
                            $premium = $premium_three * $num;     //该条款总保费
                            $totalAmount += $amount;    //订单总保额
                            $dayAmt = '<dayAmt>5000</dayAmt>';
                    }
                    $str = "<item>
                        <seq>$key</seq>
                        <isMain>$isMain</isMain>
                        <itemCode>$itemCode</itemCode>
                        <itemName>$itemName</itemName>
                        <amount>$amount</amount>
                        <premium>$premium</premium>
                        ".$freeCoeff. $dayAmt .
                        "</item>";
                    $item_xml .= $str;
                }
            }

            //雇员信息
            foreach($input['insurance_attributes']['ty_beibaoren'] as $k => $v){
                $card_info = IdentityCardHelp::getIDCardInfo($v['ty_beibaoren_id_number']);
                if($card_info['status'] != 2){
                    return ['data'=> $v['ty_beibaoren_name'] . '证件信息有误', 'code'=>403];
                }
                $sex = $input['insurance_attributes']['ty_beibaoren'][$k]['ty_beibaoren_sex'] = ($card_info['sex']==1) ? 1 : 2;
                $input['insurance_attributes']['ty_beibaoren'][$k]['ty_beibaoren_birthday'] = $card_info['birthday'];
                $swq = $k;
                $name = $v['ty_beibaoren_name'];
                $cardNo = $v['ty_beibaoren_id_number'];
                $employee_xml .= '<employee>
                            <seq>'.$swq.'</seq>
                            <name>'.$name.'</name>
                            <sex>'.$sex.'</sex>
                            <cardNo>'.$cardNo.'</cardNo>
                            <workType>NV040208</workType>
                        </employee>';
            }

            //被保人信息
            $insured_xml = '<insured>
                            <seq>1</seq>
                            <relationship>601005</relationship>
                            <name>'.$tmp_data['ty_toubaoren']['name'].'</name>
                            <cardType>'.$tmp_data['ty_toubaoren']['cardType'].'</cardType>
                            <cardNo>'.$tmp_data['ty_toubaoren']['cardNo'].'</cardNo>
                            <mobile>'.$tmp_data['ty_toubaoren']['mobile'].'</mobile>
                        </insured>';

            //职业信息及雇主责任信息
            $customTgt_html = '<customTgt>
                            <insOccupNo>1</insOccupNo>
                            <perIndemLmt>10000000</perIndemLmt>
                            <resvNum12>1000000</resvNum12>
                            '.$lostWages.'
                        </customTgt>';

            $order_xml = '<order>
                            <orderId>'.$orderId.'</orderId>
                            <prodNo>'.$prodNo.'</prodNo>
                            <totalPremium>'.$totalPremium.'</totalPremium>
                            <totalAmount>'.$totalAmount.'</totalAmount>
                            <appTm>'.date("Y-m-d H:i:s").'</appTm>
                            <insBeginDate>'.$ins_start_time.'</insBeginDate>
                            <insEndDate>'.$ins_end_time.'</insEndDate>
                            <applyNum>1</applyNum>
                            <agentCode>9171346</agentCode>
                            '. $jobCoeff .'
                            '. $item_xml .'
                            '. $employee_xml .'
                            <employTgt>
                                <businessNature>001</businessNature>
                                <region>全国</region>
                            </employTgt>
                        </order>';
        }



        //团意主体信息
        if(in_array($productCode, $this->product_codes_ty)){
            $totalPremium = 0;
            //条款基础保费
            $premium_one = 138;
            $premium_two = 38;
            $premium_three = 17;
            switch($quote->ty_work_type){
                case '1-2类':
                    $coeff = 1;
                    break;
                case '3-4类':
                    $coeff = 1.5;
                    break;
                case '5类':
                    $coeff = 3;
                    break;
            }
            $jobCoeff = '<jobCoeff>' . $coeff . '</jobCoeff>';
            foreach($insurance->clauses as $k => $v){
                $s = $k +1 ;
                $seq = '<seq>' . $s . '</seq>';
                $isMain = '<isMain>0</isMain>';
                if($v['type'] == 'main'){
                    $isMain = '<isMain>1</isMain>';
                }
                //条款信息封装
                $itemCode = '<itemCode>' . $v->clause_code . '</itemCode>';    //险别代码
                $itemName = '<itemName>' . $v->name . '</itemName>';    //险别代码
                $premium = 0;
                $amount = 0;
                $freeCoeff = $dayAmt = '';
                $amount_html = '';
                //条款总保额、保费计算
                switch($v->clause_code){
                    case '060652':
                        $premium = $premium_one * $coeff;
                        $amount = $v->coverage ;
                        $amount_html = '<amount>' . $v->coverage . '</amount>';
                        break;
                    case '060059':
                        $premium = $premium_two * $coeff;
                        $amount = $v->coverage;
                        $amount_html = '<amount>' . $v->coverage . '</amount>';
                        $freeCoeff = '<freeCoeff>50免，100%赔付</freeCoeff>'; //免赔系数
                        break;
                    case '066439':
                        $premium = $premium_three * $coeff;
                        $amount = $v->coverage;
                        $amount_html = '<amount>' . $v->coverage . '</amount>';
                        $dayAmt = '<dayAmt>5000</dayAmt>';  //每日津贴
                        break;
                }

                $totalAmount += $amount;    //订单总保额
                $totalPremium += $premium;
                $premium_str = '<premium>'. $premium . '</premium>';
                $str = '<item>'.$seq.$isMain.$itemCode.$itemName.$amount_html.$premium_str.$freeCoeff.$dayAmt.'</item>';
                $item_xml .= $str;
            }

            foreach($input['insurance_attributes']['ty_beibaoren'] as $k => $v){
                $card_info = IdentityCardHelp::getIDCardInfo($v['ty_beibaoren_id_number']);
                if($card_info['status'] != 2){
                    return ['data'=> $v['ty_beibaoren_name'] . '证件信息有误', 'code'=>403];
                }
                $sex = $input['insurance_attributes']['ty_beibaoren'][$k]['ty_beibaoren_sex'] = ($card_info['sex']==1) ? '<sex>1</sex>' : '<sex>2</sex>';
                $input['insurance_attributes']['ty_beibaoren'][$k]['ty_beibaoren_birthday'] = $card_info['birthday'];
                $seq = '<seq>' . $k . '</seq>';
                $name = '<name>' . $v['ty_beibaoren_name'] . '</name>';
                $cardNo = '<cardNo>' . $v['ty_beibaoren_id_number'] . '</cardNo>';
                $birthday = '<birthday>' . $card_info['birthday'] . '</birthday>';
                $occupCde = '<occupCde>' . $v['ty_beibaoren_job'] . '</occupCde>';  //职业小类码
                $dutyName = '<dutyName>' . '农夫' . '</dutyName>';  //职业小类码
                $phone = '<mobile>' . $v['ty_beibaoren_phone'] . '</mobile>';
                $insured = '<insured>
                                <relationship>601008</relationship>
                                <cardType>120001</cardType>'
                                . $seq . $name . $cardNo . $birthday . $sex . $phone . $occupCde . $dutyName
                        .'</insured>';
                $insured_xml .= $insured;
            }
            $customTgt_html = '<customTgt>
                            <insOccupNo>1</insOccupNo>
                            <insTradeCde>J7190</insTradeCde>
                            <insTradeName>其他未列明的金融活动</insTradeName>
                            <linkName>哈哈哈</linkName>
                        </customTgt>';
            $order_xml = '<order>
                            <orderId>'.$orderId.'</orderId>
                            <prodNo>'.$prodNo.'</prodNo>
                            <totalPremium>'.round($totalPremium * $num).'</totalPremium>
                            <totalAmount>'.$totalAmount * $num.'</totalAmount>
                            <appTm>'.date("Y-m-d H:i:s").'</appTm>
                            <insBeginDate>'.$ins_start_time.'</insBeginDate>
                            <insEndDate>'.$ins_end_time.'</insEndDate>
                            <applyNum>1</applyNum>
                            <agentCode>9171346</agentCode>
                            '. $jobCoeff .'
                            '. $item_xml .'
                        </order>';
        }

        //投保人信息
        $holder_xml = '<holder>
                            <name>'.$tmp_data['ty_toubaoren']['name'].'</name>
                            <cardType>'.$tmp_data['ty_toubaoren']['cardType'].'</cardType>
                            <cardNo>'.$tmp_data['ty_toubaoren']['cardNo'].'</cardNo>
                            <mobile>'.$tmp_data['ty_toubaoren']['mobile'].'</mobile>
                            <email>'.$tmp_data['ty_toubaoren']['email'].'</email>
					        '.$address.$zipCode.'
                        </holder>';

        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <packageList>
                <package>
                    '. $header_xml .'
                    <request>
                        '. $order_xml .'
                        <applyInfo>
                            '. $holder_xml .'
                            '. $insured_xml .'
                        </applyInfo>
                            '.$customTgt_html.'
                    </request>
                </package>
            </packageList>';
//        LogHelper::logError($xml,'xml', 'axc', 'buy_ins');
        $sign = md5( $sign_key . $xml);
        $response = Curl::to($this->request_url .'?sign='.$sign.'&comId='.$code)
            ->returnResponseObject()
            ->withData($xml)
            ->withHeader("Content-Type: text/xml;")
            ->withTimeout(60)
            ->post();

        if($response->status == 200){
            $formatter = Formatter::make($response->content, Formatter::XML)->toArray();
            if($formatter['package']['header']['responseCode'] == '0000'){
//                dd($formatter);
                $buy_options = [];
                $buy_options['insurance_attributes'] = $input['insurance_attributes'];
                $buy_options['quote_selected'] = $quote_selected;
                $input['insurance_num'] = $num;
                return $this->addOrder($input, $formatter, $buy_options);
            } else {
//                dd($response->content);
                LogHelper::logError($xml, $formatter['package']['header']['errorMessage'], 'axc', 'buy_ins_error');
                $msg['data'] = $formatter['package']['header']['errorMessage'];
                $msg['code'] = 403;
//                dd($msg);
                return $msg;
            }
        } else {
//            dd($response);
            LogHelper::logError($xml, $response->content, 'axc', 'buy_ins_error_not_200');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }

    /**
     * 获取支付链接
     * @return array
     */
    public function getPayWayInfo()
    {

        $input = $this->decodeOriginData(); //解签获取源数据

        if(empty($input['pay_way'] || $input['union_order_code']))
            return ['data'=>'订单号或支付类型错误', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();
        if(empty($ins_order))
            return ['data'=>'订单号错误', 'code'=> 400];
        if($ins_order->status == 'check_error')
            return ['data'=> $ins_order->insures()->first()->check_error_message, 'code'=> 400];

        //1	微信-移动端(微信公众号支付）
        //2	支付宝-移动端
        //3	微信扫码-PC端
        //4	支付宝-PC端
        //7 微信-wap支付
        $pay_type = 0;
        switch($input['is_phone']){
            case 0: //pc端
                switch($input['pay_way']){
                    case 'aliPay': //支付宝
                        $pay_type = 4;
                        break;
                    case 'wechatPay': //微信
                        $pay_type = 3;
                        break;
                }
                break;

            case 1:
                switch($input['pay_way']){
                    case 'aliPay': //支付宝
                        $pay_type = 2;
                        break;
                    case 'wechatPay': //微信
                        $pay_type = 7;
                        break;
                }
                break;
        }

        $url = 'http://axwxtest.95303.com';  //地址
        $data = array();
        $data['transNo'] = $ins_order->union_order_code;  //商户订单号 编码规则：渠道编码+yyyyMMrrHHmmss+随机数
        $data['requestCode'] = 'EC16030003';  //请求方代码
//        $data['transAmt'] = $ins_order->total_premium; //以分为单位Int类型
        $data['transAmt'] = 1; //以分为单位Int类型
        $data['payName'] = $ins_order->insurance->name; //支付商品的描述
        $data['bgRetUrl'] = urlencode(env('APP_URL') . '/api/ins/axc/call_back'); //后台回调地址
        $data['payCancelURL'] = urlencode($input['redirect_url']); //支付取消页面地址
        $data['payFinishURL'] = urlencode($input['redirect_url']); //支付完成页面地址
        $data['payType'] = $pay_type; //支付方式
        ksort($data);
        $str = '';
        foreach($data as $k=>$v){
            $str .= $k.'='.$v.'&';
        }
        $str = trim($str,'&');
        $data['checkValue'] = strtolower(md5('xxx'.$str)); //通过md5对(合作验证码 + 原始数据) 进行签名

        $response = Curl::to($url . '/axPay/product/authorization/')
            ->returnResponseObject()
            ->withData($data)
            ->withTimeout(60)
            ->post();
//        print_r($response->content);die;
        LogHelper::logSuccess($response->content, 'axc', 'get_pay_way_info');
        if($response->status != 200)
            return ['data'=> '获取支付信息失败!', 'code'=> 400];

        $rule = "/已支付/";
        $num_matches = preg_match($rule, $response->content);
        if($num_matches > 0)
            return ['data'=> '订单已支付!', 'code'=> 400];

        $url = $this->getUrl($input, $response->content, $url);
        if(!$url)
            return ['data'=> '支付失败，请稍后重试!', 'code'=> 400];

        $ins_order->status = 'pay_ing';
        $ins_order->save();
        Insure::where('ins_order_id', $ins_order->id)->update(['status'=>'pay_ing']);

        return ['data'=>['order_code'=>$ins_order->union_order_code, 'pay_way_data'=> ['url'=>$url]], 'code'=> 200];
    }

    /**
     * 支付回掉
     * @return string
     */
    public function payCallBack()
    {
        $input = $this->request->all();
        LogHelper::logSuccess($input, 'axc', 'pay_call_back');
        if($input['payResult'] == 1){
            $order = InsOrder::where(['union_order_code'=> $input['transNo'], 'api_from_uuid'=> 'Axc'])->first();
//            LogHelper::logSuccess($order, 'axc', 'pay_call_back_order');
            $user = User::where('account_id', $order->create_account_id)->first();
//            LogHelper::logSuccess($user, 'axc', 'pay_call_back_user');
            $order->pay_code = $input['payNo'];
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
                    'union_order_code' => $input['transNo'],
                    'by_stages_way' => $order->by_stages_way,
                    'error_message' => '',
                ]
            ];
//            LogHelper::logSuccess($user->call_back_url,'axc', 'pay_call_back_url');
            $url = $user->call_back_url??"http://n183967a96.iask.in:16367";
            $response = Curl::to( $url. '/ins/call_back')
                ->returnResponseObject()
                ->withData($data)
                ->asJson()
                ->withTimeout(60)
                ->post();
//            LogHelper::logSuccess($response,'pay_call_back_success');
            return 'success';
        }
    }

    public function issue()
    {
        $input = $this->decodeOriginData(); //解签获取源数据
        LogHelper::logSuccess($input);
        if(!$input['union_order_code'])
            return ['data'=>'订单号不可为空', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();  //订单
        if(empty($ins_order))
            return ['data'=>'订单不存在', 'code'=> 400];
        if(!in_array($ins_order->status, ['pay_end']))
            return ['data'=>'此订单无法出单', 'code'=> 400];
        $insurance = Insurance::where('id', $this->api_m_info->insurance_id)->with(['clauses'])->first();   //产品
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <packageList>
                <package>
                    <header>
                        <requestType>INSURE</requestType>
                        <from>'.$this->code.'</from>
                        <sendTime>'.date('Y-m-d H:i:s').'</sendTime>
                        <orderId>'.$ins_order->union_order_code.'</orderId>
                        <payNo>'.$ins_order->pay_code.'</payNo>
                        <payType>1</payType>
                        <payTime>'.$ins_order->updated_at.'</payTime>
                        <productCode>'.$this->api_m_info->p_code.'</productCode>
                        <productName>'.$insurance->name.'</productName>
                    </header>
                </package>
            </packageList>';

        $sign = md5( $this->sign_key . $xml);
        $response = Curl::to($this->request_url .'?sign='.$sign.'&comId='.$this->code)
            ->returnResponseObject()
            ->withData($xml)
            ->withHeader("Content-Type: text/xml;")
            ->withTimeout(60)
            ->post();
//        dd($response->content);
        if($response->status == 200) {
            $formatter = Formatter::make($response->content, Formatter::XML)->toArray();
            if($formatter['package']['header']['responseCode'] == '0000'){
                $policy_order_code = $formatter['package']['response']['proposal']['policyNo'];
                Insure::where('out_order_no', $ins_order->union_order_code)->update([
                    'ins_policy_code'=>$policy_order_code,
                    'policy_status' => 6
                ]);
                $return = array();
                //返回结果封装 todo
                $return['status'] = 0;    //出单状态 0：未生效 1：已生效 2：退保中 3：已退保
                $return['policy_order_code'] = $policy_order_code;   //被保人保单号
                $return['private_p_code'] = $input['private_p_code'];   //产品码
                $return['order_code'] = $input['order_code'];   //被保人单号

                $return['start_time'] = $ins_order->start_time;
                $return['end_time'] = $ins_order->end_time;
//                $return['projects'] = $protect_items;
                return ['data'=> $return, 'code'=> 200];
            } else {
                //todo
            }

        } else{
            LogHelper::logError($xml, $response->content, 'axc', 'issue');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }

    public function addOrder($original_data, $return_data, $buy_options)
    {
        //dd($return_data);
        try {
            DB::beginTransaction();
            //订单信息
            $order = new InsOrder();
            $order->order_no = $return_data['package']['response']['proposal']['orderId'];
            $order->union_order_code = $return_data['package']['response']['proposal']['orderId']; //外部合并订单号
            $order->create_account_id = $this->request['account_id'];  //代理商account_id
            $order->api_from_uuid = ApiFrom::where('id', $this->api_m_info->api_from_id)->first()->uuid;    //接口来源唯一码（接口类名称）
            $order->api_from_id = $this->api_m_info->api_from_id;
            $order->ins_id = $this->api_m_info->insurance_id;
            $order->p_code = $this->api_m_info->p_code; //产品唯一码
            $order->bind_id = $this->api_m_info->id;
            $order->total_premium = $return_data['package']['response']['proposal']['totalPremium'];  //总保费
            $order->status = 'check_ing'; //待核保状态
            $order->buy_options = json_encode($buy_options, JSON_UNESCAPED_UNICODE);
            $order->by_stages_way = '0年';
            $order->start_time = $return_data['package']['response']['proposal']['insBeginDate'];   //生效日期
            $order->end_time = $return_data['package']['response']['proposal']['insEndDate'];  //结束日期
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
                $insures[$k]['ins_order_id'] = $order->id;   //订单号
                //todo
                $insures[$k]['out_order_no'] =  $insures[$k]['union_order_code'] = $order->union_order_code;   //被保人单号(分接口来源) 外部合并订单号
                $insures[$k]['premium'] = $return_data['package']['response']['proposal']['totalPremium'] / $original_data['insurance_num'];    //保费
                $insures[$k]['p_code'] = $this->api_m_info->p_code;    //外部产品码
                $insures[$k]['name'] = $v['ty_beibaoren_name'];
//                    $insures[$k]['sex'] = $v['ty_beibaoren_sex'];
//                    $insures[$k]['phone'] = $v['ty_beibaoren_phone'];
                $insures[$k]['card_type'] = '120001';
                $insures[$k]['card_id'] = $v['ty_beibaoren_id_number'];
                $insures[$k]['relation'] = 31;
//                    $insures[$k]['birthday'] = $v['ty_beibaoren_birthday'];
                $insures[$k]['ins_start_time'] = $return_data['package']['response']['proposal']['insBeginDate'];   //生效日期
                $insures[$k]['ins_end_time'] = $return_data['package']['response']['proposal']['insEndDate'];  //结束日期
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
            $res['total_premium'] = $return_data['package']['response']['proposal']['totalPremium'];
            $res['union_order_code'] = $return_data['package']['response']['proposal']['orderId'];
            $res['pay_way'] = [
                'pc'=>[
                    'aliPay'=> '支付宝支付',
                    'wechatPay'=> '微信支付'
                ],
                'mobile'=>[
                    'aliPay'=> '支付宝支付',
                    'wechatPay'=> '微信支付'
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
     * 正则获取HTML中的支付链接
     * @param $input
     * @param $return
     * @param string $url
     * @return bool|string
     */
    protected function getUrl($input, $return, $url=''){
        try{
            $pay_url = '';
            $rule = ''; //正则规则
            switch($input['is_phone']){
                case 0: //pc端
                    switch($input['pay_way']){
                        case 'aliPay': //支付宝
                            $rule = "/window.location.href\s=\s[\'|\"](.*)[\'|\"];/";
                            preg_match($rule, $return, $m);
                            $pay_url = $m[1];
                            break;
                        case 'wechatPay': //微信
                            $rule = "/'src','(.*)'\);/";
                            preg_match($rule, $return, $m);
                            $pay_url = $url . $m[1];
                            break;
                    }
                    break;

                case 1:
                    switch($input['pay_way']){
                        case 'aliPay': //支付宝
                            $rule = "/window.location.href\s=\s[\'|\"](.*)[\'|\"];/";
                            preg_match($rule, $return, $m);
                            $pay_url = $m[1];
                            break;
                        case 'wechatPay': //微信
                            $rule = "/window.location.href\s=\s[\'|\"](.*)[\'|\"];/";
                            preg_match($rule, $return, $m);
                            $pay_url = $m[1];
                            break;
                    }
                    break;
            }
            return $pay_url;
        }catch (\Exception $e){
            return false;
        }

    }

    protected function decodeOriginData()
    {
        $input = $this->request->all();
        //业务参数 解析出源数据json字符串
        $original_data_array = $this->sign_help->tyDecodeOriginData($input['biz_content']);
        return $original_data_array;
    }

    //todo delete 雇主
    public function testExcelGy()
    {
        $a = ExcelHelper::excelToArray("upload/tariff/test_axc_gz.xlsx");
        $tariff = $a->first()->toArray();
        $field = array_shift($tariff);
        $insert_data = array();
        foreach($tariff as $k => $v){
            foreach($v as $vk => $vv){
                $insert_data[$k][$field[$vk]] = str_replace(array("\r\n", "\r", "\n"),  '', $vv);
            }
        }
        DB::table('tariff_axc_gz')->insert($insert_data);
    }

    //todo delete 团意
    public function testExcelTy()
    {
        $a = ExcelHelper::excelToArray("upload/tariff/test_axc_ty.xlsx");
        $tariff = $a->first()->toArray();
        $field = array_shift($tariff);
        $insert_data = array();
        foreach($tariff as $k => $v){
            foreach($v as $vk => $vv){
                $insert_data[$k][$field[$vk]] = str_replace(array("\r\n", "\r", "\n"),  '', $vv);
            }
        }
        DB::table('tariff_axc_ty')->insert($insert_data);
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
     *
     * @param $restrict_genes
     * @param $data
     * @return mixed
     */
//    protected function transformToApiWay($restrict_genes, $data)
//    {
//        foreach ($restrict_genes as $restrict_gene) {
//            if (isset($data[$restrict_gene['ty_key']])) {
//                foreach ($restrict_gene['values'] as $value) {
//                    if ($data[$restrict_gene['ty_key']] == $value['ty_value']) {
//                        $data[$restrict_gene['ty_key']] = $value['value'];
//                        break;
//                    }
//                }
//                $data[$restrict_gene['key']] = $data[$restrict_gene['ty_key']];
//                unset($data[$restrict_gene['ty_key']]);
//            }
//        }
//
//        return $data;
//    }

    /**
     * 去掉不用的Key
     *
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

    protected function formatQuote($only_ty_key_array)
    {
        $where = array();
        foreach($only_ty_key_array as $k => $v){
            $where[$v['ty_key']] = $v['value'];
        }
        if(in_array($this->api_m_info->p_code, $this->product_codes_ty)){
            $quote = DB::table('tariff_axc_ty')->where($where)->first();
        } else {
            $quote = DB::table('tariff_axc_gz')->where($where)->first();
        }
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
     * 获得默认试算因子计算出的价格
     * @param $data
     * @return array
     */
//    protected function getDefaultQuotePrice($data)
//    {
//        return 1;
//    }

}