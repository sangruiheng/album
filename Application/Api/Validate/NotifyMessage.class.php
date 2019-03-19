<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午12:22
 */

namespace Api\Validate;


class NotifyMessage extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'notify_type' => 'require',
    ];

    protected $message = [
        'id.require' => "id必须存在",
        'notify_type.require' => "notify_type必须存在",
    ];

}