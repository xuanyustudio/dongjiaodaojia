<?php


namespace app\technician\middleware;


use think\facade\Request;
use think\facade\Db;
class Token
{
    public function handle($request, \Closure $next)
    {
        $token = trim(Request::header('token', ''),'"');

        //Db::name('token')->insert(['token'=>Request::header('token', '')]);
        if(!$token || $token == 'null'){return msg('0','请先登录');}
        $user = cache('technician_token'.$token);
        if(!$user){ return msg('0','登录已过期，请重新登录');}
        return $next($request);
    }
}