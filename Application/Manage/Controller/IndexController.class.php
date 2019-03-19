<?php

namespace Manage\Controller;

use Manage\Model\AdminuserModel;
use Think\Controller;

class IndexController extends CommonController
{
    public function index()
    {
        $uid = session('crm_uid');
        $adminUserModel = new AdminuserModel();
        $adminUser = $adminUserModel->where("id=$uid")->find();
        $this->assign('user_name', $adminUser['username']);
        $this->display();
    }

    //欢迎页
    public function welcome()
    {
        $this->display();
    }


    public function validateSessionID()
    {
        $PHPSESSID = $_REQUEST['PHPSESSID'];
        $adminUserModel = M('adminuser');
        $map['session_id'] = $PHPSESSID;
        $adminUser = $adminUserModel->where($map)->find();

        if ($adminUser) {
            $this->ajaxReturn([
                'code' => 200,
                'msg' => 'success',
                'data' => $adminUser,
            ]);
        } else {
            $uid = session('crm_uid');
            $adminUserModel = new AdminuserModel();
            $adminUser = $adminUserModel->where("id=$uid")->find();
            $nextLoginTime = $adminUser['nextLoginTime'];
            $adminUser['nextLoginTime'] = date('Y-m-d H:i',$nextLoginTime);
            $position = $this->get_area($adminUser['nextLoginIp']);
            $adminUser['nextLoginIp'] = $position['data']['city'];
            $this->ajaxReturn([
                'code' => 400,
                'data' => $adminUser,
                'msg' => 'error',
            ]);
        }
    }


//淘宝接口：根据ip获取所在城市名称
    function get_area($ip = '')
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
        $ret = $this->https_request($url);
        $arr = json_decode($ret, true);
        return $arr;
    }
}

?>