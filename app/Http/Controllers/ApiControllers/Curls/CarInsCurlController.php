<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2017/11/3
 * Time: 10:51
 */

namespace App\Http\Controllers\ApiControllers\Curls;


use App\Helper\LogHelper;
use App\Models\CarWarranty;
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

class CarInsCurlController
{
    protected $request;
    protected $application_id;

    public function __construct(Request $request)
    {
        $this->sign_help = new RsaSignHelp();
        $this->request = $request;
        $this->application_id = "18895935";
        $this->public_key = file_get_contents('../config/car_rsa_public_key.pem');
        $this->private_key = file_get_contents('../config/car_rsa_private_key.pem');
    }

    protected function decodeOriginData()
    {
        $input = $this->request->all();
        //业务参数 解析出源数据json字符串
        $original_data_array = $this->sign_help->tyDecodeOriginData($input['biz_content']);
        return $original_data_array;
    }

    /**
     * 查询车辆信息
     */
    public function getCarInfo()
    {
        $input = $this->decodeOriginData();
//        $city_code = isset($input['city_num']) ? $input['city_num'] : '310000';

        if(!isset($input['type']) || !isset($input['car_number']) || !in_array($input['type'], ['license', 'frame']) || !$input['city_num']){
            return response('参数异常', 400);
        }
        $city_code = explode('-', $input['city_num']);

        $car_info = array();
        switch($input['type']){
            case 'license':
                $car_info = $this->getCarInfoByLicenseNo($input['car_number']);
                break;
            case 'frame':
                $car_info = $this->getCarInfoByFrameNo($input['car_number']);
                break;
        }
//        $clauses = $this->getClauseInfo();
        $car_info = json_decode($car_info, true);
        if($car_info['status'] == '1'){
            $response_no = $car_info['data']['responseNo'];
            $clauses = $this->getClauseInfo();  //条款信息
            $car_info['data']['clauses'] = $clauses;
            $company = $this->getCompany($city_code);
            $car_info['data']['company'] = $company;
        }
        return $car_info;
    }

    /**
     * 通过车牌号查询车辆车型信息
     * @param $license_no
     * @return int
     */
    protected function getCarInfoByLicenseNo($license_no)
    {
        $data = array();
        //业务参数
        $data['data']['licenseNo'] = $license_no;   //车牌号码
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/auto/vehicleAndModel')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
        $content = $response->content;
        if($content['state'] == 0)
            return $this->returnData(0, $content['msg'], 4000);
        return $this->returnData(1, 'success', 200, $content['data']);
    }

    /**
     * 通过车架号查询车辆车型信息
     * @param $frame_no
     * @return int
     */
    protected function getCarInfoByFrameNo($frame_no)
    {
        $data = array();
        //业务参数
        $data['data']['frameNo'] = $frame_no;   //机架号码
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名

//        通过车架号查询车辆信息
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/auto/vehicleInfoByFrameNo')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
        $content = $response->content;
        if($content['state'] == 0)
            return $this->returnData(0, $content['msg'], 4000);
        $return_data = $content['data'];

        //获取车型信息
        $data = array();
        //业务参数
        $data['data']['frameNo'] = $return_data['frameNo'];   //车架号
        $data['data']['licenseNo'] = $return_data['licenseNo']; //车牌号码
        $data['data']['responseNo'] = $return_data['responseNo']; //响应码
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");
        $data['sign'] = $this->carInsSign($data['data']);   //签名

//        通过机架号查询车辆信息
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/auto/modelExactness')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
        //todo if error
        $return_data['vehicleList'] = $response->content['data'];
        return $this->returnData(1, 'success', 200, $return_data);
    }

    /**
     * 查询险别信息
     */
    public function getClauseInfo()
    {
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名

        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/mdata/risks')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
        return $response->content['data'];

    }

    /**
     * 省份查询可投保公司
     */
    public function getCompany($city_code)
    {
//        $input = $this->decodeOriginData();
//        if(!isset($input['provinceCode']))
//            return response('参数异常', 400);
//        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['provinceCode'] = $city_code[0]; //省国标码
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/mdata/insurers')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
        if($response->content['state'] != '1')
            return $this->returnData(0, '查询该地区保险公司为空', 4000);
        return $response->content['data'];
    }



    /**
     * 获取下起起保日期（需要车型编号）
     */
    public function getNextInsTime($input)
    {
        $data = array();
        //业务参数
        $data['data']['responseNo'] = $input['responseNo']; //响应码

        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['licenseNo'] = $input['licenseNo'];   //车牌号
//        $data['data']['frameNo'] = '';   //车架号
        $data['data']['brandCode'] = $input['brandCode'];   //品牌型号代码
//        $data['data']['engineNo'] = '';   //发动机号码
        $data['data']['isTrans'] = $input['isTrans'];   //是否过户车
        if($data['data']['isTrans'] == '1')
            $data['data']['transDate'] = $input['transDate'];   //过户日期

        $data['data']['cityCode'] = $input['cityCode'];   //二级城市,410700
        $data['data']['firstRegisterDate'] =  $input['firstRegisterDate'];

        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/assist/effectiveDate')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
        return $response->content;

    }

    /**
     * 省份编码
     */
    public function getProvinces()
    {
        $input = $this->decodeOriginData();
        if(!isset($input['msg']) || ($input['msg'] != 'getProvinces')){
            return response('参数异常', 400);
        }

        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名

//        省份信息
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/mdata/provinces')
            ->returnResponseObject()
            ->withData($data)
            ->asJson()
            ->withTimeout(10)
            ->post();
        dd($response);
    }

    public function getCities()
    {
        $input = $this->decodeOriginData();
//        if(!isset($input['provinceCode']))
//            return response('参数异常', 400);
        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['provinceCode'] = '110000'; //省国标码
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名

//        城市信息
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/mdata/cities')
            ->returnResponseObject()
            ->withData($data)
            ->asJson()
            ->withTimeout(10)
            ->post();
        dd($response);

    }



    /**
     * 算费
     */
    public function quote()
    {
        $input = $this->decodeOriginData();
        $p_info = $input['personInfo'];
        $clause_info = $input['coverageList']['coverageList'];
//        dd($clause_info);
        $date_return = $this->getNextInsTime($input);    //下期起保日期
        if($date_return['state'] != '1')
            return $this->returnData(0, $date_return['msg'], 4000);

        $city_code = explode('-', $input['cityCode']);
        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['thpBizID'] = date('YmdHis'). rand(10, 99) . rand(100, 999); //我方业务唯一标识
        $data['data']['cityCode'] = $city_code[1]; //行驶城市代码,二级城市
        $data['data']['responseNo'] = $input['responseNo'];
        $data['data']['biBeginDate'] = $date_return['data']['biStartTime']; //商业险起保日期
        $data['data']['ciBeginDate'] = $date_return['data']['ciStartTime']; //交强险起保日期
        $data['data']['insurerCode'] = $input['insurerCode'];   //保险公司码
        $data['data']['carInfo'] = [];  //车辆信息
        $data['data']['personInfo'] = [];   //人员信息
        $data['data']['coverageList'] = []; //险别信息
        //车辆信息
        $data['data']['carInfo']['licenseNo'] = $input['licenseNo']; //车牌号
        $data['data']['carInfo']['brandCode'] = $input['brandCode']; //品牌型号代码
        $data['data']['carInfo']['isTrans'] = $input['isTrans']; //是否过户车
        if($data['data']['carInfo']['isTrans'] == '1')
            $data['data']['carInfo']['transDate'] = $input['transDate']; //过户日期
        $data['data']['carInfo']['firstRegisterDate'] = $input['firstRegisterDate']; //初登日期

        //人员信息
//        $data['data']['personInfo']['ownerName'] = '';  //车主姓名
//        $data['data']['personInfo']['ownerID'] = '';    //车主身份证
//        $data['data']['personInfo']['ownerMobile'] = '';    //车主手机
//        $data['data']['personInfo']['insuredName'] = '';    //被保人姓名
//        $data['data']['personInfo']['insuredID'] = '';    //被保人身份证
//        $data['data']['personInfo']['insuredMobile'] = '';    //被保人手机
//        $data['data']['personInfo']['applicantName'] = '';    //投保人姓名
//        $data['data']['personInfo']['applicantID'] = '';    //投保人身份证
//        $data['data']['personInfo']['applicantMobile'] = '';    //投保人手机
        $data['data']['personInfo'] = $p_info;

        //条款信息
        $data['data']['coverageList'] = $clause_info;

        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名

//        省份信息
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/main/exactnessQuote')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
//        dd($response);
        if($response->content['state'] != '1')
            return $this->returnData(0, $response->content['msg'], 4000);
        $return = $response->content['data'][0];
        $order = $this->addOrder($data['data'], $return);
        unset($return['state']);
        unset($return['msg']);
        unset($return['msgCode']);
        unset($return['bizID']);
        unset($return['thpBizID']);
        unset($return['insurerCode']);
        unset($return['channelCode']);
        $return['order_no'] = $order->order_no;
        return $this->returnData(1, '询价成功', 200, $return);


    }

    /**
     * 投保
     */
    public function buyIns()
    {
        $input = $this->decodeOriginData();
        $data = array();
        //todo find order
        $order = InsOrder::where('order_no', $input['order_no'])->first();
        if(!$order)
            return $this->returnData(0, '无相关预算记录', 4000);
        $quote_return = json_decode($order->buy_options, true)['quote_return'];

        $city_code = explode('-', $input['city_code']);
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['bizID'] = $quote_return['bizID']; //算费唯一标识
        $data['data']['insurerCode'] = $quote_return['insurerCode']; //保险公司码
        $data['data']['channelCode'] = $quote_return['channelCode']; //渠道编码
        $data['data']['addresseeName'] = $input['addresseeName']; //收件人姓名
        $data['data']['addresseeMobile'] = $input['addresseeMobile']; //收件人电话
        $data['data']['addresseeDetails'] = $input['addresseeDetails']; //收件人详细地址
        $data['data']['addresseeProvince'] = $city_code[0]; //收件人省国标码
        $data['data']['addresseeCity'] = $city_code[1]; //收件人城市国标码
        $data['data']['addresseeCounty'] = $city_code[2]; //收件人地区国标码
        $data['data']['policyEmail'] = $input['policyEmail']; //收件人邮箱
//        if(isset($input['applicantUrl']))
//            $data['data']['applicantUrl'] = $input['applicantUrl']; //成功跳转地址
        $data['payType'] = $input['payType'];
//        dd(json_encode($data['data'], JSON_UNESCAPED_UNICODE));
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名

        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/main/applyUnderwrite')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();

        if($response->content['state'] != '1'){
            return $this->returnData(0, $response->content['msg'], 4000);
        }

        if($response->content['data']['synchFlag'] == 1)
            return $this->returnData(1, '投保成功,核保结果稍后异步返回', 200, $response->content['data']);

        $response->content['data']['order_no'] = $response->content['data']['thpBizID'];
        unset($response->content['data']['bizID']);
        unset($response->content['data']['thpBizID']);

        $order->pay_code = $response->content['data']['billNo'];
        $buy_options = json_decode($order->buy_options, true);
        $buy_options['buy_return'] = $response->content['data'];
        $order->buy_options = json_encode($buy_options, JSON_UNESCAPED_UNICODE);    //请求核保返回
        $order->status = 'pay_ing';
        $order->save();
        $response->content['data']['personInfo'] = $buy_options['post']['personInfo'];
        return $this->returnData(1, '投保成功', 200, $response->content['data']);
    }


    public function getPayWay()
    {
        $input = $this->decodeOriginData();
        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['bizID'] = $input['bizID']; //请求方标识

        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名

        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/payment/payLink')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
//        dd($response->content);
        if($response->content['state'] != '1')
            return $this->returnData(0, $response->content['msg'], 4000);
        $return = ['payLink'=> $response->content['data']['payLink'], 'bizID'=>$response->content['data']['bizID']];
        return $this->returnData(1, '成功获取支付链接', 200, $return);
    }

    /**
     * 北京地区身份证信息采集
     * @return mixed
     */
    public function cardInfoPost()
    {
        $input = $this->decodeOriginData();
        $order = InsOrder::where('order_no', $input['order_no'])->first();
        if(!$order)
            return $this->returnData(0, '无相关核保申请记录', 4000);
        $data = array();
        //投保人
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['bizID'] = $order->union_order_code; //算费唯一标识
        $data['data']['personInfo']['applicantAddress'] = $input['person']['applicant_address'];
        $data['data']['personInfo']['applicantSex'] = $input['person']['applicant_sex'];
        $data['data']['personInfo']['applicantNationDes'] = $input['person']['applicant_nation_des'];
        $data['data']['personInfo']['applicantBirthday'] = $input['person']['applicant_birthday'];
        $data['data']['personInfo']['applicantIssuingInstitution'] = $input['person']['applicant_issuing_institution'];
        $data['data']['personInfo']['applicantCertStartDate'] = $input['person']['applicant_cert_start_date'];
        $data['data']['personInfo']['applicantCertEndDate'] = $input['person']['applicant_cert_end_date'];
        //被保人
        $data['data']['personInfo']['insuredAddress'] = $input['person']['insured_address'];
        $data['data']['personInfo']['insuredSex'] = $input['person']['insured_sex'];
        $data['data']['personInfo']['insuredNationDes'] = $input['person']['insured_nation_des'];
        $data['data']['personInfo']['insuredBirthday'] = $input['person']['insured_birthday'];
        $data['data']['personInfo']['insuredIssuingInstitution'] = $input['person']['insured_issuing_institution'];
        $data['data']['personInfo']['insuredCertStartDate'] = $input['person']['insured_cert_start_date'];
        $data['data']['personInfo']['insuredCertEndDate'] = $input['person']['insured_cert_end_date'];
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名

        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/assist/cardInfoAcquisition')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
        if($response->content['state'] != '1')
            return $this->returnData(0, $response->content['msg'], 4000);
        return $this->returnData(1, 'success', 200);
    }

    public function getCodeAgain()
    {
        $input = $this->decodeOriginData();
        $order = InsOrder::where('order_no', $input['order_no'])->first();
        if(!$order)
            return $this->returnData(0, '无相关核保申请记录', 4000);
        $data = array();
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['bizID'] = $order->union_order_code; //算费唯一标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/assist/resendBjVerifyCode')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
//        LogHelper::logSuccess($response, 'car', 'get_code_again');
        if($response->content['state'] != '1')
            return $this->returnData(0, $response->content['msg'], 4000);
        return $this->returnData(1, 'success', 200);
    }

    public function verifyCode()
    {
        $input = $this->decodeOriginData();
        $order = InsOrder::where('order_no', $input['order_no'])->first();
        if(!$order)
            return $this->returnData(0, '无相关核保申请记录', 4000);
        $data = array();
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['bizID'] = $order->union_order_code; //算费唯一标识
        $data['data']['verificationCode'] = $input['code']; //验证码

        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data['data']);   //签名
        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/assist/sendBjVerifyCode')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();
        //todo
        if($response->content['state'] != '1')
            return $this->returnData(0, $response->content['msg'], 4000);
        $response->content['data']['order_no'] = $input['order_no'];
        $return = $response->content['data'];
        return $this->returnData(1, '验证成功', 200, $return);
    }

    /**
     * 回调信息
     * @return mixed
     */
    public function callBack()
    {
        $input = $this->request->all();     //1:核保回调接口，2:回写保单信息，3:回写配送信息
        LogHelper::logSuccess($input,'car_back');
        $error = json_encode(['state'=>'0', 'msgCode'=>'10000008', 'msg'=>$input['msg']], JSON_UNESCAPED_UNICODE);
        if($input['state'] != '1'){
            return $error;
        }
        switch($input['data']['operType']){
            case '1':   //核保回调

                break;
            case '2':   //回写保单
                if($input['data']['payState'] == '1'){
                    $order = InsOrder::where(['union_order_code'=> $input['data']['bizID'],'order_no'=>$input['data']['thpBizID'], 'status'=>'pay_ing'])->first();
                    if($order){
                        $options = json_decode($order->buy_options, true);
                        $person =$options['post']['personInfo'];
                        $quote_return = $options['quote_return'];
                        $quote_return['personInfo'] = $person;
                        unset($quote_return['state']);
                        unset($quote_return['msg']);
                        unset($quote_return['bizID']);
                        unset($quote_return['thpBizID']);
                        $warranty = new CarWarranty();
                        $warranty->order_id = $order->id;
                        $warranty->order_no = $order->order_no;
                        $warranty->total_premium = $order->total_premium;
                        $warranty->out_order_id = $order->union_order_code;
                        $warranty->ci_policy_no = $input['data']['ciPolicyNo'];
                        $warranty->bi_policy_no = $input['data']['biPolicyNo'];
                        $warranty->ci_begin_date = strtotime($quote_return['ciBeginDate']);
                        $warranty->bi_begin_date = strtotime($quote_return['biBeginDate']);
                        $warranty->ci_end_date = strtotime($quote_return['ciBeginDate'] ." +1 years");
                        $warranty->bi_end_date = strtotime($quote_return['biBeginDate'] ." +1 years");
                        $warranty->options = json_encode($quote_return, JSON_UNESCAPED_UNICODE);
                        $warranty->save();
                        $order->pay_time = $input['data']['payTime'];
                        $order->status = 'pay_end';
                        $order->save();
                        return json_encode(['state'=>'1', 'msgCode'=>'', 'msg'=>'回写核保信息响应报文'], JSON_UNESCAPED_UNICODE);
                    }
                }
                break;
            case '3':   //回写配送

                break;
        }
    }



    protected function carInsSign($data)
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $a = str_replace("\\/", "/", $data);
        $sign = $this->rsaSign($data); //签名
//        $sign = base64_encode($this->rsaEncrypt('rsa_public_key_encrypt', $this->public_key, $data));
        return $sign;
    }

    protected function returnData($status, $msg=null, $code=200, $data=[])
    {
        $err = array();
        $err['status'] = $status;
        $err['code'] = $code;
        $err['msg'] = $msg;
        $err['data'] = $data;
        return json_encode($err, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 申请成功后，录入订单信息
     */
    public function addOrder($post_data, $return_data)
    {
//        dd($return_data);
        $order = new InsOrder();
        $order->order_no = $return_data['thpBizID']; //内部订单号
        $order->union_order_code = $return_data['bizID'];
        $order->create_account_id = $this->request['account_id'];
        $order->total_premium = (int)(($return_data['ciPremium'] + $return_data['biPremium'] + $return_data['carshipTax']) * 100);
        $buy_option = json_encode(['post'=>$post_data, 'quote_return'=> $return_data], JSON_UNESCAPED_UNICODE); //询价参数，询价返回
        $order->buy_options = $buy_option;
        $order->order_type = 2;
        $order->status = 'check_ing';
        $order->save();
        return $order;
    }





    //==============================================加密、解密 加签、验证=============================================

    /**
     * 执行公钥加密功能
     * @param $pubk
     * @param $data
     * @return mixed
     */
    public function rsa_public_key_encrypt($pubk, $data) {
        $pubk = openssl_get_publickey($pubk);
        openssl_public_encrypt($data, $en, $pubk, OPENSSL_PKCS1_PADDING);
        return $en;
    }


    /**
     * @param $method
     * @param $key
     * @param $data
     * @param int $rsa_bit
     * @return string* 加密
     * @param    $method      调用的方法
     * @param    $key         秘钥
     * @param    $data        需加密数据
     */
    function rsaEncrypt($method, $key, $data, $rsa_bit = 1024) {
        $inputLen = strlen($data);
        $offSet = 0;
        $i = 0;
        $maxDecryptBlock = $rsa_bit / 8 - 11;
        $en = '';
        // 对数据分段加密
        while ($inputLen - $offSet > 0) {
            if ($inputLen - $offSet > $maxDecryptBlock) {
                $cache = $this->$method($key, substr($data, $offSet, $maxDecryptBlock));
            } else {
                $cache = $this->$method($key, substr($data, $offSet, $inputLen - $offSet));
            }
            $en = $en . $cache;
            $i++;
            $offSet = $i * $maxDecryptBlock;
        }
        return $en;
    }

    /**
     * rsa签名
     * @param    $data     string      待签名数据
     */
    public function rsaSign($data){
        $res = openssl_get_privatekey($this->private_key);
        openssl_sign($data, $sign, $res, OPENSSL_ALGO_MD5);
        openssl_free_key($res);
        return base64_encode($sign);
    }

    /**
     * rsa验签
     * @param $data
     * @param $sign
     * @return bool
     */
    private function rsaCheckSign($data, $sign){
        $res = openssl_get_publickey($this->public_key);
        $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }

    /**
     * 解密
     */
    public function rsaDecrypt($encrypted){
        $one 		= 	128;
        $encrypted 	= 	base64_decode($encrypted);
        $decrypted 	= 	'';
        $num 		= 	ceil(strlen($encrypted) / $one);
        // $num = (strlen($encrypted) - strlen($encrypted) % $one) / $one + 1;
        for ($i = 0; $i < $num; $i++) {
            $data_part 	= 	substr($encrypted, $i * $one, $one);
            $de 		= 	'';
            openssl_private_decrypt($data_part, $de, $this->private_key, OPENSSL_PKCS1_PADDING);
            $decrypted 	.= 	$de;
        }
        return $decrypted;
    }
}