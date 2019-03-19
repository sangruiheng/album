<?php

namespace Manage\Controller;

use Manage\Model\SensitiveModel;

class SensitiveController extends CommonController
{

    public function sensitiveList()
    {
        $sensitiveModel = new SensitiveModel();
        $p = $_GET['p'];
        if (empty($p)) {
            $p = 1;
        }
        $sensitive = $sensitiveModel->order('id desc')->page($p . ',10')->select();
        $count = $sensitiveModel->count();
        $Page = getpage($count, 10);
//        foreach ($map as $key => $val) {
//            $page->parameter .= "$key=" . urlencode($val) . '&';
//        }
        $this->assign('page', $Page->show());
        $this->assign('list',$sensitive);
        $this->display();
    }

    public function addSensitiveData()
    {
        $backUrl = $_GET['backUrl'];
        $table = $_GET['table'];
        $controller = $_GET['controller'];
        $id = $_POST['id'];
        $sql = D($table);
        if ($sql->create()) {
            if (empty($id)) { //添加
                $sql->id = NULL;
                $sql->sensitive_addTime = time();
                $result = $sql->add();

            } else {  //修改
                $sql->sensitive_addTime = time();
                $result = $sql->save();
            }
        }
        if ($result) {
            $this->success('编辑成功！', U($controller . '/' . $backUrl));
        } else {
            $this->error($sql->getError(), $jumpUrl = '', $ajax = true);
        }
    }


}

?>