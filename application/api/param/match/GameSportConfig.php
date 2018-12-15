<?php

namespace app\api\param\match;

use think\Validate;

class GameSportConfig extends Validate
{
    public $rule = [
        'fee' => 'require|integer',
        'gift_id1' => 'require|alphaDash',
        'gift_id2' => 'require|alphaDash',
        'match_id' => 'require|integer',
        'banner' => 'require|alphaDash',
    ];

    public $doc = [
        'description' => '赛事配置',
        'url' => '/api/v1/match/gameSportConfig',
        'method' => 'POST',
        'param' => [
            'fee' => ['default' => '10', 'desc' => '赛事费用,单位是元.默认是10'],
            'gift_id1' => ['default' => '', 'desc' => '报名赠送的礼包,格式 道具id*道具数量'],
            'gift_id2' => ['default' => '', 'desc' => '报名赠送的礼包,格式 道具id*道具数量'],
            'match_id' => ['default' => '0', 'desc' => '游戏内赛事编号,每场比赛对应的唯一值.格式如 2018021851'],
            'banner' => ['default' => 'http://resource.mp.cmswat.com/images/icon/card_1.png', 'desc' => '赛事图片链接,可用七牛云上传'],
        ],
    ];
}