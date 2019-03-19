<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午10:17
 */

namespace Api\Exception;


/**
 * Class BaseException
 * 自定义异常类的基类
 */
class BaseException
{
    public $code = 400;
    public $msg = 'invalid parameters';
//    public $errorCode = 999;


    /**
     * 构造函数，接收一个关联数组
     * @param array $params 关联数组只应包含code、msg和errorCode，且不应该是空值
     */
    public function __construct($params = [])
    {
        if (!is_array($params)) {
            return;
//            throw new Exception("参数必须是数组");
        }
        if (array_key_exists('code', $params)) {  //判断code是否存在于$lparams中
            $this->code = $params['code'];

        }
        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];

        }
//        if (array_key_exists('errorCode', $params)) {
//            $this->errorCode = $params['errorCode'];
//
//        }

    }


    /**
     * 返回异常通用方法
     * @return array
     */
    public function getException()
    {
        return $result = [
            'msg' => $this->msg,
            'code' => $this->code,
            'data' => [],
            'request_url' =>  $_SERVER["REQUEST_URI"]
        ];

//        echo json_encode($result,JSON_UNESCAPED_UNICODE);  die; //抛出异常
    }


}