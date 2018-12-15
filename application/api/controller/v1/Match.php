<?php

namespace app\api\controller\v1;

use app\api\controller\BaseController;

/**
 * @title  精英赛事
 */
class  Match extends BaseController
{
    /**
     * @title 2-1.赛事列表
     * @doc \app\api\param\match\Lister
     */
    public function lister() {
        $data = $this->s()->lister();
        return $this->response($data);
    }

    /**
     * @title 2-2.赛事详情
     * @doc \app\api\param\match\Detail
     */
    public function detail(){
        $data = $this->s()->detail();
        return $this->response($data);
    }

    /**
     * @title 2-3.参赛报名
     * @doc \app\api\param\match\Call
     */
    public function call(){
        $data = $this->s()->call();
        return $this->response($data);
    }

    /**
     * @title 2-4.报名成功
     * @doc \app\api\param\match\CallPass
     */
    public function callPass(){
        $data = $this->s()->callPass();
        return $this->response($data);
    }

    /**
     * @title 2-5.赛事排名
     * @doc \app\api\param\match\RankSportList
     */
    public function rankSportList(){
        $data = $this->s()->rankSportList();
        return $this->response($data);
    }

    /**
     * @title 2-6.配置赛事
     * @doc \app\api\param\match\GameSportConfig
     */
    public function gameSportConfig(){
        $data = $this->s()->GameSportConfig();
        return $this->response($data);
    }

    /**
     * @title 2-7.赛事取消
     * @doc \app\api\param\match\GameSportCancel
     */
    public function gameSportCancel(){
        $data = $this->s()->gameSportCancel();
        return $this->response($data);
    }

    /**
     * @title <b class="text-danger">2-8.赛事完成</b>
     * @doc \app\api\param\match\GameSportComplete
     */
    public function gameSportComplete(){
        $data = $this->s()->gameSportComplete();
        return $this->response($data);
    }
    /**
     * @title 2-9.赛事结算
     * @doc \app\api\param\match\GameSportSettlement
     */
    public function gameSportSettlement(){
        $data = $this->s()->gameSportSettlement();
        return $this->response($data);
    }
}