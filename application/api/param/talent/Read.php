<?php
# Login.php
namespace app\api\param\talent;

use think\Validate;

class Read extends Validate
{
    public $rule = [
        'id'  =>  'require|integer',
        'uid' => 'require|alphaDash|max:32',
        'fresh_type' => 'require|in:0,1',
        'since_id' => 'require|integer',
    ];

    public $doc = [
        'url' => '/api/v1/talent/:id',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'0', 'desc'=> '用户ID，未登录传0'],
            'fresh_type' => ['default'=>'0', 'desc'=> '为“1”时使用since_id获取之后的评论数据'],
            'since_id' => ['default'=>'0', 'desc'=> '用于获取since_id时间之后的10条数据'],
        ],
    ];
}