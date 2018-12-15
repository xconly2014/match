<?php
# IncOrder.php
# 提现申请记录

namespace app\api\model;


class IncOrder extends BaseModel
{
    protected static function init()
    {
        IncOrder::event('after_insert', function (IncOrder $order) {
            if(!$order->order_id) {
                $sn = 'I'.date('ymdHis').str_pad(($order->id%1000), 3, '0', STR_PAD_LEFT);
                $order->setAttr('order_id', $sn);
                $order->save();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo('user', 'user_id');
    }

    public function talentLock()
    {
        return $this->hasOne('TalentLock', 'talent_id', 'id');
    }

    public function getByOrderId($order_id, $with=null) {
        if($with) {
            return self::with($with)->where('order_id', $order_id)->find();
        } else {
            return self::get(['order_id' => $order_id]);
        }
    }

    public function getAuthStatusAttr($value) {
        $tuple = [null, '处理中', '已完成', '驳回'];
        return isset($tuple[$value]) ? $tuple[$value] : $value;
    }

    public function getCurIncomeAttr($value) {
        return (int) $value;
    }

    /**
     * 获取列表
     * @param $status
     * @param $page
     * @param $listRows
     * @param $keyword
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getListPage($status, $page=1, $listRows=20, $keyword=Null) {
        $this->listPageQuery($status, $keyword);
        return $this->order('id DESC')->page($page, $listRows)->select();
    }

    public function countList($status, $keyword = null) {
        $this->listPageQuery($status, $keyword);
        return $this->count();
    }

    protected function listPageQuery($status, $keyword) {
        if(is_numeric($status)) {
            $this->where('auth_status', $status);
        }
        //$this->with('user');
        // 搜索
        if($keyword) {
            if(is_numeric($keyword)) {
                $this->where('order_id','like','%'.$keyword.'%');
            } else {
                $this->where('order_id','like','%'.$keyword.'%');
                $subQuery = \think\Db::table('dl_user')
                    ->field('id')
                    ->where('nick','like','%'.$keyword.'%')
                    ->buildSql();
                $this->whereOr('user_id IN '.$subQuery);
            }
        }
    }

    public function getListByUser($user, $since_id = 0, $length = 10) {
        $this->where('user_id', $user->id);
        $this->where('user_id', $user->id);
        if($since_id > 0) {
            $this->where('id', '<', $since_id);
        }
        return $this->order('id DESC')->limit($length)->select();
    }

    public function sumApplyAmountByUserAndAuthStatus($user, $auth_status = 1) {
        $this->where('user_id', $user->id);
        if(is_numeric($auth_status)) {
            $this->where('auth_status', $auth_status);
        }
        return $this->sum('apply_amount');
    }

    public function sumSettleAmountByUserAndAuthStatus($user, $auth_status = 2) {
        $this->where('user_id', $user->id);
        if(is_numeric($auth_status)) {
            $this->where('auth_status', $auth_status);
        }
        return $this->sum('settle_amount');
    }
}