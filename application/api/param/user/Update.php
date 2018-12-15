<?php
# Login.php
namespace app\api\param\user;

use think\Validate;

class Update extends Validate
{
    public $rule = [
        'icon' => 'url',
        'role_id' => 'number|max:11',
        'role_name' => 'max:20',
    ];

    public $doc = [
        'description' => '各参数传空表示删除数据，不传表示不做修改。',
        'url' => '/api/v1/user/:uid',
        'method' => 'PUT',
        'param' => [
            'icon' => [ 'default'=>'', 'desc'=> '头像url'],
            'role_id' => ['default'=>'', 'desc'=> '角色ID'],
            'role_name' => ['default'=>'', 'desc'=> '角色名'],
        ],
    ];

}