<?php
if (!defined('THINK_PATH'))	exit();

defined('APP_DEBUG') or define('APP_DEBUG', false);

defined('CGIBIN_ROOT') or define('CGIBIN_ROOT', realpath(dirname(__FILE__) .'/../../' ));
defined('CGIWWW_ROOT') or define('CGIWWW_ROOT', realpath(dirname(__FILE__) .'/../../../' ));
defined('CGIWWW_HOME') or define('CGIWWW_HOME', '../.' );

/*此处参数要改：*/

defined('BASE_UPLOAD_PATH') or define('BASE_UPLOAD_PATH', CGIWWW_ROOT.'/public/excel/' );   //如：D:/www/ford/public/excel/     //Excel文件上传位置
defined('BASE_FILE_SIZE_LIMIT') or define('BASE_FILE_SIZE_LIMIT', 20 );  //最大允许上传多少M


/****************/


$ConfigCommon = require(CGIWWW_ROOT.'/config/config.common.php');

$CofnigIgnoreChenk = require 'ignorecheck.config.php';

$array = array( 	
	'URL_MODEL'	=> 0,
	
	'DEFAULT_LANG'      => 'zh-cn', // 默认语言
	'LANG_LIST'        => 'zh-cn',
	'LANG_SWITCH_ON'    => true,
	'LANG_AUTO_DETECT'  => false, // 自动侦测语言     

	'APP_AUTOLOAD_PATH' => '@.TagLib',//	

	'TMPL_ACTION_ERROR'   => 'public:error',
	'TMPL_ACTION_SUCCESS' => 'public:success',

	'SHOW_PAGE_TRACE'	=> false,	  //是否显示TRACE信息	

	'HTML_CACHE_ON'	=> false,

    'COOKIE_EXPIRE'         => 0,    // Coodie有效期
    'COOKIE_DOMAIN'         => '',      // Cookie有效域名
    'COOKIE_PATH'           => CGIWWW_HOME, //'/LookCMS/admin/',     // Cookie路径
    'COOKIE_PREFIX'         => 'SWayLkSYS_',      // Cookie前缀 避免冲突

    'SESSION_AUTO_START'    => true,    // 是否自动开启Session
    'SESSION_OPTIONS'       => array(), // session 配置数组 支持type name id path expire domian 等参数 array('path' => '', 'domain' => '')
    'SESSION_TYPE'          => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
    'SESSION_PREFIX'        => 'SWayLkSYS_', // session 前缀

    'DEFAULT_THEME'         => '',	// 默认模板主题名称
    'DEFAULT_MODULE'        => 'index', // 默认模块名称
    'DEFAULT_ACTION'        => 'index', // 默认操作名称
    'DEFAULT_CHARSET'       => 'utf-8', // 默认输出编码

	'TMPL_TRACE_FILE' => APP_PATH.'Tpl/page_trace.tpl',
	'TMPL_EXCEPTION_FILE' => APP_PATH.'Tpl/exception.tpl',
	'TRACK_FILTER' => array(
		array('F'=> CGIBIN_ROOT, 'R' => 'app'),
		array('F'=> CGIWWW_ROOT, 'R' => ''),
	),

	'VAR_PAGE'  =>  'page',

//	'SHOW_RUN_TIME'    => APP_DEBUG, // 运行时间显示
//	'SHOW_ADV_TIME'    => APP_DEBUG, // 显示详细的运行时间
//	'SHOW_DB_TIMES'    => APP_DEBUG, // 显示数据库查询和写入次数
//	'SHOW_CACHE_TIMES' => APP_DEBUG, // 显示缓存操作次数
//	'SHOW_USE_MEM'     => APP_DEBUG, // 显示内存开销
//	'SHOW_LOAD_FILE'   => APP_DEBUG, // 显示加载文件数
//	'SHOW_FUN_TIMES'   => APP_DEBUG, // 显示函数调用次数
	'SHOW_PAGE_TRACE' => APP_DEBUG, // 显示页面Trace信息

);

return array_merge($ConfigCommon, $CofnigIgnoreChenk, $array);
?>