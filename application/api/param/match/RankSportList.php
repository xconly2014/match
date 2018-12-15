<?php

namespace app\api\param\match;

use think\Validate;

class RankSportList extends Validate
{
    public $rule = [
//        'uid' => 'require|alphaDash|max:32',
//        'fee' => 'require|integer',
        'match_id' => 'require|integer',
    ];

    public $doc = [
        'description' => '赛事排名',
        'url' => '/api/v1/match/rankSportList',
        'method' => 'POST',
        'param' => [
//            'uid' => ['default'=>'0', 'desc'=> '用户ID，未登录传0'],
//            'fee' => ['default'=>'100', 'desc'=> '赛事报名费'],
            'match_id' => ['default'=>'0'],
        ],
    ];
}