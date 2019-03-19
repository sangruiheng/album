<?php
return array(
    'URL_ROUTER_ON'   => true, //开启路由
    'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符
    'URL_ROUTE_RULES' => array( //定义路由规则
//        'api/user/:id$'    => array('Api/User/getUser',array('method'=>'get')),
//        'api/home/all$'    => array('Api/Home/getAllHome',array('method'=>'get')),
        'api/album/addComment'    => array('Api/Album/commentAlbum',array('method'=>'post')),     //评论相册
        'api/album/getComment'    => array('Api/Album/getAlbumComment',array('method'=>'post')),     //获取评论
        'api/album/getLike'    => array('Api/Album/getAlbumLike',array('method'=>'post')),     //获取点赞用户
        'api/album/getAlbumTheme'    => array('Api/Album/getAlbumTheme',array('method'=>'post')),         //获取用户相册 按照主题
        'api/album/getAlbumTime'    => array('Api/Album/getAlbumTime',array('method'=>'post')),         //获取用户相册 按照时间倒叙
        'api/album/recycleBin'    => array('Api/Album/recycleBin',array('method'=>'post')),         //删除到回收站
        'api/album/getThemeRecycleBin'    => array('Api/Album/getThemeRecycleBin',array('method'=>'post')),         //根据主题查看回收站
        'api/album/getTimeRecycleBin'    => array('Api/Album/getTimeRecycleBin',array('method'=>'post')),         //根据时间查看回收站
        'api/album/recoverAlbum'    => array('Api/Album/recoverAlbum',array('method'=>'post')),         //回收站恢复相册
        'api/album/setPermission'    => array('Api/Album/setPermission',array('method'=>'post')),         //设置权限
        'api/album/deleteAlbum'    => array('Api/Album/deleteAlbum',array('method'=>'post')),         //删除相册
        'api/album/clearRecycleBin'    => array('Api/Album/clearRecycleBin',array('method'=>'post')),         //清空回收站
        'api/album/seeAlbum'    => array('Api/Album/seeAlbum',array('method'=>'post')),         //浏览用户其他相册
        'api/album/getSeeAlbum'    => array('Api/Album/getSeeAlbum',array('method'=>'post')),         //我看过谁
        'api/album/seeOthers'    => array('Api/Album/seeOthers',array('method'=>'post')),         //查看其他用户主页
        'api/user/previewAlbum'    => array('Api/User/previewAlbum',array('method'=>'post')),         //预览相册
        'api/album/clickLike'    => array('Api/Album/clickLike',array('method'=>'post')),         //点赞
        'api/album/cancelLike'    => array('Api/Album/cancelLike',array('method'=>'post')),         //取消点赞
        'api/album/comments'    => array('Api/Album/comments',array('method'=>'post')),         //评论
        'api/album/entryLimit'    => array('Api/Album/entryLimit',array('method'=>'post')),         //获取输入限制
        'api/album/getShareAlbum'    => array('Api/Album/getShareAlbum',array('method'=>'post')),         //小程序分享显示页面
        'api/album/getSensitive'    => array('Api/Album/getSensitive',array('method'=>'post')),         //小程序分享显示页面
        'api/album/getHelpMovie'    => array('Api/Album/getHelpMovie',array('method'=>'post')),         //帮助视频

        'api/album/continueMake'    => array('Api/Album/continueMake',array('method'=>'post')),         //草稿箱 继续制作
        'api/album/getThemeDrafts'    => array('Api/Album/getThemeDrafts',array('method'=>'post')),         //查看草稿箱 根据主题排序
        'api/album/getTimeDrafts'    => array('Api/Album/getTimeDrafts',array('method'=>'post')),         //查看草稿箱 根据时间排序
        'api/album/deleteDraftsAlbum'    => array('Api/Album/deleteDraftsAlbum',array('method'=>'post')),         //删除草稿箱相册
        'api/album/clearDrafts'    => array('Api/Album/clearDrafts',array('method'=>'post')),         //清空草稿箱

        'api/album/createAlbum'    => array('Api/Album/createAlbum',array('method'=>'post')),         //创建相册
        'api/album/saveAlbum'    => array('Api/Album/saveAlbum',array('method'=>'post')),         //保存相册内容
        'api/album/getAlbumDetail'    => array('Api/Album/getAlbumDetail',array('method'=>'post')),         //获取相册内容
        'api/album/createDescriptionAlbum'    => array('Api/Album/createDescriptionAlbum',array('method'=>'post')), //相册添加文字或语音
        'api/album/beforeSaveAlbum'    => array('Api/Album/beforeSaveAlbum',array('method'=>'post')), //保存前显示相册图片
        'api/album/saveFinallyAlbum'    => array('Api/Album/saveFinallyAlbum',array('method'=>'post')), //最终保存相册


        'api/user/personalCenter'    => array('Api/User/personalCenter',array('method'=>'post')),         //个人中心
        'api/user/getSystemMessage'    => array('Api/User/getSystemMessage',array('method'=>'post')),         //获取系统消息
        'api/user/setReadSystemMsg'    => array('Api/User/setReadSystemMsg',array('method'=>'post')),         //系统消息设为已读
        'api/user/delSystemMsg'    => array('Api/User/delSystemMsg',array('method'=>'post')),         //删除系统消息
        'api/user/getNotifyMessage'    => array('Api/User/getNotifyMessage',array('method'=>'post')),         //获取通知消息
        'api/user/delNotifyMessage'    => array('Api/User/delNotifyMessage',array('method'=>'post')),         //删除通知消息
        'api/user/login'    => array('Api/User/wxLogin',array('method'=>'post')),         //登陆
        'api/user/getOpenID'    => array('Api/User/getOpenID',array('method'=>'post')),         //获取openid
        'api/user/personalMessage'    => array('Api/User/personalMessage',array('method'=>'post')),         //个人中心消息
        'api/user/albumMusic'    => array('Api/User/albumMusic',array('method'=>'post')),         //相册音乐



        'api/user/checkSignature'    => array('Api/user/checkSignature',array('method'=>'get')),         //接收微信消息



    ),
//    'TMPL_EXCEPTION_FILE' => APP_PATH.'/Public/exception.tpl',
    'DEFAULT_AJAX_RETURN' => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...

);
?>








