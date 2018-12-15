<?php
namespace app\api\param\pay;
use think\Validate;

class BalanceTop extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'gold_num' => 'require|float|>:0',
        'device_info' => 'max:32',
        'spbill_create_ip' => 'ip',
    ];

    public $doc = [
        'description' => '接口返回支付所需参数，请自行设置appid或mch_id',
        'url' => '/api/v1/balance/top',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> ''],
            'gold_num' => ['default'=>'', 'desc'=> '充值金币数'],
            'device_info' => ['default'=>'APP', 'desc'=> '终端设备号(门店号或收银设备ID)，默认请传\"WEB\"'],
            'spbill_create_ip' => ['default'=>'', 'desc'=> '用户端实际ip'],
        ],
    ];
}