<?php

namespace app\api\param\match;

use think\Validate;

class GameSportComplete extends Validate
{
    public $rule = [
        'match_id' => 'require|integer',
    ];
    public $doc = [
        'description' => '赛事完成',
        'url' => '/api/v1/match/gameSportComplete',
        'method' => 'POST',
        'param' => [
            'match_id' => ['default' => '0', 'desc' => '小程序内赛事编号'],
        ],
    ];
}