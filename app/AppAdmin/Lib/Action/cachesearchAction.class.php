<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class cachesearchAction extends TAction
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
            $cachesearchMod = M('cachesearch');
            $sql=" id in (".$in.") ";
            $cachesearchMod->where($sql)->delete();

            $this->success('删除成功', U('cachesearch/listing'));
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
		

        $this->ModManager = M('cachesearch');
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
		$rst=$this->GeneralActionForListing('cachesearch', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('搜索缓存列表');
		$PageMenu = array(
			array( U('cachesearch/create'), L('添加搜索缓存') ),
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
        
		header("Content-type:text/html;charset=utf-8");
		
		$cachedata=array();
        
        
        //新闻
        $sqlWhere = "status =1";
        $sqlOrder = " sort asc ,id DESC ";
        $this->ModManager = M('news');
        $fields = $this->ModManager->getDbFields();
        $rst=$this->GeneralActionForListing('news', $sqlWhere, $sqlOrder, '10000', 'M');
        if(isset($rst['dataset']) && count($rst['dataset'])>0){
        	$stars=$rst['dataset'];
        	foreach ($stars as $k => $item) {
				$content_show="";
				$content_show = $content_show.$this->html2text($item['summary'])."&nbsp;&nbsp;";
				$content_show = $content_show.$this->html2text($item['content'])."&nbsp;&nbsp;";
				$foreachdata['content']=$content_show;
				$foreachdata['boardname']="news";
				$foreachdata['view_allow']="0";
				$foreachdata['title']=$item['title'];
				$foreachdata['pic_show']=$item['pic_show'];
				$foreachdata['create_time']=$item['create_time'];
				$foreachdata['linkurl']=empty($item['linkurl'])?'http://'.$_SERVER["HTTP_HOST"]."/event/news_d/id/".$item['id']:$item['linkurl'];
				$cachedata[]=$foreachdata;
			}
        }
        
        //echo "<pre>";print_r($cachedata);exit;
        
        
        
        //活动
        $sqlWhere = "status =1";
        $sqlOrder = " sort asc ,id DESC ";
        $this->ModManager = M('activity');
        $fields = $this->ModManager->getDbFields();
        $rst=$this->GeneralActionForListing('activity', $sqlWhere, $sqlOrder, '10000', 'M');
        if(isset($rst['dataset']) && count($rst['dataset'])>0){
        	$stars=$rst['dataset'];
        	foreach ($stars as $k => $item) {
				$content_show="";
				$content_show = $content_show.$this->html2text($item['summary'])."&nbsp;&nbsp;";
				$content_show = $content_show.$this->html2text($item['content'])."&nbsp;&nbsp;";
				$foreachdata['content']=$content_show;
				$foreachdata['boardname']="activity";
				$foreachdata['view_allow']="0";
				$foreachdata['title']=$item['title'];
				$foreachdata['pic_show']=$item['pic_show'];
				$foreachdata['create_time']=$item['create_time'];
				$foreachdata['linkurl']=$item['link_url'];
				$cachedata[]=$foreachdata;
			}
        }
        
        
        //echo "<pre>";print_r($cachedata);exit;
        
        if(!empty($cachedata)){
        	//删除表内旧数据
        	$cachesearchMod = M('cachesearch');
        	$sql=" 1 ";
            $cachesearchMod->where($sql)->delete();
        	
        	$table_name=$cachesearchMod->getTableName();
        	foreach ($cachedata as $k => $item) {
				
	            $sql="insert into ".$table_name." SET title='".addslashes($item["title"])."'
	            , content='".addslashes($item["content"])."'
	            , linkurl='".addslashes($item["linkurl"])."'
	            , boardname='".addslashes($item["boardname"])."'
	            , view_allow='".addslashes($item["view_allow"])."'
	            , create_time='".addslashes($item["create_time"])."'
	            , pic_show='".addslashes($item["pic_show"])."'
	             ";
	            $result = $cachesearchMod->execute($sql);
                //echo var_dump($sql);echo "**";var_dump($result);echo "<br>";
			}
			
        }
        
        $this->success('操作成功！');



        }else{

            $PageTitle = L('刷新搜索缓存');
            $PageMenu = array(
                //array( U('cachesearch/listing'), L('搜索缓存列表') ),
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
    	///注意：老用户用户已填写情况下不允许修改用户名，用户名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckcachesearchData_Post($id);

	        $cachesearchMod = M('cachesearch');

	        if($cachesearchMod->create()) {
	        	
                //$rst=$this->CheckcachesearchData_Mod($cachesearchMod,$id);
                $cachesearchMod->modify_time=time();

	            $result =   $cachesearchMod->save();
	            if($result) {
	                $this->success('操作成功！', U('cachesearch/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($cachesearchMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $cachesearchMod = M('cachesearch');
		    // 读取数据
		    $data =   $cachesearchMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('用户数据读取错误');
		    }
    
			$PageTitle = L('编辑搜索缓存');
			$PageMenu = array(
					array( U('cachesearch/create'), L('添加搜索缓存') ),
					array( U('cachesearch/listing'), L('搜索缓存列表') ),
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
		$module = $UserMod = M('cachesearch');
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

	private function CheckcachesearchData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function CheckcachesearchData_Mod(&$User, $user_id=0){
		///检查 $User 模型数据。$User->email

	}





    //找所有一级分类和二级分类
    public function getAllClassList(){

        $CityMod = M('cachesearch');
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



/*搜索缓存图集*/


    public function listing_cachesearchphoto()
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
            $cachesearchMod = M('cachesearchphoto');
            $sql=" id in (".$in.") ";
            $cachesearchMod->where($sql)->delete();

            $this->success('删除成功', U('cachesearch/listing_cachesearchphoto'));
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

        $this->ModManager = M('cachesearchphoto');
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
        $rst=$this->GeneralActionForListing('cachesearchphoto', $sqlWhere, $sqlOrder, '', 'M');
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

        $PageTitle = L('搜索缓存图集列表');
        $PageMenu = array(
            array( U('cachesearch/create_cachesearchphoto'), L('添加搜索缓存图集') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }

    public function ajax_change_status_cachesearchphoto()
    {
        $module = $UserMod = M('cachesearchphoto');
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


    public function create_cachesearchphoto()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $cachesearchMod = M('cachesearchphoto');

            //$rst=$this->CheckcachesearchData_Post();

            if (false === $cachesearchMod->create()) {
                $this->error($module->getError());
            }

            if($cachesearchMod->create()) {

                //$rst=$this->CheckcachesearchData_Mod($cachesearchMod);
                $cachesearchMod->create_time=time();
                $cachesearchMod->modify_time=time();

                $result =   $cachesearchMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($cachesearchMod->getError());
            }

        }else{

            $PageTitle = L('添加搜索缓存图集');
            $PageMenu = array(
                array( U('cachesearch/listing_cachesearchphoto'), L('搜索缓存图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    public function edit_cachesearchphoto()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckcachesearchData_Post($id);

            $cachesearchMod = M('cachesearchphoto');

            if($cachesearchMod->create()) {

                //$rst=$this->CheckcachesearchData_Mod($cachesearchMod,$id);
                $cachesearchMod->modify_time=time();

                $result =   $cachesearchMod->save();
                if($result) {
                    $this->success('操作成功！', U('cachesearch/edit_cachesearchphoto', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($cachesearchMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $cachesearchMod = M('cachesearchphoto');
            // 读取数据
            $data =   $cachesearchMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑搜索缓存图集');
            $PageMenu = array(
                array( U('cachesearch/create_cachesearchphoto'), L('添加搜索缓存图集') ),
                array( U('cachesearch/listing_cachesearchphoto'), L('搜索缓存图集列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }





}
?>