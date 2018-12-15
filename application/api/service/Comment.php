<?php
# service\Talent;

namespace app\api\service;

use app\lib\exception\ForbiddenException;
use app\lib\exception\NotFoundException;

class Comment extends AbstractServiceModel
{
    /**
     * 评价订单
     * @return array
     * @throws NotFoundException
     */
    public function commentOrder() {
        $param = $this->request->param();
        $order = $this->getOrder();
        $talent = $this->getTalent($order);
        $this->chkOrderStatus($order, 1);
        $this->chkCommentExist($order);

        $param['order_id'] = $order->id;
        $param['talent_id'] = $talent ? $talent->id : 0;
        $param['user_id'] = $this->getUser()->id;

        $comment = $this->m('comment');


        // 更新 大神 游戏场次
        $talent->games_total += $param['games_total']; // TODO game_total 可选，可从order.round取值
        $param['games_win'] > 0 && $talent->games_win += $param['games_win'];
        $talent->save();

        $comment->data($param)->save();
        // 新增标签
        $this->m('commentTag')->saveAllTags($param['tags'], $comment, $talent);
        return ['comment_id' => $comment->id ];
    }

    protected function chkOrderStatus($order, $status) {
        if($order->getData('status') != $status) {
            throw new ForbiddenException(['msg'=> '订单未结束']);
        }
    }

    protected function getOrder() {
        $order_id = $this->request->route('order_id');
        $order = $this->m('order')->where(['order_id'=>$order_id])->find();
        if(! $order) {
            throw new NotFoundException(['msg'=> '订单不存在']);
        }
        return $order;
    }

    protected function getTalent($order) {
        $talent = $order->talent;
        if(!$talent) {
            throw new NotFoundException(['msg'=> '大神不存在']);
        }
        return $talent;
    }

    protected function chkCommentExist($order) {
        if( $order->commentObj ) {
            throw new ForbiddenException(['msg'=> '该订单已被评论']);
        }
    }
}