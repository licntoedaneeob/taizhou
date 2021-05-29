<?php

defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__));
defined('CGIBIN_ROOT') or define('CGIBIN_ROOT', realpath(ROOT_PATH .'/app/' ));
defined('CGIWWW_ROOT') or define('CGIWWW_ROOT', realpath(ROOT_PATH .'/' ));
defined('CGIWWW_HOME') or define('CGIWWW_HOME', '' );

//if (!is_file(ROOT_PATH . '/data/install.lock')) {
//    header('Location: ./install.php');
//    exit;
//}

define('THINK_PATH', CGIBIN_ROOT . '/ThinkPHP/');
define('APP_NAME',   'home');
define('APP_PATH',   CGIBIN_ROOT . '/AppHome/');


//AJAX、URL等站内链接相对URL前缀路径（根目录下访问，则写空；二级目录下访问，则写“/think”）
define('__ROOT__',   '');



define('RUNTIME_PATH',   realpath(ROOT_PATH . '/var/HomeRuntime/').'/');

//defined('LANG_PATH')    or define('LANG_PATH',      APP_PATH.'Lang/'); // 项目语言包目录
//defined('TMPL_PATH')    or define('TMPL_PATH',      APP_PATH.'Tpl/'); // 项目模板目录
//defined('HTML_PATH')    or define('HTML_PATH',      APP_PATH.'Html/'); // 项目静态目录
//defined('LOG_PATH')     or define('LOG_PATH',       RUNTIME_PATH.'Logs/'); // 项目日志目录
//defined('DATA_PATH')    or define('DATA_PATH',      RUNTIME_PATH.'Data/'); // 项目数据目录
//defined('TEMP_PATH')    or define('TEMP_PATH',      RUNTIME_PATH.'Temp/'); // 项目缓存目录
//defined('CACHE_PATH')   or define('CACHE_PATH',     RUNTIME_PATH.'Cache/'); // 项目模板缓存目录

//是否显示系统报错，是否启用右下角 page_trace 功能
define('APP_DEBUG', true);

//define ( "GZIP_ENABLE", function_exists ( 'ob_gzhandler' ) );
//ob_start ( GZIP_ENABLE ? 'ob_gzhandler' : null );


//设置1为cdn模式，设置0为本地模式：
$cdn_mode=1;


if($cdn_mode==1){
//images、css、js等静态文件相对路径  引用CDN的URL前缀
define('STATICSPATH', 'http://resource.tzmls.org/statics/');
//引用CDN的URL前缀
define('STATICSCDN', 'http://resource.tzmls.org');
}
else{
//images、css、js等静态文件相对路径
define('STATICSPATH', '/statics/');
define('STATICSCDN', '');
}


set_time_limit(0);  //设置不要超时

require( CGIWWW_ROOT."/link301.php");  //301跳转

require( THINK_PATH."ThinkPHP.php");
?>