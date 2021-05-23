<?php
class roleAction extends TAction
{
	/**
	 *--------------------------------------------------------------+
	 * Action: index 
	 *--------------------------------------------------------------+
	 */
//    public function index()
//    {
//    	$this->assign('DefaultPage', U('public/main'));
//        $this->display('index');
//    }


	/**
	 *--------------------------------------------------------------+
	 * Action: list 
	 *--------------------------------------------------------------+
	 */
	public function listing()
	{
		/// 列表一型： 普通
		//$this->GeneralActionForListing('CmscpRole', 'status < 250');

		/// 列表二型: 可拖动排序，不分页
		$module   = D('CmscpRole');  ///有 module 文件的
		$sqlWhere = 'status < 250';  ///取全部未删除的角色记录
		$rescount = $module->where($sqlWhere)->count();
		$dataset  = $module->where($sqlWhere)->order('sort ASC')->limit('0,255')->select();
		$this->assign('dataset', $dataset);// 赋值数据集
		$this->assign('page', '');// 赋值分页输出

		$PageTitle = L('管理者角色列表');
		$PageMenu = array(
			array( U('role/create'), L('创建新角色') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}

	/**
	 *--------------------------------------------------------------+
	 * Action: create
	 *--------------------------------------------------------------+
	 */
	function create()
	{
		if(isset($_POST['dosubmit'])){
			$module = D('CmscpRole');
			if(!isset($_POST['role'])||($_POST['role']=='')){
				$this->error(L('请填写角色代码'));
			}
			if(!isset($_POST['role_name'])||($_POST['role_name']=='')){
				$this->error(L('请填写角色名称'));
			}
			$result = $module->where("role='%s'", $_POST['role'])->count();
			if($result){
				$this->error(L('角色代码已经存在'));
			}
			$result = $module->where("role_name='%s'", $_POST['role_name'])->count();
			if($result){
				$this->error(L('角色名称已经存在'));
			}
			$module->create();
			$result = $module->add();
			if($result){
				$this->success('操作成功！');
			}else{
				$this->error(L('operation_failure'));
			}
		}else{
			$PageTitle = L('管理者角色');
			$PageMenu = array(
				array( U('role/listing'), L('管理者角色列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
	}

	/**
	 *--------------------------------------------------------------+
	 * Action: edit
	 *--------------------------------------------------------------+
	 */
	public function edit()
	{
		if(isset($_POST['dosubmit'])){
			$module = D('CmscpRole');
			if (false === $module->create()) {
				$this->error($module->getError());
			}
			$result = $module->save();
			if(false !== $result){
				//$this->success(L('operation_success'), '', '',, U('role/edit', 'id = '.));
				$this->success('操作成功！', U('role/listing'));
			}else{
				$this->error(L('operation_failure'));
			}
		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}
			$module = D('CmscpRole');
			$record = $module->where('role_id = %d',$id)->find();
			$this->assign('record', $record);

			$PageTitle = L('管理者角色');
			$PageMenu = array(
				array( U('role/listing'), L('管理者角色列表') ),
				array( U('role/create'), L('创建管理者角色') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
	}

	/**
	 *--------------------------------------------------------------+
	 * Action: delete
	 *--------------------------------------------------------------+
     * 角色不做真正删除，仅设置 status = 255。当 status < 250 时未刪
	 */
	function delete()
	{
		$REQ = isset($_POST['role_id']) ? $_POST['role_id'] : ( isset($_GET['role_id']) ? $_GET['role_id'] : '' );
		if( empty($REQ) ){
			$this->error(L('请选择要删除的角色！'));
		}
		$module = D('CmscpRole');
		//trace( print_r($REQ, true), 'delete-ids');
		if (is_array($REQ)) {
			$ids = $REQ;
		} else if( strpos($REQ, ',') !== false) {
			$ids = explode(',', $REQ);
		} else {
			$ids = array( $REQ );
		}
		//trace( print_r($ids, true), 'delete-ids');
		$ids = array_map( intval, $ids);
		//trace( print_r($ids, true), 'delete-ids');
		$result = $this->ModuleDelete('D', 'CmscpRole', $ids, 'role_id');
		if($result){
			$this->success('操作成功！', U('role/listing'));
		}else{
			$this->error(L('operation_failure'), U('role/listing'));
		}
	}


	/**
	 *--------------------------------------------------------------+
	 * Action: auth
	 *--------------------------------------------------------------+
	 * 
	 */
	public function auth()
	{
		$catas = $this->CacheCatalog;
		///$nodes = $this->CacheNodes;
		///取全部有效的可以给用户设置的Node
		$nodes = $this->CacheAuthNodes;
	
		$role_id = intval($_REQUEST['id']);
		if( $role_id < 2 ){
			redirect(U('role/listing'));
		}
		$modRole = D('CmscpRole');
		$role = $modRole->where('role_id = %d', $role_id)->find();
	
		$permission = $this->getCmscpRolePermission(false, $role_id, true);
		//		$modPermission = D('CmscpPermission');
		//		$permission = $modPermission->where("role_id = %d", $role_id)->select();
		//		if( is_null($permission) || $permission == false ){
		//			$permission = array();
		//		}
	
		trace( print_r($role,true), 'Auth - Role - ', 'debug');
		trace( print_r($permission,true), 'Auth - permission - ', 'debug');
		trace( print_r($nodes,true), 'Auth - nodes - ', 'debug');
	
	
		$menus = array();
		foreach($nodes as $node){
				
			if( $node['status']  == 9 ) { continue; }
				
			$yes = (isset( $permission[$node['node_id']] )) && ($permission[$node['node_id']]['access'] == 1); //in_array($node['node_id'], $permission) &&
			if( $node['group'] == 'Y' && $node['catalog']=='index' && $node['module'] == 'public' ){
				$yes = true;
			}else if($node['group'] != 'Y' && $node['catalog']=='index' && $node['module'] == 'public' && $node['action'] == 'index' ){
				$yes = true;
			}
				
			$ctitle = '';
			if( isset($catas[ $node['catalog'] ]) ){
				$ctitle = $catas[ $node['catalog'] ]['title'];
			}
			$menus[ $node['catalog'] ]['title'] = $ctitle;
				
			$mitem = array();
			$mitem['code']  = $node['node_id'];
			$mitem['class'] = ( $node['type'] == 0 ) ? 'link' : ( $node['group'] == 'Y'  ? $node['module'] : $node['module'].'-'.$node['action']);
			$mitem['title'] = ( $node['type'] == 0 ) ? $node['title'] : ( ( $node['group'] == 'Y' ) ? $node['module_name'] : $node['action_name']);
			$mitem['link']  = ( $node['type'] == 0 ) ? $node['link'] : (U($node['module'].'/'.$node['action']) . $node['param']);
			$mitem['auth']  = $yes ? 'yes' : 'no';
				
			if( $node['group'] == 'Y' ){
				$menus[ $node['catalog'] ]['-links'][$node['module']]['code']  = $node['node_id'];
				$menus[ $node['catalog'] ]['-links'][$node['module']]['class'] = ( $node['type'] == 0 ) ? 'link' : $node['module'];
				$menus[ $node['catalog'] ]['-links'][$node['module']]['title'] = ( $node['type'] == 0 ) ? $node['title'] : $node['module_name'];
				$menus[ $node['catalog'] ]['-links'][$node['module']]['link']  = ( $node['type'] == 0 ) ? $node['link'] : (U($node['module'].'/'.$node['action']) . $node['param']);
				//$menus[ $node['catalog'] ]['-links'][$node['module']] = $mitem;
			}else{
				$menus[ $node['catalog'] ]['-links'][$node['module']]['-links'][] = $mitem;
			}
				
		}

		//上方菜单按sway_cmscp_catalog的sort排序
		$ar_menus=array();
		foreach($catas as $cata){
			if( isset($menus[$cata['catalog']]) ){
				$ar_menus[$cata['catalog']]=$menus[$cata['catalog']];
			}
		}
		$menus=$ar_menus;


		trace( print_r($menus,true), 'Auth - menus - ', 'debug');
	
		$this->assign('modules',$menus);
		$this->assign('role_id',$role_id);
		$this->assign('catalog',$catas);
	
		$PageTitle = L('管理者角色');
		$PageMenu = array(
				array( U('role/listing'), L('管理者角色列表') ),
				array( U('role/create'), L('创建管理者角色') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	/**
	 *--------------------------------------------------------------+
	 * Action: auth_submit
	 *--------------------------------------------------------------+
	 *
	 */
	public function auth_submit()
	{
		$roles = $this->getCmscpRoleList(true, false);
		$role_id = intval($_REQUEST['id']);
		$permission = D('CmscpPermission');
		trace( print_r($roles,true), 'Auth - Sumit - Roles: ', 'debug');
	
		$permission->where("role_id=".$role_id)->delete();
	
		$node_ids = $_REQUEST['access_node'];
		foreach ($node_ids as $node_id) {
			$data=array();
			$data['role_id'] = $role_id;
			$data['node_id'] = $node_id ;
			$data['role'] = $roles[ $role_id ]['role'];
			$permission->add($data);
		}
		$this->success('操作成功！', U('role/auth', 'id='.$role_id));
		//$this->display('auth');
	}	
	
	
    /**
     * 保存排序状态
     * @access public
     * @return void
     */
	public function ajax_sort_save(){
	 	$result = array();
	 	$result['status'] = 'false';
	 	if(isset($_REQUEST['sort'])){
			$module = D('CmscpRole');
			$ids = split(',',$_REQUEST['sort']);
			$idx = 1;
			foreach($ids as $id){
				$id = intval($id);
				if( $id < 1 ) continue;
				$dat = array('id' => $id, 'sort'=> $idx);
				$dat['result'] = $module->where('role_id=%d', $id)->save(array('sort' => $idx));
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
	public function ajax_change_status()
	{
		$module = D('CmscpRole');
		$id 	= intval($_REQUEST['id']);
		//$type 	= isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'status';
		$status = $type=($type+1)%2;
		
		$result = $module->execute( sprintf("UPDATE %s SET status=(status+1)%%2 where role_id=%d", $module->getTableName(), $id) );
		$values = $module->where('role_id=%d', $id)->find();
		if( is_null($values) || $values === false ){
			$this->ajaxReturn(array('status' => 'false'));
		}else{
			$this->ajaxReturn(array('status' => 'true', 'result' => $values['status']));
		}
	}




}
/*

<table width="100%" cellpadding="2" cellspacing="1" class="grid-table" id="grid">
<foreach name="modules" item="Cat">
<notempty name="Cat['-links']">
	<thead>
		<tr><th colspan="2">{$Cat['title']}</th></tr>
	</thead>
	<tbody>
	<foreach name="Cat['-links']" item="Grp">
        <tr>
            <th align="" style="" nowrap="true">
            	<input type="checkbox" id ="node-module-{$Grp['code']}" class="module module-{$Grp['code']}" value="{$Grp['code']}" name="access_node[]"/>
            	&nbsp;<label for="node-module-{$Grp['code']}">{$Grp['title']}</label>
        	</th>
            <td>
	<notempty name="Grp['-links']">
	<foreach name="Grp['-links']" item="Val">
	<div class="node">
		<input type="checkbox" id ="node-action-{$Val['code']}" class="action action-{$Grp['code']}" data-group="{$Grp['code']}" value="{$Val['code']}" name="access_node[]" <if condition="$Val['auth'] == 'yes'">checked="checked"</if> /><label for="node-action-{$Val['code']}">{$Val['title']}</label>
	</div>
	</foreach>
	</notempty>
		
      		</td>
        </tr>

	</foreach>
	</tbody>
</notempty>
</foreach>
</table>

*/
?>