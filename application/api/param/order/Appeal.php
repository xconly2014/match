<?php
namespace app\api\param\order;
use think\Validate;

class Appeal extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'content' => 'require|max:500',
        'proof_0' => 'url',
        'proof_1' => 'url',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/order/:order_id/appeal',
        'method' => 'POST',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> ''],
            'content' => ['default'=>'', 'desc'=> '申诉内容'],
            'proof_0' => ['default'=>'', 'desc'=> '证据截图1'],
            'proof_1' => ['default'=>'', 'desc'=> '证据截图2'],
        ],
    ];
}