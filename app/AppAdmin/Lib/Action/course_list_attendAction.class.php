<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class course_list_attendAction extends TAction
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
		$allclasslist=$this->getAllClassListBig();
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
            $course_list_attendMod = M('course_list_attend');
            $sql=" id in (".$in.") ";
            $course_list_attendMod->where($sql)->delete();

            $this->success('删除成功', U('course_list_attend/listing'));
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
        
        
		$filter_status_pay = $this->REQUEST('_filter_status_pay');
		if( $filter_status_pay != '' ){
			$sqlWhere .= " and status_pay = ". intval($filter_status_pay)." ";
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
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (mobile like '%". $this->fixSQL($f_search)."%' or mobile like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or mobile like '%". $this->fixSQL($f_search)."%' or mobile like '%". $this->fixSQL($f_search)."%' )";
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
		
		
		

        $this->ModManager = M('course_list_attend');
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
        $this->assign('filter_status_pay',  $filter_status_pay);
        

		///获取列表数据集		
		$rst=$this->GeneralActionForListing('course_list_attend', $sqlWhere, $sqlOrder, '', 'M');
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




		$PageTitle = L('课程预约列表');
		$PageMenu = array(
			//array( U('course_list_attend/create'), L('添加课程预约') ),
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


        $allclasslist=$this->getAllClassListBig();
        $this->assign('allclasslist', $allclasslist );
        
        if(isset($_POST['dosubmit'])){

            $course_list_attendMod = M('course_list_attend');

            //$rst=$this->Checkcourse_list_attendData_Post();

            if (false === $course_list_attendMod->create()) {
                $this->error($module->getError());
            }

            if($course_list_attendMod->create()) {

                //$rst=$this->Checkcourse_list_attendData_Mod($course_list_attendMod);
                $course_list_attendMod->create_time=time();
                $course_list_attendMod->modify_time=time();

                $result =   $course_list_attendMod->add();
                if($result) {
                    $this->success('操作成功！', U('course_list_attend/create'));
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($course_list_attendMod->getError());
            }

        }else{

            $PageTitle = L('添加课程预约');
            $PageMenu = array(
                array( U('course_list_attend/listing'), L('课程预约列表') ),
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
		
		
        $allclasslist=$this->getAllClassListBig();
        $this->assign('allclasslist', $allclasslist );
        
    	///注意：老用户用户已填写情况下不允许修改用户名，用户名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkcourse_list_attendData_Post($id);

	        $course_list_attendMod = M('course_list_attend');

	        if($course_list_attendMod->create()) {
	        	
                //$rst=$this->Checkcourse_list_attendData_Mod($course_list_attendMod,$id);
                $course_list_attendMod->modify_time=time();

	            $result =   $course_list_attendMod->save();
	            if($result) {
	                $this->success('操作成功！', U('course_list_attend/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($course_list_attendMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $course_list_attendMod = M('course_list_attend');
		    // 读取数据
		    $data =   $course_list_attendMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('用户数据读取错误');
		    }
    
			$PageTitle = L('编辑课程预约');
			$PageMenu = array(
					array( U('course_list_attend/create'), L('添加课程预约') ),
					array( U('course_list_attend/listing'), L('课程预约列表') ),
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
		$module = $UserMod = M('course_list_attend');
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

	private function Checkcourse_list_attendData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function Checkcourse_list_attendData_Mod(&$User, $user_id=0){
		///检查 $User 模型数据。$User->email

	}





    //找所有一级分类和二级分类
    public function getAllClassListBig(){
		
		
		
        $CityMod = M('course_list');
        $parent_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc,id desc')->select();
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
		
		
        $CityMod = M('course_list_attend');
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







/*课程预约图集*/


    public function listing_course_list_attendphoto()
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
            $course_list_attendMod = M('course_list_attendphoto');
            $sql=" id in (".$in.") ";
            $course_list_attendMod->where($sql)->delete();

            $this->success('删除成功', U('course_list_attend/listing_course_list_attendphoto'));
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
		
		
		
		
		
		
		
		

        $this->ModManager = M('course_list_attendphoto');
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
        $rst=$this->GeneralActionForListing('course_list_attendphoto', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('课程预约图集列表');
        $PageMenu = array(
            array( U('course_list_attend/create_course_list_attendphoto'), L('添加课程预约图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_course_list_attendphoto()
    {
        $module = $UserMod = M('course_list_attendphoto');
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


    public function create_course_list_attendphoto()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $course_list_attendMod = M('course_list_attendphoto');

            //$rst=$this->Checkcourse_list_attendData_Post();

            if (false === $course_list_attendMod->create()) {
                $this->error($module->getError());
            }

            if($course_list_attendMod->create()) {

                //$rst=$this->Checkcourse_list_attendData_Mod($course_list_attendMod);
                $course_list_attendMod->create_time=time();
                $course_list_attendMod->modify_time=time();

                $result =   $course_list_attendMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($course_list_attendMod->getError());
            }

        }else{

            $PageTitle = L('添加课程预约图集');
            $PageMenu = array(
                array( U('course_list_attend/listing_course_list_attendphoto'), L('课程预约图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_course_list_attendphoto()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkcourse_list_attendData_Post($id);

            $course_list_attendMod = M('course_list_attendphoto');

            if($course_list_attendMod->create()) {

                //$rst=$this->Checkcourse_list_attendData_Mod($course_list_attendMod,$id);
                $course_list_attendMod->modify_time=time();

                $result =   $course_list_attendMod->save();
                if($result) {
                    $this->success('操作成功！', U('course_list_attend/edit_course_list_attendphoto', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($course_list_attendMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $course_list_attendMod = M('course_list_attendphoto');
            // 读取数据
            $data =   $course_list_attendMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑课程预约图集');
            $PageMenu = array(
                array( U('course_list_attend/create_course_list_attendphoto'), L('添加课程预约图集') ),
                array( U('course_list_attend/listing_course_list_attendphoto'), L('课程预约图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }















/*课程预约设计*/


    public function listing_course_list_attenddesign()
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
            $course_list_attendMod = M('course_list_attenddesign');
            $sql=" id in (".$in.") ";
            $course_list_attendMod->where($sql)->delete();

            $this->success('删除成功', U('course_list_attend/listing_course_list_attenddesign'));
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
		
		
		
		
		
		
		
		

        $this->ModManager = M('course_list_attenddesign');
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
        $rst=$this->GeneralActionForListing('course_list_attenddesign', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('课程预约图集列表');
        $PageMenu = array(
            array( U('course_list_attend/create_course_list_attenddesign'), L('添加课程预约图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_course_list_attenddesign()
    {
        $module = $UserMod = M('course_list_attenddesign');
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


    public function create_course_list_attenddesign()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $course_list_attendMod = M('course_list_attenddesign');

            //$rst=$this->Checkcourse_list_attendData_Post();

            if (false === $course_list_attendMod->create()) {
                $this->error($module->getError());
            }

            if($course_list_attendMod->create()) {

                //$rst=$this->Checkcourse_list_attendData_Mod($course_list_attendMod);
                $course_list_attendMod->create_time=time();
                $course_list_attendMod->modify_time=time();

                $result =   $course_list_attendMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($course_list_attendMod->getError());
            }

        }else{

            $PageTitle = L('添加课程预约图集');
            $PageMenu = array(
                array( U('course_list_attend/listing_course_list_attenddesign'), L('课程预约图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_course_list_attenddesign()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->Checkcourse_list_attendData_Post($id);

            $course_list_attendMod = M('course_list_attenddesign');

            if($course_list_attendMod->create()) {

                //$rst=$this->Checkcourse_list_attendData_Mod($course_list_attendMod,$id);
                $course_list_attendMod->modify_time=time();

                $result =   $course_list_attendMod->save();
                if($result) {
                    $this->success('操作成功！', U('course_list_attend/edit_course_list_attenddesign', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($course_list_attendMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $course_list_attendMod = M('course_list_attenddesign');
            // 读取数据
            $data =   $course_list_attendMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑课程预约图集');
            $PageMenu = array(
                array( U('course_list_attend/create_course_list_attenddesign'), L('添加课程预约图集') ),
                array( U('course_list_attend/listing_course_list_attenddesign'), L('课程预约图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }

	
    //导出活动预约数据
    public function export_attend()
    {
    	
    	
        $class_id=$_GET['class_id'];
        
        
        $andsql="";
        
        
        if(!empty($class_id)){
        	$andsql=$andsql." and class_id=".addslashes($class_id);
        }
        
        
        
        $CityMod = M('course_list_attend_attend');
        $toShow['banner'] = $CityMod->where(" status=1 ".$andsql )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;
		
		
		
		
		
		//所有项目
		$CityMod = M('course_list_attend');
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
        $output .= "预约ID编号"
        	.$expstr."课程预约名"
        	.$expstr."价格"
            .$expstr."导师"
            .$expstr."姓名"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            .$expstr."手机"
            
            .$expstr."电话"
            .$expstr."邮箱"
            .$expstr."居住地址"
            .$expstr."现有资产"
            .$expstr."意向国家或地区"
            .$expstr."提交时间"
            .$expenter;

        
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
                
                
                
                if(isset($allProductList[$v['class_id']])){
                    $toShow['banner'][$k]['class_price']=$allProductList[$v['class_id']]['price'];
                }
                else{
                    $toShow['banner'][$k]['class_price']="";
                }
                
                
                
                if(isset($allProductList[$v['class_id']])){
                    $toShow['banner'][$k]['class_daoshi']=$allProductList[$v['class_id']]['daoshi'];
                }
                else{
                    $toShow['banner'][$k]['class_daoshi']="";
                }
                
                //echo $toShow['banner'][$k]['class_daoshi'];exit;
                
                
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
                    	.$expstr.$toShow['banner'][$k]['class_price']
                    	.$expstr.$toShow['banner'][$k]['class_daoshi']
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


header('Cache-control: private');
header('Content-Disposition: attachment; filename='.$downloadfilename);



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





}
?>