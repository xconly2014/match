<?php
namespace app\api\model;


class Order extends BaseModel
{
    protected static function init()
    {
        Order::event('after_insert', function (Order $order) {
            if(!$order->order_id) {
                $sn = 'O'.date('ymdHis').str_pad(($order->id%1000), 3, '0', STR_PAD_LEFT);
                $order->setAttr('order_id', $sn);
                $order->save();
            }
        });
    }

    public function talent()
    {
        return $this->belongsTo('talent', 'talent_id');
    }

    public function user()
    {
        return $this->belongsTo('user', 'user_id');
    }

    public function commentObj()
    {
        return $this->hasOne('Comment', 'order_id', 'id');
    }

    public function appeals()
    {
        return $this->hasMany('OrderAppeal', 'order_id', 'id')->order('id DESC');
    }

    public function messages()
    {
        return $this->hasMany('OrderMessage', 'order_id', 'id');
    }

    public function match()
    {
        return $this->belongsTo('match','match_id');
    }

    public function getTypeAttr($value) {
        $tuple = ['语音指导', '娱乐局','白银局', '黄金局',  '铂金局', '钻石局', '星耀局', '王者局'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setTypeAttr($value) {
        if(is_numeric($value)) {
            $result = $value;
        } elseif( strpos($value, '语音') !== false) {
            $result = 0;
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
        } else {
            $result = 1;
        }
        return $result;
    }

    public function getAreaAttr($value) {
        $tuple = ['双区服', 'QQ', '微信'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function getPayTypeAttr($value) {
        $tuple = ['未支付', '微信', '支付宝', '余额支付'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setAreaAttr($value) {
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

    public function getStatusAttr($value) {
        $tuple = ['未完成', '已完成', '已取消', '申诉取消'];
        return $tuple[ $value ];
    }

    public function getGameStatusAttr($value) {
        $tuple = ['待确定', '未开始', '进行中', '已完成'];
        return $tuple[ $value ];
    }

    public function edit(array $data) {
        $field = ['user_id','match_id','ticket_id', 'talent_id','prepay_id', 'price', 'valid', 'status', 'pay_type', 'order_id', 'end_time'];
        foreach($data as $key => $val) {
            if(in_array($key, $field)) {
                $this->setattr($key, $val);
            }
        }
        return $this;
    }

    /**
     * 获取列表
     * @param int $user_id
     * @param int $since_id
     * @param int $length
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getListByUserId($user_id, $since_id = 0, $length = 10) {
        $fun = function($obj, $user_id, $since_id) {
            $obj->where('user_id', $user_id);
            if($since_id > 0) {
                $obj->where('create_time', '<', $since_id);
            }
            $obj->where('pay_type', '>', '0'); // 不显示未支付的
        };
        $fun($this, $user_id, $since_id);
        $this->update(['user_read_flag'=>1]);
        $fun($this, $user_id, $since_id);
        return $this->order('create_time DESC')->limit($length)->select();
    }

    public function getListByTalentId($talent_id, $since_id = 0, $length = 10) {
        $fun = function($obj, $talent_id, $since_id) {
            $obj->where('talent_id', $talent_id);
            if($since_id > 0) {
                $obj->where('create_time', '<', $since_id);
            }
            $obj->where('pay_type', '>', '0'); // 不显示未支付的
        };
        $fun($this, $talent_id, $since_id);
        $this->update(['talent_read_flag'=>1]);
        $fun($this,$talent_id, $since_id);
        return $this->order('create_time DESC')->limit($length)->select();
    }

    /**
     * 存在未完成的订单
     * @param $user_id
     * @param $talent_id
     * @return int|string
     */
    public function countUnfinished($user_id, $talent_id) {
        return $this->where('user_id', $user_id)
            ->where('pay_type', '>', 0)
            ->where('talent_id', $talent_id)
            ->where('status', '0')
            ->count();
    }

    /**
     * 获取列表
     * @param $status
     * @param $pay_type
     * @param $page
     * @param $listRows
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getListPage($status, $pay_type, $page=1, $listRows=20, $keyword=Null) {
        $this->listPageQuery($status, $pay_type, $keyword);
        return $this->order('id DESC')->page($page, $listRows)->select();
    }

    public function countList($status, $pay_type, $keyword = null) {
        $this->listPageQuery($status, $pay_type, $keyword);
        return $this->count();
    }

    protected function listPageQuery($status, $pay_type, $keyword) {
        if(is_numeric($status)) {
            $this->where('status', $status);
        }
        if(is_numeric($pay_type)) {
            $this->where('pay_type', $pay_type);
        } elseif ($pay_type == '>0') {
            $this->where('pay_type', '>', 0);
        }
        //$this->with('user, talent');

        // 搜索
        if($keyword) {
            $this->with('user');
            $area = $this->setAreaAttr($keyword);
            $system = $this->setSystemAttr($keyword);
            $type = $this->setTypeAttr($keyword);

            if(!is_numeric($keyword) && mb_substr($this->getAreaAttr($area), 0, 2,'utf-8') == mb_substr($keyword, 0, 2, 'utf-8')) {
                $this->where('area', $area);
            } elseif (!is_numeric($keyword) && mb_substr($this->getSystemAttr($system), 0, 2,'utf-8') == mb_substr($keyword, 0, 2,'utf-8')) {
                $this->where('system', $system);
            }elseif (!is_numeric($keyword) && mb_substr($this->getTypeAttr($type), 0, 2,'utf-8') == mb_substr($keyword, 0, 2,'utf-8')) {
                $this->where('type', $type);
            } else {
                if(is_numeric($keyword)) {
                    $this->where('id', $keyword);
                    $this->whereOr('match_id',$keyword);
                    $this->whereOr('order_id','like','%'.$keyword.'%');
                } else {
                    $this->where('order_id','like','%'.$keyword.'%');
                    $subQuery = \think\Db::table('dl_user')
                        ->field('id')
                        ->where('nick','like','%'.$keyword.'%')
                        ->buildSql();
                    $this->whereOr('user_id IN '.$subQuery);
                    $subQuery2 = \think\Db::table('dl_talent t')
                        ->join('dl_user u', 't.user_id = u.id')
                        ->field('t.id')
                        ->where('u.nick', 'like', '%'.$keyword.'%')
                        ->buildSql();
                    $this->whereOr('talent_id IN '.$subQuery2);
                }
            }
        }
    }

    /**
     * 完成|取消 订单
     * @param $status 1完成, 2.取消，3.申诉取消
     * @return $this
     */
    public function endOrder($status) {
        $this->status = $status;
        $this->end_time = time();
        return $this;
    }

    /**
     *  查看訂單
     */
    public function getOrderById($order_id){
        return $this->where('order_id',$order_id)->find();
    }
}