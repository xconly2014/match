<?php
namespace app\api\param\talent;
use think\Validate;

class SwitchOn extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'switch_on' => 'require|in:0,1',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/talent/:id/switch',
        'method' => 'PUT',
        'param' => [
            'uid' => ['default'=>'', 'desc'=> '用户ID'],
            'switch_on' => ['default'=>'', 'desc'=> '接单开关 1.打开 0.关闭'],
        ],
    ];
}