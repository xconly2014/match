<?php

namespace app\_cmd\TD;

use app\api\model\User AS UserModel;
use app\api\model\Match AS MatchModel;
use app\api\model\UserTicket AS TicketModel;
use app\api\model\UserRank AS RankModel;
use app\api\model\Order AS OrderModel;
use app\extra\WxPayBaseConfig;
use app\lib\Log;
use think\Config;

class Match extends AbstractEntity
{
    public function run($action)
    {
        // TODO: Implement run() method.
        $matchModel = new MatchModel;
        Log::cmd($action);
        echo "[$action]: - ".date("Y-m-d H:i:s")." - ";
        switch ($action) {
            case  'getGameWarmingUpMeg':
                $date = date("YmdHi",(time()+1800))."00";
                echo $date.PHP_EOL;
//                $date = date("20181117200000");
                $match = $matchModel->where('bfight', "{$date}")
                    ->where('status',0)
                    ->field(['id', 'title', 'match_id', 'model_name', 'bonus','bfight'])->find();
                if(!empty($match)){
                    echo $match->getLastSql().PHP_EOL;
                }
                Log::cmd(json_encode($match,JSON_UNESCAPED_UNICODE));
                echo json_encode($match,JSON_UNESCAPED_UNICODE).PHP_EOL;
                if ($match) {
                    $orderModel = new OrderModel;
                    $orderList = $orderModel->where('match_id', $match->id)
                        ->where('pay_type', 1)
                        ->field('user_id,prepay_id,trade_no')
                        ->select();
//                    echo $match->getLastSql();
                    Log::cmd(json_encode($orderList,JSON_UNESCAPED_UNICODE));
                    if (!empty($orderList)) {
                        $userList = [];
                        foreach ($orderList as $order) {
                            $userList[] = [
                                'touser' => $order->user->wxid,
                                'form_id' => $order->prepay_id,
                            ];
                        }
//                        echo json_encode($userList);
                    }
                    $data = [];
                    if (!empty($userList)) {
                        $uriGetAccessToken = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
                        $uriGetAccessToken = sprintf($uriGetAccessToken, WxPayBaseConfig::APPID, WxPayBaseConfig::APPSECRET);
                        $data = json_decode(file_get_contents($uriGetAccessToken), true);
                        if (isset($data['access_token'])) {
                            $access_token = $data['access_token'];
                            $message = [
                                'touser' => 'orMiZ5buDasMYIUNqPvLyeqc2wgM',
                                'template_id' => '2aW7JD-IgMIBQaz7qAcRm-Zw-6AT_8TrrYfKtTAy-1c',
                                'url' => '/pages/index/index',
                                'form_id' => 'wx1314534482257720a49346362555071128',
                                'data' => [
                                    'keyword1' => ['value' => '正义枪战精英赛', 'color' => '#173177'],
                                    'keyword2' => ['value' => '赏金争夺赛', 'color' => '#173177'],
                                    'keyword3' => ['value' => '个人歼灭', 'color' => '#173177'],
                                    'keyword4' => ['value' => '第一场', 'color' => '#173177'],
                                    'keyword5' => ['value' => '100', 'color' => '#173177'],
                                    'keyword6' => ['value' => '玩正义枪战，参加比赛赚积分', 'color' => '#173177'],
                                    'keyword7' => ['value' => '2018-11-13 20:00', 'color' => '#173177'],
                                    'keyword8' => ['value' => '记得用门票去游戏内精英赛参赛', 'color' => '#173177'],
                                ]
                            ];
                            foreach ($userList as $user) {
                                $message['touser'] = $user['touser'];
                                $message['form_id'] = $user['form_id'];
                                $message['data']['keyword2']['value'] = $match->title;
                                $message['data']['keyword3'] ['value']= $match->model_name;
                                $message['data']['keyword5'] ['value']= $match->bonus;
                                $message['data']['keyword7'] ['value']= date("Y-m-d H:i", strtotime($match->bfight));
                                $params = json_encode($message, JSON_UNESCAPED_UNICODE);
//                                echo $params.PHP_EOL;
//                                break;
                                //发模板消息
                                $fp = fsockopen('api.weixin.qq.com', 80, $error, $errstr, 1);
                                $http = "POST /cgi-bin/message/wxopen/template/send?access_token={$access_token} HTTP/1.1\r\nHost: api.weixin.qq.com\r\nContent-type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($params) . "\r\nConnection:close\r\n\r\n$params\r\n\r\n";
                                fwrite($fp, $http);
                                fclose($fp);
//                                sleep(1);
                            }
                        }
                    }
                    Log::cmd(json_encode($data,JSON_UNESCAPED_UNICODE));
                }
                break;
            case 'getGameSportCancel':
                //赛事取消.
                $date = date("YmdHi") . "00";
                echo $date.PHP_EOL;
                $match = $matchModel->where('eming', "{$date}")->field(['id', 'title', 'match_id', 'model_name', 'bonus'])->find();
                if ($match) {
                    $json = '{"para":{"idServer":"100068","sid":"2018021880"},"time":1540958480,"appkey":"343d3eaccd1ef98bde0511910f041e87"}';
                    $arrGameSport = json_decode($json, true);
                    $config = Config::get('cmswat');
                    $arrGameSport['para']['idServer'] = $config['serverId'];
                    $arrGameSport['para']['sid'] = $match->match_id;
                    $urlEmailSend = $config['serverUrl'] . "/sport/getLogRaceSportDetail.php?" . http_build_query($arrGameSport, NULL, "&");
                    $callBack = json_decode(file_get_contents($urlEmailSend), true);
                    if (!empty($callBack) && array_key_exists('logRaceSportDetail', $callBack) && $callBack['logRaceSportDetail']['bcancel'] == 1) {
                        //赛事取消通知.
                        $match = $matchModel->getMatchById($match->id);
                        $match->save(['status' => 3]);
                    }
                }
                break;
            case 'getGameSportComplete':
                //赛事结算
                $date = date("YmdHi") . "00";
//                $date = date("20181117201500");
                echo $date.PHP_EOL;
                $match = $matchModel
                    ->where('efight', "{$date}")
                    ->where('status', 0)
                    ->field(['id', 'title', 'match_id', 'model_name', 'svrid'])->find();
                if(!empty($match)){
                    echo $match->getLastSql().PHP_EOL;
                }
                echo json_encode($match, JSON_UNESCAPED_UNICODE).PHP_EOL;
                Log::cmd(json_encode($match, JSON_UNESCAPED_UNICODE));
                if (!empty($match)) {
                    $match_id = $match->id;
                    $game_id = $match->match_id;
                    $json = '{"para":{"idServer":"","sid":"","lookMinRank":"0","lookMaxRank":"100"},"time":1540958480,"appkey":"343d3eaccd1ef98bde0511910f041e87"}';
                    $arrGameSport = json_decode($json, true);
                    $config = Config::get('cmswat');
                    $arrGameSport['para']['idServer'] = $config['serverId'];
                    $arrGameSport['para']['sid'] = $game_id;
                    $urlGameSport = $config['serverUrl'] . "/sport/getAllRankUserRaceSportList.php?" . http_build_query($arrGameSport, NULL, "&");
                    $callBack = json_decode(file_get_contents($urlGameSport), true);
                    $dataGameRank = $callBack['rankUserRaceSportList']['data'];
                    Log::cmd($urlGameSport);
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
                        if (!empty($userRank)) {
//                            echo json_encode($userRank,JSON_UNESCAPED_UNICODE).PHP_EOL;
                            $rankModel = new RankModel();
                            $rankModel->insertAll($userRank);
                            $rankModel->execute("CALL sp_user_rank({$match_id})");
                        }
                        //更新赛事状态
                        $data = [
                            'efight' => date("YmdHis"),
                            'status' => 2,
                        ];
                        $match->save($data);
                    }
                }
                break;
            default:
                echo "Unknown Error".PHP_EOL;
        }
    }
}