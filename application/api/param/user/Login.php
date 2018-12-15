<?php
# Login.php
namespace app\api\param\user;

use think\Validate;

class Login extends Validate
{
    public $rule = [
        'type' => 'require|number|in:0,1,2,3',
        'token'  =>  'require|alphaDash|max:255',
        'nick' =>   'max:50',
        'icon' => 'url',
        'level0' => 'alphaDash|max:32',
//        'sex' => 'number|in:1,2',
//        'reg_id' => 'chsDash|max:255',
    ];

    public $doc = [
        'description' => '使用QQ和微信登录<br/>当用户在多个设备登录时，登录成功后会给旧设备发送extras.type = 0 的极光推送消息',
        'url' => '/api/v1/user/login',
        'method' => 'POST',
        'param' => [
            'type' => ['default'=>'', 'desc'=> '1.微信登录，2.QQ登录'],
            'token'  =>  ['default'=>'', 'desc'=> '第三方登录OpenId等，微信应使用unionid'],
            'nick' =>   ['default'=>'', 'desc'=> '昵称'],
            'icon' => ['default'=>'', 'desc'=> '头像'],
            'level0' => ['default'=>'', 'desc'=> '邀请人'],
//            'sex' => ['default'=>'', 'desc'=> '性别:1.男2.女'],
//            'reg_id' => ['default'=>'', 'desc'=> '极光RegistrationId'],
        ],
    ];

}