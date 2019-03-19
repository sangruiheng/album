<?php
namespace Api\Model;

use Think\Model\RelationModel;

class GrouptypeModel extends RelationModel{

    //form表单自动验证
    protected $_validate = array(
        array('typeName','require','类型不能为空'),
    );

}