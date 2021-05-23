<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class product_banner_qaAction extends TAction
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
            $product_banner_qaMod = M('product_banner_qa');
            $sql=" id in (".$in.") ";
            $product_banner_qaMod->where($sql)->delete();

            $this->success('删除成功', U('product_banner_qa/listing'));
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
		
		
		

        $this->ModManager = M('product_banner_qa');
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
		$rst=$this->GeneralActionForListing('product_banner_qa', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		
        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
                if(isset($classlist[$v['class_id']])){
                    $rst['dataset'][$k]['class_name']=$classlist[$v['class_id']]['title'];
                }
                else{
                    $rst['dataset'][$k]['class_name']="";
                }
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst);exit;
        
        
        
        
		$PageTitle = L('路线QA列表');
		$PageMenu = array(
			array( U('product_banner_qa/create'), L('添加路线QA') ),
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
        
        if(isset($_POST['dosubmit'])){

            $product_banner_qaMod = M('product_banner_qa');

            //$rst=$this->Checkproduct_banner_qaData_Post();

            if (false === $product_banner_qaMod->create()) {
                $this->error($module->getError());
            }

            if($product_banner_qaMod->create()) {

                //$rst=$this->Checkproduct_banner_qaData_Mod($product_banner_qaMod);
                $product_banner_qaMod->create_time=time();
                $product_banner_qaMod->modify_time=time();

                $result =   $product_banner_qaMod->add();
                if($result) {
                    $this->success('操作成功！', U('product_banner_qa/create'));
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($product_banner_qaMod->getError());
            }

        }else{

            $PageTitle = L('添加路线QA');
            $PageMenu = array(
                array( U('product_banner_qa/listing'), L('路线QA列表') ),
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
    	
    	///注意：老用户用户已填写情况下不允许修改用户名，用户名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkproduct_banner_qaData_Post($id);

	        $product_banner_qaMod = M('product_banner_qa');

	        if($product_banner_qaMod->create()) {
	        	
                //$rst=$this->Checkproduct_banner_qaData_Mod($product_banner_qaMod,$id);
                $product_banner_qaMod->modify_time=time();

	            $result =   $product_banner_qaMod->save();
	            if($result) {
	                $this->success('操作成功！', U('product_banner_qa/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($product_banner_qaMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $product_banner_qaMod = M('product_banner_qa');
		    // 读取数据
		    $data =   $product_banner_qaMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('用户数据读取错误');
		    }
    
			$PageTitle = L('编辑路线QA');
			$PageMenu = array(
					array( U('product_banner_qa/create'), L('添加路线QA') ),
					array( U('product_banner_qa/listing'), L('路线QA列表') ),
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
		$module = $UserMod = M('product_banner_qa');
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

	private function Checkproduct_banner_qaData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function Checkproduct_banner_qaData_Mod(&$User, $user_id=0){
		///检查 $User 模型数据。$User->email

	}





    //找所有一级分类和二级分类
    public function getAllClassList(){
		
		
		$bigclasslist=$this->getClassList();
		//echo "<pre>";print_r($bigclasslist);exit;
		
        $CityMod = M('product_list');
        $parent_list = $CityMod->field('id,title,class_id')->where(" status=1 " )->order('class_id asc , sort asc , id desc')->select();
        //echo "<pre>";print_r($parent_list);exit;

        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
            	$v['title']='【'.$bigclasslist[$v['class_id']]['title'].'】 - 【'.$v['title'].'】';
                $allclasslist[$v['id']]=$v;
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }



	//找所有分类
    public function getClassList_small(){
		
		
        $CityMod = M('product_list');
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
		
		
        $CityMod = M('product_class');
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
    
    
    
    


/*路线QA图集*/


    public function listing_product_banner_qaphoto()
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
            $product_banner_qaMod = M('product_banner_qaphoto');
            $sql=" id in (".$in.") ";
            $product_banner_qaMod->where($sql)->delete();

            $this->success('删除成功', U('product_banner_qa/listing_product_banner_qaphoto'));
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
		
		
		
		

        $this->ModManager = M('product_banner_qaphoto');
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
        $rst=$this->GeneralActionForListing('product_banner_qaphoto', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('路线QA图集列表');
        $PageMenu = array(
            array( U('product_banner_qa/create_product_banner_qaphoto'), L('添加路线QA图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_product_banner_qaphoto()
    {
        $module = $UserMod = M('product_banner_qaphoto');
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


    public function create_product_banner_qaphoto()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $product_banner_qaMod = M('product_banner_qaphoto');

            //$rst=$this->Checkproduct_banner_qaData_Post();

            if (false === $product_banner_qaMod->create()) {
                $this->error($module->getError());
            }

            if($product_banner_qaMod->create()) {

                //$rst=$this->Checkproduct_banner_qaData_Mod($product_banner_qaMod);
                $product_banner_qaMod->create_time=time();
                $product_banner_qaMod->modify_time=time();

                $result =   $product_banner_qaMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($product_banner_qaMod->getError());
            }

        }else{

            $PageTitle = L('添加路线QA图集');
            $PageMenu = array(
                array( U('product_banner_qa/listing_product_banner_qaphoto'), L('路线QA图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_product_banner_qaphoto()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkproduct_banner_qaData_Post($id);

            $product_banner_qaMod = M('product_banner_qaphoto');

            if($product_banner_qaMod->create()) {

                //$rst=$this->Checkproduct_banner_qaData_Mod($product_banner_qaMod,$id);
                $product_banner_qaMod->modify_time=time();

                $result =   $product_banner_qaMod->save();
                if($result) {
                    $this->success('操作成功！', U('product_banner_qa/edit_product_banner_qaphoto', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($product_banner_qaMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $product_banner_qaMod = M('product_banner_qaphoto');
            // 读取数据
            $data =   $product_banner_qaMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑路线QA图集');
            $PageMenu = array(
                array( U('product_banner_qa/create_product_banner_qaphoto'), L('添加路线QA图集') ),
                array( U('product_banner_qa/listing_product_banner_qaphoto'), L('路线QA图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }




/*路线QA行程*/


    public function listing_product_banner_qacircle()
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
            $product_banner_qaMod = M('product_banner_qacircle');
            $sql=" id in (".$in.") ";
            $product_banner_qaMod->where($sql)->delete();

            $this->success('删除成功', U('product_banner_qa/listing_product_banner_qacircle'));
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
		
		
		
		

        $this->ModManager = M('product_banner_qacircle');
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
        $rst=$this->GeneralActionForListing('product_banner_qacircle', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('路线QA行程列表');
        $PageMenu = array(
            array( U('product_banner_qa/create_product_banner_qacircle'), L('添加路线QA行程') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_product_banner_qacircle()
    {
        $module = $UserMod = M('product_banner_qacircle');
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


    public function create_product_banner_qacircle()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $product_banner_qaMod = M('product_banner_qacircle');

            //$rst=$this->Checkproduct_banner_qaData_Post();

            if (false === $product_banner_qaMod->create()) {
                $this->error($module->getError());
            }

            if($product_banner_qaMod->create()) {

                //$rst=$this->Checkproduct_banner_qaData_Mod($product_banner_qaMod);
                $product_banner_qaMod->create_time=time();
                $product_banner_qaMod->modify_time=time();

                $result =   $product_banner_qaMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($product_banner_qaMod->getError());
            }

        }else{

            $PageTitle = L('添加路线QA行程');
            $PageMenu = array(
                array( U('product_banner_qa/listing_product_banner_qacircle'), L('路线QA行程列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_product_banner_qacircle()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkproduct_banner_qaData_Post($id);

            $product_banner_qaMod = M('product_banner_qacircle');

            if($product_banner_qaMod->create()) {

                //$rst=$this->Checkproduct_banner_qaData_Mod($product_banner_qaMod,$id);
                $product_banner_qaMod->modify_time=time();

                $result =   $product_banner_qaMod->save();
                if($result) {
                    $this->success('操作成功！', U('product_banner_qa/edit_product_banner_qacircle', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($product_banner_qaMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $product_banner_qaMod = M('product_banner_qacircle');
            // 读取数据
            $data =   $product_banner_qaMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑路线QA行程');
            $PageMenu = array(
                array( U('product_banner_qa/create_product_banner_qacircle'), L('添加路线QA行程') ),
                array( U('product_banner_qa/listing_product_banner_qacircle'), L('路线QA行程列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }
    
    


}
?>