<?php
# Login.php
namespace app\api\param\talent;

use think\Validate;

class Lister extends Validate
{
    public $rule = [
        'uid' => 'require|alphaDash|max:32',
        'fresh_type' => 'require|integer|in:0,1',
        'since_id' => 'require|integer',
        'data_type' => 'require|integer|in:1,2',
        'keyword' => 'max:30',
        'max_level' => 'chsAlphaNum|max:20',
        'area' => 'chsAlphaNum|max:20',
        'system' => 'chsAlphaNum|max:20',
    ];

    public $doc = [
        'description' => '接口使用fresh_type 和 since_id联合实现分页加载。当fresh_type=0时，返回最新10条记录，并返回since_id的值。加载下一页时设置fresh_type=1，并赋值 since_id为上一次请求返回的值',
        'url' => '/api/v1/talent',
        'method' => 'GET',
        'param' => [
            'uid' => ['default'=>'0', 'desc'=> '用户ID，未登录传0'],
            'fresh_type' => ['default'=>'0', 'desc'=> '为“1”时使用since_id获取之后数据'],
            'since_id' => ['default'=>'0', 'desc'=> '用于获取since_id时间之后的10条数据'],
            'data_type' => ['default'=>'1', 'desc'=> '1.大神。2.美女'],
            'keyword' => ['default'=>'', 'desc'=> '搜索关键词'],
            'max_level' => ['default'=>'', 'desc'=> '大神段位 7.王者,6.星耀,5.钻石,4.铂金,3.黄金,2.白银,1.青铜'],
            'area' => ['default'=>'', 'desc'=> '服务区服 0.双区服 1.QQ 2.微信'],
            'system' => ['default'=>'', 'desc'=> '手机系统 0.双系统 1.安卓系统 2.苹果系统'],
        ],
    ];
}