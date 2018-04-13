<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2018/01/05
 * Time: 11:15
 * 英大泰和财产保险股份有限公司 产品处理：详情，算费，核保，支付，承保，出单等。
 */
namespace App\Http\Controllers\ApiControllers\Curls;

use App\Helper\LogHelper;
use DB;
use App\Models\Insurance;
use App\Helper\ExcelHelper;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Ixudra\Curl\Facades\Curl;
use SoapBox\Formatter\Formatter;
use App\Repositories\RestrictGeneRepository;
use App\Repositories\InsuranceApiFromRepository;
use App\Repositories\InsuranceAttributesRepository;
use App\Helper\AesCrypt;
use App\Models\InsOrder;
use App\Models\ApiFrom;
use App\Models\Policy;
use App\Models\Insure;
use App\Models\User;
use App\Models\OrderFinance;
use App\Models\InsApiBrokerage;
use laraveldes3\Des3;

class YdthInsCurlController
{
    /**
     *
     *TODO  测试des3加密
     *
     */
    public function index()
    {
        $des = new Des3();
        // 加密
        $encrypt = $des->encrypt('123456qwe');
        echo $encrypt.'<br/>';

        // 解密
        $decrypt =  $des->decrypt($encrypt);
        echo $decrypt;
    }
    /**
     * 核保支付接口
     *TODO  没有接口地址，非必传的默认传空
     *
     */
    public function buyIns()
    {
        //报文头信息RequstHeadDto
        //必传
        $requestType = '01';//	请求类型	Y	String	默认 01
        $user = 'ydcxCard';//		用户名	Y	String	默认ydcxCard
        $passWord = 'Ydcx5196Card';//		密码	Y	String	默认Ydcx5196Card
        $tradeTime = date('YmdHis',time());//交易时间 格式为：年月日时分秒，YYYYMMDDHHMMSS，中间不用连接符号
        $responseType = '01';//响应类型	01-实时响应（
        $signData = '';//数据加密
        //非必传，默认传空
        $queryId = '';//唯一标志码
        $sourceSystemCode = '';//来源系统代码
        $versionNo = '';//版本号
        $areaCode = '';//区域/机构代码
        $areaName = '';//区域/机构名称
        //XML
        $RequstHeadDto_XML = '<RequestHeadDto>
                                <requestType>'.$requestType.'</requestType>
                                <user>'.$user.'</user>
                                <passWord>'.$passWord.'</passWord>
                                <queryId>'.$queryId.'</queryId>
                                <sourceSystemCode>'.$sourceSystemCode.'</sourceSystemCode>
                                <versionNo>'.$versionNo.'</versionNo>
                                <areaCode>'.$areaCode.'</areaCode>
                                <areaName>'.$areaName.'</areaName>
                                <tradeTime>'.$tradeTime.'</tradeTime>
                                <responseType>'.$responseType.'</responseType>
                                <signData>'.$signData.'</signData>
                            </RequestHeadDto>';
        //请求主信息 TYInsProposalRequestMainDto
        //渠道信息
        $channelCode = '190000';//渠道代码默认,190000
        $channelName = '';//渠道名称
        $channelComCode = '';//渠道机构代码	N	String
        $channelComName = '';//渠道机构名称	N	String
        $channelProductCode = '';//渠道产品代码	N	String
        $channelOperateCode = '';//渠道操作员代码	N	String
        $channelTradeCode = '1900016';//渠道交易代码	Y	String	默认值，英大提供（待定）
        $channelTradeSerialNo = '201704271711043';//渠道交易流水号
        $channelRelationNo = '';//渠道关联单号
        $channelTradeDate = date('Ymd',time());//渠道交易日期
        $ChannelDto_XML = '<ChannelDto>
                    <channelCode>'.$channelCode.'</channelCode>
                    <channelName>'.$channelName.'</channelName>
                    <channelComCode>'.$channelComCode.'</channelComCode>
                    <channelComName>'.$channelComName.'</channelComName>
                    <channelProductCode>'.$channelProductCode.'</channelProductCode>
                    <channelOperateCode>'.$channelOperateCode.'</channelOperateCode>
                    <channelTradeCode>'.$channelTradeCode.'</channelTradeCode>
                    <channelTradeSerialNo>'.$channelTradeSerialNo.'</channelTradeSerialNo>
                    <channelRelationNo>'.$channelRelationNo.'</channelRelationNo>
                    <channelTradeDate>'.$channelTradeDate.'</channelTradeDate>
                </ChannelDto>';
        //投保人信息
        $insuredAppliType = '1';//投保人类型
        $insuredAppliName = '王石磊';//名称
        $identifyType = '01';//证件类型（身份证）
        $identifyNumber = '';//证件号码
        $insuredIdentity = '01';//与被保人关系(本人)
        $linkMobile = '15701681524';//手机
        $linkTel = '';//电话
        $hoderData = $this->getMSgByID($identifyNumber);
        $sex = $hoderData['sex'];//性别
        $birth = $hoderData['birth'];//出生日期
        $age = $hoderData['age'];//年龄
        $email = 'wangsl@inschos.com';//投保人邮箱地址
        $appliAddress = '北京市东城区夕照寺中街14号';//投保人地址
        $InsuredAppliDto_XML = '<InsuredAppliDto>
                                    <insuredAppliType>'.$insuredAppliType.'</insuredAppliType>
                                    <insuredAppliName>'.$insuredAppliName.'</insuredAppliName>
                                    <identifyType>'.$identifyType.'</identifyType>
                                    <identifyNumber>'.$identifyNumber.'</identifyNumber>
                                    <insuredIdentity>'.$insuredIdentity.'</insuredIdentity>
                                    <linkMobile>'.$linkMobile.'</linkMobile>
                                    <linkTel>'.$linkTel.'</linkTel>
                                    <sex>'.$sex.'</sex>
                                    <birth>'.$birth.'</birth>
                                    <age>'.$age.'</age>
                                    <email>'.$email.'</email>
                                    <appliAddress>'.$appliAddress.'</appliAddress>
                                </InsuredAppliDto>';
        //被保人信息（列表，可以有多个被保人）
        $insuredType = '1';//	被保险人类型	Y	String	默认值（个人为1，单位为2）
        $insuredName = '王石磊';//	名称	Y	String
        $identifyType = '01';//	证件类型	Y	String
        $identifyNumber = '';//	证件号码	Y	String
        $linkMobile = '15701681524';//	手机	N	String
        $insureData = $this->getMSgByID($identifyNumber);
        $sex = $insureData['sex'];//性别
        $birth = $insureData['birth'];//出生日期
        $age = $insureData['age'];//年龄
        $relationSerialType = '01';//	与投保人关系	Y	String	英大提供（见关系人码表）
        $occupationCode = '';//	职业代码 	Y	String	英大提供（见职业代码码表）
        $occupationName = '';//	职业名称 	N	String
        $occupationLevel = '';//	职业类别 	N	String
        $insuredAddress = '';//	被保险人地址	Ｙ	String
        $countyCode = '';//	被保人地址代码	Ｙ	String	英大提供（见被保险人地址代码码表）
        $physicalExamination = '';//	被保人是否体检	S	String	0否，1是（2729，2771，2750险种必传）
        $InsuredDtoList_XML = '<InsuredDtoList>
                                    <InsuredDto>
                                        <insuredType>'.$insuredType.'</insuredType>
                                        <insuredName>'.$insuredName.'</insuredName>
                                        <identifyType>'.$identifyType.'</identifyType>
                                        <identifyNumber>'.$identifyNumber.'</identifyNumber>
                                        <linkMobile>'.$linkMobile.'</linkMobile>
                                        <sex>'.$sex.'</sex>
                                        <birth>'.$birth.'</birth>
                                        <age>'.$age.'</age>
                                        <relationSerialType>'.$relationSerialType.'</relationSerialType>
                                        <occupationCode>'.$occupationCode.'</occupationCode>
                                        <occupationName>'.$occupationName.'</occupationName>
                                        <occupationLevel>'.$occupationLevel.'</occupationLevel>
                                        <insuredAddress>'.$insuredAddress.'</insuredAddress>
                                        <countyCode>'.$countyCode.'</countyCode>
                                        <physicalExamination>'.$physicalExamination.'</physicalExamination>
                                    </InsuredDto>
                                </InsuredDtoList>';
        $policyType = '01';//	保单类型	Y	String	默认01-个人
        $payMode = '2';//	缴费方式	N	String	1.现金2.银行转账9.其他
        $methodNo = '27600101';//   	方案号 	Y	String	英大提供（根据具体的方案传值）
        $policyNo = '1110727272017000001YD';//	保单号	S	String	若是选择多日投保方案时，将已有保单号传值
        $combProductFlag = '0';//	组合产品标识	Y	String	0 非组合 1 组合险
        $provinceCode = '';//	被保险财产地址省级代码	S	String	家财企财险种时必传(依据归属机构而定)
        $areaCode = '';//	被保险财产地址市级代码	S	String	家财企财险种时必传(依据归属机构而定)
        $houseAddress = '';//	被保险财产地址	N	String
        $cardProductFlag = 'TY';//	渠道标识	Y	String	默认TY
        $inputDate = date('Y-m-d',time());//	投保日期	Y	String
        $startDate = date('Y-m-d',time());//	起保日期	Y	String
        $startHour = '';//	起保小时	Y	String
        $startMinute = '';//	起保分钟	S	Strnig	根据具体的产品而定
        $endDate = date('Y-m-d',time());//	终保日期	Y	String
        $endHour = date('Y-m-d',time());//	终保小时	Y	String
        $endMinute = '';//	终保分钟	S	String	根据具体的产品而定
        //TODO  不知道怎么传值
        $updaterCode = '';//	最后一次修改人员代码	Y	String
        $makeCom = '08080100';//	出单机构代码	Y	String
        $comCode = '08080100';//	归属机构代码	Y	String
        $operatorCode = '08070103';//	操作员代码/第一次录入人员代码	Y	String
        $operatorName = '08070103';//	操作员名称	Y	String
        $handlerCode = '08080002';//	经办人代码	Y	String
        $handler1Code = '08080002';//		归属业务员代码	Y	String
        $shareholderFlag = '0';//		一级业务标识	Y	String
        $businessNature = '4';//		二级业务标识	Y	String
        $shareholderName = '';//		三级业务标识	Y	String
        $shareholderKindCode = '';//		四级业务标识	Y	String
        $accountNo = '6222022002006651860';//		银行卡或存折号码	  S	String	需要直接从卡里划走保费时必传
        $accountName = '王石磊';//		银行卡或存折上的所有人姓名	  S	String	需要直接从卡里划走保费时必传
        $TYInsProposalRequestMainDto_XML = '<TYInsProposalRequestMainDto>
                                                <methodNo>27600101</methodNo>
                                                <policyNo>1110727272017000001YD</policyNo>
                                                <policyType>'.$policyType.'</policyType>
                                                <payMode>'.$payMode.'</payMode>
                                                <combProductFlag>'.$combProductFlag.'</combProductFlag>
                                                <provinceCode>'.$provinceCode.'</provinceCode>
                                                <areaCode>'.$areaCode.'</areaCode>
                                                <houseAddress>'.$houseAddress.'</houseAddress>
                                                <cardProductFlag>'.$cardProductFlag.'</cardProductFlag>
                                                <inputDate>'.$inputDate.'</inputDate>
                                                <startDate>'.$startDate.'</startDate>
                                                <startHour>'.$startHour.'</startHour>
                                                <startMinute>'.$startMinute.'</startMinute>
                                                <endDate>'.$endDate.'</endDate>
                                                <endHour>'.$endHour.'</endHour>
                                                <endMinute>'.$endMinute.'</endMinute>
                                                <updaterCode>'.$updaterCode.'</updaterCode>
                                                <makeCom>'.$makeCom.'</makeCom>
                                                <comCode>'.$comCode.'</comCode>
                                                <operatorCode>'.$operatorCode.'</operatorCode>
                                                <handler1Code>'.$handler1Code.'</handler1Code>
                                                <handlerCode>'.$handlerCode.'</handlerCode>
                                                <shareholderFlag>'.$shareholderFlag.'</shareholderFlag>
                                                <businessNature>'.$businessNature.'</businessNature>
                                                <shareholderName>'.$shareholderName.'</shareholderName>
                                                <shareholderKindCode>'.$shareholderKindCode.'</shareholderKindCode>
                                                <operatorName>'.$operatorName.'</operatorName>
                                                <accountNo>'.$accountNo.'</accountNo>
                                                <accountName>'.$accountName.'</accountName>
                                                '.$ChannelDto_XML.$InsuredAppliDto_XML.$InsuredDtoList_XML.'
                                            </TYInsProposalRequestMainDto>';
        //整个xml报文
        $xml = '<?xml version=\'1.0\' encoding=\'GBK\'?>
                    <TYInsProposalRequest>
                        <RequestHeadDto>
                        '.$RequstHeadDto_XML.'
                        </RequestHeadDto>
                        <TYInsProposalRequestMainDto>
                        '.$TYInsProposalRequestMainDto_XML.'
                        </TYInsProposalRequestMainDto>
                    </TYInsProposalRequest>';
        //除去非法字符
        $xml = $this->compress_xml($xml);
        $url = '';
        $response = Curl::to($url)
            ->returnResponseObject()
            ->withData($xml)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        dd($response);die;
//        $return_data = preg_replace( "/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/ ", ' ', $return_data);//去掉非法字符
//        if($response->status == 200){
//            $formatter = Formatter::make($return_data, Formatter::XML)->toArray();
//            if($formatter['Head']['Flag'] == '0'){
//                $buy_options = [];
//                $buy_options['insurance_attributes'] = $input['insurance_attributes'];
//                $buy_options['quote_selected'] = json_decode($input['quote_selected'], true);  //算费已选项
//                $add_order_res = $this->addOrder($input, $formatter, $buy_options);
//                return $add_order_res;
//            } else {
//                LogHelper::logError($xml, $formatter['Head']['Desc'], 'Hg', 'buy_ins_error');
//                $msg['data'] = $formatter['Head']['Desc'];
//                $msg['code'] = 403;
//                return $msg;
//            }
//        } else {
//            LogHelper::logError($xml, $response->content,  'hg', 'insure_issue_error');
//            $msg['data'] = '请求异常';
//            $msg['code'] = $response->status;
//            return $msg;
//        }
    }

    /**
     * 保单查询接口
     *TODO  没有接口地址，非必传的默认传空
     *
     */
    public function selPolicy(){
        //报文头信息RequstHeadDto
        //必传
        $requestType = '01';//	请求类型	Y	String	默认 01
        $user = 'ydcxCard';//		用户名	Y	String	默认ydcxCard
        $passWord = 'Ydcx5196Card';//		密码	Y	String	默认Ydcx5196Card
        $tradeTime = date('YmdHis',time());//交易时间 格式为：年月日时分秒，YYYYMMDDHHMMSS，中间不用连接符号
        $responseType = '01';//响应类型	01-实时响应（
        $signData = '';//数据加密
        //非必传，默认传空
        $queryId = '';//唯一标志码
        $sourceSystemCode = '';//来源系统代码
        $versionNo = '';//版本号
        $areaCode = '';//区域/机构代码
        $areaName = '';//区域/机构名称
        //XML
        $RequstHeadDto_XML = '<RequestHeadDto>
                                <requestType>'.$requestType.'</requestType>
                                <user>'.$user.'</user>
                                <passWord>'.$passWord.'</passWord>
                                <queryId>'.$queryId.'</queryId>
                                <sourceSystemCode>'.$sourceSystemCode.'</sourceSystemCode>
                                <versionNo>'.$versionNo.'</versionNo>
                                <areaCode>'.$areaCode.'</areaCode>
                                <areaName>'.$areaName.'</areaName>
                                <tradeTime>'.$tradeTime.'</tradeTime>
                                <responseType>'.$responseType.'</responseType>
                                <signData>'.$signData.'</signData>
                            </RequestHeadDto>';
        //请求主信息NoCarPrPoEnQueryRequestDto
        $channelCode = '190000';//渠道代码	Y	String	默认190000
        $channelName = '';//渠道名称	N	String
        $channelComCode = '';//渠道机构代码	N	String
        $channelComName = '';//渠道机构名称	N	String
        $channelProductCode = '';//渠道产品代码	N	String
        $channelOperateCode = '';//渠道操作员代码	N	String
        $channelTradeCode = '1900016';//渠道交易代码	Y	String	默认值，英大提供（待定）
        $channelTradeSerialNo = '201704271711043';//渠道交易流水号	Y	String
        $channelRelationNo = '';//渠道关联单号	Y	String
        $channelTradeDate = '20170427';//渠道交易日期	Y	String
        $ChannelDto_XML = '<ChannelDto>
                                <channelCode>'.$channelCode.'</channelCode>
                                <channelName>'.$channelName.'</channelName>
                                <channelComCode>'.$channelComCode.'</channelComCode>
                                <channelComName>'.$channelComName.'</channelComName>
                                <channelProductCode>'.$channelProductCode.'</channelProductCode>
                                <channelOperateCode>'.$channelOperateCode.'</channelOperateCode>
                                <channelTradeCode>'.$channelTradeCode.'</channelTradeCode>
                                <channelTradeSerialNo>'.$channelTradeSerialNo.'</channelTradeSerialNo>
                                <channelRelationNo>'.$channelRelationNo.'</channelRelationNo>
                                <channelTradeDate>'.$channelTradeDate.'</channelTradeDate>
                            </ChannelDto>';
        $businessNo = '1110727272017000001YD';//业务号	Y	String
        $businessType = 'C';//业务类型	Y	String	T--投保单，C--保单，P--批单
        $cardProductFlag = 'JSSJ';//渠道标识	Y	String
        $xml3 = '<NoCarPrPoEnQueryRequestDto>
                    <businessNo>'.$businessNo.'</businessNo >
                    <businessType>'.$businessNo.'</businessType >
                    <cardProductFlag>'.$businessNo.'</cardProductFlag>
                    '.$ChannelDto_XML.'
                </NoCarPrPoEnQueryRequestDto>';
        //NoCarPrPoEnQueryRespoonse
        $ResponseHeadDto = '';//报文头信息		Object
        $requestType = '';//请求类型	N	String	参见：数据字典中的 编号0001 的代码定义
        $responseCode = '';//响应类型	N	String	0-成功  1-应用失败 2-ESB失败
        $errorCode = '';//错误代码	N	String	返回3位错误代码
        $errorMessage = '';//错误描述	N	String	返回错误信息
        $esbCode = '';//ESB错误代码	Y	String	返回3位错误代码
        $esbMessage = '';//ESB错误描述	Y	String	返回错误信息
        $signData = '';//数据加密	Y	String
        //整个xml报文
        $xml = '<?xml version=\'1.0\' encoding=\'GBK\'?>
                    <NoCarPrPoEnQueryRequest>
                     '.$xml3.'            
                    </NoCarPrPoEnQueryRequest>';
        //除去非法字符
        $xml = $this->compress_xml($xml);
        $url = '';
        $response = Curl::to($url)
            ->returnResponseObject()
            ->withData($xml)
            ->withHeader("Content-Type:text/plain;operator:5001")
            ->withTimeout(60)
            ->post();
        dd($response);die;
//        $return_data = preg_replace( "/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/ ", ' ', $return_data);//去掉非法字符
//        if($response->status == 200){
//            $formatter = Formatter::make($return_data, Formatter::XML)->toArray();
//            if($formatter['Head']['Flag'] == '0'){
//                $buy_options = [];
//                $buy_options['insurance_attributes'] = $input['insurance_attributes'];
//                $buy_options['quote_selected'] = json_decode($input['quote_selected'], true);  //算费已选项
//                $add_order_res = $this->addOrder($input, $formatter, $buy_options);
//                return $add_order_res;
//            } else {
//                LogHelper::logError($xml, $formatter['Head']['Desc'], 'Hg', 'buy_ins_error');
//                $msg['data'] = $formatter['Head']['Desc'];
//                $msg['code'] = 403;
//                return $msg;
//            }
//        } else {
//            LogHelper::logError($xml, $response->content,  'hg', 'insure_issue_error');
//            $msg['data'] = '请求异常';
//            $msg['code'] = $response->status;
//            return $msg;
//        }


    }

    /**
     * 测试回调中介系统
     *
     */
    public function TestCallBack(){
        $input = '{"notice_type":"pay_call_back","data":{"status":true,"ratio_for_agency":"49","brokerage_for_agency":98,"union_order_code":"000021122201824017257554739","by_stages_way":"0年","error_message":"泰康支付平台支付成功"}}';
        $return = json_decode($input,true);
//        $order = InsOrder::where(['union_order_code'=> $return['data']['insureNum'], 'api_from_uuid'=> 'Qx'])->first();
//        $user = User::where('account_id', $order->create_account_id)->first();
//        $url = $user->call_back_url . '/ins/call_back';
        $response = Curl::to('http://n183967a96.iask.in/ins/call_back')
            ->returnResponseObject()
            ->withData($return)
            ->asJson()
            ->withTimeout(60)
            ->post();
        if ($response->status != 200) {
            return json_encode(['state' => false, 'failMsg' => '回调处理失败'], JSON_UNESCAPED_UNICODE);
        }
        return json_encode(['state' => true]);
    }




//    =====================================================公共方法===============================================================
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
     * 根据身份证号获取性别，生日，年龄（周岁）
     * @param $id 身份证号
     * @return $data
     */
    protected function getMSgByID($id){
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
        $sex = substr($id, (strlen($id)==15 ? -2 : -1), 1) % 2 ? '1' : '0'; //1为男 2为女
        $data = [];
        $data['sex'] = $sex;
        $data['birth'] = $birth;
        $data['age'] = $age;
        return $data;
    }
}