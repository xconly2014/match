<?php
namespace app\api\param\talent;
use think\Validate;

class CancelCall extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/talent/call',
        'method' => 'DELETE',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
        ],
    ];
}