<?php
namespace app\api\param\talent;
use think\Validate;

class Random extends Validate
{
    public $rule = [
        'total_num' => 'number',
        'call_id' => 'require|alphaDash|max:63',
    ];

    public $doc = [
        'description' => '用户在呼叫大神时没人接单，则随机获取',
        'url' => '/api/v1/talent/random',
        'method' => 'GET',
        'param' => [
            'total_num' => ['default'=>'', 'desc'=> '获取人数'],
            'call_id' => ['default'=>'', 'desc'=> '呼叫老司机 呼叫ID'],
        ],
    ];
}