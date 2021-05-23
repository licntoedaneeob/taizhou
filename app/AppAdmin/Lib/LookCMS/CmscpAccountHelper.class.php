<?php
///CmscpNodeMode 辅助类
require ( dirname(__FILE__) .'/CmscpHelper.class.php' );
class CmscpAccountHelper extends CmscpHelper{
	//public $moduleNode;

	function __construct(){
		parent::__construct();
		$this->_initialize();
	}
	
    protected function _initialize() {
    	$this->thing();

    }
	
	public function ManagerLogin($username, $password){
		$module = D('CmscpManager');
		$manager = $module->where("username='%s'", $username)->find();
		if( is_null($manager) || $manager === false ){
			return -1; //$this->error('帐号不存在或已禁用！');
		}else if( $manager['status'] == 0 ){
			return -2; //$this->error('帐号不存在或已禁用了！');
		}else if( $manager['password'] != md5($password.$manager['salt']) ){
			return -3; //$this->error('帐号密码错误！');
		}else{
			session('cmscp_login', 'manager');
			session('cmscp_manager', $manager);
			//session('cmscp_role_code', $manager['role']);
			return 1;
		}
	}
	
	public function AgentLogin($username, $password){
		$module = M('agent');
		$manager = $module->where("username='%s'", $username)->find();
		if( is_null($manager) || $manager === false ){
			return -1; //$this->error('帐号不存在或已禁用！');
		}else if( $manager['status'] == 0 ){
			return -2; //$this->error('帐号不存在或已禁用了！');
		}else if( $manager['password'] != md5($password){ //.$manager['salt']) ){
			return -3; //$this->error('帐号密码错误！');
		}else{
			session('cmscp_login', 'agent');
			session('cmscp_agent', $manager);
			//session('cmscp_role_code', 'agent');
			return 1;
		}
	}
	
	public function Logout(){
		if( session('?cmscp_login') ){
			session('cmscp_login', null);
			session('cmscp_manager', null);
			session('cmscp_agent', null);
			session('cmscp_role_code', null);
			session('cmscp_role', null);
			session('cmscp_permission', null);
//			$this->success('您已注销登陆！',U('public/login'));
			return true;
		}else{
//			$this->error('您还没有登陆！');
			return false;
		}
	}
	
	public function IsLoggedIn(){
		if( session('?cmscp_login') ){
			return true;
		}else{
			return false;
		}
	}
	
	private function _SetCurrentData(){
		$account = array('Type' => '', 'UserName' => '', 'RoleName' => '',  'UserCode' => '', 'RoleStatus' => '', 'RoleCode' => '', 'Permission' => array() );
		$account['Type'] = session('cmscp_login');
		if( session('cmscp_login') == 'manager' ){
			$user = session('cmscp_manager');
			$account['UserName'] = $user['username'];
			$account['UserCode'] = $user['id'];
			$account['RoleCode'] = 'agent';
			
		}else{
			$user = session('cmscp_agent');
			$account['UserName'] = $user['username'];
			$account['UserCode'] = $user['id'];
			$account['RoleCode'] = 'agent';
		}
		$code = $account['RoleCode'];
		$role = isset( $this->DataRoleFull[ $code ] ) ? $this->DataRoleFull[ $code ] : array('status' => '0', 'role' => '', 'role_id' => '0');
		$account['RoleStatus'] = $role['status'];
		$account['RoleName'] = $role['role'];
		$account['RoleCode'] = $role['role_id'];
		
		$session('cmscp_account', $account);
	}
	private function _SetCurrentPermisson(){
		$account = $session('cmscp_account');
		
		
	}
	
	public function getAccount(){
		return session('cmscp_account');
	};
	
//	public function getAccountRoleCode(){
//		if( !$this->IsLoggedIn() ){ return ''; }
//		$code = session('cmscp_role_code');
//		if( !$code ){
//			$t = session('cmscp_login');
//			if( $t == 'agent' ){
//				$code = 'agent';
//			}else{
//				$u = session('cmscp_manager');
//				$code = $u['role'];
//			}
//		}
//		$role = session('cmscp_role');
//		if( !$role ){
//			$role = isset( $this->DataRoleFull[ $code ] ) ? $this->DataRoleFull[ $code ] : array();
//		}
//		session('cmscp_role', $role);
//		session('cmscp_role_code', $code);
//		return $code;
//	}
//	public function getAccountRole(){
//		return session('cmscp_role');
//	}

	
	public function CheckAccount(){
		
//		if( !$this->IsLoggedIn() )
//			return false;
		if( !$this->IsLoggedIn() ){ return false; }
		
		
	}
	
	var $DataCatalogFull = array();
	var $DataCatalogValid = array();
	var $DataRoleFull = array();
	var $DataRoleValid = array();
	var $DataNodeFull = array();
	var $DataNodeValid = array();
	
	var $DataUserNode = array();
	var $DataUserPermission = array();
	
	public function thing(){
		$mod = D('CmscpRole');
		$res = $mod->where('status < 250')->select();
		if( is_null($res) || $res === false){
			//$DataRoleFull = array();
		}else{
			foreach($res as $rec){
				$this->DataRoleFull[ $rec['role'] ] = $rec;
				if( $rec['status'] == '1' ){
					$this->DataRoleValid[ $rec['role'] ] = $rec;
				}
			}
		}
		$mod = D('CmscpCatalog');
		$res = $mod->where('status < 250')->select();
		if( is_null($res) || $res === false){
			//$DataCatalogFull = array();
		}else{
			foreach($res as $rec){
				$this->DataCatalogFull[ $rec['catalog'] ] = $rec;
				if( $rec['status'] == '1' ){
					$this->DataCatalogValid[ $rec['catalog'] ] = $rec;
				}
			}
		}
//		$mod = D('CmscpNodes');
//		$res = $mod->where('status < 250')->select();
//		if( is_null($res) || $res === false){
//			//$DataNodeFull = array();
//		}else{
//			foreach($res as $rec){
//				$this->DataNodeFull[ $rec['node_id'] ] = $rec;
//				if( $rec['status'] == '1' ){
//					$this->DataNodeValid[ $rec['node_id'] ] = $rec;
//				}
//			}
//		}
	}
	
	

}
?>