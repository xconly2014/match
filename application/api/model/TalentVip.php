<?php

namespace app\api\model;


class TalentVip extends BaseModel
{

    public function user()
    {
        return $this->belongsTo("Use","user_id");
    }

    public function talent()
    {
        return $this->belongsTo("Talent","talent_id");
    }
}