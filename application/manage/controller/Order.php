<?php
# Order.php

namespace app\manage\controller;

use app\lib\Log;
use think\Session;

class Order extends AbstractController
{
    public function index() {
        $keyword = $this->request->param('keyword');
        $status = $this->request->param('status');
        $pay = $this->request->param('pay');
        $page = $this->request->param('page', 1);
        $page >= 1 || $page = 1;
        $limit = 20;

        $list = $this->getList($status, $pay, $page, $limit, $keyword);
        $total = $this->m('order')->countList($status, $pay, $keyword);

        return $this->fetch('', [
            'list' => $list,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    protected function getList($status, $pay, $page, $limit, $keyword) {
        $list = $this->m('order')->getListPage($status, $pay, $page, $limit, $keyword);
        foreach($list as $order) {
            # 申诉信息
            $appeal = $order->appeals()->find();
            $order->appeal = [
                'appeal_auth_status' => $appeal ? $appeal->getData('auth_status') : 0,
                'appeal_reject_reason' => $appeal ? $appeal->reject_reason : '',
            ];
        }
        return $list;
    }

    public function detail() {
        $order_id = $this->request->param('order_id');
        $order = $this->m('order')->with('user,talent')->where('order_id', $order_id)->find();
        return $this->fetch('', [
            'order' => $order
        ]);
    }

    /**
     * 完成订单
     * @return \think\response\Json
     */
    public function finish() {
        $id = $this->request->param('id');
        $order = $this->m('order')->where('id',$id)->find();
        if($order && $order->getData('status') == 0) {
            if( $this->l('order')->end(1, $order) ) {
                Log::info('[Order.finish] manager:'. Session::get('user')['username'] .' order_id: '.$id. ' ');
                return json(['code'=>1]);
            }
        }
        return json(['code'=>0, 'message'=>'数据处理失败!']);
    }

    /**
     * 取消订单
     * @return \think\response\Json
     */
    public function cancel() {
        $id = $this->request->param('id');
        $order = $this->m('order')->where('id',$id)->find();
        if($order && $order->getData('status') == 0) {
            if( $this->l('order')->end(2, $order) ) {
                Log::info('[Order.cancel] manager:'. Session::get('user')['username'] .' order_id: '.$id. ' ');
                return json(['code'=>1]);
            }
        }
        return json(['code'=>0, 'message'=>'数据处理失败!']);
    }
}