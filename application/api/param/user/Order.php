<?php
# Login.php
namespace app\api\param\user;

use think\Validate;

class Order extends Validate
{
    public $rule = [
        'fresh_type' => 'require|in:0,1',
        'since_id' => 'require|integer',
        'data_type' => 'require|in:1,2',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/user/:uid/order',
        'method' => 'GET',
        'param' => [
            'fresh_type' => ['default'=>'0', 'desc'=> '为“1”时使用since_id获取之后数据'],
            'since_id' => ['default'=>'0', 'desc'=> '用于获取since_id时间之后的10条数据'],
            'data_type' => ['default'=>'1', 'desc'=> '1.我发起的。2.我收到的'],
        ],
    ];
}