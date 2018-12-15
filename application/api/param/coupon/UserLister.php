<?php
namespace app\api\param\coupon;


use think\Validate;

class UserLister extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'talent_id' => 'require|integer',
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/coupon/user/:uid',
        'method' => 'GET',
        'param' => [
            'talent_id' => [ 'default'=>'','desc'=> '大神ID'],
        ],
    ];
}