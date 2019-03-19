<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 上午11:58
 */

namespace Api\Controller;


use Api\Exception\AlbumException;
use Api\Exception\SuccessException;
use Api\Exception\UserException;
use Api\Model\AlbumdetailModel;
use Api\Model\AlbumModel;
use Api\Model\CommentModel;
use Api\Model\HelpModel;
use Api\Model\LikeModel;
use Api\Model\SeealbumModel;
use Api\Model\SensitiveModel;
use Api\Model\UserModel;
use Api\Service\Token;
use Api\Validate\AuthorityAlbum;
use Api\Validate\CheckAlbum;
use Api\Validate\ClickLike;
use Api\Validate\DescriptionAlbum;
use Api\Validate\IDMustBePostiveInt;
use Api\Validate\SaveAlbum;


class AlbumController extends CommonController
{
    protected $uid;

    function __construct()
    {
        //根据token来获取uid
        $this->uid = Token::getCurrentUid();
        $this->is_user();
    }

    //创建相册
    public function createAlbum()
    {
        $albumModel = new AlbumModel();
        $map['user_id'] = $this->uid;
        $map['album_finally'] = 0;
        $unSavedAlbum = M('album')->where($map)->order('id desc')->find();
        //先保存至草稿箱 相册1  相册2
        if (!$unSavedAlbum) {
            $albumModel->album_title = '相册1';
        }
        if ($unSavedAlbum) {
            $album_title = substr($unSavedAlbum['album_title'], -1) + 1;
            $albumModel->album_title = '相册' . $album_title;
        }
        $albumModel->user_id = $this->uid;
        $albumModel->album_theme = 1;   //默认第一个主题
        $albumModel->album_addTime = time();
        $album = $albumModel->add();
        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20014,
                'msg' => '创建相册失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'album_id' => $album
        ]);
    }

    //保存相册内容
    public function saveAlbum()
    {
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $albumDetailModel = new AlbumdetailModel();
        if (!$_FILES) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20015,
                'msg' => '上传图片为空'
            ]))->getException());
        }
        $info = $this->uploadCommon();
        foreach ($info as $file) {
            //$data['user_img'] = $file['img_path'];
            $data['albumdetail_img'] = $file['savepath'] . $file['savename'];
            $data['album_id'] = $album_id;
            $result = $albumDetailModel->add($data);
        }
        //设置第一张为封面图
        $albumDetailModel->is_thumb = 0;
        $albumDetailModel->where("id=$album_id")->save();
        $map['album_id'] = $album_id;
        $firstAlbum = $albumDetailModel->where($map)->order('id asc')->find();
        $albumDetailModel->is_thumb = 1;
        $albumDetailModel->where("id=" . $firstAlbum['id'])->save();
        if (!$result) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20016,
                'msg' => '上传图片失败'
            ]))->getException());
        }
        $albumDetail = $albumDetailModel->where("album_id=$album_id")->select();
        if (!$albumDetail) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20017,
                'msg' => '获取相册内容失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $albumDetail
        ]);

    }


    //获取相册内容
    public function getAlbumDetail()
    {
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $albumDetailModel = new AlbumdetailModel();
        $albumDetail = $albumDetailModel->where("album_id=$album_id")->select();
        foreach ($albumDetail as &$value) {
            $value['albumdetail_img'] = C('Setting.Website_URL') . $value['albumdetail_img'];
        }
        if (!$albumDetail) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20017,
                'msg' => '获取相册内容失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $albumDetail
        ]);
    }

//    //添加语音或文字
    public function createDescriptionAlbum()
    {
        //内容id 内容
        $albumDetail_id = $_POST['id'];
        $albumdetail_describe = $_POST['albumdetail_describe'];
        $voice_time = $_POST['voice_time'];
        $describe_type = $_POST['describe_type'];
        (new IDMustBePostiveInt())->goCheck();
        $albumDetailModel = new AlbumdetailModel();
        if ($describe_type == 1) {  //文字
            $albumDetailModel = new AlbumdetailModel();
            $albumDetailModel->albumdetail_describe = $albumdetail_describe;
            $albumDetailModel->describe_type = 1;
            $albumDetail = $albumDetailModel->where("id=$albumDetail_id")->save();
        } elseif ($describe_type == 2) {  //语音
            if (!$_FILES) {
                $this->ajaxReturn((new AlbumException([
                    'code' => 20015,
                    'msg' => '上传语音为空'
                ]))->getException());
            }
            $info = $this->uploadCommon();
            foreach ($info as $file) {
                $albumDetailModel->albumdetail_describe = $file['savepath'] . $file['savename'];
                $albumDetailModel->voice_time = $voice_time;
                $albumDetailModel->describe_type = 2;
                $albumDetail = $albumDetailModel->where("id=$albumDetail_id")->save();
            }

        }
        $result = $albumDetailModel->where("id=$albumDetail_id")->find();
        if (!$albumDetail) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20018,
                'msg' => '添加内容失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => '添加内容成功',
            'album_id' => $result['album_id']
        ]);
    }


    //保存前显示相册图片
    public function beforeSaveAlbum()
    {
        //相册id
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $albumDetailModel = new AlbumdetailModel();
        $albumDetail = $albumDetailModel->where("album_id=$album_id")->field('id,albumdetail_img,albumdetail_describe,describe_type,voice_time')->select();
        foreach ($albumDetail as &$value) {
            $value['albumdetail_img'] = C('Setting.Website_URL') . $value['albumdetail_img'];
            if ($value['describe_type'] == 2) {
                $value['albumdetail_describe'] = C('Setting.Website_URL') . $value['albumdetail_describe'];
            }
        }
        if (!$albumDetail) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20019,
                'msg' => '获取相册图片失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $albumDetail
        ]);

    }


    //保存相册草稿信息
    public function saveAlbumDrafts()
    {
        //相册id 相册名称 相册封面 相册图片 位置 权限 主题
        $album_id = $_POST['id'];
        $album_title = $_POST['album_title'];
        $album_position = $_POST['album_position'];
        $album_authority = $_POST['album_authority'];
        $album_theme = $_POST['album_theme'];
        $albumDetail_id = $_POST['detail_id'];   //设为封面的id
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $albumModel->album_title = $album_title;
        $albumModel->album_position = $album_position;
        $albumModel->album_authority = $album_authority;
        $albumModel->album_theme = $album_theme;
//        $albumModel->album_finally = 1;
        $album = $albumModel->where("id=$album_id")->save();
        $albumDetailModel->is_thumb = 1;
        $albumDetail = $albumDetailModel->where("id=$albumDetail_id")->save();
        if (!$album || !$albumDetail) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20020,
                'msg' => '保存失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '保存成功'
        ]))->getException());
    }


    //最终保存相册内容
    public function saveFinallyAlbum()
    {
        //相册id 相册名称 相册封面 相册图片 位置 权限 主题
        (new SaveAlbum())->goCheck();
        $album_id = $_POST['id'];
        $album_title = $_POST['album_title'];
        $album_position = $_POST['album_position'];
        $album_authority = $_POST['album_authority'];
        $album_theme = $_POST['album_theme'];
        $albumDetail_id = $_POST['detail_id'];   //设为封面的id
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $albumModel->album_title = $album_title;
        $albumModel->album_position = $album_position;
        $albumModel->album_authority = $album_authority;
        $albumModel->album_theme = $album_theme;
        $albumModel->album_finally = 1;
        if ($_POST['album_sensitive']) {
            $albumModel->album_sensitive = $_POST['album_sensitive'];
        }
        $album = $albumModel->where("id=$album_id")->save();
        //全部设置为没有头图
        $albumDetailModel1 = M('albumdetail');
        $albumDetailModel1->is_thumb = 0;
        $albumDetailModel1->where("album_id=$album_id")->save();
        //设置头图
        $albumDetailModel->is_thumb = 1;
        $albumDetail = $albumDetailModel->where("id=$albumDetail_id")->save();
        //删除回收站多余相册
        $this->deleteSurplusAlbum();
        if (!$album || !$albumDetail) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20020,
                'msg' => '保存失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '保存成功'
        ]))->getException());
    }


    //删除回收站多余相册
    public function deleteSurplusAlbum()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $map['user_id'] = $this->uid;
        $map['album_finally'] = 0;
        $draftAlbumCount = $albumModel->where($map)->count();
        //草稿箱大于10条
        if ($draftAlbumCount > 10) {
            $draftAlbum = $albumModel->where($map)->order('id desc')->limit('0,10')->field('id')->select();
            $draft_id = $draftAlbum[9]['id'];
            //获取多余的相册
            $surplusAlbum = $albumModel->where("user_id=$this->uid and album_finally=0 and id<$draft_id")->select();
            $surplusAlbum_ids = [];
            foreach ($surplusAlbum as $value) {
                array_push($surplusAlbum_ids, $value['id']);
            }
            //获取多余的相册详情
            $where['album_id'] = array('in', $surplusAlbum_ids);
            $surplusAlbumDetail = $albumDetailModel->where($where)->select();
            $surplusAlbumDetail_img = [];
            foreach ($surplusAlbumDetail as $value) {
                array_push($surplusAlbumDetail_img, $value['albumdetail_img']);
            }
            //删除相册图片
            if ($surplusAlbumDetail_img) {
                for ($i = 0; $i < count($surplusAlbumDetail_img); $i++) {
                    $file = ('Uploads/Manage/' . $surplusAlbumDetail_img[$i]);
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                }
            }
            //删除相册详情
            $albumDetailModel->where($where)->delete();
            //删除相册
            $where1['id'] = array('in', $surplusAlbum_ids);
            $albumModel->where($where1)->delete();
        }
    }


    //评论相册
    public function commentAlbum()
    {
        //uid 相册id 评论id（没有传0） 评论内容
        (new CheckAlbum())->goCheck();
        $album_id = $_POST['album_id'];
        $parent_id = $_POST['parent_id'];
        $comment_content = $_POST['comment_content'];
        $commentModel = new CommentModel();
        $commentModel->user_id = $this->uid;
        $commentModel->album_id = $album_id;
        $commentModel->parent_id = $parent_id;
        $commentModel->comment_content = $comment_content;
        $commentModel->comment_addTime = time();
        $comment = $commentModel->add();
        if (!$comment) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20001,
                'msg' => '评论相册失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '评论成功'
        ]))->getException());
    }


    //获取评论
    public function getAlbumComment()
    {
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $commentModel = new CommentModel();
        $map['album_id'] = $album_id;
        $comment = $commentModel->where($map)->select();
        $alubmComment = $commentModel->sort($comment);
        if (!$alubmComment) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20002,
                'msg' => '获取评论内容失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $alubmComment
        ]);
    }


    //获取点赞用户
    public function getAlbumLike()
    {
        $album_id = $_POST['id'];
        $likeModel = new LikeModel();
        (new IDMustBePostiveInt())->goCheck();
        $map['album_id'] = $album_id;
        $like = $likeModel->relation('user')->field('like_addTime,album_id', true)->where($map)->select();
        if (!$like) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20003,
                'msg' => '获取点赞失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $like
        ]);
    }


    //获取用户相册 按照主题
    public function getAlbumTheme()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $map['user_id'] = $this->uid;
        $map['is_recycle'] = 0;
//        $map['album_theme'] = array('neq',0);
        $map['album_finally'] = 1;
        $album = $albumModel->relation(array('like', 'comment'))->field('album_title,id,album_authority')->where($map)->select();

        $score = [];
        foreach ($album as $key => $value) {
            $score[$key] = $value['album_theme'];
        }
        array_multisort($score, SORT_ASC, $album);
        foreach ($album as &$value) {
            $value['album_title'] = strip_tags($this->subtext($value['album_title'], 12));
            $value['like'] = count($value['like']);
            $value['comment'] = count($value['comment']);
            $albumDetail = $albumDetailModel->where("album_id=" . $value['id'] . " and is_thumb=1")->find();
            $value['album_img'] = C('Setting.Website_URL') . $albumDetail['albumdetail_img'];
            //判断当前用户是否已经点过赞
            if ((new AlbumModel())->is_Like($this->uid, $value['id'])) {
                $value['is_like'] = 1;  //点过赞
            } else {
                $value['is_like'] = 0;  //没点过
            }
        }
        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20004,
                'msg' => '主题相册获取失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);
    }

    //获取用户相册 根据时间倒序
    public function getAlbumTime()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $map['user_id'] = $this->uid;
        $map['is_recycle'] = 0;
//        $map['album_theme'] = array('neq',0);
        $map['album_finally'] = 1;
        $album = $albumModel->relation(array('like', 'comment'))->field('album_title,id,album_authority')->where($map)->order('album_addTime desc')->select();
        foreach ($album as &$value) {
            $value['album_title'] = strip_tags($this->subtext($value['album_title'], 12));
            $value['like'] = count($value['like']);
            $value['comment'] = count($value['comment']);
            $albumDetail = $albumDetailModel->where("album_id=" . $value['id'] . " and is_thumb=1")->find();
            $value['album_img'] = C('Setting.Website_URL') . $albumDetail['albumdetail_img'];
            //判断当前用户是否已经点过赞
            if ((new AlbumModel())->is_Like($this->uid, $value['id'])) {
                $value['is_like'] = 1;  //点过赞
            } else {
                $value['is_like'] = 0;  //没点过
            }
        }

        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20004,
                'msg' => '获取时间相册失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);
    }


    //删除到回收站
    public function recycleBin()
    {
        //uid   相册id
        $albumModel = new AlbumModel();
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $albumModel->is_recycle = 1;
        $album = $albumModel->where("id=$album_id and user_id=$this->uid")->save();
        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20005,
                'msg' => '删除到回收站失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '加入回收站成功'
        ]))->getException());
    }


    //回收站恢复相册
    public function recoverAlbum()
    {
        //uid   相册id
        $albumModel = new AlbumModel();
        $album_id = $_POST['id'];
        $albumModel->is_recycle = 0;
        $album = $albumModel->where("id=$album_id and user_id=$this->uid")->save();
        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20007,
                'msg' => '恢复失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '恢复成功'
        ]))->getException());
    }

    //设置相册权限
    public function setPermission()
    {
        //uid  相册id  权限 0 1
        (new AuthorityAlbum())->goCheck();
        $albumModel = new AlbumModel();
        $album_id = $_POST['album_id'];
        $album_authority = $_POST['album_authority'];
        $albumModel->album_authority = $album_authority;
        $album = $albumModel->where("id=$album_id and user_id=$this->uid and is_recycle=0")->save();
        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20006,
                'msg' => '设置权限失败,或相册已在回收站'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '设置权限成功'
        ]))->getException());
    }

    //删除相册
    public function deleteAlbum()
    {
        //uid   相册id
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $album = $albumModel->where("id=$album_id and user_id=$this->uid and is_recycle=1")->select();
        if ($album) {
            $albumDetail = $albumDetailModel->where("album_id=$album_id")->select();
            if ($albumDetail) {
                for ($i = 0; $i < count($albumDetail); $i++) {
                    $file = ('Uploads/Manage/' . $albumDetail[$i]['albumdetail_img']);
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                    $albumDetailModel->where("id=" . $albumDetail[$i]['id'])->delete();
                }
            }
            $albumModel->where("id=$album_id and user_id=$this->uid and is_recycle=1")->delete();
        }
        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20008,
                'msg' => '删除相册失败,或相册不在回收站'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '删除相册成功'
        ]))->getException());
    }

    //清空回收站
    public function clearRecycleBin()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
//        $album = $albumModel->where("user_id=$this->uid and is_recycle=1")->delete();
        $album = $albumModel->where("user_id=$this->uid and is_recycle=1")->select();
        if ($album) {
            for ($i = 0; $i < count($album); $i++) {
                $albumDetail = $albumDetailModel->where("album_id=" . $album[$i]['id'])->select();
                if ($albumDetail) {
                    for ($j = 0; $j < count($albumDetail); $j++) {
                        $file = ('Uploads/Manage/' . $albumDetail[$j]['albumdetail_img']);
                        if (file_exists($file)) {
                            @unlink($file);
                        }
                        $albumDetailModel->where("id=" . $albumDetail[$j]['id'])->delete();
                    }
                }
            }
            $album = $albumModel->where("user_id=$this->uid and is_recycle=1")->delete();
        }

        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20009,
                'msg' => '清空回收站失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '清空回收站成功'
        ]))->getException());
    }


    //查看回收站 根据主题排序
    public function getThemeRecycleBin()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $map['user_id'] = $this->uid;
        $map['is_recycle'] = 1;
        $album = $albumModel->field('album_title,id')->where($map)->select();

        $score = [];
        foreach ($album as $key => $value) {
            $score[$key] = $value['album_theme'];
        }
        array_multisort($score, SORT_ASC, $album);
        foreach ($album as &$value) {
            $value['album_title'] = strip_tags($this->subtext($value['album_title'], 12));
            $albumDetail = $albumDetailModel->where("album_id=" . $value['id'] . " and is_thumb=1")->find();
            $value['album_img'] = C('Setting.Website_URL') . $albumDetail['albumdetail_img'];
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);
    }


    //草稿箱继续制作
    public function continueMake()
    {
        //查找相册的所有图片
        //确定用户进行到了哪一步  albumdetail_describe为空
        // 如果没有为空则已经制作完成到最后保存页面
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $albumDetail = $albumDetailModel->where("albumdetail_describe is null and album_id=$album_id")->select();
        if ($albumDetail) {   //则用户进行到制作相册步骤  返回当前图片
            foreach ($albumDetail as &$value) {
                $value['albumdetail_img'] = C('Setting.Website_URL') . $value['albumdetail_img'];
            }
            $data = $albumDetail;
        } else {   //进行到最终保存页面 读取最终保存页面数据
            $album = $albumModel->relation('albumdetail')->where("id=$album_id")->find();
            $data = $album;
        }

        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $data
        ]);
    }


    //查看回收站 根据时间排序
    public function getTimeRecycleBin()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $map['user_id'] = $this->uid;
        $map['is_recycle'] = 1;
        $album = $albumModel->field('album_title,id')->where($map)->order('album_addTime desc')->select();
        foreach ($album as &$value) {
            $value['album_title'] = strip_tags($this->subtext($value['album_title'], 12));
            $albumDetail = $albumDetailModel->where("album_id=" . $value['id'] . " and is_thumb=1")->find();
            $value['album_img'] = C('Setting.Website_URL') . $albumDetail['albumdetail_img'];
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);
    }

    //查看草稿箱 根据主题排序
    public function getThemeDrafts()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $map['user_id'] = $this->uid;
        $map['album_finally'] = 0;
        $album = $albumModel->field('album_title,id')->where($map)->select();

        $score = [];
        foreach ($album as $key => $value) {
            $score[$key] = $value['album_theme'];
        }
        array_multisort($score, SORT_ASC, $album);
        foreach ($album as &$value) {
            $value['album_title'] = strip_tags($this->subtext($value['album_title'], 12));
            $albumDetail = $albumDetailModel->where("album_id=" . $value['id'] . " and is_thumb=1")->find();
            $value['album_img'] = C('Setting.Website_URL') . $albumDetail['albumdetail_img'];
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);
    }

    //查看草稿箱 根据时间排序
    public function getTimeDrafts()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $map['user_id'] = $this->uid;
        $map['album_finally'] = 0;
        $album = $albumModel->field('album_title,id')->where($map)->order('album_addTime desc')->select();
        foreach ($album as &$value) {
            $value['album_title'] = strip_tags($this->subtext($value['album_title'], 12));
            $albumDetail = $albumDetailModel->where("album_id=" . $value['id'] . " and is_thumb=1")->find();
            $value['album_img'] = C('Setting.Website_URL') . $albumDetail['albumdetail_img'];
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);
    }

    //删除草稿箱相册
    public function deleteDraftsAlbum()
    {
        //uid   相册id
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $album = $albumModel->where("id=$album_id and user_id=$this->uid and album_finally=0")->select();
        if ($album) {
            $albumDetail = $albumDetailModel->where("album_id=$album_id")->select();
            if ($albumDetail) {
                for ($i = 0; $i < count($albumDetail); $i++) {
                    $file = ('Uploads/Manage/' . $albumDetail[$i]['albumdetail_img']);
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                    $albumDetailModel->where("id=" . $albumDetail[$i]['id'])->delete();
                }
            }
            $albumModel->where("id=$album_id and user_id=$this->uid and album_finally=0")->delete();

        }
        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20008,
                'msg' => '删除相册失败,或相册不在草稿箱'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '删除相册成功'
        ]))->getException());
    }


    //清空草稿箱
    public function clearDrafts()
    {
        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $album = $albumModel->where("user_id=$this->uid and album_finally=0")->select();
        if ($album) {
            for ($i = 0; $i < count($album); $i++) {
                $albumDetail = $albumDetailModel->where("album_id=" . $album[$i]['id'])->select();
                if ($albumDetail) {
                    for ($j = 0; $j < count($albumDetail); $j++) {
                        $file = ('Uploads/Manage/' . $albumDetail[$j]['albumdetail_img']);
                        if (file_exists($file)) {
                            @unlink($file);
                        }
                        $albumDetailModel->where("id=" . $albumDetail[$j]['id'])->delete();
                    }
                }
            }
            $album = $albumModel->where("user_id=$this->uid and album_finally=0")->delete();
        }

        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20009,
                'msg' => '清空草稿箱失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '清空草稿箱成功'
        ]))->getException());
    }


    //浏览其他用户相册
    public function seeAlbum()
    {
        //uid 相册id
        (new IDMustBePostiveInt())->goCheck();
        $album_id = $_POST['id'];
        $seeAlbumModel = new SeealbumModel();
        $seeAlbumModel->user_id = $this->uid;
        $seeAlbumModel->album_id = $album_id;
        $seeAlbumModel->see_addTime = time();
        $seeAlbum = $seeAlbumModel->add();
        if (!$seeAlbum) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20010,
                'msg' => '浏览失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '浏览成功'
        ]))->getException());
    }

    //我看过谁的相册
    public function getSeeAlbum()
    {
        //uid
        $statr_page = ($_POST['id'] - 1) * 2;
        $page = 2;
        $seeAlbumModel = new SeealbumModel();
        $seeAlbum = $seeAlbumModel->relation(array('user', 'album'))->where("user_id=$this->uid")->order('see_addTime desc')->limit($statr_page, $page)->select();
        if (!$seeAlbum) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20011,
                'msg' => '获取失败或为空',
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $seeAlbum
        ]);
    }


    //查看其他用户主页
    public function seeOthers()
    {
        // 其他用户id
        $othersUid = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $userModel = new UserModel();
        $user = $userModel->where("id=$othersUid")->find();
        if (!$user) {
            $this->ajaxReturn((new UserException())->getException());
        }

        $albumModel = new AlbumModel();
        $albumDetailModel = new AlbumdetailModel();
        $map['user_id'] = $othersUid;
        $map['is_recycle'] = 0;
        $map['album_authority'] = 0;
        $album = $albumModel->relation(array('like', 'comment'))->field('album_title,id')->where($map)->select();
        $score = [];
        foreach ($album as $key => $value) {
            $score[$key] = $value['album_theme'];
        }
        array_multisort($score, SORT_ASC, $album);
        foreach ($album as &$value) {
            $value['album_title'] = strip_tags($this->subtext($value['album_title'], 12));
            $value['like'] = count($value['like']);
            $value['comment'] = count($value['comment']);
            $map['ablum_id'] = $value['id'];
            $map['is_thumb'] = 1;
            $albumDetail = $albumDetailModel->where($map)->find();
            $value['album_img'] = $albumDetail['albumdetail_img'];
        }
        $arr['album'] = $album;
        $arr['nickName'] = $user['nickName'];
        $arr['avatarUrl'] = $user['avatarUrl'];
        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20004,
                'msg' => '主题相册获取失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $arr
        ]);

    }

//    //预览相册
//    public function previewAlbum()
//    {
//        $album_id = $_POST['id'];
//        (new IDMustBePostiveInt())->goCheck();
//        $albumModel = new AlbumModel();
//        $commentModel = new CommentModel();
//        $album = $albumModel->relation(array('albumdetail', 'like', 'comment'))->where("id=$album_id and album_finally=1")->field('album_position,album_authority,status,album_theme,is_recycle,album_addTime', true)->find();
//        $album['comment'] = $commentModel->sort($album['comment']);
//        $this->ajaxReturn([
//            'code' => 200,
//            'msg' => 'success',
//            'data' => $album
//        ]);
//    }

    //点赞
    public function clickLike()
    {
        //uid  相册id
        $album_id = $_POST['album_id'];
        (new ClickLike())->goCheck();
        $likeModel = new LikeModel();
        $map['user_id'] = $this->uid;
        $map['album_id'] = $album_id;
        $is_like = $likeModel->where($map)->find();
        if ($is_like) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20020,
                'msg' => '已经点过赞啦'
            ]))->getException());
        }
        $likeModel->user_id = $this->uid;
        $likeModel->album_id = $album_id;
        $likeModel->like_addTime = time();
        $like = $likeModel->add();
        if (!$like) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20012,
                'msg' => '点赞失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '点赞成功'
        ]))->getException());
    }

    //取消点赞
    public function cancelLike()
    {
        //uid  相册id
        $album_id = $_POST['album_id'];
        (new ClickLike())->goCheck();
        $likeModel = new LikeModel();
        $map['user_id'] = $this->uid;
        $map['album_id'] = $album_id;
        $like = $likeModel->where($map)->delete();
        if (!$like) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20015,
                'msg' => '取消点赞失败或当前用户与相册不匹配'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '取消点赞成功'
        ]))->getException());
    }

    //评论
    public function comments()
    {
        //相册id parent_id没有传0 评论内容
        $album_id = $_POST['album_id'];
        $parent_id = $_POST['parent_id'];
        $album_comment = $_POST['album_comment'];
        $commentModel = new CommentModel();
        $commentModel->user_id = $this->uid;
        $commentModel->parent_id = $parent_id;
        $commentModel->album_id = $album_id;
        $commentModel->comment_content = $album_comment;
        $commentModel->comment_addTime = time();
        $comment = $commentModel->add();
        if (!$comment) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20013,
                'msg' => '评论失败'
            ]))->getException());
        }
        $this->ajaxReturn((new SuccessException([
            'msg' => '评论成功'
        ]))->getException());
    }

    //输入限制
    public function entryLimit()
    {
        $entryModel = M('entry');
        $entry = $entryModel->find(1);
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $entry
        ]);
    }

    //小程序分享显示页面
    public function getShareAlbum()
    {
        //相册id
        $album_id = $_POST['id'];
        (new IDMustBePostiveInt())->goCheck();
        $albumModel = new AlbumModel();
        $album = $albumModel->relation('thumb_albumdetail')->where("id=$album_id")->field('id,album_title,album_authority,album_sensitive')->find();

        $album['thumb_albumdetail'][0]['albumdetail_img'] = C('Setting.Website_URL') . $album['thumb_albumdetail'][0]['albumdetail_img'];

        if (!$album) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20014,
                'msg' => '获取相册信息失败'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $album
        ]);

    }

    //获取敏感信息
    public function getSensitive()
    {
        $sensitiveModel = new SensitiveModel();
        $Sensitive = $sensitiveModel->select();
        if (!$Sensitive) {
            $this->ajaxReturn((new AlbumException([
                'code' => 20017,
                'msg' => '敏感信息获取失败或为空'
            ]))->getException());
        }
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $Sensitive
        ]);
    }


    public function getHelpMovie()
    {
        $helpModel = M('help');
        $help = $helpModel->find();
        $help['help_movie'] = C('Setting.Website_URL') . $help['help_movie'];
        $this->ajaxReturn([
            'code' => 200,
            'msg' => 'success',
            'data' => $help
        ]);
    }


}
