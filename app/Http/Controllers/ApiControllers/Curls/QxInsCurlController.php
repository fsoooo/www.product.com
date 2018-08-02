<?php

namespace App\Http\Controllers\ApiControllers\Curls;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use App\Helper\LogHelper;
use Ixudra\Curl\Facades\Curl;
use App\Models\Insurance;
use App\Models\InsOrder;
use App\Models\Insure;
use App\Models\User;
use App\Models\ApiFrom;
use App\Models\Policy;
use App\Models\InsApiBrokerage;
use App\Models\OrderFinance;
use Validator, DB, Image, Schema;
use App\Repositories\InsuranceApiFromRepository;


class QxInsCurlController
{
//    protected $api_from;
    protected $sign_help;
    protected $app_key;
    protected $app_sign_key;
    protected $api_repository;
    protected $p_code;
    protected $origin_data;
    protected $api_m_info;
    protected $need_area;
    protected $url, $test;

    public function __construct(Request $request, $insurance_api_from=null)
    {
        $this->request = $request;
        $this->sign_help = new RsaSignHelp();
        $this->test = 0; //0 生产环境  1 测试环境
        $this->app_key = $this->test ? '1006689' : '1006689';
        $this->app_sign_key = $this->test ? 'TNYzk0NDAzZGZlNWI1006689' : 'ONM2FhYWU4ODVjZWZ1006689';
        $this->url = $this->test ? 'http://tuneapi.qixin18.com/api' : 'http://api.qixin18.com/api';
        $this->need_area = false;
        if(!preg_match("/call_back/",$this->request->url())){
            $this->origin_data = $this->decodeOriginData();   //解签获得源数据
//            $this->api_repository = new InsuranceApiFromRepository();
//            $insurance_id = isset($this->origin_data['ty_product_id']) ? $this->origin_data['ty_product_id'] : 0;    //天眼产品ID
//            $private_p_code = isset($this->origin_data['private_p_code']) ? $this->origin_data['private_p_code'] : 0;
//
//            if($insurance_id){
//                $this->api_m_info = $this->api_repository->getApiStatusOn($insurance_id);    //获取当前正在启用的API来源信息
//            } else {
//                $this->api_m_info = $this->api_repository->getApiByPrivatePCode($private_p_code);    //获取产品唯一码对应的API来源信息
//            }
//            if(empty($this->api_m_info))
//                return ['data'=> 'product not exist', 'code'=> 400];
            $this->api_m_info = $insurance_api_from;
            $this->p_code = $this->api_m_info->p_code;
        }


    }

    public function decodeOriginData()
    {
        $input = $this->request->all();
//        LogHelper::logError($input,'post_data', 'quote');
        //业务参数 解析出源数据json字符串
        $original_data_array = $this->sign_help->tyDecodeOriginData($input['biz_content']);
//        LogHelper::logError($original_data_array,'tyDecodeOriginData', 'quote');
        return $original_data_array;
    }

    /**
     * 产品列表
     * @return array
     */
//    public function ins()
//    {
////        $input = $this->decodeOriginData();
//
//        $request_url = "http://tuneapi.qixin18.com/api/productList?";
//        $data = [
//            'transNo' => rand(1000000, 9999999),    //交易流水号
//            'partnerId' => $this->app_key   //开发者身份标识
//        ];
//        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data));
//        $response = Curl::to($request_url)
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();
//        dump($response);
//        exit;
//    }

    /**
     * 产品详情
     */
    public function getApiOption()
    {
        $cache_name = 'ins_info_' . $this->p_code;

        if(!\Cache::has($cache_name)){
            $p_code = $this->p_code;
            $data = [
                'transNo' => rand(10000000, 99999999),    //交易流水号
                'caseCode' => $p_code, //方案代码
                'partnerId' => $this->app_key,  //开发者身份标识
                'platformType' => 0 //平台标识 0：PC 1：H5
            ];
            $request_url = $this->url ."/productDetail?";
            $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data));
            $response = Curl::to($request_url)
                ->returnResponseObject()
                ->withData($data)
                ->asJson()
                ->withTimeout(60)
                ->post();
//            dd($response);exit;
            //获取详情失败
            if($response->status != 200)
                return ['data'=> 'request info error', 'code'=> 400];
            if($response->content->respCode != 0){
                LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'insInfo');
                return ['data'=> $response->content->respMsg, 'code'=> 400];
            }

            //获取默认算费结果
            $response_default_quote = $this->default_quote();
            if($response_default_quote['code'] != 200)  //默认算费失败
                return ['data'=>$response_default_quote['data'], 'code'=>$response_default_quote['code']];
            $response->content->data->default_quote = $response_default_quote['data'];    //默认算费信息

            //获取投保属性
            $response_ins_attr = $this->insAttr();
            if($response_ins_attr['code'] != 200)   //获取投保属性失败
                return ['data'=>$response_ins_attr['data'], 'code'=>$response_ins_attr['code']];
            $response->content->data->attr = $response_ins_attr['data'];    //投保属性信息

            $res = $this->formatApiOption($response->content->data);
            $res['switch']['health_verify'] = $this->healthVerify();

            //获取职业信息
            $ins_job = $this->insJob();
            if($ins_job['code'] != 200) //获取职业信息失败
                return ['data'=>$ins_job['data'], 'code'=>$ins_job['code']];

            //地址信息
            $ins_address = ['data'=>array()];
            if($this->need_area){
                $ins_address = $this->address();
                if($ins_address['code'] != 200)
                    return ['data'=>$ins_address['data'], 'code'=>$ins_address['code']];
            }

            $res['option']['jobs'] = $ins_job['data'];
            $res['option']['area'] = $ins_address['data'];

            //存入缓存
            \Cache::put($cache_name, serialize($res), 43200);
        }
        $res = unserialize(\Cache::get($cache_name));
        return ['data'=> json_encode($res, JSON_UNESCAPED_UNICODE), 'code'=>200];
    }


    protected function formatApiOption($return_data)
    {
        $data = array();
        $data['ty_product_id'] = $this->api_m_info->insurance_id;
        $data['private_p_code'] = $this->api_m_info->private_p_code;
        $data['bind_id'] = $this->api_m_info->id;
        $data['option']['insurance_attributes'] = $return_data->attr['attrModules'];    //投保属性
        $this->format_insurance_attributes_to_ty($data['option']['insurance_attributes']);
//        $data['firstDate'] = isset($return_data->firstDate) ? $return_data->firstDate : 1;
//        $data['latestDate'] = isset($return_data->latestDate) ? $return_data->latestDate : 20;
		if($this->p_code == 'QX000000100393'){
			$data['option']['insurance_attributes'][0]['productAttributes'][] = [
				"name"=> "投保人性别",
				"type"=> 0,
				"required"=>1,
				"attributeValues"=> [
					["value"=> "男","ty_value"=> 1],
					["value"=> "女","ty_value"=> 0],
				],
				"ty_key"=> "ty_toubaoren_sex"
			];
			$data['option']['insurance_attributes'][1]['productAttributes'][] =[
				"name"=> "被保人性别",
					"type"=> 0,
					"required"=>1,
					"attributeValues"=> [
						["value"=> "男","ty_value"=> 1],
						["value"=> "女","ty_value"=> 0],
					],
					"ty_key"=> "ty_beibaoren_sex"
			];
		}

        //选项key转为内部key
        $this->out_option_to_ty($return_data->restrictGenes);

        $data['option']['restrict_genes'] = $return_data->restrictGenes;    //试算因子及取值范围
        $data['option']['price'] = $return_data->default_quote->price;    //默认试算价格
        //默认已选参数 格式 {"protectItemId":"767","value":"5万元","sort":-1},{"protectItemId":"756","value":"10万元","sort":0}
        //惠泽的 格式为 {"productId":1644,"productPlanId":1934,"genes":[ {"protectItemId":"767","value":"5万元","sort":-1},.... ]}
        $selected = $return_data->default_quote->priceArgs;
        // 选中项的key转换为内部的key
        $this->out_option_to_ty($selected->genes, 'selected');
        $data['option']['selected_options'] = $selected;
        $data['option']['protect_items'] = $return_data->default_quote->protectItems;

        return $data;
    }

    /**
     * 投保属性
     */
    public function insAttr()
    {
        $p_code = $this->p_code;

        $data = [
            'transNo' => rand(1000000, 9999999),    //交易流水号
            'caseCode' => $p_code, //方案代码
            'partnerId' => $this->app_key,  //开发者身份标识
        ];
        $request_url = $this->url . "/productInsuredAttr?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data));
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true) //json请求 返回转数组
            ->withTimeout(60)
            ->post();

        //失败返回
        if($response->status != 200)
            return ['data'=> 'request attr error', 'code'=> 400];
        if($response->content['respCode'] != 0){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'insAttr');
//            return ['data'=> $response->content['respMsg'], 'code'=> 400];
            $err = $this->replaceErr($response->content['respMsg']);
            return ['data'=> $err, 'code'=> 400];
        }

        return ['data'=>$response->content['data']['insureAttribute'], 'code'=>200];    //投保属性信息
    }

    /**
     * 默认算费
     * @return array
     */
    public function default_quote()
    {
        $p_code = $this->p_code;

        $data = [
            'transNo' => rand(1000000, 9999999),    //交易流水号
            'partnerId' => $this->app_key,  //开发者身份标识
            'caseCode' => $p_code, //方案代码
        ];
        $request_url = $this->url ."/defaultTrial?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data));
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson() //json请求 返回转数组
            ->withTimeout(60)
            ->post();
        //失败返回
        if($response->status != 200)
            return ['data'=> 'default quote error', 'code'=> 400];
        if($response->content->respCode != 0){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'defaultQuote');
            return ['data'=> $response->content->respMsg, 'code'=> 400];
        }

        return ['data'=>$response->content->data, 'code'=>200];    //默认算费信息
    }

    public function insJob()
    {
        $p_code = $this->p_code;

        $data = [
            'transNo' => rand(1000000, 9999999),    //交易流水号
            'partnerId' => $this->app_key,  //开发者身份标识
            'caseCode' => $p_code, //方案代码
        ];
        $request_url = $this->url ."/productInsuredJob?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data));
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true) //json请求 返回转数组
            ->withTimeout(60)
            ->post();
//        dd($response);
        //失败返回
        if($response->status != 200)
            return ['data'=> 'request attr error', 'code'=> 400];
        if($response->content['respCode'] != 0){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'insJob');
            return ['data'=> null, 'code'=> 200];
        }

        foreach($response->content['data']['jobs'] as $k => $v){
            if($v['isInsure'] == '否')
                unset($response->content['data']['jobs'][$k]);
        }

//        $jobs = $this->getSubTree($response->content['data']['jobs']);
        $jobs = $this->makeTree($response->content['data']['jobs']);
        return ['data'=>$jobs, 'code'=>200];
    }

    public function address(){
        $area = $this->addressCurl();
        if($area['code'] != 200)
            return $area;
        foreach($area['data'] as $k => &$v){
            $cities = $this->addressCurl($v['code']);
            if($cities['code'] != 200)
                return $cities;
            $v['cities'] = $cities['data'];
            foreach($v['cities'] as $ck => &$cv){
                $countries = $this->addressCurl($cv['code']);
                if($countries['code'] != 200)
                    return $countries;
                if($countries['data'])
                    $cv['countries'] = $countries['data'];
            }
            if(empty($v['cities'][0]['countries'])){
                $cities = $v['cities'];
                unset($cities['countries']);
                $area['data'][$k]['cities'] = [[
                    'code' =>$v['code'],
                    'name' => $v['name'],
                    'countries' => $cities
                ]];
            }
        }
//        dd($area);
        return ['data'=>$area['data'], 'code'=>200];
    }

    protected function addressCurl($code = null)
    {
        $p_code = $this->p_code;
        $data = [
            'transNo' => rand(1000000, 9999999),    //交易流水号
            'partnerId' => $this->app_key,  //开发者身份标识
            'caseCode' => $p_code, //方案代码
            'areaCode' => $code
        ];
        $request_url = $this->url ."/productInsuredArea?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data));
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(120)
            ->post();
//                dd($response);
        //失败返回
        LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'data');
        if($response->status != 200)
            return ['data'=> 'request area error', 'code'=> 400];
        if($response->content['respCode'] != 0){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'insJob');
            return ['data'=> null, 'code'=> 200];
        }
        foreach($response->content['data']['areas'] as $k => &$v) {
            $v['code'] = $v['areaCode'];
            $v['name'] = $v['areaName'];
            unset($v['areaCode']);
            unset($v['areaName']);
        }
        return ['data'=>$response->content['data']['areas'], 'code'=>200];
    }

    /**
     * 算费
     * @return array
     */
    public function quote()
    {
        $input = $this->origin_data;
        $p_code = $this->p_code;
        $data = [
            'transNo' => rand(1000000, 9999999),
            'partnerId' => $this->app_key,
            'caseCode' => $p_code,
//            "startDate" => "2017-08-10"
        ];

        $data['newRestrictGenes'] = json_decode($input['new_val'], true);   //当前选中的值
        $this->ty_option_to_out($data['newRestrictGenes']); //当前选中值的内部key 转 外部
        $oldRestrictGene[0] = json_decode($input['old_val'], true); //被变化的旧值（旧值只有一个 加数组方便统一转化）
        $this->ty_option_to_out($oldRestrictGene);  //旧值的内部key 转 外部
        $data['oldRestrictGene'] = $oldRestrictGene[0];
//        dd($data);
        $request_url = $this->url ."/orderTrial?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data, JSON_UNESCAPED_UNICODE));

        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson()
            ->withTimeout(60)
            ->post();
//        dd($response);
        LogHelper::logError($data, 'hz', 'quote_data');
        LogHelper::logError($response, 'hz', 'quote_return');
        //失败返回
        if($response->status != 200)
            return ['data'=> 'default quote error', 'code'=> 400];
        if($response->content->respCode != 0){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'insAttr');
            return ['data'=> $response->content->respMsg, 'code'=> 400];
        }

        $data = array();
        $data['price'] = $response->content->data->price;   //算费价格
        //受影响的选项
        if(isset($response->content->data->restrictGenes)){
            $data['new_genes'] = $response->content->data->restrictGenes;
            $this->out_option_to_ty( $data['new_genes']);
        }
        //已选参数
        // 格式 [{"protectItemId":"767","value":"5万元","sort":-1},{"protectItemId":"756","value":"10万元","sort":0}]
        //惠泽的 格式为 {"productId":1644,"productPlanId":1934,"genes":[ {"protectItemId":"767","value":"5万元","sort":-1},.... ]}
        $data['selected_options'] = $response->content->data->priceArgs;
        $data['protect_items'] = isset($response->content->data->protectItems) ? $response->content->data->protectItems : ''; //变化的保障项目
        $this->out_option_to_ty($data['selected_options']->genes, 'selected');
//        dd($data);
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        return ['data'=>$data, 'code'=>200];

    }

    /**
     * 投保
     * @return array
     */
    public function buyIns()
    {
        $input = $this->decodeOriginData(); //解签获取源数据
        $p_code = $this->p_code;
        $values = $input['insurance_attributes'];
//        dd($input);exit;
        $data = [
            'transNo' => rand(1000000, 9999999),
            'caseCode' => $p_code,
            'partnerId' => $this->app_key,
            "startDate" => $values['ty_base']['ty_start_date'] //102 起保日期
        ];
        $priceArgs = json_decode($input['quote_selected'], true);
        $buy_options = array();
        $buy_options['insurance_attributes'] = $input['insurance_attributes'];
        $buy_options['quote_selected'] = $priceArgs;
        $this->ty_option_to_out($priceArgs['genes'], 'buy_ins'); //算费属性名称 转 外部
        $data["priceArgs"] = json_encode($priceArgs, JSON_UNESCAPED_UNICODE);

        //内部投保属性值 对应 外部投保值map
        $ins_val_map = $this->ins_ty_value_to_out_map();
        //投保人信息
        $ty_t_values = $values['ty_toubaoren'];

        //所在地
        if(isset($ty_t_values['ty_toubaoren_area'])){
            $area_array  = explode(',', $ty_t_values['ty_toubaoren_area']);
            $area_array = array_unique($area_array);
            $area_toubaoren = implode('-', $area_array);
        }

        $data['applicant'] = [
            "cName" => $ty_t_values['ty_toubaoren_name'],   //中文名
            "cardType" => $ins_val_map['ty_toubaoren_id_type'][$ty_t_values['ty_toubaoren_id_type']],    //证件类型
            "cardCode" => $ty_t_values['ty_toubaoren_id_number'], //证件号
            "sex" => $ins_val_map['ty_toubaoren_sex'][$ty_t_values['ty_toubaoren_sex']], //性别 0：女 1：男
            "birthday" => $ty_t_values['ty_toubaoren_birthday'], //出生日期
            "mobile" => $ty_t_values['ty_toubaoren_phone'],  //手机号码
            "email" => $ty_t_values['ty_toubaoren_email'],   //邮箱
            "contactPost" => $ty_t_values['ty_toubaoren_contact_post'] ?? '',   //邮编
            "provCityId" => isset($area_toubaoren) ? $area_toubaoren : '', //居住省市    //todo
            "contactAddress" => isset($ty_t_values['ty_toubaoren_address']) ? $ty_t_values['ty_toubaoren_address'] : '',  //联系地址
            "job" => isset($ty_t_values['ty_toubaoren_job']) ? $ty_t_values['ty_toubaoren_job'] : '',
            "height" => isset($ty_t_values['height']) ? $ty_t_values['height'] : '',  //高度
            "weight" => isset($ty_t_values['weight']) ? $ty_t_values['weight'] : '',  //体重
            "cardPeriod" => isset($ty_t_values['cardPeriod']) ? $ty_t_values['cardPeriod'] : '',   //证件有效期

        ];
        //20 被保人信息
        $ty_b_values = $values['ty_beibaoren'];
        $insurants = array();
        foreach($ty_b_values as $bk => $bv){
            if(isset($bv['ty_beibaoren_area'])){
                $area_array  = explode(',', $bv['ty_beibaoren_area']);
                $area_array = array_unique($area_array);
                $bv['area'] = implode('-', $area_array);
            }
            $relation = $ins_val_map['ty_relation'][$bv['ty_relation']];  //与投保人关系
            if($relation == 1){ //关系为本人
                $insurants[] = [
                    "relationId" => $relation,
                    "insurantId" => time() . $bk,   //被保人id
                    "singlePrice" => $input['price'],    //产品单价
                    "cName" => $data['applicant']['cName'],   //中文名
                    "cardType" => $data['applicant']['cardType'],    //证件类型
                    "cardCode" => $data['applicant']['cardCode'], //证件号
                    "sex" => $data['applicant']['sex'], //性别
                    "birthday" => $data['applicant']['birthday'], //
                    "provCityId" => isset($area_toubaoren) ? $area_toubaoren : (isset($bv['area']) ? $bv['area'] : ''), //居住省市    //todo
                    "job" => isset($bv['ty_beibaoren_job']) ? ($bv['ty_beibaoren_job']) : '',   //职业信息
                    "height" => isset($bv['ty_beibaoren_height']) ? ($bv['ty_beibaoren_height']) : '',   //高度
                    "weight" => isset($bv['ty_beibaoren_weight']) ? ($bv['ty_beibaoren_weight']) : '',   //体重
                    "contactAddress" => isset($bv['ty_beibaoren_address']) ? $bv['ty_beibaoren_address'] : '',
                    "contactPost" => $data['applicant']['contactPost'],         //邮编
                    "count" => 1,   //购买份数
                ];
            } else {
                $insurants[]= [
                    "relationId" => $relation,
                    "insurantId" => time() . $bk,   //被保人id
                    "singlePrice" => $input['price'],    //产品单价
                    "cName" => $bv['ty_beibaoren_name'],   //中文名
                    "cardType" => $ins_val_map['ty_beibaoren_id_type'][$bv['ty_beibaoren_id_type']],    //证件类型
                    "cardCode" => $bv['ty_beibaoren_id_number'], //证件号
                    "sex" => $ins_val_map['ty_beibaoren_sex'][$bv['ty_beibaoren_sex']], //性别
                    "birthday" => $bv['ty_beibaoren_birthday'], //出生日期
                    "job" => isset($bv['ty_beibaoren_job']) ? ($bv['ty_beibaoren_job']) : '',   //职业信息
                    "height" => isset($bv['ty_beibaoren_height']) ? ($bv['ty_beibaoren_height']) : '',   //高度
                    "weight" => isset($bv['ty_beibaoren_weight']) ? ($bv['ty_beibaoren_weight']) : '',   //体重
                    "provCityId" => isset($bv['area']) ? $bv['area'] : '',
                    "contactAddress" => isset($bv['ty_beibaoren_address']) ? $bv['ty_beibaoren_address'] : '',
                    "count" => 1   //购买份数
                ];
            }
        }
        $data['insurants'] = $insurants;
        //其他信息
        $data['otherInfo'] = array();

        $data['otherInfo']['healthAnswerId'] = $input['healthId'] ?? 3838783;
//        if(isset($values['ty_house'])){
//            $data["otherInfo"]['propertyAddress'] = $values['ty_house']['ty_house_address'];
//        }
        if(isset($input['insurance_attributes']['ty_payment_continue'])){
            $data["otherInfo"]['renewalPayBank'] = $values['ty_payment_continue']['ty_renewal_pay_bank']; //银行名称对应ID号
            $data["otherInfo"]['renewalPayCardholder'] = $values['ty_payment_continue']['ty_renewal_card_holder'];    //银行卡归属人
            $data["otherInfo"]['renewalPayAccount'] = $values['ty_payment_continue']['ty_renewal_pay_account'];    //银行卡号
            $data["otherInfo"]['renewalPayBankAddr'] = isset($values['ty_payment_continue']['ty_bank_address']) ? $values['ty_payment_continue']['ty_bank_address'] : '';    //银行地址
            $data["otherInfo"]['renewalPayBranch'] = isset($values['ty_payment_continue']['ty_bank_branch']) ? $values['ty_payment_continue']['ty_bank_branch'] : '';    //开户支行
        }
        if(isset($input['insurance_attributes']['ty_daikou'])){
            $data["otherInfo"]['withholdBank'] = $values['ty_daikou']['ty_renewal_pay_bank']; //银行名称对应ID号
            $data["otherInfo"]['withholdCardholder'] = $values['ty_daikou']['ty_renewal_card_holder'];    //银行卡归属人
            $data["otherInfo"]['withholdAccount'] = $values['ty_daikou']['ty_renewal_pay_account'];    //银行卡号
        }
        if(isset($input['insurance_attributes']['ty_xubao'])){
            $data["otherInfo"]['renewalBank'] = $values['ty_daikou']['ty_renewal_pay_bank']; //银行名称对应ID号
            $data["otherInfo"]['renewalCardholder'] = $values['ty_daikou']['ty_renewal_card_holder'];    //银行卡归属人
            $data["otherInfo"]['renewalAccount'] = $values['ty_daikou']['ty_renewal_pay_account'];    //银行卡号
            $data["otherInfo"]['renewalBankAddr'] = isset($values['ty_daikou']['ty_bank_address']) ? $values['ty_daikou']['ty_bank_address'] : '';    //银行地址
        }

//        echo "<pre>";
//        dd($data);
        $request_url = $this->url ."/insure?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data, JSON_UNESCAPED_UNICODE));    //中文转译问题JSON_UNESCAPED_UNICODE
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(60)
            ->post();
//        dd($response);
        if($response->status != 200)
            return ['data'=> '投保异常，检查参数是否正确!', 'code'=> 400];
        if($response->content['respCode'] != 0){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'buyIns');
            $err = $this->replaceErr($response->content['respMsg']);
            return ['data'=> $err, 'code'=> 400];
        }
        //todo add order
        $result = $this->addOrder($values, $response->content['data'], $input['price'], $buy_options);
        return $result;
    }

    /**
     * 支付
     * @return array
     */
    public function getPayWayInfo()
    {
        //todo
        $input = $this->decodeOriginData(); //解签获取源数据

        if(empty($input['pay_way'] || $input['union_order_code']))
            return ['data'=>'订单号或支付类型错误', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();
        if(empty($ins_order))
            return ['data'=>'订单号错误', 'code'=> 400];
        if($ins_order->status == 'check_error')
            return ['data'=> $ins_order->insures()->first()->check_error_message, 'code'=> 400];

        switch($input['pay_way']){
            case 'aliPay': //支付宝
                $gateway = 1;
                break;
            case 'wechatPay': //微信
                $gateway = 21;
                break;
            default:
                $gateway = 3;
                break;
        }
        $data = [
            'transNo' => rand(1000000, 9999999),
            'partnerId' => $this->app_key,
            "insureNums" => $ins_order->union_order_code,
//            "money"=> $ins_order->total_premium,  //单位分
            // "money" => $this->test ? 1 : $ins_order->total_premium,
            "gateway" => $gateway,
            "clientType" => 1,
            "callBackUrl" => $input['redirect_url']
        ];
		$data['money'] = $this->test ? 1 : $ins_order->total_premium;
//        dd($data);
        $request_url = $this->url ."/onlinePay?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data, JSON_UNESCAPED_SLASHES)); //callBackUrl 转译问题JSON_UNESCAPED_SLASHES
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(60)
            ->post();
//        dd($response);
        //请求支付失败
        if($response->status != 200)
            return ['data'=> '获取支付信息失败!', 'code'=> 400];
        if($response->content['respCode'] != 0){
//            $ins_order->status = 'check_error';
//            $ins_order->save();
//            Insure::where('ins_order_id', $ins_order->id)
//                ->update([
//                        'status'=>'check_error',
//                        'check_error_message'=> $response->content['respMsg']
//                    ]);
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'buyIns');
//            return ['data'=> $response->content['respMsg'], 'code'=> 400];
            $err = $this->replaceErr($response->content['respMsg']);
            return ['data'=> $err, 'code'=> 400];
        }
        $ins_order->status = 'pay_ing';
        $ins_order->save();
        Insure::where('ins_order_id', $ins_order->id)->update(['status'=>'getPayWayInfo']);
        $data = $response->content['data'];
        return ['data'=>['order_code'=>$ins_order->union_order_code, 'pay_way_data'=> ['url'=>$data['gatewayUrl']]], 'code'=> 200];
    }


    //退保
    public function rejectIns()
    {
        $insureNum = $this->request->get('insureNum');
        $data = [
            "transNo"=> rand(1000000, 9999999),
            "partnerId"=> $this->app_key,
            "insureNum"=> 20170907007030
        ];
        $request_url = $this->url ."/surrenderPolicy?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data, JSON_UNESCAPED_SLASHES));
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson()
            ->withTimeout(60)
            ->post();
        dd($response);
    }


    /**
     * 核保
     */
    public function checkIns()
    {

    }

    /**
     * 回调
     * @return mixed
     */
    public function callBack()
    {
        $return = $this->request->all();
        try{
            LogHelper::logSuccess($return, 'hz', $this->request->get('notifyType'));
            switch($return['notifyType']){
                case 2: //支付
                    $order = InsOrder::where(['union_order_code'=> $return['data']['insureNum'], 'api_from_uuid'=> 'Qx'])->first();
                    $user = User::where('account_id', $order->create_account_id)->first();
                    if($return['data']['state']){  //支付成功
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
                                'union_order_code' => $return['data']['insureNum'],
                                'by_stages_way' => $order->by_stages_way,
                                'error_message' => '',
                            ]
                        ];
                    } else {
                        Insure::where('ins_order_id', $order->id)->update(['status'=>'pay_error', 'pay_error_message'=>$return['data']['failMsg']]);
                        $data = [
                            "notice_type"=> 'pay_call_back',
                            'data' => [
                                'status'=>false,
                                'account_id' => $user->account_id,
                                'union_order_code' => $return['data']['insureNum'],
                                'error_message' => $return['data']['failMsg'],
                            ]
                        ];
                    }
//                    $i = $j = 0;
//                    while ($i == 0 || $j < 3){
                        $response = Curl::to($user->call_back_url . '/ins/call_back')
                            ->returnResponseObject()
                            ->withData($data)
                            ->asJson()
                            ->withTimeout(60)
                            ->post();
//                        $i = $response->content ? 1 : 0;
//                        $j++;
//                        sleep(1);
//                    }
                    break;
                case 3: //出单

                    break;
                case 4: //退保

                    break;
                case 5: //退保重出

                    break;
            }
            $res = json_encode(['state'=>true]);
            return $res;
        } catch (\Exception $e ){
            LogHelper::logError($return, json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE), 'hz', $this->request->get('notifyType'));
            return json_encode(['state'=>false, 'failMsg'=>'回调处理失败'], JSON_UNESCAPED_UNICODE);
        }
    }


    public function issue()
    {
        $input = $this->decodeOriginData(); //解签获取源数据
        LogHelper::logSuccess($input);
        if(!$input['union_order_code'])
            return ['data'=>'订单号不可为空', 'code'=> 400];
        $ins_order = InsOrder::where('union_order_code', $input['union_order_code'])->first();
        if(empty($ins_order))
            return ['data'=>'订单不存在', 'code'=> 400];
        if(!in_array($ins_order->status, ['pay_end']))
            return ['data'=>'此订单无法出单', 'code'=> 400];
        $data = [
            'transNo' => rand(1000000, 9999999),
            'partnerId' => $this->app_key,
            "insureNum"=> $ins_order->union_order_code,
        ];
        $request_url = $this->url ."/orderDetail?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data, JSON_UNESCAPED_SLASHES)); //callBackUrl 转译问题JSON_UNESCAPED_SLASHES
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(60)
            ->post();
        $return_data = $response->content['data']['orderDetail'];
//        dd($return_data);
        $return = array();
        //返回结果封装
        $return['status'] = $return_data['effectiveStatus'];    //出单状态 0：未生效 1：已生效 2：退保中 3：已退保
        $return['policy_order_code'] = $return_data['insurants'][0]['policyNum'];   //被保人保单号
        $return['private_p_code'] = $input['private_p_code'];   //产品码
        $return['order_code'] = $input['order_code'];   //被保人单号
//        $return['projects'] = $return_data['projects'];
        foreach($return_data['projects'] as $k => $v){
            if($v['projectName'] == '保障期限'){
                $return['start_time'] = $v['startDate'];
                $return['end_time'] = $v['endDate'];
            }
        }
        return ['data'=> $return, 'code'=> 200];
    }


    /**
     * 保单下载
     * @return array
     */
    public function issueDownload()
    {
        $insureNum = $this->request->get('insureNum');
        $data = [
            "transNo"=> rand(1000000, 9999999),
            "partnerId"=> $this->app_key,
            "insureNum"=> $insureNum
        ];
        $request_url = $this->url ."/downloadUrl?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data, JSON_UNESCAPED_SLASHES));
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true)
            ->withTimeout(60)
            ->post();
        dd($response);
    }

    /**
     * 获取健康告知
     */
    public function getHealthStatement(){

        $p_code = $this->p_code;

        $input = $this->origin_data;
        $data = [
            'transNo'   => rand(1000000, 9999999),    //交易流水号
            'caseCode'  => $p_code, //方案代码
            'partnerId' => $this->app_key,  //开发者身份标识
        ];

        $genes = json_decode($input['selected'], true)['genes'];

        $this->ty_option_to_out($genes,false); //当前选中值的内部key 转 外部

        $data['genes'] = $genes;

        $request_url = $this->url . "/healthStatement?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data, JSON_UNESCAPED_UNICODE));

//        print_r(json_encode($data));die;

        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true) //json请求 返回转数组
            ->withTimeout(60)
            ->post();
        $res = $response->content;

        if($response->status != 200)
            return ['data'=> 'Health notification access failure', 'code'=> 400];
        if($res['respCode'] != 0){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'health');
            return ['data'=> $res['respMsg'], 'code'=> 400];
        }

        return  ['data'=> $res['data'], 'code'=> 200];
    }

    /**
     * 提交健康告知
     */
    public function subHealthStatement(){

        $p_code = $this->p_code;

        $input = $this->origin_data;
        $data = [
            'transNo'   => $input['transNo'],    //交易流水号
            'caseCode'  => $p_code, //方案代码
            'partnerId' => $this->app_key,  //开发者身份标识
        ];

        $genes = json_decode($input['selected'], true)['genes'];

        $this->ty_option_to_out($genes,false); //当前选中值的内部key 转 外部

        $data['genes'] = $genes;
        $data['qaAnswer'] = $input['qaAnswer'];

        $request_url = $this->url . "/submitHealthState?";
        $request_url .= 'sign=' . md5($this->app_sign_key . json_encode($data, JSON_UNESCAPED_UNICODE));


        //dump($data);die;
        $response = Curl::to($request_url)
            ->returnResponseObject()
            ->withData($data)
            ->asJson(true) //json请求 返回转数组
            ->withTimeout(60)
            ->post();
        $res = $response->content;
        LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'sub_health');
        if($response->status != 200)
            return ['data'=> 'Health notification access failure', 'code'=> 400];
        if($res['respCode'] != 0){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'health');
            return ['data'=> $res['respMsg'], 'code'=> 400];
        }

        return  ['data'=> $res['data'], 'code'=> 200];
    }

    /**
     * 健康告知验证返回
     */
    protected function healthVerify(){
        //需要验证健康告知
        $verify_ins_code_arr = [
            'QX000000002515',
            'QX000000002413',
            'QX000000002414',
            'QX000000002503',
            'QX000000002499',
            'QX000000002500',
            'QX000000002501',
            'QX000000002502',
            'QX000000002503'
        ];

        if (in_array($this->p_code, $verify_ins_code_arr)) {
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 核保回调
     */
    public function checkCallBack()
    {

    }

    protected function addOrder($post_data, $return_data, $price, $buy_options)
    {
        $api_form = ApiFrom::where('id', $this->api_m_info->api_from_id)->first();
//        try{
            DB::beginTransaction();
            //订单信息
//            dd($post_data);
            $order = new InsOrder();
            $order->order_no = date('YmdHis'). rand(10, 99) . rand(100, 999); //内部订单号
            $order->union_order_code = $return_data['insureNum']; //外部总订单号
            $order->create_account_id = $this->request['account_id'];   //代理商account_id
            $order->api_from_uuid = $api_form->uuid;    //接口来源唯一码（接口类名称)
            $order->api_from_id = $api_form->id;    //接口来源唯一码（接口类名称)
            $order->ins_id = $this->api_m_info->insurance_id; //产品唯一码
            $order->p_code = $this->p_code; //产品唯一码
            $order->bind_id = $this->api_m_info->id;
            $order->total_premium = $price;  //总保费
            $order->status = 'check_ing'; //待核保状态
            $order->buy_options = json_encode($buy_options, JSON_UNESCAPED_UNICODE);
            //todo
            $order->by_stages_way = '0年';
           foreach($buy_options['quote_selected']['genes'] as $bk => $bv){
               if(isset($bv['ty_key']) && ($bv['ty_key'] == 'ty_pay_way'))
                   $order->by_stages_way = $bv['value'];
           }
            $order->save();
            //投保人信息
            $policy = new Policy();
            $policy->union_order_code = $return_data['insureNum'];  //外部总定单号
            $policy->ins_order_id = $order->id; //订单ID
            $policy->name = $post_data['ty_toubaoren']['ty_toubaoren_name'];   //投保人名称
            $policy->phone = $post_data['ty_toubaoren']['ty_toubaoren_phone']; //投保人电话
            $policy->card_type = $post_data['ty_toubaoren']['ty_toubaoren_id_type'];   //投保人卡号类型
            $policy->card_id = $post_data['ty_toubaoren']['ty_toubaoren_id_number'];
            $policy->address = '';
            $policy->email = $post_data['ty_toubaoren']['ty_toubaoren_email'];
            $policy->sex = $post_data['ty_toubaoren']['ty_toubaoren_sex'] ? '男' : '女';
            $policy->birthday = $post_data['ty_toubaoren']['ty_toubaoren_birthday'];
            $policy->save();

            //被保人信息
            $insures = array();
            foreach($post_data['ty_beibaoren'] as $k => $v){
                if($insures[$k]['relation'] = $v['ty_relation'] == 1){
                    $insures[$k]['ins_order_id'] = $order->id;   //被保人单号 分接口来源 惠泽返回无此参数
                    $insures[$k]['out_order_no'] = $return_data['insureNum'];   //被保人单号(分接口来源)
                    $insures[$k]['premium'] = $price;    //保费
                    $insures[$k]['p_code'] = $this->p_code;    //外部产品码
                    $insures[$k]['union_order_code'] =  $return_data['insureNum'];  //外部合并订单号
                    $insures[$k]['name'] = $policy->name;
                    $insures[$k]['sex'] = $policy->sex;
                    $insures[$k]['phone'] = $policy->phone;
                    $insures[$k]['card_type'] = $policy->card_type;
                    $insures[$k]['card_id'] = $policy->card_id;
                    $insures[$k]['relation'] = $v['ty_relation'];
                    $insures[$k]['birthday'] = $policy->birthday;
                    $insures[$k]['ins_start_time'] = $post_data['ty_base']['ty_start_date'];
                    $insures[$k]['created_at'] = $insures[$k]['updated_at'] = date("Y-m-d H:i:s");
                } else {
                    $insures[$k]['ins_order_id'] = $order->id;   //被保人单号 分接口来源 惠泽返回无此参数
                    $insures[$k]['out_order_no'] = $return_data['insureNum'];   //被保人单号(分接口来源)
                    $insures[$k]['premium'] = $price;    //保费
                    $insures[$k]['p_code'] = $this->p_code;    //外部产品码
                    $insures[$k]['union_order_code'] =  $return_data['insureNum'];  //外部合并订单号
                    $insures[$k]['name'] = $v['ty_beibaoren_name'];
                    $insures[$k]['sex'] = $v['ty_beibaoren_sex'];
                    $insures[$k]['phone'] = $v['ty_beibaoren_phone'];
                    $insures[$k]['card_type'] = $v['ty_beibaoren_id_type'];
                    $insures[$k]['card_id'] = $v['ty_beibaoren_id_number'];
                    $insures[$k]['relation'] = $v['ty_relation'];
                    $insures[$k]['birthday'] = $v['ty_beibaoren_birthday'];
                    //$insures[]['address'] = $v['holderAddress'];
//                $insures[]['email'] = $v['holderEmail'];
                    $insures[$k]['ins_start_time'] = $post_data['ty_base']['ty_start_date'];
                    $insures[$k]['created_at'] = $insures[$k]['updated_at'] = date("Y-m-d H:i:s");
                }
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
            $res['total_premium'] = $price;
            $res['union_order_code'] = $return_data['insureNum'];
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
//        } catch (\Exception $e) {
//            DB::rollBack();
//            LogHelper::logError([$post_data, $return_data], $e->getMessage(), 'hz', 'add_order');
//            $msg = ['data' => '保存订单失败，请重试！', 'code' => 500];
//            return $msg;
//        }
    }


    //todo
    public function orderStatus()
    {
        $input = $this->decodeOriginData(); //解签获取源数据

        if(empty($input['order_no']))
            return ['data'=>'请传入订单号', 'code'=> 400];
        $ins_order = InsOrder::where('order_no', $input['order_no'])->first();
        if(empty($ins_order))
            return ['data'=>'订单号错误', 'code'=> 400];
        $insures = Insure::where(
            [
                ['ins_order_id', $ins_order->id],
                ['check_error_message', '!=', '']
            ])
            ->orWhere(
                [
                    ['ins_order_id', $ins_order->id],
                    ['pay_error_message', '!=', '']
                ])
            ->get();
        $message = '';
        foreach($insures as $k => $v){
            $message = $v->check_error_message . $v->pay_error_message;
        }
        $data = [
            'data'=>[
                'status'=> $ins_order['status'],
                'message'=> $message
            ],
            'code'=> 200];
        return $data;
    }

    //算费属性---内部map
    protected function genes_map_to_ty()
    {
        $map = [
            'insurantDate' => 'ty_age', //年龄
            'insurantJob' => 'ty_jop',  //职业
            'insurantDateLimit' => 'ty_duration_period_value', //保障期限
            'buyCount'  => 'ty_buy_count',  //购买份数
            'insureAgeLimit'=> 'ty_pay_way',  //缴费分期方式
        ];
        return $map;
    }

    //算费属性---外部map
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

    //算费属性名称 转 内部
    protected function out_option_to_ty(&$out_options, $type='genes', $map=null)
    {
        $ty_keys = $map ? $map : $this->genes_map_to_ty();
        foreach($out_options as $k => &$v){
            //替换key为内部key
            if(isset($v->key)){
                if(isset($ty_keys[$v->key])){
                    $v->ty_key = $ty_keys[$v->key];
                }else{
                    $v->ty_key = $v->key;
                }
                unset($v->key);
            }
            if($type == 'genes'){
                //替换value为内部值
                foreach($v->values as $vk => &$gene_v){
                    $gene_v->ty_value = $gene_v->value;
                    unset($gene_v->value);
                }
            }
        }
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

    //投保属性--内部map
    protected function ins_map_to_ty($module_type)
    {
        $map = [
            'ty_toubaoren'=>[
                'cName' => 'ty_toubaoren_name',  //投保人名称
                'cardType' => 'ty_toubaoren_id_type',   //投保人证件类型
                'cardCode' => 'ty_toubaoren_id_number', //投保人证件号
                'birthday' => 'ty_toubaoren_birthday',  //投保人生日
                'sex' => 'ty_toubaoren_sex',   //被保人性别
                'mobile' => 'ty_toubaoren_phone', //投保人电话
                'job' => 'ty_toubaoren_job',    //被保人职业
                'email' => 'ty_toubaoren_email', //投保人邮箱
                'provCityId' => 'ty_toubaoren_area',  //投保人所在地
                'contactAddress' => 'ty_toubaoren_address',   //投保人详细地址
                'contactPost' => 'ty_toubaoren_contact_post',   //联系地址邮编
            ],
            'ty_beibaoren'=>[
                'cName' => 'ty_beibaoren_name',  //被保人姓名
                'relationId' => 'ty_relation',  //与投保人关系
                'cardType' => 'ty_beibaoren_id_type',   //被保人证件类型
                'cardCode' => 'ty_beibaoren_id_number', //被保人证件号
                'birthday' => 'ty_beibaoren_birthday',  //被保人生日
                'sex' => 'ty_beibaoren_sex',   //被保人性别
                'mobile' => 'ty_beibaoren_phone', //被保人电话
                'job' => 'ty_beibaoren_job',    //被保人职业
                'email' => 'ty_beibaoren_email', //投保人邮箱
                'provCityId' => 'ty_beibaoren_area',  //所在地
                'contactAddress' => 'ty_beibaoren_address',   //详细地址
                'contactPost' => 'ty_beibaoren_contact_post',   //联系地址邮编
                'height' => 'ty_beibaoren_height',  //身高
                'weight' => 'ty_beibaoren_weight',  //体重
            ],
            'ty_shouyiren' => [],
            'ty_jinji' => [],
            'ty_base' =>[
                'startDate' => 'ty_start_date'
            ],
            'ty_house' => [
                'propertyAddress' => 'ty_house_address'
            ],
            'ty_xubao' => [
                'renewalBank' => 'ty_renewal_pay_bank',  //支付银行
                'renewalCardholder' => 'ty_renewal_card_holder',    //银行卡所属人
                'renewalAccount' => 'ty_renewal_pay_account',    //支付卡号
                'renewalBankAddr' => 'ty_bank_address'  //银行详细地址
            ],
            'ty_daikou' => [
                'withholdBank' => 'ty_renewal_pay_bank',
                'withholdCardholder' => 'ty_renewal_card_holder',  //持卡人
                'withholdAccount' => 'ty_renewal_pay_account',  //支付卡号
            ],
            'ty_payment_continue' => [
                'renewalPayBank' => 'ty_renewal_pay_bank',  //支付银行
                'renewalPayCardholder' => 'ty_renewal_card_holder',    //银行卡所属人
                'renewalPayAccount' => 'ty_renewal_pay_account',    //支付卡号
                'renewalPayBankAddr' => 'ty_bank_address',  //银行详细地址
                'renewalPayBranch' => 'ty_bank_branch'  //银行详细地址
            ],
        ];
        return $map[$module_type];
    }

    //投保属性名称 转 内部
    protected function ins_option_to_ty(&$options, $type)
    {
        $map = $this->ins_map_to_ty($type);
        foreach($options as $k => &$v){
//            if(!isset($v['apiName']))
//                dd($options);
            //是否需要地址信息
            if(($v['apiName'] == 'provCityId'))
                $this->need_area = true;
            if(isset($map[$v['apiName']])){
                $v['ty_key'] = $map[$v['apiName']];
            } else {
                $v['ty_key'] = $v['apiName'];
            }
            unset($v['apiName']);
        }
    }

    //投保属性值--内部map
    protected function ins_values_map()
    {
        $map =[
            'ty_relation' => [
                ['value'=>'本人', 'ty_value'=>1],
                ['value'=>'妻子', 'ty_value'=>2],
                ['value'=>'丈夫', 'ty_value'=>3],
                ['value'=>'儿子', 'ty_value'=>4],
                ['value'=>'女儿', 'ty_value'=>5],
                ['value'=>'父亲', 'ty_value'=>6],
                ['value'=>'母亲', 'ty_value'=>7],
//                ['value'=>'兄弟', 'ty_value'=>8],
//                ['value'=>'姐妹', 'ty_value'=>9],
//                ['value'=>'祖父/祖母/外祖父/外祖母', 'ty_value'=>10],
//                ['value'=>'孙子/孙女/外孙/外孙女', 'ty_value'=>11],
//                ['value'=>'叔父/伯父/舅舅', 'ty_value'=>12],
//                ['value'=>'婶/姨/姑', 'ty_value'=>13],
//                ['value'=>'侄子/侄女/外甥/外甥女', 'ty_value'=>14],
//                ['value'=>'堂兄弟/堂姐妹/表兄弟/表姐妹', 'ty_value'=>15],
//                ['value'=>'岳父', 'ty_value'=>16],
//                ['value'=>'岳母', 'ty_value'=>17],
//                ['value'=>'同事', 'ty_value'=>18],
//                ['value'=>'朋友', 'ty_value'=>19],
//                ['value'=>'雇主', 'ty_value'=>20],
//                ['value'=>'雇员', 'ty_value'=>21],
//                ['value'=>'法定监护人', 'ty_value'=>22],
//                ['value'=>'其他', 'ty_value'=>23],
            ],
            'ty_toubaoren_id_type' => [
                ['value'=>'身份证', 'ty_value'=>1],
                ['value'=>'护照', 'ty_value'=>2],
                // ['value'=>'军官证', 'ty_value'=>3],
                ['value'=>'其他', 'ty_value'=>4],
            ],
            'ty_beibaoren_id_type' => [
                ['value'=>'身份证', 'ty_value'=>1],
                ['value'=>'护照', 'ty_value'=>2],
                // ['value'=>'军官证', 'ty_value'=>3],
                ['value'=>'其他', 'ty_value'=>4],
            ],
            'ty_toubaoren_sex' =>[
                ['value'=>'男', 'ty_value'=>1],
                ['value'=>'女', 'ty_value'=>0],
            ],
            'ty_beibaoren_sex' =>[
                ['value'=>'男', 'ty_value'=>1],
                ['value'=>'女', 'ty_value'=>0],
            ],
        ];
        return $map;

    }

    //投保属性值 转 外部
    protected function ins_ty_value_to_out_map()
    {
        //内部值对应外部值
        $map = [
            'ty_relation'=>[    //关系
                1 => 1, //本人
                2 => 2, //妻子
                3 => 3, //丈夫
                4 => 4, //儿子
                5 => 5, //女儿
                6 => 6, //父亲
                7 => 7  //母亲
            ],
            'ty_toubaoren_sex'=>[   //投保人性别
                1 => 1, //男
                0 => 0  //女
            ],
            'ty_beibaoren_sex'=>[   //被保人性别
                1 => 1, //男
                0 => 0  //女
            ],
            'ty_toubaoren_id_type'=>[   //证件类型
                1 => 1, //身份证
                2 => 2, //护照
                3 => 6, //军官证
                4 => 99, //其他
            ],
            'ty_beibaoren_id_type'=>[
                1 => 1, //身份证
                2 => 2, //护照
                3 => 6, //军官证
                4 => 99, //其他
            ],
        ];
        return $map;
    }

    //投保属性格式化
    protected function format_insurance_attributes_to_ty(&$format_insurance_attributes)
    {
        //替换选项名称
        foreach($format_insurance_attributes as $k => &$attribute){
            //是否必填 不是则跳过
            if(!isset($attribute['productAttributes'])){
                unset($format_insurance_attributes[$k]);
                continue;
            }
            $continue = 0;
            foreach($attribute['productAttributes'] as $pk => $pv){
                $continue += $pv['required'];
            }
            if(!$continue){
                unset($format_insurance_attributes[$k]);
                continue;
            }

            //todo change value
            switch($attribute['moduleId']){
                case 10:    //投保人
                    $attribute['module_key'] = 'ty_toubaoren';
                    $this->ins_option_to_ty($attribute['productAttributes'], 'ty_toubaoren');
                    break;
                case 20:    //被保人
                    $attribute['module_key'] = 'ty_beibaoren';
                    $this->ins_option_to_ty($attribute['productAttributes'], 'ty_beibaoren');
                    break;
                case 30:    //受益人
                    unset($format_insurance_attributes[$k]);
                    break;
                case 100:   //房屋信息
                    $attribute['module_key'] = 'ty_house';
                    $this->ins_option_to_ty($attribute['productAttributes'], 'ty_house');
                    break;
                case 101:   //紧急联系人
                    unset($format_insurance_attributes[$k]);
                    break;
                case 102:   //基础信息
                    $this->ins_option_to_ty($attribute['productAttributes'], 'ty_base');
                    $format_insurance_attributes[102]['moduleId'] = 1;
                    $format_insurance_attributes[102]['name'] = '基础信息';
                    $format_insurance_attributes[102]['remark'] = '基础信息';
                    $format_insurance_attributes[102]['module_key'] = 'ty_base';
                    $format_insurance_attributes[102]['productAttributes'][] = $attribute['productAttributes'][0];
                    unset($format_insurance_attributes[$k]);
//                    dd($format_insurance_attributes);exit;
                    break;
                case 104:   //续保信息
                    $attribute['module_key'] = 'ty_xubao';
                    $this->ins_option_to_ty($attribute['productAttributes'], 'ty_xubao');
                    break;
                case 105:   //代扣缴费信息
                    $attribute['module_key'] = 'ty_daikou';
                    $this->ins_option_to_ty($attribute['productAttributes'], 'ty_daikou');
                    break;
                case 107:   //续期缴费银行
                    $attribute['module_key'] = 'ty_payment_continue';
                    $this->ins_option_to_ty($attribute['productAttributes'], 'ty_payment_continue');
                    break;
            }
        }
        //替换选项值
        foreach($format_insurance_attributes as $k => &$attribute){
            $value_map = $this->ins_values_map();
            foreach($attribute['productAttributes'] as $pk => &$pv){
                if(!isset($pv['attributeValues'])){ //部分属性类型为输入框 没有对应选项值
                    $pv['attributeValues'] = [];
                } else {
                    if(!isset($pv['ty_key']))
                        dd($attribute);
                    if(isset($value_map[$pv['ty_key']])){   //有设置 内部对应 值的替换
                        $pv['attributeValues'] = $value_map[$pv['ty_key']];
                    } else {
                        foreach($pv['attributeValues'] as $vk => &$vv){ //无 内部值 遍历 拿返回值填充
                            $vv['ty_value'] = $vv['controlValue'];
                            unset($vv['controlValue']);
                        }
                    }
                }
            }
        }
    }


    /**
     * 结构化子类
     * @param $data
     * @param int $id
     * @return array
     */
    public function getSubTree(&$data , $id = 0) {
        static $son = array();
        static $sort = 0;
        foreach($data as $key => $value) {
            if($value['parentId'] == $id) {
                $sort++;
                $value['sort'] = $sort;
                $son[] = $value;
                unset($data[$key]);

                self::getSubTree($data , $value['id']);
            }
        }
        return $son;
    }


    //结构化子类
    function makeTree($list,$pk='id',$pid='parentId',$child='_child',$root=0){
        $tree=array();
        foreach($list as $key=> $val){
            if($val[$pid]==$root){
                //获取当前$pid所有子类
                unset($list[$key]);
                if(! empty($list)){
                    $child=$this->makeTree($list,$pk,$pid,$child,$val[$pk]);
                    if(!empty($child)){
                        $val['_child']=$child;
                    }
                }
                $tree[]=$val;
            }
        }
        return $tree;
    }

    //根据父id获得所有下级子类id的数据
    //$id = 父级id， $array = 所有分类
    public function getSon($id,$array){
        static $list;
        foreach ($array as $k => $v) {
            if($v['parent_id'] == $id){
                $list[] = $array[$k];
                self::getSon($v['id'],$array);
            }
        }
        return $list;
    }

    public function replaceErr($err)
    {
        $err = preg_replace("/[a-zA-Z]|[.:]/","", $err);
        return $err;
    }

}
