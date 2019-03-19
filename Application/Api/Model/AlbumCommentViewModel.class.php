<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-9
 * Time: 上午9:50
 */

namespace Api\Model;


use Think\Model\ViewModel;

class AlbumCommentViewModel extends ViewModel
{


    public $viewFields = array(
//        'album'=>"_table",
        'album'=>array('id','album_title'),
        'comment'=>array('id'=>'comment_id','user_id','parent_id','album_id','comment_content','comment_read','comment_addTime'=>'addTime', '_on'=>'album.id=comment.album_id'),
        'user'=>array('nickName','avatarUrl','_on'=>'comment.user_id=user.id'),
        'albumdetail'=>array('albumdetail_img','is_thumb', '_on'=>'comment.album_id=albumdetail.album_id'),
    );

}
