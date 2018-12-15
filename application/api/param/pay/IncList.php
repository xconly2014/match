<?php
namespace app\api\param\pay;
use think\Validate;

class IncList extends Validate
{
    public $rule = [
        'fresh_type' => 'require|in:0,1',
        'since_id' => 'require|integer',
        'uid' => 'require',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/income',
        'method' => 'GET',
        'param' => [
            'fresh_type' => ['default'=>'', 'desc'=> '为“1”时使用since_id获取之后数据'],
            'since_id' => ['default'=>'', 'desc'=> '用于获取since_id时间之后的10条数据'],
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
        ],
    ];
}