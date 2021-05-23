<?php
class cmssystemAction extends TAction
{
	/**
	 *--------------------------------------------------------------+
	 * Action: index (默认操作)
	 *--------------------------------------------------------------+
	 */
//    public function index()
//    {
//    	$this->assign('DefaultPage', U('public/main'));
//        $this->display('index');
//    }


	/**
	 *--------------------------------------------------------------+
	 * Action: setting
	 *--------------------------------------------------------------+
	 */

    public function setting()
    {
		$module = D('CmscpSetting');
		$dataset = $module->select();
		if( is_null($dataset) || $dataset === false ){
			$dataset = array();
		}
		$this->assign('dataset', $dataset);
		
		$PageTitle = L('系统属性设置');
		$PageMenu = array(
			//array( U('account/create'), L('添加管理员') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }


	/**
	 *--------------------------------------------------------------+
	 * Action: catalog
	 *--------------------------------------------------------------+
	 */
    public function catalog()
    {
		$module = D('CmscpCatalog');
		$dataset = $module->order("sort asc, id asc")->select();
		if( is_null($dataset) || $dataset === false ){
			$dataset = array();
		}
		$this->assign('dataset', $dataset);
		
		$PageTitle = L('模块分类设置');
		$PageMenu = array(
			//array( U('account/create'), L('添加管理员') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }
    /**
     * 保存排序状态
     * @access public
     * @return void
     */
	public function ajax_catalog_sort_save(){
	 	$result = array();
	 	$result['status'] = 'false';
	 	if(isset($_REQUEST['sort'])){
			$module = D('CmscpCatalog');
			$ids = split(',',$_REQUEST['sort']);
			$idx = 1;
			foreach($ids as $id){
				$id = intval($id);
				if( $id < 1 ) continue;
				$dat = array('id' => $id, 'sort'=> $idx);
				$dat['result'] = $module->where('id=%d', $id)->save(array('sort' => $idx));
				$dat['error'] = $module->error;
				//$dat['sql'] = $module->getLastSql();
				$result['sort'][] = $dat;
				$idx++;
			}
			$result['status'] = 'true';
		}
		$this->ajaxReturn($result);
	}
    /**
     * 修改状态
     * @access public
     * @return json
     */
	public function ajax_catalog_change_status()
	{
		$module = D('CmscpCatalog');
		$id 	= intval($_REQUEST['id']);
		//$type 	= isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'status';
		$status = $type=($type+1)%2;
		
		$result = $module->execute( sprintf("UPDATE %s SET status=(status+1)%%2 where id=%d", $module->getTableName(), $id) );
		$values = $module->where('id=%d', $id)->find();
		if( is_null($values) || $values === false ){
			$this->ajaxReturn(array('status' => 'false'));
		}else{
			$this->ajaxReturn(array('status' => 'true', 'result' => $values['status']));
		}
	}


}
?>