<?php
/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/
use think\Route;

//Route::rule('/', 'manage/home/index');
//Miss 404
//Miss 路由开启后，默认的普通模式也将无法访问
Route::miss('api/v1.Miss/miss');

// 文档后台
Route::rule('api/docs/login', 'docs/home/login');
Route::get('api/docs', 'docs/home/index');
Route::get('api/docs/doc', 'docs/home/doc');
Route::get('api/docs/log', 'docs/log/index');
Route::get('api/docs/log/:dir/:file', 'docs/log/content');
Route::get('api/docs/info', 'docs/info/index');
Route::rule('api/docs/edit', 'docs/edit/index');


//管理后台
Route::rule('manage/login', 'manage/access/login');
Route::rule('manage/logout', 'manage/access/logout');
Route::get('manage$', 'manage/home/index');
Route::get('manage/index', 'manage/home/index');
Route::get('manage/talent/[:status]', 'manage/talent/index');
Route::get('manage/order', 'manage/order/index'); // 订单列表
Route::get('manage/order-detail/[:order_id]', 'manage/order/detail'); // 订单详情
Route::get('manage/inc-order', 'manage/incOrder/index'); // 提现申请列表
Route::get('manage/inc-order-detail/[:order_id]', 'manage/incOrder/detail'); // 提现申请详情
Route::get('manage/appeal', 'manage/orderAppeal/index');  // 申诉列表
Route::get('manage/user/:uid', 'manage/user/detail'); // 用户资料

Route::post('manage/jsApi/pass', 'manage/talent/pass'); // 大神审核通过
Route::post('manage/jsApi/reject', 'manage/talent/reject'); // 大神审核通过
Route::post('manage/jsApi/tal_refund', 'manage/talent/refund'); // 申诉：退款
Route::post('manage/jsApi/finish', 'manage/order/finish'); // 完成订单
Route::post('manage/jsApi/cancel', 'manage/order/cancel'); // 取消订单
Route::post('manage/jsApi/inc_order_pass', 'manage/incOrder/pass'); // 提现申请完成
Route::post('manage/jsApi/inc_order_reject', 'manage/incOrder/reject'); // 提现申请驳回
Route::post('manage/jsApi/appeal_over', 'manage/orderAppeal/over'); // 标签申诉处理完成
Route::post('manage/jsApi/appeal_pass', 'manage/orderAppeal/pass'); // 申诉：同意
Route::post('manage/jsApi/appeal_reject', 'manage/orderAppeal/reject'); // 申诉：驳回
Route::post('manage/jsApi/appeal_refund', 'manage/orderAppeal/refund'); // 申诉：退款

Route::rule('app/help', 'index/m/help');
Route::rule('app/about', 'index/m/about');
Route::rule('app/agree', 'index/m/talentAgree');
Route::rule('app/sms', 'index/sms/send');

