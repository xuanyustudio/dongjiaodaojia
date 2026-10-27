<?php
namespace app\admin\controller\Sys\middleware;
use think\exception\ValidateException;
use app\admin\controller\Sys\model\Menu;
use app\admin\controller\Sys\model\Application;
use app\admin\controller\Admin;
use app\admin\controller\Sys\model\Field;
use app\admin\controller\Sys\model\Action;
use think\facade\Db;

class deleteTable extends Admin
{
	
    public function handle($request, \Closure $next)
    {	
		$data = $request->param();
		$menuInfo = Menu::find($data['menu_id']);
		$applicationInfo = Application::find($menuInfo['app_id']);

		$connect = $menuInfo['connect'] ? $menuInfo['connect'] : config('database.default');
		$prefix = config('database.connections.'.$connect.'.prefix');
		
		if($menuInfo['table_name'] && $menuInfo['create_table']){
			Db::connect($connect)->execute('DROP TABLE if exists '.$prefix.$menuInfo['table_name']);
		}
		
		try{
			//开始删除相关文件
			if(!empty($menuInfo['controller_name'])){
				$rootPath = app()->getRootPath();
				@unlink($rootPath.'/app/'.$applicationInfo['app_dir'].'/controller/'.$menuInfo['controller_name'].'.php');
				if($this->getSubFiles($rootPath.'/app/'.$applicationInfo['app_dir'].'/controller/'.explode('/',$menuInfo['controller_name'])[0])){
					deldir($rootPath.'/app/'.$applicationInfo['app_dir'].'/controller/'.explode('/',$menuInfo['controller_name'])[0]);
				}
				
				@unlink($rootPath.'/app/'.$applicationInfo['app_dir'].'/model/'.$menuInfo['controller_name'].'.php');
				if($this->getSubFiles($rootPath.'/app/'.$applicationInfo['app_dir'].'/model/'.explode('/',$menuInfo['controller_name'])[0])){
					deldir($rootPath.'/app/'.$applicationInfo['app_dir'].'/model/'.explode('/',$menuInfo['controller_name'])[0]);
				}
				
				@unlink($rootPath.'/app/'.$applicationInfo['app_dir'].'/validate/'.$menuInfo['controller_name'].'.php');
				if($this->getSubFiles($rootPath.'/app/'.$applicationInfo['app_dir'].'/validate/'.explode('/',$menuInfo['controller_name'])[0])){
					deldir($rootPath.'/app/'.$applicationInfo['app_dir'].'/validate/'.explode('/',$menuInfo['controller_name'])[0]);
				}
				
				@unlink($rootPath.'/app/'.$applicationInfo['app_dir'].'/route/'.$this->getControllerName(strtolower($menuInfo['controller_name'])).'.php');
				
				//开始删除vue文件
				@unlink($rootPath.'/ui/src/api/'.$applicationInfo['app_dir'].'/'.strtolower($menuInfo['controller_name']).'.js');
				if($this->getSubFiles($rootPath.'/ui/src/api/'.$applicationInfo['app_dir'].'/'.explode('/',$menuInfo['controller_name'])[0])){
					deldir($rootPath.'/ui/src/api/'.$applicationInfo['app_dir'].'/'.explode('/',$menuInfo['controller_name'])[0]);
				}
				
				deldir($rootPath.'/ui/src/views/'.$applicationInfo['app_dir'].'/'.strtolower($menuInfo['controller_name']));
			}
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		return $next($request);	
    }
	
	//判断当前目录有没有其他文件
	private function getSubFiles($filepath){
		$res = scandir($filepath);
		if(count($res) == 2){
			return true;
		}
	}
	
	//多级控制器 获取控制其名称
	private function getControllerName($controller_name){
		if($controller_name && strpos($controller_name,'/') > 0){
			$controller_name = explode('/',$controller_name)[1];
		}
		return $controller_name;
	}
}