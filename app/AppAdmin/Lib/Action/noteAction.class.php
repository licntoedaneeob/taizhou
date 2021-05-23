<?php
/**
 * 简单留言板系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class noteAction extends TAction
{
	

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
            $noteMod = M('note');
            $sql=" id in (".$in.") ";
            $noteMod->where($sql)->delete();

            $this->success('删除成功', U('note/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		$sqlOrder = " id DESC";

		$filter_role = $this->REQUEST('_filter_role');
		if( $filter_role != '' ){
			$sqlWhere .= " and role = '". $this->fixSQL($filter_role)."' ";
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
				$sqlWhere .= " and (phone like '%". $this->fixSQL($f_search)."%' or suggest like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%' or suggest like '%". $this->fixSQL($f_search)."%') ";
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
		
		
		
		
		

        $this->ModManager = M('note');
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
		$this->assign('filter_fieldname',  $filter_fieldname);
		$this->assign('filter_starttime',  $filter_starttime);
		$this->assign('filter_endtime',  $filter_endtime);



        $this->assign('filter_role',   $filter_role);
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

		///获取列表数据集
		$rst=$this->GeneralActionForListing('note', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('留言板列表');
		$PageMenu = array(
			//array( U('note/create'), L('添加留言板') ),
            array( U('note/export'), L('导出留言板') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}

    //导出留言板
    public function export()
    {
        $CityMod = M('note');
        $toShow['banner'] = $CityMod->where(" status=1 " )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "留言板ID编号".$expstr."标题"
            //.$expstr."手机"
            .$expstr."内容"
            .$expstr."创建时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
                

                $suggest=$toShow['banner'][$k]['suggest'];

                $suggest=str_replace("\r\n"," [Enter] ",$suggest);
                $suggest=str_replace("\n"," [Enter] ",$suggest);
                $suggest=str_replace("\r","",$suggest);


                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['title']
                    //.$expstr.$toShow['banner'][$k]['phone']
                    .$expstr.$suggest
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


	/**
	 *--------------------------------------------------------------+
	 * Action: create
	 *--------------------------------------------------------------+
	 */
	public function create()
	{

        $CityMod = M('area');
        $area_list = $CityMod->field('a_id,a_name')->where(" a_pid=0 " )->order('a_id asc')->select();
        //echo "<pre>";print_r($area_list);exit;
        $this->assign('area_list', $area_list);



        if(isset($_POST['dosubmit'])){
        //echo "<pre>";print_r($_POST);exit;
	        $noteMod = M('note');
			
			$rst=$this->ChecknoteData_Post();
			
			if (false === $noteMod->create()) {
				$this->error($module->getError());
			}
			
	        if($noteMod->create()) {

	        	//echo "<pre>";print_r($noteMod);exit;

	        	//使用 $noteMod->email
        		$rst=$this->ChecknoteData_Mod($noteMod);
	        	$noteMod->create_time=time();
	        	$noteMod->password=md5($noteMod->password);
	        	
	        	$result =   $noteMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($noteMod->getError());
	        }
			
		}else{

			$PageTitle = L('添加留言板');
			$PageMenu = array(
					array( U('note/listing'), L('留言板列表') ),
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
        $CityMod = M('area');
        $area_list = $CityMod->field('a_id,a_name')->where(" a_pid=0 " )->order('a_id asc')->select();
        //echo "<pre>";print_r($area_list);exit;
        $this->assign('area_list', $area_list);


    	///注意：老留言板留言板已填写情况下不允许修改留言板名，留言板名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            $rst=$this->ChecknoteData_Post($id);

        	///密码为空则不修改密码
        	if( isset($_POST['password'])){
                if($_POST['password'] != '' ){
        		    $_POST['password'] = md5($_POST['password']);
        	    }
        	    else{
                    unset( $_POST['password'] );
        	    }
            }

	        $noteMod = M('note');

	        if($noteMod->create()) {
	        	
                $rst=$this->ChecknoteData_Mod($noteMod,$id);
                $noteMod->modify_time=time();

	            $result =   $noteMod->save();
	            if($result) {
	                $this->success('操作成功！', U('note/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($noteMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $noteMod = M('note');
		    // 读取数据
		    $data =   $noteMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('留言板数据读取错误');
		    }
    
			$PageTitle = L('编辑留言板');
			$PageMenu = array(
					array( U('note/create'), L('添加留言板') ),
					array( U('note/listing'), L('留言板列表') ),
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
		$module = $noteMod = M('note');
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
        $noteMod = M('note');
        $sql=" id in (".$in.") ";
        $noteMod->where($sql)->delete();

        $this->success('删除成功', U('note/listing'));
    }
*/

	private function ChecknoteData_Post($note_id=0){
		///检查 $_POST 提交数据
		
			$noteMod = M('note');

			$result = $noteMod->where("notename='%s' and id!=%d ", $_POST['notename'], $note_id )->count();
			if($result>0){
            $this->error(L('存在重复的留言板名'));
            }
            
			$result = $noteMod->where("email='%s' and id!=%d ", $_POST['email'], $note_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
            
	}
	

	private function ChecknoteData_Mod(&$note, $note_id=0){
		///检查 $note 模型数据。$note->email
		
			$noteMod = M('note');
			
			$result = $noteMod->where("notename='%s' and id!=%d ", $note->notename, $note_id )->count();
			if($result>0){
            $this->error(L('存在重复的留言板名'));
            }
            
			$result = $noteMod->where("email='%s' and id!=%d ", $note->email, $note_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
		
	}


}
?>