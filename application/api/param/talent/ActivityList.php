<?php

namespace app\api\param\talent;

use think\Validate;

class ActivityList extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'fresh_type' => 'require|integer|in:0,1',
        'since_id' => 'require|integer',
    ];

    public $doc = [
        'description' => '接口使用fresh_type 和 since_id联合实现分页加载。当fresh_type=0时，返回最新10条记录，并返回since_id的值。加载下一页时设置fresh_type=1，并赋值 since_id为上一次请求返回的值',
        'url' => '/api/v1/activity/list/:talent_id',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'0', 'desc'=> '用户ID，未登录传0'],
            'fresh_type' => ['default'=>'0', 'desc'=> '为“1”时使用since_id获取之后数据'],
            'since_id' => ['default'=>'0', 'desc'=> '用于获取since_id时间之后的10条数据'],
        ],
    ];
}