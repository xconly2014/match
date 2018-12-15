<?php
# OrderAppeal.php

namespace app\manage\controller;

use app\lib\jiguang\JG;
use app\lib\Log;
use think\Session;

use think\Validate;

class OrderAppeal extends AbstractController
{
    public function index() {
        $keyword = $this->request->param('keyword');
        $status = $this->request->param('status');
        $page = $this->request->param('page', 1);
        $page >= 1 || $page = 1;
        $limit = 20;

        $list = $this->m('orderAppeal')->getListPage($status, $page, $limit, $keyword);
        $total = $this->m('orderAppeal')->countList($status, $keyword);

        return $this->fetch('', [
            'list' => $list,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    public function over() {
        $id = $this->request->param('id');
        $appeal = $this->m('orderAppeal')->where('id', $id)->find();
        if($appeal) {
            $appeal->over_flag = 1;
            $appeal->save();
            return json(['code'=>1]);
        }
        return json(['code'=>0, 'message'=>'数据处理失败!']);
    }

    /**
     * 同意
     * @return \think\response\Json
     */
    public function pass() {
        $id = $this->request->param('id');
        $appeal = $this->m('orderAppeal')->where('id', $id)->with('orderObj')->find();
        if($appeal && $appeal->getData('auth_status') <=1) {
            $appeal->auth_status = 2;
            $appeal->auth_time = time();
            if( $appeal->save() ) {
                $order = $appeal->orderObj;
                $order->endOrder(3)->save();
                $this->l('talent')->forbidReceive($order->talent, $order->order_id);
                Log::info('[Manage.appeal.pass] manager:'. Session::get('user')['username'] .' appeal: '.$id. ' ');

                $payload = JG::instance()->payload('appealSuccessToUser');
                $payload2 = JG::instance()->payload('appealSuccessToTalent');
                $payload->init($order);
                $payload2->init($order);
                JG::instance()->push($payload);
                JG::instance()->push($payload2);

                return json(['code'=>1]);
            }
        }
        return json(['code'=>0, 'message'=>'请求失败!']);
    }

    /**
     * 驳回
     * @return \think\response\Json
     */
    public function reject() {
        $validate = new Validate([
            'id'  => 'require|integer',
            'reason' => 'require|max:250',
        ]);
        $params = $this->request->param();
        if (!$validate->check($params)) {
            return json(['code'=>0, 'message'=>$validate->getError()]);
        }
        $id = $this->request->param('id');
        $appeal = $this->m('orderAppeal')->where('id', $id)->find();
        if($appeal && $appeal->getData('auth_status') <=1) {
            $appeal->auth_status = 3;
            $appeal->reject_reason = strip_tags($params['reason']);
            $appeal->auth_time = time();
            if($appeal->save()) {
                Log::info('[Manage.appeal.reject] manager:'. Session::get('user')['username'] .' appeal: '.$id. ' ');

                $payload = JG::instance()->payload('appealFailToUser');
                $payload2 = JG::instance()->payload('appealFailToTalent');
                $payload->init($appeal->orderObj);
                $payload2->init($appeal->orderObj);
                JG::instance()->push($payload);
                JG::instance()->push($payload2);

                return json(['code'=>1]);
            }
        }
        return json(['code'=>0, 'message'=>'请求失败!']);
    }

    /**
     * 退款 同意退还资费
     */
    public function refund() {
        $id = $this->request->param('id');
        $appeal = $this->m('orderAppeal')->where('id', $id)->find();
        if(!$appeal || $appeal->getData('auth_status') != 2) {
            return json(['code'=>0, 'message'=>'当前状态无法退款!']);
        }
        $order = $appeal->orderObj;
        $income = $this->m('income')
            ->where('user_id', $order->talent->user_id)
            ->where('trade_type', 'ORDER_FINISH')
            ->where('trade_no', $order->order_id)
            ->find();
        if(!$income) {
            return json(['code'=>0, 'message'=>'大神收益查询失败!']);
        }
        if($this->l('bill')->takeOutIncomeByAppeal($order, $income->total_fee)) {// 大神被扣款
            $this->l('order')->end(3, $order, null, false); // 取消订单&退费

            $appeal->refund_flag = 1;
            $appeal->save();
            Log::info('[Manage.appeal.refund] manager:'. Session::get('user')['username'] .' appeal: '.$id. ' ');
            return json(['code'=>1]);
        }  else {
            return json(['code'=>0, 'message'=>'扣除大神收益失败!!']);
        }
    }
}