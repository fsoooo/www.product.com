<?php
/**
 * 固定的属性状态
 */

return [
    'order'=>[  //订单状态
//        'all'=>0,     //所有订单，仅仅用来做判断
        'payed'=>1, //已支付
        'unpayed'=>2, //未支付
        'fail'=>3,   //支付失败
        'check_ing' =>4,
        'check_end' =>5,
        'check_error' =>6
//        'insuring'=>3, //保障中
//        'feedback'=>4,  //待评价
//        'renewal'=>5,   //带续保，已过期


    ],
    'message'=>[   //站内信
        'unread' =>0, //未读
        'read' => 1, //已读
    ],
    'cancel_type'=>[   //退保类型
        'hesitation'=>1,  //在犹豫期内
        'out_hesitation'=>0, //不再犹豫期内
    ]
];