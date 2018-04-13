<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2018/2/27
 * Time: 15:29
 */

namespace App\Http\Controllers\ApiControllers\Curls;

use App\Models\ApiFrom;
use App\Models\InsApiBrokerage;
use App\Models\Insure;
use App\Models\OrderFinance;
use App\Models\Policy;
use App\Models\User;
use DB;
use App\Helper\LogHelper;
use App\Helper\RsaSignHelp;
use App\Models\InsOrder;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\Helper\InsSign\ZaInsSign;
use App\Repositories\RestrictGeneRepository;
use App\Repositories\InsuranceAttributesRepository;

class ZaInsCurlController
{
    protected $sign_help;
    protected $app_key;
    protected $p_code;
    protected $origin_data;
    protected $insurance_api_from;
    protected $url;

    public function __construct(Request $request, $insurance_api_from=null)
    {
        $this->request = $request;
        $this->sign_help = new RsaSignHelp();
        $this->za_ins_sign = new ZaInsSign();
        if(env('INS_API_TEST', 1)){ //0 生产环境  1 测试环境
            $this->app_key = '8dffac4d1b5c5e3bcaf046ce531aeb81';
            $this->url = 'http://opengw.daily.zhongan.com/Gateway.do';
            $this->pay_merchant_code = '1512000401';
        } else {
            //todo
        }
        if(!preg_match("/call_back/",$this->request->url()) && !preg_match("/test/", $this->request->url())){
            $input = $this->request->all();
            $this->origin_data = $this->sign_help->tyDecodeOriginData($input['biz_content']);   //解签获得源数据
            $this->insurance_api_from = $insurance_api_from;
            $this->p_code = $this->insurance_api_from->p_code;
        }
    }

    public function getApiOption()
    {
        $insuranceAttributesRepository = new InsuranceAttributesRepository();
        $restrictGeneRepository = new RestrictGeneRepository();

        $input = $this->origin_data;   //解签获得源数据

        $insurance_id = $input['ty_product_id'];
        $result['ty_product_id'] = $insurance_id;
        $result['private_p_code'] = $this->insurance_api_from->private_p_code;
        $result['bind_id'] = $this->insurance_api_from->id;
        //投保属性
        $insurance_attributes = $insuranceAttributesRepository->findAttributesRecursionByBindId($this->insurance_api_from->id);
        $insurance_attributes = $insuranceAttributesRepository->unsetAfterFormat($insurance_attributes);
        $result['option']['insurance_attributes'] = $insurance_attributes;

        //试算因子对应选项
        $restrict_genes = $restrictGeneRepository->findRestrictGenesRecursionByBindId($this->insurance_api_from->id);
        $result['option']['restrict_genes'] = $restrictGeneRepository->unsetAfterFormatOnlyTyKey($restrict_genes);
        //获得默认试算选项
        $result['option']['selected_options'] = $restrictGeneRepository->findDefaultRestrictGenes($this->insurance_api_from->id);
        $result['option']['selected_options'] = $restrictGeneRepository->unsetAfterFormatOnlyTyKey($result['option']['selected_options']);
        $default_quote = $this->formatQuote($result['option']['selected_options']);
        //获得默认保费及保障内容
        $result['option']['price'] = $default_quote->price;
        //保障内容
        $result['option']['protect_items'] = $default_quote->protect_items;
        return ['data'=> $result, 'code'=> 200];
    }

    /**
     * 格式化默认算费
     * @param $only_ty_key_array
     * @return mixed
     */
    protected function formatQuote($only_ty_key_array)
    {
        $where = array();
        foreach($only_ty_key_array as $k => $v){
            $where[$v['ty_key']] = $v['value'];
        }
        $quote = DB::table('tariff_za_jk')->where($where)->first();
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
     * 算费
     * @return array
     */
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
        $quote = DB::table('tariff_za_jk')->where($api_data)->first();
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

    public function buyIns()
    {
        $input = $this->origin_data;   //解签获得源数据
//        dd($input);
        $repository = new InsuranceAttributesRepository();
        $tmp_data = $repository->inToOut($this->insurance_api_from->id, $input['insurance_attributes']);    //转换外部键值对
//        dd($tmp_data);
        $quote_selected = json_decode($input['quote_selected'], true);  //算费已选项
        $buy_options = array();
        $priceArgs = json_decode($input['quote_selected'], true);
        $buy_options['insurance_attributes'] = $input['insurance_attributes'];
        $buy_options['quote_selected'] = $priceArgs;
        $api_data = [];
        foreach ($quote_selected as $item) {
            if (isset($item['value'])) {
                $api_data[$item['ty_key']] = $item['value'];
            } else {
                $api_data[$item['ty_key']] = '';
            }
        }
        $quote = DB::table('tariff_za_jk')->where($api_data)->first();
        if(!$quote){
            $msg['data'] = '投保信息与算费信息不符';
            $msg['code'] = 403;
            return $msg;
        }

        $data = [];
        //多为数组 转为 接口所需一维业务参数
        foreach($tmp_data as $k => $v){
            foreach($v as $vk => $vv){
                if(is_array($vv)){  //被保人数组
                    foreach($vv as $vvk => $vvv){
                        $data[$vvk] = $vvv;
                    }
                } else {
                    $data[$vk] = $vv;
                }
            }
        }

        $ins_end_time = ''; //结束日期
        //todo
        switch($quote->ty_duration_period_value){
            case '3个月内':
                $ins_end_time =  date("Y-m-d", strtotime($data['policyBeginDate'] . " + 3 months"));
                break;
            case '3-12个月':
                $ins_end_time =  date("Y-m-d", strtotime($data['policyBeginDate'] . " + 1 years"));
                break;
        }
        $ins_end_time = date("Y-m-d", strtotime($ins_end_time." -1 seconds"));
        $data['policyEndDate'] = $ins_end_time;
        $data['channelOrderNo'] = date('YmdHis') . rand(10, 99) . rand(100, 999); //订单号
//        $data['campaignDefId'] = '10002538147'; //订单号
//        $data['packageDefId'] = $this->insurance_api_from->p_code;   //外部产品码
        $data['productMaskCode'] = $this->p_code;   //产品吗
        $data['sumInsured'] = '600000';   //保额
        $data['premium'] = $quote->price / 100 . '';   //保费
//        dd($data);
        $post_data = $this->formatBeforeCurl($data, 'zhongan.tianyan.loan.check');

        $response = Curl::to($this->url)
            ->returnResponseObject()
            ->withData($post_data)
//            ->withHeader("Content-Type: application/json;charset=UTF-8")
            ->withTimeout(100)
            ->post();
//        dd($response);
        if($response->status != 200){
            LogHelper::logError($response, 'za', 'buy_ins');
            return ['data'=> 'insured error', 'code'=> 400];
        }
        $res = json_decode($response->content, true);

        $sign_response = $res ["sign"];
        unset ($res ["sign"]);
        $_signCheckRst = $this->za_ins_sign->checkSign($res, $sign_response);
        if ($_signCheckRst != 1) {
            return ['data'=> '本地验签失败', 'code'=> 400];
        }
//        dd($res);
        $decryptedData = $this->za_ins_sign->decrypt($res["bizContent"]);
        $biz_content = isset($res["bizContent"]) ? json_decode ($decryptedData, true) : false;
        if(!$biz_content['isSuccess']){
            LogHelper::logError($decryptedData, 'za', 'buy_ins');
            return ['data'=> $biz_content['errorMsg'], 'code'=> 400];
        }
        return $this->addOrder($data, $buy_options);

    }

    public function formatBeforeCurl($bizParams, $service_name)
    {
        list ( $usec, $sec ) = explode ( " ", microtime () );
        $timestamp =  date ( "YmdHis" ) . sprintf ( "%03d", intval ( $usec * 1000 ) );
        $bizContent = $this->za_ins_sign->encrypt($bizParams);
        $allParams = array(
            "serviceName" => $service_name,
            "appKey" => $this->app_key,
            "format" => 'json',
            "signType" => 'RSA',
            "charset" => 'UTF-8',
            "version" => '1.0.0',
            "timestamp" => $timestamp,
            "bizContent" => $bizContent
        );
        $_signRequest = $this->za_ins_sign->sign($allParams);
        $allParams ["sign"] = $_signRequest;
//        dd($allParams);
        return $allParams;
    }

    public function addOrder($data, $buy_options)
    {
        $api_form = ApiFrom::where('id', $this->insurance_api_from->api_from_id)->first();
        try {
            DB::beginTransaction();
            //订单信息
            $order = new InsOrder();
            $order->order_no = $data['channelOrderNo']; //内部订单号
            $order->union_order_code = $data['channelOrderNo']; //外部总订单号
            $order->create_account_id = $this->request['account_id'];   //代理商account_id
            $order->api_from_uuid = $api_form->uuid;    //接口来源唯一码（接口类名称)
            $order->api_from_id = $api_form->id;    //接口来源唯一码（接口类名称)
            $order->ins_id = $this->insurance_api_from->insurance_id; //产品唯一码
            $order->p_code = $this->p_code; //产品唯一码
            $order->bind_id = $this->insurance_api_from->id;
            $order->total_premium = $data['premium'] * 100;  //总保费
            $order->status = 'check_ing'; //待核保状态
            $order->buy_options = json_encode($buy_options, JSON_UNESCAPED_UNICODE);
            $order->by_stages_way = '0年';
            $order->start_time = $data['policyBeginDate'] . " 00:00:00";
            $order->end_time = $data['policyEndDate'] . " 23:59:59";
//            dd($buy_options);
            $order->save();

            //投保人信息
            $policy = new Policy();
            $policy->union_order_code = $order->order_no;  //外部总定单号
            $policy->ins_order_id = $order->id; //订单ID
            $policy->name = $buy_options['insurance_attributes']['ty_toubaoren']['ty_toubaoren_name'];   //投保人名称
            $policy->phone = $buy_options['insurance_attributes']['ty_toubaoren']['ty_toubaoren_phone']; //投保人电话
            $policy->card_type = $buy_options['insurance_attributes']['ty_toubaoren']['ty_toubaoren_id_type'];   //投保人卡号类型
            $policy->card_id = $buy_options['insurance_attributes']['ty_toubaoren']['ty_toubaoren_id_number'];
            $policy->address = '';
//            $policy->email = $post_data['ty_toubaoren']['ty_toubaoren_email'];
            $policy->sex = $buy_options['insurance_attributes']['ty_toubaoren']['ty_toubaoren_sex'] ? '男' : '女';
            $policy->birthday = $buy_options['insurance_attributes']['ty_toubaoren']['ty_toubaoren_birthday'];
            $policy->save();

            //被保人信息
            $insures = array();
            foreach ($buy_options['insurance_attributes']['ty_beibaoren'] as $k => $v) {
                if ($insures[$k]['relation'] = $v['ty_relation'] == 1) {
                    $insures[$k]['ins_order_id'] = $order->id;
                    $insures[$k]['out_order_no'] = $order->order_no;   //被保人单号(分接口来源)
                    $insures[$k]['premium'] = $order->total_premium;    //保费
                    $insures[$k]['p_code'] = $this->p_code;    //外部产品码
                    $insures[$k]['union_order_code'] = $order->order_no;  //外部合并订单号
                    $insures[$k]['name'] = $policy->name;
                    $insures[$k]['sex'] = $policy->sex;
                    $insures[$k]['phone'] = $policy->phone;
                    $insures[$k]['card_type'] = $policy->card_type;
                    $insures[$k]['card_id'] = $policy->card_id;
                    $insures[$k]['relation'] = $v['ty_relation'];
                    $insures[$k]['birthday'] = $policy->birthday;
                    $insures[$k]['ins_start_time'] = $buy_options['insurance_attributes']['ty_base']['ty_start_date'];
                    $insures[$k]['ins_end_time'] = $data['policyEndDate'] . " 23:59:59";;
                    $insures[$k]['created_at'] = $insures[$k]['updated_at'] = date("Y-m-d H:i:s");
                } else {
                    $insures[$k]['ins_order_id'] = $order->id;   //被保人单号 分接口来源 惠泽返回无此参数
                    $insures[$k]['out_order_no'] = $order->order_no;   //被保人单号(分接口来源)
                    $insures[$k]['premium'] = $order->total_premium;    //保费
                    $insures[$k]['p_code'] = $this->p_code;    //外部产品码
                    $insures[$k]['union_order_code'] = $order->order_no;  //外部合并订单号
                    $insures[$k]['name'] = $v['ty_beibaoren_name'];
                    $insures[$k]['sex'] = $v['ty_beibaoren_sex'];
                    $insures[$k]['phone'] = $v['ty_beibaoren_phone'];
                    $insures[$k]['card_type'] = $v['ty_beibaoren_id_type'];
                    $insures[$k]['card_id'] = $v['ty_beibaoren_id_number'];
                    $insures[$k]['relation'] = $v['ty_relation'];
                    $insures[$k]['birthday'] = $v['ty_beibaoren_birthday'];
                    //$insures[]['address'] = $v['holderAddress'];
//                $insures[]['email'] = $v['holderEmail'];
                    $insures[$k]['ins_start_time'] = $buy_options['insurance_attributes']['ty_base']['ty_start_date'];
                    $insures[$k]['created_at'] = $insures[$k]['updated_at'] = date("Y-m-d H:i:s");
                }
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
            $res['total_premium'] = $order->total_premium;
            $res['union_order_code'] = $order->union_order_code;
            $res['pay_way'] = [
                'pc' => [
                    'aliPay' => '支付宝支付',
                    'wechatPay' => '微信支付'
                ],
                'mobile' => [
                    'aliPay' => '支付宝支付',
                    'wechatPay' => '微信支付'
                ],
            ];
            $msg = ['data' => $res, 'code' => 200];
//            dd($msg);
            return $msg;
        } catch (\Exception $e){
            DB::rollBack();
//            dd($e->getMessage());
            LogHelper::logError($e->getMessage(), 'za', 'buy_ins');
            return ['data'=>'订单录入失败', 'code' => 400];
        }
    }

    /**
     * 获取支付链接
     * @return array
     */
    public function getPayWayInfo()
    {
        $input = $this->origin_data;
//        dd($input);
        if(empty($input['pay_way'] || $input['union_order_code']))
            return ['data'=>'订单号或支付类型错误', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->with('insurance')->first();
        if(empty($ins_order))
            return ['data'=>'订单号错误', 'code'=> 400];
        if($ins_order->status == 'check_error')
            return ['data'=> $ins_order->insures()->first()->check_error_message, 'code'=> 400];
        $pay_data = ['input'=> $input, 'order'=> $ins_order, 'src_type' => 'pc'];
        if($input['is_phone'])
            $pay_data['src_type'] = 'mobile';
        $pay_url = $this->fomartPayInfo($pay_data);
        $ins_order->status = 'pay_ing';
        $ins_order->save();
        Insure::where('ins_order_id', $ins_order->id)->update(['status'=>'pay_ing']);
        return ['data'=>['order_code'=>$ins_order->union_order_code, 'pay_way_data'=> ['url'=>$pay_url]], 'code'=> 200];
    }

    /**
     * 支付参数格式化
     * @param $pay_data
     * @return string
     */
    public function fomartPayInfo($pay_data)
    {
        $requestData=[
            'request_charset'=>'UTF-8',                             //编码
            'sign_type'=>'MD5',                                     //签名方式
            'out_trade_no'=> $pay_data['order']->order_no,          //商户订单号
            'merchant_code'=>$this->pay_merchant_code,              //商户号
            'subject'=> $pay_data['order']->insurance->name,          //产品名称
//            'body'=>'描述',                                         //产品描述
//            'expiry_time'=>30,                                      //订单有效时间  单位：分钟
//            'pay_channel'=>'alipay^wxpay^anthb',                    //支付渠道：默认全开通 alipay^wxpay^anthb
            'src_type'=> $pay_data['src_type'],                       //来源类型 pc/mobile   电脑端/移动端
//            'order_type'=>'insurance',                              //订单类型：默认保险订单
//            'show_url'=>'http://www.xxx.com',                       //商品地址
            'notify_url' => env('APP_URL') . '/api/ins/za/call_back', //服务器通知回调url
            'back_url' => $pay_data['input']['redirect_url'],          //关闭跳转return_url
            'return_url'=>  $pay_data['input']['redirect_url'],       //前端跳转url
//            'notify_info'=>'{"a":"abcxyz","b":"XXXX","c":"9.00"}',  //公用回传参数  json格式
//            'order_info'=>'{"被保人":"XXX","保险期限":"XXXX"}',       //产品详情  json格式
        ];
        $requestData['amt'] = $pay_data['order']['total_premium'] / 100;
//        if(env('INS_API_TEST', 1)){
//            $requestData['amt'] = 0.01;    //支付金额 单位:元 精确到小树点后两位
//        }
        return $this->za_ins_sign->createPayUrl($requestData);
    }

    /**
     * 支付回调
     */
    public function payCallBack()
    {
        $input = $this->request->all();
        LogHelper::logSuccess($input, 'za', 'call_back_a');
        if ($input['pay_result'] != 'S')
            die;
        $order = InsOrder::where(['union_order_code' => $input['out_trade_no'], 'api_from_uuid' => 'Za'])->first();
        $user = User::where('account_id', $order->create_account_id)->first();
        $order->status = 'pay_end';
        $order->pay_code = $input['za_order_no'];
        $order->pay_time = $input['pay_time'];
        $order->save();
        Insure::where('ins_order_id', $order->id)->update(['status' => 'pay_end']);
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
        $finance->brokerage_for_us = $order->total_premium * $brokerage->ratio_for_us / 100;
        $finance->brokerage_for_agency = $order->total_premium * $brokerage->ratio_for_agency / 100;
        $finance->save();

        $data = [
            "notice_type" => 'pay_call_back',
            'data' => [
                'status' => true,
                'ratio_for_agency' => $brokerage->ratio_for_agency,
                'brokerage_for_agency' => $finance->brokerage_for_agency,
                'union_order_code' => $order->union_order_code,
                'by_stages_way' => $order->by_stages_way,
                'error_message' => '',
            ]
        ];
        //通知312
        $response = Curl::to($user->call_back_url . '/ins/call_back')
            ->returnResponseObject()
            ->withData($data)
            ->asJson()
            ->withTimeout(60)
            ->post();
        echo 'success';

    }

    //出单
    public function issue()
    {
        $input = $this->origin_data;
        if(!$input['union_order_code'])
            return ['data'=>'订单号不可为空', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();
        if(empty($ins_order))
            return ['data'=>'订单不存在', 'code'=> 400];
        if(!in_array($ins_order->status, ['pay_end']))
            return ['data'=>'此订单无法出单', 'code'=> 400];

        $data['channelOrderNo'] = $ins_order->order_no;
        $data['productMaskCode'] = $ins_order->p_code;   //产品吗
        $data['payTradeNo'] = $ins_order->pay_code;   //支付流水号
//        LogHelper::logError($data, 'insure_post');
        $post_data = $this->formatBeforeCurl($data, 'zhongan.tianyan.loan.confirm');
        //todo
        $response = Curl::to($this->url)
            ->returnResponseObject()
            ->withData($post_data)
            ->withTimeout(100)
            ->post();

        if($response->status != 200){
            LogHelper::logError($response, 'za', 'buy_ins');
            return ['data'=> 'insured error', 'code'=> 400];
        }
        $res = json_decode($response->content, true);

        $sign_response = $res ["sign"];
        unset ($res ["sign"]);
        $_signCheckRst = $this->za_ins_sign->checkSign($res, $sign_response);
        if ($_signCheckRst != 1) {
            return ['data'=> '本地验签失败', 'code'=> 400];
        }
//        dd($res);
        $decryptedData = $this->za_ins_sign->decrypt($res["bizContent"]);
        $biz_content = json_decode ($decryptedData, true);
        if(!$biz_content['isSuccess']){
            LogHelper::logError($biz_content, 'insure');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        } else {
            Insure::where('out_order_no', $ins_order->union_order_code)->update([
                'ins_policy_code'=>$biz_content['response']['policyNo'],
                'e_policy_url'=>$biz_content['response']['ePolicyUrl'],
                'policy_status' => 6
            ]);
            $return = array();
            //返回结果封装 todo
            $return['status'] = 0;    //出单状态 0：未生效 1：已生效 2：退保中 3：已退保
            $return['policy_order_code'] = $biz_content['response']['policyNo'];   //被保人保单号
            $return['private_p_code'] = $input['private_p_code'];   //产品码
            $return['order_code'] = $input['order_code'];   //被保人单号

            $return['start_time'] = $ins_order->start_time;
            $return['end_time'] = $ins_order->end_time;
//                $return['projects'] = $protect_items;
            LogHelper::logError($return, 'return');
            return ['data'=> $return, 'code'=> 200];
        }

    }

    //退保
    public function rejectIns()
    {
        $input = $this->request->all();
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->with('insures')->first();
        $data['channelOrderNo'] = $ins_order->order_no;
        $data['productMaskCode'] = $ins_order->p_code;   //产品吗
        $data['policyNo'] = $ins_order->insures[0]->ins_policy_code;   //支付流水号
//        dd($data);
//        LogHelper::logError($data, 'insure_post');
        $post_data = $this->formatBeforeCurl($data, 'zhongan.tianyan.loan.cancel');
        //todo
        $response = Curl::to($this->url)
            ->returnResponseObject()
            ->withData($post_data)
            ->withTimeout(100)
            ->post();

        if($response->status != 200){
            LogHelper::logError($response, 'za', 'buy_ins');
            return ['data'=> 'reject ins error', 'code'=> 400];
        }
        $res = json_decode($response->content, true);

        $sign_response = $res ["sign"];
        unset ($res ["sign"]);
        $_signCheckRst = $this->za_ins_sign->checkSign($res, $sign_response);
        if ($_signCheckRst != 1) {
            return ['data'=> '本地验签失败', 'code'=> 400];
        }
//        dd($res);
        $decryptedData = $this->za_ins_sign->decrypt($res["bizContent"]);
        $biz_content = json_decode ($decryptedData, true);
//        dd($biz_content);
        if(!$biz_content['isSuccess']){
            LogHelper::logError($biz_content, 'insure');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        } else {
            LogHelper::logSuccess($biz_content, 'za', 'rejectIns');
            $ins_order->status = 'send_back_end';
            $ins_order->insures[0]->status = 'send_back_end';
            $ins_order->save();
            $ins_order->insures[0]->save();

        }
    }

    //T-1日保单推送
    public function issuePushCallBack()
    {
        $input = $this->request->all();
        $input = json_decode($this->za_ins_sign->pushDecrypt($input['data']), true);
//        LogHelper::logSuccess($input, 'za', 'call_back_a');
        LogHelper::logSuccess($input, 'za', 'call_back_a');
        $dataList = [];
        foreach($input['policyList'] as $k => $v){
            $dataList[$k]['policyNo'] = $v['policyNo'];
            $dataList[$k]['dataType'] = $v['dataType'];
            $dataList[$k]['isSuccess'] = true;
            $dataList[$k]['errorCode'] = '0';
            $dataList[$k]['errorMsg'] = '成功';
        }
        $return = [
            'isSuccess'=> true,
            'errorCode'=> '0',
            'errorMsg'=> '成功',
            'dataList'=> $dataList
        ];
        return json_encode($return);
    }
}