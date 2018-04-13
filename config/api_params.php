<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2017/9/18
 * Time: 11:25
 */

/**
 * 接口固定参数
 */
return [
    //泰康在线
    'Tk'=>[
        'key'=>'1234567890ABCDEF',//秘钥，泰康和每家合作公司约定的一个唯一标识
        'api_test_url'=>'http://119.253.81.113/tk-link/rest',//测试
        'api_insure_url'=>'http://119.253.80.26/tk-link/rest',//生产
        'coop_id' => 'yun_da_kuai_di', //合作方代码
        'service_id_check' => '01', //核保
        'service_id_issue' => '02', //出单
        'service_id_pay' => '11', //生成支付链接
        'sign_type' => 'md5',//签名方式
        'sign' => 'md5(key+apply_content)',//加签方式
        'format' => 'json',//报文格式
        'charset' => 'utf-8',//编码方式
        'version' => '1.0', //版本号
        'timestamp' => 'time()', //时间戳
        'serial_no' => '',//流水号
        'product_type'=>'1122A01G',//产品类型
        'FieldAA_1' => '1122A01G01',//产品代码  ￥2    ￥270000
        'FieldAA_2' => '1122A01G02',//产品代码  ￥1.5  ￥235000
        'FieldAA_3' => '1122A01G03',//产品代码  ￥1    ￥130000
        'apply_content'=> '',//业务参数信息
    ]
];

