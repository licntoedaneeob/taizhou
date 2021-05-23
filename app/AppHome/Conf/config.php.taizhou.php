<?php
if (!defined('THINK_PATH'))	exit();

defined('APP_DEBUG') or define('APP_DEBUG', false);

defined('CGIBIN_ROOT') or define('CGIBIN_ROOT', realpath(dirname(__FILE__) .'/../../' ));
defined('CGIWWW_ROOT') or define('CGIWWW_ROOT', realpath(dirname(__FILE__) .'/../../../' ));
defined('CGIWWW_HOME') or define('CGIWWW_HOME', './' );

/*此处参数要改：*/

defined('BASE_URL') or define('BASE_URL', 'http://'.$_SERVER["HTTP_HOST"].__ROOT__ );   //如：http://cuicuisha.loc/cms    前台全局url
defined('BASE_URL_WEBSITE') or define('BASE_URL_WEBSITE', 'http://'.$_SERVER["HTTP_HOST"] );   //如：http://cuicuisha.loc     网站域名



defined('PIC_SIZE_LIMIT') or define('PIC_SIZE_LIMIT', 10485760 );  //成绩证书、体检证明，上传图片限制 10M

defined('CERT_MEDICAL_UPLOAD') or define('CERT_MEDICAL_UPLOAD', ROOT_PATH.'/public/cert_medical' );     //体检证明上传  最终路径
defined('CERT_MEDICAL_UPLOAD_URI') or define('CERT_MEDICAL_UPLOAD_URI', '/public/cert_medical' );     //体检证明上传  相对路径

defined('CERT_CHENGJI_UPLOAD') or define('CERT_CHENGJI_UPLOAD', ROOT_PATH.'/public/cert_chengji' );     //成绩证书上传  最终路径
defined('CERT_CHENGJI_UPLOAD_URI') or define('CERT_CHENGJI_UPLOAD_URI', '/public/cert_chengji' );     //成绩证书上传  相对路径

defined('SERVICE_PHOTO_UPLOAD') or define('SERVICE_PHOTO_UPLOAD', ROOT_PATH.'/public/service_photo' );     //照片查询  最终路径
defined('SERVICE_PHOTO_UPLOAD_URI') or define('SERVICE_PHOTO_UPLOAD_URI', '/public/service_photo' );     //照片查询  相对路径

defined('WXPAY_SCAN_UPLOAD') or define('WXPAY_SCAN_UPLOAD', ROOT_PATH.'/public/wxpay_scan' );     //微信扫码支付二维码  最终路径
defined('WXPAY_SCAN_UPLOAD_URI') or define('WXPAY_SCAN_UPLOAD_URI', '/public/wxpay_scan' );     //微信扫码支付二维码  相对路径



defined('UPLOAD_WEIXIN_PATH') or define('UPLOAD_WEIXIN_PATH', ROOT_PATH.'/public/weixin/' );   //如：微支付日志保存路径


defined('UPLOAD_SIGN_PATH') or define('UPLOAD_SIGN_PATH', ROOT_PATH.'/public/sign/' );   //如：签名  保存路径
defined('SIGN_UPLOAD_URI') or define('SIGN_UPLOAD_URI', '/public/sign' );     //签名  相对路径



//最终用的微信app
//taizhou
defined('WX_APPID') or define('WX_APPID', 'wx161f19ac48b1f2f3' );  
defined('WX_APPSECRET') or define('WX_APPSECRET', '4fa917911e939520bad222225bbe213a' );  
defined('WX_MCHID') or define('WX_MCHID', '1268067601' );  
defined('WX_KEY') or define('WX_KEY', 'zxcvbnmasdfghjklqwertyuiop123456' );  
//cdmalasong
//defined('WX_APPID') or define('WX_APPID', 'wxecf4b3e8bcbd0eba' );  
//defined('WX_APPSECRET') or define('WX_APPSECRET', 'db933727143f0d9140bc0023d68d350a' );  
//defined('WX_MCHID') or define('WX_MCHID', '1484055112' );  
//defined('WX_KEY') or define('WX_KEY', 'xraAce82LR8123k9yt7LKS74932j25g9' );  
//defined('WX_CACHE_PATH') or define('WX_CACHE_PATH', 'weixin/' );  
//xrace
//defined('WX_APPID') or define('WX_APPID', 'wx0631c2947bfff48d' );  
//defined('WX_APPSECRET') or define('WX_APPSECRET', '98b5faf82102b028bb38ee787f9474d8' );  
//defined('WX_MCHID') or define('WX_MCHID', '1334808901' );  
//defined('WX_KEY') or define('WX_KEY', 'xraAce82LR8123k9yt7LKS74932j25g9' );  
//defined('WX_CACHE_PATH') or define('WX_CACHE_PATH', 'weixin/' );  



//支付宝信息
//taizhou
defined('ALIPAY_ACCOUNT') or define('ALIPAY_ACCOUNT', '1371619541@qq.com' );  
defined('ALIPAY_PARTNER') or define('ALIPAY_PARTNER', '2088421970652684' );  
defined('ALIPAY_KEY') or define('ALIPAY_KEY', 'omveefkx4fq4f808sj32p3h8ahozpzoy' );  
//jinchang
//defined('ALIPAY_ACCOUNT') or define('ALIPAY_ACCOUNT', '2088111358238934' );  
//defined('ALIPAY_PARTNER') or define('ALIPAY_PARTNER', '2088111358238934' );  
//defined('ALIPAY_KEY') or define('ALIPAY_KEY', 'jb3tbjj8gdc1qdquisi1ntxlzwxzysql' );  
//cdmalasong
//defined('ALIPAY_ACCOUNT') or define('ALIPAY_ACCOUNT', 'lidongsheng@tengtidu.com' );  
//defined('ALIPAY_PARTNER') or define('ALIPAY_PARTNER', '2088721225485523' );  
//defined('ALIPAY_KEY') or define('ALIPAY_KEY', 'icrzfgmbv09qw70q0en5q229zmjb4xfn' );  
//xrace
//defined('ALIPAY_ACCOUNT') or define('ALIPAY_ACCOUNT', 'admin@xrace.cn' );  
//defined('ALIPAY_PARTNER') or define('ALIPAY_PARTNER', '2088221413253230' );  
//defined('ALIPAY_KEY') or define('ALIPAY_KEY', 'gzt22y5mwnpacw1n8x3dgqzyed27uxq5' );  



//如最终用的微信app是借来的，此处写自己的官方的微信app：
//defined('WX_APPID_LIANMESHA') or define('WX_APPID_LIANMESHA', 'wxf7b73d816da2a4a5' );  
//defined('WX_APPSECRET_LIANMESHA') or define('WX_APPSECRET_LIANMESHA', '1e7dc0ba27a32c5f4c7f0d3ccfbbef96' );  


//微信自动回复配置
defined('WX_APPID_TOKEN') or define('WX_APPID_TOKEN', 'weixin123' );    //Token(令牌)
defined('WX_APPID_ENCODINGAESKEY') or define('WX_APPID_ENCODINGAESKEY', '37U0xOkic89X4me2JoTg5WtDwAqqp5M4aBmEoRkR6f4' );    //EncodingAESKey(消息加解密密钥)

//defined('WX_APPID_WELCOME_TEXT') or define('WX_APPID_WELCOME_TEXT', '非常欢迎您的关注' );


/****************/


/*

//$setting['http_url'] = "http://enroll.xrace.cn";   //2016报名
$setting['http_url'] = "http://xracebm.loc";

$setting['project_path'] = "/";

$setting['upload_path'] = "userfiles/";

$setting['session_prefix'] = "albm";
$setting['cookie_prefix'] = "";
$setting['encryption_key'] = "xxaamm";

$setting['admin_safe_ask'] = "whoisadmin";
$setting['admin_safe_answer'] = "xrace";

//微信  2016   关联的是：http://enroll.xrace.cn
$setting['wx_cache_path'] = "weixin/";
$setting['wx_appid'] = "wx0631c2947bfff48d";
$setting['wx_appsecret'] = "98b5faf82102b028bb38ee787f9474d8";
$setting['wx_mchid'] = "1334808901";
$setting['wx_key'] = "xraAce82LR8123k9yt7LKS74932j25g9";

//支付宝  2016  关联的是：http://enroll.xrace.cn
$setting['alipay_account'] = "admin@xrace.cn";
$setting['alipay_partner'] = "2088221413253230";
$setting['alipay_key'] = "gzt22y5mwnpacw1n8x3dgqzyed27uxq5";

$setting['smtp_host'] = 'smtp.exmail.qq.com;smtp.xrace.cn';
$setting['smtp_crypto'] = 'ssl';
$setting['smtp_port'] = 465;
$setting['smtp_user'] = 'order-confirm@xrace.cn';
$setting['smtp_pass'] = 'nonSTOP4u';

*/



$ConfigCommon = require(CGIWWW_ROOT.'/config/config.common.php');

$CofnigIgnoreChenk = array();

$array = array( 	
	//如果为0：/index.php?m=events&a=list&page=1 的模式。 
	//如果为1：/index.php/events/list_nt/page/1的模式。
	//如果为2：/events/list/para/1的模式。 
	//都使用U('')方法来封装url;
	'URL_MODEL'	=> 2,
	
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
    'COOKIE_PATH'           => CGIWWW_HOME,     // Cookie路径
    'COOKIE_PREFIX'         => 'SWayLkWeb_',      // Cookie前缀 避免冲突

    'SESSION_AUTO_START'    => true,    // 是否自动开启Session
    'SESSION_OPTIONS'       => array(), // session 配置数组 支持type name id path expire domian 等参数 array('path' => '', 'domain' => '')
    'SESSION_TYPE'          => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
    'SESSION_PREFIX'        => 'SWayLkWeb_', // session 前缀

    'DEFAULT_THEME'         => '',	// 默认模板主题名称
    'DEFAULT_MODULE'        => 'home', // 默认模块名称
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
	

    //伪静态、自定义路由设置
	'URL_ROUTER_ON' => true,
    'URL_ROUTE_RULES'=>array(

        /*
        'exp/detail' => 'exp/detail',
        'exp/center' => 'exp/center',

		'proform/share/list' => 'share/list_pf',
        'proform/share/detail' => 'share/detail_pf',
        'proform/events' => 'events/list_pf',
        'proform/about' => 'about/about_pf',
        'proform/sitemap' => 'sitemap/index_pf',
        'proform/service/store' => 'service/store',
        'proform/tech/ifit' => 'tech/ifit',
        'proform/tech/newidea' => 'tech/newidea',
        'proform/user/welcome' => 'user/welcome',
        'proform/user/reg' => 'user/reg',
        'proform/user/login' => 'user/login',
        'proform/user/center' => 'user/center',
        'proform' => 'home/index_pf',
        

        'nordictrack/share/list' => 'share/list_nt',
        'nordictrack/share/detail' => 'share/detail_nt',
        'nordictrack/events' => 'events/list_nt',
        'nordictrack/about' => 'about/about_nt',
        'nordictrack/sitemap' => 'sitemap/index_nt',
        'nordictrack/service/store' => 'service/store',
        'nordictrack/tech/ifit' => 'tech/ifit',
        'nordictrack/tech/newidea' => 'tech/newidea',
        'nordictrack/user/welcome' => 'user/welcome',
        'nordictrack/user/reg' => 'user/reg',
        'nordictrack/user/login' => 'user/login',
        'nordictrack/user/center' => 'user/center',
        'nordictrack' => 'home/index_nt',
        */

    ),
    /*
    //伪静态、自定义路由设置，获取参数方法
    http://aikang.loc/abc?id=3  --> echo $_GET['id'];exit;
    http://aikang.loc/abc/id/3  --> echo $_GET['id'];exit;
    http://aikang.loc/abc/id/3/sid/5  --> echo $_GET['sid'];exit;
    */

);

return array_merge($ConfigCommon, $CofnigIgnoreChenk, $array);
?>