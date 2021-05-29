<?php

/*
//301跳转 start
$current_page_url = 'http';
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]== "on") {
    $current_page_url .= "s";
}
 $current_page_url .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
$current_page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
} else {
    $current_page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
}
$old_url=$current_page_url;
//echo $old_url;exit;


if(stristr($old_url,'tzmls.org')){
	$new_url=str_replace('tzmls.org','www.tzmls.org',$old_url);
	header("HTTP/1.1 301 Moved Permanently");
	header('location:'.$new_url);
	exit;
}
//301跳转 end
*/
?>