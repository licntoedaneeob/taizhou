<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class user_quan_historyAction extends TAction
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
		$allclassQuan=$this->getAllClassQuan();
        $this->assign('allclassQuan', $allclassQuan );
        
        
        $allUser=$this->getAllUser();
        $this->assign('allUser', $allUser );
        
        
        
        
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
            $user_quan_historyMod = M('user_quan_history');
            $sql=" id in (".$in.") ";
            $user_quan_historyMod->where($sql)->delete();

            $this->success('删除成功', U('user_quan_history/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		$sqlOrder = " id DESC";
		
		
        
        $filter_class_id = $this->REQUEST('_filter_class_id');
        
        if( $filter_class_id != '' ){
            $sqlWhere .= " and class_id = ". addslashes($filter_class_id)." ";
        }
        
        
        
        
        $filter_user_id = $this->REQUEST('_filter_user_id');
        
        if( $filter_user_id != '' ){
            $sqlWhere .= " and user_id = ". addslashes($filter_user_id)." ";
        }
        
        
        
        $filter_is_used = $this->REQUEST('_filter_is_used');
        
        if( $filter_is_used != '' ){
            $sqlWhere .= " and is_used = ". addslashes($filter_is_used)." ";
        }
        
        
        
        
        
        
		$filter_state = $this->REQUEST('_filter_state');
		if( $filter_state != '' ){
			$sqlWhere .= " and status = ". intval($filter_state)." ";
		}

        
        
        
        //新搜索
        $f_search = $this->REQUEST('_f_search');
        $filter_fieldname = $this->REQUEST('_filter_fieldname');
		if( $f_search != '' ){
			if($filter_fieldname=='title'){
				$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (summary like '%". $this->fixSQL($f_search)."%' or content like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' or summary like '%". $this->fixSQL($f_search)."%' or content like '%". $this->fixSQL($f_search)."%' )";
			}
			else{
			}
		}
		
		
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and create_time < ". $sql_endtime." ";
		}
		
		
		

        $this->ModManager = M('user_quan_history');
        $f_order = $this->REQUEST('_f_order', 'id');
        $fields = $this->ModManager->getDbFields();
        //echo "<pre>";print_r($fields);exit;
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
		$this->assign('filter_fieldname',  $filter_fieldname);
		$this->assign('filter_starttime',  $filter_starttime);
		$this->assign('filter_endtime',  $filter_endtime);
		

        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );
        
        
        //附加查询条件
		$this->assign('filter_class_id',  $filter_class_id);
        $this->assign('filter_user_id',  $filter_user_id);
        $this->assign('filter_is_used',  $filter_is_used);
        

		///获取列表数据集		
		$rst=$this->GeneralActionForListing('user_quan_history', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		
        if(isset($rst['dataset'])){
        	
            foreach($rst['dataset'] as $k => $v){

                if(isset($allclassQuan[$v['class_id']])){
                    $rst['dataset'][$k]['class_name']=$allclassQuan[$v['class_id']]['title'];
                }
                else{
                    $rst['dataset'][$k]['class_name']="";
                }
                
                
                if(isset($allclassQuan[$v['class_id']])){
                    $rst['dataset'][$k]['start_time']=$allclassQuan[$v['class_id']]['start_time'];
                }
                else{
                    $rst['dataset'][$k]['start_time']="";
                }
                
                if(isset($allclassQuan[$v['class_id']])){
                    $rst['dataset'][$k]['end_time']=$allclassQuan[$v['class_id']]['end_time'];
                }
                else{
                    $rst['dataset'][$k]['end_time']="";
                }
                
                
                
                
                if(isset($allUser[$v['user_id']])){
                    $rst['dataset'][$k]['user_username']=$allUser[$v['user_id']]['username'];
                }
                else{
                    $rst['dataset'][$k]['user_username']="";
                }
                
                
                
			
            }
            
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst);exit;




		$PageTitle = L('卡券分配列表');
		$PageMenu = array(
			array( U('user_quan_history/create'), L('添加卡券分配') ),
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


        $allclassQuan=$this->getAllClassQuan();
        $this->assign('allclassQuan', $allclassQuan );
        //echo "<pre>";print_r($allclassQuan);exit;
        
        $allUser=$this->getAllUser();
        $this->assign('allUser', $allUser );
        //echo "<pre>";print_r($allUser);exit;
        
        
        if(!empty($_REQUEST['class_id'])){
        	$class_id=$_REQUEST['class_id'];
        }
        else{
        	$class_id=0;
        }
        $this->assign('class_id', $class_id );
        
        
        
        if(!empty($_REQUEST['user_id'])){
        	$user_id=$_REQUEST['user_id'];
        }
        else{
        	$user_id=0;
        }
        $this->assign('user_id', $user_id );
        
        
        
        
        if(isset($_POST['dosubmit'])){
			
			$cur_time=time();
            
			//获得卡券信息
			$quan_info=$allclassQuan[$_POST['class_id']];
			//echo "<pre>";print_r($quan_info);exit;
			
			
			//获得所有用户id
			if(!empty($_POST['user_id'])){
				$user_list[$_POST['user_id']]['id']=$_POST['user_id'];
			}
			else{
				
				$andsql='';
				if($_POST['level']=='0'){
					$andsql=$andsql.' and level=0 ';
				}
				elseif($_POST['level']=='1'){
					$andsql=$andsql.' and level=1 ';
				}
				else{
				}
				
		        $userMod = M('user');
		        $user_list = $userMod->field('id,username,realname,kahao,level')->where(" status=1 ".$andsql )->select();
		        $user_list=empty($user_list)?array():$user_list;
		        
			}
			//echo "<pre>";print_r($user_list);exit;
			
			
			$user_quan_historyMod = M('user_quan_history');
			
			$number=intval($this->REQUEST('number'), 0);
			//echo $number;exit;
            
            if($number>=1){
	            $x=0;
	            do{
	            	foreach($user_list as $k_user=>$v_user){
	            		
	            		
				        //兑换码
				        $title=$this->shortCode(uniqid());
				        
				        $user_quan_historyMod->create_time=$cur_time;
				        $user_quan_historyMod->modify_time=$cur_time;
				        $user_quan_historyMod->addtime=date("Y-m-d H:i:s",$cur_time);
				        $user_quan_historyMod->user_id=$v_user['id'];
				        $user_quan_historyMod->class_id=$quan_info['id'];
				        $user_quan_historyMod->title=$title;
				        $user_quan_historyMod->is_used=1;
				        $user_quan_history_id = $user_quan_historyMod->add();
				        //var_dump($user_quan_history_id);exit;
				        
				        
				        /*
				        //, start_time='".addslashes($quan_info['start_time'])."' 
				        //, end_time='".addslashes($quan_info['end_time'])."' 
				        
			    		$sql=sprintf("INSERT %s SET user_id='".addslashes($v_user['id'])."' 
				        , class_id='".addslashes($quan_info['id'])."' 
				        , title='".addslashes($title)."' 
				        , create_time='".$cur_time."' 
				        , modify_time='".$cur_time."' 
				        , addtime='".date("Y-m-d H:i:s",$cur_time)."' 
				        ", $user_quan_historyMod->getTableName() );
				        //echo $sql;exit;
				        $result = $user_quan_historyMod->execute($sql);
				        var_dump($result);exit;
				        */
				        
	            	}
	            	$x=$x+1;
	            }while($x<$number);
            }
            
            
			//echo "<pre>";print_r($_POST);exit;
			$this->success('操作成功！', U('user_quan_history/create'));
			
			
			
			/*
            if (false === $user_quan_historyMod->create()) {
                $this->error($module->getError());
            }

            if($user_quan_historyMod->create()) {

                $user_quan_historyMod->create_time=time();
                $user_quan_historyMod->modify_time=time();

                $result =   $user_quan_historyMod->add();
                if($result) {
                    $this->success('操作成功！', U('user_quan_history/create'));
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($user_quan_historyMod->getError());
            }
            */

        }else{

            $PageTitle = L('添加卡券分配');
            $PageMenu = array(
                array( U('user_quan_history/listing'), L('卡券分配列表') ),
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
		
		
        $allclassQuan=$this->getAllClassQuan();
        $this->assign('allclassQuan', $allclassQuan );
        
        $allUser=$this->getAllUser();
        $this->assign('allUser', $allUser );
        
        
        
        
        
        
        
        
    	///注意：老用户用户已填写情况下不允许修改用户名，用户名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkuser_quan_historyData_Post($id);

	        $user_quan_historyMod = M('user_quan_history');

	        if($user_quan_historyMod->create()) {
	        	
                //$rst=$this->Checkuser_quan_historyData_Mod($user_quan_historyMod,$id);
                $user_quan_historyMod->modify_time=time();

	            $result =   $user_quan_historyMod->save();
	            if($result) {
	                $this->success('操作成功！', U('user_quan_history/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($user_quan_historyMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $user_quan_historyMod = M('user_quan_history');
		    // 读取数据
		    $data =   $user_quan_historyMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('用户数据读取错误');
		    }
    
			$PageTitle = L('编辑卡券分配');
			$PageMenu = array(
					array( U('user_quan_history/create'), L('添加卡券分配') ),
					array( U('user_quan_history/listing'), L('卡券分配列表') ),
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
		$module = $UserMod = M('user_quan_history');
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
        $UserMod = M('user');
        $sql=" id in (".$in.") ";
        $UserMod->where($sql)->delete();

        $this->success('删除成功', U('user/listing'));
    }
*/

	private function Checkuser_quan_historyData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function Checkuser_quan_historyData_Mod(&$User, $user_id=0){
		///检查 $User 模型数据。$User->email

	}





    //找所有卡券
    public function getAllClassQuan(){
		
		
		
        $CityMod = M('quan_list');
        $parent_list = $CityMod->field('id,title,start_time,end_time')->where(" status=1 " )->order('sort asc,id desc')->select();
        //echo "<pre>";print_r($parent_list);exit;

        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
                $allclasslist[$v['id']]=$v;
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
        
        
    }

	
	
	
	//所有用户
    public function getAllUser(){
		
        $CityMod = M('user');
        $parent_list = $CityMod->field('id,username,realname,kahao,level')->where(" status=1 " )->order('sort asc , id desc')->select();
        //echo "<pre>";print_r($parent_list);exit;
		
        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
            	$allclasslist[$v['id']]=$v;
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }
    
    
    
    
    
    
    
	//所有预算
    public function getAll_user_quan_history_budget_List(){
		
		
		//$bigclasslist=$this->getClassList();
		//echo "<pre>";print_r($bigclasslist);exit;
		
        $CityMod = M('user_quan_history_budget');
        $parent_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc , id desc')->select();
        //echo "<pre>";print_r($parent_list);exit;
		
        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
            	$allclasslist[$v['id']]=$v;
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }
    
    
	//所有学历
    public function getAll_user_quan_history_diploma_List(){
		
		
		//$bigclasslist=$this->getClassList();
		//echo "<pre>";print_r($bigclasslist);exit;
		
        $CityMod = M('user_quan_history_diploma');
        $parent_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc , id desc')->select();
        //echo "<pre>";print_r($parent_list);exit;
		
        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
            	$allclasslist[$v['id']]=$v;
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }
    
    
    
	//所有英语水平
    public function getAll_user_quan_history_english_List(){
		
		
		//$bigclasslist=$this->getClassList();
		//echo "<pre>";print_r($bigclasslist);exit;
		
        $CityMod = M('user_quan_history_english');
        $parent_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc , id desc')->select();
        //echo "<pre>";print_r($parent_list);exit;
		
        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
            	$allclasslist[$v['id']]=$v;
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }
    
    
    
	//所有年收入
    public function getAll_user_quan_history_income_List(){
		
		
		//$bigclasslist=$this->getClassList();
		//echo "<pre>";print_r($bigclasslist);exit;
		
        $CityMod = M('user_quan_history_income');
        $parent_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc , id desc')->select();
        //echo "<pre>";print_r($parent_list);exit;
		
        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
            	$allclasslist[$v['id']]=$v;
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }
    
    
    
    
    




    //找所有一级分类和二级分类
    public function getAllClassList(){
		
		
        $CityMod = M('user_quan_history');
        $parent_list = $CityMod->field('id,title')->where(" status=1 " )->order('id asc')->select();
        //echo "<pre>";print_r($parent_list);exit;

        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
                $allclasslist[$v['id']]=$v;
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }







/*卡券分配图集*/


    public function listing_user_quan_historyphoto()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

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
            $user_quan_historyMod = M('user_quan_historyphoto');
            $sql=" id in (".$in.") ";
            $user_quan_historyMod->where($sql)->delete();

            $this->success('删除成功', U('user_quan_history/listing_user_quan_historyphoto'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC";
		
		$filter_state = $this->REQUEST('_filter_state');
		if( $filter_state != '' ){
			$sqlWhere .= " and status = ". intval($filter_state)." ";
		}
		
		
		
        
        //新搜索
        $f_search = $this->REQUEST('_f_search');
        $filter_fieldname = $this->REQUEST('_filter_fieldname');
		if( $f_search != '' ){
			if($filter_fieldname=='title'){
				$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (summary like '%". $this->fixSQL($f_search)."%' or content like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' or summary like '%". $this->fixSQL($f_search)."%' or content like '%". $this->fixSQL($f_search)."%' )";
			}
			else{
			}
		}
		
		
		
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and create_time < ". $sql_endtime." ";
		}
		
		
		
		
		
		
		
		

        $this->ModManager = M('user_quan_historyphoto');
        $f_order = $this->REQUEST('_f_order', 'id');
        $fields = $this->ModManager->getDbFields();
        //echo "<pre>";print_r($fields);exit;
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
        $this->assign('filter_fieldname',  $filter_fieldname);
		$this->assign('filter_starttime',  $filter_starttime);
		$this->assign('filter_endtime',  $filter_endtime);



        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('user_quan_historyphoto', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
                if(isset($allclasslist[$v['class_id']])){
                    $rst['dataset'][$k]['class_name']=$allclasslist[$v['class_id']]['title'];
                }
                else{
                    $rst['dataset'][$k]['class_name']="";
                }
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst);exit;

        $PageTitle = L('卡券分配图集列表');
        $PageMenu = array(
            array( U('user_quan_history/create_user_quan_historyphoto'), L('添加卡券分配图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_user_quan_historyphoto()
    {
        $module = $UserMod = M('user_quan_historyphoto');
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


    public function create_user_quan_historyphoto()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $user_quan_historyMod = M('user_quan_historyphoto');

            //$rst=$this->Checkuser_quan_historyData_Post();

            if (false === $user_quan_historyMod->create()) {
                $this->error($module->getError());
            }

            if($user_quan_historyMod->create()) {

                //$rst=$this->Checkuser_quan_historyData_Mod($user_quan_historyMod);
                $user_quan_historyMod->create_time=time();
                $user_quan_historyMod->modify_time=time();

                $result =   $user_quan_historyMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($user_quan_historyMod->getError());
            }

        }else{

            $PageTitle = L('添加卡券分配图集');
            $PageMenu = array(
                array( U('user_quan_history/listing_user_quan_historyphoto'), L('卡券分配图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_user_quan_historyphoto()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkuser_quan_historyData_Post($id);

            $user_quan_historyMod = M('user_quan_historyphoto');

            if($user_quan_historyMod->create()) {

                //$rst=$this->Checkuser_quan_historyData_Mod($user_quan_historyMod,$id);
                $user_quan_historyMod->modify_time=time();

                $result =   $user_quan_historyMod->save();
                if($result) {
                    $this->success('操作成功！', U('user_quan_history/edit_user_quan_historyphoto', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($user_quan_historyMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $user_quan_historyMod = M('user_quan_historyphoto');
            // 读取数据
            $data =   $user_quan_historyMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑卡券分配图集');
            $PageMenu = array(
                array( U('user_quan_history/create_user_quan_historyphoto'), L('添加卡券分配图集') ),
                array( U('user_quan_history/listing_user_quan_historyphoto'), L('卡券分配图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }















/*卡券分配设计*/


    public function listing_user_quan_historydesign()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

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
            $user_quan_historyMod = M('user_quan_historydesign');
            $sql=" id in (".$in.") ";
            $user_quan_historyMod->where($sql)->delete();

            $this->success('删除成功', U('user_quan_history/listing_user_quan_historydesign'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC";
		
		$filter_state = $this->REQUEST('_filter_state');
		if( $filter_state != '' ){
			$sqlWhere .= " and status = ". intval($filter_state)." ";
		}
		
		
		
        
        //新搜索
        $f_search = $this->REQUEST('_f_search');
        $filter_fieldname = $this->REQUEST('_filter_fieldname');
		if( $f_search != '' ){
			if($filter_fieldname=='title'){
				$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (summary like '%". $this->fixSQL($f_search)."%' or content like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' or summary like '%". $this->fixSQL($f_search)."%' or content like '%". $this->fixSQL($f_search)."%' )";
			}
			else{
			}
		}
		
		
		
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and create_time < ". $sql_endtime." ";
		}
		
		
		
		
		
		
		
		

        $this->ModManager = M('user_quan_historydesign');
        $f_order = $this->REQUEST('_f_order', 'id');
        $fields = $this->ModManager->getDbFields();
        //echo "<pre>";print_r($fields);exit;
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
        $this->assign('filter_fieldname',  $filter_fieldname);
		$this->assign('filter_starttime',  $filter_starttime);
		$this->assign('filter_endtime',  $filter_endtime);



        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('user_quan_historydesign', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
                if(isset($allclasslist[$v['class_id']])){
                    $rst['dataset'][$k]['class_name']=$allclasslist[$v['class_id']]['title'];
                }
                else{
                    $rst['dataset'][$k]['class_name']="";
                }
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst);exit;

        $PageTitle = L('卡券分配图集列表');
        $PageMenu = array(
            array( U('user_quan_history/create_user_quan_historydesign'), L('添加卡券分配图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_user_quan_historydesign()
    {
        $module = $UserMod = M('user_quan_historydesign');
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


    public function create_user_quan_historydesign()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $user_quan_historyMod = M('user_quan_historydesign');

            //$rst=$this->Checkuser_quan_historyData_Post();

            if (false === $user_quan_historyMod->create()) {
                $this->error($module->getError());
            }

            if($user_quan_historyMod->create()) {

                //$rst=$this->Checkuser_quan_historyData_Mod($user_quan_historyMod);
                $user_quan_historyMod->create_time=time();
                $user_quan_historyMod->modify_time=time();

                $result =   $user_quan_historyMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($user_quan_historyMod->getError());
            }

        }else{

            $PageTitle = L('添加卡券分配图集');
            $PageMenu = array(
                array( U('user_quan_history/listing_user_quan_historydesign'), L('卡券分配图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_user_quan_historydesign()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkuser_quan_historyData_Post($id);

            $user_quan_historyMod = M('user_quan_historydesign');

            if($user_quan_historyMod->create()) {

                //$rst=$this->Checkuser_quan_historyData_Mod($user_quan_historyMod,$id);
                $user_quan_historyMod->modify_time=time();

                $result =   $user_quan_historyMod->save();
                if($result) {
                    $this->success('操作成功！', U('user_quan_history/edit_user_quan_historydesign', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($user_quan_historyMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $user_quan_historyMod = M('user_quan_historydesign');
            // 读取数据
            $data =   $user_quan_historyMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑卡券分配图集');
            $PageMenu = array(
                array( U('user_quan_history/create_user_quan_historydesign'), L('添加卡券分配图集') ),
                array( U('user_quan_history/listing_user_quan_historydesign'), L('卡券分配图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }

	
	
	
    //导出活动预约数据
    public function export_attend()
    {
    	
    	
        $user_quan_history_id=$_GET['user_quan_history_id'];
        
        
        $andsql="";
        
        
        if(!empty($user_quan_history_id)){
        	$andsql=$andsql." and class_id=".addslashes($user_quan_history_id);
        }
        
        
        
        $CityMod = M('user_quan_history_attend');
        $toShow['banner'] = $CityMod->where(" status=1 ".$andsql )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;
		
		
		
		//$CityMod = M('user_quan_history');
		//$user_quan_history_info =   $CityMod->find($user_quan_history_id);
		//echo "<pre>";print_r($company_event_info);exit;
		
		
		
		
		//所有卡券分配
		$CityMod = M('user_quan_history');
        $allProductList = $CityMod->where(" status=1 " )->order('id desc')->select();
        //echo "<pre>";print_r($allProductList);exit;
        
        
        $allclasslist=array();
        if(isset($allProductList)){
            foreach($allProductList as $k => $v){
                $allclasslist[$v['id']]=$v;
            }
        }
        $allProductList=$allclasslist;
        //echo "<pre>";print_r($allProductList);exit;
        
        
        
		
		
        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "评估ID编号"
        	.$expstr."卡券分配"
        	.$expstr."姓名"
            .$expstr."性别"
            .$expstr."出生日期"
            .$expstr."手机"
            .$expstr."电话"
            .$expstr."邮箱"
            .$expstr."居住地址"
            .$expstr."现有资产"
            .$expstr."意向国家或地区"
            .$expstr."提交时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
                
                
                $v=$toShow['banner'][$k];
                
                if(isset($allProductList[$v['class_id']])){
                    $toShow['banner'][$k]['class_name']=$allProductList[$v['class_id']]['title'];
                }
                else{
                    $toShow['banner'][$k]['class_name']="";
                }
                
                
                
                //$summary=$toShow['banner'][$k]['summary'];
                //$summary=str_replace("\r\n"," [Enter] ",$summary);
                //$summary=str_replace("\n"," [Enter] ",$summary);
                //$summary=str_replace("\r","",$summary);
				
				
                //if ($toShow['banner'][$k]['device']=="0"){
                //    $device_show="PC端";
                //}
                //if ($toShow['banner'][$k]['device']=="1"){
                //    $device_show="手机端";
                //}





                    $output .= $toShow['banner'][$k]['id']
                    	.$expstr.$toShow['banner'][$k]['class_name']
                    	.$expstr.$toShow['banner'][$k]['realname']
                    	.$expstr.$toShow['banner'][$k]['gender']
                    	.$expstr.$toShow['banner'][$k]['birthday']
                    	.$expstr.$toShow['banner'][$k]['mobile']
                    	.$expstr.$toShow['banner'][$k]['phone']
                    	.$expstr.$toShow['banner'][$k]['email']
                    	.$expstr.$toShow['banner'][$k]['address']
                    	.$expstr.$toShow['banner'][$k]['money']
                        .$expstr.$toShow['banner'][$k]['want_country']
                        .$expstr.date('Y-m-d H:i:s',$toShow['banner'][$k]['create_time'])
                        .$expenter;


                $k=$k+1;
            }while($k<count($toShow['banner']));
        }

        $T_text=$output;

//如果mb_convert_encoding函数存在则用此函数来转编码,前提是需要安装mbstring包
        if(function_exists('mb_convert_encoding')){
            header('Content-type: text/csv; charset=UTF-16LE');
            echo(chr(255).chr(254));
            echo(mb_convert_encoding($T_text,"UTF-16LE","UTF-8"));
        }
//如果iconv函数存在则用此函数来转编码
        elseif(function_exists('iconv')){
            header('Content-type: text/csv');
            echo(chr(255).chr(254));
            echo(iconv("UTF-8","UTF-16LE",$T_text));
        }
//直接从utf-8转,这个貌似不灵...
        else{
            header('Content-type: text/csv; charset=UTF-8');
            echo(chr(239).chr(187).chr(191));
            echo($T_text);
        }
        exit;
    }





//设为已使用
	public function ajax_used_yes()
	{
		$module = $game1Mod = M('user_quan_history');
		$id 	= intval($_REQUEST['id']);
		$user_id 	= intval($_REQUEST['user_id']);
		
		$result = $module->execute( sprintf("UPDATE %s SET is_used=2  where id=%d", $module->getTableName(), $id) );
		
		$this->ajaxReturn(array('status' => 'true'));
		
		
	}
	
	
	
//设为未使用
	public function ajax_used_no()
	{
		$module = $game1Mod = M('user_quan_history');
		$id 	= intval($_REQUEST['id']);
		$user_id 	= intval($_REQUEST['user_id']);
		
		$result = $module->execute( sprintf("UPDATE %s SET is_used=1 where id=%d", $module->getTableName(), $id) );
		
		$this->ajaxReturn(array('status' => 'true'));
		
		
	}
	
	



}
?>