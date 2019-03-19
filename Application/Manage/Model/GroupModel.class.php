<?php
namespace Manage\Model;
use Think\Model\RelationModel;
class GroupModel extends RelationModel{
	//form表单自动验证
	protected $_validate = array(
			array('title','require','群组名称不能为空'),
			array('rules','require','请选择规则'),
	);
	protected $_auto = array (
			array('addTime', 'time', 3, 'function'),
	);
}
?>