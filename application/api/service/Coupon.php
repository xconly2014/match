<?php

namespace app\api\service;


class Coupon extends AbstractServiceModel
{
    public function lister(){
        $param = $this->request->param();
        $param = array_filter($param, 'strlen');
        return [];
    }

    public function detail(){
        $param = $this->request->param();
        $param = array_filter($param, 'strlen');
        return [];
    }

    public function userLister(){
        $param = $this->request->param();
        $param = array_filter($param, 'strlen');
        return [];
    }
}