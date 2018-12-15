<?php
namespace app\api\param\other;
use think\Validate;

class wxMiniOpenid extends Validate
{
    public $rule = [
        'code' => 'require|alphaDash|max:32',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/wechat/openid',
        'method' => 'GET',
        'param' => [
            'code' => ['default'=>'', 'desc'=> '微信小程序登录时获取code,code只能用一次'],
        ],
    ];
}