<?php
namespace app\api\param\talent;
use think\Validate;

class Call extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'max_level' => 'require|chsAlphaNum|max:20',
        'area' => 'require|chsAlphaNum|max:20',
        'system' => 'require|chsAlphaNum|max:20',
        'round' => 'require|integer|>=:1',
    ];

    public $doc = [
        'description' => '具体呼叫和抢单推送消息见：<a target="_blank" href="/api/docs/doc#Jpush-Extras">说明文档</a>',
        'url' => '/api/v1/talent/call',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
            'max_level' => ['default'=>'', 'desc'=> '段位 7.王者,6.星耀,5.钻石,4.铂金,3.黄金,2.白银,1.青铜'],
            'area' => ['default'=>'', 'desc'=> '服务区服：0.双区服，1.QQ，2.微信.'],
            'system' => ['default'=>'', 'desc'=> '手机系统 0.双系统 1.安卓系统 2.苹果系统'],
            'round' => ['default'=>'', 'desc'=> '游戏局数'],
        ],
    ];
}