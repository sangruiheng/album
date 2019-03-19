<?php
namespace Manage\Model;
use Think\Model\RelationModel;
class CommentModel extends RelationModel{
	protected $_link = array(
//        'album' => array(
//            'mapping_type' => self::HAS_MANY,
//            'class_name' => 'album',//要关联的表名
//            'foreign_key' => 'user_id', //本表的字段名称
////            'as_fields' => 'img:img',  //被关联表中的字段名：要变成的字段名
////            'relation_deep'    =>    'attributeValue',   //多表关联  关联第三个模型的名称
//        ),
        'user' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'user',//要关联的表名
            'foreign_key' => 'user_id', //本表的字段名称
            'as_fields' => 'nickName:nickName',  //被关联表中的字段名：要变成的字段名
        ),
        'album' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'album',//要关联的表名
            'foreign_key' => 'album_id', //本表的字段名称
//            'as_fields' => 'nickName:nickName',  //被关联表中的字段名：要变成的字段名
        )
	);

}