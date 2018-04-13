<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2018/2/7
 * Time: 13:44
 */

namespace App\Console\Commands\PullProducts;

use App\Helper\LogHelper;
use Curl;
use App\Models\ApiFrom;
use App\Models\Insurance;
use App\Models\InsuranceApiInfo;

class QxPullProduct implements BaseProduct
{
    protected $test, $app_key, $app_sign_key, $url, $need_area, $p_code_array, $num, $ins_api_info ,$p_code;

    public function __construct()
    {
        $this->test = 1;
        $this->app_key = $this->test ? '1006689' : '1006689';
        $this->app_sign_key = $this->test ? 'TNYzk0NDAzZGZlNWI1006689' : 'ONM2FhYWU4ODVjZWZ1006689';
        $this->url = $this->test ? 'http://tuneapi.qixin18.com/api' : 'http://api.qixin18.com/api';
        $this->need_area = false;
        $this->p_code_array = array();
    }

    public function pullProducts($p_code_array=[])
    {
        if($p_code_array){
            $this->p_code_array = InsuranceApiInfo::whereIn('p_code', $p_code_array)
                ->get(['id', 'private_p_code', 'insurance_id', 'p_code'])
                ->toArray();
        } else {
            $api_from = ApiFrom::where('uuid', 'Qx')->first(['id']);
            $this->p_code_array = InsuranceApiInfo::where(['api_from_id'=> $api_from->id, 'status'=>'1'])
                ->get(['id', 'private_p_code', 'insurance_id', 'p_code'])
                ->toArray();
        }
        return $this->p_code_array;
    }

    public function pulling($p_code)
    {
        $cache_name = 'ins_info_' . $p_code['p_code'];
        $this->ins_api_info =$p_code;
        $this->p_code = $p_code['p_code'];
        if(!\Cache::has($cache_name)){
            $data = [
                'transNo' => rand(10000000, 99999999),    //交易流水号
                'caseCode' => $this->p_code, //方案代码
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

            $res = $this->formatReturn($response->content->data);
            $res['switch']['health_verify'] = $this->healthVerify();

            //投保告知
            $product = Insurance::where(['id'=> $this->ins_api_info['insurance_id']])->first();
            $res['option']['insurance_notice'] = $product['content'];

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
            \Cache::forever($cache_name, serialize($res));
        }
        $res = unserialize(\Cache::get($cache_name));
        return ['data'=> json_encode($res, JSON_UNESCAPED_UNICODE), 'code'=>200];
    }

    public function formatReturn($return_data)
    {
        $data = array();
        $data['ty_product_id'] = $this->ins_api_info['insurance_id'];
        $data['private_p_code'] = $this->ins_api_info['private_p_code'];
        $data['bind_id'] = $this->ins_api_info['id'];
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
//        $data = array();
//        $data['ty_product_id'] = $this->api_m_info->insurance_id;
//        $data['private_p_code'] = $this->api_m_info->private_p_code;
//        $data['bind_id'] = $this->api_m_info->id;
//        $data['option']['insurance_attributes'] = $return_data->attr['attrModules'];    //投保属性
//        $this->format_insurance_attributes_to_ty($data['option']['insurance_attributes']);
////        $data['firstDate'] = isset($return_data->firstDate) ? $return_data->firstDate : 1;
////        $data['latestDate'] = isset($return_data->latestDate) ? $return_data->latestDate : 20;
//        if($this->p_code == 'QX000000100393'){
//            $data['option']['insurance_attributes'][0]['productAttributes'][] = [
//                "name"=> "投保人性别",
//                "type"=> 0,
//                "required"=>1,
//                "attributeValues"=> [
//                    ["value"=> "男","ty_value"=> 1],
//                    ["value"=> "女","ty_value"=> 0],
//                ],
//                "ty_key"=> "ty_toubaoren_sex"
//            ];
//            $data['option']['insurance_attributes'][1]['productAttributes'][] =[
//                "name"=> "被保人性别",
//                "type"=> 0,
//                "required"=>1,
//                "attributeValues"=> [
//                    ["value"=> "男","ty_value"=> 1],
//                    ["value"=> "女","ty_value"=> 0],
//                ],
//                "ty_key"=> "ty_beibaoren_sex"
//            ];
//        }
//
//        //选项key转为内部key
//        $this->out_option_to_ty($return_data->restrictGenes);
//
//        $data['option']['restrict_genes'] = $return_data->restrictGenes;    //试算因子及取值范围
//        $data['option']['price'] = $return_data->default_quote->price;    //默认试算价格
//        //默认已选参数 格式 {"protectItemId":"767","value":"5万元","sort":-1},{"protectItemId":"756","value":"10万元","sort":0}
//        //惠泽的 格式为 {"productId":1644,"productPlanId":1934,"genes":[ {"protectItemId":"767","value":"5万元","sort":-1},.... ]}
//        $selected = $return_data->default_quote->priceArgs;
//        // 选中项的key转换为内部的key
//        $this->out_option_to_ty($selected->genes, 'selected');
//        $data['option']['selected_options'] = $selected;
//        $data['option']['protect_items'] = $return_data->default_quote->protectItems;
//
//        return $data;
    }

    /**
     * 默认算费
     * @return array
     */
    protected function default_quote()
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

    /**
     * 投保属性
     */
    protected function insAttr()
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
//            $err = $this->replaceErr($response->content['respMsg']);
            return ['data'=> $response->content['respMsg'], 'code'=> 400];
        }
        return ['data'=>$response->content['data']['insureAttribute'], 'code'=>200];    //投保属性信息
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
                        //洗去多余数据
                        $pv_val = [];
                        foreach ($pv['attributeValues'] as $val) $pv_val[] = $val['value'];
                        $pv['attributeValues'] = $value_map[$pv['ty_key']];
                        $ty_pv = $value_map[$pv['ty_key']];
                        $filtration_pv = [];
                        foreach ($ty_pv as $vo){
                            if(in_array($vo['value'],$pv_val))$filtration_pv[] = $vo;
                        }
                        $pv['attributeValues'] = $filtration_pv;
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
        if($response->status != 200){
            LogHelper::logError($data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'hz', 'data');
            return ['data'=> 'request area error', 'code'=> 400];
        }

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
}