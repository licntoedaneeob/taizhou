<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class storeAction extends TAction
{




    /**
     *--------------------------------------------------------------+
     * Action: 街镇列表
     *--------------------------------------------------------------+
     */
    public function listing()
    {
		
		
        $provlist=$this->getProvList();
        $this->assign('provlist', $provlist );
        //echo "<pre>";print_r($provlist);exit;
        
        $citylistClean=$this->getCityListClean();
        $this->assign('citylistClean', $citylistClean );
        //echo "<pre>";print_r($citylistClean);exit;
        
        $streetlistClean=$this->getStreetListClean();
        $this->assign('streetlistClean', $streetlistClean );
        //echo "<pre>";print_r($streetlistClean);exit;
        
        
        
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
            $NewsMod = M('store');
            $sql=" id in (".$in.") ";
            $NewsMod->where($sql)->delete();

            $this->success('删除成功', U('store/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC";

        
        //附加查询条件
		$filter_prov_id = $this->REQUEST('_filter_prov_id');
        $filter_city_id = $this->REQUEST('_filter_city_id');
        $filter_street_id = $this->REQUEST('_filter_street_id');
        
        
        if( $filter_prov_id != '' ){
            $sqlWhere .= " and prov_id = ". addslashes($filter_prov_id)." ";
        }
        if( $filter_city_id != '' ){
            $sqlWhere .= " and city_id = ". addslashes($filter_city_id)." ";
        }
        if( $filter_street_id != '' ){
            $sqlWhere .= " and street_id = ". addslashes($filter_street_id)." ";
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
		
		
		
		
        if ($this->CurrAccount['Account-Role']=='sales'){
	        $store_id = $this->CurrAccount['Account-Code'];
	        $sqlWhere .= " and id = ". addslashes($store_id)." ";
        }
        
        

        $this->ModManager = M('store');
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
        $this->assign('filter_prov_id',  $filter_prov_id);
        $this->assign('filter_city_id',  $filter_city_id);
        $this->assign('filter_street_id',  $filter_street_id);
        
        
        ///获取列表数据集
        $rst=$this->GeneralActionForListing('store', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
            	$rst['dataset'][$k]['prov_name'] = $provlist[$v['prov_id']]['title'];
                $rst['dataset'][$k]['city_name'] = $citylistClean[$v['city_id']]['title'];
                $rst['dataset'][$k]['street_name'] = $streetlistClean[$v['street_id']]['title'];
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst['dataset']);exit;

        $PageTitle = L('店铺列表');
        
        if ($this->CurrAccount['Account-Role']=='sales'){
	        $PageMenu = array(
	            //array( U('store/create'), L('添加店铺') ),
	        );
        }
        else{
       	$PageMenu = array(
	            array( U('store/create'), L('添加店铺') ),
	        );
	}
        
        
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }



    /**
     *--------------------------------------------------------------+
     * Action: 街镇 改状态
     *--------------------------------------------------------------+
     */
    public function ajax_change_status()
    {
        $module = $UserMod = M('store');
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
     * Action: 街镇 添加
     *--------------------------------------------------------------+
     */
    public function create()
    {



        $provlist=$this->getProvList();
        $this->assign('provlist', $provlist );
        //echo "<pre>";print_r($provlist);exit;

        if(isset($_POST['dosubmit'])){

            $NewsMod = M('store');

            //$rst=$this->CheckNewsData_Post();

            if (false === $NewsMod->create()) {
                $this->error($module->getError());
            }

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod);
                
                $rst=$this->Check_store_Data_Mod($NewsMod);
                
                $NewsMod->create_time=time();
				$NewsMod->modify_time=time();
				

                $result =   $NewsMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{

            $PageTitle = L('添加店铺');
            $PageMenu = array(
                array( U('store/listing'), L('店铺列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }





    /**
     *--------------------------------------------------------------+
     * Action:  街镇 修改
     *--------------------------------------------------------------+
     */
    public function edit()
    {

        $provlist=$this->getProvList();
        $this->assign('provlist', $provlist );



        $citylist=$this->getCityList();
        $this->assign('citylist', $citylist );
        //echo "<pre>";print_r($citylist);exit;
        
        
        
        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckNewsData_Post($id);

            $NewsMod = M('store');

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod,$id);
                
                $rst=$this->Check_store_Data_Mod($NewsMod,$id);
                
                $NewsMod->modify_time=time();
				
				
                $result =   $NewsMod->save();
                if($result) {
                    $this->success('操作成功！', U('store/edit', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $NewsMod = M('store');
            // 读取数据
            $data =   $NewsMod->find($id);
            if($data) {
            	
            	
            	//$cityinfo =   $NewsMod->find($data['pid']);
            	//echo "<pre>";print_r($cityinfo);exit;
            	
            	//$data['prov_id']=$cityinfo['pid'];
            	//$data['city_name']=$cityinfo['title'];
            	
            	//echo "<pre>";print_r($data);exit;
            	
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑店铺');
            
            
            if ($this->CurrAccount['Account-Role']=='sales'){
	        $PageMenu = array(
                //array( U('store/create'), L('添加店铺') ),
                array( U('store/listing'), L('店铺列表') ),
	            );
	        }
	        else{
	       	$PageMenu = array(
	                array( U('store/create'), L('添加店铺') ),
	                array( U('store/listing'), L('店铺列表') ),
	            );
		}
	
	
            
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    
    
    
    
    
    
    
    
    
//找所有省份
    public function getProvList(){
        //echo $parent_id;exit;
        $CityMod = M('region');
        $result = $CityMod->where(" status=1 and pid='0' and level=0 " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        
        $allclasslist=array();
        if(isset($result) && !empty($result) ){
            foreach($result as $k => $v){
                $allclasslist[$v['id']]=$v;
            }
        }
        
        $datas = $allclasslist;
        return $datas;
    }
    



//找所有市区 Clean
    public function getCityListClean(){
    	
        
        //echo $parent_id;exit;
        $CityMod = M('region');
        $result = $CityMod->where(" status=1 and pid>0 and level=1 " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        
        
        
        $allclasslist=array();
        if(isset($result) && !empty($result) ){
            foreach($result as $k => $v){
                $allclasslist[$v['id']]=$v;
            }
        }
        
        $datas = $allclasslist;
        
        
        
        //echo "<pre>";print_r($datas);exit;
        
        return $datas;
    }
    





//找所有街镇 Clean
    public function getStreetListClean(){
    	
        
        //echo $parent_id;exit;
        $CityMod = M('region');
        $result = $CityMod->where(" status=1 and pid>0 and level=2 " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        
        
        
        $allclasslist=array();
        if(isset($result) && !empty($result) ){
            foreach($result as $k => $v){
                $allclasslist[$v['id']]=$v;
            }
        }
        
        $datas = $allclasslist;
        
        
        
        //echo "<pre>";print_r($datas);exit;
        
        return $datas;
    }
    



//找所有市区
    public function getCityList(){
    	
    	$provlist=$this->getProvList();
        $this->assign('provlist', $provlist );
        //echo "<pre>";print_r($provlist);exit;
        
        
        //echo $parent_id;exit;
        $CityMod = M('region');
        $result = $CityMod->where(" status=1 and pid>0 and level=1 " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        
        $datas=array();
        if(!empty($result)){
            foreach($result as $k => $v){
            	$tmp=array();
            	$tmp=$v;
            	$tmp['prov_name'] = $provlist[$v['pid']]['title'];
                $datas[$v['id']] = $tmp;
            }
        }
        
        //echo "<pre>";print_r($datas);exit;
        
        return $datas;
    }
    





//找所有街镇
    public function getStreetList(){
    	
    	$citylist=$this->getCityList();
        
        //echo "<pre>";print_r($citylist);exit;
        
        $CityMod = M('region');
        $result = $CityMod->where(" status=1 and pid>0 and level=2 " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        
        
        
        $datas=array();
        if(!empty($result)){
            foreach($result as $k => $v){
            	$tmp=array();
            	$tmp=$v;
            	$tmp['city_id'] = $citylist[$v['pid']]['id'];
            	$tmp['city_name'] = $citylist[$v['pid']]['title'];
            	$tmp['prov_id'] = $citylist[$v['pid']]['pid'];
            	$tmp['prov_name'] = $citylist[$v['pid']]['prov_name'];
                $datas[$v['id']] = $tmp;
            }
        }
        
        //echo "<pre>";print_r($datas);exit;
        
        return $datas;
        
    }
    




//切换省份，重置城市
    public function ajax_select_prov()
    {
        
        $prov_id 	= intval($_REQUEST['pid']);
        $defaultid 	= intval($_REQUEST['defaultid']);
        
        $CityMod = M('region');
        $result = $CityMod->where(" status=1 and pid='".addslashes($prov_id)."' and level=1 " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        
        $output="";
        $output.="<option value=''> - 请选择市区 - </option>";
        if (!empty($result)){
			foreach($result as $k=>$v){
				$isselected=($defaultid==$v["id"])?" selected ":"";
				$output.="<option value='".$v["id"]."' ".$isselected.">".$v["title"]."</option>";
			}
		}

        
        $this->ajaxReturn(array('status' => 'true', 'result' => $output));
        
    }
    
    
    
    
//切换市区，重置街镇
    public function ajax_select_city()
    {
        
        $city_id 	= intval($_REQUEST['pid']);
        $defaultid 	= intval($_REQUEST['defaultid']);
        
        $CityMod = M('region');
        $result = $CityMod->where(" status=1 and pid='".addslashes($city_id)."' and level=2 " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        
        $output="";
        $output.="<option value=''> - 请选择街镇 - </option>";
        if (!empty($result)){
			foreach($result as $k=>$v){
				$isselected=($defaultid==$v["id"])?" selected ":"";
				$output.="<option value='".$v["id"]."' ".$isselected.">".$v["title"]."</option>";
			}
		}

        
        $this->ajaxReturn(array('status' => 'true', 'result' => $output));
        
    }
    
    
    
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
     * Action: 省份
     *--------------------------------------------------------------+
     */
    public function listing_prov()
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

            //删除自身数据
            $NewsMod = M('store');
            $sql=" id in (".$in.") ";
            $NewsMod->where($sql)->delete();

            $this->success('删除成功', U('store/listing_prov'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC ";

        $sqlWhere .= " and pid=0 ";

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

        $this->ModManager = M('store');
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
        $rst=$this->GeneralActionForListing('store', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        $PageTitle = L('省份列表');
        $PageMenu = array(
            array( U('store/create_prov'), L('添加省份') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }
    
    
    
    
    
    
    /**
     *--------------------------------------------------------------+
     * Action: 省 改状态
     *--------------------------------------------------------------+
     */
    public function ajax_change_status_prov()
    {
        $module = $UserMod = M('store');
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
     * Action: 省 添加
     *--------------------------------------------------------------+
     */
    public function create_prov()
    {
		
        if(isset($_POST['dosubmit'])){

            $NewsMod = M('store');

            //$rst=$this->CheckNewsData_Post();

            if (false === $NewsMod->create()) {
                $this->error($module->getError());
            }

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod);
                $NewsMod->create_time=time();
                $NewsMod->modify_time=time();

                $result =   $NewsMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{

            $PageTitle = L('添加省份');
            $PageMenu = array(
                array( U('store/listing_prov'), L('省份列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }


    /**
     *--------------------------------------------------------------+
     * Action:  省 修改
     *--------------------------------------------------------------+
     */
    public function edit_prov()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckNewsData_Post($id);

            $NewsMod = M('store');

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod,$id);
                $NewsMod->modify_time=time();

                $result =   $NewsMod->save();
                if($result) {
                    $this->success('操作成功！', U('store/edit_classfather', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $NewsMod = M('store');
            // 读取数据
            $data =   $NewsMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑省份');
            $PageMenu = array(
                array( U('store/create_prov'), L('添加省份') ),
                array( U('store/listing_prov'), L('省份列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }




    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     *--------------------------------------------------------------+
     * Action: 市区列表
     *--------------------------------------------------------------+
     */
    public function listing_city()
    {
		
		$provlist=$this->getProvList();
        $this->assign('provlist', $provlist );
        //echo "<pre>";print_r($provlist);exit;
        
        
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
            $NewsMod = M('store');
            $sql=" id in (".$in.") ";
            $NewsMod->where($sql)->delete();

            $this->success('删除成功', U('store/listing_city'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC";

        $sqlWhere .= " and pid>0 and level=1 ";

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

        $this->ModManager = M('store');
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
        $rst=$this->GeneralActionForListing('store', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
                $rst['dataset'][$k]['prov_name'] = $provlist[$v['pid']]['title'];
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst);exit;

        $PageTitle = L('市区列表');
        $PageMenu = array(
            array( U('store/create_city'), L('添加市区') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }



    /**
     *--------------------------------------------------------------+
     * Action: 市区 改状态
     *--------------------------------------------------------------+
     */
    public function ajax_change_status_city()
    {
        $module = $UserMod = M('store');
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
     * Action: 市区 添加
     *--------------------------------------------------------------+
     */
    public function create_city()
    {



        $provlist=$this->getProvList();
        $this->assign('provlist', $provlist );
        //echo "<pre>";print_r($provlist);exit;

        if(isset($_POST['dosubmit'])){

            $NewsMod = M('store');

            //$rst=$this->CheckNewsData_Post();

            if (false === $NewsMod->create()) {
                $this->error($module->getError());
            }

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod);
                $NewsMod->create_time=time();
				$NewsMod->modify_time=time();
				$NewsMod->level=1;

                $result =   $NewsMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{

            $PageTitle = L('添加市区');
            $PageMenu = array(
                array( U('store/listing_city'), L('市区列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }





    /**
     *--------------------------------------------------------------+
     * Action:  市区 修改
     *--------------------------------------------------------------+
     */
    public function edit_city()
    {

        $provlist=$this->getProvList();
        $this->assign('provlist', $provlist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckNewsData_Post($id);

            $NewsMod = M('store');

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod,$id);
                $NewsMod->modify_time=time();
				$NewsMod->level=1;
				
                $result =   $NewsMod->save();
                if($result) {
                    $this->success('操作成功！', U('store/edit_city', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $NewsMod = M('store');
            // 读取数据
            $data =   $NewsMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑市区');
            $PageMenu = array(
                array( U('store/create_city'), L('添加市区') ),
                array( U('store/listing_city'), L('市区列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     *--------------------------------------------------------------+
     * Action: 街镇列表
     *--------------------------------------------------------------+
     */
    public function listing_street()
    {
		
		$provlist=$this->getProvList();
        $this->assign('provlist', $provlist );
        //echo "<pre>";print_r($provlist);exit;
        
        
        $citylist=$this->getCityList();
        $this->assign('citylist', $citylist );
        //echo "<pre>";print_r($citylist);exit;
        
        
        
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
            $NewsMod = M('store');
            $sql=" id in (".$in.") ";
            $NewsMod->where($sql)->delete();

            $this->success('删除成功', U('store/listing_street'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC";

        $sqlWhere .= " and pid>0 and level=2 ";

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

        $this->ModManager = M('store');
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
        $rst=$this->GeneralActionForListing('store', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
                $rst['dataset'][$k]['city_name'] = $citylist[$v['pid']]['title'];
                $rst['dataset'][$k]['prov_name'] = $citylist[$v['pid']]['prov_name'];
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst['dataset']);exit;

        $PageTitle = L('街镇列表');
        $PageMenu = array(
            array( U('store/create_street'), L('添加街镇') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }



    /**
     *--------------------------------------------------------------+
     * Action: 街镇 改状态
     *--------------------------------------------------------------+
     */
    public function ajax_change_status_street()
    {
        $module = $UserMod = M('store');
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
     * Action: 街镇 添加
     *--------------------------------------------------------------+
     */
    public function create_street()
    {



        $provlist=$this->getProvList();
        $this->assign('provlist', $provlist );
        //echo "<pre>";print_r($provlist);exit;

        if(isset($_POST['dosubmit'])){

            $NewsMod = M('store');

            //$rst=$this->CheckNewsData_Post();

            if (false === $NewsMod->create()) {
                $this->error($module->getError());
            }

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod);
                $NewsMod->create_time=time();
				$NewsMod->modify_time=time();
				$NewsMod->level=2;

                $result =   $NewsMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{

            $PageTitle = L('添加街镇');
            $PageMenu = array(
                array( U('store/listing_street'), L('街镇列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }





    /**
     *--------------------------------------------------------------+
     * Action:  街镇 修改
     *--------------------------------------------------------------+
     */
    public function edit_street()
    {

        $provlist=$this->getProvList();
        $this->assign('provlist', $provlist );



        $citylist=$this->getCityList();
        $this->assign('citylist', $citylist );
        //echo "<pre>";print_r($citylist);exit;
        
        
        
        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckNewsData_Post($id);

            $NewsMod = M('store');

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod,$id);
                $NewsMod->modify_time=time();
				$NewsMod->level=2;
				
                $result =   $NewsMod->save();
                if($result) {
                    $this->success('操作成功！', U('store/edit_street', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $NewsMod = M('store');
            // 读取数据
            $data =   $NewsMod->find($id);
            if($data) {
            	
            	
            	$cityinfo =   $NewsMod->find($data['pid']);
            	//echo "<pre>";print_r($cityinfo);exit;
            	$data['prov_id']=$cityinfo['pid'];
            	$data['city_name']=$cityinfo['title'];
            	
            	//echo "<pre>";print_r($data);exit;
            	
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑街镇');
            $PageMenu = array(
                array( U('store/create_street'), L('添加街镇') ),
                array( U('store/listing_street'), L('街镇列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    


    //通过市区的 parent_id 找省
    public function getParentClass($parent_id){
        //echo $parent_id;exit;
        $CityMod = M('store');
        $result = $CityMod->where(" status=1 and id='".$parent_id."' " )->order('id ASC')->select();
        //echo "<pre>";print_r($result);exit;
        if(isset($result[0])){
            $datas = $result[0];
        }
        else{
            $datas = array();
        }
        return $datas;
    }

    //找所有省
    public function getParentClassList(){
        //echo $parent_id;exit;
        $CityMod = M('store');
        $result = $CityMod->where(" status=1 and parent_id='0' " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        $datas = $result;
        return $datas;
    }


    //通过省的 id 找市区
    public function getSonClass($parent_id){
        $CityMod = M('store');
        $result = $CityMod->where(" status=1 and parent_id='".$parent_id."' " )->order('sort ASC')->select();
        //echo "<pre>";print_r($result);exit;
        $datas = $result;
        return $datas;
    }


    //找所有省和市区
    public function getAllClassList(){

        $CityMod = M('store');
        $parent_list = $CityMod->where(" status=1 and parent_id='0' " )->order('sort ASC')->select();
        //echo "<pre>";print_r($parent_list);exit;

        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
                $allclasslist[$v['id']]=$v;
                $son_class_list=$this->getSonClass($v['id']);
                foreach($son_class_list as $k2 => $v2){
                    $v2['title']=$v['title']." -- ".$v2['title'];
                    $allclasslist[$v2['id']]=$v2;
                }
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }






    /**
     *--------------------------------------------------------------+
     * Action: 照片列表
     *--------------------------------------------------------------+
     */
    public function listing_store()
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
            $NewsMod = M('store');
            $sql=" id in (".$in.") ";
            $NewsMod->where($sql)->delete();

            $this->success('删除成功', U('store/listing_store'));
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
		
		

        $this->ModManager = M('store');
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
        $rst=$this->GeneralActionForListing('store', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('店铺列表');
        $PageMenu = array(
            array( U('store/create_store'), L('添加店铺') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }




    /**
     *--------------------------------------------------------------+
     * Action: 照片 改状态
     *--------------------------------------------------------------+
     */
    public function ajax_change_status_store()
    {
        $module = $UserMod = M('store');
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
     * Action: 照片 添加
     *--------------------------------------------------------------+
     */
    public function create_store()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $NewsMod = M('store');

            //$rst=$this->CheckNewsData_Post();

            if (false === $NewsMod->create()) {
                $this->error($module->getError());
            }

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod);
                $NewsMod->create_time=time();
				$NewsMod->modify_time=time();

                $result =   $NewsMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{

            $PageTitle = L('添加店铺');
            $PageMenu = array(
                array( U('store/listing_store'), L('店铺列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    /**
     *--------------------------------------------------------------+
     * Action:  照片 修改
     *--------------------------------------------------------------+
     */
    public function edit_store()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckNewsData_Post($id);

            $NewsMod = M('store');

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod,$id);
                $NewsMod->modify_time=time();

                $result =   $NewsMod->save();
                if($result) {
                    $this->success('操作成功！', U('store/edit_store', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $NewsMod = M('store');
            // 读取数据
            $data =   $NewsMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑店铺');
            $PageMenu = array(
                array( U('store/create_store'), L('添加店铺') ),
                array( U('store/listing_store'), L('店铺列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
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

	private function CheckNewsData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function Check_store_Data_Mod(&$User, $id=0){
		///检查 $User 模型数据。$User->email
		
		$UserMod = M('store');
		
		$result = $UserMod->where("username='%s' and id!=%d ", $User->username, $id )->count();
		if($result>0){
        $this->error(L('存在重复的用户名'));
        }
        
        

	}


}
?>