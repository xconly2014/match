<?php

namespace app\api\param\pay;


use think\Validate;

class WxPayMP extends Validate
{
    public $rule = [
        'device_info' => 'max:32',
        'order_id' => 'require|max:32',
        'spbill_create_ip' => 'ip',
    ];

    public $doc = [
        'url' => '/api/v1/pay/wx_pay_mp',
        'method' => 'GET',
        'param' => [
            'device_info' => [ 'default'=>'APP','desc'=> '终端设备号(门店号或收银设备ID)，默认请传"WEB"'],
            'order_id' => [ 'default'=>'','desc'=> '订单ID'],
            'spbill_create_ip' => [ 'default'=>'','desc'=> '用户端实际ip'],
        ],
        'description' => '使用MD5类型签名，微信小程序支付文档：https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=7_7&index=5<br /> 请自行设置appid 和 mch_id,app_id,app_secret',
    ];
}