<?php 
namespace Manage\Model; 
use Think\Model\RelationModel; 
class PictureModel extends RelationModel{ 
    /*关联模型*/ 
    protected $_link = array( 
            
    );
    /*form表单验证*/ 
    protected $_validate = array(
            /*字段名：path*/
            array('path','require','请输入path'),
            /*字段名：status*/
            array('status','require','请输入status'),
            /*字段名：addTime*/
            array('addTime','require','请输入addTime'),
    );
    /*表单自动验证auto*/
    protected $_auto = array ( 
            array('addTime', 'time', self::MODEL_INSERT, 'function'),
            array('saveTime', 'time', self::MODEL_UPDATE, 'function'),
    );
} 
