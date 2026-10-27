<?php
use think\facade\Route;

Route::group(function () {

    Route::post('index/service','index/service');
    Route::post('index/index','index/index');
    Route::post('index/serviceDetails','index/serviceDetails');
    Route::post('index/comment','index/comment');
//    Route::post('sys','index/sys');
//    Route::post('news','index/latestNews');
//    Route::post('newsCate','news/newsCate');
//    Route::post('newsList','news/newsList');
//    Route::post('newsDetail','news/newsDetail');
//    Route::post('wxPay','index/wxPay');
//    Route::post('wxNotify','index/wxNotify');
//
//    Route::post('lessonCate','lesson/lessonCate');
//    Route::post('lessonList','lesson/lessonList');
//    Route::post('lessonDetail','lesson/lessonDetail');
//    Route::post('favorites','lesson/favorites');
//    Route::post('chapterDetail','lesson/chapterDetail');
//
    Route::post('user/register','user/register');
//    Route::get('register','user/register');
    Route::post('user/login','user/login');
    Route::post('user/editPassword','user/editPassword');
    Route::post('order/upload','order/upload');
    Route::post('user/config','user/config');
    Route::post('user/smsCode','user/smsCode');
//    Route::post('logout','user/logout');
//    Route::post('editPassword','user/editPassword');
    Route::post('user/cs','user/cs');
    // Route::post('technician/details','technician/details');
    Route::post('order/confirmCancel','order/confirmCancel');
    Route::post('user/userInfo','user/userInfo');
    Route::group(function () {
        Route::post('index/coupon','index/coupon');
        Route::post('index/receive','index/receive');
        Route::post('index/upload','index/upload');

        Route::post('user/editUser','user/editUser');

        // Route::post('user/userInfo','user/userInfo');
        Route::post('user/technicianApply','user/technicianApply');
        Route::post('user/customerService','user/customerService');

        Route::post('share/index','share/index');
        Route::post('share/team','share/team');

        Route::post('coupon/index','coupon/index');
        Route::post('coupon/exchange','coupon/exchange');

        Route::post('news/index','news/index');

        Route::post('member/index','member/index');
        Route::post('member/memberList','member/memberList');
        Route::post('member/createOrder','member/createOrder');

        Route::post('recharge/index','recharge/index');
        Route::post('recharge/rechargeList','recharge/rechargeList');
        Route::post('recharge/createOrder','recharge/createOrder');
        Route::post('recharge/orderList','recharge/orderList');
        Route::post('recharge/consumeList','recharge/consumeList');
        Route::post('recharge/refundList','recharge/refundList');
        Route::post('recharge/brokerageList','recharge/brokerageList');

        Route::post('opinion/index','opinion/index');
        Route::post('opinion/opinionList','opinion/opinionList');
        Route::post('opinion/createOpinion','opinion/createOpinion');

        Route::post('pay/payWay','pay/payWay');
        Route::post('pay/pay','pay/pay');
        Route::post('pay/is_pay','pay/is_pay');

//        Route::post('pay/rechargeOrder','pay/rechargeOrder');
//        Route::post('pay/order','pay/order');

        Route::post('address/index','address/index');
        Route::post('address/details','address/details');
        Route::post('address/editAddress','address/editAddress');
        Route::post('address/del','address/del');

        Route::post('technician/index','technician/index');
        Route::post('technician/details','technician/details');
        Route::post('order/getDistance','order/getDistance');
        Route::post('order/settlement','order/settlement');
        Route::post('order/serviceDate','order/serviceDate');
        Route::post('order/dateList','order/dateList');
        Route::post('order/coupon','order/coupon');
        Route::post('order/createOrder','order/createOrder');
        Route::post('order/orderList','order/orderList');
        Route::post('order/orderDetails','order/orderDetails');
        Route::post('order/cancelOrder','order/cancelOrder');
        // Route::post('order/confirmCancel','order/confirmCancel');
        Route::post('order/completeOrder','order/completeOrder');
        Route::post('order/complaint','order/complaint');
        Route::post('order/userInstructions','order/userInstructions');
        Route::post('order/commentType','order/commentType');
        Route::post('order/comment','order/comment');

        Route::post('collect/index','collect/index');
        Route::post('collect/collect','collect/collect');

        Route::post('wallet/aliBind','wallet/aliBind');
        Route::post('wallet/wallet','wallet/wallet');
        Route::post('wallet/withdraw','wallet/withdraw');
        Route::post('wallet/withdrawApply','wallet/withdrawApply');
        Route::post('wallet/withdrawList','wallet/withdrawList');


    })->middleware(\app\api\middleware\Token::class);
})->allowCrossDomain();