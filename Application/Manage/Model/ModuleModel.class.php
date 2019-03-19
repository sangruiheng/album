<?php 
namespace Manage\Model;
use Think\Model\RelationModel;
class ModuleModel extends RelationModel{
    protected $_link = array(
		
	);

    //form表单自动验证
    protected $_validate = array(
    		array('moduleName','require','模块名称不能为空'),
			array('moduleLink','require','模块链接不能为空'),
			array('moduleIcon','require','模块icon不能为空'),
			array('parent_id','require','请选择主模块'),
    );
}
?>