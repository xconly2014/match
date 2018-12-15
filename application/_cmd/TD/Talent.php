<?php
# Talent.php

namespace app\_cmd\TD;
use app\api\model\Talent AS TalentModel;
use think\console\Input;
use think\console\Output;
use think\Db;


class Talent extends AbstractEntity
{

    protected $uids = [];

    public function __construct(Input $input, Output $output)
    {
        parent::__construct($input, $output);
        $rows = Db::query('SELECT id FROM `dl_user` WHERE id NOT IN (SELECT user_id FROM `dl_talent`)');
        if($rows) {
            foreach($rows as $row) {
                $this->uids[] = $row['id'];
            }
        }
        shuffle($this->uids);
    }

    public function run($length)
    {
        $rows = [];
        for($i=0; $i<$length; $i++) {
            $time = time();
            $user_id = array_pop($this->uids);
            if(!$user_id) { // 为了保证UID的唯一性，在取不出来UID后中断
                break;
            }

            $auth_status = mt_rand(1, 2);
            $games_total = mt_rand(1, 800);
            $games_win = mt_rand(0, $games_total);
            $single = [
                'user_id' => $user_id,
                'auth_status' => $auth_status,
                'max_level' => mt_rand(1, 7),
                'area' => mt_rand(0, 2),
                'system' => mt_rand(0, 2),
                'hero' => $this->hero(),
                'honour' => $this->honour(),
                'cert_sex' => mt_rand(1, 2),
                'honour_image' => 'https://picsum.photos/720/480/?image=1',
                'idcard_image' => 'https://picsum.photos/720/480/?image='.mt_rand(1, 1000),
                'wx_qrcode' => 'https://picsum.photos/480/480/?image='.mt_rand(1, 1000),
                'wx_account' => 'test_wx'.$time,
                'qq_account' => 'test_qq'.$time,
                'tel' => $this->faker->phoneNumber,
                'games_total' => $games_total,
                'games_win' => $games_win,
                'order_num' => ceil($games_total / mt_rand(1, 5)),
                'sort_key' => substr($time, strlen($length) + 1)*$length + $i,
                'create_time' => $time,
            ];
            $auth_status > 1 && $single['auth_time'] = $time;
            $rows[] = $single;
        }
        $model = new TalentModel;
        $model->saveAll($rows);
        $this->output('Insert Length: ' . $length);
    }
}