<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 上午11:58
 */

namespace Api\Controller;


use Api\Exception\SuccessException;
use Api\Exception\UserException;
use Api\Model\AlbumCommentViewModel;
use Api\Model\AlbumLikeViewModel;
use Api\Model\AlbumModel;
use Api\Model\CommentModel;
use Api\Model\LikeModel;
use Api\Model\MusicModel;
use Api\Model\UserModel;
use Api\Model\UsersystemModel;
use Api\Service\Token;
use Api\Service\UserToken;
use Api\Validate\IDMustBePostiveInt;
use Api\Validate\NotifyMessage;

Vendor('WxSampleCode.wxBizMsgCrypt');

class UserController extends CommonController
{

    //获取openid
    public function getOpenID($code = '')
    {
        $userToken = new UserToken($code);
        $openID = $userToken->getUserOpenID();
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $openID
        ]);
    }


    //用户登陆 返回token
    public function wxLogin()
    {
        //openID nickName avatarUrl city

//        if(!$_POST['openID']){
//            $this->ajaxReturn((new UserException([
//                'code' => 30033,
//                'msg' => 'openID为空'
//            ]))->getException());
//        }
        $userToken = new UserToken();
        $wxResult = [
            'openID' => $_POST['openID'],
            'nickName' => $_POST['nickName'],
            'avatarUrl' => $_POST['avatarUrl'],
            'city' => $_POST['city'],
        ];
        $token = $userToken->wxGetUserInfo($wxResult);
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'Token' => $token
        ]);
    }


    //个人中心显示页
    public function personalCenter()
    {
        //token
        //根据token来获取uid
        $this->uid = Token::getCurrentUid();
        $this->is_user();
        $userModel = new UserModel();
        $user = $userModel->where("id=$this->uid")->find();
        $user['message'] = $this->personalMessage();
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $user
        ]);
    }


    //预览相册
    public function previewAlbum()
    {
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $albumModel = new AlbumModel();
        $commentModel = new CommentModel();
        $album = $albumModel->relation(array('albumdetail', 'like', 'comment'))->where("id=$album_id and album_finally=1")->field('album_position,album_authority,status,album_theme,is_recycle,album_addTime', true)->find();
        $album['comment'] = $commentModel->sort($album['comment']);
        foreach ($album['albumdetail'] as &$value) {
            $value['albumdetail_img'] = C('Setting.Website_URL') . $value['albumdetail_img'];
            if ($value['describe_type'] == 2) {
                $value['albumdetail_describe'] = C('Setting.Website_URL') . $value['albumdetail_describe'];
            }
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);
    }


    //通知消息
    public function getNotifyMessage()
    {
        //查找属于用户的相册
        //查找除自己以外的所有评论 按照时间倒序排列
        $this->uid = Token::getCurrentUid();
        $this->is_user();
        $albumModel = new AlbumModel();
        $albumCommentViewModel = new AlbumCommentViewModel();
        $albumLikeViewModel = new AlbumLikeViewModel();
        $album = $albumModel->where("user_id=$this->uid and album_finally=1")->select();
        $album_ids = [];
        foreach ($album as $value) {
            array_push($album_ids, $value['id']);
        }
        //评论
        $map['user_id'] = array('neq', $this->uid);
        $map['album_id'] = array('in', $album_ids);  //用户相册
        $map['comment_read'] = 0;
        $map['is_thumb'] = 1;
        $album_comment = $albumCommentViewModel->where($map)->select();

        //点赞
        $where['user_id'] = array('neq', $this->uid);  //他人点赞
        $where['album_id'] = array('in', $album_ids);  //用户相册
        $where['like_read'] = 0;   //未读
        $where['is_thumb'] = 1;   //缩略图
        $album_like = $albumLikeViewModel->where($where)->select();

        $album = array_merge($album_comment, $album_like);
        $score = [];
        foreach ($album as $key => $value) {
            $score[$key] = $value['addTime'];
        }
        array_multisort($score, SORT_DESC, $album);
        foreach ($album as &$value) {
            if ($value['comment_id']) {
                $value['notify_type'] = 0;
            } elseif ($value['like_id']) {
                $value['notify_type'] = 1;
            }
            $value['albumdetail_img'] = C('Setting.Website_URL') . $value['albumdetail_img'];
            $value['addTime'] = date('Y-m-d H:i', $value['addTime']);
        }

        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);

    }

    //删除通知消息
    public function delNotifyMessage()
    {
        //通知id  通知状态
        $this->uid = Token::getCurrentUid();
        $this->is_user();
        (new NotifyMessage())->goCheck();
        $notify_id = $_POST['id'];
        $notify_type = $_POST['notify_type'];
        $likeModel = new LikeModel();
        $commentModel = new CommentModel();
        if ($notify_type == 0) {  //评论
            $commentModel->comment_read = 1;
            $result = $commentModel->where("id=$notify_id")->save();
        } elseif ($notify_type == 1) {  //点赞
            $likeModel->like_read = 1;
            $result = $likeModel->where("id=$notify_id")->save();
        }

        if (!$result) {
            $this->ajaxReturn((new UserException([
                'code' => 30003,
                'msg' => '删除消息失败或消息已经删除'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '删除消息成功'
        ]))->getException());

    }

    //系统消息
    public function getSystemMessage()
    {
        //根据token来获取uid
        $this->uid = Token::getCurrentUid();
        $this->is_user();
        $UsersystemModel = new UsersystemModel();
        $Usersystem = $UsersystemModel->relation('album')->where("user_id=$this->uid")->order('system_addTime desc')->select();
        foreach ($Usersystem as &$value) {
            $value['system_addTime'] = date('Y-m-d H:i', $value['system_addTime']);
        }
        if (!$Usersystem) {
            $this->ajaxReturn((new UserException([
                'code' => 30001,
                'msg' => '获取消息失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $Usersystem
        ]);
    }

    //消息设为已读
    public function setReadSystemMsg()
    {
        //根据token来获取uid
        $this->uid = Token::getCurrentUid();
        $this->is_user();
        $UsersystemModel = new UsersystemModel();
        $likeModel = new LikeModel();
        $commentModel = new CommentModel();
        $map['user_id'] = $this->uid;
        $UsersystemModel->is_read = 1;
        $userSystem = $UsersystemModel->where($map)->save();
        $likeModel->is_read = 1;
        $like = $likeModel->where($map)->save();
        $likeModel->is_read = 1;
        $comment = $commentModel->where($map)->save();
        if ($comment) {
            $this->ajaxReturn((new SuccessException([
                'msg' => '已读成功'
            ]))->getException());
        }

    }


    //删除系统消息
    public function delSystemMsg()
    {
        //根据token来获取uid
        $this->uid = Token::getCurrentUid();
        $this->is_user();
        //消息id
        (new IDMustBePostiveInt())->goCheck();
        $message_id = $_POST['id'];
        $userSystemModel = new UsersystemModel();
        $map['user_id'] = $this->uid;
        $map['id'] = $message_id;
        $userSystem = $userSystemModel->where($map)->find();
        if (!$userSystem) {
            $this->ajaxReturn((new UserException([
                'code' => 30002,
                'msg' => '消息不存在或当前系统消息与用户不匹配'
            ]))->getException());
        }
        $result = $userSystemModel->delete($message_id);
        if (!$result) {
            $this->ajaxReturn((new UserException([
                'code' => 30003,
                'msg' => '删除消息失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '删除消息成功'
        ]))->getException());

    }


    //个人中心消息
    public function personalMessage()
    {
        //获取当前用户的未读 通知消息和系统消息  显示最后一个用户点赞或评论的头像
        $likeModel = new LikeModel();
        $albumModel = new AlbumModel();
        $commentModel = new CommentModel();
        $UsersystemModel = new UsersystemModel();

        $album = $albumModel->where("user_id=$this->uid and album_finally=1")->select();
        $album_ids = [];
        foreach ($album as $value) {
            array_push($album_ids, $value['id']);
        }
        if ($album_ids) {
            $map['album_id'] = array('in', $album_ids);  //用户相册
            $map['is_read'] = 0;
            $likeCount = $likeModel->where($map)->count();
            $commentCount = $commentModel->where($map)->count();
            $userSystemCount = $UsersystemModel->where($map)->count();
            $like = $likeModel->where($map)->relation('user')->order("like_addTime desc")->find();
            $comment = $commentModel->where($map)->relation('user')->order("comment_addTime desc")->find();
            $result = $like['like_addTime'] > $comment['comment_addTime'] ? $like : $comment;
            $messageCount = $likeCount + $commentCount + $userSystemCount;
            $arr = [
                'messageCount' => $messageCount,
                'avatarUrl' => $result['avatarUrl']
            ];
        } else {
            $arr = 0;
        }

        return $arr;
    }


    //微信分享朋友圈 客服消息
    public function checkSignature()
    {

        if (isset($_GET['echostr'])) {  //当有值时 校验服务器地址URL
            $this->valid();
        } else {   //响应消息
            $this->responseMsg($_GET);
        }

    }

    //校验服务器地址URL
    public function valid()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'sang123456';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            echo $_GET['echostr'];
        } else {
            return false;
        }
    }


    //响应消息
    public function responseMsg($get)
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr) && is_string($postStr)) {

            $postArr = json_decode($postStr, true);
            $this->printLog($postArr);
            if (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'text') {   //文本消息
                $robot = $this->jiqiren2($postArr['Content']);
                $this->printLog($robot);

            } elseif (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'miniprogrampage') {
                //获取access_token
                $access_token = $this->getAccessToken();

                //openid
                $openID = $get['openid'];

                //发送消息
                $messageUrl = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $access_token;
                $ThumbUrl = $postArr['ThumbUrl'];
                $URL = "www.baidu.com";
                $messageData = <<<EOF
                    {
                    
                        "touser": "$openID",
                        "msgtype": "link",
                        "link": {
                              "title": "点此进入，即可分享朋友圈",
                              "description": "打开页面，点击右上角的【...】，分享到朋友圈",
                              "url": "$URL",
                              "thumb_url": "$ThumbUrl"
                        }
                    }
EOF;
//                $this->printLog($messageData);

                $sendCustomerMessage = $this->request_url($messageUrl, $messageData);
//                $this->printLog($sendCustomerMessage);

            }
        }

    }


    //相册音乐
    public function albumMusic(){
        $albumMusicModel = new MusicModel();
        $albumMusic = $albumMusicModel->select();
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $albumMusic
        ]);
    }



}
