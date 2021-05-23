<?php
if (!function_exists('lkShowExceptionValue')) { 
function lkShowExceptionValue($value){
	$value = str_replace("\\", '/', $value);
	$filters = C('TRACK_FILTER');
	if( is_array($filters) ){
		$search = array();
		$replace = array();
		foreach($filters as $filter){
			$search[] = str_replace("\\", '/', $filter['F']);
			$replace[] = str_replace("\\", '/', $filter['R']);
		}
		$value = str_replace($search, $replace, $value);
	}
	return $value;
}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统发生错误</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
html{ overflow-y: scroll; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 12px; }
img{ border: 0; }
.error{ padding: 24px 48px; }
.face{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
h1{ font-size: 32px; line-height: 48px; }
.error .content{ padding-top: 10px}
.error .info{ margin-bottom: 12px; }
.error .info .title{ margin-bottom: 3px; }
.error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }
.error .info .text{ line-height: 24px; }
.copyright{ padding: 12px 48px; color: #999; }
.copyright a{ color: #000; text-decoration: none; }
</style>
</head>
<body>
<div class="error">
<p class="face">:(</p>
<h1><?php echo strip_tags(lkShowExceptionValue($e['message']));?></h1>
<div class="content">
<?php if(isset($e['file'])) {?>
	<div class="info">
		<div class="title">
			<h3>错误位置</h3>
		</div>
		<div class="text">
			<p>FILE: <?php echo lkShowExceptionValue($e['file']) ;?> &#12288;LINE: <?php echo $e['line'];?></p>
		</div>
	</div>
<?php }?>
<?php if(isset($e['trace'])) {?>
	<div class="info">
		<div class="title">
			<h3>TRACE</h3>
		</div>
		<div class="text">
			<p><?php echo nl2br(lkShowExceptionValue($e['trace']));?></p>
		</div>
	</div>
<?php }?>
</div>
</div>
<div class="copyright">
<?php /* 隐藏
<p><a title="" href="http://www.web.com">WebSite</a><sup><?php //echo THINK_VERSION ?></sup></p>
*/ ?>
</div>
</body>
</html>