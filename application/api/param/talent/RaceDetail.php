<?php
namespace app\api\param\talent;
use think\Validate;

class RaceDetail extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/talent/race/:id',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '接单大神的uid'],
        ],
    ];
}