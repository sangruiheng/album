<?php
//公共类文件
namespace Manage\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){
		$this->checkLogin();

        //权限管理
//        $auth = new \Think\Auth();
//        $module_name = '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
//        if (!$auth->check1($module_name, $_SESSION['crm_uid'])) {
//            echo '<div style="font-size: 14px;background: rgba(0, 0, 0, 0.74);color: #fff;position: absolute;padding: 20px 25px;border-radius: 5px;left: 50%;margin-left: -102px;top: 20%;">您没有权限访问此模块！</div>';
//            exit;
//        }
	}
	//检查用户是否登录
	//读取icon信息,模块信息,群组信息列表
	public function checkLogin(){
		if(empty(session('crm_uid'))) {
            $this->redirect('/Login/index');
            return false;
        }
		$this->getModuleList();
	}

    ////POST请求函数
    function https_request($url, $data = null)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        if (!empty($data)) {//如果有数据传入数据
            curl_setopt($curl, CURLOPT_POST, 1);//CURLOPT_POST 模拟post请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);//传入数据
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);

        return $output;
    }

	
	//读取模块信息
	public function getModuleList()
	{
		$condition['id'] = array('eq',session('crm_groupID'));
		$authList = M('group')->where($condition)->field('rules')->find();
		$parentID = I('crm_parents');
		if(empty($parentID)){
			$_SESSION['crm_rules'] = $authList['rules'];
			$where['parent_id'] = array('eq',0);
			$where['status']    = array('eq',0);
//			$where['id']        = array('in',$authList['rules']);
			$moduleTypeList = M('module')->where($where)->field('id,moduleName,moduleLink,moduleIcon')->order('sort asc')->select();
			$this->assign('moduleTypeList',$moduleTypeList);
		}else{
			$map['parent_id'] = array('eq',$parentID);
			$map['id']      = array('in',$authList['rules']);
			$moduleList = M('module')->where($map)->field('id,moduleName,moduleLink,moduleIcon')->order('sort asc')->select();
			$this->ajaxReturn($moduleList);
		}
	}
	
	//退出登录，清空session
	public function loginOut(){
		session(null);
		$ajaxReturn['status'] = 1;
		$this->ajaxReturn($ajaxReturn);
	}
	
	//编辑查询数据
	public function getEditData(){
		$table = I('table');
		if(empty($table))$this->ajaxReturn(array('code'=>400,'msg'=>'表名缺失'));
		$where = I('where');
		if(empty($where))$this->ajaxReturn(array('code'=>400,'msg'=>'条件缺失'));
		$field = I('field');
		if(empty($field))$field = '*';
		$rs = M($table)->where($where)->field($field)->select();
		if($rs){
			$this->ajaxReturn(array('code'=>200,'data'=>$rs));
		}else{
			$this->ajaxReturn(array('code'=>400,'msg'=>'暂无数据'));
		}
	}




    //编辑新闻查询数据
    public function getEditNewsData()
    {
        $table = I('table');
        if (empty($table)) $this->ajaxReturn(array('code' => 400, 'msg' => '表名缺失'));
        $where = I('where');
        if (empty($where)) $this->ajaxReturn(array('code' => 400, 'msg' => '条件缺失'));
        $field = I('field');
        if (empty($field)) $field = '*';
        $news = M($table)->where($where)->field($field)->find();
        $id = $news['id'];
        $newsImg = M('newsimg')->where("newsID=$id")->select();
        $news['newsImg'] = $newsImg;
        if ($news) {
            $this->ajaxReturn(array('code' => 200, 'data' => $news));
        } else {
            $this->ajaxReturn(array('code' => 400, 'msg' => '暂无数据'));
        }
    }


    //编辑新闻查询数据
    public function getEditAttrData()
    {
        $table = I('table');
        if (empty($table)) $this->ajaxReturn(array('code' => 400, 'msg' => '表名缺失'));
        $where = I('where');
        if (empty($where)) $this->ajaxReturn(array('code' => 400, 'msg' => '条件缺失'));
        $field = I('field');
        if (empty($field)) $field = '*';
        $attributeName = M($table)->where($where)->field($field)->find();
        $id = $attributeName['id'];
        $attributeValue = M('attributevalue')->where("attributename_id=$id")->select();
        for ($i = 0; $i < count($attributeValue); $i++){
            $arr[] = $attributeValue[$i]['attributevalue_name'];
        }
        $attributeValue = implode("/", $arr);
        $attributeName['attributevalue_name'] = $attributeValue;
        if ($attributeName) {
            $this->ajaxReturn(array('code' => 200, 'data' => $attributeName));
        } else {
            $this->ajaxReturn(array('code' => 400, 'msg' => '暂无数据'));
        }
    }


    //修改表审核status字段
    public function table_status()
    {
        $rs = M($_POST['table'])->save(array('id'=>$_POST['id'],'status'=>$_POST['status']));
        if($rs){
            $returnData['success'] = $rs;
            $returnData['status']  = $_POST['status'];
            $returnData['id']  = $_POST['id'];
            $returnData['table']  = $_POST['table'];
        }else{
            $returnData['success'] = $rs;
            $returnData['info']  = '修改状态失败，请及时联系管理员';
        }
        $this->ajaxReturn($returnData);
    }


	
	//添加、编辑数据的公共方法
	public function addData(){
		$backUrl    = $_GET['backUrl'];
		$table      = $_GET['table'];
		$controller = $_GET['controller'];
		$id         = $_POST['id'];
		$sql        = D($table);
        if($table == 'Group'){
            $_POST['twoRules'] = implode(',' ,$_POST['twoRules']);
        }
		if($sql->create()){
			if(empty($id)){
				$sql->id = NULL;
				$result = $sql->add();
				$this->setAuth($table,$result);
			}else{
				$result = $sql->save();
			}
			if($result){
				$this->success('编辑成功！',U($controller.'/'.$backUrl));
			}
		}else{
			$this->error($sql->getError(),$jumpUrl='',$ajax=true);
		}
	}
	
	
	/* 如果当前用户是admin用户自动增加权限
	table = module
	id = 新增的ID
	后台生成文件 php 和 HTML文件 
	生成文件之前首先判断当前文件是否已存在
	*/
	public function setAuth($table,$id){
		if(empty($table))return false;
		if(empty($id))return false;
		if(C('AUTH_MODULE.auth_user_id') != session('crm_uid'))return false;
		if($table != 'Module')return false;
		$map['id'] = array('eq',C('AUTH_MODULE.auth_group_id'));
		$rules = M('group')->where($map)->getField('rules');
		$data['id'] = C('AUTH_MODULE.auth_group_id');
		$data['rules'] = $rules.','.$id;
		$data['addTime'] = time();
		M('group')->save($data);

		$where['id'] = array('eq',$id);
		$info = M('Module')->where($where)->field('moduleLink,moduleName,status,is_file,is_table,is_add_file')->find();
		if(empty($info['moduleLink']))return false;
		if($info['status'] == 1)return false;
		if($info['is_file'] == 0)return false;
		$link_arr = explode('/', $info['moduleLink']);
		$controller_name = $link_arr[1];
		$view_name = $link_arr[2];
		$controller_file = $_SERVER['DOCUMENT_ROOT'].C('GENERATE_DIR.generate_controller_file').$controller_name.'Controller.class.php';
		//PHP 文件
		if(file_exists($controller_file)){
			//存在追加PHP文件
			$txt = '';
			$handle = fopen($controller_file,"a+");
			$fp = file($controller_file);
			file_put_contents($controller_file, join('', array_slice($fp, 0, -1)));
			$txt .= "    /*".$info['moduleName']."*/ \n";
			$txt .= "    public function ".$view_name."(){ \n";
			$txt .= "        \$this->getMList('".$info['is_table']."',\$_GET['keyWord']); \n";
			$txt .= "    } \n";
			$txt .= "} \n";
			fwrite($handle,$txt);
			fclose($handle);
		}else{
			//不存在生成PHP文件/*  */
			$myfile = fopen($controller_file, "w") or die("Unable to open file!");
			$txt = "<?php \n";
			$txt .= C('GENERATE_DIR.namespace_controller_name')." \n";
			$txt .= C('GENERATE_DIR.use_controller')." \n";
			$txt .= "class ".$controller_name."Controller extends CommonController{ \n";
			$txt .= "    /*".$info['moduleName']."*/ \n";
			$txt .= "    public function ".$view_name."(){ \n";
			$txt .= "        \$this->getMList('".$info['is_table']."',\$_GET['keyWord']); \n";
			$txt .= "    } \n";
			$txt .= "} \n";
			fwrite($myfile, $txt);
			fclose($myfile);
		}
		//html文件 列表
		$fields = M($info['is_table'])->getDbFields();
		$dir_file = $_SERVER['DOCUMENT_ROOT'].C('GENERATE_DIR.generate_view_file').$controller_name;
		is_dir($dir_file) OR mkdir($dir_file, 0777, true);
		$view_file = $dir_file.'/'.$view_name.'.html';
		if(!file_exists($view_file)){
			$txt = '';
			$myfile = fopen($view_file, "w") or die("Unable to open file!");
			$txt .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"> \n";
			$txt .= "<html xmlns=\"http://www.w3.org/1999/xhtml\"> \n";
			$txt .= "<head> \n";
			$txt .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /> \n";
			$txt .= "<title>无标题文档</title> \n";
			$txt .= "<include file=\"Common:common\" /> \n";
			$txt .= "<load href=\"__PUBLIC__/css/common/rightCommon.css\" /> \n";
			$txt .= "<script type=\"text/javascript\"> \n";
			$txt .= "$(document).ready(function(e) { \n";
			$txt .= "    \n";
			$txt .= "    \n";
			$txt .= "}); \n";
			$txt .= "</script> \n";
			$txt .= "</head> \n";
			$txt .= "<body> \n";
			$txt .= "<nav class=\"navbar navbar-default\" role=\"navigation\"> \n";
			$txt .= "<div class=\"navbar-header\"> \n";
			$txt .= "<a class=\"navbar-brand\" href=\"#\">".$info['moduleName']."</a> \n";
			$txt .= "</div> \n";
			$txt .= "<div> \n";
			if($info['is_add_file'] == 1){
				$txt .= "<button type=\"button\" data-id=\"<{:C('AUTH_MODULE.auth_del_id')}>\" class=\"btn btn-danger navbar-btn listButton\" onclick=\"deleteData('".ucwords($info['is_table'])."')\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i> 删除</button> \n";
				$txt .= "<button type=\"button\" data-id=\"<{:C('AUTH_MODULE.auth_save_id')}>\" class=\"btn btn-info navbar-btn listButton\" onclick=\"editData('add".ucwords($info['is_table'])."')\"><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> 修改</button> \n";
				$txt .= "<button type=\"button\" data-id=\"<{:C('AUTH_MODULE.auth_add_id')}>\" class=\"btn btn-success navbar-btn listButton\" onclick=\"openAddData('add".ucwords($info['is_table'])."')\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i> 新建</button> \n";
			}
			$txt .= "<form class=\"navbar-form navbar-right listSearch\" role=\"search\" method=\"get\" action=\"__ACTION__\"> \n";
			$txt .= "<div class=\"form-group\"> \n";
			$txt .= "<input name=\"keyWord\" type=\"text\" class=\"form-control\" id=\"keyWord\" placeholder=\"请输入关键词进行搜索\"> \n";
			$txt .= "</div> \n";
			$txt .= "<button type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-search\" aria-hidden=\"true\"></i> 搜索</button> \n";
			$txt .= "</form> \n";
			$txt .= "</div> \n";
			$txt .= "</nav> \n";
			$txt .= "<div class=\"list-box\"> \n";
			$txt .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> \n";
			$txt .= "<thead> \n";
			$txt .= "<tr> \n";
			$txt .= "<th>选择</th> \n";
			foreach ($fields as $v){
				if($v =='id'){
					$txt .= "<th>编号</th> \n";
				}else{
					$txt .= "<th>".$v."</th> \n";
				}
			}
			$txt .= "</tr> \n";
			$txt .= "</thead> \n";
			$txt .= "<tbody> \n";
			$txt .= "<if condition=\"count(\$list) gt 0\"> \n";
			$txt .= "<volist name=\"list\" id=\"vo\"> \n";
			$txt .= "<td><input type=\"checkbox\" name=\"del_listID\" id=\"del_listID\" data-name=\"multi-select\" value=\"<{\$vo.id}>\" /></td> \n";
			foreach ($fields as $v){
				$txt .= "<td><{\$vo.".$v."}></td> \n";
			}
			$txt .= "</tr> \n";
			$txt .= "</volist> \n";
			$txt .= "<else/> \n";
			$txt .= "<tr> \n";
			$txt .= "<td colspan=\"".(count($fields)+1)."\">Oh!暂无列表</td> \n";
			$txt .= "</tr> \n";
			$txt .= "</if> \n";
			$txt .= "</tbody> \n";
			$txt .= "</table> \n";
			$txt .= "</div> \n";
			$txt .= "<ul class=\"pagination\"> \n";
			$txt .= "<{\$page}> \n";
			$txt .= "</ul> \n";
			$txt .= "</body> \n";
			$txt .= "</html> \n";
			fwrite($myfile, $txt);
			fclose($myfile);
		}
		//html 添加\修改页面 并且创建模型文件
		if($info['is_add_file'] == 0)return false;
		$model_add_file = $controller_file = $_SERVER['DOCUMENT_ROOT'].C('GENERATE_DIR.generate_model_file').ucwords($info['is_table']).'Model.class.php';
		if(!file_exists($model_add_file)){
			$txt = '';
			$myfile = fopen($model_add_file, "w") or die("Unable to open file!");
			$txt .= "<?php \n";
			$txt .= C('GENERATE_DIR.namespace_model_name')." \n";
			$txt .= C('GENERATE_DIR.use_model_name')." \n";
			$txt .= "class ".ucwords($info['is_table'])."Model extends RelationModel{ \n";
			$txt .= "    /*关联模型*/ \n";
			$txt .= "    protected \$_link = array( \n";
			$txt .= "            \n";
			$txt .= "    );\n";
			$txt .= "    /*form表单验证*/ \n";
			$txt .= "    protected \$_validate = array(\n";
			foreach ($fields as $v){
				if($v != 'id'){
					$txt .= "            /*字段名：".$v."*/\n";
					$txt .= "            array('".$v."','require','请输入".$v."'),\n";
				}
			}
			$txt .= "    );\n";
			$txt .= "    /*表单自动验证auto*/\n";
			$txt .= "    protected \$_auto = array ( \n";
			$txt .= "            array('addTime', 'time', self::MODEL_INSERT, 'function'),\n";
			$txt .= "            array('saveTime', 'time', self::MODEL_UPDATE, 'function'),\n";
			$txt .= "    );\n";
			$txt .= "} \n";
			fwrite($myfile, $txt);
			fclose($myfile);
		}


		$view_add_file = $dir_file.'/add'.ucwords($info['is_table']).'.html';
		if(!file_exists($view_add_file)){
			$txt = '';
			$myfile = fopen($view_add_file, "w") or die("Unable to open file!");
			$txt .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"> \n";
			$txt .= "<html xmlns=\"http://www.w3.org/1999/xhtml\"> \n";
			$txt .= "<head> \n";
			$txt .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /> \n";
			$txt .= "<title>无标题文档</title> \n";
			$txt .= "<include file=\"Common:common\" /> \n";
			$txt .= "<load href=\"__PUBLIC__/css/common/rightCommon.css\" /> \n";
			$txt .= "<script type=\"text/javascript\"> \n";
			$txt .= "$(document).ready(function(e) { \n";
			$txt .= "    getEditData(function(jdata){});\n";
			$txt .= "    \n";
			$txt .= "}); \n";
			$txt .= "</script> \n";
			$txt .= "</head> \n";
			$txt .= "<body> \n";
			$txt .= "<!--alert弹窗Start  --> \n";
			$txt .= "<div id=\"top-alert\" class=\"fixed alert alert-error\" style=\"display:none;\"> \n";
			$txt .= "<button class=\"close fixed\" style=\"margin-top: -18px;margin-right: 7px;\">&times;</button> \n";
			$txt .= "<div class=\"alert-content\">这是内容</div> \n";
			$txt .= "</div> \n";
			$txt .= "<!--alert弹窗end  --> \n";
			$txt .= "<nav class=\"navbar navbar-default\" role=\"navigation\"> \n";
			$txt .= "<div class=\"navbar-header\"> \n";
			$txt .= "<a class=\"navbar-brand\" href=\"#\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i> <span id=\"changeTitle\">添加</span>".$info['moduleName']."</a> \n";
			$txt .= "</div> \n";
			$txt .= "</nav> \n";
			$txt .= "<div class=\"add-box\"> \n";
			$txt .= "<form class=\"addForm ajax-fadein\" id=\"form1\" name=\"form1\" method=\"post\" action=\"__APP__/Common/addData/controller/".$controller_name."/backUrl/".$view_name."/table/".$info['is_table']."\"> \n";
			$txt .= "<input name=\"id\" type=\"hidden\" id=\"id\" value=\"<{\$_GET['id']}>\" /> \n";
			$txt .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> \n";
			$txt .= "<tbody> \n";
			foreach ($fields as $v){
				if($v != 'id'){
					$txt .= "    <tr> \n";
					$txt .= "        <td align=\"center\">".$v."</td> \n";
					$txt .= "        <td><input type=\"text\" class=\"form-control\" name=\"".$v."\" id=\"".$v."\" placeholder=\"\" /></td> \n";
					$txt .= "    </tr> \n";
				}
			}
			$txt .= "    <tr> \n";
			$txt .= "        <td>&nbsp;</td> \n";
			$txt .= "        <td> \n";
			$txt .= "        <button type=\"submit\" class=\"btn btn-success\" id=\"saveButton\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i> 添加</button> \n";
			$txt .= "        <button type=\"button\" class=\"btn btn-default\" id=\"cancelButton\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i> 取消</button> \n";
			$txt .= "        </td> \n";
			$txt .= "    </tr> \n";
			$txt .= "</tbody> \n";
			$txt .= "</table> \n";
			$txt .= "</form> \n";
			$txt .= "</div> \n";
			$txt .= "</body> \n";
			$txt .= "</html> \n";
			fwrite($myfile, $txt);
			fclose($myfile);
		}
		return false;
	}


    public function Search($table, $keyWord){

        $fields = D($table)->getDbFields();
        foreach($fields as $value){
            $fieldsName .= $value.'|';
        }
        $fieldsName = substr($fieldsName,0,strlen($fieldsName)-1);
        $map[$fieldsName] = array('EXP', "LIKE BINARY '%{$keyWord}%'");
        return $map;

    }
	
	
	//读取列表数据的M公共方法
	public function getMlist($table,$keyWord,$condition){
		if(!empty($condition)){
			$map['_string'] = $condition;
		}
		$sql = M($table);
		if(!empty($keyWord)){
			$fields = $sql->getDbFields();
			foreach($fields as $value){
				$fieldsName .= $value.'|';
			}
			$fieldsName = substr($fieldsName,0,strlen($fieldsName)-1);
			$map[$fieldsName] = array('EXP', "LIKE BINARY '%{$keyWord}%'");
		}

		$p = $_GET['p'];
		if(empty($p)){
			$p = 1;
		}
		$order = 'id desc';
		$list = $sql->where($map)->order($order)->page($p.',10')->select();
		$this->assign('list',$list);
		$count = $sql->where($map)->count();
		$Page = getpage($count,10);
		foreach($map as $key=>$val) {
			$Page->parameter[$key] = urlencode($val);
		}
		$this->assign('page',$Page->show());
		$this->assign('count',$count);
		$this->display();
	}
	
	//读取列表数据的D公共方法
	/************************************/
	//参数说明
	//$table =========== 要查询的数据表名
	//$keyWord ========= 模糊查询关键词
	//$condition ======= 组合查询语句
	//$nestedTable ===== 嵌套查询的表名
	//$nestedKey ======= 嵌套查询的关联键名
	/***********************************/
	public function getDlist($table,$keyWord,$condition,$nestedTable,$nestedKey){
		if(!empty($condition)){
			$map['_string'] = $condition;
		}
		$sql = D($table);
		if(!empty($keyWord)){
			$fields = $sql->getDbFields();
			foreach($fields as $value){
				$fieldsName .= $value.'|';
			}
			$fieldsName = substr($fieldsName,0,strlen($fieldsName)-1);
			$map[$fieldsName] = array('EXP', "LIKE BINARY '%{$keyWord}%'");
		}

		$p = $_GET['p'];
		if(empty($p)){
			$p = 1;
		}
		$list = $sql->where($map)->order('id desc')->page($p.',10')->relation(true)->select();
		if(!empty($nestedTable)){
			$nestedSSql = M($nestedTable);
			foreach($list as $n=> $val){
				$list[$n]['voo']=$nestedSSql->where(''.$nestedKey.'='.$val['id'].'')->select();
			}
		}
//		print_r($list);
		$this->assign('list',$list);
		$count = $sql->where($map)->count();
		$Page = getpage($count,10);
		foreach($map as $key=>$val) {
			$page->parameter .= "$key=".urlencode($val).'&';
		}
		$this->assign('page',$Page->show());
		$this->assign('count',$count);
		$this->display();
	}
	
	//删除数据的公共方法
	public function deleteData(){
		$table = $_POST['table'];
		$sql = M($table);
		$ids = $_POST['delID'];
		if(strlen($ids) > 0){
			$ids = substr($ids,0,strlen($ids)-1);
		}
		$Result = $sql->delete($ids);
		$this->auth_save_group($table,$ids);
	}

	//当用户删除模块信息时 更新群组里的IDS
	//IDS有可能是多个id 所以循环处理群组表
	//用户删除群组将后台管理员关联群组ID 替换为 ‘’
	public function auth_save_group($table,$ids){
		if(empty($table))return false;
		if(empty($ids))return false;
		switch($table){
			case 'Module':
				$ids_arr = explode(',', $ids);
				for($i=0;$i<count($ids_arr);$i++){
					$map = 'find_in_set("'.$ids_arr[$i].'",rules)';
					$list = M('group')->where($map)->field('id,rules')->select();
					foreach ($list as $v){
						$rules_arr = explode(',', $v['rules']);
						$rules_key = array_search($ids_arr[$i],$rules_arr);
						unset($rules_arr[$rules_key]);
						$rules = implode(',',$rules_arr);
						$data['id'] = $v['id'];
						$data['rules'] = $rules;
						dump($data);
						M('group')->save($data);
					}
				}
			break;
			case 'Group':
				$ids_arr = explode(',', $ids);
				for($i=0;$i<count($ids_arr);$i++){
					$map['groupID'] = array('eq',$ids_arr[$i]);
					$list = M('adminuser')->where($map)->field('id')->select();
					foreach ($list as $v){
						$data['groupID'] = 'is null';
						$data['id'] = $v['id'];
						M('adminuser')->save($data);
					}
				}
			break;
		}
		
		return false;
	}
	//升降序操作的公共方法
	public function dataSort(){
		$action = $_POST['action'];
		$table = $_POST['table'];
		$sql = M($table);
		$where['id'] = $_POST['dataID'];
		if($action == 'dataAsc'){
			$sql->where($where)->setInc('sort');
		}else{
			$sql->where($where)->setDec('sort');
		}
		
	}


    //上传图片公共方法
    public function uploadCommon()
    {
        $config = array(
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
            'exts' => array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './Uploads/Manage/', //保存根路径
            'savePath' => '',//保存路径
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        $info = $upload->upload();
        if (!$info) {
            $this->ajaxReturn($upload->getError());
        } else {
            foreach ($info as $file) {
                $data['url'] = $file['savepath'] . $file['savename'];
            }
            $this->ajaxReturn($data);
        }
    }


    //验证图片大小
    public function check_file_size($width,$height,$path){
        if(empty($width) || empty($height))return true;
        $info = getimagesize($_SERVER['DOCUMENT_ROOT'].$path);
        if($width == $info[0] && $height == $info[1])return true;
        return false;
    }


}
?>