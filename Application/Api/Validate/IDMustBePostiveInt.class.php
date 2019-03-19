<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午12:22
 */

namespace Api\Validate;


class IDMustBePostiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'isPositiveInteger|require',
    ];

    protected $message = [
        'id.isPositiveInteger' => "id必须是正整数",
        'id.require' => "id必须存在",
    ];

}