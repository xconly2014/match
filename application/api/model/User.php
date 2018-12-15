<?php
namespace app\api\model;

use app\lib\jiguang\JG;
use app\lib\exception\ParameterException;
use app\lib\exception\NotFoundException;

class User extends AbstractModel
{
    protected $hidden = ['id', 'password'];

    protected static function init()
    {
        User::event('after_insert', function ($user) {
            $user->setAttr('uid', id2uniqHexId($user->id));
            $user->save();
        });
    }

    /**
     * 关联talent表
     */
    public function talent()
    {
        return $this->hasOne('Talent', 'user_id', 'id');
    }

    public function tickets($with=null)
    {
        return $this->hasMany('UserTicket','user_id','id')->with($with);
    }

    public function comments()
    {
        return $this->hasMany('Comment', 'user_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany('Order', 'user_id', 'id');
    }

    public function orderAppeals()
    {
        return $this->hasMany('OrderAppeal', 'user_id', 'id');
    }

    public function balances()
    {
        return $this->hasMany('Balance', 'user_id', 'id');
    }

    public function incomes()
    {
        return $this->hasMany('Income', 'user_id', 'id')->order('id DESC');
    }

    public function incOrders()
    {
        return $this->hasMany('IncOrder', 'user_id', 'id');
    }

    public function balOrders()
    {
        return $this->hasMany('BalOrder', 'user_id', 'id');
    }

    public function wdAccount()
    {
        return $this->hasOne('UserWdAccount', 'user_id', 'id');
    }

    public function calls()
    {
        return $this->hasMany('Call', 'user_id', 'id');
    }

    /**
     * 校正获取个人收益余额
     * @return float|int|mixed|string
     */
    public function reviseIncome() {
        $incs = $this->incomes;
        $total = 0;
        if($incs) {
            foreach($incs as $row) {
                $total += $row->total_fee;
            }
        }
        $total = $total / 100;
        if($this->income != $total) {
            $this->income = $total;
            $this->save();
        }
        return $this->income;
    }

    /**
     * 校正获取个人账户余额
     * @return float|int|mixed|string
     */
    public function reviseBalance() {
        $bals = $this->balances;
        $total = 0;
        if($bals) {
            foreach($bals as $row) {
                $total += $row->total_fee;
            }
        }
        $total = $total / 100;
        if($this->balance != $total) {
            $this->balance = $total;
            $this->save();
        }
        return $this->balance;
    }

    public function getGameMaxLevelAttr($value) {
        $tuple = [1=>'倔强青铜', 2=>'秩序白银', 3=>'荣耀黄金', 4=>'尊贵铂金', 5=>'永恒钻石', 6=>'至尊星耀', 7=>'最强王者'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setGameMaxLevelAttr($value) {
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

    public function getGameAreaAttr($value) {
        $tuple = ['双区服', 'QQ', '微信'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setGameAreaAttr($value) {
        if(is_numeric($value)) {
            $result = $value;
        } elseif( strpos($value, 'QQ') !== false) {
            $result = 1;
        } elseif(strpos($value, '微信') !== false) {
            $result = 2;
        } else {
            $result = 0;
        }
        return $result;
    }

    public function getGameSystemAttr($value) {
        $tuple = ['双系统', '安卓系统', '苹果系统'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setGameSystemAttr($value) {
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

    /**
     * 更新极光推送reg_id, 并推送登录过期消息给旧设备
     * 注意:并未调用save()
     * @param $reg_id
     * @return User
     */
    public function updateRegId($reg_id) {
        $reg_id && JG::instance()->resetDevice($this);
        if($this->reg_id && $reg_id != $this->reg_id && $this->is_online == 1) {
            $payload = JG::instance()->payload('LoginExpired');
            $payload->init($this);
            JG::instance()->push($payload);
        }
        $this->reg_id = $reg_id;
        return $this;
    }

    public static function getByLoginType($type, $token) {
        switch($type) {
            case 0: //普通登录
                $user = self::get(['type'=> 0, 'uid'=> $token]);
                break;
            case 1: // 微信登录
                $user = self::get(['type'=> 1, 'wxid'=> $token]);
                break;
            case 2: // QQ登录
                $user = self::get(['type'=> 2, 'qqid'=> $token]);
                break;
            default:
                throw new ParameterException(['msg'=>'invalid parameters: type']);
        }
        if (!$user) {
            $user = new User();
            $user->register = time();
            switch($type) {
                case 1: // 微信登录
                    $user->setattr('type', 1);;
                    $user->wxid =$token;
                    break;
                case 2: // QQ登录
                    $user->setattr('type', 2);
                    $user->qqid = $token;
                    break;
            }
        }
        return $user;
    }

    public function getByUid($uid, $check_exist = true) {
        $user = self::get(['uid'=>$uid]);
        if($check_exist && !$user) {
            throw new NotFoundException(['msg'=>'用户不存在']);
        }
        return $user;
    }

    public function edit(array $data) {
        $field = ['nick', 'type', 'icon', 'sex', 'age', 'province', 'city', 'phone','level1','level0','role_id','role_name'];
        foreach($data as $key => $val) {
            if(in_array($key, $field)) {
                $this->setattr($key, $val);
            }
        }
        return $this;
    }

    public function uodateBalance($value) {
        $result = self::update(
            ['balance' => $value],
            ['id' => $this->id, 'balance' => $this->balance ]
        );
        if($result) {
            $this->balance = $value;
        }
        return $result;
    }

    public function updateIncome($value) {
        $result = self::update(
            ['income' => $value],
            ['id' => $this->id, 'income' => $this->income ]
        );
        if($result) {
            $this->income = $value;
        }
        return $result;
    }

    public function getBalanceList($since_id = 0, $length = 10) {
        $query = $this->balances();
        if($since_id > 0) {
            $query->where('id', '<', $since_id);
        }
        return $query->order('id DESC')->limit($length)->select();
    }

    public function getIncomeList($since_id = 0, $length = 10) {
        $query = $this->incomes();
        if($since_id > 0) {
            $query->where('id', '<', $since_id);
        }
        return $query->order('id DESC')->limit($length)->select();
    }

    public function getUserInvitedList($data_type=1,$uid,$since_id = 0, $length = 10){
        $query = $this;
        switch ($data_type){
            case 1:
                $query = $query->where("level0",$uid);
                break;
            case 2:
                $query = $query->where("level1",$uid);
        }
        if ($since_id > 0){
            $query->where('id','<',$since_id);
        }
        return $query->order('id DESC')->limit($length)->select();
    }

    public function  getUserTicketList($since_id = 0, $length = 10)
    {
        $query = $this->tickets("Match");
        $since_id > 0 && $query->where('id', '<', $since_id);
        return $query->order('create_time DESC')->limit($length)->select();
    }

    public function getUserByRoleId($role_id)
    {
        return $this->where('role_id',$role_id)->find();
    }
}