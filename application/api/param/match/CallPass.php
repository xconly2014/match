<?php

namespace app\api\param\match;

use think\Validate;

class CallPass extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'ticket_id' => 'require|integer',
    ];

    public $doc = [
        'url' => '/api/v1/match/callPass',
        'method' => 'POST',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
            'ticket_id' => ['default'=>'', 'desc'=> '入场券id'],
        ],
    ];
}