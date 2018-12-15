<?php
# Order.php

namespace app\manage\controller;

use app\lib\jiguang\JG;
use app\lib\Log;
use think\Session;
use think\Validate;

class IncOrder extends AbstractController
{
    public function index() {
        $keyword = $this->request->param('keyword');
        $status = $this->request->param('status');
        $page = $this->request->param('page', 1);
        $page >= 1 || $page = 1;
        $limit = 20;

        $list = $this->getList($status, $page, $limit, $keyword);
        $total = $this->m('incOrder')->countList($status, $keyword);

        return $this->fetch('', [
            'list' => $list,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    public function getList($status, $page, $limit, $keyword) {
        $list = $this->m('incOrder')->getListPage($status, $page, $limit, $keyword);
        if($list) {
            foreach($list as $row) {
                # 三天内收入
                $threeDaysTotalFee = $this->m('income')
                    ->where('user_id', $row->user_id)
                    ->where('total_fee', '>', 0)
                    ->where('create_time', '>=', time() - 86400*3)
                    ->sum('total_fee');
                $row->availableIncome = $row->user->income * 100 - $threeDaysTotalFee;
            }
        }
        return $list;
    }

    public function detail() {
        $order_id = $this->request->param('order_id');
        $order = $this->m('incOrder')->getByOrderId($order_id, 'user');
        $time = time() - 86400 * 5;
        $time -= $time % 86400;
        $incomes = $order->user->incomes()->where('create_time', '>=', $time)->select();
        $incOrders = $this->m('incOrder')->where('user_id', $order->user->id)->order('id DESC')->limit(10)->select();
        return $this->fetch('', [
            'order' => $order,
            'incomes' => $incomes,
            'orders' => $incOrders,
        ]);
    }

    /**
     * 完成
     * @return \think\response\Json
     */
    public function pass() {
        $id = $this->request->param('id');
        $amount = $this->request->param('amount');
        $incOrder =  $this->m('incOrder')->where('id',$id)->find();
        if(!$incOrder || $incOrder->getData('auth_status') >1) {
            return json(['code'=>0, 'message'=>'请求失败!']);
        }
        if($incOrder->user->income < $amount) {
            return json(['code'=>0, 'message'=>'余额不足!']);
        }
        if(!$this->l('bill')->withdraw($incOrder, $amount * 100)) {
            return json(['code'=>0, 'message'=>'数据更新失败!']);
        }
        $incOrder->auth_status = 2;
        $incOrder->auth_time = time();
        $incOrder->settle_amount += $amount * 100;
        $incOrder->cur_income = $incOrder->user->income * 100;
        $incOrder->save();
        Log::info('[Manage.pass] manager:'. Session::get('user')['username'] .' inc_order_id: '.$id. ' amount:'. $amount);
        return json(['code'=>1]);
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
        $order =  $this->m('incOrder')->where('id',$id)->find();
        if($order && $order->getData('auth_status') <=1) {
            $order->auth_status = 3;
            $order->reject_reason = $params['reason'];
            $order->auth_time = time();
            $order->save();

            $payload = JG::instance()->payload('withdrawFail');
            $payload->init($order);
            JG::instance()->push($payload);

            Log::info('[Manage.reject] manager:'. Session::get('user')['username'] .' inc_order_id: '.$id. ' ');
            return json(['code'=>1]);
        }
        return json(['code'=>0, 'message'=>'请求失败!']);
    }
}