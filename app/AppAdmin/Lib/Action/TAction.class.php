<?php
/**
 * 基础Action
 */
class TAction extends Action {
	
	//启用邮件通知，1启用，0禁用
      public $open_email_msg = 1;
      
	//启用短信通知，1启用，0禁用
      public $open_sms_msg = 1;
      
      
      
	public $SiteHome = ''; ///前台首页

	public $CurrAccount = "";
	public $CurrUserRole = "";
	public $CurrUserCode = "";

	//public $CurrentManager = null; 
	//public $CurrentPermission = null;
	//public $CurrentRole = null;

	public $CacheCatalog = null;
	public $CacheNodes = null; 
	public $CacheAuthNodes = null;

	public $ModManager = '';   //管理员模型
	public $ModRole = '';      //管理员角色
	public $ModPermission = '';//管理员权限
	
	public $SettingPagingConfig  = array();
	public $SettingPagingLimit = 10;
	public $SettingPagingRoll = 10;
	
    /**
     * 初始化公用模块
     * @access private
     * @return void
     */
	private function initModule(){
		$this->ModManager = D('CmscpManager');
		$this->ModRole = D('CmscpRole');
		$this->ModPermission = D('CmscpPermission');
	}

    /**
     * 初始化后台系统配置信息
     * @access private
     * @return void
     * ToDo: 考虑把配置信息放入语言包文件
     */
	private function initSetting(){
		///获取CmsCP配置信息
		$modCmscpSetting = M('CmscpSetting');
		//$modCmscpSetting = M('cmscp_setting');
		$CmscpSetting = $modCmscpSetting->select();
		foreach ( $CmscpSetting as $val ) {
			//echo $val['key'];exit;
			//$setting[ $val['key'] ] = $val['value'];
			//$setting->$val['key'] = $val['value'];
			$field_key=$val['key'];
			$setting->$field_key = $val['value'];
		}
		$this->Setting = $setting;
		
		$this->SettingPagingLimit = empty($setting->pagination_limit) ? 10 : ($setting->pagination_limit + 0);
		$this->SettingPagingRoll  = empty($setting->pagination_roll) ? 10 : ($setting->pagination_roll + 0);
		if( !empty($setting->pagination_cfg_header) ){
			$this->SettingPagingConfig['header'] = $setting->pagination_cfg_header;
		}
		if( !empty($setting->pagination_cfg_pre) ){
			$this->SettingPagingConfig['pre'] = $setting->pagination_cfg_pre;
		}
		if( !empty($setting->pagination_cfg_next) ){
			$this->SettingPagingConfig['next'] = $setting->pagination_cfg_next;
		}
		if( !empty($setting->pagination_cfg_first) ){
			$this->SettingPagingConfig['first'] = $setting->pagination_cfg_first;
		}
		if( !empty($setting->pagination_cfg_last) ){
			$this->SettingPagingConfig['last'] = $setting->pagination_cfg_last;
		}
		if( !empty($setting->pagination_cfg_theme) ){
			$this->SettingPagingConfig['theme'] = $setting->pagination_cfg_theme;
		}
		$this->Setting = get_object_vars($setting);
	}

    /**
     * Action 初始化
     * @access protected
     * @return void
     */
	function _initialize() {		
//		//过滤所有的GET POST请求			
//		//判断是否允许ip访问
//		$banip=getBanip();
//		if($banip){			
//			foreach ($banip as $key=>$value){
//				banip($value[0], $value[1]);
//			}
//		}
//		include ROOT_PATH.'/includes/lib_common.php';	
		//$U = session('cmscp_account');
		//trace(print_r($U, true), '当前用户(S)', 'debug');


		$this->SiteHome = defined( 'CGIWWW_HOME' ) ? CGIWWW_HOME : "http://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']==80?'':':'.$_SERVER['SERVER_PORT']).__ROOT__."/../";
		$this->assign('SiteHome',$this->SiteHome);

		$this->initModule();

		$this->initSetting();

		///缓存权限用有效Node
		$this->CacheAuthNodes = $this->getCmscpNodeList(false, '', true);		
		
		///用户权限检查
		$this->CheckPriv();

		///登陆后(获得管理员的所有信息)
		//echo "<pre>";print_r($this->CurrAccount);exit;
		$this->assign('CurrAccount', $this->CurrAccount);
		//trace(print_r($this->CurrAccount,true), '当前用户(I)', 'debug');
		
		///缓存分类
		$this->CacheCatalog = $this->getCmscpCatalogList(false);
		///缓存当前用户可用的有效Node
		$this->CacheNodes = $this->getCmscpNodeList(false, isset($this->CurrUserRole) ? $this->CurrUserRole : '');
		//$this->CacheRoles = getCmscpRoleList(false);

		$this->initMenus();

		$this->assign('ShowPageHeader', true);

		$this->assign('iframe',isset($_REQUEST['iframe']) ? $_REQUEST['iframe'] : '' );
		$def=array(
			'request'=>$_REQUEST
		);	
		$this->assign('def',json_encode($def));
	}

	
    /**
     * Action 失败页面重写
     * @access protected
     * @return void
     */
	protected function error($message, $url_forward='',$ms = 3, $dialog=false, $ajax=false, $returnjs = '')
	{
		//报错后返回前一个页面，可以保留表单之前填写的信息。
		if(empty($url_forward)){
		$this->jumpUrl = 'history.back();';
		}
		else{
		$this->jumpUrl = "redirect('".$url_forward."');";
		}
		
		$this->waitSecond = $ms;
		$this->assign('dialog',$dialog);
		$this->assign('returnjs',$returnjs);
		parent::error($message, $ajax);
	}
    /**
     * Action 成功页面重写
     * @access protected
     * @return void
     */
	protected function success($message, $url_forward='',$ms = 3, $dialog=false, $ajax=false, $returnjs = '')
	{
		$this->jumpUrl = $url_forward;
		$this->waitSecond = $ms;
		$this->assign('dialog',$dialog);
		$this->assign('returnjs',$returnjs);
		parent::success($message, $ajax);
	}

	/**
	 *--------------------------------------------------------------+
	 * GeneralAction: ForList 
	 *--------------------------------------------------------------+
	 */
	protected function GeneralActionForListing( $moduleName, $queryWhere = '', $queryOrder = '', $paginglimit = '', $moduleType = 'D' , $relation = false, $groupBy = '' )
	{
		//import("ORG.Util.Page");
		import("ORG.Util.Pageadmin");

		if( $moduleType == 'D'){
			$module = D($moduleName);  ///有 module 文件的
		}else{
			$module = M($moduleName);  ///无 module 文件的
			$relation = false;
		}

		$qwhere = $queryWhere;
		//$rescount = $module->where($qwhere)->count();   //用->count()无法用->group($groupBy)->count()这种写法，故改为先获取所有记录，再用php的count()计算总数。
		if($relation){
			//$rescount_Arr = $module->relation($relation)->where($qwhere)->group($groupBy)->select();   //这句是查全表，遇到数据量大的时候，无法运作。
			$rescount_Arr = $module->field('count(*) as rescount_num')->relation($relation)->where($qwhere)->group($groupBy)->select();
		}else{
			//$rescount_Arr = $module->where($qwhere)->group($groupBy)->select();   //这句是查全表，遇到数据量大的时候，无法运作。
			$rescount_Arr = $module->field('count(*) as rescount_num')->where($qwhere)->group($groupBy)->select();
		}
		//$rescount=count($rescount_Arr);
		$rescount=empty($rescount_Arr[0]['rescount_num'])?0:$rescount_Arr[0]['rescount_num'];
		//echo $rescount;exit;
		
		if( $paginglimit !== false ){
			$paginglimit = intval($paginglimit);
			$paginglimit = $paginglimit < 1 ? $this->SettingPagingLimit : $paginglimit;
			
			//$Page = new Pageadmin($rescount, $this->SettingPagingLimit);
			$Page = new Pageadmin($rescount, $paginglimit);
			
			
			$Page->rollPage = $this->SettingPagingRoll;
			foreach($this->SettingPagingConfig as $key => $val){
				$Page->setConfig($key, $val);
			}
			//$Page->setConfig($this->SettingPagingConfig);
			$navg = $Page->show();
		}else{ $navg = ''; }

		if( $paginglimit !== false ){
			if($relation){
				$dataset = $module->relation($relation)->where($qwhere)->group($groupBy)->order($queryOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
			}else{
				$dataset = $module->where($qwhere)->group($groupBy)->order($queryOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
			}
		}else{
			if($relation){
				$dataset = $module->relation($relation)->where($qwhere)->group($groupBy)->order($queryOrder)->select();
			}else{
				$dataset = $module->where($qwhere)->group($groupBy)->order($queryOrder)->select();
			}
		}

		$sequence_number = $Page->firstRow;
		if( is_null($dataset) || $dataset  === false ){
			$dataset = array();
		}else{
			foreach($dataset as $k => $val){
				$dataset[$k]['_sequence_number_'] = ++$sequence_number;
			}
			//$this->assign('_sql_', $module->getLastSql());
			trace($module->getLastSql(), 'TAction-GeneralActionForListing-SQL');
		}

		$this->assign('dataset', $dataset);// 赋值数据集
		$this->assign('page', $navg);// 赋值分页输出
		
		$rst['dataset']=$dataset;
		$rst['navg']=$navg;
		$rst['model']=$module;
		return $rst;
	}

    /**
     * 
     * @access protected
     * @return void
     */
    public function ModuleDelete($t, $m, $ids, $fldId = 'id',  $fldStatus = 'status'){
    	if( $t == 'D' ){
			$module = D($m);
		}else{
			$module = M($m);
		}
		if( is_null($ids) ){
			return false;
		}
		if (is_array($ids)) {
			$ids = implode(',', $ids);
		} else {
			$ids = intval($ids);
		}
		trace( $ids, 'module-delete-ids');
		$query = "UPDATE %s SET %s = 255 WHERE %s in (%s)";
		return $module->execute( sprintf($query, $module->getTableName(), $fldStatus, $fldId, $ids) );
    }

    /**
     * 载入菜单系统
     * @access protected
     * @return void
     */
	protected function initMenus()
	{
		$permission = $this->CurrAccount['Permission'];
		//echo "<pre>";print_r($permission);exit;
		$role = $this->CurrUserRole;
		
		$catas = $this->CacheCatalog;
		//$nodes = $this->getCmscpNodeList(true, isset($this->CurrentRole['role']) ? $this->CurrentRole['role'] : '');//$this->CacheNodes;
		//echo $this->CurrUserRole;exit;
		$nodes = $this->getCmscpNodeList(true, $this->CurrUserRole);
		//echo "<pre>";print_R($nodes);exit;
		//trace(print_r($catas,true), 'Init-Menus-Catas', 'debug');
		//trace(print_r($nodes,true), 'Init-Menus-Nodes', 'debug');
		//trace(print_r($permission, true), '当前用户权限(@MENU)', 'debug');
		
		$menus = array();
		
		foreach($nodes as $node){
			
			$yes = ($role == 'administrator') || isset( $permission[ $node['node_id'] ]);
			if( $node['group'] == 'Y' && $node['catalog']=='index' && $node['module'] == 'public' ){
				$yes = true;
			}else if( $node['group'] == 'Y' && $node['catalog']=='index' && $node['module'] == 'index' ){
				$yes = true;
			}else if( $node['group'] == 'N' && $node['catalog']=='index' && $node['module'] == 'index' && $node['action']=='index' ){
				$yes = true;
			}else if($node['group'] != 'Y' && $node['catalog']=='index' && $node['module'] == 'public' && $node['action'] == 'main' ){
				$yes = true;
			}else if($node['group'] == 'Y'){
				$yes = true;				
			}
			
			//trace( "[". ($yes?'Y':'N'). "]". $node['node_id'] ."=". $node['type'] .",". $node['group'] .":". $node['catalog'] ."/". $node['module'] ."/". $node['action'], 'TActioin-IniMenus-', 'debug');
			//trace( (isset( $permission[ $node['node_id'] ]) ? "@Permission" : "!Permission"), "TActioin-IniMenus-", 'debug' );	
			if( $yes ){
				$ctitle = '';
				if( isset($catas[ $node['catalog'] ]) ){
					$ctitle = $catas[ $node['catalog'] ]['title'];
					//echo $ctitle."<br>";
//					trace( $ctitle, 'Nodes - Cat -', 'debug');
				}
//				foreach($catas as $cat){
//					if( $cat['catalog'] == $node['catalog'] ){
//						$ctitle = $cat['title'];
//						break;
//					}
//				}
				$menus[ $node['catalog'] ]['title'] = $ctitle;
				//$menus[ $node->catalog ]['menus'][] = $node;
//				trace( $node['catalog'], 'Nodes - Grp - C', 'debug');
//				trace( $node['module'], 'Nodes - Grp - M', 'debug');
//				trace( $node['action'], 'Nodes - Grp - A', 'debug');
//				trace( $node['type'], 'Nodes - Grp - T', 'debug');
//				trace( $node['module_name'], 'Nodes - Grp - T', 'debug');
				if( $node['group'] == 'Y' ){
//					trace( $node['node_id'], 'Nodes - Grp - Y', 'debug');
					//$mitem = array();
					$menus[ $node['catalog'] ]['-links'][$node['module']]['class'] = ( $node['type'] == 0 ) ? 'link' : $node['module'];
					$menus[ $node['catalog'] ]['-links'][$node['module']]['title'] = ( $node['type'] == 0 ) ? $node['title'] : $node['module_name'];
					$menus[ $node['catalog'] ]['-links'][$node['module']]['link'] = ( $node['type'] == 0 ) ? $node['link'] : (U($node['module'].'/'.$node['action']) . $node['param']);
					//$menus[ $node['catalog'] ]['-links'][$node['module']] = $mitem;
				}else{
//					trace( $node['node_id'], 'Nodes - Grp - N', 'debug');
					$mitem = array();
					$mitem['class']  = ( $node['type'] == 0 ) ? 'link' : $node['module'].'-'.$node['action'];
					$mitem['title'] = ( $node['type'] == 0 ) ? $node['title'] : $node['action_name'];
					if( $node['module'] == 'index' && $node['action']=='index' ){
					$mitem['link'] = ( $node['type'] == 0 ) ? $node['link'] : (U('public/main') . $node['param']);
					}else{
					$mitem['link'] = ( $node['type'] == 0 ) ? $node['link'] : (U($node['module'].'/'.$node['action']) . $node['param']);
					}
					$menus[ $node['catalog'] ]['-links'][$node['module']]['-links'][] = $mitem;
				}
				
			}
			
		}
		
		//echo "<pre>";print_r($menus);exit;
		//trace( print_r($menus, true), 'TAction-Menus-No1', 'debug');
		foreach($menus as $CatKey => $Cat){
			if( !isset($Cat['-links']) || empty($Cat['-links'])){
				//$menus[$CatKey] = '';
				unset($menus[$CatKey]);
				continue;
			}
			foreach($Cat['-links'] as $GrpKey => $Grp){
				if( !isset($Grp['-links']) || empty($Grp['-links'])){
					//trace($CatKey .' / '. $GrpKey, '(menu-unset-mod)', 'debug');
					unset($menus[$CatKey]['-links'][$GrpKey]);
					//continue;
				}
			}
			if( !isset($menus[$CatKey]['-links']) || empty($menus[$CatKey]['-links'])){
				//trace($CatKey .' -', '(menu-unset-cat)', 'debug');
				unset($menus[$CatKey]);
				continue;
			}
			
		}
		//trace( print_r($menus, true), 'TAction-Menus-No2', 'debug');
		
		//echo "<pre>";print_r($menus);exit;
		
		//上方菜单按sway_cmscp_catalog的sort排序
		$ar_menus=array();
		foreach($catas as $cata){
			if( isset($menus[$cata['catalog']]) ){
				$ar_menus[$cata['catalog']]=$menus[$cata['catalog']];
			}
		}
		$menus=$ar_menus;
		
		//echo "<pre>";print_r($menus);exit;
		$this->assign('Menus', $menus);
		//trace( print_r($menus, true), 'TAction-Menus-CUser', 'debug');

	}


	//检查权限
	public function CheckPriv()
	{
		if( !session('?cmscp_login') && !in_array(ACTION_NAME, array('login','captcha', 'login_agent')) ){
			$this->redirect('public/login');
		}
		$this->CurrAccount = session('cmscp_account');
		$this->CurrUserRole = $this->CurrAccount['Account-Role'];
		$this->CurrUserCode = $this->CurrAccount['Account-Code'];

		/// 还要判断role 是否有效
		$CurrentRole = D('CmscpRole')->field('role_id', 'role',  'role_name', 'ststus')->where("role = '%s'", $this->CurrUserRole)->find();
		if( is_null($CurrentRole) || $CurrentRole === false){
			$CurrentRole = array();
		}
		$CurrentPermission = array();
		if( isset($CurrentRole['status']) && $CurrentRole['status'] == 1 ){
			$CurrentPermission = $this->getCmscpRolePermission(false, $this->CurrUserRole, false);
		}
		
		$this->CurrAccount['Role'] = $CurrentRole;
		$this->CurrAccount['Permission'] = $CurrentPermission;
		//trace(print_r($this->CurrAccount, true), '当前用户ACC(C)', 'debug');
		
		//排除一些不必要的权限检查
		foreach (C('IGNORE_PRIV_LIST') as $key=>$val){
			if(MODULE_NAME==$val['module_name']){
				if(count($val['action_list'])==0)return true;

				foreach($val['action_list'] as $action_item){
					if(ACTION_NAME==$action_item)return true;
				}
			}
		}
		if( MODULE_NAME == 'index' && ACTION_NAME == 'index' ){
			return true;
		}
		if(MODULE_NAME == 'public' &&  ACTION_NAME == 'main'){
			return true;
		} 
		//$permission = session('cmscp_permission');
		if( $this->CurrUserRole == 'administrator' ){
			return true;
		}

		$nodes = $this->CacheAuthNodes;
		foreach( $nodes as $node){
			if( $node['module'] == MODULE_NAME && $node['action'] == ACTION_NAME){
				if(isset($CurrentPermission[ $node['node_id'] ])){
					return true;
				}else{
					//if( $node['note'] == 'ajax'){
					if( substr(ACTION_NAME, 0, 5) == 'ajax_'){
						$this->ajaxReturn(array('status' => '', 'error'=>'no-permission'));
					}else{
						$this->error(L('_VALID_ACCESS_'));
					}
					return false;
				}
				break;
			}
		}
		return true;
		///无注册模块
	}


	public function getCmscpCatalogList($all = false){
		$mode = D('CmscpCatalog');
		if( $all ){
			$sqlWhere = 'status < 250';
		}else{
			$sqlWhere = 'status = 1';
		}
		$data = $mode->field('id, catalog, title, status')->where($sqlWhere)->order('sort ASC')->select();
		$cats = array();
		if(!is_null($data) && $data !== false){
			foreach($data as $dat){
				$cats[ $dat['catalog'] ] = $dat;
			}
		}
		return $cats;
	}
	
	public function getCmscpNodeList($all = false, $role = '', $withAction = false){
		//var_dump($role);echo "<br>";
		$mode = D('CmscpNodes');
		if( $all ){
			if( $role == 'administrator' ){
				$sqlWhere = '(status = 1 or status = 0 or status = 9)';
			}else{
				$sqlWhere = '(status = 1 or status = 0)';
			}
		}else{
			if( $role == 'administrator' ){
				$sqlWhere = '(status = 1 or status = 9)';
			}else{
				$sqlWhere = 'status = 1';
			}
		}
		if( !$withAction ){
			$sqlWhere .= " and `type` < 3";
		}else{
			$sqlWhere .= " and `type` <= 3";
		}
		$data  = $mode->where($sqlWhere)->order('catalog, module, sort')->select();
		$nodes = array();
		if(!is_null($data) && $data !== false){
			foreach($data as $node){
				$nodes[ $node['node_id'] ] = $node;
			}
		}
		return $nodes;
	}
	
	public function getCmscpRoleList($all = false, $byKey = true){
		$mode = D('CmscpRole');
		if( $all ){
			$sqlWhere = 'status < 250';
		}else{
			$sqlWhere = 'status = 1';
		}
		$data  = $mode->where($sqlWhere)->order('sort')->select();
		$roles = array();
		if(!is_null($data) && $data !== false){
			foreach($data as $role){
				if( $byKey ){
					$roles[ $role['role'] ] = $role;
				}else{
					$roles[ $role['role_id'] ] = $role;
				}
			}
		}
		return $roles;
	}
	
	public function getCmscpRolePermission($all = false, $role = '', $roleIsId = false){
		$mode = D('CmscpPermission');
		if( $all ){
			$sqlWhere = 'access < 250';
		}else{
			$sqlWhere = 'access = 1';
		}
		if( $roleIsId ){
			$data  = $mode->where($sqlWhere.' and role_id = %d', $role)->select();
		}else{
			$data  = $mode->where($sqlWhere." and role = '%s'", $role)->select();
		}
		//$data  = $mode->where($sqlWhere)->select();
		$pers = array();
		if( !is_null($data) && $data !== false){
			foreach($data as $per){
				$pers[ $per['node_id'] ] = $per;
			}
		}
		return $pers;
	}
	
	
	public function setPagething($PageTitle = '', $PageMenu = array(), $ShowPageHeader = true){
		$this->assign('PageTitle', $PageTitle);
		$this->assign('PageMenu', $PageMenu);
		$this->assign('ShowPageHeader', $ShowPageHeader);
	}
	
	public function REQUEST($name, $def = ''){
		$val = '';
		if( isset($_POST[$name])){
			$val = $_POST[$name];
		}else if( isset($_GET[$name])){
			$val = $_GET[$name];
		}
		if( is_string($val) && $val == '' ){
			return $def;
		}
		return $val;
	
	}
	public function fixSQL($str) {
		return addslashes($str);
	}	
	


    /**
     * 系统邮件发送函数
     * @param string $to    接收邮件者邮箱
     * @param string $name  接收邮件者名称
     * @param string $subject 邮件主题 
     * @param string $body    邮件内容
     * @param string $attachment 附件列表
     * @param string $ssl 代表 SMTPSecure 的属性。默认为0。如果是ssl写1，如果是tls写2。
     * @return boolean 
     * 调用方式：
     $this->think_send_mail($to, $name, $subject, $body);  //无验证
     $this->think_send_mail($to, $name, $subject, $body, 1);  //ssl验证
     $this->think_send_mail($to, $name, $subject, $body, 2);  //tls验证
     */
    function think_send_mail($to, $name, $subject = '', $body = '', $ssl=0,  $attachment = null){
        
        $config = C('THINK_EMAIL');
        
        vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
        $mail             = new PHPMailer(); //PHPMailer对象
        $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();  // 设定使用SMTP服务
        $mail->SMTPDebug  = 0;                     // 0 关闭SMTP调试功能
                                                   // 1 = errors and messages
                                                   // 2 = messages only
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        
        if($ssl==1){
        	$mail->SMTPSecure = 'ssl';                 // 使用安全协议
        }
        elseif($ssl==2){
        	$mail->SMTPSecure = 'tls';                 // 使用安全协议
        }
        else{
        }
        
        $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
        $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
        $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
        $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
        $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
        $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
        $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject    = $subject;
        
        $mail->MsgHTML($body);
        $mail->AddAddress($to, $name);
        if(is_array($attachment)){ // 添加附件
            foreach ($attachment as $file){
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        
        return $mail->Send() ? true : $mail->ErrorInfo;
    }





	public function html2text($str,$encode = 'utf-8'){
	  $str = preg_replace("/<style .*?<\/style>/is", "", $str);
	  $str = preg_replace("/<script .*?<\/script>/is", "", $str);
	  $str = preg_replace("/<br \s*\/?\/>/i", "\n", $str);
	  $str = preg_replace("/<\/?p>/i", "\n\n", $str);
	  $str = preg_replace("/<\/?td>/i", "\n", $str);
	  $str = preg_replace("/<\/?div>/i", "\n", $str);
	  $str = preg_replace("/<\/?blockquote>/i", "\n", $str);
	  $str = preg_replace("/<\/?li>/i", "\n", $str);

	  $str = preg_replace("/\&nbsp\;/i", " ", $str);
	  $str = preg_replace("/\&nbsp/i", " ", $str);
	  
	  $str = preg_replace("/\&amp\;/i", "&", $str);
	  $str = preg_replace("/\&amp/i", "&", $str);
	  
	  $str = preg_replace("/\&lt\;/i", "<", $str);
	  $str = preg_replace("/\&lt/i", "<", $str);
	  
	  $str = preg_replace("/\&ldquo\;/i", '"', $str);
	  $str = preg_replace("/\&ldquo/i", '"', $str);

	    $str = preg_replace("/\&lsquo\;/i", "'", $str);
	    $str = preg_replace("/\&lsquo/i", "'", $str);

	    $str = preg_replace("/\&rsquo\;/i", "'", $str);
	    $str = preg_replace("/\&rsquo/i", "'", $str);

	  $str = preg_replace("/\&gt\;/i", ">", $str); 
	  $str = preg_replace("/\&gt/i", ">", $str); 

	  $str = preg_replace("/\&rdquo\;/i", '"', $str); 
	  $str = preg_replace("/\&rdquo/i", '"', $str); 

	  $str = strip_tags($str);
	  $str = html_entity_decode($str, ENT_QUOTES, $encode);
	  $str = preg_replace("/\&\#.*?\;/i", "", $str);


	$str=str_replace("·","",$str);

	  return $str;
	}



    ////检查并重设重复文件名
    function checkFileName($filefolder,$filename){
        $i = 1;
        while (file_exists($filefolder."/".$filename)){
            $fn = $filename;
            $dotpos = strrpos($fn,".");
            $mainfn = substr($fn,0,$dotpos);
            $exfn = substr($fn,$dotpos+1,strlen($fn));
            $leftpos = strrpos($mainfn,"[");
            $rightpos = strrpos($mainfn,"]");
            if ($leftpos === false && $rightpos === false){
                $filename = $mainfn."[".$i."]".".".$exfn;
            }else{
                $signnum = substr($mainfn,$leftpos,$rightpos-$leftpos+1);
                $simplenum = substr($signnum,1,strlen($signnum)-2);
                if (is_numeric($simplenum)){
                    $mainfn = str_replace($signnum,"",$mainfn);
                }
                $filename = $mainfn."[".$i."]".".".$exfn;
            }
            $i++;
        }
        return $filename;
    }
    //上传文件
    function uploadImg($photeDir,$temp_name,$file_name)
    {
        $imgPath = $photeDir . '/' . $file_name;
        $dPath = $imgPath;
        @move_uploaded_file($temp_name, $dPath);
    }
    
    
    
	//取客户端IP地址
	public function get_customer_ip(){

	  if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	           $ip = getenv("HTTP_CLIENT_IP");
	       else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	           $ip = getenv("HTTP_X_FORWARDED_FOR");
	       else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	           $ip = getenv("REMOTE_ADDR");
	       else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	           $ip = $_SERVER['REMOTE_ADDR'];
	       else
	           $ip = "unknown";
	       
	$user_IP=$ip;

	return $user_IP;
	}
	
	
	
	/**
	 * 取得文本摘要(按宽度)
	 * @param str $txt			原文本
	 * @param int $width		截取宽度
	 * @param real $zhCharWidth	中文字宽
	 * @param real $enCharWidth	英文字宽
	 * @return str
	 */
	static public function getSummaryByWidth($txt,$width,$zhCharWidth=1.2,$enCharWidth=0.7,$suffix='...')
	{	
		$data = '';
		$w = 0;
		$len = mb_strlen($txt,'UTF-8');
		for($i=0;$i<$len;$i++)
		{
			$char = mb_substr($txt,$i,1,'UTF-8');
			if(strlen($char)==1) $w += $enCharWidth;
			else $w += $zhCharWidth;
			$data .= $char;

			if(($w+strlen($suffix))>=$width)
			{
				if($i<($len-1))
				{
					$data .= $suffix;
					break;					
				}
			}
		}

		return $data;
	}




	//写日志
	public function setlog($board,$title,$op_type,$create_user=''){
    	$ip_address=$this->get_customer_ip();
    	if($create_user==''){
    		$create_user=$this->CurrAccount['User']['username'];
    	}
    	$logMod = M('log');
		$table_name=$logMod->getTableName();
		$sql="insert into ".$table_name." SET title='".addslashes($title)."'
		, board='".addslashes($board)."'
        , op_type='".addslashes($op_type)."'
        , create_user='".addslashes($create_user)."'
        , create_time='".time()."'
        , ip_address='".addslashes($ip_address)."'
         ";
        $result = $logMod->execute($sql);
    }
    
    
    //生成 唯一码 兑换码 （来自公司封装的/Domain/File/Print方法）
    public function shortCode($input){
        $base32 = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l', 'm', 'n', 'w', 'p',
            'q', 'r', 's', 't', 'u', 'v', 'x', 'y',
            '2', '3', '4', '5', '6', '7', '8', '9',
        );
		
		
        $hex = md5($input);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;
        $output = array();

        for ($i = 0; $i < $subHexLen; $i++) {
            $subHex = substr ($hex, $i * 8, 8);
            $int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
            $out = '';

            for ($j = 0; $j < 6; $j++) {
                $val = 0x0000001F & $int;
                $out .= $base32[$val];
                $int = $int >> 5;
            }

            $output[] = $out;
        }
        
        
        //默认6位，如果要加n位随机值，则n写大于0的值
        $current_output=$output[0];
        $n_length=2;
        
        for ($i = 0; $i < $n_length; $i++) {
        	$get_key=rand(0, 31);
        	$current_output=$current_output.$base32[$get_key];
        }
        
        $current_output=strtoupper($current_output);
		//echo $current_output;exit;
        
        return $current_output;
    }
    
    
    
    
    //模拟GET请求
    public function http_request_url_get($url='',$para=array())
    {
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt ($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt ($ch, CURLOPT_POSTFIELDS,$para);
        curl_setopt ($ch, CURLOPT_VERBOSE, 0);
        $result = curl_exec($ch);

        if(!$result){
            return '请求失败';
        }
        $result = $this->decode($result);
        return $result;
    }

	//模拟POST请求
    public function http_request_url_post($url='',$para=array())
    {
    	$this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");  //调用第三方短信发送接口，遇到中文乱码的问题，可能可以启用这句，或许可以解决。
        
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_HEADER, false);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt ($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 4);
        
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $this_header);  //调用第三方短信发送接口，遇到中文乱码的问题，可能可以启用这句，或许可以解决。
        
        curl_setopt ($ch, CURLOPT_POSTFIELDS,http_build_query($para));
        curl_setopt ($ch, CURLOPT_VERBOSE, 0);
        $result = curl_exec($ch);

        if(!$result){
            return '请求失败';
        }
        
        $result = json_decode($result,true);
        //$result = $this->decode($result);
        //echo "<pre>";print_r($result);exit;
        return $result;
    }
    

    public function decode($data)
    {
        if(is_array($data))
        {
            foreach($data as $key=>$value)
            {
                $data[$key] = $this->decode($value);
            }
        }
        else
        {
            $data2 = json_decode($data,true);
            if($data2)
            {
                if(is_array($data2)) $data = $this->decode($data2);
            }
        }
        return $data;
    }
	
	
	
    
    
    //过滤xss  <script>alert(1);</script>
    public function remove_xss($val) {
	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	   // this prevents some character re-spacing such as <java\0script>
	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
	   // straight replacements, the user should never need these since they're normal characters
	   // this prevents like <IMG SRC=@avascript:alert('XSS')>
	   $search = 'abcdefghijklmnopqrstuvwxyz';
	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   $search .= '1234567890!@#$%^&*()';
	   $search .= '~`";:?+/={}[]-_|\'\\';
	   for ($i = 0; $i < strlen($search); $i++) {
	      // ;? matches the ;, which is optional
	      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
	      // @ @ search for the hex values
	      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
	      // @ @ 0{0,7} matches '0' zero to seven times
	      $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	   }
	   // now the only remaining whitespace attacks are \t, \n, and \r
	   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	   $ra = array_merge($ra1, $ra2);
	   $found = true; // keep replacing as long as the previous round replaced something
	   while ($found == true) {
	      $val_before = $val;
	      for ($i = 0; $i < sizeof($ra); $i++) {
	         $pattern = '/';
	         for ($j = 0; $j < strlen($ra[$i]); $j++) {
	            if ($j > 0) {
	               $pattern .= '(';
	               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
	               $pattern .= '|';
	               $pattern .= '|(�{0,8}([9|10|13]);)';
	               $pattern .= ')*';
	            }
	            $pattern .= $ra[$i][$j];
	         }
	         $pattern .= '/i';
	         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
	         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
	         if ($val_before == $val) {
	            // no replacements were made, so exit the loop
	            $found = false;
	         }
	      }
	   }
	   return $val;
	}


    
	public function checkOrderTotalAmout(){
		
		//修复订单总金额变成0的数据
		$op_time = date("2016-09-01 00:00:00");
		// select * from hs_order where amount_total='0' and status=1 and isPay=1 and createDateTime>='2016-09-01 00:00:00' 
		
    	$OrderMod = M('order');
	    $order_big = $OrderMod->where(" amount_total='0' and status=1 and isPay=1 and createDateTime>='".$op_time."' " )->group('order_no')->select();
	    //echo "<pre>";print_r($order_list);echo "<pre>";exit;
	    
	    
	    if(!empty($order_big)){
	    	foreach($order_big as $k_big=>$v_big){
	    		
	    		//echo "<pre>";print_r($v_big);echo "<pre>";exit;
	    		
	    		
	    		
				if(isset($v_big['ticket_type']) && $v_big['ticket_type']==2){
					//通票情况，需要显示订单里的循环信息。并提示是否继续选择or确认订单 如继续选择，跳ticket_start页；如确认订单，生成订单号order_no，并跳支付页。
					
					$order_no = $v_big['order_no'];
					
					
					//获取分站信息及票信息：
					$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$v_big['stage_id'];
					//echo $api_url;exit;
					$api_para=array();
					$api_result=$this->http_request_url_post($api_url,$api_para);
					//echo "<pre>";print_r($api_result);exit;
					$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
					//echo "<pre>";print_r($RaceStageInfo);exit;
					//$this->assign('RaceStageInfo', $RaceStageInfo);
					$stru=$RaceStageInfo['comment']['RaceStructure'];
					//echo $stru;exit;  //group or race 模式。
					
					
					
					//计算当前总价
					
					$and_cond='';
					$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
					$orderMod = M('order');
			        $order_all = $orderMod->where(" 1 ".$and_cond )->select();
			        //echo "<pre>";print_r($order_all);exit;
			        $order_all=empty($order_all)?array():$order_all;
			        $this->assign('order_all', $order_all);
					
					
					$all_amount_total=0;   //最后全部的总价，含产品和比赛。
					$arr_order_id=array();   //所有参加非精英的人，含个人和团队，订单流水order_id
					$arr_people=array();  //所有参加非精英的人，含个人和团队
			        //加上历史循环金额
			        $tongpiao_price=0;  //最终计算的通票的钱
					if(!empty($order_all)){
						foreach($order_all as $k=>$v){
							
							//echo "<pre>";print_r($v);exit;
							
							
							
							
							
							
							
							//非精英部分
							if($v['price_type']==2){
								$arr_order_id[]=$v['id'];
								
								//选了 非精英 通票的总共多少独立人，含个人和团队
								if($v['user_type']==1){
									$push_value=$v['m_realname']."|".$v['m_id_type']."|".$v['m_id_number'];
									
									if (!in_array($push_value, $arr_people)) {
										$arr_people[]=$push_value;
									}
								}
								else{
									//团队
									$and_cond='';
									$and_cond=$and_cond.' and order_id="'.addslashes($v['id']).'" ' ;
									$and_cond=$and_cond.' and member_id="'.addslashes($v['user_id']).'" ';
									//echo $and_cond;exit;
									$groupBy= 't_realname,t_id_type,t_id_number ';
									$order_teamMod = M('order_team');
							        $order_team_list_groupby = $order_teamMod->where(" 1 ".$and_cond )->group($groupBy)->select();
							        //echo "<pre>";print_r($order_team_list_groupby);exit;
							        //$chengyuan_number=count($order_team_list_groupby);
							        //echo $chengyuan_number;exit;
							        if(!empty($order_team_list_groupby)){
							        	foreach($order_team_list_groupby as $k_team_person=>$v_team_person){
							        		$push_value=$v_team_person['t_realname']."|".$v_team_person['t_id_type']."|".$v_team_person['t_id_number'];
							        		
							        		if (!in_array($push_value, $arr_people)) {
												$arr_people[]=$push_value;
											}
											
							        	}
							        }
								}
							}
							
							//精英部分
							if($v['price_type']==1){
								
								
								//计算人头数，去掉重复的人。
								$chengyuan_number=1;
								if($v['user_type']==2){
									$and_cond='';
									$and_cond=$and_cond.' and order_id="'.addslashes($v['id']).'" ' ;
									$and_cond=$and_cond.' and member_id="'.addslashes($v['user_id']).'" ';
									//echo $and_cond;exit;
									$groupBy= 't_realname,t_id_type,t_id_number ';
									$order_teamMod = M('order_team');
							        $order_team_list_groupby = $order_teamMod->where(" 1 ".$and_cond )->group($groupBy)->select();
							        //echo "<pre>";print_r($order_team_list_groupby);exit;
							        $chengyuan_number=count($order_team_list_groupby);
							        //echo $chengyuan_number;exit;
								}
								
								
								$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$v['race_id'];
								//echo $api_url;echo "<br>";exit;
								$api_para=array();
								$raceInfo=$this->http_request_url_post($api_url,$api_para);
							    //echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
								//echo "<pre>";print_r($raceInfo['RaceInfo']['PriceList']);echo "</pre>";exit;
								
								if($v['user_type']==2){
									//团队报名
									$x_price=$raceInfo['RaceInfo']['PriceList'][2];
									
								}
								else{
									//个人报名
									$x_price=$raceInfo['RaceInfo']['PriceList'][1];
									
								}
								
								
								
								$price_race=$x_price*$chengyuan_number;
								
								$all_amount_total=$all_amount_total+$price_race;
								
							    
							}
							
							
							//算上产品价格
							$all_amount_total=$all_amount_total+$v['price_product'];
							
							
						}
						
					}
					
					
					//计算非精英部分的总价
					if(!empty($arr_people)){
						
						$chengyuan_number=count($arr_people);
						
						if(isset($RaceStageInfo['comment']['PriceList']) && !empty($RaceStageInfo['comment']['PriceList'])){
							
					        $PriceList=$RaceStageInfo['comment']['PriceList'];
					        //echo "<pre>";print_r($PriceList);exit;
							
							
					        ksort($PriceList);
					        //echo "<pre>";print_r($PriceList);exit;
					        
					        $x_price=$PriceList[1];
					        foreach($PriceList as $k=>$v){
					        	if($chengyuan_number>=$k){
					        		$x_price=$v;
					        	}
					        }
						    
							
							//echo $x_price;exit;
							
							$price_race=$x_price*$chengyuan_number;
							
							$all_amount_total=$all_amount_total+$price_race;
							
							//echo $price_race;exit;
							
							
						}
						else{
							//不可能走到这里
							//exit;
							break;
						}
						
					}
					
					//echo $price_race;exit;
					$str_order_id=implode(",", $arr_order_id);
					
					
					//写入全部的总价
					//用最新的总价 all_amount_total 更新到每个循环体的 amount_total 字段里
				    $orderMod = M('order');
					$sql=sprintf("update %s SET amount_total='".addslashes($all_amount_total)."'  
				    where order_no='".addslashes($order_no)."'  
				    ", $orderMod->getTableName() );
				    //echo $sql;echo "<br>";
				    $result = $orderMod->execute($sql);
				    
				    
				}
				else{
					//单票情况，生成订单号order_no，并立即跳到支付页
					
					$order_no = $v_big['order_no'];
					
					
					
					//获取分站信息及票信息：
					$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$v_big['stage_id'];
					//echo $api_url;exit;
					$api_para=array();
					$api_result=$this->http_request_url_post($api_url,$api_para);
					//echo "<pre>";print_r($api_result);exit;
					$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
					//echo "<pre>";print_r($RaceStageInfo);exit;
					//$this->assign('RaceStageInfo', $RaceStageInfo);
					$stru=$RaceStageInfo['comment']['RaceStructure'];
					//echo $stru;exit;  //group or race 模式。
					
					
					
					//计算人头数，去掉重复的人。
					$chengyuan_number=1;
					if($v_big['user_type']==2){
						$and_cond='';
						$and_cond=$and_cond.' and order_id="'.addslashes($v_big['id']).'" ' ;
						$and_cond=$and_cond.' and member_id="'.addslashes($v_big['user_id']).'" ';
						//echo $and_cond;exit;
						$groupBy= 't_realname,t_id_type,t_id_number ';
						$order_teamMod = M('order_team');
				        $order_team_list_groupby = $order_teamMod->where(" 1 ".$and_cond )->group($groupBy)->select();
				        //echo "<pre>";print_r($order_team_list_groupby);exit;
				        $chengyuan_number=count($order_team_list_groupby);
				        //echo $chengyuan_number;exit;
					}
					
					
					$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$v_big['race_id'];
					//echo $api_url;echo "<br>";exit;
					$api_para=array();
					$raceInfo=$this->http_request_url_post($api_url,$api_para);
				    //echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
					//echo "<pre>";print_r($raceInfo['RaceInfo']['PriceList']);echo "</pre>";exit;
					
					
					if(empty($raceInfo['RaceInfo']['PriceList'])){
						//非精英
						
						if(isset($RaceStageInfo['comment']['PriceList']) && !empty($RaceStageInfo['comment']['PriceList'])){
							
					        $PriceList=$RaceStageInfo['comment']['PriceList'];
					        
							if($v_big['user_type']==2){
								//团队报名
								//$x_price=$raceInfo['RaceInfo']['PriceList'][2];
								
						        ksort($PriceList);
						        //echo "<pre>";print_r($PriceList);exit;
						        
						        $x_price=$PriceList[1];
						        foreach($PriceList as $k=>$v){
						        	if($chengyuan_number>=$k){
						        		$x_price=$v;
						        	}
						        }
						        
								
							}
							else{
								//个人报名
								$x_price=$PriceList[1];
								
							}
							
							
						}
						else{
							//不可能走到这里
							//exit;
							break;
						}
					}
					else{
						//精英
						
						if($v_big['user_type']==2){
							//团队报名
							$x_price=$raceInfo['RaceInfo']['PriceList'][2];
							
						}
						else{
							//个人报名
							$x_price=$raceInfo['RaceInfo']['PriceList'][1];
							
						}
						
					}
					
					
					$price_race=$x_price*$chengyuan_number;
					
					$amount_total=$price_race+$v_big['price_product'];
					
					$orderMod = M('order');
			    	$sql=sprintf("update %s SET amount_total='".addslashes($amount_total)."' 
				    where id='".addslashes($order_id)."' 
				    ", $orderMod->getTableName() );
				    //echo $sql;echo "<br>";
				    $result = $orderMod->execute($sql);
					
					
				}
	    		
	    		
	    		
	    	}
	    }
	    
	    
	    //exit;
	    
	    
	    
    	
    }
    
    
    
	//写入数据库sql语句日志
	public function set_log_sql($sql_txt){
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		
		$log_sqlMod = M('log_sql');
        $sql=sprintf("INSERT %s SET 
         addtime='".addslashes($addtime)."' 
        , sql_txt='".addslashes($sql_txt)."' 
        ", $log_sqlMod->getTableName() );
        //echo $sql;exit;
        $result = $log_sqlMod->execute($sql);
	    
		
	}
	


}

?>
