<?php

namespace app\api\param\coupon;


use think\Validate;

class Lister extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/coupon/list',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'0', 'desc'=> '用户ID，未登录传0'],
        ],
    ];
}