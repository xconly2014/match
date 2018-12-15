<?php
# Login.php
namespace app\api\param\user;

use think\Validate;

class UpdatePhone extends Validate
{
    public $rule = [
        'iv' => 'require|length:24',
        'encryptedData' => 'require|length:216',
        'session_key' => 'require|length:24',
    ];

    public $doc = [
        'description' => '小程序获取电话号码 https://developers.weixin.qq.com/miniprogram/dev/api/getPhoneNumber.html?search-key=getPhoneNumber',
        'url' => '/api/v1/user/:uid/phone',
        'method' => 'PUT',
        'param' => [
            'iv' => [ 'default'=>'', 'desc'=> '矢量值'],
            'encryptedData' => [ 'default'=>'', 'desc'=> '待解密数据'],
            'session_key' => [ 'default'=>'', 'desc'=> '会话密钥'],
        ],
    ];

}