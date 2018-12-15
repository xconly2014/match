<?php
# User.php
/* TD order 300
 * // 不生成完成订单，请在管理后台操作完成订单
 */
namespace app\_cmd\TD;

use app\api\model\Order AS OrderModel;
use app\api\model\User AS UserModel;
use app\api\model\Talent AS TalentModel;
use app\api\model\Balance AS BalanceModel;
use app\api\model\WxNotify;
use app\api\model\AliNotify;

class Order extends AbstractEntity
{
    public function run($length) {
        $new_balance = $bal_rows = $inc_rows = $wx_rows = $ali_rows = $rows = [];
        for($i=0; $i<$length; $i++) {
            $time = time();
            $user_id = $this->getUserId();
            if($user_id) {
                $talent_id = $this->getTalentId($user_id);
                if($talent_id) {
                    $order_id = 'OT'.date('mdHis').str_pad(($i%10000), 4, '0', STR_PAD_LEFT);
                    $type = mt_rand(0, 7);
                    $price = $this->getPrice($talent_id, $type);
                    # pay_type status game_status
                    $pay_type = mt_rand(0, 2);
                    $status = mt_rand(0, 1) * 2;
                    $game_status = $status == 0 ? mt_rand(0, 2) : 0;

                    if($pay_type > 0) {
                        if($status == 2) { // 取消
                            $new_balance[ $user_id ] = $this->balance[$user_id] +  $price * 100;
                            $bal_rows[] = [
                                'user_id'=> $user_id,
                                'trade_type' => 'ORDER_REFUND',
                                'total_fee' => $price * 100,
                                'cur_balance' => $new_balance[ $user_id ] ,
                                'trade_no' => $order_id,
                                'create_time' => $time
                            ];
                        }

                        if($pay_type == 1) {
                            $wx_rows[] = [
                                'out_trade_no' => $order_id,
                                'total_fee' => $price * 100,
                                'attach' => '测试数据',
                                'create_time' => $time,
                            ];
                        } elseif($pay_type == 2) {
                            $ali_rows[] = [
                                'out_trade_no' => $order_id,
                                'total_amount' => $price,
                                'passback_params' => '测试数据',
                            ];
                        }
                    }

                    $round = mt_rand(1, 10);
                    if($price) {
                        $rows[] = [
                            'order_id' => $order_id,
                            'user_id' => $user_id,
                            'talent_id' => $talent_id,
                            'type' => $type,
                            'system' => mt_rand(0, 2),
                            'area' => mt_rand(0, 2),
                            'round' => $round,
                            'price' => $price * $round,
                            'status' => $status,
                            'game_status' => $game_status,
                            'pay_type' => $pay_type,
                            'create_time' => $time
                        ];
                    }
                }
            }
        }
        if( !empty($rows) ) {
            $model = new OrderModel;
            $model->saveAll($rows);
            $this->output('Insert Order: ' . count($rows));
        }
        if( !empty($wx_rows) ) {
            $model = new WxNotify();
            $model->saveAll($wx_rows);
            $this->output('Insert Wx: ' . count($wx_rows));
        }
        if( !empty($ali_rows) ) {
            $model = new AliNotify();
            $model->saveAll($ali_rows);
            $this->output('Insert Ali: ' . count($ali_rows));
        }

        if( !empty($bal_rows) ) {
            $balModel = new BalanceModel;
            $balModel->saveAll($bal_rows);
            $this->output('Insert Balance: ' . count($bal_rows));
        }

        if( !empty($new_balance)) {
            foreach($new_balance as $user_id => $balance) {
                $uModel = new UserModel;
                $uModel->where('id', $user_id)->update(['balance'=>$balance]);
            }
            $this->output('Update User Balance');
        }
    }


    protected $uids = [];
    protected $balance = [];
    protected function getUserId() {
        if(empty($this->uids)) {
            $model = new UserModel;
            $rows = $model->field('id, balance')->select();
            if($rows) {
                foreach($rows as $row) {
                    $this->uids[] = $row->id;
                    $this->balance[ $row->id ] = $row->balance;
                }
            }
        }
        return $this->uids[ array_rand($this->uids) ];
    }

    protected $utids = [];
    protected $tids = [];
    protected $max_levels = [];
    protected function getTalentId($user_id) {
        if(empty($this->tids)) {
            $model = new TalentModel;
            $rows = $model->field('id, max_level')->select();
            if($rows) {
                foreach($rows as $row) {
                    $this->tids[] = $row->id;
                    $this->max_levels[ $row->id ] = $row->getData('max_level');
                }
            }
        }
        $result = null;
        while (true) {
            $id = $this->tids[ array_rand($this->tids) ];
            $key = $user_id.'-'.$id;
            if(!in_array( $key , $this->utids)) {
                $this->utids[] = $key;
                $result = $id;
                break;
            }
        }
        return $result;
    }

    protected function getPrice($talent_id ,$type) {
        if( isset($this->max_levels[$talent_id] )) {
            $max_level = $this->max_levels[$talent_id];
            $key = $max_level . '-' . $type;
            if(isset($this->fask[ $key ])) {
                return $this->fask[ $key ];
            }
        }
        return null;
    }

    protected $fask = [
        '7-0'=> 68,
        '7-7'=> 99,
        '7-6'=> 55,
        '7-5'=> 25,
        '7-4'=> 18,
        '7-3'=> 10,
        '7-2'=> 8,
        '7-1'=> 8,
        '6-0'=> 68,
        '6-7'=> 99,
        '6-6'=> 55,
        '6-5'=> 25,
        '6-4'=> 18,
        '6-3'=> 10,
        '6-2'=> 8,
        '6-1'=> 8,
        '5-0'=> 68,
        '5-7'=> 99,
        '5-6'=> 55,
        '5-5'=> 25,
        '5-4'=> 18,
        '5-3'=> 10,
        '5-2'=> 8,
        '5-1'=> 8,
        '4-0'=> 68,
        '4-7'=> 99,
        '4-6'=> 55,
        '4-5'=> 25,
        '4-4'=> 18,
        '4-3'=> 10,
        '4-2'=> 8,
        '4-1'=> 8,
        '3-0'=> 68,
        '3-7'=> 99,
        '3-6'=> 55,
        '3-5'=> 25,
        '3-4'=> 18,
        '3-3'=> 10,
        '3-2'=> 8,
        '3-1'=> 8,
        '2-0'=> 68,
        '2-7'=> 99,
        '2-6'=> 55,
        '2-5'=> 25,
        '2-4'=> 18,
        '2-3'=> 10,
        '2-2'=> 8,
        '2-1'=> 8,
        '1-0'=> 68,
        '1-7'=> 99,
        '1-6'=> 55,
        '1-5'=> 25,
        '1-4'=> 18,
        '1-3'=> 10,
        '1-2'=> 8,
        '1-1'=> 8,
    ];
}