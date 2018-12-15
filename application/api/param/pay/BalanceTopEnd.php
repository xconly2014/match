<?php
namespace app\api\param\pay;
use think\Validate;

class BalanceTopEnd extends Validate
{
    public $rule = [
        'order_id' => 'require|max:64',
        'pay_type' => 'require|number|in:1,2',
    ];

    public $doc = [
        'description' => '通知服务器一笔充值订单完成，服务器主动查询支付方',
        'url' => '/api/v1/balance/topEnd',
        'method' => 'GET',
        'param' => [
            'order_id' => ['default'=>'', 'desc'=> '充值订单号'],
            'pay_type' => ['default'=>'', 'desc'=> '充值方式：1.微信，2.支付宝'],
        ],
    ];
}