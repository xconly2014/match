<?php
# Login.php
namespace app\api\param\user;

use think\Validate;

class Read extends Validate
{
    public $rule = [
        'uid'  =>  'require|alphaDash|max:32',
    ];

    public $doc = [
        'url' => '/api/v1/user/:uid',
        'method' => 'GET',
    ];
}