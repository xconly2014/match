<?php
namespace app\api\param\other;
use think\Validate;

class userGameInfo extends Validate
{
    public $rule = [
        'role_id' => 'require|integer',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/game/userInfo/role_id',
        'method' => 'GET',
        'param' => [],
    ];
}