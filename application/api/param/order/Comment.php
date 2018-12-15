<?php
# Login.php
namespace app\api\param\order;

use think\Validate;

class Comment extends Validate
{
    public $rule = [
        'uid'  =>  'require|alphaDash|max:32',
        'star' => 'require|float|between:0,5',
        'tags' => 'require|regex:\[.*\]',
        'content' => 'require|max:250',
        'games_total'  =>  'require|integer|>=:1',
        'games_win'  =>  'require|integer|>=:0',
    ];

    public $doc = [
        'url' => '/api/v1/order/:order_id/comment',
        'method' => 'POST',
        'param' => [
            'uid' => [ 'default'=>'','desc'=> '用户ID'],
            'star' => ['default'=>'5','desc'=> '评分，星级'],
            'tags' => ['default'=>'','desc'=> 'json数组字符串形式，示例：["Carry全场","溜","躺赢"]'],
            'content' =>  ['default'=>'','desc'=> '评论内容, 250字以内'],
            'games_total' => ['default'=>'1','desc'=> '游戏总场次，不能小于1场'],
            'games_win' => ['default'=>'0','desc'=> '胜利场次'],
        ],
    ];
}