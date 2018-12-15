<?php
namespace app\api\param\pay;

use think\Validate;

class AliPay extends Validate
{
    public $rule = [
        'order_id' => 'require|max:32',
    ];

    public $doc = [
        'url' => '/api/v1/pay/alipay',
        'method' => 'GET',
        'param' => [
            'order_id' => [ 'default'=>'','desc'=> '订单ID'],
        ],
        'description' => '若验证签名失败，请确认请求支付宝时是否多出参数  请自行设置app_id',
    ];
}