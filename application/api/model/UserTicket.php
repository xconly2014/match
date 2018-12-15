<?php

namespace app\api\model;


class UserTicket extends AbstractModel
{
    protected $columns_filter = ['user_id', 'match_id','ticket_id','order_id', 'create_time'];

    public function user()
    {
        return $this->belongsTo("User", "uid");
    }

    public function match($with = null)
    {
        return $this->belongsTo("Match", "match_id")->with($with);
    }

    public function ticketOrder($with=null)
    {
        return $this->hasOne('Order','ticket_id','id')->with($with);
    }

    public function getTicketById($idTicket)
    {
        return $this->where("id", $idTicket)->find();
    }

    public function getTicketListByUid($uid, $since_id = 0, $length = 10)
    {
        $query = $this->match('User')->where("uid", $uid);
        $since_id > 0 && $query->where('id', '>', $since_id);
        return $query->order('create_time ASC')->limit($length)->select();
    }

    public function getUserTicketStatus($idMatch, $uid)
    {
         return $this->where('match_id', $idMatch)->where('user_id', $uid)->find();
    }

    public function edit(array $data) {
        $field = ['user_id','match_id','form_id','order_id'];
        foreach($data as $key => $val) {
            if(in_array($key, $field)) {
                $this->setattr($key, $val);
            }
        }
        return $this;
    }
}