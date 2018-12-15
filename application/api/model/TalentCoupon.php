<?php

namespace app\api\model;


use think\Model;

class TalentCoupon extends Model
{
    protected $hidden = ['delete_time'];

    public function coupon()
    {
        return $this->hasOne("UserCoupon","coupon_id","coupon_id");
    }

    public function getById($id)
    {
        return self::get($id);
    }

}