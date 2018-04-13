<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2017/11/13
 * Time: 15:25
 *
 * 内外键名对照
 */

return [
    //关系内部值
    'relation'=>[
        '本人'=>'1',
        '妻子'=>'2',
        '丈夫'=>'3',
        '儿子'=>'4',
        '女儿'=>'5',
        '父亲'=>'6',
        '母亲'=>'7',
        '兄弟'=>'8',
        '姐妹'=>'9',
        '祖父/祖母/外祖父/外祖母'=>'10',
        '叔父/伯父/舅舅'=>'11',
        '孙子/孙女/外孙/外孙女'=>'12',
        '婶/姨/姑'=>'13',
        '侄子/侄女/外甥/外甥女'=>'14',
        '法定监护人'=>'15',
        '其他'=>'16',
        '雇佣关系'=>'17',
        '父母'=>'18',
	    '配偶'=>'19',
	    '子女'=>'20',
	    '祖孙'=>'21',
        '雇员'=>'31'


    ],
    //性别
    'sex'=>[
        '男'=>'1',
        '女'=>'0',
    ],
    //被保人证件类型
    'type'=>[
        '身份证'=>'1',
        '护照'=>'2',
        '军官证'=>'3',
        '其他'=>'4',
        '组织机构代码'=>'11',
        '工商注册号码'=>'12',
        '税务登记证'=>'13',
        '统一社会信用代码'=>'14',
        '营业执照'=>'15'
    ],
    //算费因子
    'quote_base'=>[
        '缴别'=>'ty_pay_way',
        '保期类型'=>'ty_duration_period_type',
        '保障期限'=>'ty_duration_period_value',
        '出生日期'=>'ty_birthday',
        '性别'=>'ty_sex',
        '年龄'=>'ty_age',
        '职业'=>'ty_job',
        '购买份数'=>'ty_buy_count',
        '保险介绍'=>'ty_ins_descrip',
        '适用人群'=>'ty_for_people',
        '保障额度'=>'ty_ins_guarantee_amount',
        '居住城市'=>'ty_area',
        '保额'=>'ty_ins_coverage',
        '有无社保'=>'ty_social_security',
        '职业类别'=>'ty_work_type'
    ],
    //投保属性
    'ins_base'=>[
        //基础信息
        'ty_base'=>[
            '起保日期'=>'ty_start_date',
        ],
        //投保人信息
        'ty_toubaoren'=>[
            '投保人名称'=>'ty_toubaoren_name',
            '投保人证件类型'=>'ty_toubaoren_id_type',
            '投保人证件号'=>'ty_toubaoren_id_number',
            '投保人生日'=>'ty_toubaoren_birthday',
            '投保人性别'=>'ty_toubaoren_sex',
            '投保人电话'=>'ty_toubaoren_phone',
            '投保人职业'=>'ty_toubaoren_job',
            '投保人邮箱'=>'ty_toubaoren_email',
            '投保人所在地'=>'ty_toubaoren_area',
            '详细地址'=>'ty_toubaoren_address',
            '联系地址邮编'=>'ty_toubaoren_contact_post',
            '与被保人关系'=>'ty_toubaoren_relation',
            '国籍'=>'ty_toubaoren_native',
            '年收入(万元)'=>'ty_toubaoren_income',
            '健康告知'=>'ty_toubaoren_health',
            '身高（cm）'=>'ty_toubaoren_height',
            '体重(kg)'=>'ty_toubaoren_weight',

            '企业名称'=>'ty_toubaoren_name',
            '企业证件类型'=>'ty_toubaoren_id_type',
            '企业证件号'=>'ty_toubaoren_id_number',
            '企业联系电话'=>'ty_toubaoren_phone',
            '企业联系邮箱'=>'ty_toubaoren_email',

        ],
        //被保人信息
        'ty_beibaoren'=>[
            '被保人名称'=>'ty_beibaoren_name',
            '关系'=>'ty_relation',
            '被保人证件类型'=>'ty_beibaoren_id_type',
            '被保人证件号'=>'ty_beibaoren_id_number',
            '被保人生日'=>'ty_beibaoren_birthday',
            '被保人性别'=>'ty_beibaoren_sex',
            '被保人电话'=>'ty_beibaoren_phone',
            '被保人职业'=>'ty_beibaoren_job',
            '被保人邮箱'=>'ty_beibaoren_email',
            '被保人所在地'=>'ty_beibaoren_area',
            '被保人详细地址'=>'ty_beibaoren_address',
            '联系地址邮编'=>'ty_beibaoren_contact_post',
            '身高(cm)'=>'ty_beibaoren_height',
            '体重(kg)'=>'ty_beibaoren_weight',
            '国籍'=>'ty_beibaoren_native',
            '年收入'=>'ty_beibaoren_income',
            '健康告知'=>'ty_beibaoren_health',
        ],
        //受益人信息
        'ty_shouyiren'=>[
            '受益人类型'=>'ty_shouyiren_type',
            '受益人姓名'=>'ty_shouyiren_name',
            '受益人证件号'=>'ty_shouyiren_id_number',
            '受益人证件类型'=>'ty_shouyiren_id_type',
            '受益人地址'=>'ty_shouyiren_address',
            '受益人生日'=>'ty_shouyiren_birthday',
            '受益人性别'=>'ty_shouyiren_sex',
            '受益人比例'=>'ty_shouyiren_proportion',
            '受益人顺序'=>'ty_shouyiren',
            '与被保人关系'=>'ty_shouyiren_relation_beibaoren',
        ],
        //续保信息
        'ty_xubao'=>[
            '支付银行'=>'ty_renewal_pay_bank',
            '银行卡所属人'=>'ty_renewal_card_holder',
            '支付卡号'=>'ty_renewal_pay_account',
            '银行详细地址'=>'ty_bank_address',
        ],
    ],
];
