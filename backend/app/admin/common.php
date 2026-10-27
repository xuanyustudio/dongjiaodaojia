<?php
// +----------------------------------------------------------------------
// | 应用公共文件
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------


use think\helper\Str;

error_reporting(0);



//根据键名获取键值
function getItemVal($val,$item_config){
	if($val){
		$str = '';
		foreach(explode(',',$val) as $v){
			foreach(json_decode($item_config,true) as $m){
				if($v == $m['val']){
					$str .= $m['key'].',';
				}
			}
		}
		return rtrim($str,',');
	}
}

//根据键值获取键名
function getValByKey($val,$item_config){
	if($val){
		$str = '';
		foreach(explode(',',$val) as $v){
			foreach(json_decode($item_config,true) as $m){
				if($v == $m['key']){
					$str .= $m['val'].',';
				}
			}
		}
		return rtrim($str,',');
	}
}

//无限极分类转为带有 children的树形select结构
function _generateSelectTree ($data, $pid = 0) {
	$tree = [];
	if ($data && is_array($data)) {
		foreach($data as $v) {
			if($v['pid'] == $pid) {
				$tree[] = [
					'key' => $v['key'],
					'val' => $v['val'],
					'children' => _generateSelectTree($data, $v['val']),
				];
			}
		}
	}
	return $tree;
}

//无限极分类转为带有 children的树形list表格结构
function _generateListTree ($data, $pid = 0,$config=[]) {
	$tree = [];
	if ($data && is_array($data)) {
		foreach($data as $v) {
			if($v[$config[1]] == $pid) {
				$tree[] = array_merge($v,['children' => _generateListTree($data, $v[$config[0]],$config)]);
			}
		}
	}
	return $tree;
}

function deldir($dir) {
//先删除目录下的文件：
   $dh=opendir($dir);
   while ($file=readdir($dh)) {
	  if($file!="." && $file!="..") {
		 $fullpath=$dir."/".$file;
		 if(!is_dir($fullpath)) {
			unlink($fullpath);
		 } else {
			deldir($fullpath);
		 }
	  }
   }
 
   closedir($dh);
   //删除当前文件夹：
   if(rmdir($dir)) {
	  return true;
   } else {
	  return false;
   }
}

/*写入
* @param  string  $type 1 为生成控制器
*/

function filePutContents($content,$filepath,$type){
	if(in_array($type,[1,3])){
		$str = file_get_contents($filepath);
		$parten = '/\s\/\*+start\*+\/(.*)\/\*+end\*+\//iUs';
		preg_match_all($parten,$str,$all);
		if($all[0]){
			foreach($all[0] as $key=>$val){
				$ext_content .= $val."\n\n";
			}
		}
		
		$content .= $ext_content."\n\n";
		if($type == 1){
			$content .="}\n\n";
		}
	}
	
	ob_start();
	echo $content;
	$_cache=ob_get_contents();
	ob_end_clean();
	
	if($_cache){
		$File = new \think\template\driver\File();
		$File->write($filepath, $_cache);	
	}
}

/**
     * 获取当月天数
     * @param  $date 
     * @param  $rtype  1天数 2具体日期数组
     * @return 
     */
    function get_day( $date ,$rtype = '1')   
    {
        $tem = explode('-' , $date);       //切割日期  得到年份和月份
        $year = $tem['0'];
        $month = $tem['1'];
        if( in_array($month , array( '1' , '3' , '5' , '7' , '8' , '01' , '03' , '05' , '07' , '08' , '10' , '12')))
        {
            // $text = $year.'年的'.$month.'月有31天';
            $text = '31';
        }
        elseif( $month == 2 )
        {
            if ( $year%400 == 0  || ($year%4 == 0 && $year%100 !== 0) )        //判断是否是闰年
            {
                // $text = $year.'年的'.$month.'月有29天';
                $text = '29';
            }
            else{
                // $text = $year.'年的'.$month.'月有28天';
                $text = '28';
            }
        }
        else{

            // $text = $year.'年的'.$month.'月有30天';
            $text = '30';
        }

        if ($rtype == '2') {
            for ($i = 1; $i <= $text ; $i ++ ) {
                $r[] = $year."-".$month."-".$i;
            }
        } else {
            $r = $text;
        }
        return $r;
    }