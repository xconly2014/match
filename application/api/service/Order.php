<?php
# Order.php

namespace app\api\service;

use app\lib\exception\ForbiddenException;
use app\lib\exception\NotFoundException;
use app\lib\jiguang\JG;
use think\Config;


class Order extends AbstractServiceModel
{
    /**
     * 快车价格表
     * @return mixed
     */
    public function fast() {
        $max_level = $this->request->get('talent_max_level');
        $rows = $this->m('fastPrice')->selectByMaxLevel($max_level);
        return $this->l('fastPrice')->formatRows($rows);
    }

    /**
     * 快车订单详情
     * @throws ForbiddenException
     * @return mixed
     */
    public function read() {
        $user = $this->getUser();
        $order = $this->l()->getOrder();
        $this->l('bill')->queryPayByOrder($order);

        if($order->user_id != $user->id && $order->talent_id != $user->talent->id) {
            throw new ForbiddenException(['msg'=> '这不是你的订单']);
        }
        return $this->l()->detailInfo($order);
    }

    /**
     * 快车约单
     * @return array
     * @throws NotFoundException|ForbiddenException
     */
    public function newOrder() {
        $param = $this->request->param();
        $user = $this->getUser();

        $match = $this->m('match')->getById($param['match_id'], true);
        if(!$match) {
            throw new NotFoundException(['msg'=> '该赛事不存在或已经关闭']);
        }

//        if($talent->user->id == $user->id) throw new ForbiddenException(['msg'=> '不可约自己']);
//        if($this->m()->countUnfinished($user->id, $talent->id)) throw new ForbiddenException(['msg'=> '请确认之前的订单已完成']);

        $param['user_id'] = $user->id;
        # 价格
//        $single_price = $this->m('fastPrice')
//            ->where('max_level', $talent->getData('max_level'))
//            ->where('type', $param['date_type'])->value('price');
//        $param['price'] = $single_price * $param['round'];
//        if( ! $single_price) {
//            throw new NotFoundException(['msg'=> '计算价格时出错']);
//        }
        $config = Config::get('developer');
        $price = $config['isReal'] == 1 ? $match->fee *100 : 1;
        $param['price'] = $price;
        $order = $this->m()->getInstance();
        $order->edit($param)->save();

        # 微信参数
/*        $device_info = $this->request->param('device_info', 'APP');
        $spbill_create_ip = $this->request->param('spbill_create_ip');
        $wx_param = $this->l('bill')->wxPayParam($order->order_id, $order->price, $device_info, $spbill_create_ip);
        # 支付宝参数
        $ali_param = $this->l('bill')->alipayParam($order->order_id, $order->price);*/
        #微信小程序参数
        $wxm_param =  $this->l('bill')->wxPayParamMP($order->order_id, $order->price, $param['device_info'], "",$user->wxid);

        //如果是从呼叫老司机创建订单
//        if(isset($param['call_id']) && $param['call_id']) {
//            $this->m('call')->where('id', $param['call_id'])->update(['order_id'=> $order->id]);
//        }

//        $payload = JG::instance()->payload('engagedTalent');
//        $payload->init($order);
//        JG::instance()->push($payload);
        parse_str($wxm_param['package'],$data);
        $order->edit($data)->save();
        $userTicket = $this->m('userTicket')->getTicketById($param['ticket_id']);
        $userTicket->edit($data)->save();

        return [
            'order_id'=>$order->order_id,
            'price'=>$order->price,
//            'wx_param' => $wx_param,
//            'ali_param' => $ali_param,
            'wxm_param' => $wxm_param,
        ];
    }

    /**
     * 大神接收订单
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function receive() {
        $order = $this->l()->getOrder();
        $talent = $this->getUser()->talent;
        $this->l()->chkOrderTalentId($order, $talent);
        $this->l()->chkPay($order);
        $this->l()->chkOrderStatus($order, 0, 0);
        $order->game_status = 1;
        $order->save();
    }

    /**
     * 标记开始游戏
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function start() {
        $order = $this->l()->getOrder();
        $talent = $this->getUser()->talent;
        $this->l()->chkOrderTalentId($order, $talent);
        $this->l()->chkPay($order);
        $this->l()->chkOrderStatus($order, 0);
        $order->game_status = 2;
        $order->save();
    }

    /**
     * 大神完成订单
     * @return bool
     */
    public function finish() {
        $order = $this->l()->getOrder();
        $user = $this->getUser();
        $talent = $user->talent;
        $this->l()->chkOrderTalentId($order, $talent);
        $order->setRelation('talent', $talent);
        return $this->l()->end(1, $order);
    }

    /**
     * 买家取消订单
     * @return bool
     */
    public function cancel() {
        $order = $this->l()->getOrder();
        $user = $this->getUser();
        $this->l()->chkOrderUserId($order, $user);
        $result = $this->l()->end(2, $order);
        return $result;
    }

    /**
     * 订单申诉
     */
    public function appeal() {
        $order = $this->l()->getOrder();
        $user = $this->getUser();

        $this->l()->chkAppealAble($order);
        $this->l()->chkOrderUserId($order, $user);

        $exist = $order->appeals()->where('over_flag', 0)->count();
        if($exist) {
            throw new ForbiddenException(['msg'=> '该订单上次申诉未完成，不可重复申请']);
        }

        $this->m('orderAppeal')->data([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'content' => $this->request->param('content'),
            'proof_0' => $this->request->param('proof_0'),
            'proof_1' => $this->request->param('proof_1'),
        ])->save();

        $payload = JG::instance()->payload('orderAppeal');
        $payload->init($order);
        JG::instance()->push($payload);
    }
}