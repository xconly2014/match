<?php
namespace app\api\param\talent;
use think\Validate;

class Audit extends Validate
{
    public $rule = [
        'uid' => 'require|max:32',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/talent/audit',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '登录UID'],
        ],
    ];
}