<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午12:22
 */

namespace Api\Validate;


class DescriptionAlbum extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
        'albumdetail_describe' => 'require',
    ];

    protected $message = [
        'id.require' => "id必须存在",
        'id.isPositiveInteger' => "id必须是正整数",
        'notify_type.require' => "albumdetail_describe必须存在",
    ];

}