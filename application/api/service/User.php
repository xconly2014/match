<?php
# service\Talent;

namespace app\api\service;

use app\extra\WxPayBaseConfig;
use app\lib\exception\ForbiddenException;
use app\lib\jiguang\JG;
use think\Config;
use wxmp\WXBizDataCrypt;
use app\lib\exception\NotFoundException;

class User extends AbstractServiceModel
{
    /**
     * 登录
     * @return mixed
     */
    public function login() {
        $param = $this->request->param();
        $param = array_filter($param, 'strlen');

        $user = $this->m()->getByLoginType($param['type'], $param['token']);
        $this->l()->markLogin($user);
        //注册
        if(empty($user->nick)){
            if( isset($param['level0']) && !empty($param['level0'])){
                $userInvite = $this->m()->getByUid($param['level0']);
                !empty($userInvite->level0) && $param['level1'] = $userInvite->level0;
            }
            $user->edit($param);
        }
        if(isset($param['reg_id'])) {
            $user->updateRegId($param['reg_id']); // 更新极光推送reg_id, 并推送登录过期消息给旧设备
        }
        $user->save();
        return $this->l()->baseInfo($user);
    }

    /**
     * 退出登录
     */
    public function logout() {
        $user = $this->getUser();
        $this->l()->markLogout($user);
        if($user->talent) {
            $user->talent->switch_on = 0;
            $user->talent->save();
        }
        JG::instance()->resetTags($user);
        $user->save();
    }

    /**
     * 主页
     * @return mixed
     */
    public function homeDate() {
        $user = $this->getUser();
        $data = $this->l()->baseInfo($user);

        $user_unread_num = $this->m('order')->where('user_id', $user->id)->where('pay_type', '>', 0)->where('user_read_flag', 0)->count();
        $talent_unread_num = $user->talent ? $this->m('order')->where('talent_id', $user->talent->id)->where('pay_type', '>', 0)->where('talent_read_flag', 0)->count() : 0;

        $data['order_unread_num'] = $user_unread_num + $talent_unread_num;
        $data['talent_auth_status'] = $this->l()->getTalentAuthStatus($user);
        $data['talent_id'] =  $user->talent ? $user->talent->id : 0;
        $data['talent_switch_on'] = $user->talent ? $user->talent->getData('switch_on') : 0;
        $data['talent_lock'] = $user->talent ? $this->l('talent')->isLocked($user->talent) : 0;
        return $data;
    }

    /**
     * 我的钱包账户
     */
    public function balance() {
        $user = $this->getUser();
        $param = $this->request->param();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;

        if($param['fresh_type'] == 0) {
            # 余额
            $data['balance'] = $user->reviseBalance();
            # 充值项目
            $data['plans'] = [
                ['gold_num'=>7, 'price'=>6.0],
                ['gold_num'=>14, 'price'=>12.0],
                ['gold_num'=>33, 'price'=>30.0],
                ['gold_num'=>55, 'price'=>50.0],
                ['gold_num'=>135, 'price'=>128.0],
                ['gold_num'=>530, 'price'=>518.0],
            ];
        }

        # 收支明细
        $list = $user->getBalanceList($since_id);
        $data['list'] = [];
        foreach($list as $balance) {
            $data['list'][] = [
                'trade_type' => $balance->getData('trade_type'),
                'trade_type_des' => $balance->trade_type,
                'total_fee' => round($balance->total_fee / 100),
                'cur_balance' =>  round($balance->cur_balance / 100),
                'trade_no' => $balance->trade_no,
                'create_time' => $balance->create_time
            ];
            $since_id = $balance->id;
        }
        $data['count'] = count($data['list']);
        $data['since_id'] = $since_id;
        return $data;
    }

    /**
     * 我的收益
     */
    public function income() {
        $user = $this->getUser();
        $param = $this->request->param();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;

        if($param['fresh_type'] == 0) {
            $data['income'] = $user->reviseIncome();
            $apply_amount = $this->m('incOrder')->sumApplyAmountByUserAndAuthStatus($user, 1);
            $settle_amount = $this->m('incOrder')->sumSettleAmountByUserAndAuthStatus($user, 2);
            $apply_amount || $apply_amount = 0;
            $settle_amount || $settle_amount = 0;
            $data['applying_amount'] = $apply_amount / 100;
            $data['total_settle_amount'] = $settle_amount / 100;

            $data['bind'] = $user->wdAccount ?  [
                'account_type' => $user->wdAccount->getData('account_type'),
                'account_type_des' => $user->wdAccount->account_type,
                'account' => $user->wdAccount->account,
                'real_name' => $user->wdAccount->real_name,
            ] : [
                'account_type' => 0,
                'account_type_des' => '',
                'account' => '',
                'real_name' => ''
            ];
        }

        $data['list'] = [];
        $list = $user->getIncomeList($since_id);
        foreach($list as $item) {
            $data['list'][] = [
                'trade_type' => $item->getData('trade_type'),
                'total_fee' => round($item->total_fee / 100),
                'service_fee' =>  round($item->service_fee / 100),
                'cur_income' =>  round($item->cur_income / 100),
                'trade_no' => $item->trade_no,
                'create_time' => $item->create_time
            ];
            $since_id = $item->id;
        }
        $data['count'] = count($data['list']);
        $data['since_id'] = $since_id;
        return $data;
    }

    /**
     * 详情
     * @return mixed
     */
    public function detailData() {
        $user = $this->getUser();
        return $this->l()->detailInfo($user);
    }

    /**
     * 编辑
     */
    public function updateByRequest() {
        $data = $this->request->param();
        $user = $this->getUser();

        if(!empty($data['role_id']) && !empty($data['role_name'])){
            $userExist = $this->m()->getUserByRoleId($data['role_id']);
            if($userExist && strcmp($userExist->uid,$user->uid)){
                throw new NotFoundException(['msg'=>'该账号已和其他账号绑定.']);
            }
            $json = '{"para":{"uid":"","nick":"","idServer":"100068"},"time":1529647471,"appkey":"991d40dcc9a327444250d75c4aa8fa1d"}';
            $arrServerEmail = json_decode($json, true);
            $config = Config::get('cmswat');
            $arrServerEmail['para']['idServer'] = $config['serverId'];
            $arrServerEmail['para']['uid'] = $data['role_id'];
            $arrServerEmail['para']['nick'] = $data['role_name'];
            $urlEmailSend = $config['serverUrl']."/user/getUserServerExist.php?" . http_build_query($arrServerEmail, NULL, "&");
            $callBack = json_decode(file_get_contents($urlEmailSend), true);
            if(empty($callBack) || $callBack['data']['exist'] == 0){
                throw new NotFoundException(['msg'=> '游戏账号不匹配']);
            }
            $user->edit($data)->save();
        }
//        return empty($callBack) ? array() : $callBack;
    }

    /**
     *  绑定手机号码
     */
    public function updatePhoneByRequest(){
        $data = $this->request->param();
        $wxBizEncrypt = new WXBizDataCrypt(WxPayBaseConfig::APPID,$data['session_key']);
        $errCode = $wxBizEncrypt->decryptData($data['encryptedData'],$data['iv'],$arrRst);
        $arrRst['code'] = $errCode;
        if($errCode == 0){
            $data = ['phone' => $arrRst['phoneNumber']];
            $user = $this->getUser();
            $user->edit($data)->save();
        }
        return $arrRst;
    }

    /**
     * 我的快车订单
     * @return array
     */
    public function orderList() {
        $param = $this->request->param();
        $user = $this->getUser();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;

        $rows = [];
        $length = 5;
        # 未读徽标
        $result = [
            'user_unread_num' => $this->m('order')->where('user_id', $user->id)->where('pay_type', '>', 1)->where('user_read_flag', 0)->count(),
            'talent_unread_num' => $user->talent ? $this->m('order')->where('talent_id', $user->talent->id)->where('pay_type', '>', 1)->where('talent_read_flag', 0)->count() : 0,
        ];

        if($param['data_type'] == 1) { // 我发起的
            $rows = $this->m('order')->getListByUserId($user->id, $since_id, $length);
        } elseif ($param['data_type'] == 2) { // 我收到的
            if($user->talent) {
                $rows = $this->m('order')->getListByTalentId($user->talent->id, $since_id, $length);
            }
        }
        $result += $this->l('order')->formatRows($rows);
        return $result;
    }

    /**
     * 提现账号绑定
     */
    public function wdBind() {
        $user = $this->getUser();
        $param = $this->request->param();
        $param['user_id'] = $user->id;
        $wd = $user->wdAccount;
        if(!$wd) {
            $wd = $this->m('UserWdAccount');
        }
        $wd->data($param);
        return $wd->save();
    }

    /**
     * 订单消息列表
     */
    public function orderMsg() {
        $param = $this->request->param();
        $user = $this->getUser();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;

        $list = $this->m('orderMessage')->getListByUserId($user->id, $since_id);
        $data['list'] = [];
        foreach($list as $msg) {
            $order = $msg->orderObj;
            if($order) {
                $single = [
                    'id' => $msg->id,
                    'message_type' => $msg->getData('type'),
                    'content' => $msg->content ? $msg->content : '',
                ];

                $info = $order->l()->detailInfo($order);
                $single += $info;

                $data['list'][] = $single;
                $since_id = $msg->id;
            }
        }
        $data['count'] = count($data['list']);
        $data['since_id'] = $since_id;
        return $data;
    }

    /**
     * 系统消息列表
     */
    public function sysMsg() {
        $param = $this->request->param();
        $user = $this->getUser();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;

        $list = $this->m('sysMessage')->getListByUserId($user->id, $since_id);
        $data['list'] = [];
        foreach($list as $msg) {
            $single = [
                'id' => $msg->id,
                'message_type' => $msg->getData('type'),
                'content' => $msg->content ? $msg->content : '',
                'create_time' => $msg->create_time,
            ];
            $extras = $msg->extras ? unserialize($msg->extras) : [];
            empty($extras) || $single['extras'] = $extras;
            $data['list'][] = $single;
            $since_id = $msg->id;
        }
        $data['count'] = count($data['list']);
        $data['since_id'] = $since_id;
        return $data;
    }

    /**
     *  用户的分级邀请用户
     */
    public function userInvitedList(){
        $param = $this->request->param();
        $user = $this->getUser();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;
        $list = $this->m()->getUserInvitedList($param['data_type'],$user->uid,$param['since_id']);
        $listUserInvited = array();
        foreach ($list as $userInvited){
            $listUserInvited[] = [
                "uid"   =>  $userInvited->uid,
                "nick"  =>  $userInvited->nick,
                "icon"  =>  $userInvited->icon,
                "register"  => $userInvited->register,
            ];
            $since_id = $userInvited->id;
        }
        $data = [
            "count" =>  count($listUserInvited),
            "since_id"  =>  $since_id,
            "list"      =>  $listUserInvited,
        ];
        return $data;
    }
}