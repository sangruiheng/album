<?php

namespace Manage\Controller;

use Api\Model\AlbumdetailModel;
use Manage\Model\AlbumModel;
use Manage\Model\CommentModel;
use Manage\Model\LikeModel;
use Manage\Model\UsersystemModel;

class AlbumController extends CommonController
{

    public function albumList()
    {
        $albumModel = new AlbumModel();
        $p = $_GET['p'];
        if (empty($p)) {
            $p = 1;
        }
        $map['album_finally'] = 1;
        $album = $albumModel->where($map)->relation('user')->order('id desc')->page($p . ',10')->select();
        $count = $albumModel->where($map)->count();
        $Page = getpage($count, 10);
        foreach ($map as $key => $val) {
            $page->parameter .= "$key=" . urlencode($val) . '&';
        }
        $this->assign('page', $Page->show());
        $this->assign('album', $album);
        $this->display();
    }


    //相册详情
    public function albumDetail(){
        $albumModel = new AlbumModel();
        $likeModel = new LikeModel();
        $commentModel = new CommentModel();
        $albumDetailModel = new AlbumdetailModel();
        $album_id = $_GET['album_id'];
        $album = $albumModel->relation('albumdetail')->where("id=$album_id")->find();
        $album['count_like'] = $likeModel->where("album_id=$album_id")->count();
        $album['count_comment'] = $commentModel->where("album_id=$album_id")->count();
        $map['album_id'] = $album_id;
        $map['is_thumb'] = 1;
        $albumDetail = $albumDetailModel->where($map)->find();
        $album['img_thumb'] = C('Setting.Website_URL') . $albumDetail['albumdetail_img'];
        foreach ($album['albumdetail'] as &$value){
            $value['albumdetail_img'] = C('Setting.Website_URL') . $value['albumdetail_img'];
            if($value['describe_type'] == 2){
                $value['albumdetail_describe'] = C('Setting.Website_URL') . $value['albumdetail_describe'];
            }
        }
//        print_r($album);
        $this->assign('album',$album);
        $this->display();
    }


    //删除并添加系统消息
    public function deleteAlbum()
    {
        $table = $_POST['table'];
        $ids = $_POST['delID'];
        $sql = M($table);
        $userSystem = new UsersystemModel();
        $albumModel = new AlbumModel();
        if (strlen($ids) > 0) {
            $ids = substr($ids, 0, strlen($ids) - 1);
        }

        $array_ids = explode(",", $ids);
        foreach ($array_ids as $key => $val) {
            $album = $albumModel->where("id=".$array_ids[$key])->find();
            $userSystem->album_id = $array_ids[$key];
            $userSystem->album_system = 1;
            $userSystem->system_addTime = time();
            $userSystem->user_id = $album['user_id'];
            $userSystem->add();
        }
//        $Result = $sql->delete($ids);
    }

    //下架并加入系统消息
    public function lowerFrame()
    {
        $userSystem = new UsersystemModel();
        $albumModel = new AlbumModel();
        $rs = M($_POST['table'])->save(array('id' => $_POST['id'], 'status' => $_POST['status']));
        if($_POST['status'] == 1){  //
            $album = $albumModel->where("id=".$_POST['id'])->find();
            $userSystem->album_id = $_POST['id'];
            $userSystem->album_system = 0;
            $userSystem->system_addTime = time();
            $userSystem->user_id = $album['user_id'];
            $userSystem->add();
        }else if($_POST['status'] == 0){
            $userSystem->where("album_id=".$_POST['id'])->delete();
        }
        if ($rs) {
            $returnData['success'] = $rs;
            $returnData['status'] = $_POST['status'];
            $returnData['id'] = $_POST['id'];
            $returnData['table'] = $_POST['table'];
        } else {
            $returnData['success'] = $rs;
            $returnData['info'] = '修改状态失败，请及时联系管理员';
        }
        $this->ajaxReturn($returnData);
    }


}

?>