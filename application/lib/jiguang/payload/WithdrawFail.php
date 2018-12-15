<?php
# 提现审核失败

namespace app\lib\jiguang\payload;


class WithdrawFail extends abstractPushPayload
{
    protected $type = 514;

    public function init($incOrder) {
        $user = $incOrder->user;
        $content = '您的个人收益提现失败，原因：'.$incOrder->reject_reason.'，有疑问可咨询客服。';
        $this->setContent($content);
        $this->saveToSysMessage($user, null, [
            'order_id' => $incOrder->order_id,
        ]);
        if($user->reg_id) {
            $this->addAlias( $user->uid );
        }
    }
}