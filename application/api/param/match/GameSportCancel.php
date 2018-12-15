<?php
namespace app\api\param\match;

use think\Validate;

class GameSportCancel extends Validate
{
    public $rule = [
        'match_id' => 'require|integer',
    ];
    public $doc = [
        'description' => '赛事取消',
        'url' => '/api/v1/match/gameSportCancel',
        'method' => 'POST',
        'param' => [
            'match_id' => ['default' => '0', 'desc' => '小程序内赛事编号'],
        ],
    ];
}