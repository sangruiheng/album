<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午12:22
 */

namespace Api\Validate;


class CheckAlbum extends BaseValidate
{
    protected $rule = [
        'uid' => 'isPositiveInteger|require',
        'album_id' => 'isPositiveInteger|require',
        'parent_id' => 'require',
        'comment_content' => 'require',
    ];

    protected $message = [
        'uid.isPositiveInteger' => "id必须是正整数",
        'uid.require' => "id必须存在",
        'album_id.isPositiveInteger' => "album_id必须是正整数",
        'album_id.require' => "album_id必须存在",
//        'parent_id.isPositiveInteger' => "parent_id必须是正整数",
        'parent_id.require' => "parent_id必须存在",
        'comment_content.require' => "comment_content必须存在",
    ];

}