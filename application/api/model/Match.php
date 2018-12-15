<?php

namespace app\api\model;


class Match extends BaseModel
{

    public function tickets($with=null)
    {
        return $this->hasOne('userTicket','id')->with($with);
    }
    /**
     *  赛事列表
     */
    public function getList($svrid=500035,$since_id=0, $length=10)
    {
        $query = $this->where('status',0)->where('svrid',$svrid);
        $since_id > 0 && $query->where('id', '>', $since_id);
        return $query->order('eming ASC')->limit($length)->select();
    }

    public function getMatchById($idMatch)
    {
        return $this->where("id",$idMatch)->find();
    }
}