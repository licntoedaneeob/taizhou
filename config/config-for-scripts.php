<?php
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__));
defined('CGIBIN_ROOT') or define('CGIBIN_ROOT', realpath(ROOT_PATH .'/../app/' ));
defined('CGIWWW_ROOT') or define('CGIWWW_ROOT', realpath(ROOT_PATH .'/../' ));
defined('CGIWWW_HOME') or define('CGIWWW_HOME', '' );

define('THINK_PATH', CGIBIN_ROOT . '/ThinkPHP/');
define('APP_NAME',   'home');
define('APP_PATH',   CGIBIN_ROOT . '/AppHome/');
define('__ROOT__',   '');

$Configura = require( APP_PATH .'/Conf/config.php' );

?>