<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class newsmbAction extends TAction
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

            //删除自身数据
            $newsmbMod = M('newsmb');
            $sql=" id in (".$in.") ";
            $newsmbMod->where($sql)->delete();

            $this->success('删除成功', U('newsmb/listing'));
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
		
		
		
		
		
		
		
		

        $this->ModManager = M('newsmb');
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
		
		
		//新搜索
		$this->assign('filter_fieldname',  $filter_fieldname);
		$this->assign('filter_starttime',  $filter_starttime);
		$this->assign('filter_endtime',  $filter_endtime);
		
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

		///获取列表数据集		
		$rst=$this->GeneralActionForListing('newsmb', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('新闻列表');
		$PageMenu = array(
			array( U('newsmb/create'), L('添加新闻') ),
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
			//echo "<pre>";print_r($_POST);exit;
			
            $newsmbMod = M('newsmb');

            //$rst=$this->ChecknewsmbData_Post();

            if (false === $newsmbMod->create()) {
                $this->error($module->getError());
            }

            if($newsmbMod->create()) {
				
				//echo "<pre>";print_r($_POST);exit;
				
				$keyword="";
				if(isset($_POST['keyword']) && !empty($_POST['keyword'])){
					$keyword=implode("|", $_POST['keyword']);
				}
				//var_dump($keyword);exit;
				
                //$rst=$this->ChecknewsmbData_Mod($newsmbMod);
                $newsmbMod->create_time=time();
                $newsmbMod->modify_time=time();
                $newsmbMod->keyword=$keyword;
                //$newsmbMod->newsmb_id_relative=$_POST['newsmb_id_relative'];
                

                $result =   $newsmbMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($newsmbMod->getError());
            }

        }else{

            $PageTitle = L('添加新闻');
            $PageMenu = array(
                array( U('newsmb/listing'), L('新闻列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            
            
            $allkeywordlist=$this->getAllKeywordList();
            //echo "<pre>";print_r($allkeywordlist);exit;
            $this->assign('allkeywordlist', $allkeywordlist);
            
            
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
    	///注意：老用户用户已填写情况下不允许修改用户名，用户名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复
		//echo "<pre>";print_r($_POST);exit;
		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            //$rst=$this->ChecknewsmbData_Post($id);

	        $newsmbMod = M('newsmb');

	        if($newsmbMod->create()) {
	        	
	        	
				$keyword="";
				if(isset($_POST['keyword']) && !empty($_POST['keyword'])){
					$keyword=implode("|", $_POST['keyword']);
				}
				
				
                //$rst=$this->ChecknewsmbData_Mod($newsmbMod,$id);
                $newsmbMod->modify_time=time();
                $newsmbMod->keyword=$keyword;
                

	            $result =   $newsmbMod->save();
	            if($result) {
	                $this->success('操作成功！', U('newsmb/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($newsmbMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $newsmbMod = M('newsmb');
		    // 读取数据
		    $data =   $newsmbMod->find($id);
		    //var_dump($data);exit;
		    
		    
		    //获得上一篇和下一篇
		    $data['newsmb_id_prev_title']="";
		    $data['newsmb_id_next_title']="";
		    if(!empty($data['newsmb_id_prev'])){
		    	$newsmb_prev =   $newsmbMod->find($data['newsmb_id_prev']);
		    	$data['newsmb_id_prev_title'] = $newsmb_prev['title'];
		    }
		    if(!empty($data['newsmb_id_next'])){
		    	$newsmb_next =   $newsmbMod->find($data['newsmb_id_next']);
		    	$data['newsmb_id_next_title'] = $newsmb_next['title'];
		    }
		    
		    
		    //相关文章
		    $data['newsmb_id_relative_title']="";
			$newsmb_id_relative=array();
			$newsmb_id_relative_text=array();
			if(!empty($data['newsmb_id_relative'])){
				$newsmb_id_relative=explode("||", $data['newsmb_id_relative']);
				//echo "<pre>";print_r($newsmb_id_relative);exit;
				if(!empty($newsmb_id_relative)){
					foreach ($newsmb_id_relative as $k=>$v){
						$rel_id=str_replace("|","",$v);
						$rel_data =   $newsmbMod->find($rel_id);
						if(isset($rel_data['title'])){
							$newsmb_id_relative_text[] = $rel_data['title'];
						}
					}
					if(!empty($newsmb_id_relative_text)){
						$data['newsmb_id_relative_title']="|".implode("|"."\n"."|", $newsmb_id_relative_text)."|"."\n";
					}
				}
			}
			//echo $data['newsmb_id_relative_title'];exit;
			
			
			
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		        
		    }else{
		        $this->error('用户数据读取错误');
		    }
    
			$PageTitle = L('编辑新闻');
			$PageMenu = array(
					array( U('newsmb/create'), L('添加新闻') ),
					array( U('newsmb/listing'), L('新闻列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			
			
			
			$keyword=array();
			if(!empty($data['keyword'])){
				$keyword=explode("|", $data['keyword']);
			}
			//echo "<pre>";print_r($keyword);exit;
			
			$allkeywordlist=$this->getAllKeywordList();
            //echo "<pre>";print_r($allkeywordlist);exit;
            
            if( !empty($allkeywordlist) && !empty($keyword) ){
            	foreach($allkeywordlist as $k=>$v){
            		if( in_array($v['title'],$keyword) ){
            			$allkeywordlist[$k]['checked']=1;
            		}
            		else{
            			$allkeywordlist[$k]['checked']=0;
            		}
            	}
            }
            //echo "<pre>";print_r($allkeywordlist);exit;
            $this->assign('allkeywordlist', $allkeywordlist);
            
            
            
            
            
            
            
            
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
		$module = $UserMod = M('newsmb');
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

	private function ChecknewsmbData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function ChecknewsmbData_Mod(&$User, $user_id=0){
		///检查 $User 模型数据。$User->email

	}




	//找所有关键字
    public function getAllKeywordList(){

        $CityMod = M('keyword');
        $parent_list = $CityMod->field('id,title')->where(" status=1 " )->group('title')->order('sort asc')->select();
        
        //echo "<pre>";print_r($parent_list);exit;
		return $parent_list;
		
        //$allclasslist=array();
        //if(isset($parent_list)){
        //    foreach($parent_list as $k => $v){
        //        $allclasslist[$v['id']]=$v;
        //    }
        //}
        //echo "<pre>";print_r($allclasslist);exit;
        //return $allclasslist;
    }
    
    
    
    
    
    
    
	//搜索上一篇和下一篇
	public function listing_prev()
	{
        if(isset($_GET['input_id'])){
        	$input_id=$_GET['input_id'];
        }
        else{
        	$input_id="";
        }
        $this->assign('input_id',   $input_id );
        
        /*
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
            $newsmbMod = M('newsmb');
            $sql=" id in (".$in.") ";
            $newsmbMod->where($sql)->delete();

            $this->success('删除成功', U('newsmb/listing'));
            exit;
        }
        */


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
		
		
		
		

        $this->ModManager = M('newsmb');
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
		$rst=$this->GeneralActionForListing('newsmb', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('新闻列表');
		//$PageMenu = array(
		//	array( U('newsmb/create'), L('添加新闻') ),
		//);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}


    
    
    
	//相关文章
	public function listing_relative()
	{
        if(isset($_GET['input_id'])){
        	$input_id=$_GET['input_id'];
        }
        else{
        	$input_id="";
        }
        $this->assign('input_id',   $input_id );
        
        /*
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
            $newsmbMod = M('newsmb');
            $sql=" id in (".$in.") ";
            $newsmbMod->where($sql)->delete();

            $this->success('删除成功', U('newsmb/listing'));
            exit;
        }
        */


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
		
		
		

        $this->ModManager = M('newsmb');
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
		$rst=$this->GeneralActionForListing('newsmb', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('新闻列表');
		//$PageMenu = array(
		//	array( U('newsmb/create'), L('添加新闻') ),
		//);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}



    //找所有一级分类和二级分类
    public function getAllClassList(){

        $CityMod = M('newsmb');
        $parent_list = $CityMod->field('id,title')->where(" status=1 " )->order('id desc')->select();
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



/*新闻图集*/


    public function listing_newsmbphoto()
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
            $newsmbMod = M('newsmbphoto');
            $sql=" id in (".$in.") ";
            $newsmbMod->where($sql)->delete();

            $this->success('删除成功', U('newsmb/listing_newsmbphoto'));
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

        $this->ModManager = M('newsmbphoto');
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
        $rst=$this->GeneralActionForListing('newsmbphoto', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('新闻图集列表');
        $PageMenu = array(
            array( U('newsmb/create_newsmbphoto'), L('添加新闻图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_newsmbphoto()
    {
        $module = $UserMod = M('newsmbphoto');
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


    public function create_newsmbphoto()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $newsmbMod = M('newsmbphoto');

            //$rst=$this->ChecknewsmbData_Post();

            if (false === $newsmbMod->create()) {
                $this->error($module->getError());
            }

            if($newsmbMod->create()) {

                //$rst=$this->ChecknewsmbData_Mod($newsmbMod);
                $newsmbMod->create_time=time();
                $newsmbMod->modify_time=time();

                $result =   $newsmbMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($newsmbMod->getError());
            }

        }else{

            $PageTitle = L('添加新闻图集');
            $PageMenu = array(
                array( U('newsmb/listing_newsmbphoto'), L('新闻图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_newsmbphoto()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->ChecknewsmbData_Post($id);

            $newsmbMod = M('newsmbphoto');

            if($newsmbMod->create()) {

                //$rst=$this->ChecknewsmbData_Mod($newsmbMod,$id);
                $newsmbMod->modify_time=time();

                $result =   $newsmbMod->save();
                if($result) {
                    $this->success('操作成功！', U('newsmb/edit_newsmbphoto', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($newsmbMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $newsmbMod = M('newsmbphoto');
            // 读取数据
            $data =   $newsmbMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑新闻图集');
            $PageMenu = array(
                array( U('newsmb/create_newsmbphoto'), L('添加新闻图集') ),
                array( U('newsmb/listing_newsmbphoto'), L('新闻图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }





}
?>