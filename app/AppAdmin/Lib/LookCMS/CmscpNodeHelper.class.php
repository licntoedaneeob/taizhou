<?php
///CmscpNodeMode 辅助类
require ( dirname(__FILE__) .'/CmscpHelper.class.php' );
class CmscpNodeMode extends CmscpHelper{
	//public $moduleNode;

	function __construct(){
		parent::__construct();
	}

    protected function _initialize() {
    	$moduleNode = D('CmscpNode');
    	
    	///取全部Node
    	///分出供 菜单使用的、供权限使用的。然后根据角色给出列表。并提供 根据用户的角色的菜单数据生成 和 当前用户的权限认证
    	
    }
}
/*
Node

group
	Y	菜单分组
style:
	//0	link		空	等同于 内部链接且link为#
	1	module		模块	一般同时作为分组（单纯模块无动作，则无显示页面，一般作为分组用）
	2	action		行为	可作为菜单项显示
	3	operation	动作	此类条目不显示为菜单项（一般都是通过其他页面调用。比如列表调用编辑动作）
	4	link-		内显链接
	5	link-blank	新开页链接
type
	0	无需权限
	1	需要权限
	2	内部权限（仅 administrator）
status
	0	无效
	1	有效



*/
?>