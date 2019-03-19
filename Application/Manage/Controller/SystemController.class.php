<?php
namespace Manage\Controller;
use Think\Controller;
use Think\Db;
use Think\Model;
class SystemController extends CommonController {

    public function index(){
        $this->display();
    }

    //模块列表页面
    public function moduleList(){
        //获取主模块
        $map['parent_id'] = array('eq',0);
        $parList = M('module')->where($map)->field('id,moduleName')->select();
        $this->assign('parList',$parList);

        $where = 'id is not null';
        if(!empty(I('moduleType'))){
            $where .= ' and parent_id'.$_GET['moduleType'];
        }
        if(!empty(I('parent_id'))){
            $where .= ' and parent_id = '.I('parent_id');
        }
        $this->getMlist('module',$_GET['keyWord'],$where);
    }

    //添加模块列表
    public function addModule(){
        $iconList = M('icons')->select();
        $this->assign('iconList',$iconList);

        $this->display();
    }

    //字典类型列表页面
    public function dictionaryTypeList(){
        $this->getMlist('Dictionary',$_GET['keyWord'],'status=1');
    }

    //字典数据列表页面
    public function dictionaryDataList(){
        $where = 'status = 2';
        if(!empty($_GET['typeID'])){
            $where .= ' AND typeID = '.$_GET['typeID'];
        }
        $this->assign('zdlxList',M('Dictionary')->where('status=1')->select());
        $this->getDlist('Dictionary',$_GET['keyWord'],$where);
    }

    //添加字典数据页面-读取字典类型列表
    public function addDictionaryData(){
        $sql = M('Dictionary');
        $list = $sql->where('status=1')->order('id desc')->select();
        $this->assign("list",$list);
        $this->display();
    }

    //读取用户群组列表
    public function groupList(){
        $this->getMlist('Group',$_GET['keyWord']);
    }

    //添加用户群组页面-读取权限规则列表
    public function addGroup(){
        $map['parent_id'] = array('eq',0);
        $moduleList = M('module')->where($map)->order('sort desc')->field('id,moduleName')->select();
        $moduleCount = M('module')->where($map)->count();
        foreach ($moduleList as $k=>$v){
            $where['parent_id'] = array('eq',$v['id']);
            $moduleList[$k]['list'] = M('module')->where($where)->field('id,moduleName')->order('sort desc')->select();
        }

        //读取群组规则
        $sql = M('Rule');
        $list = $sql->order('id desc')->select();
        $this->assign("list",$list);

        $this->assign("moduleList",$moduleList);
        $this->assign("moduleCount",$moduleCount);

        $this->display();
    }

    //添加用户页面-读取群组列表和部门列表
    public function addAdminuser(){
        $groupList = M('group')->where('status = 0')->field('id,title')->select();
        $this->assign('groupList',$groupList);

        $this->display();
    }

    //用户列表页面
    public function adminuserList(){
        $this->getDlist('Adminuser',$_GET['keyWord']);
    }

    //添加主模块页面
    public function addModuleType(){
        $sql = M('Icons');
        $list = $sql->select();
        $this->assign("list",$list);
        $this->display();
    }

    public function userInfo(){
        $sql = M('Icons');
        $file = fopen($_SERVER["DOCUMENT_ROOT"]."/icons.txt", "r") or exit("Unable to open file!");
        while(!feof($file))
        {
            $data['iconName'] = fgets($file);
            $sql->add($data);
        }
        fclose($file);
        $this->display();
    }

    //系统临时文件列表
    public function tempList(){
        $dir = $_SERVER["DOCUMENT_ROOT"].'/Application/Runtime';
        $list = loopFun($dir);

        $this->getMlist('temp');
    }
    //删除系统临时文件
    public function delLog(){
        $list = M('temp')->select();
        $ids = M('temp')->getField('id',true);
        foreach ($list as $file){
            //M('temp')->delete($file['id']);
            if(file_exists($file['filePath'])){
                $result = @unlink ($file['filePath']);
            }
        }
        $rs = M('temp')->delete(implode(',', $ids));
        $this->ajaxReturn($ids);
    }

    //SQL 备份
    public function sqlList(){
        $db = Db::getInstance();
        $list = $db->query('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case', $list);

        $p = $_GET['p']?$_GET['p']:$_GET['p'];

        $Page = getpage(count($list),10);
        $this->assign('page',$Page->show());
        $this->assign('count',count($list));


        $this->assign('list',$list);
        $this->display();
    }

    //权限规则列表页面
    public function ruleList(){
        $this->getMlist('Rule',$_GET['keyWord']);
    }

    //设置权限
    public function addAuthData()
    {
        $backUrl = $_GET['backUrl'];
        $table = $_GET['table'];
        $controller = $_GET['controller'];
        $id = $_POST['id'];
        $sql = D($table);
        if($table == 'Group'){
            $_POST['twoRules'] = implode(',' ,$_POST['twoRules']);
        }
        if ($sql->create()) {
            if (empty($id)) {  //添加
                $sql->id = NULL;
                $result = $sql->add();
                $access = D('access');
                $uid = D('adminuser')->max('id');
                $groupID = $_POST['groupID'];
//                $access->uid = $uid;
//                $access->group_id = $groupID;
//                $result = $access->add();
            } else {     //修改
                $result = $sql->save();
                $access = D('access');
                $groupID = $_POST['groupID'];
//                $access->group_id = $groupID;
//                $result = $access->where("uid=$id")->save();
                $this->success('编辑成功！', U($controller . '/' . $backUrl));
            }
            if ($result) {
                $this->success('编辑成功！', U($controller . '/' . $backUrl));
            }
        } else {
            $this->error($sql->getError(), $jumpUrl = '', $ajax = true);
        }
    }


}
?>