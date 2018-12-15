<?php
# Talent.php

namespace app\api\logic;

use app\api\model\Order AS OrderModel;
use app\api\service\AbstractServiceModel;
use app\lib\exception\BaseException;
use app\lib\exception\ForbiddenException;
use app\lib\exception\NotFoundException;
use app\lib\jiguang\JG;

class Order extends  AbstractServiceModel
{
    public function detailInfo(OrderModel $order) {
        $order->visible(['order_id','order_type', 'system', 'area', 'round', 'price', 'create_time', 'end_time']);
        $result = $order->toArray();
        $result['order_type'] = $order->getData('type');
        $result['pay_type'] = $order->getData('pay_type');
        $result['score'] = (int) ( $order->commentObj ?  $order->commentObj->star * 2 : 0 );

        $order_status = $order->getData('status');
        $game_status = $order->getData('game_status');
        $result['order_status'] = [
            'game_status'=>$game_status,
            'may_cancel'=> $this->cancelAble($order),
            'order_status'=>$order_status,
            'may_comment' => $this->commentAble($order),
            'order_phases' => $this->getOrderPhases($order),
        ];

        # 用户信息
        $user = $order->user;
        $userInfo = ['uid' => '', 'icon' => '', 'nick' => ''];
        if($user) {
            $user->avatar && $user->icon = $user->avatar;
            $userInfo = [
                'uid' => $user->uid,
                'icon' => $user->icon,
                'nick' => $user->nick,
            ];
        }

        # 大神信息
        $talent = $order->talent;
        $talentInfo = ['uid' => '', 'icon' => '', 'nick' => ''];
        if($talent) {
            $talent->user->avatar && $talent->user->icon = $talent->user->avatar;
            $talentInfo = [
                'uid' => $talent->user->uid,
                'icon' => $talent->user->icon,
                'nick' => $talent->user->nick,
                'avg_score' => round($talent->getAvgStar() * 2, 1),
                'wx_qrcode' => $order->getData('pay_type') ? $talent->wx_qrcode : '',
                'wx_account' => $order->getData('pay_type') ? $talent->wx_account : '',
                'qq_account' => $order->getData('pay_type') ? $talent->qq_account : '',
                'tel' => $order->getData('pay_type') ? $talent->tel : '',
            ];
        }

        # 申诉信息
        $appeal = $order->appeals()->find();
        $appealInfo = [
            'appeal_auth_status' => $appeal ? $appeal->getData('auth_status') : 0,
            'appeal_reject_reason' => $appeal ? $appeal->reject_reason : '',
        ];

        $result['userInfo'] = $userInfo;
        $result['talentInfo'] = $talentInfo;
        $result['appeal'] = $appealInfo;
        return $result;
    }

    public function formatRows($rows) {
        $list = [];
        $since_id = 0;
        if(isset($rows[0])) {
            foreach($rows as $order) {
                $list[] = $this->detailInfo($order);
                $since_id = $order->create_time;
            }
        }
        return ['list'=> $list, 'count' => count($list), 'since_id'=> $since_id];
    }

    /**
     * 订单可取消
     * @param OrderModel $order
     * @return int
     */
    public function cancelAble(OrderModel $order) {
        $order_status = $order->getData('status');
        //$game_status = $order->getData('game_status');
        //return $order_status == 0 && $game_status <=1 ? 1 : 0;
        return $order_status == 0 ? 1 : 0;
    }

    /**
     * 订单是否可取消
     * @param $order
     * @throws ForbiddenException
     */
    public function chkOrderCancelAble($order) {
        if(! $this->cancelAble($order) ) {
            throw new ForbiddenException(['msg'=> '订单不可取消']);
        }
    }

    public function commentAble(OrderModel $order) {
        $result = 0;
        $order_status = $order->getData('status');
        if( $order_status == 1) {
            if(!$order->commentObj) {
                $result = 1;
            }
        }
        return $result;
    }

    /**
     * 完成(取消)订单
     * @param int $status 1. 完成订单， 2. 取消订单, 3.被申诉取消
     * @param $order
     * @param $talent
     * @return bool
     * @throws BaseException
     */
    public function end($status = 1, $order, $talent=null, $coca=true ) {
        if($order->getData('status') == $status) return true;
        $time = time();
        if($status == 1) {
            $this->chkPay($order);
            $ret = $this->l('bill')->finishOrder($order);
        } else {
            $coca && $this->chkOrderCancelAble($order);
            $ret = $this->l('bill')->cancelOrder($order);
        }
        if(!$ret) {
            throw new BaseException(['msg'=>'订单账目处理失败']);
        }

        $order->status = $status;
        $order->game_status = 3;
        $order->end_time = $time;
        $order->save();

        $talent || $talent = $order->talent;
        if($status == 1) {
            if($talent) {
                $talent->order_num += 1;
            }
            $payload = JG::instance()->payload('orderFinish');
            $payload->init($order);
            JG::instance()->push($payload);
        } elseif($status == 2) {
            $payload = JG::instance()->payload('orderCancel');
            $payload->init($order);
            JG::instance()->push($payload);
        }
        $talent->sort_key = $time;
        $talent->save();
        return true;
    }

    public function getOrder() {
        $param = $this->request->param();
        $order = $this->m()->where(['order_id'=> $param['order_id']])->find();
        if(!$order) {
            throw new NotFoundException(['msg'=> '订单不存在']);
        }
        return $order;
    }

    public function chkOrderUserId($order, $user) {
        if($order->user_id != $user->id) {
            throw new ForbiddenException(['msg'=> '这不是你的订单']);
        }
    }

    public function chkOrderTalentId($order, $talent) {
        if(!$talent || $order->talent_id != $talent->id) {
            throw new ForbiddenException(['msg'=> '这不是你的订单']);
        }
    }

    public function chkOrderStatus($order, $status=null, $game_status=null) {
        if( (is_numeric($status) && $order->getData('status') != $status)
            || (is_numeric($game_status) && $order->getData('game_status') != $game_status)
            || (is_array($status) && !in_array( $order->getData('status'), $status) )
            || (is_array($game_status) && !in_array( $order->getData('game_status'), $game_status) )) {
            throw new ForbiddenException(['msg'=> '订单状态错误']);
        }
    }

    /**
     * 订单是否可申诉
     * @param $order
     * @throws ForbiddenException
     */
    public function chkAppealAble($order) {
        $this->chkPay($order);
        if($this->cancelAble($order)) {
            throw new ForbiddenException(['msg'=> '该订单未进入申诉环节']);
        }
    }

    public function chkPay($order) {
        if($order->getData('pay_type') == 0) {
            throw new ForbiddenException(['msg'=> '订单未支付']);
        }
    }

    public function chkIsPayed($order) {
        if($order->trade_no) {
            throw new ForbiddenException(['msg'=> '订单已支付']);
        }
    }


    /**
     * @param $order
     * @return integer
     *
     * 订单所处阶段
     * 0       未确认
     * 1       进行中
     * 2        已完成(已评价)
     * 3        已取消
     * 4        申诉中
     * 5        申诉成功
     * 6        申诉失败
     * 7        等待评价
     */
    public function getOrderPhases($order) {
        $phases = 0;
        switch ($order->getData('status')) {
            case 0: // 未完成
                if($order->getData('game_status') > 0) {
                    $phases = 1;
                }
                break;
            case 1: // 已完成
                $appeal = $order->appeals()->find();
                if($appeal) {
                    // $appeal->getData('auth_status') 1.申诉中,2.成功,3.失败
                    $phases = $appeal->getData('auth_status') + 3;
                } else {
                    $comment = $order->commentObj;
                    if($comment) {
                        $phases = 2;
                    } else {
                        $phases = 7;
                    }
                }
                break;
            case 2: // 已取消
                $phases = 3;
                break;
            case 3: // 申诉取消
                $phases = 5;
                break;
        }
        return $phases;
    }
}