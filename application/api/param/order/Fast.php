<?php
# Login.php
namespace app\api\param\order;

use think\Validate;

class Fast extends Validate
{
    public $rule = [
        'talent_max_level'  =>  'require|number|between:1,7',
    ];

    public $doc = [
        'url' => '/api/v1/fast',
        'method' => 'GET',
        'param' => [
            'talent_max_level' => ['default'=>'7', 'desc'=> '大神最高段位：1.青铜,2.白银,3.黄金,4.铂金,5.钻石,6.星耀,7.王者'],
        ],
    ];
}