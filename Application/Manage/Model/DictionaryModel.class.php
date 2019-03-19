<?php 
namespace Manage\Model;
use Think\Model\RelationModel;
class DictionaryModel extends RelationModel{
    protected $_link = array(
		'Dictionary' => array(    
			'mapping_type'  => self::BELONGS_TO,    
			'parent_key' => 'typeID',
			'as_fields'  => 'typeName',  
		),
    );
    //form表单自动验证
    protected $_validate = array(
    	array('typeName','require','请输入字典类型名称'),
    	array('dataName','require','请输入字典类型名称'),
    );
}
?>