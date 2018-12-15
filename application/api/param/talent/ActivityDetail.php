<?php

namespace app\api\param\talent;

use think\Validate;

class ActivityDetail extends Validate
{
    public $rule = [
        'id'  =>  'require|integer',
        'uid' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/activity/:id',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'0', 'desc'=> '用户ID，未登录传0'],
        ],
    ];
}