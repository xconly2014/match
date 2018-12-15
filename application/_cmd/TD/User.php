<?php
# User.php

namespace app\_cmd\TD;

use app\api\model\User AS UserModel;

class User extends AbstractEntity
{
    public function run($length) {
        $rows = [];
        for($i=0; $i<$length; $i++) {
            $time = time();
            $rows[] = [
                'username' => $this->faker->name,
                'nick' => $this->faker->nickname,
                'icon' => 'https://picsum.photos/200/200/?image='.mt_rand(1, 1000),
                'sex' => mt_rand(1, 2),
                'age' => mt_rand(1, 100),
                'province' => $this->faker->state,
                'city' => $this->faker->city,
                'cover_0' => 'https://picsum.photos/720/480/?image='.mt_rand(1, 1000),
                'cover_1' => 'https://picsum.photos/720/480/?image='.mt_rand(1, 1000),
                'cover_2' => 'https://picsum.photos/720/480/?image='.mt_rand(1, 1000),
                'cover_3' => 'https://picsum.photos/720/480/?image='.mt_rand(1, 1000),
                'game_nick' => $this->faker->nickname,
                'game_max_level' => mt_rand(1, 7),
                'game_area' => mt_rand(0, 2),
                'game_system' => mt_rand(0, 2),
                'game_position' => $this->position[ array_rand($this->position) ],
                'game_honour_image' => 'https://picsum.photos/720/480/?image=1',
                'game_honour' => $this->honour(),
                'register' => $time,
                'last_login_timer' => $time,
            ];
        }
        $userModel = new UserModel;
        $userModel->saveAll($rows);
        $this->output('Insert Length: ' . $length);
    }
}