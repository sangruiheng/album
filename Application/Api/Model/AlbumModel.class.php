<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-9
 * Time: 上午9:50
 */

namespace Api\Model;


use Think\Model\RelationModel;

class AlbumModel extends RelationModel
{
    protected $_link = array(
        'like' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'like',//要关联的表名
            'foreign_key' => 'album_id', //外键的字段名称
//            'as_fields' => 'groupID,imgPath',  //被关联表中的字段名：要变成的字段名
//            'relation_deep'    =>    'refundproduct',   //多表关联  关联第三个表的名称
//            'relation_deep'    =>    'express',   //多表关联  关联第三个表的名称
        ),
        'comment' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'comment',//要关联的表名
            'foreign_key' => 'album_id', //外键的字段名称
//            'as_fields' => 'groupID,imgPath',  //被关联表中的字段名：要变成的字段名
//            'relation_deep'    =>    'refundproduct',   //多表关联  关联第三个表的名称
//            'relation_deep'    =>    'express',   //多表关联  关联第三个表的名称
        ),
        'albumdetail' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'albumdetail',//要关联的表名
            'foreign_key' => 'album_id', //外键的字段名称
//            'condition' => 'is_thumb=1',  //被关联表中的字段名：要变成的字段名
//            'mapping_fields'    =>    'albumdetail_img',   //多表关联  关联第三个表的名称
//            'relation_deep'    =>    'express',   //多表关联  关联第三个表的名称
        ),
        'thumb_albumdetail' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'albumdetail',//要关联的表名
            'foreign_key' => 'album_id', //外键的字段名称
            'condition' => 'is_thumb=1',  //被关联表中的字段名：要变成的字段名
            'mapping_fields'    =>    'albumdetail_img',   //多表关联  关联第三个表的名称
//            'relation_deep'    =>    'express',   //多表关联  关联第三个表的名称
        ),
        'usersystem' => array(
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'usersystem',//要关联的表名
            'foreign_key' => 'album_id', //本表的字段名称
//            'as_fields' => 'album_system,system_addTime,is_read',  //被关联表中的字段名：要变成的字段名  可以多个
//            'condition'    =>    'album_system=0',   //多表关联  关联第三个表的名称
        ),
    );

    //当前用户是否已经点过赞
    public function is_Like($uid, $album_id){
        $likeModel = new LikeModel();
        $map['user_id'] = $uid;
        $map['album_id'] = $album_id;
        $like = $likeModel->where($map)->find();
        if($like){
            return true;
        }else{
            return false;
        }
    }

}
