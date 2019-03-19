<?php
namespace Manage\Controller;

use Manage\Model\HelpModel;

class HelpController extends CommonController {

    public function helpList(){
        $helpModel = new HelpModel();
        $help = $helpModel->find();
        $this->assign('helpList',$help);
        $this->display();
    }


    public function addHelpData(){
        $id = $_POST['id'];
        $helpModel = new HelpModel();
        if(!$id){   //添加
            if($_FILES){
                $info = $this->uploadCommon();
                foreach ($info as $file) {
                    $helpModel->help_movie = $file['savepath'] . $file['savename'];
                    $helpModel->help_title = $_POST['help_title'];
                    $result = $helpModel->add();
                }
            }

        }else{     //修改
            if($_FILES){
                $info = $this->uploadCommon();
                foreach ($info as $file) {
                    $helpModel->help_movie = $file['savepath'] . $file['savename'];
                    $helpModel->help_title = $_POST['help_title'];
                    $result = $helpModel->where("id=$id")->save();
                }
            }else{
                $helpModel->help_title = $_POST['help_title'];
                $result = $helpModel->where("id=$id")->save();
            }


        }
        if($result){
            $this->ajaxReturn([
                'code' => 200,
                'msg' => 'success',
            ]);
        }else{
            $this->ajaxReturn([
                'code' => 400,
                'msg' => 'error',
            ]);
        }
    }


    public function uploadCommon() {
        $config = array(
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
            'exts' => array( 'jpg', 'gif', 'png', 'jpeg', 'mp4' ), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array( 'date', 'Ymd' ), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './Uploads/Manage/', //保存根路径
            'savePath' => '', //保存路径
        );
        $upload = new\ Think\ Upload( $config ); // 实例化上传类
        $info = $upload->upload();
        if ( !$info ) {
            $this->ajaxReturn( $upload->getError() );
        } else {
            return $info;
        }
    }

}
?>