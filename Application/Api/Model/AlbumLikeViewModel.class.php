<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-9
 * Time: 上午9:50
 */

namespace Api\Model;


use Think\Model\ViewModel;

class AlbumLikeViewModel extends ViewModel
{


    public $viewFields = array(
//        'album'=>"_table",
        'album'=>array('id','album_title'),
        'like'=>array('_as'=>'myLike','id'=>'like_id','user_id','album_id','like_read','like_addTime'=>'addTime', '_on'=>'album.id=myLike.album_id'),
        'user'=>array('nickName','avatarUrl','_on'=>'myLike.user_id=user.id'),
        'albumdetail'=>array('albumdetail_img','is_thumb', '_on'=>'myLike.album_id=albumdetail.album_id'),
    );

}
