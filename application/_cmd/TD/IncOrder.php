<?php
# IncOrder.php
# 生成申请提现记录
namespace app\_cmd\TD;

use app\api\model\User;
use app\api\model\IncOrder AS IOModel;

class IncOrder extends AbstractEntity
{
    public function run($length)
    {
        $user_rows = $rows = [];
        for ($i = 0; $i < $length; $i++) {
            $time = time();
            $user_rows[] = $user = $this->getUser();
            $order_id = 'IT'.date('mdHis').str_pad(($i%10000), 4, '0', STR_PAD_LEFT);
            $price = mt_rand(200, 2000) / 2;

            $user->income += $price;

            $rows[] = [
                'user_id' => $user->id,
                'order_id' => $order_id,
                'apply_amount' => $price * 100,
                'create_time' => $time,
            ];
        }
        if(!empty($rows)) {
            $model = new IOModel;
            $model->saveAll( $rows );
            $this->output('Insert IncOrder: ' . count($rows));
        }

        if(!empty($user_rows)) {
            foreach ($user_rows as $u) {
                $u->save();
            }
            $this->output('Update User: ' . count($rows));
        }
    }

    protected $users = [];
    protected function getUser() {
        if(empty($this->users)) {
            $model = new User;
            $rows = $model->field('id, income')->limit(500)->select();
            if($rows) {
                $this->users = $rows;
            }
        }
        return $this->users[ array_rand($this->users) ];
    }
}
