<?php

namespace app\api\param\match;

use think\Validate;

class Detail extends Validate
{
    public $rule = [
        'id'  =>  'require|integer',
        'uid' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'url' => '/api/v1/match/:id',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'0', 'desc'=> '用户ID，未登录传0'],
        ],
    ];
}