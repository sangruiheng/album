<?php 
namespace Manage\Model;
use Think\Model\RelationModel;
class AdminuserModel extends RelationModel{
	protected $_link = array(
		'groupName' => array(    
		'mapping_type'  => self::BELONGS_TO,    
		'class_name'    => 'Group',    
		'foreign_key'   => 'groupID', 
		'as_fields'  => 'title:groupName',  
		)
	);
	//form表单自动验证
	protected $_validate = array(
			//-1,账号长度不合法！
			array('username','require','用户名不能为空！',self::EXISTS_VALIDATE),
			array('username','/^[A-Za-z0-9]+$/','用户名不能有中文字符！',self::EXISTS_VALIDATE),
			//-1,账号长度不合法！
	        array('username','/^[^@]{5,20}$/i','用户名长度不合法！',self::EXISTS_VALIDATE),
			//-4,账号被占用
			array('username', '', '账号被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
			//-2,密码长度不合法！,新增模式
	        array('password','6,30','密码长度不合法！',self::EXISTS_VALIDATE,'length', self::MODEL_BOTH),
			array('reName','require','真实姓名不能为空'),
			array('reName','/^[\x7f-\xff]+$/','真实姓名只能是中文！',self::EXISTS_VALIDATE),
			//-6,手机号不合法！
	        array('phone','/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/','手机号不合法！',self::EXISTS_VALIDATE),
			//-3,邮箱格式不正确
	        array('email','email','邮箱格式不正确',self::EXISTS_VALIDATE),
			
			//-7,手机号被占用
			array('phone','','手机号被占用',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH),
			//-5,邮箱被占用
			array('email','','邮箱被占用',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH),
			array('groupID','require','请选择用户群组！'),
	);
	protected $_auto = array ( 
       		array('password','md5',1,'function') , // 对password字段在新增和编辑的时候使md5函数处理
			array("password","buildPass",2,"callback"),
			array('addTime', 'time', self::MODEL_INSERT, 'function'),
			array('saveTime', 'time', self::MODEL_UPDATE, 'function'),
    );
	public function buildPass($passWord) {
		return !empty($passWord) ? md5($passWord) : false;
	}
	
}
?>