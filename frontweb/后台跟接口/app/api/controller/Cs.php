<?php


namespace app\api\controller;


use app\BaseController;


use app\common\controller\CronParser;
use Cron\CronExpression;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use think\facade\Console;
use think\facade\Db;
use wxpay\WxPay;

//require root_path().'/extend/mtdowling_cron_expression/src/Cron/CronExpression.php';
//require root_path().'/extend/cronparser-master/src/CronParser.php';
//use Godv\CronParser;

class Cs extends BaseController
{
  public function cs(){
      $wxpay=new WxPay();
      $refund=$wxpay->Refund('O2022032910057565',14.62,1);
      var_dump($refund);
      var_dump(root_path() . 'public/uploads/wx/apiclient_cert.pem');
      exit();


      $host = "https://jumfixed.market.alicloudapi.com";
      $path = "/voice-notify/send";
      $method = "POST";
      $appcode = "e290fd44d3c2410e9ca24bb6e100f0df";
      $headers = array();
      array_push($headers, "Authorization:APPCODE " . $appcode);
      //根据API的要求，定义相对应的Content-Type
      array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
      $querys = "";
      $bodys = "mobile=18175153344&param=&templateId=955762849774743552";
      $url = $host . $path;

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_FAILONERROR, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      //设定返回信息中是否包含响应信息头，启用时会将响应信息头作为数据流输出，true 表示输出信息头, false表示不输出信息头
      //如果想将响应结果json字符串转为json数组，建议将 CURLOPT_HEADER 设置成 false
      curl_setopt($curl, CURLOPT_HEADER, true);
      if (1 == strpos("$".$host, "https://"))
      {
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      }
      curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
      curl_exec($curl);




      $next_time_list=CronParser::formatToDate('40 * * * *',2,'1647682681');
      var_dump($next_time_list);

      var_dump(date('t','202'));
//      Console::call('crontab');
//      exit();

      $time = time();

      $crons = Db::name('dev_crontab')
          ->where(['status' => 1])
          ->select();
      if (empty($crons)) {
          return;
      }


      foreach ($crons as $cron) {
          //规则错误，不执行

//          if(CronParser::check($cron['expression'])==false){
//              continue;
//          }
////          if (CronExpression::isValidExpression($cron['expression']) === false) {
////              continue;
////          }
//
//
//          //未到时间，不执行
//          $next_time_list=CronParser::formatToDate($cron['expression'],1,$cron['last_time']);
//          if(empty($next_time_list)){
//              continue;
//          }
//          $next_time=$next_time_list[0];
////          $cron_expression = CronExpression::factory($cron['expression']);
////
////          $next_time = $cron_expression->getNextRunDate(date('Y-m-d H:i:s', $cron['last_time']))->getTimestamp();
//
//
//          if ($next_time >= $time) {
//              continue;
//          }

          //开始执行
          try {
//                Debug::remark('begin');
              $parameter = explode(' ', $cron['parameter']);
              if (is_array($parameter) && !empty($cron['parameter'])) {
                  Console::call($cron['command'], $parameter);
              } else {
                  Console::call($cron['command']);
              }
              Db::name('dev_crontab')
                  ->where(['id' => $cron['id']])
                  ->update(['error' => '']);
          } catch (\Exception $e) {
              Db::name('dev_crontab')
                  ->where(['id' => $cron['id']])
                  ->update(['error' => $e->getMessage(), 'status' => 3]);
          } finally {
//                Debug::remark('end');
//                $range_time = Debug::getRangeTime('begin', 'end');
//                $max_time = max($cron['max_time'], $range_time);
              Db::name('dev_crontab')
                  ->where(['id' => $cron['id']])
                  ->update(['last_time' => $time]);
          }
      }
  }
}