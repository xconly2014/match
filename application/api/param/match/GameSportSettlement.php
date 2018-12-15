<?php
namespace app\api\param\match;

use think\Validate;

class GameSportSettlement extends Validate
{
    public $rule = [
        'uid'  =>  'require|alphaDash|max:32',
        'match_id' => 'require|integer',
    ];
    public $doc = [
        'description' => '赛事结算',
        'url' => '/api/v1/match/gameSportSettlement',
        'method' => 'GET',
        'param' => [
            'uid' => ['default' => '0', 'desc' => ''],
            'match_id' => ['default' => '0', 'desc' => ''],
        ],
    ];
}