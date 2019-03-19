<?php
namespace Manage\Model;
use Think\Model\RelationModel;
class SensitiveModel extends RelationModel{
	protected $_link = array(

	);

    protected $_validate = array(
        array('sensitive_title','require','敏感词不能为空'),
    );
}