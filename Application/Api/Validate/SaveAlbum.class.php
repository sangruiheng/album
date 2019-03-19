<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-8
 * Time: 下午12:22
 */

namespace Api\Validate;


class SaveAlbum extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
        'album_title' => 'require',
        'album_theme' => 'require|isPositiveInteger',
        'detail_id' => 'require|isPositiveInteger',
    ];

    protected $message = [
        'id.require' => "id必须存在",
        'id.isPositiveInteger' => "id必须是正整数",
        'album_title.require' => "album_title必须存在",
        'album_theme.require' => "album_theme必须存在",
        'album_theme.isPositiveInteger' => "album_theme必须是正整数",
        'detail_id.require' => "detail_id必须存在",
        'detail_id.isPositiveInteger' => "detail_id必须是正整数",
    ];

}