<?php
return array(
    //'配置项'=>'配置值'
    'TMPL_L_DELIM' => '<{',//修改左定界符
    'TMPL_R_DELIM' => '}>',//修改右定界符
    'APP_STATUS' => 'debug', //应用调试模式状态
    'ERROR_PAGE' => '/error.html',//错误和异常页面链接
    'LOAD_EXT_CONFIG' => 'const',  //自定义全局常量
    //数据库链接
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_NAME' => 'album', // 数据库名
    'DB_USER' => 'icpnt', // 用户名
    'DB_PWD' => 'icpnt.com2014', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'icpnt_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 字符集
    'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
    'DB_PARAMS' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),//数据库字段名大小写敏感
);
