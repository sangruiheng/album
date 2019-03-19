<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends CommonController {
	//重定向
	public function index(){
		 header("location:".C('HOST')."/manage.php");
	}
	
	//资格效验查询
	public function validation_submit(){
		$project_id = I("project_id");
		if(empty($project_id))$this->ajaxReturn(array('code'=>400,'msg'=>'请选择申购项目'));
		$applier_people_name = I("applier_people_name");
		if(empty($applier_people_name))$this->ajaxReturn(array('code'=>400,'msg'=>'请输入真实姓名'));
		$applier_id_no = I("applier_id_no");
		if(empty($applier_id_no))$this->ajaxReturn(array('code'=>400,'msg'=>'请输入身份证号'));
		$seq_no = I("seq_no");
		if(empty($seq_no))$this->ajaxReturn(array('code'=>400,'msg'=>'请输入申请编码'));
		$arr = array(
				"project_id"=>$project_id,
				"applier_people_name"=>$applier_people_name,
				"applier_id_no"=>$applier_id_no,
				"seq_no"=>$seq_no,
				"submitbutton.x"=>109,
				"submitbutton.y"=>16,
				"postback"=>1
		);
		//curl验证成功
		$ch = curl_init("http://210.75.213.154/shh/portal/familyaudit/result_su.aspx");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
		//"Accept-Encoding: gzip, deflate",
		"Accept-Language: zh-CN,zh;q=0.9",
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS,$arr);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		//设置连接结束后保存cookie信息的文件
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			print curl_error($ch);
		}
		curl_close($ch);
		
		//首先是错误信息
		//[ 错误反馈 ]
		//申请编号，申请人姓名或申请人身份证件号码错误。
		$regex = "/<div.*?class=\"caption\".*?>(.*?)<\/div>/is";
		preg_match_all($regex,$result,$caption,PREG_PATTERN_ORDER);
		
		//查询成功 获取用户信息
		//首先查询状态失败知道 成功未知
		preg_match_all('#<table.+</table>#isU', $result, $m);
		foreach(array_map('strip_tags', $m[0]) as $k=>$r) {
			$a = preg_split('/\s+/', $r, -1, PREG_SPLIT_NO_EMPTY);
			$res[] = array_chunk(array_slice($a, 0), 3);
		}
		$user_arr[0] = $res[0];
		$validation_arr[0] = $res[1];
		//循环第一个table
		foreach ($user_arr as $k=>$v){
			$user_name_str = $v[0][0];
			$user_name_arr = explode("：", $user_name_str);
			$result_user_info[$k]['user_name'] = $user_name_arr[1];
			$result_user_info[$k]['certificates_name'] = $v[0][2];
			$result_user_info[$k]['applier_id_no'] = $v[1][1];
			$result_user_info[$k]['house_address'] = $v[2][0];
			$result_user_info[$k]['family_num'] = $v[2][2];
			$result_user_info[$k]['shack_sn'] = $v[3][1];
			$result_user_info[$k]['validity_time'] = $v[4][0];
		}
		//循环第二个table
		foreach ($validation_arr as $k=>$v){
			$result_info[$k]['result_info_title'] = $v[0][0];
			//正则匹配
			preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $v[0][1], $matches);
			//把匹配到的数组连接为字符串
			$str = implode('', $matches[0]);
			//中文字符去重
			$str = str_split($str,3);
			$str = array_unique($str);
			$str = implode($str);
			$result_info[$k]['result_info'] = $str;
		}
		//查询成功
		if($caption[1][0] == '[ 申请核验人信息 ]'){
			$returnData['result_user_info'] = $result_user_info[0];
			$returnData['result_info'] = $result_info[0];
			$this->ajaxReturn(array('code'=>200,'msg'=>'查询成功','data'=>$returnData));
		}else{
			$this->ajaxReturn(array('code'=>400,'msg'=>'【错误反馈 】申请编号，申请人姓名或申请人身份证件号码错误。'));
		}
	}
	
	//从数据库中读取申购列表
	public function home_db_list(){
		$rs = M('home')->where('status = 0')->field('id,project_name,enterprise_name,house_num,start_time,end_time,project_address,link')->order('id desc')->select();
		if($rs){
			$this->ajaxReturn(array('code'=>200,'msg'=>'获取成功','data'=>$rs));
		}else{
			$this->ajaxReturn(array('code'=>400,'msg'=>'暂无申购列表'));
		}
	}
	
	//获取申购项目列表
	public function project_list(){
		header("Content-type: text/html; charset=utf-8");
		$rs = file_get_contents('http://210.75.213.188/shh/portal/familyaudit/query_seq_no_share.aspx');
		//获取HTMLselect option里的数据
		$regex = "/<option.*?value=\".*?.*?\".*?>(.*?)<\/option>/is";
		preg_match_all($regex,$rs,$select,PREG_PATTERN_ORDER);
		
		//获取HTMLoption里的value值
		preg_match_all('|value="(.*)"|isU',$rs,$value); //匹配到数组$arr中；
		
		//将两个数组合并成一个二维数组 并且两个数组长度相等
		if(count($select[1]) == count($value[1])){
			for($i=1;$i<count($select[1]);$i++){
				$res[] = array("project_id"=>$value[1][$i],"project_name"=>$select[1][$i]);
			}
		}
		if(!empty($res)){
			$this->ajaxReturn(array("code"=>200,'msg'=>'获取成功','data'=>$res));
		}else{
			$this->ajaxReturn(array("code"=>400,'msg'=>'获取失败'));
		}
	}
	
	//申请编码查询
	//project_id
	//applier_people_name
	//applier_id_no
	public function apply_sn(){
		$project_id = I("project_id");
		if(empty($project_id))$this->ajaxReturn(array('code'=>400,'msg'=>'请选择申购项目'));
		$applier_people_name = I("applier_people_name");
		if(empty($applier_people_name))$this->ajaxReturn(array('code'=>400,'msg'=>'请输入真实姓名'));
		$applier_id_no = I("applier_id_no");
		if(empty($applier_id_no))$this->ajaxReturn(array('code'=>400,'msg'=>'请输入身份证号'));
		$arr = array(
				"project_id"=>$project_id,
				"applier_people_name"=>$applier_people_name,
				"applier_id_no"=>$applier_id_no,
				"submitbutton.x"=>137,
				"submitbutton.y"=>20,
				"postback"=>1
		);
		//curl验证成功
		$ch = curl_init("210.75.213.188/shh/portal/familyaudit/result_seq_no_su.aspx?type_id=2");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
		//"Accept-Encoding: gzip, deflate",
		"Accept-Language: zh-CN,zh;q=0.9",
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS,$arr);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		//设置连接结束后保存cookie信息的文件
		//curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			//print curl_error($ch);
		}
		curl_close($ch);
		
		$regex = "/<div.*?class=\"yhclass\".*?>(.*?)<\/div>/is";
		preg_match_all($regex,$result,$yhclass,PREG_PATTERN_ORDER);
		
		$regex = "/<div.*?class=\"caption\".*?>(.*?)<\/div>/is";
		preg_match_all($regex,$result,$caption,PREG_PATTERN_ORDER);
		
		$caption_left = trim($caption[1][0],'[');
		$caption_rigth = trim($caption_left,']');
		$caption_arr = explode("：", $caption_rigth);
		$caption_string = trim($caption_arr[1]," ");
		if(!empty($yhclass[1][0]) && !empty($caption_string)){
			$returnData['house_type'] = $yhclass[1][0];
			$returnData['apply_sn'] = $caption_string;
			$this->ajaxReturn(array('code'=>200,'msg'=>'查询成功','data'=>$returnData));
		}else{
			$this->ajaxReturn(array('code'=>400,'msg'=>'【错误反馈】未查询到申购该项目信息，请检查申请人姓名或申请人身份证件号码是否录入正确。'));
		}
	}
	
	
	
	//获取住房申购列表
	public function homeList(){
		header("Content-type: text/html; charset=utf-8");
		$cookie_file=tempnam('./Uploads/temp','cookie');
		$ch2 = curl_init();
		$url2 = "http://zzfws.bjjs.gov.cn/enroll/home.jsp";
		curl_setopt($ch2,CURLOPT_URL,$url2);
		curl_setopt($ch2,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
		curl_setopt($ch2,CURLOPT_HEADER,0);
		curl_setopt($ch2,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch2, CURLOPT_ENCODING ,'gzip'); //加入gzip解析
		//设置连接结束后保存cookie信息的文件
		curl_setopt($ch2,CURLOPT_COOKIEJAR,$cookie_file);
		$content=curl_exec($ch2);
		curl_close($ch2);
		
		$arr = array(
				"currPage"=>1,
				"pageJSMethod"=>"goToPage"
		);
		//json也可以
		$data_string =  json_encode($arr);
		 
		//curl验证成功
		$ch = curl_init("http://zzfws.bjjs.gov.cn/enroll/dyn/enroll/viewEnrollHomePager.json");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Accept: application/json, text/javascript, */*; q=0.01",
		//"Accept-Encoding: compress, gzip",
		"Accept-Language: zh-CN,zh;q=0.9",
		"Content-Type: application/json;charset=UTF-8",
		"Content-Length: " . strlen($data_string),
		 
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		//设置连接结束后保存cookie信息的文件
		curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			//print curl_error($ch);
		}
		curl_close($ch);
		$rs = json_decode($result,true);
		
		//用正则获取所有table里的内容
		preg_match_all('#<table.+</table>#isU', $rs['data'], $m);
		 
		
		foreach ($m[0] as $k=>$v){
			//获取HTML中a标签链接
			preg_match_all('/<a\s.*?href=[\'|\"]([^\"\']*)[\'|\"][^>]*>/is', $v, $href);
			$link[] = $href[1];
		}
		foreach(array_map('strip_tags', $m[0]) as $k=>$r) {
			$a = preg_split('/\s+/', $r, -1, PREG_SPLIT_NO_EMPTY);
			$res[] = array_chunk(array_slice($a, 0), 3);
		}
		//首先该二维数组必须为下标固定项 0-3,如有更改请根据$res二维数组来更改
		foreach ($res as $k=>$v){
			$res_data[$k]['title'] = $v[0][0].$v[0][1].$v[0][2].$v[0][3];
			$project_name_str = $v[1][0];
			$project_name_arr = explode("：",$project_name_str);
			$res_data[$k]['project_name'] = $project_name_arr[1];
			$enterprise_name_str = $v[1][1];
			$enterprise_name_arr = explode("：",$enterprise_name_str);
			$res_data[$k]['enterprise_name'] = $enterprise_name_arr[1];
			$house_num_str = $v[1][2];
			$house_num_arr = explode("：",$house_num_str);
			$res_data[$k]['house_num'] = $house_num_arr[1];
			$start_time_str = $v[2][0];
			$start_time_arr = explode("：",$start_time_str);
			$res_data[$k]['start_time'] = $start_time_arr[1]." ".$v[2][1];
			$end_time_str = $v[2][2];
			$end_time_arr = explode("：",$end_time_str);
			$res_data[$k]['end_time'] = $end_time_arr[1]." ".$v[3][0];
			$project_address_str = $v[3][1];
			$project_address_arr = explode("：",$project_address_str);
			$res_data[$k]['project_address'] = $project_address_arr[1];
			$res_data[$k]['link'] = $link[$k][0];
		}
		//查询表记录更新所有字段为1已申购
		$home_ids = M('home')->getField('id',true);
		if(!empty($home_ids)){
			$upData['status'] = 1;
			$map['id'] = array('eq',implode(',', $home_ids));
			M('home')->where($map)->save($upData);
		}
		//首先循环添加数据
		foreach ($res_data as $v){
			$where['project_name'] = array('eq',$v['project_name']);
			$home_info = M('home')->where($where)->field('id')->find();
			if(empty($home_info)){
				$addData['project_name'] = $v['project_name'];
				$addData['title'] = $v['title'];
				$addData['enterprise_name'] = $v['enterprise_name'];
				$addData['house_num'] = $v['house_num'];
				$addData['start_time'] = $v['start_time'];
				$addData['end_time'] = $v['end_time'];
				$addData['project_address'] = $v['project_address'];
				$addData['link'] = $v['link'];
				$addData['addTime'] = time();
				M('home')->add($addData);
			}else{
				$saveData['id'] = $home_info['id'];
				$saveData['status'] = 0;
				M('home')->save($saveData);
			}
		}
		
		$aaa['addTime'] = date("Y-m-d H:i:s");
		M('test')->add($aaa);
	}
}