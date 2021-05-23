<?php
class resourceAction extends TAction
{
	
	/**
	 *--------------------------------------------------------------+
	 * Action: index 
	 *--------------------------------------------------------------+
	 */
    public function index()
    {

		$PageTitle = L('资源管理 - 媒体文件管理');
		$PageMenu = array(
			//array( U('role/create'), L('创建新角色') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
        $this->display('index');
    }

}
?>