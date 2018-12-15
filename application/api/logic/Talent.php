<?php
# Talent.php

namespace app\api\logic;

use app\api\model\Comment;
use app\api\model\TalentActivity;
use app\api\model\TalentLock;
use app\lib\exception\ForbiddenException;
use think\Db;
use think\Model;
use think\Config;
use app\api\model\Talent AS TalentModel;

class Talent extends  Model
{
    public function getShareInfo() {
        return [
            'share_title' => '标题标题标题标题',
            'share_content' => '内容内容内容内容内容内容内容内容内容内容',
            'share_url' => 'http://www.9game.cn/fkjycmswat/',
            'share_img' => 'http://www.gamemei.com/background/uploads/allimg/161026/24-1610261PH20-L.jpg',
        ];
    }

    public function getTagsInfo(TalentModel $talent) {
        $tags = $talent->commentTags;
        $result = $tmp = [];
        if($tags) {
            foreach($tags as $tag) {
                if(isset($tmp[$tag->content])) {
                    $tmp[ $tag->content ] ++;
                } else {
                    $tmp[$tag->content] = 1;
                }
            }
            arsort($tmp);
            foreach($tmp as $key => $value) {
                $result[] = ['tag_name'=> $key, 'tag_num' => $value];
            }
        }
        return $result;
    }

    public function getComments(TalentModel $talent, $since_id,$length=10) {
        $list = [];
        $comments = $talent->getComments($since_id, $length, 'user');
        foreach( $comments as $comment ) {
            $comment->visible(['id','star', 'content', 'games_total', 'games_win', 'create_time', 'user']);
            $user = $comment->user;
            if($user) {
                $user->avatar && $user->icon = $user->avatar;
                $user->visible(['uid', 'icon', 'nick']);
                $list[] = $comment;
                $since_id = $comment->id;
            }
        }
        $comment = new Comment();
        $score = $comment->where('talent_id',$talent->id)->avg('star');
        $total = $comment->where('talent_id',$talent->id)->count('id');
        return [
            'list' => $list,
            'score' => round($score,1),
            'total' =>  $total,
            'since_id' => $since_id,
        ];
    }

    public function getActivityLatest(TalentModel $talent)
    {
        $activity_latest = [];
        $activities = $talent->getActivities(0,1,null);
        foreach ($activities as $activity){
            $activity->visible(['id','talent_id','title','content','image1','image2','image3','view_total','like_total','share_total','begin_time','end_time','send_time']);
            $activity_latest = $activity;
        }
        return [
            'latest'    =>  $activity_latest,
        ];
    }

    public function getActivities(TalentModel $talent,$since_id)
    {
        $list = [];
        $activities = $talent->getActivities($since_id,10,null);
        foreach ($activities as $activity){
            $activity->visible(['id','talent_id','title','content','image1','image2','image3','view_total','like_total','share_total','begin_time','end_time','send_time']);
            $list[] = $activity;
            $since_id = $activity->id;
        }
        return [
            'list'  =>  $list,
            'since_id' =>   $since_id,
        ];
    }

    public function singleData(TalentModel $talent, $user=null) {
        $user || $user = $talent->user;
        $single = [
            'id' => $talent->id,
            'user_id' => $user->uid,
            'icon' => $user->l()->getIcon($user),
            'nick' => $user->nick,
            'sex' => $user->sex,
            'age' => $user->age,
            'level' => $talent->level,
            'max_level' => $talent->max_level,
            'area' => $talent->area,
            'system' => $talent->system,
            'hero' => $talent->hero,
            'honour' => $talent->honour,
            'order_num' => $talent->order_num,
            'win_rate' => $talent->games_total > 0 ?  ceil($talent->games_win*100/ $talent->games_total) : 100,
            'time' => (int) $talent->sort_key,
        ];
        return $single;
    }

    /**
     * 禁止接单
     */
    public function forbidReceive(TalentModel $talent, $order_id) {
        $talent->switch_on = 0;
        $talentLock = new TalentLock();
        $talentLock->data([
            'talent_id' => $talent->id,
            'reason' => '被申诉订单号：'.$order_id,
            'end_time' => time() + 86400
        ])->save();
    }

    /**
     * 可打开接单开关
     */
    public function chkEditSwitchOnAble(TalentModel $talent) {
        if( $this->isLocked($talent) ) {
            throw new ForbiddenException(['msg'=>'您暂时被禁止接单']);
        }
    }
    public function isLocked(TalentModel $talent) {
        $count = $talent->talentLocks()
            ->where('end_time', '>', time())
            ->count();
        return $count ? 1 : 0;
    }

    /**
     *  获取商家的指定活动详情
     */
    public function getActivityDetail($activity_id){
        $talentActivity = new TalentActivity();
        $activity = $talentActivity->where('id',$activity_id)
            ->field('id,talent_id,title,content,image1,image2,image3,view_total,like_total,share_total,begin_time,end_time,send_time')
            ->find();
        return $activity;
    }

    /**
     *  获取商家的往期活动列表
     */
    public function getActivityListByTalent($talent_id){
        $talentActivity = new TalentActivity();
        $listActivity = $talentActivity->where('talent_id',$talent_id)
            ->field('id,talent_id,title,content,image1,image2,image3,view_total,like_total,share_total,begin_time,end_time,send_time')
            ->select();
        return [
            'list'  =>  $listActivity
        ];
    }

    /**
     *  获取用户在指定商家的优惠券
     */
    public function getDataCouponUser($talent_id,$user_id){
        $dataCouponUser = Db::name('talent_coupon')->alias('dtc')
            ->field("dtc.coupon_id,type,dtc.title,money,deduction,discount,birth,outlet,notice,begin_time,end_time")
            ->join('user_coupon duc','dtc.coupon_id = duc.coupon_id')
            ->join('talent_activity dta',"dta.id = duc.activity_id")
            ->where('duc.talent_id',$talent_id)
            ->where("duc.user_id",$user_id)
            ->order('dtc.id DESC')->find();
        return $dataCouponUser;
    }


    /**
     *  获取用户在指定商家的优惠券列表
     */
    public function getListCouponUser($talent_id,$user_id,$since_id,$length=10)
    {
        $query = Db::name('talent_coupon')->alias('dtc')
            ->field("dtc.*,begin_time,end_time")
            ->join('user_coupon duc','dtc.coupon_id = duc.coupon_id')
            ->join('talent_activity dta',"dta.id = duc.activity_id")
            ->where('duc.talent_id',$talent_id)
            ->where("duc.user_id",$user_id)
            ->select();

        if($since_id > 0) {
            $query->where('id', '<', $since_id);
        }
        return $query->order('id DESC')->limit($length)->select();
    }
}