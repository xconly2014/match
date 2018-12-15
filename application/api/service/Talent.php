<?php
# service\Talent;

namespace app\api\service;

use app\lib\exception\AccessException;
use app\lib\exception\BaseException;
use app\lib\exception\ParameterException;
use app\lib\exception\NotFoundException;
use app\lib\jiguang\JG;
use think\Loader;
use think\Request;


class Talent extends AbstractServiceModel
{
    /**
     * 申请大神
     * @throws ParameterException
     */
    public function apply() {
        $user = $this->getUser();
        $data = $this->request->param();

        $talent = $user->talent;
        if($talent) {
            if($talent->getData('auth_status') != 3) {
                throw new ParameterException(['msg'=>'您已申请成为大神']);
            }
            // 删除旧记录以保证与User表一对一
            $talent->where(['user_id'=>$talent->user_id])->update(['delete_time'=>time()]);
        }

        $talent = $this->m()->getInstance();
        $data['user_id'] = $user->id;
        $talent->edit($data)
            ->save();
    }

    /**
     * 大神列表|美女列表
     * @return array
     */
    public function lister() {
        $list = [];

        $talentModel = $this->m('talent');
        $param = $this->request->param();

        $keyword = isset($param['keyword']) && $param['keyword'] ? $param['keyword'] : '';
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;

        $filter = [];
        isset($param['max_level']) && $param['max_level'] && $filter['max_level'] = $param['max_level'];
        isset($param['area']) && $param['area'] && $filter['area'] = $param['area'];
        isset($param['system']) && $param['system'] && $filter['system'] = $param['system'];

        $rows = $talentModel->getList($param['data_type'], $since_id, $keyword, 10, $filter);

        foreach ($rows as $talent) {
//            $user = $talent->user;
            $list[] = [
                'id' => $talent->id,
                "logo"      =>  $talent->logo,
                "name"      =>  $talent->nick,
                "color"      =>  $talent->color,
                "score"      =>  $talent->score,
                "area"      =>  $talent->area,
                "type"      =>  $talent->category,
                "new"      =>  $talent->new,
                "vip"      =>  $talent->vip,
                "activity"  =>  $this->l()->getActivityLatest($talent),
            ];
            $since_id = (int) $talent->sort_key;
        }
        $data = [
            'list' => $list,
            'count' => count($list),
            'since_id' => $since_id,
        ];

        if($param['fresh_type'] == 0) {
            $domain = $this->request->server('HTTP_HOST');
            # jump_type: 1： 跳转url， 2：跳转大神详情
            $data['banner'] = [
                [
                    'title'=> '轮播图',
                    'logo' => 'http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg',
                    'jump_type' => 0,
                    'jump_id' => '',
                ],
                [
                    'title'=> '轮播图',
                    'logo' => 'http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg',
                    'jump_type' => 0,
                    'jump_id' => '',
                ],
                [
                    'title'=> '跳转URL',
                    'logo' => 'http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg',
                    'jump_type' => 1,
                    'jump_id' => url('/app/help', '', false, $domain),
                ],
            ];
        }

        return $data;
    }


    /**
     * 大神详情
     * @return array
     * @throws NotFoundException
     */
    public function read() {
        $id = $this->request->param('id');
        $param = $this->request->param();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;
        $talent = $this->m()->getById($id);
        if(!$talent) {
            throw new NotFoundException(['msg'=> '大神不存在']);
        }
        if($param['fresh_type'] == 0) {
//            $talentData = [
//                'honour_image' => $talent->honour_image,
//                'hero' => $talent->hero,
//                'honour' => $talent->honour,
//                'order_num' => $talent->order_num,
//                'win_rate' => $talent->games_total > 0 ?  ceil($talent->games_win*100/ $talent->games_total) : 100,
//                'avg_score' => round($talent->getAvgStar() * 2, 1),
//                'switch_on' => $talent->switch_on,
//            ];

//            $userData = $this->l('user')->detailInfo($talent->user);
//            $userData['sex'] = $talent->cert_sex;
//            $userData['level'] = $talent->level;
//            $userData['fans_num'] = 0; // TODO 粉丝数
            $userData = array(
                "id"          =>    $talent->id,
                "logo"      =>  $talent->logo,
                "name"      =>  $talent->nick,
                "color"      =>  $talent->color,
                "score"      =>  $talent->score,
                "area"      =>  $talent->area,
                "type"      =>  $talent->category,
                "new"      =>  $talent->new,
                "vip"      =>  $talent->vip,
                "slogan"    =>  $talent->slogan,
                "tel"   =>  $talent->tel,
                "opentime"  => $talent->opentime
            );

            $result['header'] = $userData;

            // 价格表
//            $rows = $this->m('FastPrice')->selectByMaxLevel($talent->getData('max_level'));
//            $priceInfo =  $this->l('FastPrice')->formatRows($rows);
//
//            $result['middle']['certification'] = $talentData;
//            $result['middle']['tag'] = $this->l()->getTagsInfo($talent);
//            $result['middle'] += $priceInfo;
            #商家近期活动
            $result['activity'] = $this->l()->getActivityLatest($talent);
            #会员优惠券
            $user = $this->getUser();

            if($user){
                $dataCouponUser= $this->l()->getDataCouponUser($talent->id,$user->id);
                $countCouponUser = $this->m('userCoupon')
                    ->where('talent_id',$talent->id)
                    ->where('user_id',$user->id)
                    ->count();
            }
            $result['coupon'] = [
                'data'      =>  empty($dataCouponUser) ? array() : $dataCouponUser,
                'count'     =>  empty($countCouponUser) ? 0 : $countCouponUser,
            ];
            # 评论
            $result['comment'] = $this->l()->getComments($talent, $since_id,3);
            $result['share'] = $this->l()->getShareInfo();
        }
        return empty($result) ? array() : $result;
    }

    public function simpleInfo() {
        $user = $this->getUser();
        $talent = $user->talent;
        if(!$talent) {
            throw new ParameterException(['msg'=>'您未提交大神申请']);
        }
        # 主动查询 是否支付认证费用
        if(!$talent->getData('pay_flag')) {
            $talOrder = $talent->talOrder;
            if($talOrder) {
                if($talOrder->getData('pay_type') == 0) {
                    $this->l('bill')->queryPayByTalOrder($talOrder);
                }
                if($talOrder->getData('pay_type') > 0) {
                    $talent->pay_flag = 1;
                    $talent->save();
                }
            }
        }

        $talent->talent_auth_status = $talent->getData('auth_status');
        $talent->sex = $talent->cert_sex;
        $talent->win_rate = $talent->games_total > 0 ?  ceil($talent->games_win*100/ $talent->games_total) : 100;
        $talent->visible([ 'id', 'talent_auth_status', 'rejection_reason', 'auth_time', 'level', 'max_level', 'area', 'system', 'hero', 'honour',
            'sex', 'honour_image', 'idcard_image', 'wx_qrcode', 'wx_account', 'qq_account', 'tel', 'order_num', 'win_rate', 'switch_on', 'pay_flag', 'create_time']);
        return $talent;
    }

    /**
     * 大神接单开关
     */
    public function switchOn() {
        $id = $this->request->param('id');
        $on = $this->request->param('switch_on');

        $user= $this->getUser();
        $talent = $this->m()->getById($id);
        if($talent && $talent->user_id != $user->id) {
            throw new AccessException();
        }

        if($talent && $talent->getData('auth_status') == 2) {
            if($on > 0) {
                if ($talent->switch_on == 0) {
                    $this->l('talent')->chkEditSwitchOnAble($talent);
                    $talent->switch_on = 1;
                    $talent->sort_key = time();
                    $talent->save();
                }
            } else {
                if( $talent->switch_on == 1 ) {
                    $talent->switch_on = 0;
                    $talent->save();
                }
            }
            JG::instance()->resetTags($user);
        }
    }


    /**
     * 呼叫老司机
     */
    public function call() {
        $user = $this->getUser();

        $param = $this->request->param();
        $param['user_id'] = $user->id;
        $model = $this->m('talent');
        $call = $this->m('call');
        $call->data($param)->save();

        $payload = JG::instance()->payload('callTalent');
        $payload->init($user, $call);
        JG::instance()->push($payload);

        $total = $model->where('switch_on', 1)
            ->where('auth_status', 2)
            ->where('max_level', '>=', $model->setMaxLevelAttr($param['max_level']))
            ->where('area', $model->setAreaAttr($param['area']) )
            ->where('system', $model->setSystemAttr('system'))
            ->count();
        return ['match_total' => $total, 'call_id' => $call->id];
    }

    /**
     * 老司机抢单
     */
    public function race() {
        $user = $this->getUser();
        $call = $this->l('call')->getCall($this->request->param('id'));
        $price = $this->m('fastPrice')->getPrice(
                $user->talent->getData('max_level'), // 大神的段位
                $call->getData('max_level') // 请求的段位
            )  * $call->round;
        if(! $price ) {
            throw new BaseException(['msg'=>'系统无法计算订单价格']);
        }

        $extras = $this->l('talent')->singleData($user->talent, $user);
        $extras['price'] = $price;

        $payload = JG::instance()->payload('raceCall');
        $payload->init($call, $user, $price);
        JG::instance()->push($payload);
    }

    /**
     * 取消抢单呼叫
     */
    public function cancelCall() {
        $user = $this->getUser();
        $this->m('call')->delByUserId($user->id);
    }

    /**
     * 呼叫抢单信息（大神查看）
     */
    public function raceDetail() {
        //$user = $this->getUser();
        $this->m('call')->delOverTimeRows();
        $call = $this->l('call')->getCall($this->request->param('id'));
        $user = $call->user;

        $price = $this->m('fastPrice')->getPrice(
                $user->talent->getData('max_level'), // 大神的段位
                $call->getData('max_level') // 请求的段位
            )  * $call->round;
        if(! $price ) {
            throw new BaseException(['msg'=>'系统无法计算订单价格']);
        }

        $data = [
            'id' => $call->id,
            'user_id' => $user->uid,
            'icon' => $this->l('user')->getIcon($user),
            'nick' => $user->nick,
            'sex' => $user->sex,
            'age' => $user->age,
            'max_level' => $call->max_level,
            'area' => $call->area,
            'system' => $call->system,
            'round' => $call->round,
            'create_time' => $call->create_time,
            'price' => $price,
        ];
        return $data;
    }

    /**
     * 随机获取大神
     */
    public function random() {
        $num = $this->request->param('total_num');
        $call_id = $this->request->param('call_id');
        $call = $this->l('call')->getCall($call_id);

        $num || $num = 10;
        $list = [];

        $rows = $this->m('talent')->getRandomList($call->getData('area'), $call->getData('system'), $num, $call->user->talent);
        if($rows) {
            foreach ($rows as $talent) {
                $price = $this->m('fastPrice')->getPrice(
                        $talent->getData('max_level'), // 大神的段位
                        $call->getData('max_level') // 请求的段位
                    )  * $call->round;
                if( $price ) {
                    $single = $talent->l()->singleData($talent);
                    $single['price'] = $price;
                    array_push($list, $single);
                }
            }
        }

        return [
            'list' => $list,
            'count' => count($list)
        ];
    }


    /**
     *  商家活动详情页
     */
    public function activityDetail(){
        $id = $this->request->param('id');
//        $param = $this->request->param();
        $activity = $this->l()->getActivityDetail($id);
//        $talent = $activity->talent();
        $talent = $this->m()->getById($activity->talent_id);
        if(!$talent) {
            throw new NotFoundException(['msg'=> '大神不存在']);
        }
        $store = [
            "id"    =>  $talent->id,
            "nick"  =>  $talent->nick,
            "logo"  =>  $talent->logo,
        ];

        return [
            'store'    =>  $store,
            'activity'  =>  $activity
        ];
    }

    /**
     *  商家活动列表
     */
    public function activityList(){
        $idTalent = $this->request->param('talent_id');
        $listActivity = $this->l()->getActivityListByTalent($idTalent);
        return [
            'activity'   =>  $listActivity,
        ];
    }

    /**
     *  商家评论列表
     */
    public function commentList(){
        $id = $this->request->param('talent_id');
        $param = $this->request->param();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;

        $talent = $this->m()->getById($id);
        if(!$talent) {
            throw new NotFoundException(['msg'=> '大神不存在']);
        }

        if($param['fresh_type'] == 0) {
            #商家信息
            $userData = array(
                "id"          =>    $talent->id,
                "logo"      =>  $talent->logo,
                "name"      =>  $talent->nick,
                "color"      =>  $talent->color,
                "score"      =>  $talent->score,
                "area"      =>  $talent->area,
                "type"      =>  $talent->category,
                "new"      =>  $talent->new,
                "vip"      =>  $talent->vip,
                "slogan"    =>  $talent->slogan,
                "tel"   =>  $talent->tel,
                "opentime"  => $talent->opentime
            );
            $result['store'] = $userData;
        }
        # 评论
        $result['comment'] = $this->l()->getComments($talent, $since_id,5);
        return $result;
    }

}