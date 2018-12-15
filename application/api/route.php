<?php
# route.php
use think\Route;
# 用户
Route::post('api/:version/user/login', 'api/:version.user/login'); //1-1.登录
Route::get('api/:version/user/:uid/logout', 'api/:version.user/logout'); //登出
Route::get('api/:version/user/:uid/home', 'api/:version.user/home'); //主页
Route::get('api/:version/user/:uid', 'api/:version.user/read'); //详情
Route::put('api/:version/user/:uid', 'api/:version.user/update'); //编辑
Route::put('api/:version/user/:uid/phone', 'api/:version.user/updatePhone'); //绑定手机号码
Route::get('api/:version/user/:uid/order', 'api/:version.user/orderList', ['api_cache' => null]); //1-6.我的快车订单
Route::get('api/:version/user/:uid/balance', 'api/:version.user/balance'); //1-7.我的钱包账户
Route::get('api/:version/user/:uid/income', 'api/:version.user/income'); //1-8.我的收益
Route::post('api/:version/user/:uid/wd_bind', 'api/:version.user/wdbind'); //1-9.提现账号绑定
Route::get('api/:version/user/:uid/orderMsg', 'api/:version.user/orderMsg'); //1-10.订单消息列表
Route::get('api/:version/user/:uid/sysMsg', 'api/:version.user/sysMsg'); //1-11.系统消息列表
Route::get('api/:version/user/:uid/level', 'api/:version.user/userInvitedList'); //1-12.系统消息列表

## 赛事
Route::get('api/:version/match', 'api/:version.match/lister'); //2-1. 赛事列表
Route::get('api/:version/match/:id', 'api/:version.match/detail', ['api_cache' => null], ['id'=>'\d+']);// 赛事详情
Route::get('api/:version/match/call', 'api/:version.match/call'); //2-3.报名参赛
Route::post('api/:version/match/callPass', 'api/:version.match/callPass'); //2-4.报名参赛
Route::post('api/:version/match/rankSportList', 'api/:version.match/rankSportList'); //2-5.赛事排名
Route::post('api/:version/match/gameSportConfig', 'api/:version.match/gameSportConfig'); //2-6.配置赛事
Route::post('api/:version/match/gameSportCancel', 'api/:version.match/gameSportCancel'); //2-7.赛事取消
Route::post('api/:version/match/gameSportComplete', 'api/:version.match/gameSportComplete'); //2-8.赛事完成
Route::get('api/:version/match/gameSportSettlement', 'api/:version.match/gameSportSettlement',['api_cache' => 15]); //2-9.赛事结算

# 大神
Route::post('api/:version/talent', 'api/:version.talent/apply'); //2-1.申请成为大神
Route::get('api/:version/talent', 'api/:version.talent/lister', ['api_cache' => null]); //2-2.大神or美女列表 60
Route::get('api/:version/talent/:id', 'api/:version.talent/read', ['api_cache' => 15], ['id'=>'\d+']);// 大神详情
Route::get('api/:version/talent/audit', 'api/:version.talent/simpleInfo'); //大神审核详情
Route::put('api/:version/talent/:id/switch', 'api/:version.talent/switchOn'); //2-5.大神接单开关
Route::get('api/:version/talent/call', 'api/:version.talent/call'); //2-6.呼叫老司机
Route::get('api/:version/talent/race', 'api/:version.talent/race'); //2-7.接单（老司机）
Route::delete('api/:version/talent/call', 'api/:version.talent/cancelCall'); //2-8.取消呼叫
Route::get('api/:version/talent/race/:id', 'api/:version.talent/raceDetail', ['api_cache' => null]); //2-9.抢单信息
Route::get('api/:version/talent/random', 'api/:version.talent/random', ['api_cache' => null]); //2-10.随机大神列表
Route::get('api/:version/activity/:id', 'api/:version.talent/activityDetail', ['api_cache' => null]); //2-11.商家活动详情页.
Route::get('api/:version/activity/list/:talent_id', 'api/:version.talent/activityList', ['api_cache' => null]); //2-12.商家活动列表
Route::get('api/:version/comment/list/:talent_id', 'api/:version.talent/commentList', ['api_cache' => null]); //2-13.商家评价列表
# 订单
Route::get('api/:version/fast', 'api/:version.order/fast', ['api_cache' => 60]); // 快车价格表
Route::get('api/:version/order/:order_id', 'api/:version.order/read');
Route::post('api/:version/order', 'api/:version.order/newOrder'); //约Ta
Route::get('api/:version/order/:order_id/receive', 'api/:version.order/receive'); // 大神接收订单
Route::get('api/:version/order/:order_id/start', 'api/:version.order/start'); // 开始游戏
Route::get('api/:version/order/:order_id/finish', 'api/:version.order/finish'); //3-6.大神完成订单
Route::get('api/:version/order/:order_id/cancel', 'api/:version.order/Cancel'); // 买家取消订单
Route::post('api/:version/order/:order_id/comment', 'api/:version.order/comment');// 买家评论订单
Route::post('api/:version/order/:order_id/appeal', 'api/:version.order/appeal'); //3-9.申诉
# 支付
Route::get('api/:version/pay/wx_pay', 'api/:version.pay/wxPay'); // 微信支付参数
Route::get('api/:version/pay/wx_pay_mp', 'api/:version.pay/wxPayMP'); // 微信小程序支付参数
Route::get('api/:version/pay/alipay', 'api/v1.pay/aliPay'); // 支付宝支付参数
Route::post('api/:version/pay/balance', 'api/:version.pay/balance'); //4-3.余额支付
Route::get('api/:version/balance/top', 'api/:version.pay/balancetop'); //4-4.余额充值
Route::get('api/:version/balance/topEnd', 'api/:version.pay/balanceTopEnd', ['api_cache' => null]); //4-4-1.余额充值完成
Route::post('api/:version/income/wd', 'api/:version.pay/wd'); //4-5.收益提现申请
Route::post('api/:version/income/wdPass', 'api/:version.pay/wdPass'); //4-5. 完成提现
Route::post('api/:version/income/wdRefused', 'api/:version.pay/wdRefused'); //4-5. 完成提现
Route::get('api/:version/income', 'api/:version.pay/incList', ['api_cache' => 15]); //4-6.收益提现记录
Route::get('api/:version/income/wdPriceList', 'api/:version.pay/wdPriceList', ['api_cache' => 15]); //4-6.收益提现记录
Route::get('api/:version/pay/cert', 'api/:version.pay/cert'); //4-7. 大神认证费

Route::rule('notify/wx', 'index/notify/wx'); // 微信支付回调
Route::rule('notify/ali', 'index/notify/ali'); // 支付宝支付回调

# 优惠券
Route::get('api/:version/coupon/list', 'api/:version.coupon/lister', ['api_cache' => null]); //5-1.商家优惠券列表
Route::get('api/:version/coupon/detail/:id', 'api/:version.coupon/detail', ['api_cache' => null]); //5-2.商家优惠券列表
Route::get('api/:version/coupon/user/:uid', 'api/:version.coupon/userLister', ['api_cache' => null]); //5-3.用户优惠券列表

# 会员
Route::get('api/:version/vip/list', 'api/:version.vip/lister', ['api_cache' => null]); //6-1.商家会员列表
Route::get('api/:version/vip/user', 'api/:version.vip/userLister', ['api_cache' => null]); //6-1.用户会员卡列表

# 其他
Route::post('api/:version/qiniu/token', 'api/:version.other/qiniuToken',['api_cache' => 3600]); //0-1.七牛上传凭证
Route::get('api/:version/video', 'api/:version.other/videoList'); //0-2.视频列表
Route::get('api/:version/other/urls', 'api/:version.other/urls', ['api_cache' => 60]); //0-3.H5链接地址
Route::get('api/:version/wechat/openid', 'api/:version.other/wxMiniOpenid'); //0-4.获取用户的Openid
Route::get('api/:version/game/userInfo/:role_id', 'api/:version.other/userGameInfo',['role_id'=>'\d+']); //0-5.获取游戏账号信息


