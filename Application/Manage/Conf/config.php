<?php
return array(
		//'配置项'=>'配置值'
		'TMPL_PARSE_STRING' => array(
	     		'__PUBLIC__' => '/Public/Manage', // 更改默认的__PUBLIC__ 替换规则
		),
		/*项目标题*/
		'PROJECT_TITLE' => array(
				'ICON_TITLE' => '太美相册',//icon标题
				'LOGIN_TITLE' => '太美相册',//login登录标题
		),
		/*超级管理员和超级用户ID 添加或修改权限ID 表里数据ID更改 此处也要更改  添加或修改走的都是统一方法方便页面管理 添加或修改分开 */
		'AUTH_MODULE' => array(
				'auth_user_id' => 1,//超级管理员id
				'auth_group_id' => 1,//超级群组id
				'auth_add_id' => 10,//公共添加方法
				'auth_del_id' => 11,//公共删除方法
				'auth_save_id' => 10,//公共修改方法
		),
		/* 配置生成文件目录结构 注意文件结束必须是以 "/"结尾     继承控制器  指定命名空间*/
		'GENERATE_DIR' => array(
				'generate_model_file' => '/Application/Manage/Model/',//生成model文件
				'generate_controller_file' => '/Application/Manage/Controller/',//生成controller文件路径
				'generate_view_file'=> '/Application/Manage/View/',//生成view文件路径，
				'namespace_model_name' => 'namespace Manage\Model;',//模型命名空间
				'namespace_controller_name' => 'namespace Manage\Controller;',//控制器命名空间
				'use_model_name' => 'use Think\Model\RelationModel;',//继承model文件夹名称
				'use_controller' => 'use Manage\Controller\CommonController;',//继承controller文件夹名称
		),
);
?>