<?php
# 订单消息表

namespace app\api\model;


class SysMessage extends BaseModel
{
    protected $columns_filter = ['user_id', 'type', 'content', 'extras'];


    public function getListByUserId($user_id, $since_id = 0, $length = 10) {
        $fun = function($obj, $user_id, $since_id) {
            $obj->where('user_id', $user_id);
            if($since_id > 0) {
                $obj->where('id', '<', $since_id);
            }
        };
        $fun($this, $user_id, $since_id);
        $this->update(['read_flag'=>1]);
        $fun($this, $user_id, $since_id);
        return $this->order('id DESC')->limit($length)->select();
    }
}