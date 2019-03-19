<?php

namespace Manage\Controller;

class EntryController extends CommonController
{

    public function entryList()
    {
        $entryList = M('entry')->find(1);
        $this->assign('entryList', $entryList);
        $this->display();
    }


    public function addEntryData()
    {
        $backUrl = $_GET['backUrl'];
        $table = $_GET['table'];
        $controller = $_GET['controller'];
        $id = $_POST['id'];
        $sql = D($table);
        if ($sql->create()) {
            if (empty($id)) { //添加
                $sql->id = NULL;
                $result = $sql->add();

            } else {  //修改
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