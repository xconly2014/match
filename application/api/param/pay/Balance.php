<?php
namespace app\api\param\pay;
use think\Validate;

class Balance extends Validate
{
    public $rule = [
        'order_id' => 'require|alphaNum|max:32',
        'uid' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'description' => '使用账户余额完成支付',
        'url' => '/api/v1/pay/balance',
        'method' => 'POST',
        'param' => [
            'order_id' => ['default'=>'', 'desc'=> '支持O开头的约车订单号，T开头的大神认证申请订单号'],
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
        ],
    ];
}