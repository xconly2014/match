<?php
# Home.php

namespace app\manage\controller;


class Home extends AbstractController
{
    public function index() {
        return redirect('/manage/talent');
        //return $this->fetch();
    }
}