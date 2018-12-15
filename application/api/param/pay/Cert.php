<?php
namespace app\api\param\pay;
use think\Validate;

class Cert extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'device_info' => 'max:32',
        'spbill_create_ip' => 'ip',
    ];

    public $doc = [
        'description' => '大神认证费支付参数和预支付<br/>客户端支付完成后需重新请求 《2-4.大神基本详情》接口以通知服务器',
        'url' => '/api/v1/pay/cert',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> ''],
            'device_info' => ['default'=>'', 'desc'=> '终端设备号(门店号或收银设备ID)'],
            'spbill_create_ip' => ['default'=>'', 'desc'=> '用户端实际ip'],
        ],
    ];
}