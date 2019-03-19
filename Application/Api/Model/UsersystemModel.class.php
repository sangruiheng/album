<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-9
 * Time: 上午9:50
 */

namespace Api\Model;


use Think\Model\RelationModel;

class UsersystemModel extends RelationModel
{
    protected $_link = array(
        'album' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'album',//要关联的表名
            'foreign_key' => 'album_id', //本表的字段名称
            'as_fields' => 'album_title',  //被关联表中的字段名：要变成的字段名  可以多个
//            'relation_deep'    =>    'grouptype',   //多表关联  关联第三个表的名称
        )
    );

}
