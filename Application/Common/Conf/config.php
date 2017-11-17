<?php
return array(
	//'配置项'=>'配置值'
    //打开调试工具trace
    "SHOW_PAGE_TRACE" => true,
    //trace调试工具设置
    "TRACE_PAGE_TABS" => array(
    	"base" => "基本",
    	"file" => "文件",
    	"think" => "流程",
    	"error" => "又错了",
    	"sql" => "SQL",
    	"debug" => "找BUG",
    	"user" => "草泥马"
    	),
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'shopping',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'root',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'tpshop_',    // 数据库表前缀
    //地址栏设置，取消index.php文件的显示
    "URL_MODEL" => 2,
    //自动开启SESSION
    "SESSION_AUTO_START" => true,
    'URL_HTML_SUFFIX'=>'shtml'
);