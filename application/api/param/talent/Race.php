<?php
namespace app\api\param\talent;
use think\Validate;

class Race extends Validate
{
    public $rule = [
        'id' => 'require|alphaDash|max:63',
        'uid' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'description' => '具体呼叫和抢单推送消息见：<a target="_blank" href="/api/docs/doc#Jpush-Extras">说明文档</a>',
        'url' => '/api/v1/talent/race',
        'method' => 'GET',
        'param' => [
            'id' => ['default'=>'', 'desc'=> '呼叫ID，call_id, 呼叫推送（type=201）的扩展字段extras获得'],
            'uid' => ['default'=>'', 'desc'=> '接单大神的uid'],
        ],
    ];
}