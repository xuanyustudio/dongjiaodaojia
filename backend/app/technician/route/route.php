<?php
use think\facade\Route;

Route::group(function () {

    Route::post('news/index','news/index');
    Route::post('login/getOpenid','login/getOpenid');
    Route::post('login/login','login/login');
    Route::rule('login/bindMobile','login/bindMobile');
    Route::post('index/cs','index/cs');
    Route::post('login/cs','login/cs');
    Route::post('index/tcp','index/tcp');
    Route::post('index/uploadImage','index/uploadImage');
    Route::post('order/comment','order/comment');
    Route::group(function () {

        Route::post('login/follow','login/follow');
        Route::post('index/uploadAudio','index/uploadAudio');
        Route::post('index/index','index/index');
        Route::post('index/status','index/status');
        Route::post('index/signIn','index/signIn');
        Route::post('index/callPolice','index/callPolice');
        Route::post('index/busyDate','index/busyDate');
        Route::post('index/busyHour','index/busyHour');
        Route::post('index/setBusyHour','index/setBusyHour');
        Route::post('index/incomeStatistics','index/incomeStatistics');
        Route::post('index/incomeList','index/incomeList');
        Route::post('index/orderStatistics','index/orderStatistics');
        Route::post('index/aliBind','index/aliBind');
        Route::post('index/wallet','index/wallet');
        Route::post('index/withdraw','index/withdraw');
        Route::post('index/withdrawApply','index/withdrawApply');
        Route::post('index/withdrawList','index/withdrawList');
        Route::post('index/getTmplIds','index/getTmplIds');


        Route::post('order/orderList','order/orderList');

        Route::post('order/orderReceiving','order/orderReceiving');
        Route::post('order/orderService','order/orderService');

        Route::post('order/orderDetails','order/orderDetails');
        Route::post('order/cancelReason','order/cancelReason');
        Route::post('order/cancelOrder','order/cancelOrder');
        Route::post('order/completeOrder','order/completeOrder');
        // Route::post('order/comment','order/comment');
        Route::post('order/complaint','order/complaint');

        Route::post('my.Index/get_technician_tcp','my.Index/get_technician_tcp');
        Route::post('my/Index/index','my/Index/index');
        Route::post('my.Index/sign_tcp','my.Index/sign_tcp');
        Route::post('my.Index/get_service','my.Index/get_service');
        Route::post('my.Index/set_service','my.Index/set_service');
        Route::post('my.Index/set_service_data','my.Index/set_service_data');

        Route::post('my.Index/setAlbum','my.Index/setAlbum');
        Route::post('my.Index/setData','my.Index/setData');
        Route::post('my.Index/setHealthy','my.Index/setHealthy');
        Route::post('my.Index/setIdentity','my.Index/setIdentity');
        Route::post('my.Index/setQualifications','my.Index/setQualifications');
        Route::post('my.Index/creditLog','my.Index/creditLog');
        Route::rule('my.Index/bindMobile','my.Index/bindMobile');

        Route::post('my.Opinion/index','my.Opinion/index');
        Route::post('my.Opinion/list','my.Opinion/list');
        Route::post('my.Opinion/add','my.Opinion/add');
        Route::post('my.Comment/get_type','my.Comment/get_type');
        Route::post('my.Comment/list','my.Comment/list');

    })->middleware(\app\technician\middleware\Token::class);
})->allowCrossDomain();