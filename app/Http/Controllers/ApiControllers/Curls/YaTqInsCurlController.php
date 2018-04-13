<?php
/**
 * Created by PhpStorm.
 * User: tmingyy
 * Date: 2018/02/07
 * Time: 15:37
 */

namespace App\Http\Controllers\ApiControllers\Curls;

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
use QrCode;


class YaTqInsCurlController
{


    protected $app_id;
    protected $app_secret;
    protected $request;
    protected $sign_help;
    protected $api_m_info;
    protected $open_id;
    protected $access_token;
    protected $app_url;

    //出行无忧
    protected $product_codes_cxwy = '10012001002001';
    //e享百万医疗
    protected $product_codes_exbw = '20173008001A0000';
    //e享运动
    protected $product_codes_exyd = '10011005001000';

    public function __construct(Request $request)
    {
        //0正式 1测试

        //e享运动和出行无忧暂时生产环境未通过
        $this->test = 0;

        $this->app_id = $this->test ? 'AFFD7FD919C0DC3B8C1FD94CC541874E' : '54AA13212C87802017595430FDB231D8';          //渠道代码
        $this->app_secret  = $this->test ? 'F46316225C5114EB3673413125140A93' : 'E6C748F57E71592EA67CA4B0DAC90C16';     //密钥
        $this->app_url  = $this->test ? 'http://183.60.22.143/sns/' : 'http://open.1an.com/sns/';                       //url路径

        $this->sign_help = new RsaSignHelp();
        $this->request = $request;
        $this->request_url = 'https://antx11.95303.com/com.isoftstone.iics.bizsupport.epartner/amt/processOrder';
        if(!preg_match("/call_back/",$this->request->url())){
            $this->origin_data = $this->decodeOriginData();   //解签获得源数据
            $this->api_repository = new InsuranceApiFromRepository();
            $insurance_id = isset($this->origin_data['ty_product_id']) ? $this->origin_data['ty_product_id'] : 0;    //天眼产品ID
            $private_p_code = isset($this->origin_data['private_p_code']) ? $this->origin_data['private_p_code'] : 0;

            if($insurance_id){
                $this->api_m_info = $this->api_repository->getApiStatusOn($insurance_id);    //获取当前正在启用的API来源信息
            } else {
                $this->api_m_info = $this->api_repository->getApiByPrivatePCode($private_p_code);    //获取产品唯一码对应的API来源信息
            }
            if(empty($this->api_m_info))
                return ['data'=> 'product not exist', 'code'=> 400];
            $this->p_code = $this->api_m_info->p_code;
        }
    }

    /**
     * 产品详情
     * @return array
     */
    public function getApiOption()
    {
        $insuranceAttributesRepository = new InsuranceAttributesRepository();
        $restrictGeneRepository = new RestrictGeneRepository();
        $repository = new InsuranceAttributesRepository();

        $input = $this->decodeOriginData();   //解签获得源数据

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

//        $tmp_data = $repository->inToOut($this->api_m_info->id, $result['option']['selected_options']);    //转换外部键值对
//        print_r(json_encode($result));
        dump($this->formatQuote($restrict_genes));

        die;


        $default_quote = $this->formatQuote($result['option']['selected_options']);

        print_r(json_encode($result));
        die;
        //获得默认保费及保障内容
        $result['option']['price'] = $default_quote->price;
        //保障内容
        $result['option']['protect_items'] = $default_quote->protect_items;
        return ['data'=> $result, 'code'=> 200];
    }

    /**
     * 本地算费
     * @return array
     */
    public function quote()
    {
        $msg = [];
        $original_data = $this->origin_data;
//        $restrict_genes = (new RestrictGeneRepository())->findRestrictGenesRecursionByBindId($this->api_m_info->id);

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


//        print_r($original_data);die;
        //本地数据库算费查询
        switch ($this->p_code)
        {
            case $this->product_codes_cxwy:
                $quote = DB::table('tariff_ya_cxwy')->where($api_data)->first();
                break;
            case $this->product_codes_exbw:
                $quote = DB::table('tariff_ya_exbw')->where($api_data)->first();
                break;
            case $this->product_codes_exyd:
                $quote = DB::table('tariff_ya_exyd')->where($api_data)->first();
                break;
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
        $msg['data']['protect_items'] = $items;
        $msg['code'] = 200;
        return $msg;
    }

    /**
     * 投保接口
     * @return array
     */
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
        switch ($this->p_code)
        {
            case $this->product_codes_cxwy:
                $quote = DB::table('tariff_ya_cxwy')->where($api_data)->first();
                break;
            case $this->product_codes_exbw:
                $quote = DB::table('tariff_ya_exbw')->where($api_data)->first();
                break;
            case $this->product_codes_exyd:
                $quote = DB::table('tariff_ya_exyd')->where($api_data)->first();
                break;
        }


        $ty_duration_period_value = $quote->ty_duration_period_value;

        $ins_start_time = date("Y-m-d H:i:s", strtotime($tmp_data['ty_base']['startDate']));   //起保日期

        $ins_end_time =  date("Y-m-d H:i:s", strtotime($ins_start_time." +".$quote->ty_duration_period_time));
        $ins_end_time = date("Y-m-d H:i:s", strtotime($ins_end_time." -1 seconds"));   //结束日期

        $productCode = substr($quote->agrt_code, 0,-3);   //外部产品码
        $premium = $quote->price;   //保费价格
        $num = 1; //默认投保数量

        $insurance = Insurance::where('id', $this->api_m_info->insurance_id)->with(['clauses'])->first();

        $productName = $insurance->name;    //产品名称

        $orderMain = [];

        //订单主体数据
        $orderMain['interfaceCode'] = 'CreateOrder';   //接口标识
        $orderMain['requestTime'] =  date('Y-m-d H:i:s',time());
        $orderMain['dataSource'] = 'O-TYKJ';    //数据来源
        $orderMain['agrtCode'] = $quote->agrt_code;            //外部产品协议码
        $orderMain['outBusinessCode'] = date('YmdHis'). rand(10, 99) . rand(100, 999); //内部订单号

        $orderData = [];
        $orderData['startDate'] = $ins_start_time;          //起保日期
        $orderData['endDate'] = $ins_end_time;              //终保日期

        $orderData['uwCount'] = $num;                       //投保份数
        $orderData['premium'] = ($premium * $num) / 100;    //总保费
        $orderData['projectCode'] = $productCode;           //产品方案码
        //投保人 customerList
        $tmp_data['ty_toubaoren']['customerType'] = 1;
        $tmp_data['ty_toubaoren']['customerFlag'] = 1;
        $orderData['customerList'][] = $tmp_data['ty_toubaoren'];

        //标的主体信息
        $itemAcciData = [];
        $itemAcciData['quantity'] = 1;  //人数
        $itemAcciData['nominativeInd'] = 1;  //记名标识
        $itemAcciData['occupationCode'] = '0000000';  //职业

        //被保人
        $acciInsured = [];
        $acciInsured = $tmp_data['ty_beibaoren'][0];  //被保人
        $acciInsured['customerFlag'] = 2;//默认被保险人

        //被保人信息
        $itemAcciData['acciInsuredList'][] = $acciInsured;

        //标的全部信息
        $orderData['itemAcciList'][] = $itemAcciData;

        //订单信息
        $orderMain['data']['createOrderReq']['orderList'][] =$orderData;

        //刷新鉴权
        $this->getAuthentication();

        $url = $this->test ? $this->app_url . 'dy-order-service-dev?access_token=' . $this->access_token . '&open_id=' . $this->open_id : $this->app_url . 'dy-order-service?access_token=' . $this->access_token . '&open_id=' . $this->open_id;
        $response = Curl::to($url)
            ->returnResponseObject()
            ->withData(json_encode($orderMain))
            ->withTimeout(60)
            ->withHeader("Content-Type: application/json;charset=UTF-8")
            ->post();

        if($response->status == 200){

            $formatter = json_decode(urldecode($response->content), true);

            if($formatter['code'] == '0000'){

                $buy_options = [];
                $buy_options['insurance_attributes'] = $input['insurance_attributes'];
                $buy_options['quote_selected'] = $quote_selected;

                $input['insurance_num'] = $num;
                //写入订单号  保费  起保期限
                $formatter['data']['createOrderResp']['ty_order_no'] = $orderMain['outBusinessCode'];
                $formatter['data']['createOrderResp']['totalPremium'] = $premium * $num; //单位 分
                $formatter['data']['createOrderResp']['start_time'] = $ins_start_time;
                $formatter['data']['createOrderResp']['end_time'] = $ins_end_time;
                return $this->addOrder($input, $formatter, $buy_options);
            } else {
//                dd($response->content);
                LogHelper::logError($orderMain, $formatter['message'], 'ya', 'buy_ins_error');
                $msg = [];
                $msg['data'] = $formatter['message'];
                $msg['code'] = 403;

                return $msg;
            }
        } else {
//            dd($response);
            LogHelper::logError($orderMain, $response->content, 'ya', 'buy_ins_error_not_200');
            $msg['data'] = '请求异常';
            $msg['code'] = $response->status;
            return $msg;
        }
    }

    /**
     * 本地写入订单数据
     * @param $original_data
     * @param $return_data
     * @param $buy_options
     * @return array
     */
    public function addOrder($original_data, $return_data, $buy_options)
    {
        try {
            DB::beginTransaction();
            //订单信息
            $order = new InsOrder();
            $order->order_no = $return_data['data']['createOrderResp']['ty_order_no'];
            $order->union_order_code = $return_data['data']['createOrderResp']['orderCode']; //外部合并订单号
            $order->pay_code = $return_data['data']['createOrderResp']['orderExt']; //订单附加信息
            $order->create_account_id = $this->request['account_id'];  //代理商account_id
            $order->api_from_uuid = ApiFrom::where('id', $this->api_m_info->api_from_id)->first()->uuid;    //接口来源唯一码（接口类名称）
            $order->api_from_id = $this->api_m_info->api_from_id;
            $order->ins_id = $this->api_m_info->insurance_id;
            $order->p_code = $this->api_m_info->p_code; //产品唯一码
            $order->bind_id = $this->api_m_info->id;
            $order->total_premium = $return_data['data']['createOrderResp']['totalPremium'];  //总保费
            $order->status = 'check_ing'; //待核保状态
            $order->buy_options = json_encode($buy_options, JSON_UNESCAPED_UNICODE);
            $order->by_stages_way = '0年';
            $order->start_time = $return_data['data']['createOrderResp']['start_time'];   //生效日期
            $order->end_time = $return_data['data']['createOrderResp']['end_time'];  //结束日期
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
                $insures[$k]['premium'] = $return_data['data']['createOrderResp']['totalPremium'] / $original_data['insurance_num'];    //保费
                $insures[$k]['p_code'] = $this->api_m_info->p_code;    //外部产品码
                $insures[$k]['name'] = $v['ty_beibaoren_name'];
//                    $insures[$k]['sex'] = $v['ty_beibaoren_sex'];
//                    $insures[$k]['phone'] = $v['ty_beibaoren_phone'];
                $insures[$k]['card_type'] = $v['ty_beibaoren_id_type'];
                $insures[$k]['card_id'] = $v['ty_beibaoren_id_number'];
                $insures[$k]['relation'] = 31;
//                    $insures[$k]['birthday'] = $v['ty_beibaoren_birthday'];
                $insures[$k]['ins_start_time'] = $return_data['data']['createOrderResp']['start_time'];     //生效日期
                $insures[$k]['ins_end_time'] = $return_data['data']['createOrderResp']['end_time'];         //结束日期
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
            $res['total_premium'] = $return_data['data']['createOrderResp']['totalPremium'];    //保费总额
            $res['union_order_code'] = $return_data['data']['createOrderResp']['orderCode'];    //外部订单号
            $res['pay_code'] = $return_data['data']['createOrderResp']['orderExt'];             //订单附加信息 暂存流水号
            $res['pay_way'] = [
                'pc'=>[
//                    'aliPay'=> '支付宝支付',
                    'wechatPay'=> '微信支付'
                ],
                'mobile'=>[
                    'aliPay'=> '支付宝支付',
//                    'wechatPay'=> '微信支付'
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

        $data = array();
        $pay_type = '';

        switch($input['is_phone']){
            case 0: //pc端
                $data['interfaceCode'] = 'initOrderPay';    //接口标识
                switch($input['pay_way']){
                    case 'aliPay': //支付宝
                        $pay_type = '03';
                        break;
                    case 'wechatPay': //微信
                        $pay_type = '06';
                        $data['data']['tradeType'] = 'NATIVE';
                        $data['data']['spBillCreateIp'] = $_SERVER['REMOTE_ADDR'];
                        break;
                }
                break;

            case 1:
                $data['interfaceCode'] = 'yaH5Pay';    //接口标识
                switch($input['pay_way']){
                    case 'aliPay': //支付宝
                        $pay_type = '02';
                        break;
                    case 'wechatPay': //微信
                        $pay_type = '01';
                        break;
                }
                break;
        }

        //根据算费获取配置的协议号
        $quote = $this->formatQuote(json_decode($ins_order->buy_options, true)['quote_selected']);

        //刷新鉴权
        $this->getAuthentication();
        $data['requestTime'] = date('Y-m-d H:i:s',time());  //请求时间
        $data['dataSource'] = 'O-TYKJ';  //数据来源
        $data['agrtCode'] = $quote->agrt_code ?? $ins_order->p_code;  //协议号
        //$data['data']['orderCode'] = $ins_order->union_order_code;  //外部订单号
        $data['data']['orderCode'] = $ins_order->union_order_code;  //外部订单号
        $data['data']['orderExt'] = $ins_order->pay_code;;  //订单附加信息  暂存流水号
        $data['data']['payWay'] = $pay_type;  //支付方式
        $data['data']['redirectUrl'] = env('APP_URL') . '/api/ins/ya/call_back';  //回调

        $url = $this->test ? $this->app_url . 'pay-service-dev?access_token='. $this->access_token .'&open_id=' . $this->open_id : $this->app_url . 'pay-service?access_token='. $this->access_token .'&open_id=' . $this->open_id;

        $response = Curl::to($url)
            ->returnResponseObject()
            ->withData(json_encode($data))
            ->withTimeout(60)
            ->withHeader("Content-Type: application/json;charset=UTF-8")
            ->post();

        $content = json_decode($response->content, true);

        if($response->status == 200 && $content['code'] == '0000'){
            //神奇的另一种错误返回格式
            if($pay_type == '06'){
                if($content['data']['passFlag'] == 1) return ['code'=>400, 'data'=> $content['data']['message']];
            }

            //微信扫码支付 生成二维码
            if($pay_type == '06'){
                $wx_order_info = json_decode($content['data']['message'] ,true);
                //生成目录
                $dir = date('Y-m-d', time());
                if (!file_exists(public_path('upload/'.$dir))) mkdir (public_path('upload/'.$dir),0777,true);

                //生成二维码
                $img_url = $dir.'/'.time().rand(1000,9999).'.png';
                QrCode::format('png')->size(400)->generate($wx_order_info['codeUrl'], public_path('upload/'.$img_url));
                $pay_url = env('APP_URL').'/upload/'.$img_url;

                $res_data = ['order_code'=>$ins_order->union_order_code, 'pay_way_data'=> ['url'=>$pay_url]];

            }else if ($pay_type == '01'){
                //微信h5支付

                $pay_url = urldecode($content['data']['payUrl']);
                $res_data_arr = ['order_code'=>$ins_order->union_order_code, 'pay_way_data'=> ['url'=>$pay_url]];
                $res_data = str_replace("\\/", "/", json_encode($res_data_arr));

                $msg['data'] = '暂未开通本项支付';
                $msg['code'] = 400;
                return $msg;

            }else if($pay_type == '03'){
//                //支付宝pc支付
//                $ali_order_info = json_decode($content['data']['message'], true);
//
//
//                $ali_service_url = $ali_order_info['serviceUrl'].'?_input_charset=UTF-8';
//
//                $order_data['sign'] = $ali_order_info['parameter']['sign'];
//                $order_data['body'] = $ali_order_info['parameter']['body'];
//                $order_data['subject'] = $ali_order_info['parameter']['subject'];
//                $order_data['out_trade_no'] = $ali_order_info['parameter']['out_trade_no'];
//                $order_data['return_url'] = $ali_order_info['parameter']['return_url'];
//                $order_data['it_b_pay'] = $ali_order_info['parameter']['it_b_pay'];
//                $order_data['extra_common_param'] = $ali_order_info['parameter']['extra_common_param'];
//                $order_data['total_fee'] = $ali_order_info['parameter']['total_fee'];
//                $order_data['service'] = $ali_order_info['parameter']['service'];
//                $order_data['paymethod'] = $ali_order_info['parameter']['paymethod'];
//                $order_data['partner'] = $ali_order_info['parameter']['partner'];
//                $order_data['seller_id'] = $ali_order_info['parameter']['seller_id'];
//                $order_data['payment_type'] = $ali_order_info['parameter']['payment_type'];
//                $order_data['sign_type'] = $ali_order_info['parameter']['sign_type'];
//                $order_data['notify_url'] = $ali_order_info['parameter']['notify_url'];
//
//
//                //$ali_service_url .= '&biz_content='.json_encode($order_data);
//
//                $data_url = '';
//                foreach ($order_data as $key=>$val){
//                    $data_url .= '&'.$key.'='.$val;
//                }
//
//                print_r($ali_service_url.$data_url);
//
//                $response = Curl::to($ali_service_url)
//                    ->returnResponseObject()
//                    ->withData($order_data)
//                    ->withTimeout(60)
//                    //->withHeader("Content-Type: application/json;charset=GBK")
//                    ->post();
//
//                print_r($response);
//                die;


                $msg['data'] = '暂未开通本项支付';
                $msg['code'] = 400;
                return $msg;

            }else if($pay_type == '02'){
                //支付宝 h5
                $pay_url = urldecode($content['data']['payUrl']);

                $res_data_arr = ['order_code'=>$ins_order->union_order_code, 'pay_way_data'=> ['url'=>$pay_url]];

                $res_data = str_replace("\\/", "/", json_encode($res_data_arr));
                //print_r($res_data_arr);die;
            }

            //成功
            return ['data'=>$res_data, 'code'=> 200];

        } else {

            LogHelper::logError($data, json_encode($response), 'ya', 'getPayWayInfo');
            $msg['data'] = $content['message'];
            $msg['code'] = 400;
            return $msg;
        }

    }

    /**
     * 支付回调
     * @return string
     */
    public function payCallBack(){

        //框架
        $input = $this->request->all();

        if(empty($input)){
            $data = file_get_contents('php://input', 'r');
            $input = json_decode($data, true);
        }


        if($input['payResult'] == 'Y'){
            LogHelper::logSuccess($input, 'ya', 'pay_call_back');

            $order = InsOrder::where(['union_order_code'=> $input['orderCode'], 'api_from_uuid'=> 'Ya'])->first();
            $user = User::where('account_id', $order->create_account_id)->first();
            $order->pay_code = $input['orderExt'];
            $order->status = 'pay_end';
            $order->policy_status = 6;
            $order->pay_time = time();
            $order->save();

            //写入保单号
            Insure::where('ins_order_id', $order->id)->update([
                'status'=> 'pay_end',
                'ins_policy_code'=> $input['policyNo'],
                'e_policy_url'=> $input['epolicyUrl'],
                'policy_status'=> 3,
            ]);

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
                    'union_order_code' => $input['orderCode'],
                    'by_stages_way' => $order->by_stages_way,
                    'error_message' => '',
                ]
            ];

            $response = Curl::to($user->call_back_url . '/ins/call_back')
                ->returnResponseObject()
                ->withData($data)
                ->asJson()
                ->withTimeout(60)
                ->post();
            LogHelper::logSuccess($response,'ya',  'ty/ins/call_back');
            return 'SUCCESS';
        }else{
            return  'ERROR';
        }
    }

    /**
     * 出单接口
     * @return array
     */
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


        $insure_data = Insure::where('union_order_code', $input['union_order_code'])->first();

        //易安暂不提供保单查询接口，保单状态基于本地判断
        $status = 0;
        if(time() >= strtotime($insure_data->ins_start_time) && time() < strtotime($insure_data->ins_end_time)){
            Insure::where('out_order_no', $ins_order->union_order_code)->update(['policy_status' => 6]);
            $status = 1;
        }

        $return = array();
        //返回结果封装 todo
        $return['status'] = $status;    //出单状态 0：未生效 1：已生效 2：退保中 3：已退保
        $return['policy_order_code'] = $insure_data->ins_policy_code;   //被保人保单号
        $return['private_p_code'] = $input['private_p_code'];   //产品码
        $return['order_code'] = $input['order_code'];   //被保人单号
        $return['e_policy_url'] =$insure_data->e_policy_url;   //电子保单地址
        $return['start_time'] = $insure_data->ins_start_time;
        $return['end_time'] = $insure_data->ins_end_time;
        return ['data'=> $return, 'code'=> 200];

    }

    /**
     * 解签数据源
     * @return mixed
     */
    protected function decodeOriginData()
    {
        $input = $this->request->all();
        //业务参数 解析出源数据json字符串
        $original_data_array = $this->sign_help->tyDecodeOriginData($input['biz_content']);
        return $original_data_array;
    }

    /**
     * 获取鉴权信息
     */
    protected function getAuthentication(){
        $response = Curl::to($this->app_url .'oauth2/authorize?app_id='.$this->app_id.'&app_secret='.$this->app_secret.'&code=1anOpcode&grant_type=authorization_code')
            ->returnResponseObject()
            ->withTimeout(60)
            ->get();
        $data = json_decode($response->content, true);

        if($data['code'] == '0000'){
            //查看 access_token 是否过期
            if($data['data']['expires_in'] > 0){
                $this->access_token = $data['data']['access_token'];
                $this->open_id = $data['data']['open_id'];
            }else{
                //刷新 access_token
                $response = Curl::to($this->app_url .'oauth2/token_refresh?app_id='.$this->app_id.'&app_secret='.$this->app_secret.'&grant_type=refresh_token&refresh_token='.$data['data']['refresh_token'])
                    ->returnResponseObject()
                    ->withTimeout(60)
                    ->get();
                $data = json_decode($response->content, true);
                $this->access_token = $data['data']['access_token'];
                $this->open_id = $data['data']['open_id'];


            }

            return $data;
        }
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

    protected function getArea(){
        //配置开放地区
        $data = [
            "name"=> "山东",
		    "value"=> "770"
        ];
    }

    /**
     * 获取默认算费费信息
     * @param $only_ty_key_array
     * @return mixed
     */
    protected function formatQuote($genes_data)
    {

   'type				=	weixin
    保费						current_price		=	10
    用户码						_token				=	C9StZhZhINqUJSbFTm8z9E265XLrXAob7WImQCRf';




        dump($genes_data);

        $api_data = [];

        foreach ($genes_data as $item) {
            if (isset($item['defaultValue'])) {
                foreach ($item['values'] as $val){
                    if($val['name'] == $item['defaultValue']){
                        //匹配 T+ T-
                        $search = '/^[\+|\-]{1}\d{1,5}\s+/';
                        if(preg_match($search,$val['value'])){
                            $api_data[$item['key']] = date('Y-m-d' , strtotime($val['value']));
                        }else{
                            $api_data[$item['key']] = $val['value'];
                        }
                    }
                }
            }
        }

        dump($api_data);
        die;

        $data = [];
        $data['title'] = '';//保险名称
        $data['city_code'] = '';//城市编码
        $data['city_name'] = '';//城市名称
//        $data['station_id'] = ;//气象站点
        $data['weather_factor_id'] = '';//天气因子
        $data['weather_attr_id'] = '';//阈值
        $data['model'] = '≥';//条件
        $data['start_time'] = '';//保障开始时间
        $data['end_time'] = '';//保障结束时间
        $data['effective_day'] = '';//保障天数

        $response = Curl::to('http://diy.weatherplus.com.cn/product/evaluate2')
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(10)
            ->post();



    }

    //算费属性名称 转 外部
    protected function ty_option_to_out(&$ty_options, $for='quote')
    {
//        dd($ty_options);
        $out_keys = $this->genes_map_to_out();
        foreach($ty_options as $k => &$v){
            if(isset($v['key'])){
                if(isset($out_keys[$v['key']])){
                    $v['key'] = $out_keys[$v['key']];
                }
            }
            if($for != 'quote'){
                if(isset($v['ty_key'])){
                    if(isset($out_keys[$v['ty_key']])){
                        $v['key'] = $out_keys[$v['ty_key']];
                    } else {
                        $v['key'] = $v['ty_key'];
                    }
                    unset($v['ty_key']);
                }
            }
        }
//        dd($ty_options);
    }

    protected function genes_map_to_out()
    {
        $map = [
            'ty_age'=>'insurantDate', //年龄
            'ty_jop'=>'insurantJob',  //职业
            'ty_duration_period_value'=> 'insurantDateLimit', //保障期限
            'ty_buy_count'=> 'buyCount',  //购买份数
            'ty_pay_way'=> 'insureAgeLimit',  //缴费分期方式
        ];
        return $map;
    }



}