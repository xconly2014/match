<?php
namespace app\api\param\vip;


use think\Validate;

class UserList extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/vip/user',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'0', 'desc'=> '用户ID，未登录传0'],
        ],
    ];
}