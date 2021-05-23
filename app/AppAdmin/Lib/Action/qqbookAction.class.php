<?php
/**
 * 简单QQ数据系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class qqbookAction extends TAction
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
            $qqbookMod = M('qqbook');
            $sql=" id in (".$in.") ";
            $qqbookMod->where($sql)->delete();

            $this->success('删除成功', U('qqbook/listing'));
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
			if($filter_fieldname=='username'){
				$sqlWhere .= " and (username like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='title'){
				$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' ) ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (username like '%". $this->fixSQL($f_search)."%' or title like '%". $this->fixSQL($f_search)."%' )";
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
		
		
		

        $this->ModManager = M('qqbook');
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
		$rst=$this->GeneralActionForListing('qqbook', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('QQ数据列表');
		$PageMenu = array(
			array( U('qqbook/create'), L('添加QQ数据') ),
            array( U('qqbook/export'), L('导出QQ数据') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}

    //导出QQ数据
    public function export()
    {
        $CityMod = M('qqbook');
        $toShow['banner'] = $CityMod->where(" status=1 " )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "数据库ID编号".$expstr."QQ号"
            .$expstr."姓名".$expstr."备份时间"
            .$expstr."创建时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
            	/*
                if ($toShow['banner'][$k]['contact_sex']=="1"){
                    $sex_show="男";
                }
                if ($toShow['banner'][$k]['contact_sex']=="0"){
                    $sex_show="女";
                }

				
				if(isset($toShow['banner'][$k]['pic_show']) && !empty($toShow['banner'][$k]['pic_show']) ){
	        		$pic_show='http://'.$_SERVER["HTTP_HOST"].$toShow['banner'][$k]['pic_show'];
				}
				else{
					$pic_show='';
				}
				*/
				
	        	
	        	if(!empty($toShow['banner'][$k]['username'])){
	        		$username="".$toShow['banner'][$k]['username'];
	        	}
	        	
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$username
                    .$expstr.$toShow['banner'][$k]['title']
                    .$expstr.$toShow['banner'][$k]['backup_time']
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

        $CityMod = M('hangye');
        $hangye_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc')->select();
        //echo "<pre>";print_r($hangye_list);exit;
        $this->assign('hangye_list', $hangye_list);



        if(isset($_POST['dosubmit'])){
        //echo "<pre>";print_r($_POST);exit;
	        $qqbookMod = M('qqbook');
			
			$rst=$this->CheckqqbookData_Post();
			
			if (false === $qqbookMod->create()) {
				$this->error($module->getError());
			}
			
	        if($qqbookMod->create()) {

	        	//echo "<pre>";print_r($qqbookMod);exit;

	        	//使用 $qqbookMod->email
        		$rst=$this->CheckqqbookData_Mod($qqbookMod);
	        	$qqbookMod->create_time=time();
	        	$qqbookMod->password=md5($qqbookMod->password);
	        	
	        	$result =   $qqbookMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($qqbookMod->getError());
	        }
			
		}else{

			$PageTitle = L('添加QQ数据');
			$PageMenu = array(
					array( U('qqbook/listing'), L('QQ数据列表') ),
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
        $CityMod = M('hangye');
        $hangye_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc')->select();
        //echo "<pre>";print_r($hangye_list);exit;
        $this->assign('hangye_list', $hangye_list);


    	///注意：老QQ数据QQ数据已填写情况下不允许修改卡号，卡号空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            $rst=$this->CheckqqbookData_Post($id);

        	///密码为空则不修改密码
        	if( isset($_POST['password'])){
                if($_POST['password'] != '' ){
        		    $_POST['password'] = md5($_POST['password']);
        	    }
        	    else{
                    unset( $_POST['password'] );
        	    }
            }

	        $qqbookMod = M('qqbook');

	        if($qqbookMod->create()) {
	        	
                $rst=$this->CheckqqbookData_Mod($qqbookMod,$id);
                $qqbookMod->modify_time=time();

	            $result =   $qqbookMod->save();
	            if($result) {
	                $this->success('操作成功！', U('qqbook/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($qqbookMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $qqbookMod = M('qqbook');
		    // 读取数据
		    $data =   $qqbookMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('QQ数据数据读取错误');
		    }
    
			$PageTitle = L('编辑QQ数据');
			$PageMenu = array(
					array( U('qqbook/create'), L('添加QQ数据') ),
					array( U('qqbook/listing'), L('QQ数据列表') ),
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
		$module = $qqbookMod = M('qqbook');
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
        $qqbookMod = M('qqbook');
        $sql=" id in (".$in.") ";
        $qqbookMod->where($sql)->delete();

        $this->success('删除成功', U('qqbook/listing'));
    }
*/

	private function CheckqqbookData_Post($qqbook_id=0){
		///检查 $_POST 提交数据
		
			$qqbookMod = M('qqbook');

			$result = $qqbookMod->where("username='%s' and id!=%d ", $_POST['username'], $qqbook_id )->count();
			if($result>0){
            $this->error(L('存在重复的QQ号'));
            }
            
            
	}
	

	private function CheckqqbookData_Mod(&$qqbook, $qqbook_id=0){
		///检查 $qqbook 模型数据。$qqbook->email
		
			$qqbookMod = M('qqbook');
			
			$result = $qqbookMod->where("username='%s' and id!=%d ", $qqbook->username, $qqbook_id )->count();
			if($result>0){
            $this->error(L('存在重复的QQ号'));
            }
            
		
	}
	
	
	
	public function listing_detail()
	{

        
        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");
        $f_search = $this->REQUEST('_f_search');
        
        
        if(!empty($_POST) && !empty($f_search) ){
        

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
				if($filter_fieldname=='username'){
					$sqlWhere .= " and (username like '%". $this->fixSQL($f_search)."%') ";
				}
				elseif($filter_fieldname=='qqbook_name'){
					$sqlWhere .= " and (qqbook_name like '%". $this->fixSQL($f_search)."%' ) ";
				}
				elseif($filter_fieldname=='contact_name'){
					$sqlWhere .= " and (contact_name like '%". $this->fixSQL($f_search)."%' ) ";
				}
				elseif($filter_fieldname==''){
					$sqlWhere .= " and (username like '%". $this->fixSQL($f_search)."%' or qqbook_name like '%". $this->fixSQL($f_search)."%' or contact_name like '%". $this->fixSQL($f_search)."%')";
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
			
			
			

	        $this->ModManager = M('qqbook');
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
			$rst=$this->GeneralActionForListing('qqbook', $sqlWhere, $sqlOrder, '1', 'M');
	        //echo "<pre>";print_r($rst);exit;
		}
		
		
		$PageTitle = L('单位卡号查详情');
		$PageMenu = array(
			//array( U('qqbook/create'), L('添加QQ数据') ),
            //array( U('qqbook/export'), L('导出QQ数据') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	//导入
	public function listing_import()
	{

        
        if(isset($_FILES['importfile']['name']) && $_FILES['importfile']['name']!=''  ){
			
			header("Content-type:text/html;charset=utf-8");
			
			$ftype=strtolower(substr(strrchr($_FILES['importfile']['name'],'.'),1));
			
			if(!($ftype=='xls' || $ftype=='xlsx')){
	         	$this->error(L('请上传xls或xlsx格式'));
	        }
	        
			$filename = $this->checkFileName(BASE_UPLOAD_PATH, $_FILES['importfile']['name']);
			$file11   =   basename($filename);
			$aa=explode(".",$file11);
			$aa_num=count($aa)-1;
			$fname="excel_".time()."_".rand(10,99);
			$filename=$fname.".".$aa[$aa_num];
			$this->uploadImg(BASE_UPLOAD_PATH, $_FILES['importfile']['tmp_name'], $filename);

			//最终文件上传位置
			$uploadfile=BASE_UPLOAD_PATH.$filename;
			//echo $uploadfile;exit;
			
			require_once APP_PATH .'Lib/phpexcel/Classes/phpexcel.php'; 
			require_once APP_PATH .'Lib/phpexcel/Classes/PHPExcel/IOFactory.php';
			
			
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format 
			$objPHPExcel = $objReader->load($uploadfile); 


			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); // 取得总行数 
			$highestColumn = $sheet->getHighestColumn(); // 取得总列数
  			$arr_result=array();
  			$strs=array();
			
			$qqbookMod = M('qqbook');
			$table_name=$qqbookMod->getTableName();
			
			
			
			if($highestRow>=2){
				
				//先删除旧数据
				//$sql=" 1 ";
            	//$qqbookMod->where($sql)->delete();
				
				//$j=2 代表从第2行开始获取数据
				for($j=2;$j<=$highestRow;$j++){ 
					unset($arr_result);
				    unset($strs);
				 	for($k='A';$k<= $highestColumn;$k++){ 
				     	//读取单元格
				  		//$arr_result  .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue().',';   //如果含公式，则拿到的是公式
				  		$arr_result  .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getCalculatedValue().',';    //如果含公式，则拿到的是公式计算后的最终结果
				    }
					$strs=explode(",",$arr_result);
					//echo $strs[9];exit;
					
		            $result_isset = $qqbookMod->where(" username='".addslashes($strs[0]) ."' " )->select();
		            if(isset($result_isset[0])){
		            	//更新
		            	$sql="update ".$table_name." SET 
			             title='".addslashes($strs[1])."'
			            , backup_time='".addslashes($strs[2])."'
			            where username='".addslashes($strs[0]) ."' 
			             ";
			            $result_edit = $qqbookMod->execute($sql);
		            }
		            else{
		            	//新增
		            	$sql="insert into ".$table_name." SET 
		            	  username='".addslashes($strs[0])."'
			            , title='".addslashes($strs[1])."'
			            , backup_time='".addslashes($strs[2])."'
			            , create_time='".time()."'
			             ";
			            $result_add = $qqbookMod->execute($sql);
		            }
				}
			}
			
			/*
			//导入历史
			$qqbookhistoryMod = M('qqbook_import_history');
			$table_name=$qqbookhistoryMod->getTableName();
			
			$number=$highestRow-2;
        	$sql="insert into ".$table_name." SET number='".addslashes($number)."'
            , username='".addslashes($this->CurrAccount['Account-Name'])."'
            , pic_show='".addslashes('/public/excel/'.$filename)."'
            , create_time='".time()."'
             ";
            $result_add = $qqbookhistoryMod->execute($sql);
            */
            
			
            $this->success('导入成功！', U('qqbook/listing_import'));
            exit;
        }
        
        
        


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		$PageTitle = L('QQ数据导入');
		$PageMenu = array(
			//array( U('qqbook/create'), L('添加QQ数据') ),
            //array( U('qqbook/export'), L('导出QQ数据') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	

}
?>