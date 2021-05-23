<?php
class publicAction extends TAction
{
	/**
	 *--------------------------------------------------------------+
	 * Action: login
	 *--------------------------------------------------------------+
	 */
	public function login()
	{
		$modCmscpManager = D('CmscpManager');
		
		if( isset($_POST['formSubmit']) ){
			$goon = true;
			if (!$modCmscpManager->autoCheckToken($_POST)){
				//// 令牌验证错误
				$this->error('登陆已过期，请重试！');
				$goon = false;
			}
			if($goon){
				$username = isset($_POST['username']) ? trim($_POST['username']) : '';
				$password = isset($_POST['password']) ? trim($_POST['password']) : '';
				if ($username!="" && $password!="") {
                }
				else{
                    $this->error('帐号密码错误！');exit;
                    //redirect(U('public/login'));
				}
				//echo "<pre>";print_R($_POST);exit;
				//echo md5($_POST['verify']);exit;

				//trace($_POST['verify'], 'login-get-verify');
				//trace(md5($_POST['verify']), 'login-md5-verify');
				//trace($_SESSION['verify'], 'login-org-verify');

                if( isset($_POST['verify']) && $_POST['verify']!="" && $_SESSION['verify'] == md5($_POST['verify']) ){
                }
                else{
                    $this->error('验证码错误！');exit;
                }

				//if($this->setting['check_code']==1){
//					if ($_SESSION['verify'] != md5($_POST['verify'])){
//						$this->error(L('verify_error'));
//					}
				//}
				$manager = $modCmscpManager->where("username='%s'", $username)->find();
//				trace(($manager ? $manager->getLastSql() : 'NULL'), 'login-get-sql');
				trace(print_r($manager,true), 'login-get-info');
				if( is_null($manager) || $manager === false ){
					
					
					//$this->error('帐号不存在或已禁用！');
					//exit;
					
					//查店铺表
					$userMod = M('store');
					$sqlWhere = "status =1";
			        $sqlWhere .=" and username='".addslashes($username)."' and password='".addslashes($password)."' ";
			        $sqlOrder = " id DESC ";
			        $userlist = $userMod->field('id,title,prov_id,city_id,street_id,username')->where( $sqlWhere )->order( $sqlOrder )->select();
			        //echo "<pre>";print_r($userlist);exit;
			        if(isset($userlist) && count($userlist)>0){
			        	$userinfo=$userlist[0];
			        	$userinfo['role_id']=3;
			        	$userinfo['role']='sales';
			        }
			        else{
			        	$this->error('帐号不存在或已禁用！');
						exit;
			        }
			        //echo "<pre>";print_r($userinfo);exit;
			        
			        $Account = array();
					$Account['Account-Code'] = $userinfo['id'];
					$Account['Account-User'] = $userinfo['username'];
					$Account['Account-Name'] = $userinfo['username'];
					$Account['Account-Role'] = 'sales';
					$Account['Account-Type'] = 'manager';
					$Account['User'] = $userinfo;
					$Account['Role'] = array();
					$Account['Permission'] = array();
					
					
					//echo "<pre>";print_r($Account);exit;
					
					session('cmscp_login', 'manager');
					session('cmscp_account', $Account);
					
					
					$this->success('登录成功！',U('index/index'));
					exit;
					
					
					
				}else if( $manager['status'] == 0 ){
					$this->error('帐号不存在或已禁用了！');
					//exit;
				}else if( $manager['password'] != md5($password.$manager['salt']) ){
					$this->error('帐号密码错误！');
					//exit;
				}else{
					
					
					$Account = array();
					$Account['Account-Code'] = $manager['id'];
					$Account['Account-User'] = $manager['username'];
					$Account['Account-Name'] = $manager['username'];
					$Account['Account-Role'] = $manager['role'];
					$Account['Account-Type'] = 'manager';
					$Account['User'] = $manager;
					$Account['Role'] = array();
					$Account['Permission'] = array();
					
					//echo "<pre>";print_r($Account);exit;
					
					session('cmscp_login', 'manager');
					session('cmscp_account', $Account);
//					
//					$manager['Account-Code'] = $manager['id'];
//					$manager['Account-User'] = $manager['username'];
//					$manager['Account-Name'] = $manager['username'];
//					$manager['Account-Role'] = $manager['role'];
//					session('cmscp_login', 'manager');
//					session('cmscp_manager', $manager);
					/*
					/// 还要判断role 是否有效
					$CurrentRole = $this->ModRole->field('role_id', 'role',  'role_name', 'ststus')->where("role = '%s'", $manager['role'])->find();
					if( is_null($CurrentRole) || $CurrentRole === false){
						$CurrentRole = array();
					}
					session('cmscp_role', $CurrentRole);
					if( !isset($CurrentRole['status']) || $CurrentRole['status'] != 1 ){
						session('cmscp_permission', array());
					}else{
						$CmscpPermission = $this->getCmscpRolePermission(false, $manager['role'], false);
						session('cmscp_permission', $CmscpPermission);
					}
					*/
					$this->success('登录成功！',U('index/index'));
					exit;
				}
			}
		}
		
		$this->assign('set',$this->Setting);

//		$this->assign('test',md5('look63530539'));

		C('TOKEN_ON',true);
		$this->display();
	}
	public function login_agent()
	{
		if( !isset($_POST['formSubmit']) ){
			redirect(U('public/login#login-agent'), 't='.time());
		}
		//$modCmscpManager = D('CmscpManager');
		$modCmscpManager = M('agent');
		
		//if( isset($_POST['formSubmit']) ){
			$goon = true;
			if (!$modCmscpManager->autoCheckToken($_POST)){
				//// 令牌验证错误
				$this->error('登陆已过期，请重试！');
				$goon = false;
			}
			if($goon){
				$username = isset($_POST['username']) ? trim($_POST['username']) : '';
				$password = isset($_POST['password']) ? trim($_POST['password']) : '';
				if (!$username || !$password) {
					redirect(U('public/login#login-agent'));
				}
				trace($_POST['verify'], 'login-get-verify');
				trace(md5($_POST['verify']), 'login-md5-verify');
				trace($_SESSION['verify'], 'login-org-verify');
				//if($this->setting['check_code']==1){
//					if ($_SESSION['verify'] != md5($_POST['verify'])){
//						$this->error(L('verify_error'));
//					}
				//}
				$manager = $modCmscpManager->where("username='%s'", $username)->find();
				if( is_null($manager) || $manager === false ){
					$this->error('帐号不存在或已禁用！');
					exit;
				}else if( $manager['status'] == 0 ){
					$this->error('帐号不存在或已禁用了！');
					//exit;
				}else if( $manager['password'] != md5($password.$manager['salt']) ){
					$this->error('帐号密码错误！');
					//exit;
				}else{

					$Account = array();
					$Account['Account-Code'] = $manager['id'];
					$Account['Account-User'] = $manager['username'];
					$Account['Account-Name'] = $manager['agent_name'];
					$Account['Account-Role'] = 'agent';
					$Account['Account-Type'] = 'agent';
					$Account['User'] = $manager;
					$Account['Role'] = array();
					$Account['Permission'] = array();
					
					session('cmscp_login', 'agent');
					session('cmscp_account', $Account);
//
//
//					$manager['Account-Code'] = $manager['id'];
//					$manager['Account-User'] = $manager['username'];
//					$manager['Account-Name'] = $manager['agent_name'];
//					$manager['Account-Role'] = 'agent';
//					$manager['role'] = 'agent';
//					$manager['ShowName'] = $manager['agent_name'];
//					session('cmscp_login', 'agent');
//					session('cmscp_manager', $manager);
					$this->success('登录成功！',U('index/index'));
					exit;
				}
			}
		//}
		
		$this->assign('set',$this->Setting);

//		$this->assign('test',md5('look63530539'));

		C('TOKEN_ON',true);
		$this->display();
	
	}
	/**
	 *--------------------------------------------------------------+
	 * Action: logout
	 *--------------------------------------------------------------+
	 */
	public function logout()
	{
		//if( session('?cmscp_manager') ){
		if( session('?cmscp_login') ){
			session('cmscp_account', null);
//			session('cmscp_manager', null);
//			session('cmscp_agent', null);
//			session('cmscp_role', null);
//			session('cmscp_permission', null);
			$this->CurrentManager = null;
			$this->CurrentPermission = null;
			$this->CurrentRole = null;
			$this->success('您已注销登陆！',U('public/login'));
		}else{
			$this->error('您还没有登陆！',U('public/login'));
		}
	}
	
	
    /**
     * 生成验证码
     * @access public
     * @return Image
     */
    public function captcha(){
        //查看验证码的session是否成功赋值
        //echo $_SESSION['verify'];exit;

        import("ORG.Util.Image");
        Image::buildImageVerify();
    }
	
	
	/**
	 *--------------------------------------------------------------+
	 * Action: main
	 *--------------------------------------------------------------+
	 */
	public function main()
	{
		$security_info=array();
		if(is_dir(ROOT_PATH."/install")){
			//$security_info[]="强烈建议删除安装文件夹,点击<a href='".u('public/delete_install')."'>【删除】</a>";
		}
		if(APP_DEBUG==true){
			$security_info[]="强烈建议您网站上线后，建议关闭 DEBUG （前台错误提示）";
		}	
		if(count($security_info)<=0){
			$this->assign('no_security_info',0);
		}
		else{
			$this->assign('no_security_info',1);
		}	
		$this->assign('security_info',$security_info);
        $disk_space = @disk_free_space(".")/pow(1024,2);
		$server_info = array(
		    '程序版本'     => '1.0.201305 ',
            '操作系统'     => PHP_OS,
            '运行环境'     => $_SERVER["SERVER_SOFTWARE"],	
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time').'秒',		
            '服务器域名/IP'=> $_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]'//,
            //'剩余空间'     => round($disk_space < 1024 ? $disk_space:$disk_space/1024 ,2).($disk_space<1024?'M':'G'),
		);
        $this->assign('set',$this->Setting);
		$this->assign('server_info',$server_info);	
		$this->display();
	}





    function clearCache()
    {
    	import("@.ORG.Io.Dir");
    	$dir = new Dir;
    	
    	$path = RUNTIME_PATH .'../HomeRuntime/Cache/';
    	if (is_dir($path) )
		{
			$dir->del($path);
		}
		
		echo "success";

	}

























	// 菜单页面
	public function menu(){
		//显示菜单项
		$id	=	intval($_REQUEST['tag'])==0?6:intval($_REQUEST['tag']);
		$menu  = array();
		$role_id = D('admin')->where('id='.$_SESSION['admin_info']['id'])->getField('role_id');
		$node_ids_res = D("access")->where("role_id=".$role_id)->field("node_id")->select();
		
		$node_ids = array();
		foreach ($node_ids_res as $row) {
			array_push($node_ids,$row['node_id']);
		}
		//读取数据库模块列表生成菜单项
		$node    =   M("node");
		$where = "auth_type<>2 AND status=1 AND is_show=0 AND group_id=".$id;
		$list	=$node->where($where)->field('id,action,action_name,module,module_name,data')->order('sort DESC')->select();
		foreach($list as $key=>$action) {
			$data_arg = array();
			if ($action['data']){
				$data_arr = explode('&', $action['data']);
				foreach ($data_arr as $data_one) {
					$data_one_arr = explode('=', $data_one);
					$data_arg[$data_one_arr[0]] = $data_one_arr[1];
				}
			}
			$action['url'] = U($action['module'].'/'.$action['action'], $data_arg);
			if ($action['action']) {
				$menu[$action['module']]['navs'][] = $action;
			}
			$menu[$action['module']]['name']	= $action['module_name'];
			$menu[$action['module']]['id']	= $action['id'];
		}
		$this->assign('menu',$menu);
		$this->display('left');
	}

	public function delete_install(){
		import("ORG.Io.Dir");
		$dir = new Dir;
		$dir->delDir(ROOT_PATH."/install");
		@unlink(ROOT_PATH.'/install.php');
		if(!is_dir(ROOT_PATH."/install")){
			$this->success('操作成功！');
		}
	}
}
?>