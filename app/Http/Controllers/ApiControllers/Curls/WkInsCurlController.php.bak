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
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Illuminate\Support\Facades\DB;
use Ixudra\Curl\Facades\Curl;
use App\Models\InsOrder;
use App\Models\Insure;
use App\Helper\LogHelper;

class WkInsCurlController
{
    /**
     * 算费接口
     */
    const API_QUOTE = 'http://test.openapi.wkbins.com/openapi/life/quote/interface';

    /**
     * 投保接口
     */
    const API_INSURE = 'http://test.openapi.wkbins.com/openapi/life/insure/interfaceFresh';

    /**
     * 支付接口
     */
    const API_PAY = 'http://test.openapi.wkbins.com/openapi/life/payOrder/interfaceFesh';

    /**
     * 出单接口
     */
    const API_ISSUE = 'http://test.openapi.wkbins.com/openapi/life/underwriting/interface';

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
        $this->private_key = file_get_contents('../config/rsa_private_key_1024_pkcs8.pem.pem');
        $this->public_key = file_get_contents('../config/rsa_public_key_1024_pkcs8.pem');
        $this->wk_public_key = file_get_contents('../config/wk_rsa_public_key.pem');

        if(!preg_match("/call_back/",$this->request->url())) {
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

            if (empty($this->bind)) {
                return ['data' => 'product not exist', 'code' => 400];
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

        // 格式化成api需要的格式 ty_key => key; 内部值 -> 接口值
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
        if (empty($data['insuredBirthday'])) {
            $api_data['insuredBirthday'] = date('Y-m-d');
        }
        // ty_key => key; 内部值 -> 接口值
        foreach ($restrict_genes as $restrict_gene) {
            if (isset($api_data[$restrict_gene['ty_key']])) {
                foreach ($restrict_gene['values'] as $value) {
                    if ($api_data[$restrict_gene['ty_key']] == $value['ty_value']) {
                        $api_data[$restrict_gene['ty_key']] = $value['value'];
                        break;
                    }
                }
                $api_data[$restrict_gene['key']] = $api_data[$restrict_gene['ty_key']];
                unset($api_data[$restrict_gene['ty_key']]);
            }
        }
        $api_data['productCode'] = $this->bind->p_code;
        // 封装参数
        $api_data = json_encode($api_data);
        $api_data = base64_encode($this->rsaEncrypt('rsa_public_key_encrypt', $this->wk_public_key, $api_data));//加密后的数据
        $api_data = $this->formatData($api_data);
        // 调接口
        $response = Curl::to(self::API_QUOTE)
            ->returnResponseObject()
            ->withData($api_data)
            ->withTimeout(60)
            ->post();
        $result = json_decode($response->content, true);
        if (!$result) {
            return ['data' => '参数错误', 'code' => 400];
        }
        $checkSign = $this->checkReturnSign($result);

        if ($checkSign == 1) {        //验签成功
            //业务请求结果判断
            if ($result['msgCode'] != 'SUCCESS') {
                $msg['data'] = '算费无结果';
                $msg['code'] = 400;
                return $msg;
            }
            //解密获得返回结果
            $de_bizContent = $this->rsaDecrypt($result['bizContent']);
            $de_bizContent = json_decode($de_bizContent, true);
            $msg['data']['price'] = $de_bizContent['rateMoney'] * 100;
            $selected_options = json_decode($original_data['new_val'], true);
            // 替换"key"为"ty_key"
            foreach ($selected_options as &$item) {
                $item['ty_key'] = $item['key'];
                unset($item['key']);
            }
            $msg['data']['selected_options'] = $selected_options;
            $msg['data']['new_genes'] = $restrict_genes;
            $msg['code'] = 200;
            return $msg;
        } else {
            $msg['data'] = "返回结果验签失败";
            $msg['code'] = 400;
            return $msg;
        }
    }

    /**
     * 投保
     * @return array
     */
    public function buyIns()
    {
        $msg = []; //初始化返回结果
        $tmp_data = [];
        $original_data = $this->original_data;
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
            if ($module_key == 'ty_beibaoren') {
                $data['policyInsuredList'] = $item;
            } elseif ($module_key == 'ty_toubaoren') {
                $data['policyHolder'] = $item;
            } else {
                array_push($data, $item);
            }
        }
        // 加入悟空保默认内置参数
        $data = $this->originalBuyInsDataMake($data);
        $data['productCode'] = $this->bind->p_code;
//                return ['data' => $data, 'code' => 403];
        $biz_content = json_encode($data);
        //业务参数 加密
        $biz_content = base64_encode($this->rsaEncrypt('rsa_public_key_encrypt', $this->wk_public_key, $biz_content));//加密后的数据
        //系统参数排序、拼接成字符串 格式化后加签
        $biz_content = $this->formatData($biz_content);
        //请求
        $response = Curl::to(self::API_INSURE)
            ->returnResponseObject()
            ->withData($biz_content)
            ->withTimeout(60)
            ->post();

        $result = json_decode($response->content, true);
        if (!$result) {
            return ['data' => '参数错误', 'code' => 400];
        }
        //结果验签
        $checkSign = $this->checkReturnSign($result);
        //验签
        if ($checkSign == 1) {
            //业务请求结果判断
            if ($result['msgCode'] != 'SUCCESS') {
                $msg['data'] = $result['msgInfo'];
                $msg['code'] = 403;
                return $msg;
            }
            //解密获得返回结果
            $return_biz_json = $this->rsaDecrypt($result['bizContent']);
            //            return ['data' => json_decode($return_biz_json, true), 'code' => 403];
            $buy_options = [];
            $buy_options['insurance_attributes'] = $original_data['insurance_attributes'];
            $buy_options['quote_selected'] = json_decode($original_data['quote_selected'], true);
            $res = $this->addOrder($data, $return_biz_json, $buy_options);
            return $res;
        } else {  //验签失败
            $msg['data'] = "返回结果验签失败";
            $msg['code'] = 400;
            return $msg;
        }
    }

    /**
     * 核保 正常状态不使用 而是通过异步回调接口返回处理
     */
    public function checkIns()
    {
        $msg = array(); //初始化返回结果
        $input = $this->request->all();
        //业务参数 解析出源数据json字符串
        $original_data_str = $this->sign_help->base64url_decode(strrev($input['biz_content']));

        //加密 业务参数
        $biz_content = base64_encode($this->rsaEncrypt('rsa_public_key_encrypt', $this->wk_public_key, $original_data_str));//加密后的数据
        //系统参数格式化、加签
        $data = $this->formatDate($biz_content);
        //请求
        $response = Curl::to($this->api_from->hebao_api)
            ->returnResponseObject()
            ->withData($data)
            ->withTimeout(60)
            ->post();
        $result = json_decode($response->content, true);
        //结果验签
        $checkSign = $this->checkReturnSign($result);
        //验签成功
        if ($checkSign == 1) {
            //业务请求结果判断
            if ($result['msgCode'] != 'SUCCESS') {
                $msg['data'] = $result['msgInfo'];
                $msg['code'] = 403;
                return $msg;
            }
            //解密获得返回结果
            $de_bizContent = json_decode($this->rsaDecrypt($result['bizContent']), true);
            try{
                switch ($de_bizContent['undwrtStatus']) {
                    case 0:
                        $msg = ['data' => '核保中', 'code' => 200];
                        break;
                    case 1:
                        $this->changeOrderInfo($de_bizContent['orderCode'], 'pay_ing');
                        $msg = ['data'=> '核保成功', 'code' => 200];
                        break;
                    case 2:
                        $this->changeOrderInfo($de_bizContent['orderCode'], 'check_error', $de_bizContent['undwrtMsg']);
                        $msg = ['data'=> $de_bizContent['undwrtMsg'], 'code' => 444];
                        break;
                }
                return $msg;
            } catch(\Exception $e){
                return $msg = ['data' => $e->getMessage(), 'code' => 400];
            }
        } else {  //验签失败
            $msg['data'] = "返回结果验签失败";
            $msg['code'] = 400;
            return $msg;
        }
    }

    /**
     * 支付
     * @return array
     */
    public function payIns()
    {
        $msg = []; //初始化返回结果:/
        $original_data = $this->original_data;

        $all_insure = Insure::where(['union_order_code' => $original_data['unionOrderCode']])->get();
        LogHelper::logError($all_insure, 'pay_ins', 'pay_ins');
        $allowed_status = ['pay_ing', 'pay_error'];
        foreach ($all_insure as $ak => $av) {
            if (false === array_search($av->status, $allowed_status)){
                $msg['data'] = '当前订单已失效';
                $msg['code'] = 403;
                return $msg;
            }
        }
        // 支付接口数据
        $pay_data = [
            'unionOrderCode' => $original_data['unionOrderCode'],
            'hengqinBankCode' => $original_data['bank_code'],
            'bankCode' => $original_data['bank_uuid'],
            'bankCardNum' => $original_data['bank_number']
        ];

        //加密 业务参数
        $pay_data = base64_encode($this->rsaEncrypt('rsa_public_key_encrypt', $this->wk_public_key, json_encode($pay_data)));

        //系统参数格式化、加签
        $data = $this->formatData($pay_data);
        //请求
        $response = Curl::to(self::API_PAY)
            ->returnResponseObject()
            ->withData($data)
            ->withTimeout(60)
            ->post();

        $result = json_decode($response->content, true);
        if(empty($result))
            return ['data'=> '参数异常', 'code' => 403];
        //结果验签
        $checkSign = $this->checkReturnSign($result);
        if ($checkSign != 1){
            $msg['data'] = "返回结果验签失败";
            $msg['code'] = 400;
            return $msg;
        }
        //业务请求结果判断
        if ($result['msgCode'] != 'SUCCESS') {
            $msg['data'] = $result['msgInfo'];
            $msg['code'] = 403;
            return $msg;
        }
        //解密获得返回结果
        $de_bizContent = json_decode($this->rsaDecrypt($result['bizContent']), true);
        try{
            switch ($de_bizContent['orderInfos']['payResultstatus']){
                case 0:
                    $this->changeOrderInfo($de_bizContent['orderInfos']['orderCode'], 'pay_end');
                    $msg = ['data'=> '支付成功', 'code' => 200];
                    break;
                case 1:
                    $this->changeOrderInfo($de_bizContent['orderInfos']['orderCode'], 'pay_error', $de_bizContent['orderInfos']['resultBackInfo']);
                    $msg = ['data'=> '支付失败' . $de_bizContent['orderInfos']['resultBackInfo'], 'code' => 444];
                    break;
                case 2:
                    $msg = ['data'=> '支付中', 'code' => 201];
                    break;
            }
            return $msg;
        } catch (\Exception $e) {
            return $msg = ['data' => $e->getMessage(), 'code' => 400];
        }


    }

    /**
     * 出单
     * @return array
     */
    public function issue()
    {
        //初始化返回结果
        $msg = [];
        $original_data = $this->original_data;
        $api_data = [
            'orderCode' => $original_data['order_code'],
            'unionOrderCode' => $original_data['union_order_code'],
            'productCode' => $this->bind->p_code
        ];
        //加密 业务参数
        $api_data = base64_encode($this->rsaEncrypt('rsa_public_key_encrypt', $this->wk_public_key, json_encode($api_data)));//加密后的数据
        //系统参数格式化、加签
        $api_data = $this->formatData($api_data);
        //请求
        $response = Curl::to(self::API_ISSUE)
            ->returnResponseObject()
            ->withData($api_data)
            ->withTimeout(60)
            ->post();
        $result = json_decode($response->content, true);

        //结果验签
        $checkSign = $this->checkReturnSign($result);

        if ($checkSign == 1) {        //验签成功
            //业务请求结果判断
            if ($result['msgCode'] != 'SUCCESS') {
                $msg['data'] = $result['msgInfo'];
                $msg['code'] = 403;
                return $msg;
            }
            //解密获得返回结果
            $de_bizContent = json_decode($this->rsaDecrypt($result['bizContent']), true);
            //            return ['data' => json_encode($de_bizContent), 'code' => 233];
            return $this->handleIssue($de_bizContent);
        } else {
            $msg['data'] = "返回结果验签失败";
            $msg['code'] = 400;
            return $msg;
        }
    }

    /**
     * 调用出单接口后，处理数据
     *
     * 修改保单状态，存储保险开始时间(保险起期)和保险结束时间(保险止期)
     *
     * 暂时把接口返回的bizContent的数据全部返回给代理
     *
     * @param $data
     * @return array
     */
    protected function handleIssue($data)
    {
        $result = []; // 回调给代理商的数据
        try {
            DB::beginTransaction();
            $order = Insure::where('out_order_no', $data['orderCode'])->first();
            $order->policy_status = $data['policyStatus'];  //保单状态
            $order->ins_start_time = $result['start_time'] = $data['policyBeginDate'];  //生效日期
            $order->ins_end_time = $result['end_time'] = $data['policyEndDate'];  //结束日期
            $order->ins_policy_code = $result['policy_order_code'] = $data['supplierInsurancePolicyCode']; //保单号
            $order->save();
            DB::commit();
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
     * 核保回调
     */
    public function checkCallBack()
    {
        $all = $this->request->all();
        $biz_content = $all['bizContent'];

        if ($biz_content['underwritingResult'] == 'success') {
            LogHelper::logSuccess($all, 'wk', 'check_ins_call_back');
            $status = 'pay_ing';
        } elseif ($biz_content['underwritingResult'] == 'failed') {
            LogHelper::logError($all, $biz_content['underwritingMsg'], 'check_ins_call_back');
            $status = 'check_error';
        }

        $this->changeOrderInfo($biz_content['orderCode'], $status, $biz_content['underwritingMsg']);
    }

    /**
     * 支付回调
     * 修改订单状态在payIns方法，这个方法不一定回调
     */
    public function payCallBack()
    {
        $all = $this->request->all();
        LogHelper::logSuccess($all, 'wk', 'pay_ins_call_back');
        $biz_content = $all['bizContent'];

        if ($biz_content['payResult'] == 'success') {
            $status = 'pay_end';
        } elseif ($biz_content['payResult'] == 'failed') {
            $status = 'pan_error';
        }

        try {
            $this->changeOrderInfo($biz_content['orderCode'], $status, $biz_content['resultBackInfo']);
            $msg['data'] = $biz_content;
            $msg['code'] = 200;
            return $msg;
        } catch (\Exception $exception) {
            LogHelper::logError($all, $exception->getMessage(), 'pay_ins_call_back');
        }
    }

    /**
     * 不同返回结果的订单状态更改
     * @param $order_no
     * @param $status
     * @param null $error_msg
     * @return mixed
     * @throws \Exception
     */
    protected function changeOrderInfo($order_no, $status, $error_msg = null)
    {
        try {
            $insure = Insure::where('out_order_no', $order_no)->first();
            switch ($status) {
                case 'check_error':
                    $insure->check_error_message = $error_msg;
                    $insure->status = 'check_error';
                    $insure->save();
                    $conditions = [
                        'union_order_code' => $insure->union_order_code,
                        'api_from_uuid' => 'Wk'
                    ];
                    $order = InsOrder::where($conditions)->first();
                    $order->status = 'check_error';
                    $order->save();
                    $ty_callback_data = [
                        'notice_type' => 'check_call_back',
                        'data' => [
                            'status' => false,
                            'union_order_code' => $insure->union_order_code,
                            'error_message' => $error_msg
                        ]
                    ];
                    $user = User::where('account_id', $order->create_account_id)->first();
                    Curl::to(rtrim($user->call_back_url, '/') . '/ins/call_back')
                        ->returnResponseObject()
                        ->withData($ty_callback_data)
                        ->withTimeout(60)
                        ->post();
                    break;
                case 'send_back_error':
                    $insure->send_back_error_message = $error_msg;
                    InsOrder::where('union_order_code', $insure->union_order_code)->update(['status'=>'send_back_error']);
                    break;
                case 'pay_error':
                    Insure::where('union_order_code', $insure->union_order_code)->update([
                        'status' => 'pay_error',
                        'pay_error_message' => $error_msg
                    ]);
                    $conditions = [
                        'union_order_code' => $insure->union_order_code,
                        'api_from_uuid' => 'Wk'
                    ];
                    $order = InsOrder::where($conditions)->first();
                    $order->status = 'pay_error';
                    $order->save();
                    $ty_callback_data = [
                        'notice_type' => 'pay_call_back',
                        'data' => [
                            'status' => false,
                            'union_order_code' => $insure->union_order_code,
                            'error_message' => $error_msg
                        ]
                    ];
                    $user = User::where('account_id', $order->create_account_id)->first();
                    Curl::to(rtrim($user->call_back_url, '/'). '/ins/call_back')
                        ->returnResponseObject()
                        ->withData($ty_callback_data)
                        ->withTimeout(60)
                        ->post();
                    break;
                case 'pay_ing':
                    Insure::where('union_order_code', $insure->union_order_code)->update([
                        'status' => 'pay_ing'
                    ]);
                    $conditions = [
                        'union_order_code' => $insure->union_order_code,
                        'api_from_uuid' => 'Wk'
                    ];
                    $order = InsOrder::where($conditions)->first();
                    $order->status = 'pay_ing';
                    $order->save();
                    $ty_callback_data = [
                        'notice_type' => 'check_call_back',
                        'data' => [
                            'status' => true,
                            'union_order_code' => $insure->union_order_code,
                            'error_message' => $error_msg
                        ]
                    ];
                    $user = User::where('account_id', $order->create_account_id)->first();
                    Curl::to(rtrim($user->call_back_url, '/') . '/ins/call_back')
                        ->returnResponseObject()
                        ->withData($ty_callback_data)
                        ->withTimeout(60)
                        ->post();
                    break;
                case 'pay_end':
                    //todo
                    Insure::where('union_order_code', $insure->union_order_code)->update([
                        'status' => 'pay_end'
                    ]);
                    $conditions = [
                        'union_order_code' => $insure->union_order_code,
                        'api_from_uuid' => 'Wk'
                    ];
                    $order = InsOrder::where($conditions)->first();
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
                    $user = User::where('account_id', $order->create_account_id)->first();
                    Curl::to(rtrim($user->call_back_url, '/') . '/ins/call_back')
                        ->returnResponseObject()
                        ->withData($ty_callback_data)
                        ->withTimeout(60)
                        ->post();
                    break;
            }
        } catch (\Exception $e) {
            LogHelper::logError([$order_no, $status], json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE), 'wk', 'changeStatusWithCheckCallBack');
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $data
     * @return mixed
     * @internal param $json 格式化请求参数* 格式化请求参数
     */
    public function originalBuyInsDataMake($data)
    {
        $data['needShordMes'] = "N";    //不需要验证码
        $data['underwritingBackUrl'] = env('APP_SERVICE_URL') . "api/ins/wk/call_back/check";   //核保成功异步回调给商户的地址
        $data['payHandleBackUrl']= env('APP_SERVICE_URL'). "api/ins/wk/call_back/pay";    //支付状态异步回调接口地址；下面支付接口原本为同步，如有超时，支付状态异步回调接口地址
        $data['merchantOrderCode'] = date('YmdHis'). rand(10, 99) . rand(100, 999);

        return $data;
    }

    /**
     * 保存订单数据
     * @param $json
     * @param $buy_ins_return_json
     * @return array
     */
    public function addOrder($post_data, $buy_ins_return_json, $buy_options)
    {
        $return_data = json_decode($buy_ins_return_json, true);
        try{
            DB::beginTransaction();
            //订单信息
            //            $orders = array();
            //            foreach($return_data['orderInfo'] as $k => $v){
            $order = new InsOrder();
            $order->order_no = $post_data['merchantOrderCode']; //内部订单号
            $order->union_order_code = $return_data['unionOrderCode']; //外部合并订单号
            $order->create_account_id = $this->request['account_id'];  //代理商account_id
            $order->api_from_uuid = ApiFrom::where('id', $this->bind->api_from_id)->first()->uuid;    //接口来源唯一码（接口类名称）
            $order->api_from_id = $this->bind->api_from_id;
            $order->ins_id = $this->bind->insurance_id;
            $order->p_code = $this->bind->p_code; //产品唯一码
            $order->bind_id = $this->bind->id;
            $order->total_premium = $return_data['totalPremium'];  //总保费
            $order->status = 'check_ing'; //待核保状态
            $order->buy_options = json_encode($buy_options, JSON_UNESCAPED_UNICODE);
            $order->by_stages_way = $post_data['policyInsuredList'][0]['insurePayWay'] . '年';
//            foreach($buy_options['quote_selected'] as $bk => $bv){
//                if(isset($bv['ty_key']) && ($bv['ty_key'] == 'ty_pay_way'))
//                    $order->by_stages_way = $bv['value'];
//            }
            $order->save();
            //投保人信息
            $policy = new Policy();
            $policy->ins_order_id = $order->id;
            $policy->union_order_code = $return_data['unionOrderCode'];
            $policy->name = $post_data['policyHolder']['holderName'];
            $policy->phone = $post_data['policyHolder']['holderPhone'];
            $policy->card_type = $post_data['policyHolder']['holderIdType'];
            $policy->card_id = $post_data['policyHolder']['holderIdNumber'];
            $policy->address = $post_data['policyHolder']['holderAddress'];
            $policy->email = $post_data['policyHolder']['holderEmail'];
            $policy->save();
            //被保人信息
            $insures = array();
            foreach($post_data['policyInsuredList'] as $k => $v){
                foreach($return_data['orderInfo'] as $rk => $rv){
                    if($rv['insuredName'] == $v['insuredName'])
                        $insures[$k]['out_order_no'] = $rv['orderCode'];
                    $insures[$k]['premium'] = $rv['premium'];
                    //                    $insures[$k]['coverage'] = $rv['coverage'];
                    $insures[$k]['p_code'] = $rv['productNo'];
                    unset($return_data['orderInfo'][$rk]);
                }
                $insures[$k]['ins_order_id'] = $order->id;
                $insures[$k]['union_order_code'] = $return_data['unionOrderCode'];
                $insures[$k]['name'] = $v['insuredName'];
                $insures[$k]['sex'] = $v['insuredSex'];
                $insures[$k]['phone'] = $v['insuredPhone'];
                $insures[$k]['card_type'] = $v['insuredIdType'];
                $insures[$k]['card_id'] = $v['insuredIdNumber'];
                $insures[$k]['relation'] = $v['holdInsRelation'];
                $insures[$k]['birthday'] = $v['insuredBirthday'];
                //$insures[]['address'] = $v['holderAddress'];
                //$insures[]['email'] = $v['holderEmail'];
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
            $res['total_premium'] = $return_data['totalPremium'];
            $res['union_order_code'] = $return_data['unionOrderCode'];
            $res['pay_way'] = [
                'pc'=>[
                    'cardPay' => '银行卡代扣'
                ],
                'mobile'=>[
                    'cardPay' => '银行卡代扣'
                ],
            ];
            $msg = ['data' => $res, 'code' => 200];
            return $msg;
        } catch (\Exception $e){
            DB::rollBack();
            $msg = ['data' => $e->getMessage(), 'code' => 444];
            return $msg;
        }
    }

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
                        ['name'=>'招商银行','code'=>308,'uuid'=>'CMB'],
                        ['name'=>'中国工商银行','code'=>102,'uuid'=>'ICBC'],
                        ['name'=>'中国农业银行','code'=>103,'uuid'=>'ABC'],
                        ['name'=>'中国建设银行','code'=>105,'uuid'=>'CCB'],
                        ['name'=>'中国银行','code'=>104,'uuid'=>'BOC'],
                        ['name'=>'中国交通银行','code'=>301,'uuid'=>'BCOM'],
                        ['name'=>'兴业银行','code'=>309,'uuid'=>'CIB'],
                        ['name'=>'中信银行','code'=>302,'uuid'=>'CITIC'],
                        ['name'=>'中国光大银行','code'=>303,'uuid'=>'CEB'],
                        ['name'=>'平安银行','code'=>783,'uuid'=>'PAB'],
                        ['name'=>'中国邮政储蓄银行','code'=>403,'uuid'=>'PSBC'],
                        ['name'=>'浦东发展银行','code'=>310,'uuid'=>'SPDB'],
                        ['name'=>'民生银行','code'=>305,'uuid'=>'CMBC'],
                        ['name'=>'广发银行','code'=>306,'uuid'=>'GDB'],
                        ['name'=>'华夏银行','code'=>304,'uuid'=>'HXB'],
                    ],
                    'url'=>''
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
        $restrict_genes = $restrictGeneRepository->findRestrictGenesRecursionByBindId($this->bind->id);
        $result['option']['selected_options'] = $restrictGeneRepository->findDefaultRestrictGenes($this->bind->id);
        $result['option']['selected_options'] = $this->onlyTyKey($result['option']['selected_options']);
        $result['option']['area'] = json_decode(file_get_contents(base_path('config/wk_area.json')), true);
        $jobs = json_decode(file_get_contents(base_path('config/wk_job.json')), true);
        $id = 0;
        foreach ($jobs as $key => &$job) {
            if (!isset($job['id'])) {
                $job['id'] = $id++;
            }
            foreach ($job['_child'] as $k => &$item) {
                if (!isset($item['id'])) {
                    $item['id'] = $id++;
                }
            }
        }
        $result['option']['jobs'] = $jobs;
        $data = $this->encapsulateDefaultData($result['option']['selected_options']);
        $data = $this->transformToApiWay($restrict_genes, $data);
        $tmp = $this->getDefaultQuotePrice($data);
        if ($tmp['code'] != 200) {
            return $tmp;
        }
        $result['option']['price'] = $tmp['data'];
        $result['option']['restrict_genes'] = $this->onlyTyKey($restrict_genes);
        $protect_items = $this->getProtectItems($insurance_id);
        foreach ($protect_items as &$protect_item) {
            $protect_item->sort = 0;
            $coverage_bs = explode(',', $protect_item->coverage_bs);
            $coverage_bs = array_shift($coverage_bs);
            $protect_item->defaultValue = $coverage_bs * $protect_item->coverage_jc . '元';
            unset($protect_item->coverage_bs, $protect_item->coverage_jc);
        }
        $result['option']['protect_items'] = $protect_items;
        return ['data'=> $result, 'code'=> 200];
    }

    protected function getProtectItems($insurance_id)
    {
        $result = [];

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
            ->join('duty as e', 'd.duty_id', '=', 'e.id')
            ->where('a.id', $insurance_id)
            ->select($select)
            ->get()
            ->toArray();

        return $result;
    }

    /**
     * 封装默认试算参数
     *
     * @param $restrict_genes
     * @return array
     */
    protected function encapsulateDefaultData($restrict_genes)
    {
        $data = [];
        foreach ($restrict_genes as $restrict_gene) {
            $data[$restrict_gene['ty_key']] = $restrict_gene['value'];
        }

        $data['productCode'] = $this->bind->p_code;
        $data['ty_birthday'] = date('Y-m-d');
        preg_match('/(\d+)/', $data['ty_pay_way'], $matches);
        $data['ty_pay_way'] = $matches[0];
        return $data;
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
     * 获得默认试算因子计算出的价格
     * @param $data
     * @return array
     */
    protected function getDefaultQuotePrice($data)
    {
        $msg = [];
        $input = $this->request->all();
        $data = json_encode($data);
        //加密 业务参数
        $data = base64_encode($this->rsaEncrypt('rsa_public_key_encrypt', $this->wk_public_key, $data));//加密后的数据
        //系统参数格式化、加签
        $data = $this->formatData($data);
        //请求
        $response = Curl::to(self::API_QUOTE)
            ->returnResponseObject()
            ->withData($data)
            ->withTimeout(60)
            ->post();

        $result = json_decode($response->content, true);
        if (!$result) {
            return ['data' => '算费无结果', 'code' => 400];
        }

        //结果验签
        $checkSign = $this->checkReturnSign($result);

        if ($checkSign == 1) {        //验签成功
            //业务请求结果判断
            if ($result['msgCode'] != 'SUCCESS') {
                return ['data'=>'选择算费参数', 'code'=> 200];
                //                $msg['data'] = $result['msgInfo'];
                //                $msg['code'] = 403;
                //                return $msg;
            }
            //解密获得返回结果
            $de_bizContent = json_decode($this->rsaDecrypt($result['bizContent']), true);
            return ['data'=>$de_bizContent['rateMoney'] * 100, 'code'=>200];
        } else {
            //            return '算费无结果';
            $msg['data'] = "返回结果验签失败";
            $msg['code'] = 400;
            return $msg;
        }
    }

    /**
     * 格式化成一维数组
     * 关联数组，key为内部的key
     *
     * @param $data
     * @return mixed
     */
    protected function formatToKeyValue($data)
    {
        foreach ($data as $item) {
            if (!empty($item['value'])) {
                preg_match('/(\d+-?)+/', $item['value'], $matches);
                $data[$item['key']] = $matches[0];
            } else {
                $data[$item['key']] = $item['value'];
            }
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

    /**
     * 转换格式，用于调用接口
     *
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
     * 转换格式，用于前台显示
     *
     * @param $data
     * @return mixed
     */
    protected function transformToShow($data)
    {
        foreach ($data as &$item) {
            $item['ty_key'] = $item['key'];
            unset($item['key']);
        }

        return $data;
    }

    /**
     * 业务参数格式化
     * @param $biz_content
     * @return array|string
     */
    protected function formatData($biz_content)
    {
        //通用参数
        $data = [
            'appKey' => '149688819951752069',
            //            'appKey' => '148351715840909859',
            'charset' => 'UTF-8',
            'signType' => 'RSA',
            'version' => '1.0.0',
            'timestamp'=> date('YmdHis'),
            'format' => 'JSON',
            'bizContent'=>$biz_content,
            'serviceName'=> 'SkyEye',
        ];
        //通用参数 排序 签名
        $data = $this->sortToSignData($data);
        $sign = $this->rsa_sign($data); //签名
        $data.= '&sign='.$sign;
        $data = str_replace('+', '%2B', $data);     //格式？
        //        $post_data = str_replace('&','%26',$post_data);
        //        $post_data = str_replace('+','%2B',$post_data);
        //        $post_data = str_replace('%','%25',$post_data);
        return $data;
    }

    /**
     * 返回结果验签
     * @param $data
     * @return bool
     */
    protected function checkReturnSign($data)
    {
        $data_for_check_sign = $data;
        unset($data_for_check_sign['sign']);
        $data_for_check = $this->sortToSignData($data_for_check_sign);
        $checkSign = $this->rsaCheckSign($data_for_check, $data['sign']);
        return $checkSign;
    }

    protected function logError($data, $error_msg)
    {
        $log = "\n\r[Error][" . Carbon::now() . "]\n";
        $log .= "Callback Data: " . json_encode($data) . "\n";
        $log .= "Error Message: " . $error_msg;
        file_put_contents('../log/callback.log', $log, FILE_APPEND);
    }

    protected function logSuccess($data)
    {
        $log = "[" . Carbon::now() . "] " . json_encode($data);
        file_put_contents('../log/callback.log', $log, FILE_APPEND);
    }

    //==============================================加密、解密 加签、验证=============================================
    /**
     * 将数据进行排序后再进行拼接字符串
     * @param    $data    array     待加签的数据
     * @param $data
     * @return string
     */
    private function sortToSignData($data){
        ksort($data);
        $str = '';
        foreach($data as $k=>$v){
            $str .= $k.'='.$v.'&';
        }
        $str = trim($str,'&');
        return $str;
    }

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
    public function rsa_sign($data){
        $res = openssl_get_privatekey($this->private_key);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        return base64_encode($sign);
    }

    private function rsaCheckSign($data, $sign){
        $res = openssl_get_publickey($this->wk_public_key);
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