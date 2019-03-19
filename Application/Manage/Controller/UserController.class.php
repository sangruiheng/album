<?php

namespace Manage\Controller;

use Manage\Model\AdminuserModel;
use Manage\Model\CommentModel;
use Manage\Model\LikeModel;
use Manage\Model\UserModel;
use Manage\Model\AlbumModel;

class UserController extends CommonController
{

    public function userList()
    {

        $userModel = new UserModel();
        if (!empty($_GET['keyWord'])) {
            $map = $this->Search('user', $_GET['keyWord']);
        }
        $p = $_GET['p'];
        if (empty($p)) {
            $p = 1;
        }
        $user = $userModel->relation('album')->where($map)->order('id desc')->page($p . ',10')->select();
        foreach ($user as &$value) {
            $value['album_count'] = count($value['album']);
        }
        $count = $userModel->where($map)->count();
        $Page = getpage($count, 10);
        foreach ($map as $key => $val) {
            $page->parameter .= "$key=" . urlencode($val) . '&';
        }
        $this->assign('page', $Page->show());
        $this->assign('list', $user);
        $this->assign('tree', $a);
        $this->display();
    }

    /*    public function sort($data, $pid = 0, $level = 0)
        {
            $userModel = new UserModel();
            $commentModel = new CommentModel();
            static $arr = array();
            foreach ($data as &$v) {
                if ($v['parent_id'] == $pid) {
                    $user = $userModel->where("id=".$v['user_id'])->find();
                    $v['uid'] = $user['id'];
                    $v['nickName'] = $user['nickName'];
                    if($v['parent_id'] != 0){
                        $pComment = $commentModel->where("id=".$v['parent_id'])->find();
                        $pUser = $userModel->where("id=".$pComment['user_id'])->find();
                        $v['pUser_nickName'] = $pUser['nickName'];
                    }
                    $v['level'] = $level;
                    $arr[] = $v;
                    $this->sort($data, $v['id'], $level + 1);
                }
            }
            return $arr;
        }*/


    public function userDetail()
    {
        $user_id = $_GET['user_id'];
        $userModel = new UserModel();
        $map['id'] = $user_id;
        $user = $userModel->where($map)->find();
        $this->assign('user', $user);
        $this->display();
    }

    public function albumList()
    {
        $albumModel = new AlbumModel();
        $album = $albumModel->relation('user')->select();
        $this->assign('album', $album);
        $this->display();
    }


    public function likeList()
    {
        $likeModel = new LikeModel();
        $p = $_GET['p'];
        if (empty($p)) {
            $p = 1;
        }
        $like = $likeModel->relation(array('user', 'album'))->order('id desc')->page($p . ',10')->select();
        $count = $likeModel->count();
        $Page = getpage($count, 10);
//        foreach ($map as $key => $val) {
//            $page->parameter .= "$key=" . urlencode($val) . '&';
//        }
        $this->assign('page', $Page->show());
        $this->assign('list', $like);
        $this->display();
    }

    public function commentList()
    {
        $commentModel = new CommentModel();
        $p = $_GET['p'];
        if (empty($p)) {
            $p = 1;
        }
        $comment = $commentModel->relation(array('user', 'album'))->order('id desc')->page($p . ',10')->select();
        $count = $commentModel->count();
        $Page = getpage($count, 10);
//        foreach ($map as $key => $val) {
//            $page->parameter .= "$key=" . urlencode($val) . '&';
//        }
        $this->assign('page', $Page->show());
        $this->assign('list', $comment);
        $this->display();
    }

    //修改密码显示
    public function updatePassword()
    {
            $this->display();
    }

    //修改密码
    public function updatePasswordData(){
        $uid = $_POST['uid'];
        $adminUserModle = new AdminuserModel();
        $adminUserModle->password = md5($_POST['new_password']);
        $adminUser = $adminUserModle->where("id=$uid")->save();
        session(null);
        if(!$adminUser){
            $this->ajaxReturn([
                'code' => 400,
                'msg' => 'error'
            ]);
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
        ]);

    }

    //验证密码
    public function validatePassword()
    {
        $id = $_POST['uid'];
        $new_password = md5($_POST['new_password']);
        $old_password = md5($_POST['old_password']);
        $adminUserModle = new AdminuserModel();
        $adminUser = $adminUserModle->where("id=$id")->find();
        if (!$adminUser) {
            $this->ajaxReturn([
                'code' => 400,
                'msg' => 'error'
            ]);
        }

        if ($adminUser['password'] == $new_password) {
            $this->ajaxReturn([
                'code' => 202,
                'msg' => '修改的密码不能与原密码一致',
            ]);
        }

        if ($adminUser['password'] != $old_password) {
            $this->ajaxReturn([
                'code' => 203,
                'msg' => '原密码输入不正确',
            ]);
        }


        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
        ]);
    }

}

?>