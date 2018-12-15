<?php
# Login.php
namespace app\api\param\order;

use think\Validate;

class Read extends Validate
{
    public $rule = [
        'order_id' => 'require|alphaNum|max:32',
        'uid'  =>  'require|alphaDash|max:32',
    ];

    public $doc = [
        'url' => '/api/v1/order/:order_id',
        'method' => 'GET',
        'description' => '该接口可能会主动查询第三方支付交易。因此该接口可能被阻塞延迟',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
        ],
    ];
}