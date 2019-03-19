<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午10:17
 */

namespace Api\Exception;


class TokenException extends BaseException
{
    public $code = 80000;

    public $msg = "Token已过期或无效！";

//    public $errorCode = 80000;
}