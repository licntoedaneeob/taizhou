<?php
/**
 * 简单代理商系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class agentAction extends TAction
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

        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='删除'){
            if( empty($_POST['id']) ){
                $this->error(L('请选择要删除的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }

            //删除连带数据
			$AgentAddressMod = M('agent_address');
            $sql=" agent_id in (".$in.") ";
            $AgentAddressMod->where($sql)->delete();


            //删除自身数据
            $AgentMod = M('agent');
            $sql=" id in (".$in.") ";
            $AgentMod->where($sql)->delete();

            $this->success('删除成功', U('agent/listing'));
            exit;
        }

        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		$sqlOrder = "id DESC";


		$filter_state = $this->REQUEST('_filter_state');
		if( $filter_state != '' ){
			$sqlWhere .= " and status = ". intval($filter_state)." ";
		}

        $f_search = $this->REQUEST('_f_search');
		if( $f_search != '' ){
			$sqlWhere .= " and (username like '%". $this->fixSQL($f_search)."%' or agent_name like '%". $this->fixSQL($f_search)."%' or address like '%". $this->fixSQL($f_search)."%')";
		}

        $this->ModManager = M('agent');
        $f_order = $this->REQUEST('_f_order', 'id');
        $fields = $this->ModManager->getDbFields();
        if( in_array($f_order, $fields) ){
            $sqlOrder = $f_order . ' ';
        }else{
            $sqlOrder = 'id ';
        }
        $f_direc = strtoupper($this->REQUEST('_f_direc'));if($f_direc==""){$f_direc='DESC';}
        if( $f_direc != 'DESC' ){
            $sqlOrder .= 'ASC';
        }else{
            $sqlOrder .= 'DESC';
        }

		///回传过滤条件
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

		///获取列表数据集		
		$rst=$this->GeneralActionForListing('agent', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('代理商列表');
		$PageMenu = array(
			array( U('agent/create'), L('添加代理商') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}

	/**
	 *--------------------------------------------------------------+
	 * Action: create
	 *--------------------------------------------------------------+
	 */
	public function create()
	{
    	
		if(isset($_POST['dosubmit'])){

	        $AgentMod = M('agent');
			
			$rst=$this->CheckAgentData_Post();
			
			if (false === $AgentMod->create()) {
				$this->error($module->getError());
			}
			
			
	        if($AgentMod->create()) {
	        	
	        	//使用 $AgentMod->email
        		$rst=$this->CheckAgentData_Mod($AgentMod);
	        	$AgentMod->create_time=time();
	        	$AgentMod->password=md5($AgentMod->password);
	        	
	        	$result =   $AgentMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($AgentMod->getError());
	        }
			
		}else{

			$PageTitle = L('添加代理商');
			$PageMenu = array(
					array( U('agent/listing'), L('代理商列表') ),
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
    	///注意：老代理商代理商已填写情况下不允许修改代理商名，代理商名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复
		
		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            $rst=$this->CheckAgentData_Post($id);

        	///密码为空则不修改密码
        	if( isset($_POST['password'])){
                if($_POST['password'] != '' ){
        		    $_POST['password'] = md5($_POST['password']);
        	    }
        	    else{
                    unset( $_POST['password'] );
        	    }
            }

	        $AgentMod = M('agent');

	        if($AgentMod->create()) {
	        	
                $rst=$this->CheckAgentData_Mod($AgentMod,$id);
                $AgentMod->modify_time=time();

	            $result =   $AgentMod->save();
	            if($result) {
	                $this->success('操作成功！', U('agent/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($AgentMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $AgentMod = M('agent');
		    // 读取数据
		    $data =   $AgentMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('代理商数据读取错误');
		    }
    
			$PageTitle = L('编辑代理商');
			$PageMenu = array(
					array( U('agent/create'), L('添加代理商') ),
					array( U('agent/listing'), L('代理商列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
	}
	


	/**
	 * 修改状态
	 * @access public
	 * @return json
	 */
	public function ajax_change_status()
	{
		$module = $AgentMod = M('agent');
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

/*
    function delete()
    {
        if( empty($_POST['id']) ){
            $this->error(L('请选择要删除的数据！'));
        }

        if(is_array($_POST['id'])){
            $ids = $_POST['id'];
            $in = implode(",",$_POST['id']);
        }else{
            $ids[] = $_POST['id'];
            $in = $_POST['id'];
        }

        //删除连带数据

        //删除自身数据
        $AgentMod = M('agent');
        $sql=" id in (".$in.") ";
        $AgentMod->where($sql)->delete();

        $this->success('删除成功', U('agent/listing'));
    }
*/

	private function CheckAgentData_Post($agent_id=0){
		///检查 $_POST 提交数据
		
			$AgentMod = M('agent');

			$result = $AgentMod->where("username='%s' and id!=%d ", $_POST['username'], $agent_id )->count();
			if($result>0){
            $this->error(L('存在重复的代理商用户名'));
            }
            
			//$result = $AgentMod->where("email='%s' and id!=%d ", $_POST['email'], $agent_id)->count();
			//if($result>0){
            //$this->error(L('存在重复的邮箱'));
            //}
            
	}
	

	private function CheckAgentData_Mod(&$Agent, $agent_id=0){
		///检查 $Agent 模型数据。$Agent->email
		
			$AgentMod = M('agent');
			
			$result = $AgentMod->where("username='%s' and id!=%d ", $Agent->username, $agent_id )->count();
			if($result>0){
            $this->error(L('存在重复的代理商用户名'));
            }
            
			//$result = $AgentMod->where("email='%s' and id!=%d ", $Agent->email, $agent_id)->count();
			//if($result>0){
            //$this->error(L('存在重复的邮箱'));
            //}
		
	}
	
	
	
	
	//获得所有代理商
	public function getAllAgentList(){
			$AgentMod = M('agent');
			$result = $AgentMod->where(" status=1 " )->order('id ASC')->select();
			
			return $result;
            
	}
	
	
	
	//获得某个代理商 通过 agent_id
	public function getAgent($agent_id)
	{
    	
			if( isset($agent_id) ){
				$id = isset($agent_id) && intval($agent_id) ? intval($agent_id) : $this->error(L('参数错误'));
			}

	        $AgentMod = M('agent');
		    // 读取数据
		    $data =   $AgentMod->find($id);
		    
			return $data;
			
	}
	
	
	
	
	//代理商 分店 列表
	public function listing_address()
	{
		
		if( isset($_GET['agent_id']) ){
				$agent_id = isset($_GET['agent_id']) && intval($_GET['agent_id']) ? intval($_GET['agent_id']) : $this->error(L('参数错误'));
		}
		
		$agent_info=$this->getAgent($agent_id);
		$this->assign('agent_info', $agent_info );
		
		
		//$allagent=$this->getAllAgentList();
		//$this->assign('allagent', $allagent );
		//echo "<pre>";print_r($allagent);exit;

        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='删除'){
            if( empty($_POST['id']) ){
                $this->error(L('请选择要删除的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }

            //删除连带数据

            //删除自身数据
            $AgentMod = M('agent_address');
            $sql=" id in (".$in.") ";
            $AgentMod->where($sql)->delete();

            $this->success('删除成功', U('agent/listing_address',array('agent_id'=>$agent_info['id'])));
            exit;
        }

        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250 ";
		$sqlOrder = "id DESC";
		
		$sqlWhere .= " and agent_id = ". intval($agent_id)." ";


		$filter_state = $this->REQUEST('_filter_state');
		if( $filter_state != '' ){
			$sqlWhere .= " and status = ". intval($filter_state)." ";
		}

        $f_search = $this->REQUEST('_f_search');
		if( $f_search != '' ){
			$sqlWhere .= " and (prov like '%". $this->fixSQL($f_search)."%' or city like '%". $this->fixSQL($f_search)."%' or address like '%". $this->fixSQL($f_search)."%')";
		}

        $this->ModManager = M('agent_address');
        $f_order = $this->REQUEST('_f_order', 'id');
        $fields = $this->ModManager->getDbFields();
        if( in_array($f_order, $fields) ){
            $sqlOrder = $f_order . ' ';
        }else{
            $sqlOrder = 'id ';
        }
        $f_direc = strtoupper($this->REQUEST('_f_direc'));if($f_direc==""){$f_direc='DESC';}
        if( $f_direc != 'DESC' ){
            $sqlOrder .= 'ASC';
        }else{
            $sqlOrder .= 'DESC';
        }

		///回传过滤条件
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

		//echo $sqlWhere;exit;

		///获取列表数据集		
		$rst=$this->GeneralActionForListing('agent_address', $sqlWhere, $sqlOrder, '', 'M');
		
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('代理商分店地址列表 - '.$agent_info['agent_name']);
		$PageMenu = array(
			array( U('agent/create_address', array('agent_id'=>$agent_id)), L('添加代理商分店地址')  ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	/**
	 * 代理商 分店 修改状态
	 * @access public
	 * @return json
	 */
	public function ajax_change_status_address()
	{
		$module = $AgentMod = M('agent_address');
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





	/**
	 *--------------------------------------------------------------+
	 * Action: 代理商 分店  create
	 *--------------------------------------------------------------+
	 */
	public function create_address()
	{
    	
    	
		if( isset($_GET['agent_id']) ){
				$agent_id = isset($_GET['agent_id']) && intval($_GET['agent_id']) ? intval($_GET['agent_id']) : $this->error(L('参数错误'));
		}
		
		$agent_info=$this->getAgent($agent_id);
		$this->assign('agent_info', $agent_info );
		
		
		if(isset($_POST['dosubmit'])){

	        $AgentMod = M('agent_address');
			
			//$rst=$this->CheckAgentData_Post();
			
			if (false === $AgentMod->create()) {
				$this->error($module->getError());
			}
			
	        if($AgentMod->create()) {
	        	
	        	//使用 $AgentMod->email
        		//$rst=$this->CheckAgentData_Mod($AgentMod);
	        	$AgentMod->create_time=time();
	        	
	        	$result =   $AgentMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($AgentMod->getError());
	        }
			
		}else{

			$PageTitle = L('添加代理商分店地址');
			$PageMenu = array(
					array( U('agent/listing_address',array('agent_id'=>$agent_info['id'])), L('代理商分店地址列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
	}
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * Action: 代理商 分店   edit
	 *--------------------------------------------------------------+
	 */
	public function edit_address()
	{
	    
		if( isset($_GET['agent_id']) ){
				$agent_id = isset($_GET['agent_id']) && intval($_GET['agent_id']) ? intval($_GET['agent_id']) : $this->error(L('参数错误'));
		}
		
		
		$agent_info=$this->getAgent($agent_id);
		$this->assign('agent_info', $agent_info );
		
		
		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckAgentData_Post($id);
			//echo "<pre>";print_r($_POST);exit;

	        $AgentMod = M('agent_address');

	        if($AgentMod->create()) {
	        	
                //$rst=$this->CheckAgentData_Mod($AgentMod,$id);
                $AgentMod->modify_time=time();

	            $result =   $AgentMod->save();
	            if($result) {
	                $this->success('操作成功！', U('agent/edit_address', array('id'=>$id,'agent_id'=>$agent_info['id']  )) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($AgentMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $AgentMod = M('agent_address');
		    // 读取数据
		    $data =   $AgentMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('代理商分店地址数据读取错误');
		    }
    
			$PageTitle = L('编辑代理商分店地址');
			$PageMenu = array(
					array( U('agent/create_address' ,array('agent_id'=>$agent_info['id']) ), L('添加代理商分店地址') ),
					array( U('agent/listing_address' ,array('agent_id'=>$agent_info['id']) ), L('代理商分店地址列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
	}
	
	
	
	
	
}
?>