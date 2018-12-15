<?php
# Video.php

namespace app\api\model;


class Video extends BaseModel
{
    /**
     * 获取列表
     * @param int $since_id
     * @param int $length
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getList($since_id = 0, $length = 10) {
        if($since_id > 0) {
            $this->where('id', '<', $since_id);
        }
        return $this->order('id DESC')->limit($length)->select();
    }
}