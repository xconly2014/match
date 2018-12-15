<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 07:32:10
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message=[
        'code' => '没有code.'
    ];
}