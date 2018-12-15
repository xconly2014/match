<?php
namespace app\api\model;

class UserRank extends BaseModel
{
    /**
     *  赛事列表
     */
    public function getList($since_id=0, $length=10)
    {
        $now = date("YmdHis",time());
        $query = $this->where('efight','>',$now);
        $since_id > 0 && $query->where('id', '>', $since_id);
        return $query->order('eming ASC')->limit($length)->select();
    }

    public function getListByType($match_id,$type){
        return $this->field("rank as id,nick,avatar, price as awards")
            ->where('match_id',$match_id)
            ->where('type',$type)
            ->order('rank ASC')
            ->select();
    }

    public function getMatchRankTotal($match_id){
        return $this->where('match_id',$match_id)->count();
    }

    public function getMatchRankByUid($match_id,$uid){
        return $this
            ->field("rank as id,nick,avatar, price as awards")
            ->where('match_id',$match_id)
            ->where('uid',$uid)
            ->order('rank ASC')
            ->find();
    }

    public function getMatchById($idMatch)
    {
        return $this->where("id",$idMatch)->find();
    }
}