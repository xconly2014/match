<?php
namespace app\api\model;

use think\db\Query;

class Talent extends BaseModel
{
    protected function scopeStatus(Query $query, $status = 2)
    {
        $query->where('auth_status' ,$status);
    }

    protected function scopeSwitchOn(Query $query, $on = 1)
    {
        $query->where('switch_on' ,$on);
    }

    /**
     * 关联user表
     */
    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }
    /**
     * 关联活动表
     * @param null $with
     * @return \think\model\relation\HasMany
     */
    public function activity($with=null)
    {
        return $this->hasMany('TalentActivity','talent_id','id')->with($with);
    }

    /**
     * 关联评论表
     * @param string with
     * @return \think\model\relation\HasMany
     */
    public function comments($with=null)
    {
        return $this->hasMany('Comment', 'talent_id', 'id')->with($with);
    }

    public function commentTags()
    {
        return $this->hasMany('CommentTag', 'talent_id', 'id');
    }

    public function talentLocks()
    {
        return $this->hasMany('TalentLock', 'talent_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany('Order', 'talent_id', 'id');
    }

    public function getMaxLevelAttr($value) {
        $tuple = [1=>'倔强青铜', 2=>'秩序白银', 3=>'荣耀黄金', 4=>'尊贵铂金', 5=>'永恒钻石', 6=>'至尊星耀', 7=>'最强王者'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function talOrder()
    {
        return $this->hasOne('TalOrder', 'talent_id', 'id');
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

    public function getCategoryAttr($value){
        $tuple = ['餐饮', '服装', '饮品'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setCategoryAttr($value) {
        if(is_numeric($value)) {
            $result = $value;
        } elseif( strpos($value, '服装') !== false) {
            $result = 1;
        } elseif(strpos($value, '饮品') !== false) {
            $result = 2;
        } else {
            $result = 0;
        }
        return $result;
    }

    public function getAreaAttr($value) {
//        $tuple = ['双区服', 'QQ', '微信'];
        $tuple = ['SM生活广场', '万达广场', '加州商业广场'];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }

    public function setAreaAttr($value) {
        if(is_numeric($value)) {
            $result = $value;
        } elseif( strpos($value, '万达广场') !== false) {
            $result = 1;
        } elseif(strpos($value, '加州商业广场') !== false) {
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


    /**
     * @param int $since_id
     * @param int $length
     * @param null|string $with
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getComments($since_id = 0, $length = 10, $with=null) {

        $query = $this->comments($with);
        if($since_id > 0) {
            $query->where('id', '<', $since_id);
        }
        return $query->order('id DESC')->limit($length)->select();
    }

    public function getActivities($since_id = 0,$length = 10, $with=null)
    {
        $query = $this->activity($with);
        if($since_id > 0){
            $query->where('id','<',$since_id);
        }
        return $query->order('send_time DESC')->limit($length)->select();
    }

    public function edit(array $data) {
        $field = ['user_id', 'max_level', 'area', 'system', 'hero', 'honour', 'cert_sex', 'honour_image',
            'idcard_image', 'wx_qrcode','wx_account', 'qq_account', 'tel'];
        foreach($data as $key => $val) {
            if(in_array($key, $field)) {
                $this->setattr($key, $val);
            }
        }
        return $this;
    }

    public function getById($id, $scope_flag = false) {
        if($scope_flag) {
            return self::scope('status, switchOn')->where('id', $id)->find();
        } else {
            return self::get($id);
        }
    }

    /**
     * 获取列表
     * @param int $type 1.大神，2.美女
     * @param int $since_id
     * @param string keyword
     * @param int $length
     * @param array|null $filter
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getList($type=1, $since_id = 0, $keyword = '', $length = 10, $filter = null) {
        $query = self::scope('status, switchOn')->with('User');
        $type == 2 && $query->where('cert_sex', 2);
        $since_id > 0 && $query->where('sort_key', '<', $since_id);
        # filter
        isset($filter['max_level']) && $query->where('max_level', $this->setMaxLevelAttr($filter['max_level']));
        isset($filter['area']) && (
            $this->setAreaAttr($filter['area'])
            ? $query->where('area', ['=',0],  ['=',$this->setAreaAttr($filter['area'])], 'or')
            : $query->where('area', 0)
        );
        isset($filter['system']) && (
        $this->setSystemAttr($filter['system'])
            ? $query->where('system', ['=',0],  ['=',$this->setSystemAttr($filter['system'])], 'or')
            : $query->where('system', 0)
        );

        if($keyword) {
            $query->where('nick','like','%'.$keyword.'%');
            $subQuery = \think\Db::table('dl_user')
                ->field('id')
                ->where('nick','like','%'.$keyword.'%')
                ->buildSql();
            $query->whereOr('user_id IN '.$subQuery);
        }

        return $query->order('sort_key DESC')->limit($length)->select();
    }

    public function getRandomList($area, $system, $length, $exclusion_talent = null) {
        $query = self::scope('status, switchOn')
            ->with('user')
            ->where('sort_key', '>=', time() - 86400 * 30);
        if($area > 0) {
            $query->where('area', 'in', '0,'.$area);
        }
        if($system > 0) {
            $query->where('system', 'in', '0,'.$system);
        }
        if($exclusion_talent) { // 排除大神
            $query->where('id', '<>', $exclusion_talent->id);
        }

        $result = \think\Db::query('SELECT MIN(id) as a, MAX(id) as b FROM dl_talent');
        if(!empty($result)) {
            $ids = range($result[0]['a'], $result[0]['b']);
            shuffle($ids);
            $idstr = implode(',', $ids);
            return $query->order('Field(id,'.$idstr.')')->limit($length)->select();
        }
        return [];
    }

    /**
     * 获取列表
     * @param $auth_status
     * @param $page
     * @param $listRows
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getListPage($auth_status, $page=1, $listRows=20, $keyword=Null) {
        $this->listPageQuery($auth_status, $keyword);
        // 排序
        if($auth_status >= 2 ) {
            $this->order('auth_time DESC');
        } else {
            $this->order('id DESC');
        }
        return $this->page($page, $listRows)->select();
    }

    public function countList($auth_status, $keyword) {
        $this->listPageQuery($auth_status, $keyword);
        return $this->count();
    }

    protected function listPageQuery($auth_status, $keyword) {
        if($auth_status > 0) {
            if($auth_status == 1) {
                //$this->where('pay_flag', 1);
                $this->where('(auth_status = 1 OR (auth_status = 3 AND refund_flag = 0))');
            } else {
                $this->where('auth_status', $auth_status);
            }
        }

        // 搜索
        if($keyword) {
            $this->with('user');
            $area = $this->setAreaAttr($keyword);
            $system = $this->setSystemAttr($keyword);

            if($keyword == '男') {
                $this->where('cert_sex', 1);
            } elseif ($keyword == '女') {
                $this->where('cert_sex', 2);
            } elseif(!is_numeric($keyword) && $this->getAreaAttr($area) == $keyword) {
                $this->where('area', $area);
            } elseif (!is_numeric($keyword) && $this->getSystemAttr($system) == $keyword) {
                $this->where('system', $system);
            } else {
                if(is_numeric($keyword)) {
                    $this->where('tel|wx_account|qq_account','like','%'.$keyword.'%');
                } else {
                    $this->where('level|max_level|hero|honour|wx_account|qq_account','like','%'.$keyword.'%');
                }
                $subQuery = \think\Db::table('dl_user')
                    ->field('id')
                    ->where('nick','like','%'.$keyword.'%')
                    ->buildSql();
                $this->whereOr('user_id IN '.$subQuery);
            }
        }
    }


    public function getAvgStar() {
        return $this->comments()->avg('star');
    }

    /**
     * 尝试设置大神当前接单ID，设置失败后，表示该订单接单失败，应该对 $order 进行其他处理。
     * 一个大神同时只能接一次单。
     * @param Order $order
     * @return bool
     */
    public function setCurOrderId(Order $order) {
        $result = 0;
        if($this->id) {
            $result = $this->where('id', $this->id)
                ->where('cur_order_id', '')
                ->update(['cur_order_id' => $order->id]);
            if($result) {
                $this->cur_order_id = $order->id;
            }
        }
        return $result;

    }

    public function save($data = [], $where = [], $sequence = null)
    {
        $this->sort_key > 0 || $this->sort_key = time();
        return parent::save($data, $where, $sequence);
    }
}