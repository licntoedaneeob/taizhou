<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class linkAction extends TAction
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
            $linkMod = M('link');
            $sql=" id in (".$in.") ";
            $linkMod->where($sql)->delete();

            $this->success('删除成功', U('link/listing'));
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
		
		

        $this->ModManager = M('link');
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
		$rst=$this->GeneralActionForListing('link', $sqlWhere, $sqlOrder, '', 'M');
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
        
        

		$PageTitle = L('友情链接列表');
		$PageMenu = array(
			array( U('link/create'), L('添加友情链接') ),
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
		
		$allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );
        
        if(isset($_POST['dosubmit'])){

            $linkMod = M('link');

            //$rst=$this->ChecklinkData_Post();

            if (false === $linkMod->create()) {
                $this->error($module->getError());
            }

            if($linkMod->create()) {

                //$rst=$this->ChecklinkData_Mod($linkMod);
                $linkMod->create_time=time();
                $linkMod->modify_time=time();
				$linkMod->start_time=str_replace("/","-",$this->REQUEST('start_time'));
                $linkMod->end_time=str_replace("/","-",$this->REQUEST('end_time'));
                
                $result =   $linkMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($linkMod->getError());
            }

        }else{

            $PageTitle = L('添加友情链接');
            $PageMenu = array(
                array( U('link/listing'), L('友情链接列表') ),
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
    	$allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            //$rst=$this->ChecklinkData_Post($id);

	        $linkMod = M('link');

	        if($linkMod->create()) {
	        	
                //$rst=$this->ChecklinkData_Mod($linkMod,$id);
                $linkMod->modify_time=time();
                $linkMod->start_time=str_replace("/","-",$this->REQUEST('start_time'));
                $linkMod->end_time=str_replace("/","-",$this->REQUEST('end_time'));

	            $result =   $linkMod->save();
	            if($result) {
	                $this->success('操作成功！', U('link/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($linkMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $linkMod = M('link');
		    // 读取数据
		    $data =   $linkMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('用户数据读取错误');
		    }
    
			$PageTitle = L('编辑友情链接');
			$PageMenu = array(
					array( U('link/create'), L('添加友情链接') ),
					array( U('link/listing'), L('友情链接列表') ),
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
		$module = $UserMod = M('link');
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

	private function ChecklinkData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function ChecklinkData_Mod(&$User, $user_id=0){
		///检查 $User 模型数据。$User->email

	}





    //找所有一级分类和二级分类
    public function getAllClassList(){
		
		
        $CityMod = M('link_class');
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
    
    




/*友情链接图集*/


    public function listing_linkphoto()
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
            $linkMod = M('linkphoto');
            $sql=" id in (".$in.") ";
            $linkMod->where($sql)->delete();

            $this->success('删除成功', U('link/listing_linkphoto'));
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

        $f_search = $this->REQUEST('_f_search');
        if( $f_search != '' ){
            $sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' )";
        }

        $this->ModManager = M('linkphoto');
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
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('linkphoto', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('友情链接图集列表');
        $PageMenu = array(
            array( U('link/create_linkphoto'), L('添加友情链接图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_linkphoto()
    {
        $module = $UserMod = M('linkphoto');
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


    public function create_linkphoto()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $linkMod = M('linkphoto');

            //$rst=$this->ChecklinkData_Post();

            if (false === $linkMod->create()) {
                $this->error($module->getError());
            }

            if($linkMod->create()) {

                //$rst=$this->ChecklinkData_Mod($linkMod);
                $linkMod->create_time=time();
                $linkMod->modify_time=time();

                $result =   $linkMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($linkMod->getError());
            }

        }else{

            $PageTitle = L('添加友情链接图集');
            $PageMenu = array(
                array( U('link/listing_linkphoto'), L('友情链接图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_linkphoto()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->ChecklinkData_Post($id);

            $linkMod = M('linkphoto');

            if($linkMod->create()) {

                //$rst=$this->ChecklinkData_Mod($linkMod,$id);
                $linkMod->modify_time=time();

                $result =   $linkMod->save();
                if($result) {
                    $this->success('操作成功！', U('link/edit_linkphoto', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($linkMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $linkMod = M('linkphoto');
            // 读取数据
            $data =   $linkMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑友情链接图集');
            $PageMenu = array(
                array( U('link/create_linkphoto'), L('添加友情链接图集') ),
                array( U('link/listing_linkphoto'), L('友情链接图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }





}
?>