<?php
namespace app\api\model;


class Comment extends BaseModel
{
    protected $columns_filter = ['order_id', 'talent_id', 'user_id', 'star', 'content' , 'games_total', 'games_win'];

    protected $hidden = ['id', 'order_id','talent_id', 'user_id', 'delete_time'];

    public function orderObj()
    {
        return $this->belongsTo('Order', 'order_id');
    }

    public function talent()
    {
        return $this->belongsTo('talent', 'talent_id');
    }

    public function user()
    {
        return $this->belongsTo('user', 'user_id');
    }

    public function commentTags($with=null)
    {
        return $this->hasMany('CommentTag', 'comment_id', 'id')->with($with);
    }
}