<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-9
 * Time: 上午11:19
 */

namespace Api\Exception;

//参数异常错误
class AlbumException extends BaseException
{
    public $code = 20000;

    public $msg = "相册异常";

//    public $errorCode = 10000;
}