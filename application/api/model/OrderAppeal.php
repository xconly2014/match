<?php
# OrderAppeal.php

namespace app\api\model;


class OrderAppeal extends BaseModel
{
    public function orderObj()
    {
        return $this->belongsTo('order', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo('user', 'user_id');
    }

    public function getOverFlagAttr($value) {
        $tuple = ['处理中', '已完成'];
        return isset($tuple[$value]) ? $tuple[$value] : $value;
    }

    public function setOverFlagAttr($value) {
        if(is_numeric($value)) {
            $result = $value;
        } elseif(trim($value) == '已完成') {
            $result = 1;
        } else {
            $result = 0;
        }
        return $result;
    }

    public function getAuthStatusAttr($value) {
        $tuple = [null, '', '同意', '驳回'];
        return isset($tuple[$value]) ? $tuple[$value] : $value;
    }

    /**
     * 获取列表
     * @param $over_flag
     * @param $page
     * @param $listRows
     * @param $keyword
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getListPage($over_flag, $page=1, $listRows=20, $keyword=Null) {
        $this->listPageQuery($over_flag, $keyword);
        return $this->order('id DESC')->page($page, $listRows)->select();
    }

    public function countList($over_flag, $keyword = null) {
        $this->listPageQuery($over_flag, $keyword);
        return $this->count();
    }

    protected function listPageQuery($over_flag, $keyword) {
        if(is_numeric($over_flag)) {
            $this->where('over_flag', $over_flag);
        }
        $this->with('user,orderObj');
        // 搜索
        if($keyword) {
            $this->where('content','like','%'.$keyword.'%');
            $this->whereOr('reject_reason','like','%'.$keyword.'%');

            $subQuery = \think\Db::table('dl_user')
                ->field('id')
                ->where('nick','like','%'.$keyword.'%')
                ->buildSql();
            $this->whereOr('user_id IN '.$subQuery);
            $subQuery2 = \think\Db::table('dl_order o')
                ->join('dl_talent t', 'o.talent_id = t.id')
                ->join('dl_user u', 't.user_id = u.id')
                ->field('o.id')
                ->where('u.nick', 'like', '%'.$keyword.'%')
                ->whereOr('o.order_id','like','%'.$keyword.'%')
                ->buildSql();
            $this->whereOr('order_id IN '.$subQuery2);
        }
    }
}