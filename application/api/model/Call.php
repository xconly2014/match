<?php
# Call.php

namespace app\api\model;


class Call extends BaseModel
{
    protected $columns_filter = ['user_id', 'max_level', 'area', 'system', 'round'];

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    public function getById($id) {
        return self::get($id);
    }

    public function getMaxLevelAttr($value) {
        $tuple = [1=>'倔强青铜', 2=>'秩序白银', 3=>'荣耀黄金', 4=>'尊贵铂金', 5=>'永恒钻石', 6=>'至尊星耀', 7=>'最强王者'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setMaxLevelAttr($value) {
        if(is_numeric($value)) {
            $result = $value;
        } elseif(strpos($value, '王者') !== false) {
            $result = 7;
        } elseif(strpos($value, '星耀') !== false) {
            $result = 6;
        } elseif(strpos($value, '钻石') !== false) {
            $result = 5;
        } elseif(strpos($value, '铂金') !== false) {
            $result = 4;
        } elseif(strpos($value, '黄金') !== false) {
            $result = 3;
        } elseif(strpos($value, '白银') !== false) {
            $result = 2;
        } elseif(strpos($value, '青铜') !== false) {
            $result = 1;
        } else {
            $result = 1;
        }
        return $result;
    }

    public function getAreaAttr($value) {
        $tuple = ['双区服', 'QQ', '微信'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setAreaAttr($value) {
        if(is_numeric($value)) {
            $result = $value;
        } elseif( mb_strpos($value, 'QQ', 0, 'utf-8') !== false) {
            $result = 1;
        } elseif(mb_strpos($value, '微信', 0, 'utf-8') !== false) {
            $result = 2;
        } else {
            $result = 0;
        }
        return $result;
    }

    public function getSystemAttr($value) {
        $tuple = ['双系统', '安卓系统', '苹果系统'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setSystemAttr($value) {
        if(is_numeric($value)) {
            $result = $value;
        } elseif( strpos($value, '安卓') !== false || stripos($value, 'android') !== false) {
            $result = 1;
        } elseif(strpos($value, '苹果') !== false || stripos($value, 'ios') !== false) {
            $result = 2;
        } else {
            $result = 0;
        }
        return $result;
    }

    public function delByUserId($user_id) {
        return $this->where('user_id', $user_id)->update(['delete_time' => time()] );
    }

    /**
     * 删除超时呼叫
     */
    public function delOverTimeRows() {
        $t = time();
        $this->where('create_time', '<=', $t - 10 * 60)->update(['delete_time'=>$t]);
    }
}