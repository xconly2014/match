<?php

namespace app\api\model;


use think\Model;

class UserCoupon extends Model
{

    public function talent()
    {
        return $this->belongsTo("Talent","talent_id");
    }

    public function user()
    {
        return $this->belongsTo("User","user_id");
    }

    public function activity()
    {
        return $this->belongsTo("TalentActivity","activity_id");
    }

    public function coupon(){
        return $this->belongsTo("TalentCoupon","coupon_id");
    }

}