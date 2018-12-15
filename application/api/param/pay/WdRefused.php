<?php
namespace app\api\param\pay;
use think\Validate;

class WdRefused extends Validate
{
    public $rule = [
        'uid' => 'require',
        'amount' => 'require|float|>=:2',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/income/wdRefused',
        'method' => 'POST',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
            'amount' => ['default'=>'', 'desc'=> '提现金额（元），大于等于2的浮点数.'],
        ],
    ];
}