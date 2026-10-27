<?php
namespace app\admin\controller;
use think\facade\Db;
use app\admin\model\Order as OrderModel;
use app\admin\model\Technicianapply as TechnicianapplyModel;
use app\admin\model\Technician as TechnicianModel;
use app\admin\model\Withdrawapply as WithdrawapplyModel;
use app\admin\model\User as UserModel;
class Home extends Admin
{
	
	//头部消息通知
	function getNotice(){
    //今日新订单
    $res['num1'] = OrderModel::where('order_status','>','0')->whereDay('create_time')->count();
    //提现审核

    //技术入驻
    $res['num2'] =  TechnicianapplyModel::where('status','=','0')->count();
    //技术审核
    $res['num3'] = TechnicianModel::where('status','=','0')->count();
    $res['num4'] = WithdrawapplyModel::where('status','=','1')->count();
		$data = [
			[
				'num'=>$res['num1'],
				'title'=>'条订单待处理（今日）',
				'url'=>(string)url('admin/Order/index'),
			],
			[
				'num'=>$res['num2'],
				'title'=>'技师入驻申请',
				'url'=>(string)url('admin/Technicianapply/index'),
			],
			[
				'num'=> $res['num3'] ,
				'title'=>'技师待审核处理',
				'url'=>(string)url('admin/Technician/index'),
			],
      [
				'num'=> $res['num4'] ,
				'title'=>'提现申请待处理',
				'url'=>(string)url('admin/Withdrawapply/index'),
			],
		];
		
		return json(['status'=>200,'data'=>$data]);
	}
	
	//仪表盘数据
	function index(){
		$show_home_chats = !is_null(config('my.show_home_chats')) ? config('my.show_home_chats') : true;	//是否显示首页图表
		

    //获取当前年月

  $day_data = [];
   foreach(get_day(date("Y-m-d"),'2') as $item){
       $value =  OrderModel::where('order_status','>','0')->whereDay('create_time', $item)->sum('pay_money');  
       array_push($day_data,$value ); 
   }
		//折线图数据
		$echat_data['day_count'] = [
			'title'=>'当月业绩折线图',
			'day'=>get_day(date("Y-m-d"),'2'),	//每月天数
			'data'=>$day_data	//每天数据
		];
    
	
		//圆形图数据
		$echat_data['cycle_data'] = [
			'title'=>'网站访问类别',
			'catagory'=>["超级管理员", "管理员", "作者", "普通用户", "普通用户","访客"],
			'data'=> [
				['value'=>rand(0,1000) , 'name'=> "超级管理员" ],
				['value'=>rand(0,1000) , 'name'=> "管理员" ],
				['value'=>rand(0,1000) , 'name'=> "作者" ],
				['value'=>rand(0,1000) , 'name'=> "普通用户" ],
				['value'=>rand(0,1000) , 'name'=> "访客" ],
			]
		];
		
		
		if($show_home_chats){
			$data['echat_data'] = $echat_data;
		}
		$data['card_data'] = $this->getCardData();
		$data['menus'] = $this->getMenuLink();
		$data['status'] = 200;
		
		return json($data);
		
	}
	


	//首页统计数据
	private function getCardData(){
    $item['num1'] =  TechnicianModel::where('status','=',1)->whereDay('create_time')->count();
    $item['num2'] =  TechnicianModel::where('status','=',1)->count();

    $item['num3'] = WithdrawapplyModel::where('status','=','1')->whereDay('create_time')->sum('left_money');
    $item['num4'] = WithdrawapplyModel::sum('left_money');
    $item['num5'] = OrderModel::where('order_status','>','1')->whereDay('create_time')->sum('pay_money');
    $item['num6'] = OrderModel::where('order_status','>','1')->sum('pay_money');
    
    $item['num7'] = UserModel::whereDay('create_time')->count();

    $item['num8'] = UserModel::count();
		$card_data = [	//头部统计数据
			[
			  'card_title_icon'=>"el-icon-user",
			  'card_title'=> "技师数量",
			  'card_cycle'=> "日",
			  'card_cycle_back_color'=> "#409EFF",
			  'bottom_title'=> "技师总数量",
			  'vist_num'=>$item['num1'],
			  'vist_all_num'=> $item['num2'],
			  'vist_all_icon'=> "el-icon-trophy",
			],
			[
			  'card_title_icon'=> "el-icon-download",
			  'card_title'=> "提现",
			  'card_cycle'=> "日",
			  'card_cycle_back_color'=> "#67C23A",
			  'bottom_title'=> "总提现数",
			  'vist_num'=> $item['num3'],
			  'vist_all_num'=>  $item['num4'],
			  'vist_all_icon'=> "el-icon-download",
			],
			[
			  'card_title_icon'=> "el-icon-wallet",
			  'card_title'=> "收入",
			  'card_cycle'=> "日",
			  'card_cycle_back_color'=> "#F56C6C",
			  'bottom_title'=> "总收入",
			  'vist_num'=>  $item['num5'],
			  'vist_all_num'=>  $item['num6'],
			  'vist_all_icon'=> "el-icon-coin",
			],
			[
			  'card_title_icon'=> "el-icon-coordinate",
			  'card_title'=> "用户",
			  'card_cycle'=> "日",
			  'card_cycle_back_color'=> "#E6A23C",
			  'bottom_title'=> "总用户",
			  'vist_num'=> $item['num7'],
			  'vist_all_num'=> $item['num8'],
			  'vist_all_icon'=> "el-icon-data-line",
			],
		];
		
		return $card_data;
	}
	
	
	//获取首页快捷导航
	private function getMenuLink(){
		$show_home_menu = !is_null(config('my.show_home_menu')) ? config('my.show_home_menu') : true;	//是否显示首页快捷导航
		if($show_home_menu){
			$data = Db::name('menu')->field('title,menu_pic,controller_name')->where('home_show',1)->limit(8)->select()->toArray();
			foreach($data as $k=>$v){
				$data[$k]['url'] = (string)url('admin/'.str_replace('/','.',$v['controller_name']).'/index');
			}
			return $data;
		}
	}
	
	
}
