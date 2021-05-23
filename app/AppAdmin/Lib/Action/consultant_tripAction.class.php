<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class consultant_tripAction extends TAction
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
		
		
		//$classlist=$this->getClassList();
		
		$classlist=$this->getAllClassList();
        $this->assign('classlist', $classlist );
        //echo "<pre>";print_r($classlist);exit;
        
        
        $countrylist=$this->getAllCountryList();
        $this->assign('countrylist', $countrylist );
        
        
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
            $consultant_tripMod = M('consultant_trip');
            $sql=" id in (".$in.") ";
            $consultant_tripMod->where($sql)->delete();

            $this->success('删除成功', U('consultant_trip/listing'));
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
        
        
        
        
        $filter_class_country_id = $this->REQUEST('_filter_class_country_id');
        
        if( $filter_class_country_id != '' ){
            $sqlWhere .= " and class_country_id = ". addslashes($filter_class_country_id)." ";
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
		
		
		

        $this->ModManager = M('consultant_trip');
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
        $this->assign('filter_class_country_id',  $filter_class_country_id);
        
        
        

		///获取列表数据集		
		$rst=$this->GeneralActionForListing('consultant_trip', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		
        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
                if(isset($classlist[$v['class_id']])){
                    $rst['dataset'][$k]['class_name']=$classlist[$v['class_id']]['title'];
                }
                else{
                    $rst['dataset'][$k]['class_name']="";
                }
                
                if(isset($countrylist[$v['class_country_id']])){
                    $rst['dataset'][$k]['country_name']=$countrylist[$v['class_country_id']]['title'];
                }
                else{
                    $rst['dataset'][$k]['country_name']="";
                }
                
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst);exit;
        
        
        
        
		$PageTitle = L('顾问文章列表');
		$PageMenu = array(
			array( U('consultant_trip/create'), L('添加顾问文章') ),
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
		
		$classlist=$this->getAllClassList();
        $this->assign('classlist', $classlist );
        //echo "<pre>";print_r($classlist);exit;
        
        $countrylist=$this->getAllCountryList();
        $this->assign('countrylist', $countrylist );
        
        	
        
        if(isset($_POST['dosubmit'])){

            $consultant_tripMod = M('consultant_trip');

            //$rst=$this->Checkconsultant_tripData_Post();

            if (false === $consultant_tripMod->create()) {
                $this->error($module->getError());
            }

            if($consultant_tripMod->create()) {

                //$rst=$this->Checkconsultant_tripData_Mod($consultant_tripMod);
                $consultant_tripMod->create_time=time();
                $consultant_tripMod->modify_time=time();

                $result =   $consultant_tripMod->add();
                if($result) {
                    $this->success('操作成功！', U('consultant_trip/create'));
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($consultant_tripMod->getError());
            }

        }else{

            $PageTitle = L('添加顾问文章');
            $PageMenu = array(
                array( U('consultant_trip/listing'), L('顾问文章列表') ),
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
    	
    	$classlist=$this->getAllClassList();
        $this->assign('classlist', $classlist );
        //echo "<pre>";print_r($classlist);exit;
    	
    	
        $countrylist=$this->getAllCountryList();
        $this->assign('countrylist', $countrylist );
        
        	
        	
        	
    	///注意：老用户用户已填写情况下不允许修改用户名，用户名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkconsultant_tripData_Post($id);

	        $consultant_tripMod = M('consultant_trip');

	        if($consultant_tripMod->create()) {
	        	
                //$rst=$this->Checkconsultant_tripData_Mod($consultant_tripMod,$id);
                $consultant_tripMod->modify_time=time();

	            $result =   $consultant_tripMod->save();
	            if($result) {
	                $this->success('操作成功！', U('consultant_trip/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($consultant_tripMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $consultant_tripMod = M('consultant_trip');
		    // 读取数据
		    $data =   $consultant_tripMod->find($id);
		    
		    
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		    	
		        $this->error('用户数据读取错误');
		    }
    
			$PageTitle = L('编辑顾问文章');
			$PageMenu = array(
					array( U('consultant_trip/create'), L('添加顾问文章') ),
					array( U('consultant_trip/listing'), L('顾问文章列表') ),
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
		$module = $UserMod = M('consultant_trip');
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

	private function Checkconsultant_tripData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function Checkconsultant_tripData_Mod(&$User, $user_id=0){
		///检查 $User 模型数据。$User->email

	}





    //所有顾问
    public function getAllClassList(){
		
		
		//$bigclasslist=$this->getClassList();
		//echo "<pre>";print_r($bigclasslist);exit;
		
        $CityMod = M('consultant_list');
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


	//所有顾问国家
    public function getAllCountryList(){
		
		
		//$bigclasslist=$this->getClassList();
		//echo "<pre>";print_r($bigclasslist);exit;
		
        $CityMod = M('product_class');
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
    
    


	//找所有分类
    public function getClassList_small(){
		
		
        $CityMod = M('consultant_list');
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
    
    
    //找所有分类
    public function getClassList(){
		
		
        $CityMod = M('consultant_class');
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
    
    
    
    


/*顾问文章图集*/


    public function listing_consultant_tripphoto()
    {

        $allclasslist=$this->getAllClassList();
        //echo "<pre>";print_r($allclasslist);exit;
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
            $consultant_tripMod = M('consultant_tripphoto');
            $sql=" id in (".$in.") ";
            $consultant_tripMod->where($sql)->delete();

            $this->success('删除成功', U('consultant_trip/listing_consultant_tripphoto'));
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
		
		
		
		

        $this->ModManager = M('consultant_tripphoto');
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
        $rst=$this->GeneralActionForListing('consultant_tripphoto', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('顾问文章图集列表');
        $PageMenu = array(
            array( U('consultant_trip/create_consultant_tripphoto'), L('添加顾问文章图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_consultant_tripphoto()
    {
        $module = $UserMod = M('consultant_tripphoto');
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


    public function create_consultant_tripphoto()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $consultant_tripMod = M('consultant_tripphoto');

            //$rst=$this->Checkconsultant_tripData_Post();

            if (false === $consultant_tripMod->create()) {
                $this->error($module->getError());
            }

            if($consultant_tripMod->create()) {

                //$rst=$this->Checkconsultant_tripData_Mod($consultant_tripMod);
                $consultant_tripMod->create_time=time();
                $consultant_tripMod->modify_time=time();

                $result =   $consultant_tripMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($consultant_tripMod->getError());
            }

        }else{

            $PageTitle = L('添加顾问文章图集');
            $PageMenu = array(
                array( U('consultant_trip/listing_consultant_tripphoto'), L('顾问文章图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_consultant_tripphoto()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkconsultant_tripData_Post($id);

            $consultant_tripMod = M('consultant_tripphoto');

            if($consultant_tripMod->create()) {

                //$rst=$this->Checkconsultant_tripData_Mod($consultant_tripMod,$id);
                $consultant_tripMod->modify_time=time();

                $result =   $consultant_tripMod->save();
                if($result) {
                    $this->success('操作成功！', U('consultant_trip/edit_consultant_tripphoto', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($consultant_tripMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $consultant_tripMod = M('consultant_tripphoto');
            // 读取数据
            $data =   $consultant_tripMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑顾问文章图集');
            $PageMenu = array(
                array( U('consultant_trip/create_consultant_tripphoto'), L('添加顾问文章图集') ),
                array( U('consultant_trip/listing_consultant_tripphoto'), L('顾问文章图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }




/*顾问文章行程*/


    public function listing_consultant_tripcircle()
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
            $consultant_tripMod = M('consultant_tripcircle');
            $sql=" id in (".$in.") ";
            $consultant_tripMod->where($sql)->delete();

            $this->success('删除成功', U('consultant_trip/listing_consultant_tripcircle'));
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
		
		
		
		

        $this->ModManager = M('consultant_tripcircle');
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
        $rst=$this->GeneralActionForListing('consultant_tripcircle', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('顾问文章行程列表');
        $PageMenu = array(
            array( U('consultant_trip/create_consultant_tripcircle'), L('添加顾问文章行程') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_consultant_tripcircle()
    {
        $module = $UserMod = M('consultant_tripcircle');
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


    public function create_consultant_tripcircle()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $consultant_tripMod = M('consultant_tripcircle');

            //$rst=$this->Checkconsultant_tripData_Post();

            if (false === $consultant_tripMod->create()) {
                $this->error($module->getError());
            }

            if($consultant_tripMod->create()) {

                //$rst=$this->Checkconsultant_tripData_Mod($consultant_tripMod);
                $consultant_tripMod->create_time=time();
                $consultant_tripMod->modify_time=time();

                $result =   $consultant_tripMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($consultant_tripMod->getError());
            }

        }else{

            $PageTitle = L('添加顾问文章行程');
            $PageMenu = array(
                array( U('consultant_trip/listing_consultant_tripcircle'), L('顾问文章行程列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_consultant_tripcircle()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkconsultant_tripData_Post($id);

            $consultant_tripMod = M('consultant_tripcircle');

            if($consultant_tripMod->create()) {

                //$rst=$this->Checkconsultant_tripData_Mod($consultant_tripMod,$id);
                $consultant_tripMod->modify_time=time();

                $result =   $consultant_tripMod->save();
                if($result) {
                    $this->success('操作成功！', U('consultant_trip/edit_consultant_tripcircle', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($consultant_tripMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $consultant_tripMod = M('consultant_tripcircle');
            // 读取数据
            $data =   $consultant_tripMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑顾问文章行程');
            $PageMenu = array(
                array( U('consultant_trip/create_consultant_tripcircle'), L('添加顾问文章行程') ),
                array( U('consultant_trip/listing_consultant_tripcircle'), L('顾问文章行程列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }
    
    


}
?>