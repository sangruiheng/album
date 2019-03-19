<?php 
namespace Manage\Model;
use Think\Model\RelationModel;
class CategoryModel extends RelationModel{
	//form表单自动验证
	protected $_validate = array(
			array('title','require','分类名称不能为空'),
			array('title','checkLength','名称长度仅能为2~4之间！', self::EXISTS_VALIDATE,'callback',self::MODEL_BOTH),
			array('title','','该分类名称已占用',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH),
			array('parentID','require','请选择主分类'),
			array('pictureID','require','请上传icon'),
			array('cateType','require','请选择分类'),
	);
	protected $_auto = array ( 
        	array('addTime', 'time', self::MODEL_INSERT, 'function'),
			array('saveTime', 'time', self::MODEL_UPDATE, 'function'),
    );
	
	public function checkLength($name){
		$len = mb_strlen($name,'utf8');
		if($len >= 2 && $len <= 4)return true;
		return false;
	}
}
?>
