<?php
# Receive.php

namespace app\api\param\order;

use think\Validate;

class Finish extends Validate
{
    public $rule = [
        'order_id' => 'require|alphaNum|max:32',
        'uid'  =>  'require|alphaDash|max:32',
    ];

    public $doc = [
        'url' => '/api/v1/order/:order_id/finish',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
        ],
    ];
}