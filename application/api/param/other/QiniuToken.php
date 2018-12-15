<?php
namespace app\api\param\other;
use think\Validate;

class QiniuToken extends Validate
{
    public $rule = [
        'bucket' => 'alphaDash|max:32',
        'key' => '',
        'expires' => 'integer|gt:0',
    ];

    public $doc = [
        'description' => '参考：https://developer.qiniu.com/kodo/sdk/1241/php#4',
        'url' => '/api/v1/qiniu/token',
        'method' => 'POST',
        'param' => [
            'bucket' => ['default'=>'', 'desc'=> '储存空间,不传或传空则使用服务器配置的默认空间'],
            'key' => ['default'=>'', 'desc'=> '覆盖上传的文件名，覆盖七牛云中已存在的文件'],
            'expires' => ['default'=>'', 'desc'=> '凭证有效期(秒)，默认3600'],
        ],
    ];
}