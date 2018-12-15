<?php
# Home.php

namespace app\manage\controller;
use app\lib\jiguang\JG;
use app\lib\Log;
use think\Session;
use think\Validate;

class Talent extends AbstractController
{
    public function index() {
        $keyword = $this->request->param('keyword');
        $status = $this->request->param('status', 1);
        $page = $this->request->param('page', 1);
        $page >= 1 || $page = 1;
        $limit = 10;

        $list = $this->m('talent')->getListPage($status, $page, $limit, $keyword);
        $total = $this->m('talent')->countList($status, $keyword);

        return $this->fetch('', [
            'list' => $list,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    /**
     * 通过
     * @return \think\response\Json
     */
    public function pass() {
        $talent_id = $this->request->param('id');
        $talent =  $this->m('talent')->where('id',$talent_id)->find();
        if($talent && $talent->getData('auth_status') <=1) {
            $talent->auth_status = 2;
            $talent->sort_key = $talent->auth_time = time();
            if( $talent->save() ) {
                Log::info('[Manage.talent.pass] manager:'. Session::get('user')['username'] .' talent_id: '.$talent_id. ' ');
                $payload = JG::instance()->payload('talentAuditSuccess');
                $payload->init($talent->user);
                JG::instance()->push($payload);
                JG::instance()->resetTags($talent->user);
                return json(['result'=>1]);
            }

        }
        return json(['result'=>0, 'msg'=>'请求失败!']);
    }

    /**
     * 驳回
     * @return \think\response\Json
     */
    public function reject() {
        $validate = new Validate([
            'id'  => 'require|integer',
            'reason' => 'require|max:250',
        ]);
        $params = $this->request->param();
        if (!$validate->check($params)) {
            return json(['result'=>0, 'msg'=>$validate->getError()]);
        }
        $talent_id = $this->request->param('id');
        $talent =  $this->m('talent')->where('id',$talent_id)->find();
        if($talent && $talent->getData('auth_status') <=1) {
            $talent->auth_status = 3;
            $talent->reject_reason = $params['reason'];
            $talent->sort_key = $talent->auth_time = time();
            if($talent->save()) {
                Log::info('[Manage.talent.reject] manager:'. Session::get('user')['username'] .' talent_id: '.$talent_id. ' ');
                $payload = JG::instance()->payload('talentAuditFail');
                $payload->init($talent->user);
                JG::instance()->push($payload);
                return json(['result'=>1]);
            }
        }
        return json(['result'=>0, 'msg'=>'请求失败!']);
    }

    /**
     * 退款 同意退还资费
     */
    public function refund() {
        $id = $this->request->param('id');
        $talent = $this->m('talent')->getById($id);
        if(!$talent || $talent->getData('auth_status') != 3) {
            return json(['code'=>0, 'message'=>'当前状态无法退款!']);
        }
        if($talent->getData('refund_flag') == 0) {
            $talOrder = $talent->talOrder;
            $ret = $this->l('bill')->cancelTalOrder($talOrder);
            if(!$ret) {
                return json(['code'=>0, 'message'=>'账目处理失败!']);
            }
            $talent->refund_flag = 1;
            $talent->save();
        }

        return json(['code'=>1]);
    }
}