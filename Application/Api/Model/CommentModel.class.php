<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-9
 * Time: 上午9:50
 */

namespace Api\Model;


use Think\Model\RelationModel;

class CommentModel extends RelationModel
{
    protected $_link = array(
        'user' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'user',//要关联的表名
            'foreign_key' => 'user_id', //本表的字段名称
            'as_fields' => 'nickName:nickName,tel:tel,avatarUrl:avatarUrl',  //被关联表中的字段名：要变成的字段名  可以多个
//            'relation_deep'    =>    'grouptype',   //多表关联  关联第三个表的名称
        )
    );



    //获取评论
    public function sort($data, $pid = 0, $level = 0)
    {
        $userModel = M('user');
        $commentModel = M('comment');
        static $arr = array();
        foreach ($data as &$v) {
            if ($v['parent_id'] == $pid) {
                $user = $userModel->where("id=".$v['user_id'])->find();
                $v['uid'] = $user['id'];
                $v['nickName'] = $user['nickName'];
                if($v['parent_id'] != 0){
                    $pComment = $commentModel->where("id=".$v['parent_id'])->find();
                    $pUser = $userModel->where("id=".$pComment['user_id'])->find();
                    $v['pUser_nickName'] = $pUser['nickName'];
                }
                $v['level'] = $level;
                $arr[] = $v;
                $this->sort($data, $v['id'], $level + 1);
            }
        }
        return $arr;
    }



    /**
     * @param $data array  数据
     * @param $parent  string 父级元素的名称 如 parent_id
     * @param $son     string 子级元素的名称 如 comm_id
     * @param $pid     int    父级元素的id 实际上传递元素的主键
     * @return array   嵌套分类树
     */
    public function getSubTree($data , $parent , $son , $pid = 0) {
        $tmp = array();
        $userModel = M('user');

        foreach ($data as $key => $value) {
            if($value[$parent] == $pid) {
                $user = $userModel->where("id=".$value['user_id'])->find();
                $value['uid'] = $user['id'];
                $value['nickName'] = $user['nickName'];
                $value['child'] =  $this->getSubTree($data , $parent , $son , $value[$son]);
                $tmp[] = $value;
            }
        }
        return $tmp;
    }


}
