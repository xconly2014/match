<?php

namespace app\api\logic;

use app\api\model\UserRank;
use think\Model;

class Match extends Model
{


    public function gameSportAwards($match)
    {
        $bonus = $match->bonus;
//        $bonus = 1100*72 + $bonus;
        $total = round($bonus / ($match->fee * 0.72));
        $bonus_rate = $this->bonusAllocation($total);
        $special_list = $this->generateSpecial($total, [66], 100, 65);
        $special_bonus = count($special_list) == 0 ? 0 : round($bonus * $bonus_rate['special'] / count($special_list), 2);

        $bonus_allocation = array(
            'normal' => array(
                'special' => number_format($special_bonus, 2, '.', ""),
                'rank0' => number_format($bonus * $bonus_rate['super'], 2, '.', ""),
                'rank1' => number_format($bonus * 0.20 * $bonus_rate['normal1'], 2, '.', ""),
                'rank2' => number_format($bonus * 0.14 * $bonus_rate['normal1'], 2, '.', ""),
                'rank3' => number_format($bonus * 0.10 * $bonus_rate['normal1'], 2, '.', ""),
            ),
            'special' => array(),
            'supper' => array(),
        );
        return $bonus_allocation;
    }

    /**
     *  赛事结算
     */
    public function gameSportSettlement($match)
    {
        $bonus = $match->bonus;
//        $bonus = 1100*72 + $bonus;
        $total = round($bonus / ($match->fee * 0.72), 2);
        $bonus_rate = $this->bonusAllocation($total);
        $supper = [];
        if ($total > 1000) {
            $supper = [
                'id' => 888,
                'avatar' => 'http://resource.mp.cmswat.com/images/icon/frame_nodetermined.png',
                'nick' => '微信用户',
                'awards' => number_format($bonus * $bonus_rate['super'], 2, '.', "")
            ];
        }
        $normal_rate_allocation = [0.20, 0.14, 0.10, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08];
        $normal = [];
        if ($total > 0) {
            $count = 1;
            foreach ($normal_rate_allocation as $normal_rate) {
                $normal[] = [
                    'id' => $count,
                    'avatar' => 'http://resource.mp.cmswat.com/images/icon/user_icon_self.png',
                    'nick' => '微信用户',
                    'awards' => number_format($bonus * $normal_rate * $bonus_rate['normal1'], 2, '.', "")
                ];
                if ($count == $total) break;
                $count++;
            }
        }
//            $special = array(19,20,21,29,30,31,39,40,41,49,50,51,59,60,61);
        $normal_allocation = [];
        $normal_list = $this->generateSpecial($total, [9, 10, 11], 10, 11);
        if (!empty($normal_list)) {
            $normal_bonus = round($bonus * $bonus_rate['normal2'] / count($normal_list), 2);
            $normal_allocation = [];
            foreach ($normal_list as $id) {
                $normal_allocation[] = [
                    'id' => $id,
                    'avatar' => 'http://resource.mp.cmswat.com/images/icon/user_icon_self.png',
                    'nick' => '微信用户',
                    'awards' => number_format($normal_bonus, 2, '.', "")
                ];
            }
        }
        $special_allocation = [];
        $special_list = $this->generateSpecial($total, [66], 100, 65);
        if (!empty($special_list)) {
            $special_bonus = round($bonus * $bonus_rate['special'] / count($special_list), 2);
            foreach ($special_list as $id) {
                $special_allocation[] = [
                    'id' => $id,
                    'avatar' => 'http://resource.mp.cmswat.com/images/icon/user_icon_self.png',
                    'nick' => '微信用户',
                    'awards' => number_format($special_bonus, 2, '.', "")
                ];
            }
        }
        $normal = array_merge($normal, $normal_allocation);
        $mine = [
            'id' => 0,
            'avatar' => 'http://resource.mp.cmswat.com/images/icon/user_icon_self.png',
            'nick' => '微信用户',
            'awards' => 0.00
        ];
        $bonus_allocation = array(
            'total' => $total,
            'normal' => $normal,
            'special' => $special_allocation,
            'supper' => $supper,
            'mine' => $mine,
            'real' => 0,
        );
//        $param = $this->request->param();
        return $bonus_allocation;
    }


    public function bonusAllocation($total)
    {
        $super_rate = 0.3;
        if ($total < 51) {
            $rate = 0.4;
        } elseif ($total < 101) {
            $rate = 0.35;
        } elseif ($total < 201) {
            $rate = 0.30;
        } elseif ($total < 301) {
            $rate = 0.25;
        } elseif ($total < 401) {
            $rate = 0.20;
        } elseif ($total < 501) {
            $rate = 0.15;
        } elseif ($total < 1001) {
            $rate = 0.10;
        } else {
            $rate = 0.05;
        }
        return array(
            'normal1' => $total > 1000 ? $rate * (1 - $super_rate) : $rate,
            'normal2' => $total > 1000 ? (0.80 - $rate * (1 - $super_rate)) : (0.85 - $rate),
            'special' => 0.1,
            'super' => $total > 1000 ? 0.05 : 0,
        );
    }

    public function getMatchRankListByType($match_id, $type)
    {
        $userRank = new UserRank();
        $awardsList = $userRank->getListByType($match_id, $type);
        if (!empty($awardsList)) {
            foreach ($awardsList as $awards) {
                $awards->nick = $this->subStrCut($awards->nick);
            }
        }
        return $awardsList;
    }

    public function subStrCut($str)
    {
        //获取字符串长度
        $strLength = mb_strlen($str, 'utf-8');
        //如果字符创长度小于2，不做任何处理
        if ($strLength < 2) {
            return $str . "**";
        } else {
            //mb_substr — 获取字符串的部分
            $firstStr = mb_substr($str, 0, 1, 'utf-8');
            $lastStr = mb_substr($str, -1, 1, 'utf-8');
            //str_repeat — 重复一个字符串
//        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen-2) . $lastStr;
            return $strLength == 2 ? $firstStr . str_repeat('*', mb_strlen($str, 'utf-8') - 1) : $firstStr . "****" . $lastStr;
        }
    }

    public function generateSpecial($total, $temp, $base, $min = 10)
    {
        $list = [];
        $rate = ceil($total / $base);
        $num = 0;
        while ($num < $rate) {
            foreach ($temp as $value) {
                $value = $value + $base * ($num);
                if ($value > $min && $value <= $total) {
                    array_push($list, $value);
                }
            }
            $num++;
        }
        return $list;
    }
}