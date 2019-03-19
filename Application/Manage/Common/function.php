<?php
function assoc_unique(&$arr, $key)
{
	$rAr=array();
	for($i=0;$i<count($arr);$i++)
	{
		if(!isset($rAr[$arr[$i][$key]]))
		{
			$rAr[$arr[$i][$key]]=$arr[$i];
		}
	}
	$arr=array_values($rAr);
	return $arr;
}

//截取字符串
function subtext($text, $length)
{
    if(mb_strlen($text, 'utf8') > $length)
        return mb_substr($text, 0, $length, 'utf8').'...';
    return $text;
}
//缴纳明细列表
function returnDateils($uid,$status,$type){
	switch ((int)$type){
		case 1:
			 if($status == 0 || $status == 1){
			 	$rs = '商家';
			 }else{
			 	$rs = '用户';
			 }
		break;
		case 2:
			$map['id'] = array('eq',$uid);
			if($status == 0 || $status == 1){
				$rs = M('business')->where($map)->getField('telPhone');
			}else{
				$rs = M('user')->where($map)->getField('telPhone');
			}
		break;
		case 3:
			if($status == 0){
				$rs = '商家缴纳服务费';
			}else if($status == 1){
				$rs = '商家缴纳城市合伙人';
			}else if($status == 2){
				$rs = '用户缴纳城市合伙人';
			}
		break;
		case 4:
			if($status == 4){
				$rs = '支付宝';
			}else{
				$rs = '微信';
			}
		break;
	}
	return $rs;
}

//修改表的status 状态$status,$id,$text
function statusInfo($table,$id,$status,$text1,$text2)
{
    if($status == 0){
        $html = '<a class="btn btn-danger" data-table="'.$table.'" data-id="'.$id.'" data-status="'.$status.'" data-text1="'.$text1.'" data-text2="'.$text2.'" href="javascript:void(0);">'.$text1.'</a>';
    }else{
        $html = '<a class="btn btn-success" data-table="'.$table.'" data-id="'.$id.'" data-status="'.$status.'" data-text1="'.$text1.'" data-text2="'.$text2.'" href="javascript:void(0);">'.$text2.'</a>';
    }
    return $html;
}

//遍历文件夹下的所有文件
function loopFun($dir){
	$list = scandir( $dir );
	foreach( $list as $key=>$file ) {
		$location_dir = $dir . '/' . $file;
		//判断是否是文件夹 是就调用自身函数再去进行处理
		if( is_dir( $location_dir ) && '.' != $file && '..' != $file ){
			loopFun($location_dir);
		}else{
			if('.' != $file && '..' != $file){
				$where['fileName'] = $file;
				$where['filePath'] = $location_dir;
				
				$temp = M('temp')->where($where)->getField('id');
				if(empty($temp)){
					$data['fileName'] = $file;
					$data['filePath'] = $location_dir;
					M('temp')->add($data);
				}
			}
		}
	}
	return true;
}
/**
 * 导出excel函数
 * @param $fileName 导出的文件名
 * @param int $headArr 表头数组
 * @param int $data 要循环的数据
 * @return \Think\Page
 */
function getExcel($fileName,$headArr,$data){
	//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
	import("Org.Util.PHPExcel");
	import("Org.Util.PHPExcel.Writer.Excel5");
	import("Org.Util.PHPExcel.IOFactory.php");

	$fileName .= ".xls";

	//创建PHPExcel对象
	$objPHPExcel = new \PHPExcel();
	$objProps = $objPHPExcel->getProperties();

	//设置表头
	$key = ord("A");
	//print_r($headArr);exit;
	foreach($headArr as $v){
		$colum = chr($key);
		$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
		$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
		$key += 1;
	}

	$column = 2;
	$objActSheet = $objPHPExcel->getActiveSheet();

	//print_r($data);exit;
	foreach($data as $key => $rows){ //行写入
		$span = ord("A");
		foreach($rows as $keyName=>$value){// 列写入
			$j = chr($span);
			$objActSheet->setCellValue($j.$column, $value);
			$span++;
		}
		$column++;
	}

	$fileName = iconv("utf-8", "gb2312", $fileName);
	//重命名表
	//$objPHPExcel->getActiveSheet()->setTitle('test');
	//设置活动单指数到第一个表,所以Excel打开这是第一个表
	$objPHPExcel->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=\"$fileName\"");
	header('Cache-Control: max-age=0');

	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output'); //文件通过浏览器下载
	exit;
}
//计算周边距离
function vicinity($lng, $lat, $distance = 0.5) {
	define('EARTH_RADIUS', 6370.6935);
	//$distance = 0.5;      // 单位 10KM
	$radius = EARTH_RADIUS;

	$dlng = rad2deg(2*asin(sin($distance/(2*$radius))/cos($lat)));
	$dlat = rad2deg($distance*10/$radius);

	$lng_left = round($lng - $dlng, 6);
	$lng_right = round($lng + $dlng, 6);
	$lat_top = round($lat + $dlat, 6);
	$lat_bottom = round($lat - $dlat, 6);

	return array('lng'=> array('left'=> $lng_left, 'right'=> $lng_right), 'lat'=> array('top'=> $lat_top, 'bottom'=> $lat_bottom));
}
/* 生成唯一用户推送ID */
function buildUPush()
{
	$pushID = date('ym').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
	$map['pushID'] = array('eq',$pushID);
	$orders = M('user')->where($map)->find();
	if (empty($orders))
	{
		return $pushID;
	}
	return buildUPush();
}
/* 生成唯一订单号 */
function buildOrder()
{
	$orderSn = date('ym').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
	$map['orderSn'] = array('eq',$orderSn);
	$orders = M('order')->where($map)->find();
	if (empty($orders))
	{
		return $orderSn;
	}
	return buildOrder();
}
//获取一月 或 三月 或 一年的时间戳
function dayMonth($date,$num)
{
	//时间戳转为时间格式
	$time  = date('Y-m-d H:i:s',$date);
	$year  = substr($time,0,4);
	$month = substr($time,5,2)+$num;
	$day   = substr($time,8,2);
	$hour  = substr($time,11,2);
	$min   = substr($time,14,2);
	$sec   = substr($time,17,2);
	return mktime($hour,$min,$sec,$month,$day,0+$year);
}
//获取提现分类并归类
//$status 1 代表 区分商家 或 用户
//$status 2 查看 商家 或 用户 联系方式
//$status 3 区分 提现类型
function cashType($typeID,$status,$uid){
	if(empty($typeID))return false;
	if(empty($status))return false;
	switch ((int)$status){
		case 1:
			if($typeID == 1){
				$typeName = '用户';
			}else{
				$typeName = '商家';
			}
		break;
		case 2:
			$map['id'] = array('eq',$uid);
			if($typeID == 1){
				$typeName = M('user')->where($map)->getField('telPhone');
			}else{
				$typeName = M('business')->where($map)->getField('telPhone');
			}
		break;
		case 3:
			if($typeID == 1){
				$typeName = '分享提现';
			}else if($typeID == 2){
				$typeName = '分享提现';
			}else if($typeID == 3){
				$typeName = '分红提现';
			}else if($typeID == 4){
				$typeName = '押金提现';
			}else{
				$typeName = '未归类';
			}
		break;
	}
	return $typeName;
}
//协议文档返回参数
function returnStat($status){
	$arr = array('','提现规则','用户协议','帮助','隐私协议','商家入驻必读','关于我们','城市合伙人协议','服务条款','城市合伙人-营销','城市合伙人-规则');
	return $arr[$status];
}
//意见反馈 获取商家或用户联系方式
function getTel($uid,$status){
	if(empty($status))return false;
	if(empty($uid))return false;
	if($status == 1){
		$table = 'user';
	}elseif($status == 2){
		$table = 'business';
	}
	$map['id'] = array('eq',$uid);
	return M($table)->where($map)->getField('telPhone');
}
//
function read_pdf($file) {
	if(strtolower(substr(strrchr($file,'.'),1)) != 'pdf') {
		//echo '文件格式不对.';
		return false;
	}
	if(!file_exists($file)) {
		//echo '文件不存在';
		return false;
	}
	header('Content-type: application/pdf');
	header('filename='.$file);
	readfile($file);
}
//单选框返回值
function isShow($isShow,$id,$class){
	if((int)$isShow == 1){
		$html = '<input type="checkbox" name="isShow" data-id="'.$id.'" class="js-switch '.$class.'" checked/>';
	}else{
		$html = '<input type="checkbox" name="isShow" data-id="'.$id.'" class="js-switch '.$class.'" />';
	}
	return $html;
}
//截取中文字
function substrMan($string, $start){
	$strlen = mb_strlen($string,'utf8');
	if($strlen > $start){
		$substr = mb_substr($string, 0, $start,'utf-8').'...';
	}else{
		$substr = $string;
	}
	return $substr;
}
//返回商家发布服务的状态值
function releserReturn($status){
	$arr = array('待审核','已审核未上架','已上架','审核驳回','','','','','','该服务已删除');
	return $arr[$status];
}
//隐藏手机号码中间几位数字
function hidtel($phone){
	$IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone);
	if($IsWhat == 1){
		return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);
	}else{
		return preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
	}
}
function jpushBus($registration_id="",$title="",$content = "",$extras){
	$app_key = '26719fe4dacefac00e4352e4';
	$master_secret = '090f08333f02b187ef47e433';
	$pushObj = new \Org\Jpush($app_key,$master_secret);
	//组装需要的参数
	if(empty($registration_id)){
		$receive = 'all';     //全部
	}else{
		$receive = array(
				'alias' => $registration_id
		);
	}
	//调用推送,并处理
	$result = $pushObj->push($receive,$title,$content,$extras);
	if($result){
		$res_arr = json_decode($result, true);
		if(isset($res_arr['error'])){
			return false;
		}else{
			return true;
		}
	}else{
		return false;
	}
}
//商家 添加消息表
function addBusinMsg($businessID,$title,$content,$msgType){
	$data['businessID'] = $businessID;
	$data['title']      = $title;
	$data['content']    = $content;
	$data['addTime']    = time();
	if(!empty($msgType))$data['msgType'] = $msgType;
	return M('businessmsg')->add($data);
}
/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
    $Page = new Think\Page($count, $pagesize);
    $Page->setConfig('header', '<li><a>共%TOTAL_ROW%条记录</a></li> <li><a>第%NOW_PAGE%页/共%TOTAL_PAGE%页</a></li>');
    $Page->setConfig('prev', '上一页');
    $Page->setConfig('next', '下一页');
    $Page->setConfig('last', '末页');
    $Page->setConfig('first', '首页');
    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $Page->lastSuffix = false;//最后一页不显示为总页数
    return $Page;
}
/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i'){
	if(empty($time))return '';
	return date($format, $time);
}
/*拆分图片*/
function splitPic($pic){
	if(empty($pic))return false;
	$pic_arr = explode(',',$pic);
	if(is_array($pic_arr)){
		foreach($pic_arr as $key=>$vo){
			$pic_arr[$key] = M("picture")->where("id = ".$vo)->getField("path");
		}
		$pic_arr = implode(',',$pic_arr);
	}else{
		$pic_arr = M("picture")->where("id = ".$pic_arr)->getField("path");
	}
	return $pic_arr;
}
//状态展示
function status($table,$id,$status,$field,$textArr,$colorArr){
	return '<a data-table="'.$table.'" data-id="'.$id.'"  data-status="'.$status.'" data-text="'.implode(',',$textArr).'" data-color="'.implode(',',$colorArr).'" data-field="'.$field.'" class="btn btn-success status" href="javascript:void(0);"  style="color: #fff;background-color: '.$colorArr[$status].';border-color: '.$colorArr[$status].';">'.$textArr[$status].'</a>';
}