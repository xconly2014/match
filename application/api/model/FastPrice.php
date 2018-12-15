<?php
# FastPrice.php

namespace app\api\model;


use think\Model;

class FastPrice extends Model
{
    public function getTypeAttr($value)
    {
        $tuple = [0=>'语音指导', 1=>'娱乐局', 2=>'白银局', 3=>'黄金局', 4=>'铂金局', 5=>'钻石局', 6=>'星耀局', 7=>'王者局'];
        return $tuple[$value];
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

    /**
     *
     * @param int $max_level
     * @return false|static[]
     */
    public function selectByMaxLevel($max_level = 7) {
        return self::all(['max_level'=> $max_level]);
    }

    /**
     * 获取某配置下的价格
     * @param $max_level
     * @param $type
     * @return mixed
     */
    public function getPrice($max_level, $type) {
        return $this->where(['max_level'=> $max_level, 'type'=> $type])->value('price');
    }
}