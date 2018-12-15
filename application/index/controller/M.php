<?php
# Index.php

namespace app\index\controller;

use think\Controller;

class M extends Controller
{
    /**
     * 使用帮助
     */
    public function help() {
        return $this->fetch();
    }

    /**
     * 关于我们
     */
    public function about() {
        return $this->fetch();
    }

    /**
     * 大神协议
     */
    public function talentAgree() {
        return $this->fetch();
    }
}