<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午12:19
 */

namespace Api\validate;


use Api\Exception\ParameterException;
use Api\Exception\UserException;
use Think\Request;
use Think\Validate;

/**
 * Class BaseValidate
 * 验证类的基类
 */
class BaseValidate extends Validate
{
    /**
     * 检测所有客户端发来的参数是否符合验证类规则
     * 基类定义了很多自定义验证方法
     * 这些自定义验证方法其实，也可以直接调用
     * @return
     */
    public function goCheck()
    {
        $params = I('param.');
        $result = $this->batch()->check($params);
        if (!$result) {  //验证不通过 抛出异常


//            return $returnData = (new ParameterException([
//                'msg' => $this->getError()
//            ]))->getException();

            $result = $returnData = (new ParameterException([
                'msg' => $this->getError()
            ]))->getException();

            echo json_encode($result,JSON_UNESCAPED_UNICODE);  die; //抛出异常


        } else {
            return true;
        }

    }


    public function goChecks($params)
    {
        $result = $this->batch()->check($params);
        if (!$result) {  //验证不通过 抛出异常

            $result = $returnData = (new ParameterException([
                'msg' => $this->getError()
            ]))->getException();

            echo json_encode($result,JSON_UNESCAPED_UNICODE);  die; //抛出异常


        } else {
            return true;
        }

    }





    /**
     * $date  控制器的发送的date数据
     * $field 当前做校验的参数字段
     * $rule
     * $value 验证的值
     */
    public static function isPositiveInteger($value, $rule = '', $date = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
        }

    }


//    public static function isNotEmpty($value, $rule = '', $date = '', $field = '')
//    {
//        if (empty($value)) {
//            return false;
//        } else {
//            return true;
//        }
//    }


    //不推荐使用正则，因为复用性太差
    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public static function isArray($value){
        if(is_array($value)){
            return true;
        }else{
            return false;
        }
    }
}