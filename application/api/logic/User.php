<?php
# Talent.php

namespace app\api\logic;

use think\Config;
use think\Model;
use app\api\model\User AS UserModel;

class User extends  Model
{

    /**
     * 标记登录
     * @param UserModel $user
     * @return UserModel
     */
    public function markLogin(UserModel $user) {
        $user->last_login_timer = time();
        $user->is_online = 1;
        return $user;
    }

    /**
     * 标记登出
     * @param UserModel $user
     * @return UserModel
     */
    public function markLogout(UserModel $user) {
        $user->last_logout_timer = time();
        $user->is_online = 0;
        return $user;
    }

    /**
     * 大神认证状态
     * @param UserModel $user
     * @return integer 0.未申请
     */
    public function getTalentAuthStatus(UserModel $user) {
        $status = 0;
        $talent = $user->talent;
        $talent && $status = $talent->getData('auth_status');
        return $status;
    }

    public function getIcon(UserModel $user) {
        return $user->avatar ? $user->avatar : ($user->icon ? $user->icon : '');
    }

    public function baseInfo(UserModel $user) {
        $level0_total = $user->where('level0',$user->uid)->count();
        $level1_total = $user->where('level1',$user->uid)->count();
        $developer = Config::get('developer');
        return [
            'uid'=> $user->uid,
            'nick' => $user->nick,
            'icon' => $user->icon,
            'phone' =>  $user->phone,
            'income' =>  number_format($user->income,2,'.',""),
            'balance' =>  number_format($user->balance,2,'.',''),
            'role_id' =>  $user->role_id,
            'role_name' =>  $user->role_name,
            'level0_total' => round($level0_total, 1),
            'level1_total' => round($level1_total, 1),
            'audit'     =>  $developer['audit'] ,
//            'vip_card' => $user->vip_card,
        ];
    }

    public function detailInfo(UserModel $user) {
        $developer = Config::get('developer');
        $level0_total = $user->where('level0',$user->uid)->count();
        $level1_total = $user->where('level1',$user->uid)->count();
        $user->visible(['uid', 'nick', 'icon','phone','income','balance','role_id','role_name']);
        $arrUser = $user->toArray();
        //一级用户
        $arrUser['level0_total'] = $level0_total;
        //二级用户
        $arrUser['level1_total'] = $level1_total;
        $arrUser['audit'] = $developer['audit'] ;
        return $arrUser;
    }

    /**
     * 是否可申请提现
     * @param UserModel $user
     * @param float $apply_amount  申请金额
     * @return bool
     */
    public function applyWithdrawAble(UserModel $user, $apply_amount) {
        if($user->income > $apply_amount) {
            $applying_amount = $user->incOrders()
                ->where('auth_status', '<=', 1)
                ->sum('apply_amount'); // 正在处理的申请金额
            if( $user->income >=  $applying_amount / 100 + $apply_amount) {
                return true;
            }
        }
        return false;
    }

    public function getTags(UserModel $user) {
        $tags = [];
        if($user->talent && $user->talent->getData('auth_status') == 2) { // 审核成功的大神
            $tags[] = 'talent';
            $tags[] = 'T_MAX_LEVEL_' . $user->talent->getData('max_level');
            if($user->talent->getData('area') == 0) {
                $tags[] = 'T_AREA_1';
                $tags[] = 'T_AREA_2';
            } else {
                $tags[] = 'T_AREA_' . $user->talent->getData('area');
            }
            if($user->talent->getData('system')) {
                $tags[] = 'T_SYSTEM_1';
                $tags[] = 'T_SYSTEM_2';
            } else {
                $tags[] = 'T_SYSTEM_' . $user->talent->getData('system');
            }
            if($user->talent->switch_on) {
                $tags[] = 'T_SWITCH_ON';
            }
        }
        return $tags;
    }

}