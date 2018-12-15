<?php
# Login.php
namespace app\api\param\talent;

use think\Validate;

class Create extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'max_level' => 'chsAlphaNum|max:20',
        'area' => 'chsAlphaNum|max:20',
        'system' => 'chsAlphaNum|max:20',
        'hero' => 'max:100',
        'honour' => 'max:200',
        'honour_image' => 'url',
        'idcard_image' => 'url',
        'wx_qrcode' => 'url',
        'cert_sex' => 'number|in:1,2',
        'wx_account' => 'alphaDash|max:30',
        'tel' => 'number|between:10000000000,19999999999',
        'qq_account' => 'alphaDash|max:30',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/talent',
        'method' => 'POST',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
            'max_level' => ['default'=>'', 'desc'=> '最高段位 7.王者,6.星耀,5.钻石,4.铂金,3.黄金,2.白银,1.青铜'],
            'area' => ['default'=>'', 'desc'=> '服务区服：0.双区服，1.QQ，2.微信.'],
            'system' => ['default'=>'', 'desc'=> '手机系统 0.双系统 1.安卓系统 2.苹果系统'],
            'hero' => ['default'=>'', 'desc'=> '擅长英雄'],
            'honour' => ['default'=>'', 'desc'=> '荣耀成就'],
            'honour_image' => ['default'=>'', 'desc'=> '最高段位截图url'],
            'idcard_image' => ['default'=>'', 'desc'=> '身份证照片'],
            'wx_qrcode' => ['default'=>'', 'desc'=> '微信二维码'],
            'cert_sex' => ['default'=>'', 'desc'=> '认证性别:1.男,2.女'],
            'wx_account' => ['default'=>'', 'desc'=> '微信账号'],
            'qq_account' => ['default'=>'', 'desc'=> 'QQ账号'],
            'tel' => ['default'=>'', 'desc'=> '手机号'],
        ],
    ];
}