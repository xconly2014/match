<?php

namespace app\api\param\match;

use think\Validate;

class Call extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'match_id' => 'require|integer',
    ];

    public $doc = [
        'url' => '/api/v1/match/call',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
            'match_id' => ['default'=>'', 'desc'=> '赛事id'],
        ],
    ];
}