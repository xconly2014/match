<?php

namespace app\api\model;


class TalentActivity extends  BaseModel
{
    protected $columns_filter = ['talent_id','title','content','image1','image2','image3','begin_time','end_time','send_time'];
    protected $hidden = ['delete_time'];

    public function talent()
    {
        return $this->belongsTo('Talent','talent_id');
    }

    public function coupon()
    {
        return $this->hasOne("UserCoupon","coupon_id","coupon_id");
    }
}