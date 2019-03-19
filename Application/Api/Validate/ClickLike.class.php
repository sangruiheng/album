<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午12:22
 */

namespace Api\Validate;


class ClickLike extends BaseValidate
{
    protected $rule = [
        'album_id' => 'isPositiveInteger|require',
    ];

    protected $message = [
        'album_id.isPositiveInteger' => "album_id必须是正整数",
        'album_id.require' => "album_id必须存在",
    ];

}