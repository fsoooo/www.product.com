<?php

namespace App\Http\Controllers\ApiControllers\Curls;

use App\Models\ApiFrom;
use App\Models\InsApiBrokerage;
use App\Models\OrderFinance;
use App\Models\Policy;
use App\Models\User;
use App\Repositories\InsuranceApiFromRepository;
use App\Repositories\InsuranceAttributesRepository;
use App\Repositories\RestrictGeneRepository;
use Carbon\Carbon;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Illuminate\Support\Facades\DB;
use Ixudra\Curl\Facades\Curl;
use App\Models\InsOrder;
use App\Models\Insure;
use App\Helper\LogHelper;
use App\Jobs\Demo;
use App\Jobs\DemoTest;
use App\Jobs\TkPayCallBack;
use App\Jobs\TkSignCallBack;

class TkInsCurlController
{
    //接口地址（测试）
    const API_INSURE = 'http://119.253.81.113/tk-link/rest';
    //接口地址（生产）
    const API_INSURE_PRODUCT = 'http://119.253.80.26/tk-link/rest';
    // 理赔接口地址
    const API_CLAIM = 'http://ecuat.taikang.com/tkchannel_ydwy/service/yundaClaim';
    // 理赔接口地址(生产)
    const API_CLAIM_PRODUCT = 'http://channel.tk.cn/tkchannel/service/yundaClaim';
    // 理赔固定参数中的token由req+key进行md5加密获得
    const CLAIM_TOKEN_KEY = '78A79D1FAD767A4DBA961263E296FA60E42A645BAAA9C243B7477979F37ED71B';
    // 报文加密key
    const CLAIM_ENCODE_KEY = 'tianyany';
    //测试环境
    const INS_ENCODE_KEY  = '1234567890ABCDEF';
    // 承投保秘钥(生产环境)  TODO  已更新
    const INS_ENCODE_KEY_PRODUCT= 'Fdw3bgdHj8I0S0i834F3882E56QB2S61e0Yzv1nC7D4y3b12Z40Bp9DbhS5o0hwb094O223c9n7184Xp908Ss1I92F0m0TH7PphB89i4iwGu00L2830c352B72TDW3Vl';
    protected $sign_help;
    protected $private_key;
    protected $public_key;
    protected $wk_public_key;
    protected $request;

    /**
     * 解参之后的数据
     *
     * @var $original_data
     */
    protected $original_data;

    /**
     * 产品和API来源绑定的Model
     *
     * @var $bind
     */
    protected $bind;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->sign_help = new RsaSignHelp();
        if(!preg_match("/call_back|claim/",$this->request->url())) {
            $this->original_data = $this->decodeOriginData();
            //业务参数 解析出源数据json字符串
            $repository = new InsuranceApiFromRepository();
            $insurance_id = isset($this->original_data['ty_product_id']) ? $this->original_data['ty_product_id'] : 0;    //天眼产品ID
            $private_p_code = isset($this->original_data['private_p_code']) ? $this->original_data['private_p_code'] : 0;   //内部产品唯一码

            if ($insurance_id) {
                $this->bind = $repository->getApiStatusOn($insurance_id);    //获取当前正在启用的API来源信息
            } else {
                $this->bind = $repository->getApiByPrivatePCode($private_p_code);   //通过内部产品唯一码获得相关信息
            }
            $api_from = ApiFrom::where('id',  $this->bind->api_from_id)->first();
            if (empty($this->bind)) {
                return ['data' => 'product not exist', 'code' => 403];
            }
        }
    }

    /**
     * 算费
     *
     * @return array
     */
    public function quote()
    {
        $msg = [];
        $original_data = $this->original_data;
        $restrict_genes = (new RestrictGeneRepository())->findRestrictGenesRecursionByBindId($this->bind->id);
        $api_data = [];
        $new_val = json_decode($original_data['new_val'], true);
        foreach ($new_val as $item) {
            if (isset($item['value'])) {
                preg_match('/(\d+-?)*/', $item['value'], $matches);
                $api_data[$item['key']] = $matches[0];
            } else {
                $api_data[$item['key']] = '';
            }
        }

        $selected_options = json_decode($original_data['new_val'], true);
        // 替换"key"为"ty_key"
        foreach ($selected_options as &$item) {
            $item['ty_key'] = $item['key'];
            unset($item['key']);
        }
        $msg['data']['price'] = 200;
        $msg['data']['selected_options'] = $selected_options;
        $msg['data']['new_genes'] = $restrict_genes;
        $msg['code'] = 200;
        return $msg;
    }

    /**
     * 投保
     * @return array
     * todo   修改了投保时间（今天）
     *
     */
    public function buyIns()
    {
        $original_data = $this->original_data;
        $repository = new InsuranceAttributesRepository();
        $attributes = $original_data['insurance_attributes'];
        $tmp_data = $repository->inToOut($this->bind->id, $attributes);
        // 加入内置参数
        $data = $this->originalDataMake();
        $data['service_id'] = '01';
        $data['apply_content'] = [
            "holder_name"=> $tmp_data['ty_toubaoren']['holder_name'],   //投保人姓名
            "holder_cid_type"=> $tmp_data['ty_toubaoren']['holder_cid_type'],   //投保人证件类型
            "holder_cid_number"=> $tmp_data['ty_toubaoren']['holder_cid_number'],   //投保人证件号
            "holder_mobile"=> $tmp_data['ty_toubaoren']['holder_mobile'],   //投保人手机号
            "holder_insuredFlag"=> "1", //关系人标示
            "insurants_insuredFlag"=> "2",  //关系人标示
            "holder_insuredType"=> "1", //企业个人投保标志
            "insurants_name"=> $tmp_data['ty_beibaoren'][0]['insurants_name'], //被保人姓名
            "insurants_cid_type"=> $tmp_data['ty_beibaoren'][0]['insurants_cid_type'], //被保人证件类型
            "insurants_cid_number"=> $tmp_data['ty_beibaoren'][0]['insurants_cid_number'], //被保人证件号
            "insurants_mobile"=> $tmp_data['ty_toubaoren']['holder_mobile'], //被保人手机号
            "insurants_insuredType"=> "1",  //企业个人投保标志
            "occupationCode"=> "01529003",  //被保人职业代码
            "fromId" => "64090",    //渠道分类编号
            "relatedperson"=> $tmp_data['ty_beibaoren'][0]['relatedperson'],   //投被保人关系
            "comboId"=> $this->bind->p_code,   //方案代码
            "startDate"=> strtotime(date("Y-m-d",strtotime("+1 day")) . " 07:00:00") . '000',    //起保日期
            "endDate"=> strtotime(date("Y-m-d",strtotime("+1 day")). " 23:59:59") . '000',  //终保日期
            "issueDate"=>time() . '000',    //投保日期
//            //TODO  测试投保，支付，出单，时间（前一天）
//            "startDate"=> strtotime(date("Y-m-d",time()) . " 07:00:00") . '000',    //起保日期
//            "endDate"=> strtotime(date("Y-m-d",time()). " 23:59:59") . '000',  //终保日期
//            "issueDate"=>time()-24*60*60 . '000',    //投保日期
            "FieldAA"=> $this->bind->p_code,   //方案代码
            "FieldAG"=> "110000",   //投保地区（省）
            "FieldAH"=> "110000",   //投保地区（市）
            "FieldAI"=> "110114",   //投保地区（县）
            "FieldAJ"=> "回龙观东大街",   //站点名称
            "FieldAK"=> strtotime(date("Y-m-d",strtotime("+1 day")). " 9:00:00") . '000',  //分拣开始时间
            "premium"=> "2",    //保额
            "amount"=> "270000", //总保费
        ];
        $time_array = array();
        $time_array['start'] = date("Y-m-d",strtotime("+1 day")) . " 07:00:00";
        $time_array['end'] = date("Y-m-d",strtotime("+1 day")) . " 23:59:59";
        //中文转译问题JSON_UNESCAPED_UNICODE** 泰康接口不用考虑中文转码问题
        $sign = md5(self::INS_ENCODE_KEY_PRODUCT . json_encode($data['apply_content']));
        $data['sign'] = $sign;
        //请求
//        dump($data);
        $response = Curl::to(self::API_INSURE_PRODUCT)
            ->returnResponseObject()
            ->withData($data)
            ->withHeader("Content-Type: application/json;charset=UTF-8")
            ->asJson(true)
            ->withTimeout(60)
            ->post();
        $return = $response->content;
//        print_r($return);die;
        if($return['result_code'] != 0){
            LogHelper::logError($return, 'tk_buy_ins_no');
            return ['data' => $return['result_msg'], 'code' => 400];
        }
        LogHelper::logSuccess($return,'tk_buy_ins_ok');
        //success
        $buy_options = [];
        $buy_options['insurance_attributes'] = $original_data['insurance_attributes'];
        $buy_options['quote_selected'] = json_decode($original_data['quote_selected'], true);
        $result = $return['result_content'];
        $result['time_array'] = $time_array;
        return $this->addOrder($original_data, $result, $buy_options, $data);
    }

    /**
     *
     * TODO 签约接口
     *
     *
     */
    public function contractIns()
    {
        set_time_limit(0);//永不超时
        $original_data = $this->original_data;
        $repository = new InsuranceAttributesRepository();
        $attributes = $original_data['insurance_attributes'];
        $tmp_data = $repository->inToOut($this->bind->id, $attributes);
        // 加入内置参数
        $data = $this->originalDataMake();
        $data['service_id'] = '07';
        $data['apply_content'] = [
            //签约接口参数
            'proposalNo'=>$original_data['union_order_code'],//联合订单号
            'contract_display_account'=>$original_data['pay_account'],//支付账户（微信号）
            'request_serial'=>$original_data['union_order_code'].date('Ymd',time()),//请求序列号
            'contract_code'=>$original_data['union_order_code'].date('Ymd',time()),//签约序列号
            'callbackurl'=>env('APP_URL').'/api/ins/tk/call_back',//签约回调地址
            'payWayId'=>'79',//固定值，79 微信代扣
            'fromId'=> '64090', //渠道代码
            'comboId'=> '1122A01G', //方案代码
            'clientIp'=>$original_data['clientIp'],

        ];
        //中文转译问题JSON_UNESCAPED_UNICODE** 泰康接口不用考虑中文转码问题
        $sign = md5(self::INS_ENCODE_KEY_PRODUCT . json_encode($data['apply_content']));
        $data['sign'] = $sign;
        // dump($data);
        // print_r(json_encode($data));
        //请求
        $response = Curl::to(self::API_INSURE_PRODUCT)
            ->returnResponseObject()
            ->withData($data)
            ->withHeader("Content-Type: application/json;charset=UTF-8")
            ->asJson(true)
            ->withTimeout(60)
            ->post();
        $return = $response->content;
        // print_r($return);die;
        if($return['result_code'] != 0){
            LogHelper::logError($return, 'tk_buy_ins');
            return ['data' => $return['result_msg'], 'code' => 400];
        }
        LogHelper::logSuccess($return,'contractIns');
        return ['data' => $return, 'code' => 200];
    }



    /**
     * 签约回调
     *todo 签约回调
     * 2017-11-30
     *
     */
    public function contractCallBack()
    {
        $return = $this->request->all();
        dispatch(new TkSignCallBack($return));
        return json_encode(['flag'=>true]);
    }
    /**
     * TODO 微信代扣
     * @return array
     */
    public function insWithhold(){
        set_time_limit(0);//永不超时
        $original_data = $this->original_data;
        $repository = new InsuranceAttributesRepository();
        $attributes = $original_data['insurance_attributes'];
        $tmp_data = $repository->inToOut($this->bind->id, $attributes);
        // 加入内置参数
        $data = $this->originalDataMake();
        $data['service_id'] = '10';
        $data['apply_content'] = [
            //代扣接口参数
            'fromId'=> '64090', //渠道代码
            'comboId'=> '1122A01G01', //方案代
            'payWayId'=>'79',//支付方式
            'proposalNo'=>$original_data['union_order_code'],//联合订单号
            'payAccount'=>$original_data['pay_account'],//支付账户（微信号）
            'contractId'=>$original_data['contract_id'],//委托代扣协议
            'tradeId'=>substr($original_data['union_order_code'],5,24),//联合订单号
            'payCallBackUrl'=>env('APP_URL').'/api/ins/tk/pay_call_back',//微信代扣回调地址
            'description'=>'wechat描述',//描述

        ];
        LogHelper::logSuccess($original_data['union_order_code'], 'tk', 'pay_order_code');
        $order = InsOrder::where(['union_order_code'=> $original_data['union_order_code'], 'api_from_uuid'=> 'Tk'])->first();
        LogHelper::logSuccess($order, 'tk', 'pay_order_data');
        $user = User::where('account_id', '15113213053124')->first();
        //中文转译问题JSON_UNESCAPED_UNICODE** 泰康接口不用考虑中文转码问题
        // dump(self::INS_ENCODE_KEY);
        $sign = md5(self::INS_ENCODE_KEY_PRODUCT . json_encode($data['apply_content']));
        $data['sign'] = $sign;
        //请求
        // dump(self::INS_ENCODE_KEY_PRODUCT);
        //dump($data);
        // print_r(json_encode($data));
        //dump(self::API_INSURE_PRODUCT);
        LogHelper::logSuccess($data, 'tk', 'pay_response_data');
        $response = Curl::to(self::API_INSURE_PRODUCT)
            ->returnResponseObject()
            ->withData($data)
            ->withHeader("Content-Type: application/json;charset=UTF-8")
            ->asJson(true)
            ->withTimeout(60)
            ->post();
        $return = $response->content;
        //$return = json_decode('{"service_id":10,"timestamp":1515151701000,"result_code":0,"coop_id":"yun_da_kuai_di","result_msg":"","result_content":{"amount":270000,"createTime":1515151702309,"premium":2,"billno":151515170223505183092,"proposalNo":000021122201824012440190215}}',true);
        LogHelper::logSuccess($return, 'tk', 'pay_return_data');
        if($return['result_code'] != 0){
            return ['data' => $return['result_msg'], 'code' => 400];
        }
        $result = $return['result_content'];
        $result['pay_time'] = $data['timestamp'];
        try{
            $results = $return;
            $this->changeOrderInfo($original_data['union_order_code'], 'pay_end', $result, '', $user);
            $msg = ['data'=>$results, 'code' => 200];
            return $msg;
        } catch (\Exception $e) {
            return $msg = ['data' => $e->getMessage(), 'code' => 400];
        }
    }


    /**
     * TODO 微信代扣回调
     * @return array
     */
    public function insWithholdCallBack(){
        $return = $this->request->all();
        dispatch(new TkPayCallBack($return));
        return json_encode(['flag'=>true]);
    }

    /**
     * 支付
     * @return array
     */
    public function payIns()
    {
        $original_data = $this->original_data;
        $all_insure = Insure::where(['union_order_code' => $original_data['unionOrderCode']])->get();
        $allowed_status = ['check_ing', 'pay_ing', 'pay_error'];
        foreach ($all_insure as $ak => $av) {
            if (false === array_search($av->status, $allowed_status)){
                $msg['data'] = '当前订单已失效';
                $msg['code'] = 403;
                return $msg;
            }
        }
        $order = InsOrder::where(['union_order_code'=> $original_data['unionOrderCode'], 'api_from_uuid'=> 'Tk'])->first();
        $user = User::where('account_id', $order->create_account_id)->first();
        $data = $this->originalDataMake();
        $data['serial_no'] = $order->order_no;
        $data['service_id'] = "11";
        // 支付接口数据
        $data['apply_content'] = [
            "proposalNo"=> $order->union_order_code,    //投保单号
            "payWayId"=> "72", //支付方式的id
            "comboid"=> $this->bind->p_code,   //方案编号
            "payCallBackUrl"=> env('APP_URL') . "/ins/tk/call_back",    //支付回调url
//            "failUrl"=> $user->call_back_url . '/order/index/all', //失败地址Url
//            "successUrl"=> $user->call_back_url . '/order/index/all', //成功url
//            "payCallBackUrl"=> "http://dev308.inschos.com/api/ins/tk/call_back",    //支付回调url
            "failUrl"=>'http://yunda.inschos.com/order/index/all', //失败地址Url
            "successUrl"=>'http://yunda.inschos.com/order/index/all', //成功url
            "fromId"=> "64090", //渠道分类编号
        ];
        $sign = md5(self::INS_ENCODE_KEY_PRODUCT . json_encode($data['apply_content'])); //中文转译问题JSON_UNESCAPED_UNICODE** 泰康接口不用考虑中文转码问题
        $data['sign'] = $sign;
        LogHelper::logSuccess($data, 'tk', 'pay_data');
        //请求
        $response = Curl::to(self::API_INSURE_PRODUCT)
            ->returnResponseObject()
            ->withData($data)
            ->withHeader("Content-Type: application/json;charset=UTF-8")
            ->asJson(true)
            ->withTimeout(60)
            ->post();
//        print_r($response);die;
        $return = $response->content;
        LogHelper::logSuccess($return, 'tk', 'pay_return_data');
        if($return['result_code'] != 0){
            return ['data' => $return['result_msg'], 'code' => 400];
        }
        $result = $return['result_content'];
        $result['pay_time'] = $data['timestamp'];
        //todo 接口版本更新 不使用银行卡代扣后 payFlag 废弃
//        if($result['payFlag'] == 'true'){
        try{
            $this->changeOrderInfo($order->union_order_code, 'pay_end', $result, '', $user);
            $msg = ['data'=>$result, 'code' => 200];
            return $msg;
        } catch (\Exception $e) {
            return $msg = ['data' => $e->getMessage(), 'code' => 400];
        }
//        }
    }


    /**
     * 回调
     * @return mixed
     */
    public function payCallBack()
    {
        $return = $this->request->all();
        LogHelper::logSuccess($return, 'Tk_pay_call_back', $this->request->get('requestType'));
        DB::beginTransaction();
        try{
            LogHelper::logSuccess($return, 'Tk', $this->request->get('requestType'));
            if(!is_array($return)){
                $return = json_decode($return,true);
            }
            switch($return['requestType']){
                case 1: //支付通知
                    $order = InsOrder::where(['union_order_code'=> $return['proposalNo'], 'api_from_uuid'=> 'Tk'])->first();
                    $user = User::where('account_id', $order->create_account_id)->first();
                    if($return['result']){  //支付成功
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
                                'union_order_code' => $return['proposalNo'],
                                'by_stages_way' => $order->by_stages_way,
                                'error_message' => '',
                            ]
                        ];
                    }else{
                        Insure::where('ins_order_id', $order->id)->update(['status'=>'pay_error', 'pay_error_message'=>$return['reason']]);
                        $data = [
                            "notice_type     "=> 'pay_call_back',
                            'data' => [
                                'status'=>false,
                                'account_id' => $user->account_id,
                                'union_order_code' => $return['proposalNo'],
                                'error_message' => $return['reason'],
                            ]
                        ];
                    }
                    $response = Curl::to('http://yunda.inschos.com/ins/call_back')
                        ->returnResponseObject()
                        ->withData($data)
                        ->asJson()
                        ->withTimeout(60)
                        ->post();
                    LogHelper::logSuccess($response, 'Tk_callback_yunda', $this->request->get('requestType'));
                    if($response->status!=200){
                        return json_encode(['state'=>false, 'failMsg'=>'回调处理失败'], JSON_UNESCAPED_UNICODE);
                    }
                    break;
                case 0: //支付跳转
                    break;
            }
            $res = json_encode(['state'=>true]);
            return $res;
        } catch (\Exception $e ){
            LogHelper::logError($return, json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE), 'hz', $this->request->get('notifyType'));
            return json_encode(['state'=>false, 'failMsg'=>'回调处理失败'], JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * 出单
     * @return array
     */
    public function issue()
    {
        //初始化返回结果
        $original_data = $this->original_data;
        $order = InsOrder::where(['union_order_code'=>$original_data['union_order_code'],'api_from_uuid'=> 'Tk'])->first();
        $data = $this->originalDataMake();
        $data['serial_no'] = $order->order_no;
        $data['service_id'] = '02';
        $data['apply_content'] = [
            "proposalNo"=> $original_data['union_order_code'],   //投保单号
            "tradeId"=> $order->pay_code,    //支付订单号
            "outTradeId"=> $order->pay_code, //第三方支付单号
            "payAccount"=> "0000",  //支付账号
            "payTime"=> date('Y-m-d H:i:s',time()),    //支付时间
            "payMoney"=> $order->total_premium / 100,   //支付金额
            "payWayId"=> "79", //支付方式 微信支付
            "fromId"=> "64090", //渠道代码
            "comboId"=> $this->bind->p_code    //方案代码
        ];
        $sign = md5(self::INS_ENCODE_KEY_PRODUCT . json_encode($data['apply_content'])); //中文转译问题JSON_UNESCAPED_UNICODE** 泰康接口不用考虑中文转码问题
        $data['sign'] = $sign;
        //请求
        LogHelper::logSuccess($data, 'tk_issue_data');
        $response = Curl::to(self::API_INSURE_PRODUCT)
            ->returnResponseObject()
            ->withData($data)
            ->withHeader("Content-Type: application/json;charset=UTF-8")
            ->asJson(true)
            ->withTimeout(60)
            ->post();
        $return = $response->content;
        LogHelper::logSuccess($return, 'tk', 'issue_return_data');
        if($return['result_code'] != 0){
            return ['data' => $return['result_msg'], 'code' => 400];
        }
//        dd($response);
        $result = $return['result_content'];
        $result['union_order_code'] = $order->union_order_code;
        return $this->handleIssue($result);
    }

    /**
     * 不同返回结果的订单状态更改
     * @param $order_no
     * @param $status
     * @param array $result
     * @param null $error_msg
     * @param $user
     * @throws \Exception
     */
    protected function changeOrderInfo($order_no, $status, $result = [], $error_msg = null, $user)
    {
        DB::beginTransaction();
        try {
            $insure = Insure::where('out_order_no', $order_no)->first();
            switch ($status) {
                case 'pay_end':
                    Insure::where('union_order_code', $insure->union_order_code)->update([
                        'status' => 'pay_end'
                    ]);
                    $conditions = [
                        'union_order_code' => $insure->union_order_code,
                        'api_from_uuid' => 'Tk'
                    ];
                    $order = InsOrder::where($conditions)->first();
                    $order->pay_code = $result['result_content']['billno']??""; //支付流水号
                    $order->pay_time = $result['result_content']['createTime']??""; //支付时间
                    $order->status = 'pay_end';
                    $order->save();
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
                    DB::commit();
                    $ty_callback_data = [
                        'notice_type' => 'pay_call_back',
                        'data' => [
                            'status' => true,
                            'union_order_code' => $insure->union_order_code,
                            'ratio_for_agency'=> $brokerage->ratio_for_agency,
                            'brokerage_for_agency'=> $finance->brokerage_for_agency,
                            'by_stages_way' => $order->by_stages_way,
                            'error_message' => $error_msg
                        ]
                    ];

                    Curl::to($user->call_back_url . '/ins/call_back')
                        ->returnResponseObject()
                        ->withData($ty_callback_data)
                        ->withTimeout(60)
                        ->post();
                    break;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            LogHelper::logError([$order_no, $status], json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE), 'tk', 'changeStatusWithCheckCallBack');
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    protected function handleIssue($data)
    {
        $result = []; // 回调给代理商的数据
        DB::beginTransaction();
        try {
            $insure = Insure::where('out_order_no', $data['union_order_code'])->first();
            $insure->policy_status = 6;  //保单状态
            $insure->ins_policy_code = $result['policy_order_code'] = $data['policyNo']; //保单号
            $insure->member_id = $data['memberId']; //会员编号 泰康理赔时用
            $insure->save();
            DB::commit();
            $result['start_time'] = $insure->ins_start_time;
            $result['end_time'] = $insure->ins_end_time;
            $result['ins_down_url'] = $data['policyUrl'];
            $result['union_order_code'] = $data['union_order_code'];
            $msg = ['data' => $result, 'code' => 200];
            return $msg;
        } catch (\Exception $exception) {
            DB::rollBack();
            LogHelper::logError($exception->getMessage(), 'handleIssue', 'handleIssue');
            $msg = ['data' => $exception->getMessage(), 'code' => 444];
            return $msg;
        }
    }


    /**
     * 初始化固定参数
     * @return array
     */
    public function originalDataMake()
    {
        $data = array();
        $data['coop_id'] = 'yun_da_kuai_di';
        $data['sign_type'] = 'md5';
        $data['sign'] = ''; //业务参数封装完后处理
        $data['format'] = 'json';
        $data['charset'] = 'utf-8';
        $data['version'] = '1.0';
        $data['timestamp'] = time() . '000';
        $data['serial_no'] = date('YmdHis'). rand(10, 99) . rand(100, 999);
        $data['product_type'] = '1122A01G';
        return $data;
    }


    /**
     * @param $original_data
     * @param $return_data
     * @param $buy_options
     * @param $curl_data
     * @return array
     */
    public function addOrder($original_data, $return_data, $buy_options, $curl_data)
    {
//        dd($original_data);
        DB::beginTransaction();
        try{
            //订单信息
            $order = new InsOrder();
            $order->order_no = $curl_data['serial_no']; //内部单号
            $order->union_order_code = $return_data['proposalNo']; //外部合并订单号
            $order->create_account_id = $this->request['account_id'];  //代理商account_id
            $order->api_from_uuid = ApiFrom::where('id', $this->bind->api_from_id)->first()->uuid;    //接口来源唯一码（接口类名称）
            $order->api_from_id = $this->bind->api_from_id;
            $order->ins_id = $this->bind->insurance_id;
            $order->p_code = $this->bind->p_code; //产品唯一码
            $order->bind_id = $this->bind->id;
            $order->total_premium = $return_data['premium'] * 100;  //总保费
            $order->status = 'check_ing'; //待核保状态
            $order->buy_options = json_encode($buy_options, JSON_UNESCAPED_UNICODE);
            $order->by_stages_way = '0年';

            $order->save();
            //投保人信息
            $policy = new Policy();
            $policy->ins_order_id = $order->id;
            $policy->union_order_code = $return_data['proposalNo'];
            $policy->name = $curl_data['apply_content']['holder_name'];
            $policy->phone = $curl_data['apply_content']['holder_mobile'];
            $policy->card_type = $original_data['insurance_attributes']['ty_toubaoren']['ty_toubaoren_id_type'];
            $policy->card_id = $curl_data['apply_content']['holder_mobile'];
//            $policy->address = $original_data['apply_content']['holderAddress'];
//            $policy->email = $original_data['apply_content']['holderEmail'];
            $policy->save();
            //被保人信息
            $insures = array();
            foreach($original_data['insurance_attributes']['ty_beibaoren'] as $k => $v){
                $insures[$k]['ins_order_id'] = $order->id;   //订单号
                $insures[$k]['out_order_no'] = $return_data['proposalNo'];   //被保人单号(分接口来源)
                $insures[$k]['premium'] = $return_data['premium'] * 100;    //保费
                $insures[$k]['p_code'] = $this->bind->p_code;    //外部产品码
                $insures[$k]['union_order_code'] =  $return_data['proposalNo'];  //外部合并订单号
                $insures[$k]['name'] = $v['ty_beibaoren_name'];
//                    $insures[$k]['sex'] = $v['ty_beibaoren_sex'];
//                    $insures[$k]['phone'] = $v['ty_beibaoren_phone'];
                $insures[$k]['card_type'] = $v['ty_beibaoren_id_type'];
                $insures[$k]['card_id'] = $v['ty_beibaoren_id_number'];
                $insures[$k]['relation'] = $v['ty_relation'];
//                    $insures[$k]['birthday'] = $v['ty_beibaoren_birthday'];
                $insures[$k]['ins_start_time'] = $return_data['time_array']['start'];   //生效日期
                $insures[$k]['ins_end_time'] = $return_data['time_array']['end'];  //结束日期
                $insures[$k]['created_at'] = $insures[$k]['updated_at'] = date("Y-m-d H:i:s");
            }
            Insure::insert($insures);
            $return_insures = array();
            foreach($insures  as $ik => $iv){
                unset($iv['ins_order_id']);
                unset($iv['p_code']);
                unset($iv['created_at']);
                unset($iv['updated_at']);
                $return_insures[$ik] = $iv;
            }
            DB::commit();
            $res['order_list'] = $return_insures;
            $res['total_premium'] = $return_data['premium'] * 100;
            $res['union_order_code'] = $return_data['proposalNo'];
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
        } catch (\Exception $e){
            DB::rollBack();
            $msg = ['data' => $e->getMessage(), 'code' => 444];
            LogHelper::logError($e->getMessage(), 'add_order');
            return $msg;
        }
    }

    public function getPayWayInfo()
    {
        $input = $this->original_data;
        if(empty($input['pay_way'] || $input['union_order_code']))
            return ['data'=>'订单号或支付类型错误', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();
        if(empty($ins_order))
            return ['data'=>'订单号错误', 'code'=> 400];
        if($ins_order->status == 'check_error')
            return ['data'=> $ins_order->insures()->first()->check_error_message, 'code'=> 400];
        //pay_way just cardPay
        return [
            'data'=>[
                'order_code'=>$ins_order->union_order_code,
                'pay_way_data'=> [
                    'banks'=>[
                        ['name'=>'招商银行','code'=> '06','uuid'=>'CMB'],
                        ['name'=>'中国工商银行','code'=> '01','uuid'=>'ICBC'],
                        ['name'=>'中国农业银行','code'=> '04','uuid'=>'ABC'],
                        ['name'=>'中国建设银行','code'=> '03','uuid'=>'CCB'],
                        ['name'=>'中国银行','code'=> '02','uuid'=>'BOC'],
                        ['name'=>'中国交通银行','code'=> '10','uuid'=>'BCOM'],
                        ['name'=>'中国光大银行','code'=> '11','uuid'=>'CEB'],
                        ['name'=>'平安银行','code'=> '12','uuid'=>'PAB'],
                        ['name'=>'广发银行','code'=> '14','uuid'=>'GDB'],
                    ]
                ]
            ],
            'code'=> 200];
    }

    /**
     * API参数
     */
    public function getApiOption()
    {
        $insuranceAttributesRepository = new InsuranceAttributesRepository();
        $restrictGeneRepository = new RestrictGeneRepository();

        $input = $this->original_data;
        $insurance_id = $input['ty_product_id'];

        $result['ty_product_id'] = $insurance_id;
        $result['private_p_code'] = $this->bind->private_p_code;
        $result['bind_id'] = $this->bind->id;
        //投保属性
        $insurance_attributes = $insuranceAttributesRepository->findAttributesRecursionByBindId($this->bind->id);
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
        //算费因子
        $restrict_genes = $restrictGeneRepository->findRestrictGenesRecursionByBindId($this->bind->id);
        $result['option']['restrict_genes'] = $this->onlyTyKey($restrict_genes);
        //已选因子值
        $result['option']['selected_options'] = $restrictGeneRepository->findDefaultRestrictGenes($this->bind->id);
        $result['option']['selected_options'] = $this->onlyTyKey($result['option']['selected_options']);
        //基础价格
        $result['option']['price'] = 200;


        //保障内容
        $protect_items = $this->getProtectItems($insurance_id);
        $result['option']['protect_items'] = $protect_items;
//        dd($result);
        return ['data'=> $result, 'code'=> 200];
    }

    /**
     * 保障内容
     * @return array
     */
    protected function getProtectItems()
    {
        $result = [];
        $result[0]['name'] = '人身意外伤害身故/伤残';
        $result[0]['defaultValue'] = '200000';
        $result[0]['description'] = '被保险人遭受意外伤害事故，并自该事故发生之日起180日（含第180日）内因该事故为直接且单独原因导致被保险人身故的，保险人按保险单上载明的保险金额向意外身故保险金受益人给付意外身故保险金，本合同终止。
        被保险人遭受意外伤害事故，并自该事故发生之日起180日（含第180日）内因该事故为直接且单独原因导致被保险人发生《人身保险伤残评定标准及代码》所述伤残项目，保险人根据本保险合同及该《伤残评定标准》规定的评定原则对被保险人伤残程度进行评定，并按评定结果所对应该《伤残评定标准》中规定的给付比例乘以保险单上载明的保险金额向意外伤残保险金受益人给付意外伤残保险金。
如自意外伤害事故发生之日起180日治疗仍未结束的，则按该意外伤害事故发生之日起第180日的身体情况进行伤残评定，并据此向意外伤残保险金受益人给付意外伤残保险金。';

        $result[1]['name'] = '意外伤害医疗/住院津贴';
        $result[1]['defaultValue'] = '10000';
        $result[1]['description'] = '被保险人因在中国境内遭受意外伤害事故，保险人在扣除本附加合同约定的意外伤害医疗免赔额（以下简称“免赔额”）后，对剩余部分的医疗费用按本附加合同约定的意外伤害医疗给付比例（以下简称“给付比例”）向被保险人给付意外伤害医疗保险金。
        被保险人因在中国境内遭受意外伤害事故，并因该事故为直接且单独原因导致在二级及以上医院经诊断必须住院治疗的，我们按被保险人每次在二级及以上医院的实际住院天数和本附加合同约定的意外住院津贴保险金日额给付意外住院津贴保险金，即：
被保险人每次住院获得的意外住院津贴保险金＝实际住院天数×意外住院津贴保险金日额';

        $result[2]['name'] = '非机动车第三者责任/死亡、伤残、医疗';
        $result[2]['defaultValue'] = '50000';
        $result[2]['description'] = '在本附加合同约定的保险期间内，被保险人在驾驶保险单载明类型的非机动车过程中发生意外事故，造成第三者人身伤亡或财产损失，对依照中华人民共和国法律（不包括港澳台地区法律）应由被保险人承担的经济赔偿责任，保险人按照本附加合同约定负责赔偿。';

        $result[3]['name'] = '非机动车第三者责任/财产损失';
        $result[3]['defaultValue'] = '10000';
        $result[3]['description'] = '在本附加合同约定的保险期间内，被保险人在驾驶保险单载明类型的非机动车过程中发生意外事故，造成第三者人身伤亡或财产损失，对依照中华人民共和国法律（不包括港澳台地区法律）应由被保险人承担的经济赔偿责任，保险人按照本附加合同约定负责赔偿。';

        return $result;
    }


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


    /**
     * 业务参数 解析成数组形式
     *
     * @return mixed
     */
    protected function decodeOriginData()
    {
        $input = $this->request->all();

        return $this->sign_help->tyDecodeOriginData($input['biz_content']);
    }

    /****************************************************** 理赔部分 ***************************************************/

    /**
     * 理赔接口 - 1、	会员绑定信息查询接口
     */
    public function claimGetMemberInfo()
    {
        $api = self::API_CLAIM_PRODUCT . '/memberBind';

        $original_data = $this->decodeOriginData();
        $insure = Insure::where([
            'ins_policy_code'=> $original_data['ins_policy_code'],
            'union_order_code' => $original_data['union_order_code'],
        ])->first();
        if (!$insure) {
            return ['data' => '无法查询到对应的保单记录', 'code' => 400];
        }
        $req['function_code'] = 'isMember'; // 功能编号固定值isMember
        $req['open_id'] = $insure->member_id; // 承保出单后报文中返回的memberId
        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withHeader('Content-Type: application/json;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);
        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['info'], 'code' => 200];
    }

    /**
     * 理赔接口 - 2、	出险地区初始化接口
     */
    public function claimGetArea()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimHosPhone';

        $req['function_code'] = 'getPovinceCity'; // 功能编号固定值getPovinceCity
        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 500];
        }

        return [
            'data' =>
                [
                    'province' => $content['province'],
                    'city' => $content['city']
                ],
            'code' => 200];
    }

    /**
     * 理赔接口 - 4、	通过财产险保单号获取被保险人信息接口
     */
    public function claimGetInsurantInfo()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKA';

        $original_data = $this->decodeOriginData();
        $req['function_code'] = 'getInsurantMsg'; // 功能编号固定值getInsurantMsg
        $req['updateStr'] = 'N'; // 修改标示 N会员证件号Y修改后的证件号
        $req['ins_cidtype'] = $original_data['cidtype']; // 出险人证件类型
        $req['ins_cidnumber'] = $original_data['cidnumber_decrypt']; // 出险人证件号码
        $req['policy_no'] = $original_data['ins_policy_code']; // 财产险保单号
        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['tka_info'], 'code' => 200];
    }

    /**
     * 理赔接口 - 5、	获取验证码接口
     */
    public function claimGetVerifyCode()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimYZM';

        $origin_data = $this->decodeOriginData();

        $req['mobile_encry'] = $origin_data['tka_mobile']; // 加密手机号 即4接口返回的tka_mobile
        $req['mobile_sign'] = $origin_data['mobile_sign']; // 签名 即4接口返回的mobile_sign

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => '验证码...', 'code' => 200];
    }

    /**
     * 理赔接口 - 8、	人伤理赔资料上传类型查询接口(身份证\银行卡\资料)
     */
    public function claimGetTKCDocType()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimInfoStep';

        $original_data = $this->decodeOriginData();
        $req['function_code'] = 'getDocTypes'; // 固定值：getDocTypes
        $req['claim_id'] = 'TKC'.$original_data['claim_id']; // 人伤的报案号前加TKC
        $req['sign'] = $original_data['sign']; // 3接口返回的claim_sign

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['doc_types'], 'code' => 200];
    }

    /**
     * 理赔接口 - 9、	通过财产险保单号查询上传资料描述接口
     */
    public function claimGetTKAUploadDesc()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKA';

        $original_data = $this->decodeOriginData();
        $req['function_code'] = 'getClaimImgMsg'; // 固定值：getClaimImgMsg
        $req['policy_no'] = $original_data['ins_policy_code']; // 保单号

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => '获取失败', 'code' => 400];
        }

        return ['data' => $content['desc'], 'code' => 200];
    }

    /**
     * 理赔接口 - 14、	理赔进度记录查询接口
     */
    public function claimGetProgress()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimApplyQuery';

        $original_data = $this->decodeOriginData();
        $req['function_code'] = 'claimApplyQuery'; // 固定值：claimApplyQuery
        $req['claim_channel'] = 'YDAPP'; // 固值：YDAPP
        $req['claim_id'] = $original_data['claim_id']; // 3、6、7保存接口返回的claim_id 人伤的报案号前加TKC 财险的报案号前加TKA
        $req['coop_id'] = $original_data['coop_id']; // member_id
        $req['member_id'] = $original_data['member_id']; // member_id
        $req['sign'] = $original_data['sign']; // 1接口返回的member_sign

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
//        dump($data);
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();
//        print_r($response);die;
        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['claimapplys'], 'code' => 200];
    }

    /************************************ 理赔资料上传、删除、回显  *****************************************************/

    /**
     * 理赔接口 - 操作理赔资料
     * 10、	理赔资料上传接口
     * 11、	理赔资料删除接口
     * 12、	理赔资料回显接口
     * add,add,query
     */
    public function claimHandleDocs()
    {
        $original_data = $this->decodeOriginData();
        // 报案类型
        $function_code = $original_data['function_code'];
        switch ($function_code) {
            case 'addBase64':
                $result = $this->claimHandleDocsAddBase64();
                break;
            case 'del':
                $result = $this->claimHandleDocsDel();
                break;
            case 'query':
                $result = $this->claimHandleDocsQuery();
                break;
            default:
                return ['data' => '不存在此接口', 'code' => 400];
        }

        return $result;
    }

    /**
     * 理赔接口 - 10、	理赔资料上传接口(身份证\银行卡\其他资料)
     *
     * @return array
     */
    protected function claimHandleDocsAddBase64()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKAInfoImage';

        $original_data = $this->decodeOriginData();
        $req['function_code'] = $original_data['function_code']; // 固定值：add
        $req['claim_id'] = $original_data['claim_id']; // 保存接口返回的claim_id
        $req['coop_id'] = $original_data['coop_id']; // member_id
        $req['claim_flag'] = $original_data['claim_flag']; // 固定值：人伤TKC/财产险TKA/人伤+财产险TKAC
        $req['img_id'] = str_replace('/','-',str_replace('data:image/jpeg;base64,','',$original_data['img_id'])); // 图片压缩后base64编码
        $req['sign'] = $original_data['sign']; // 取保存接口返回的sign
        $req['img_type'] = $original_data['img_type']; // 图片类型

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
//        dump($data);
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();
//        print_r($response);
        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['info'], 'code' => 200];
    }

    /**
     * 理赔接口 - 11、	理赔资料删除接口
     *
     * @return array
     */
    protected function claimHandleDocsDel()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKAInfoImage';

        $original_data = $this->decodeOriginData();
        $req['function_code'] = $original_data['function_code']; // del
        $req['claim_id'] = $original_data['claim_id']; // 保存接口返回的claim_id
        $req['coop_id'] = $original_data['coop_id']; // member_id
        $req['claim_flag'] = $original_data['claim_flag']; // 固定值：人伤TKC/财产险TKA/人伤+财产险TKAC
        $req['img_id'] = str_replace('/','-',$original_data['img_id']); // 图片压缩后base64编码
        $req['sign'] = $original_data['sign']; // 取保存接口返回的sign

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        dump($data);
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();
        print_r($response);
        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => '删除成功', 'code' => 200];
    }

    /**
     * 理赔接口 - 12、	理赔资料回显接口
     *
     * @return array
     */
    protected function claimHandleDocsQuery()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKAInfoImage';
        $original_data = $this->decodeOriginData();
        $req['function_code'] = $original_data['function_code']; // 固定值：query
        $req['claim_id'] = $original_data['claim_id']; // 保存接口返回的claim_id
        $req['coop_id'] = $original_data['coop_id']; // member_id
        $req['claim_flag'] = $original_data['claim_flag']; // 固定值：人伤TKC/财产险TKA/人伤+财产险TKAC
        $req['sign'] = $original_data['sign']; // 取保存接口返回的sign
        $req['pageType'] = $original_data['pageType']; // pageBank银行卡界面、pageCid证件上传界面、pageData理赔资料页面、pageSupply补充资料页面

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['info'], 'code' => 200];
    }

    /************************************ 理赔资料上传、删除、回显 end *************************************************/

    /**
     * 理赔接口 - 13、	申请提交接口
     */
    public function claimSubmit()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKAInfoSubmit';

        $original_data = $this->decodeOriginData();
        $req['claim_id'] = $original_data['claim_id']; // 保存接口返回的claim_id
        $req['sign'] = $original_data['sign']; // 取保存接口返回的sign
        $req['claim_flag'] = $original_data['claim_flag']; // 固定值：人伤TKC/财产险TKA/人伤+财产险TKAC

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];

        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();
//        print_r($response);die;
        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => '提交成功', 'code' => 200];
    }

    /**
     * 理赔接口 - 16、	补充资料接口
     */
    public function claimSubmitAppend()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimInfoSubmit';

        $original_data = $this->decodeOriginData();
        $req['function_code'] = $original_data['sendQuestionInfo2ECM']; // 固定值：sendQuestionInfo2ECM
        $req['claim_id'] = $original_data['claim_id']; // 保存接口返回的claim_id
        $req['coop_id'] = $original_data['coop_id']; // member_id
        $req['seq_no'] = $original_data['seq_no'];
        $req['questionid'] = $original_data['questionid'];
        $req['sign'] = $original_data['sign'];
        $req['channel'] = 'YDAPP'; // 固定值：YDAPP
        $req['question_desc'] = $original_data['question_desc'];

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['desc'], 'code' => 200];
    }

    /**
     * 理赔接口 - 15、	理赔详情查询接口
     */
    public function claimGetDetail()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimApplySpeedQuery';

        $original_data = $this->decodeOriginData();

        $req['function_code'] = 'claimApplySpeedQuery'; // 固定值：claimApplySpeedQuery
        $req['claim_id'] = $original_data['claim_id']; // 3、6、7保存接口返回的claim_id 人伤的报案号前加TKC 财险的报案号前加TKA
        $req['sign'] = $original_data['sign']; // 14接口返回的对应的sign

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['claimapplys'], 'code' => 200];
    }

    /************************************ 理赔人伤出现人、财产出险人、人伤+财产出险人信息保存  **************/

    /**
     * 理赔 - 保存报案信息 3 6 7接口 根据claim_flag调用不同的方法
     */
    public function claimSaveCaseInfo()
    {
        $original_data = $this->decodeOriginData();

        // 报案类型
        $claim_flag = $original_data['claim_flag'];
        switch ($claim_flag) {
            case 'TKC'://人伤
                $result = $this->claimSaveTKCCaseInfo();
                break;
            case 'TKA'://财产
                $result = $this->claimSaveTKACaseInfo();
                break;
            case 'TKAC'://人伤+财产
                $result = $this->claimSaveTKACCaseInfo();
                break;
            default:
                return ['data' => '报案类型错误', 'code' => 400];
        }

        return $result;
    }

    /**
     * 理赔接口 - 3、	人伤出险人信息保存接口
     */
    public function claimSaveTKCCaseInfo()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKAInfoSave';

        $original_data = $this->decodeOriginData();

        $req['claim_flag'] = 'TKC'; // 报案类型 固定值TKC
        $req['ins_cidnumber'] = $original_data['ins_cidnumber']; // 出险人证件号码
        $req['ins_cidtype'] = $original_data['ins_cidtype']; // 出险人证件类型
        $req['ins_mobile'] = base64_encode($original_data['ins_mobile']); // 出险人手机号
        $req['ins_name'] = urlencode(urlencode($original_data['ins_name'])); // 出险人姓名
        $req['applyname'] = urlencode(urlencode($original_data['applyname'])); // 申请人姓名
        $req['applymobile'] = base64_encode('15701681524'); // 申请人电话
        $req['cidtype'] = $original_data['cidtype']; // 会员证件类型
        $req['cidnumber_decrypt'] = $original_data['cidnumber_decrypt']; // 会员加密证件号
        $req['mobile_decrypt'] = $original_data['mobile_decrypt'] ?: ''; // 会员加密手机号
        $req['coop_id'] = $original_data['coop_id']; // 会员编号
        $req['member_id'] = $original_data['member_id']; // 会员编号
        $req['sign'] = $original_data['sign']; // 会员签名
        $req['relationship'] = $original_data['relationship']; // 申请人出险人关系
        $req['accidentDate'] = $original_data['accidentDate']; // 出险时间
        $req['claim_operatetype'] = 'YDAPP'; // 理赔渠道
        $req['accidentResult'] = implode('|',$original_data['accidentResult']); // 理赔渠道
        $req['company_no'] = $original_data['company_no']; // 理赔渠道
        $req['branch_no'] = substr($original_data['branch_no'], -2);

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['info'], 'code' => 200];
    }

    /**
     * 理赔接口 - 6、	财产险出险人信息保存接口
     */
    public function claimSaveTKACaseInfo()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKAInfoSave';

        $original_data = $this->decodeOriginData();
        $req['claim_flag'] = 'TKA'; // 报案类型 固定值TKC
        $req['ins_cidnumber'] = $original_data['ins_cidnumber']; // 出险人证件号码
        $req['ins_cidtype'] = $original_data['ins_cidtype']; // 出险人证件类型
        $req['ins_mobile'] = base64_encode($original_data['ins_mobile']); // 出险人手机号
        $req['ins_name'] = urlencode(urlencode($original_data['ins_name'])); // 出险人姓名
        $req['applyname'] = urlencode(urlencode($original_data['applyname'])); // 申请人姓名
        $req['applymobile'] = base64_encode($original_data['applymobile']); // 申请人电话
        $req['cidnumber_decrypt'] = $original_data['cidnumber_decrypt']; // 会员加密证件号
        $req['mobile_decrypt'] = $original_data['mobile_decrypt'] ?: ''; // 会员加密手机号
        $req['cidtype'] = $original_data['cidtype']; // 会员证件类型
        $req['coop_id'] = $original_data['coop_id']; // 会员编号
        $req['member_id'] = $original_data['member_id']; // 会员编号
        $req['tka_accidentResult'] = $original_data['tka_accidentResult'];
        $req['relationship'] = $original_data['relationship']; // 申请人出险人关系
        $req['tka_accidentDate'] =  urlencode(urlencode($original_data['tka_accidentDate'])); // 出险时间
        $req['claim_operatetype'] = 'YDAPP'; // 理赔渠道
        $req['tka_company_no'] = $original_data['tka_company_no']; // 理赔渠道
        $req['tka_branch_no'] = substr($original_data['tka_branch_no'], -2); // 理赔渠道
        $req['address'] = urlencode(urlencode($original_data['address'])); // 理赔渠道
        $req['policy_no'] = $original_data['policy_no'];
        $req['lost_money'] = $original_data['lost_money'];
        $req['claim_desc'] = urlencode(urlencode($original_data['claim_desc']));
        $req['tka_name'] = urlencode(urlencode($original_data['tka_name']));
        $req['tka_cidnumber'] = $original_data['tka_cidnumber'];
        $req['tka_cidtype'] = $original_data['tka_cidtype'];
        $req['tka_mobile'] =$original_data['tka_mobile'];
        $req['tka_accidentResult_desc'] = urlencode(urlencode($original_data['tka_accidentResult_desc']));
        $req['yzmCode'] = $original_data['yzmCode']; // 出险人姓名
        $req['sign'] = $original_data['sign']; // 会员签名

        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['info'], 'code' => 200];
    }

    /**
     * 理赔接口 - 7、	人伤+财产险出险人信息保存接口
     */
    public function claimSaveTKACCaseInfo()
    {
        $api = self::API_CLAIM_PRODUCT . '/claimTKAInfoSave';
        $original_data = $this->decodeOriginData();
        $req['claim_flag'] = $original_data['claim_flag']; // 报案类型 固定值TKC
        $req['ins_cidnumber'] = $original_data['ins_cidnumber']; // 出险人证件号码
        $req['ins_cidtype'] = $original_data['ins_cidtype']; // 出险人证件类型
        $req['ins_mobile'] = base64_encode($original_data['ins_mobile']); // 出险人手机号
        $req['ins_name'] = urlencode(urlencode($original_data['ins_name'])); // 出险人姓名
        $req['applyname'] = urlencode(urlencode($original_data['applyname'])); // 申请人姓名
        $req['applymobile'] = base64_encode($original_data['applymobile']); // 申请人电话
        $req['cidnumber_decrypt'] = $original_data['cidnumber_decrypt']; // 会员加密证件号
        $req['mobile_decrypt'] = $original_data['mobile_decrypt'] ?: ''; // 会员加密手机号
        $req['cidtype'] = $original_data['cidtype']; // 会员证件类型
        $req['coop_id'] =$original_data['coop_id'];
        $req['member_id'] =  $original_data['member_id']; // 会员编号
        $req['accidentResult'] = array_shift($original_data['accidentResult']); // 理赔申请类型
        $req['tka_accidentResult'] = $original_data['tka_accidentResult'];	//出险原因	字符	Y	由4中保单号获取到的出险代码作为入参;
        $req['relationship'] = $original_data['relationship']; // 申请人出险人关系
        $req['accidentDate'] = $original_data['accidentDate']; // 出险时间
        $req['tka_accidentDate'] = urlencode(urlencode($original_data['tka_accidentDate']));	//财产险出险时间	字符	Y	YYYY-MM-DD(半角)
        $req['claim_operatetype'] = 'YDAPP'; // 理赔渠道
        $req['company_no'] =  $original_data['company_no']; // 出险省编码
        $req['tka_company_no'] = $original_data['tka_company_no'];	//出险省编码	字符	Y	一位
        $req['branch_no'] = substr($original_data['branch_no'], -2); // 出险市编码
        $req['tka_branch_no'] = substr($original_data['tka_branch_no'], -2);	//	字符	Y	两位
        $req['address'] = urlencode(urlencode($original_data['address']));	//出险详细地址	字符	Y	最多150个字
        $req['policy_no'] = $original_data['policy_no'];	//财产险保单号	字符	Y	最大32位
        $req['lost_money'] = $original_data['lost_money'];	//损失金额	字符	Y
        $req['claim_desc'] = urlencode(urlencode($original_data['claim_desc']));	//事故经过和损失描述	字符	Y	最多150个字
        $req['tka_name'] = urlencode(urlencode($original_data['tka_name']));	//财产险保单被保险人姓名	字符	Y
        $req['tka_cidnumber'] = $original_data['tka_cidnumber'];	//财产险保单被保险人证件号码	字符	Y
        $req['tka_cidtype'] = $original_data['tka_cidtype'];	//财产险保单被保险人证件类型	字符	Y
        $req['tka_mobile'] = $original_data['tka_mobile'];	//财产险保单被保险人手机号	字符	Y
        $req['tka_accidentResult_desc'] = urlencode(urlencode($original_data['tka_accidentResult_desc']));	//出险原因描述	字符	Y
        $req['yzmCode'] = $original_data['yzmCode'] ?: '';	//验证码	字符	Y	6位
        $req['sign'] = $original_data['sign']; // 会员签名

//        return ['data' => $req, 'code' => 400];
        LogHelper::logSuccess($req, '请求的参数', __CLASS__.':'.__FUNCTION__);
        $data = [
            'token' => $this->claimMakeToken($req),
            'req' => $req
        ];
        $response = Curl::to($api)
            ->returnResponseObject()
            ->withData($this->claimEncode($data))
            ->withHeader('Content-Type: application/x-www-form-urlencoded;charset=gbk')
            ->withTimeout(60)
            ->post();

        $response = $this->claimResponseContentConvertEncoding($response);
        if ($response->status != 200) {
            LogHelper::logError($response, '接口调用失败', __CLASS__.':'.__FUNCTION__);
            return ['data' => '接口异常', 'code' => 500];
        }
        LogHelper::logSuccess($response, '接口成功返回数据', __CLASS__.':'.__FUNCTION__);

        $content = json_decode($response->content, true);

        if ($content['success'] != 'Y') {
            return ['data' => $content['desc'], 'code' => 400];
        }

        return ['data' => $content['info'], 'code' => 200];
    }

    /************************************ 理赔人伤出现人、财产出险人、人伤+财产出险人信息保存 end  **************/

    /**
     * 理赔接口 - 生成token
     *
     * @param array $req
     * @return string
     */
    protected function claimMakeToken(array $req)
    {
        ksort($req);
        return  md5(json_encode($req) . self::CLAIM_TOKEN_KEY);
    }

    /**
     * 理赔接口 - 参数加密
     *
     * @param array $data
     * @return string
     */
    protected function claimEncode(array $data)
    {
        $str = json_encode($data);
        $method = 'DES-ECB';
        $des_encode_str = openssl_encrypt($str, $method, self::CLAIM_ENCODE_KEY, OPENSSL_RAW_DATA);
        return base64_encode($des_encode_str);
    }

    /**
     * 理赔接口 - 结果统一转码、json_decode
     * $response['content'] 将存入转码过后的content
     *
     * @param $response
     * @return mixed
     */
    protected function claimResponseContentConvertEncoding($response)
    {
        $content = mb_convert_encoding($response->content, 'utf-8', 'gbk');
        $response->content = $content;
        return $response;
    }

    /****************************************************** 理赔部分结束 ***************************************************/

}
