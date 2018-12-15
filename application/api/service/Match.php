<?php

namespace app\api\service;

use app\lib\exception\ForbiddenException;
use app\lib\exception\NotFoundException;
use think\Config;

class Match extends AbstractServiceModel
{
    public function lister()
    {
        $param = $this->request->param();
        $param = array_filter($param, 'strlen');
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;
        $server_id = $param['svrid'];
        $uid = $param['uid'];
        $list = [];
        $adList = array(
            array(
                "id" => 1,
                "banner" => "http://resource.mp.cmswat.com/images/icon/banner_affli_reward.png",
                "title" => "邀请玩家参与全民电竞,即可获取现金奖励",
            )
        );
        $marquee = [
            "公告：1，点击右上角”联系客服“咨询赛事相关问题！",
            "公告：2，赛事报名成功，记得用门票去游戏内参赛！",
//            "公告：玩正义枪战，参加比赛赚积分",
//            "公告：玩正义枪战，参加比赛赚积分",
        ];
        $developer = Config::get('developer');
        if ($uid == '0') {
            $matchModel = $this->m('match');
            $listMatch = $matchModel->getList($server_id,$since_id, 10);
            foreach ($listMatch as $match) {
                $list[] = [
                    "id" => $match->id,
                    "icon" => "http://resource.mp.cmswat.com/images/icon/app_saishi_icon.png",
                    "banner" => $match->banner,
                    "title" => $match->title,
                    "bonus" => $developer['audit'] ? null : number_format($match->bonus, 2, '.', ""),
                    "bming" => date("m/d H:i", strtotime($match->bming)),
                    "eming" => date("m/d H:i", strtotime($match->eming)),
                    "bfight" => date("m/d H:i", strtotime($match->bfight)),
                    "efight" => date("m/d H:i", strtotime($match->efight)),
                    "status" => strtotime($match->efight) < time() ? 2 : $match->status,
                ];
            }
            $since_id = empty($match) ? 0 : $match->id;
        } else {
            $userModel = $this->getUser();
            $listTicket = $userModel->getUserTicketList($since_id, 10);
            if (!empty($listTicket)) {
                foreach ($listTicket as $ticket) {
                    $match = $ticket->match;
                    $list[] = [
                        "id" => $match->id,
                        "icon" => "http://resource.mp.cmswat.com/images/icon/app_saishi_icon.png",
                        "banner" => $match->banner,
                        "title" => $match->title,
                        "bonus" => $developer['audit'] ? null : number_format($match->bonus, 2, '.', ""),
                        "fee" => $match->fee,
                        "bming" => date("m/d H:i", strtotime($match->bming)),
                        "eming" => date("m/d H:i", strtotime($match->eming)),
                        "bfight" => date("m/d H:i", strtotime($match->bfight)),
                        "efight" => date("m/d H:i", strtotime($match->efight)),
                        "status" => strtotime($match->efight) < time() ? 2 : $match->status,
                    ];
                    $since_id = empty($match) ? 0 : $match->id;
                }
            }
        }

        $data = [
            "list" => $list,
            "adList" => $adList,
            "marquee"   =>  $marquee,
            "since_id" => $since_id,
            "count" => count($list)
        ];
        return $data;
    }

    public function detail()
    {
        $param = $this->request->param();
        $param = array_filter($param, 'strlen');
        $match = $this->m()->getMatchById($param['id']);
        $enroll = 0;
        $developer = Config::get('developer');
        if ($param['uid'] != '0') {
            $user = $this->getUser();
            $uid = $user->id;
            $ticket = $this->m('userTicket')->getUserTicketStatus($param['id'], $uid);
            $enroll = empty($ticket) ? $enroll : $ticket->status;
        }
        $time = time();
        $bonus = $match->bonus;
//        $bonus = 1100*72 + $bonus;
        $bonus_allocation = $this->l()->gameSportAwards($match);
        $data = [
            "id" => $match->id,
            "icon" => "http://resource.mp.cmswat.com/images/icon/app_saishi_icon.png",
            "banner" => $match->banner,
            "title" => $match->title,
            "model_name" => $match->model_name,
            "bonus" => $developer['audit'] ? null : number_format($bonus, 2, '.', ""),
            "fee" => $match->fee,
            "bming" => date("m/d H:i", strtotime($match->bming)),
            "eming" => date("m/d H:i", strtotime($match->eming)),
            "bfight" => date("m/d H:i", strtotime($match->bfight)),
            "efight" => date("m/d H:i", strtotime($match->efight)),
            "item_pic1" =>  $match->item_pic1,
            "item_name1" =>  $match->item_name1,
            "item_pic2" =>  $match->item_pic2,
            "item_name2" =>  $match->item_name2,
            "item_pic3" =>  $match->item_pic3,
            "item_name3" =>  $match->item_name3,
            "status" => strtotime($match->efight) < $time ? 2 : (strtotime($match->eming) < $time ? 1 : 0),
            'enroll' => $enroll,
            "bonus_allocation" => $bonus_allocation,
        ];
        return $data;
    }


    /**
     *  报名比赛
     */
    public function call()
    {
        $user = $this->getUser();
        if (empty($user->role_id) || empty($user->role_name)) {
            throw new NotFoundException(['msg' => '游戏账号不存在,请到用户中心绑定']);
        }
        $param = $this->request->param();
        $param['user_id'] = $user->id;
        $param['create_time'] = time();
        $ticket = $this->m('userTicket')->getUserTicketStatus($param['match_id'], $user->id);
        if (empty($ticket)) {
            $ticket = $this->m('userTicket');
            $ticket->data($param)->save();
        }
        return [
            'ticket_id' => intval($ticket->id),
            'match'       =>    $ticket->match,
        ];
    }

    /**
     *  完成报名
     */
    public function callPass()
    {
        $user = $this->getUser();
        $param = $this->request->param();
        $ticket = $this->m('userTicket')->getTicketById($param['ticket_id']);
        if (empty($ticket)) {
            throw new NotFoundException(['msg' => '订单不存在']);
        }
//        if ($ticket->user_id != $user->id) {
//            throw new ForbiddenException(['msg' => '这不是你的订单']);
//        }
        $ticket->status = 2;
//        $ticket->ticketOrder->trade_no && $ticket->order_id = $ticket->ticketOrder->trade_no;
        $ticket->save();

        ## 分配报名费
        $this->m()->execute("CALL sp_invite_awards({$user->id},{$ticket->match->fee},201,{$ticket->match_id})");
        ## 发送道具.
        $gift1 = $ticket->match->gift_id1;
        $gift2 = $ticket->match->gift_id2;
        $json = '{"para":{"idServer":"100068","uid":"","title":"赏金争夺赛","content":"亲爱的战友，恭喜您成功在小程序报名赏金争夺赛，请附件查收获得礼包和比赛入场券。请尽快使用入场券去精英赛-挑战赛报名对应赛事。","createaccount":"MP","platform":"0","iditem1":"0","numitem1":"0","iditem2":"0","numitem2":"0"},"time":1483769941,"appkey":"123d6853c9950721090174e8bf9bb25b"}';
        $arrGameSport = json_decode($json, true);
        $config = Config::get('cmswat');
        $arrGameSport['para']['idServer'] = $config['serverId'];
        $arrGameSport['para']['uid'] = $user->role_id;
        if (!empty($gift1)) {
            list($iditem1, $numitem1) = explode('*', $gift1);
            $arrGameSport['para']['iditem1'] = $iditem1;
            $arrGameSport['para']['numitem1'] = $numitem1;
        }
        if (!empty($gift2)) {
            list($iditem2, $numitem2) = explode('*', $gift2);
            $arrGameSport['para']['iditem2'] = $iditem2;
            $arrGameSport['para']['numitem2'] = $numitem2;
        }
        $urlGameSport = $config['serverUrl'] . "/email/emailServerSend.php?" . http_build_query($arrGameSport, NULL, "&");
        $callBack = json_decode(file_get_contents($urlGameSport), true);

        return [
//            'match' =>  $ticket->match,
            'url' => $urlGameSport,
            'callBack' => $callBack
        ];
    }

    /**
     *  赛事排名同步
     */
    public function rankSportList()
    {
        $param = $this->request->param();
        $match = $this->m()->getMatchById($param['match_id']);
        if (empty($match)) {
            throw new NotFoundException(['msg' => '该赛事不存在']);
        }
//        return $param;
        $json = '{"para":{"idServer":"500035","sid":"2018022106","lookMinRank":"0","lookMaxRank":"20"},"time":1540958480,"appkey":"343d3eaccd1ef98bde0511910f041e87"}';
        $arrGameSport = json_decode($json, true);
        $config = Config::get('cmswat');
        $arrGameSport['para']['idServer'] = $config['serverId'];
        $arrGameSport['para']['sid'] = $match->match_id;
        $urlGameSport = $config['serverUrl'] . "/sport/getRankUserRaceSportList.php?" . http_build_query($arrGameSport, NULL, "&");
        $callBack = json_decode(file_get_contents($urlGameSport), true);
        return $callBack;
    }

    /**
     *  配置赛事活动
     */
    public function gameSportConfig()
    {
        $param = $this->request->param();
        if (!empty($param['match_id'])) {
            $json = '{"para":{"idServer":"100068","match_id":"2018100110"},"time":1540958480,"appkey":"343d3eaccd1ef98bde0511910f041e87"}';
            $arrGameSport = json_decode($json, true);
            $config = Config::get('cmswat');
            $arrGameSport['para']['idServer'] = $config['serverId'];
            $arrGameSport['para']['match_id'] = $param['match_id'];
            $urlGameSport = $config['serverUrl'] . "/sport/getRaceSportDetail.php?" . http_build_query($arrGameSport, NULL, "&");
            $callBack = json_decode(file_get_contents($urlGameSport), true);
            return $callBack;
        }
        return $param;
    }

    /**
     *  赛事取消
     */
    public function gameSportCancel()
    {
        $param = $this->request->param();
        return $param;
    }

    /**
     *  赛事完成
     */
    public function gameSportComplete()
    {
        $param = $this->request->param();
        $match_id = $param['match_id'];
        $match = $this->m()->getMatchById($param['match_id']);
        if (empty($match)) {
            throw new NotFoundException(['msg' => '该赛事不存在']);
        }
        $game_id = $match->match_id;
        $json = '{"para":{"idServer":"","sid":"","lookMinRank":"0","lookMaxRank":"100"},"time":1540958480,"appkey":"343d3eaccd1ef98bde0511910f041e87"}';
        $arrGameSport = json_decode($json, true);
        $config = Config::get('cmswat');
        $arrGameSport['para']['idServer'] = $config['serverId'];
        $arrGameSport['para']['sid'] = $game_id;
        $urlGameSport = $config['serverUrl'] . "/sport/getAllRankUserRaceSportList.php?" . http_build_query($arrGameSport, NULL, "&");
        $callBack = json_decode(file_get_contents($urlGameSport), true);
        $dataGameRank = $callBack['rankUserRaceSportList']['data'];
        if (!empty($dataGameRank)) {
            $count = 1;
            foreach ($dataGameRank as $gameRank) {
                $userRank[] = array(
                    'rank' => empty($gameRank['rank']) ? $count : $gameRank['rank'],
                    'match_id' => $match_id,
                    'role_id' => $gameRank['userid'],
                    'role_name' => $gameRank['nick'],
                    'score' => $gameRank['data'],
                );
                $count++;
            }
            $data = [
//                'efight' => date("YmdHis"),
                'status' => 2,
            ];
            $match->save($data);
            if (!empty($userRank)) {
                $this->m('UserRank')->insertAll($userRank);
                $this->m()->execute("CALL sp_user_rank({$match_id})");
            }
        }
        return [
            'callback' => $callBack,
            'url' => $urlGameSport,
        ];
    }

    /**
     *  赛事结算
     */
    public function gameSportSettlement()
    {
        $param = $this->request->param();
        $match = $this->m()->getMatchById($param['match_id']);
        $match_id = $param['match_id'];
        $uid = $param['uid'];
        $mine = [
            'id' => 0,
            'avatar' => 'http://resource.mp.cmswat.com/images/icon/user_icon_self.png',
            'nick' => '微信用户',
            'awards' => "0.00"
        ];
        $total = $this->m('UserRank')->getMatchRankTotal($match_id);
        if (!empty($uid)) {
            $mine = $this->m('UserRank')->getMatchRankByUid($match_id, $uid);
            if (empty($mine)) {
                $userModel = $this->m('User')->getByUid($uid);
                $mine = [
                    'id' => 0,
                    'avatar' => $userModel->icon,
                    'nick' => $userModel->nick,
                    'awards' => "0.00"
                ];
            }
        }
        if ($total > 0) {
            $normalAwards1 = $this->l()->getMatchRankListByType($match_id, 1);
            $normalAwards2 = $this->l()->getMatchRankListByType($match_id, 2);
            $specialAwards = $this->l()->getMatchRankListByType($match_id, 3);
            $supperAwards = $this->l()->getMatchRankListByType($match_id, 4);
            $normalAwards = array_merge($normalAwards1, $normalAwards2);
            $bonus_allocation = array(
                'total' => $total,
                'normal' => $normalAwards,
                'special' => $specialAwards,
                'supper' => $supperAwards,
                'mine' => $mine,
                'real' => 1,
            );
        } else {
            $bonus_allocation = $this->l()->gameSportSettlement($match);
            $bonus_allocation['mine'] = $mine;
        }
        return $bonus_allocation;
    }

}