<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-9
 * Time: 上午11:19
 */

namespace Api\Exception;

//参数异常错误
class ParameterException extends BaseException
{
    public $code = 10000;

    public $msg = "参数错误";

//    public $errorCode = 10000;
}