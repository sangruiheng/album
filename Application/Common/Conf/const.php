<?php
/**
 * Created by 有情人好聚好散.
 * Author: ASang
 * Date: 18-8-20
 * Time: 下午3:55
 */

return array(

    'WX' => array(
        // 小程序app_id
        'app_id' => 'wx5fa2df0d2026644f',
        // 小程序app_secret
        'app_secret' => '8da70d7152ccc028ead8f5f305c3549d',

        // 微信使用code换取用户openid及session_key的url地址
        'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
            "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

        // 微信获取access_token的url地址
        'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
            "grant_type=client_credential&appid=%s&secret=%s",

        // 第三方发送消息给公众平台
        'token' => 'sang123456',

        'encodingAesKey' => 'JPGlAhLmnSOObh0SJfCP6iklBK5hHHWWWNNMe1nFrBH',


    ),

    'Setting' => array(
        'Website_URL' => 'https://album.icpnt.com/Uploads/Manage/'
    ),
);