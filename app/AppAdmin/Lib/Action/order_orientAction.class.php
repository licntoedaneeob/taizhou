<?php
/**
 * 简单报名定向赛系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class order_orientAction extends TAction
{
	
	

	/**
	 *--------------------------------------------------------------+
	 * Action: list 报名定向赛列表
	 *--------------------------------------------------------------+
	 */
	public function listing()
	{
		
		//echo  "<pre>";print_r($_POST);exit;
		
		
		
		
		//摆渡车点
		//$this->checkorder_orientTotalAmout();
		$bus_point_list=$this->get_bus_point();
        $this->assign('bus_point_list', $bus_point_list );
        //echo  "<pre>";print_r($bus_point_list);exit;
		
		
		//国家地区
		$cityarea_list=$this->get_cityarea();
        $this->assign('cityarea_list', $cityarea_list );
        //echo  "<pre>";print_r($cityarea_list);exit;
		

        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='删除'){
        	
        	//如为了确保数据安全，则禁用删除功能 
        	//exit;
        	
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
            $order_orientMod = M('order_orient');
            $sql=" id in (".$in.") ";
            $order_orientMod->where($sql)->delete();

            $this->success('删除成功', U('order_orient/listing'));
            exit;
            
        }
        
        
        /*
        //批量设为普通等级
        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='批量设为普通等级'){
            if( empty($_POST['id']) ){
                $this->error(L('请选择要批量设为普通等级的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }

            
            $sql_where=" id in (".$in.") ";
            
			
			$order_orientMod = M('order_orient');
		    $sql=sprintf("update %s SET level='0' 
		    where ".$sql_where." 
		    ", $order_orientMod->getTableName() );
		    //echo $sql;exit;
		    $result = $order_orientMod->execute($sql);
		    
            $this->success('批量设为普通等级成功', U('order_orient/listing'));
            exit;
        }
        
        
        
        //批量设为VIP等级
        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='批量设为VIP等级'){
            if( empty($_POST['id']) ){
                $this->error(L('请选择要批量设为VIP等级的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }

            
            $sql_where=" id in (".$in.") ";
            
			
			$order_orientMod = M('order_orient');
		    $sql=sprintf("update %s SET level='1' 
		    where ".$sql_where." 
		    ", $order_orientMod->getTableName() );
		    //echo $sql;exit;
		    $result = $order_orientMod->execute($sql);
		    
            $this->success('批量设为VIP等级成功', U('order_orient/listing'));
            exit;
        }
        
        
        
        
        
        
        
        //批量分配卡券
        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='批量分配卡券'){
            if( empty($_POST['id']) ){
                $this->error(L('请选择要批量分配卡券的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }
			
			
			
			//获得卡券信息
			$allclassQuan=$this->getAllClassQuan();
        	$this->assign('allclassQuan', $allclassQuan );
			$quan_info=$allclassQuan[$_POST['quan_id']];
			//echo "<pre>";print_r($quan_info);exit;
			
			
			
			//需要分配卡券的报名定向赛列表
			$order_orient_list=$ids;
			//echo "<pre>";print_r($order_orient_list);exit;
			$cur_time=time();
			
			//echo "<pre>";print_r($_POST);exit;
			
			if(!empty($order_orient_list)){
				
				$order_orient_quan_historyMod = M('order_orient_quan_history');
				
				$number=intval($this->REQUEST('quan_number'), 0);
				
	            if($number>=1 && is_numeric($number)){
		            $x=0;
		            do{
		            	foreach($order_orient_list as $k_order_orient=>$v_order_orient){
		            		
		            		
					        //兑换码
					        $title=$this->shortCode(uniqid());
					        
					        $order_orient_quan_historyMod->create_time=$cur_time;
					        $order_orient_quan_historyMod->modify_time=$cur_time;
					        $order_orient_quan_historyMod->addtime=date("Y-m-d H:i:s",$cur_time);
					        $order_orient_quan_historyMod->order_id=$v_order_orient;
					        $order_orient_quan_historyMod->class_id=$quan_info['id'];
					        $order_orient_quan_historyMod->title=$title;
					        $order_orient_quan_historyMod->is_used=1;
					        //echo "<pre>";print_r($order_orient_quan_historyMod);exit;
					        $order_orient_quan_history_id = $order_orient_quan_historyMod->add();
					        //var_dump($order_orient_quan_history_id);exit;
					        
		            	}
		            	$x=$x+1;
		            }while($x<$number);
	            }
	            
            }
            
		    
            $this->success('批量分配卡券成功', U('order_orient/listing'));
            exit;
        }
        */
		
		
		/*
        $quanMod = M('quan_list');
        $quan_list = $quanMod->where(" status=1 " )->order('sort asc,id desc')->select();
        $quan_arr=array();
        if(!empty($quan_list)){
        	foreach($quan_list as $k=>$v){
        		$quan_arr[$v['id']]=$v;
        	}
        }
        //echo "<pre>";print_r($quan_arr);exit;
        $this->assign('quan_list', $quan_list);
        $this->assign('quan_arr', $quan_arr);
        
        
        $quan_historyMod = M('order_orient_quan_history');
        $quan_history_list = $quan_historyMod->where(" status=1 " )->select();
        //echo "<pre>";print_r($quan_history_list);exit;
        $quan_history_arr=array();
        if(!empty($quan_history_list)){
        	foreach($quan_history_list as $k=>$v){
        		$quan_summary=$quan_arr[$v['class_id']]['title'].' '.$quan_arr[$v['class_id']]['start_time'].' ~ '.$quan_arr[$v['class_id']]['end_time'].' '.$v['title'];
        		if(isset($quan_history_arr[$v['order_orient_id']]['quan_summary'])){
        			$quan_history_arr[$v['order_orient_id']]['quan_summary']=$quan_history_arr[$v['order_orient_id']]['quan_summary']."<br>".$quan_summary;
        		}
        		else{
        			$quan_history_arr[$v['order_orient_id']]['quan_summary']=$quan_summary;
        		}
        	}
        }
        //echo "<pre>";print_r($quan_history_arr);exit;
        $this->assign('quan_history_list', $quan_history_list);
        $this->assign('quan_history_arr', $quan_history_arr);
        */
        
        
        
        
        
        
        
        
        

        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status=1 ";
		$sqlWhere .= " and confirm_apply=1 ";
		$sqlorder_orient = "id DESC";
		
		$filter_role = $this->REQUEST('_filter_role');
		if( $filter_role != '' ){
			$sqlWhere .= " and role = '". $this->fixSQL($filter_role)."' ";
		}

		$filter_state = $this->REQUEST('_filter_state');
		if( $filter_state != '' ){
			$sqlWhere .= " and status = ". intval($filter_state)." ";
		}

        
		//echo "<pre>";print_r($_POST);exit;
		
        
        //关键字
        $f_search = $this->REQUEST('_f_search');
        $filter_fieldname = $this->REQUEST('_filter_fieldname');
		if( $f_search != '' ){
			if($filter_fieldname=='order_no'){
				$sqlWhere .= " and (order_no like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='trade_no'){
				$sqlWhere .= " and (trade_no like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='mobile'){
				$sqlWhere .= " and (mobile like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='realname'){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='id_number'){
				$sqlWhere .= " and (id_number like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='match_code'){
				$sqlWhere .= " and (match_code like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='invit_code'){
				$sqlWhere .= " and (invit_code like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (order_no like '%". $this->fixSQL($f_search)."%' or trade_no like '%". $this->fixSQL($f_search)."%' or mobile like '%". $this->fixSQL($f_search)."%' or realname like '%". $this->fixSQL($f_search)."%' or id_number like '%". $this->fixSQL($f_search)."%' or match_code like '%". $this->fixSQL($f_search)."%' or invit_code like '%". $this->fixSQL($f_search)."%' )";
			}
			else{
			}
		}
		
		
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sql_starttime=date('Y-m-d H:i:s',$sql_starttime);
			$sqlWhere .= " and addtime_apply >= '". $this->fixSQL($sql_starttime)."' ";
		}
		if( $filter_endtime != '' ){
			//$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sql_endtime=strtotime($filter_endtime);
			$sql_endtime=date('Y-m-d H:i:s',$sql_endtime);
			$sqlWhere .= " and addtime_apply <= '". $this->fixSQL($sql_endtime)."' ";
		}
		//echo $sqlWhere;exit;
		
		
		
		
		$filter_starttime_attach = $this->REQUEST('_filter_starttime_attach');
		$filter_endtime_attach = $this->REQUEST('_filter_endtime_attach');
		if( $filter_starttime_attach != '' ){
			$sql_starttime_attach=strtotime($filter_starttime_attach);
			$sql_starttime_attach=date('Y-m-d H:i:s',$sql_starttime_attach);
			$sqlWhere .= " and addtime_attach >= '". $this->fixSQL($sql_starttime_attach)."' ";
		}
		if( $filter_endtime_attach != '' ){
			//$sql_endtime_attach=strtotime($filter_endtime_attach)+(24*3600);
			$sql_endtime_attach=strtotime($filter_endtime_attach);
			$sql_endtime_attach=date('Y-m-d H:i:s',$sql_endtime_attach);
			$sqlWhere .= " and addtime_attach <= '". $this->fixSQL($sql_endtime_attach)."' ";
		}
		//echo $sqlWhere;exit;
		
		
		
		
		$filter_starttime_pay = $this->REQUEST('_filter_starttime_pay');
		$filter_endtime_pay = $this->REQUEST('_filter_endtime_pay');
		if( $filter_starttime_pay != '' ){
			$sql_starttime_pay=strtotime($filter_starttime_pay);
			$sql_starttime_pay=date('Y-m-d H:i:s',$sql_starttime_pay);
			$sqlWhere .= " and payDateTime >= '". $this->fixSQL($sql_starttime_pay)."' ";
		}
		if( $filter_endtime_pay != '' ){
			//$sql_endtime_pay=strtotime($filter_endtime_pay)+(24*3600);
			$sql_endtime_pay=strtotime($filter_endtime_pay);
			$sql_endtime_pay=date('Y-m-d H:i:s',$sql_endtime_pay);
			$sqlWhere .= " and payDateTime <= '". $this->fixSQL($sql_endtime_pay)."' ";
		}
		//echo $sqlWhere;exit;
		
		
		
		
		/*
		$filter_starttime_birthday = $this->REQUEST('_filter_starttime_birthday');
		$filter_endtime_birthday = $this->REQUEST('_filter_endtime_birthday');
		if( $filter_starttime_birthday != '' ){
			$sql_starttime_birthday=addslashes($filter_starttime_birthday);
			$sqlWhere .= " and birthday >= '". $sql_starttime_birthday."' ";
		}
		if( $filter_endtime_birthday != '' ){
			$sql_endtime_birthday=addslashes($filter_endtime_birthday);
			$sqlWhere .= " and birthday <= '". $sql_endtime_birthday."' ";
		}
		//echo $sqlWhere;exit;
		
		
		
		$filter_level = $this->REQUEST('_filter_level');
		if( $filter_level==='' ){
		}
		else{
			$sqlWhere .= " and level = '". $filter_level."' ";
		}
		
		
		
		
		$filter_birth_month = $this->REQUEST('_filter_birth_month');
		if( $filter_birth_month==='' ){
		}
		else{
			$sqlWhere .= " and birthday like '%-". $filter_birth_month."-%' ";
		}
		*/
		
		
		
		$filter_cat_id = $this->REQUEST('_filter_cat_id');
		if( $filter_cat_id==='' ){
		}
		else{
			$sqlWhere .= " and cat_id = '". $this->fixSQL($filter_cat_id)."' ";
		}
		
		
		
		
		$filter_payMode = $this->REQUEST('_filter_payMode');
		if( $filter_payMode==='' ){
		}
		else{
			$sqlWhere .= " and payMode = '". $this->fixSQL($filter_payMode)."' ";
		}
		
		
		
		
		$filter_isPay = $this->REQUEST('_filter_isPay');
		if( $filter_isPay==='' ){
		}
		else{
			$sqlWhere .= " and isPay = '". $this->fixSQL($filter_isPay)."' ";
		}
		
		
		
		
		$filter_status_apply = $this->REQUEST('_filter_status_apply');
		if( $filter_status_apply==='' ){
		}
		else{
			$sqlWhere .= " and status_apply = '". $this->fixSQL($filter_status_apply)."' ";
		}
		
		
		
		$filter_status_attach = $this->REQUEST('_filter_status_attach');
		if( $filter_status_attach==='' ){
		}
		else{
			$sqlWhere .= " and status_attach = '". $this->fixSQL($filter_status_attach)."' ";
		}
		
		
		
		
		
		$filter_bus_point = $this->REQUEST('_filter_bus_point');
		if( $filter_bus_point==='' ){
		}
		else{
			$sqlWhere .= " and bus_point = '". $this->fixSQL($filter_bus_point)."' ";
		}
		
		
		
		
		$filter_renshou_zengxian = $this->REQUEST('_filter_renshou_zengxian');
		if( $filter_renshou_zengxian==='' ){
		}
		else{
			$sqlWhere .= " and renshou_zengxian = '". $this->fixSQL($filter_renshou_zengxian)."' ";
		}
		
		
		
		
		$filter_is_invit = $this->REQUEST('_filter_is_invit');
		if( $filter_is_invit==='' ){
		}
		elseif( $filter_is_invit=='1' ){
			$sqlWhere .= " and (invit_code!='' and is_free=1 ) ";
		}
		elseif( $filter_is_invit=='2' ){
			$sqlWhere .= " and (invit_code!='' and is_free=2 ) ";
		}
		elseif( $filter_is_invit=='3' ){
			$sqlWhere .= " and invit_code=''  ";
		}
		else{
		}
		
		
		
		$filter_is_cert_medical = $this->REQUEST('_filter_is_cert_medical');
		if( $filter_is_cert_medical==='' ){
		}
		elseif( $filter_is_cert_medical=='1' ){
			$sqlWhere .= " and cert_medical!='' ";
		}
		elseif( $filter_is_cert_medical=='2' ){
			$sqlWhere .= " and cert_medical='' ";
		}
		else{
		}
		
		
		
		$filter_cert_chengji_score = $this->REQUEST('_filter_cert_chengji_score');
		if( $filter_cert_chengji_score==='' ){
		}
		else{
			//$sqlWhere .= " and (cert_chengji!='' or best_chengji_score!='') ";
			$sqlWhere .= " and (cert_chengji!='' ) ";
		}
		
		
		
		
		$filter_cityarea = $this->REQUEST('_filter_cityarea');
		if( $filter_cityarea==='' ){
		}
		else{
			$sqlWhere .= " and cityarea = '". $this->fixSQL($filter_cityarea)."' ";
		}
		
		
		

        $this->ModManager = M('order_orient');
        $f_order_orient = $this->REQUEST('_f_order_orient', 'id');
        $fields = $this->ModManager->getDbFields();
        if( in_array($f_order_orient, $fields) ){
            $sqlorder_orient = $f_order_orient . ' ';
        }else{
            $sqlorder_orient = 'id ';
        }
        $f_direc = strtoupper($this->REQUEST('_f_direc'));if($f_direc==""){$f_direc='DESC';}
        if( $f_direc != 'DESC' ){
            $sqlorder_orient .= 'ASC';
        }else{
            $sqlorder_orient .= 'DESC';
        }
        
        
        //强制排序
        $sqlorder_orient = ' id DESC ';
        //echo $sqlorder_orient;exit;

		///回传过滤条件
		$this->assign('filter_fieldname',  $filter_fieldname);
		$this->assign('filter_starttime',  $filter_starttime);
		$this->assign('filter_endtime',  $filter_endtime);
		$this->assign('filter_starttime_attach',  $filter_starttime_attach);
		$this->assign('filter_endtime_attach',  $filter_endtime_attach);
		$this->assign('filter_starttime_pay',  $filter_starttime_pay);
		$this->assign('filter_endtime_pay',  $filter_endtime_pay);
		
		$this->assign('filter_cat_id',  $filter_cat_id);
		$this->assign('filter_payMode',  $filter_payMode);
		$this->assign('filter_isPay',  $filter_isPay);
		$this->assign('filter_status_apply',  $filter_status_apply);
		$this->assign('filter_status_attach',  $filter_status_attach);
		$this->assign('filter_bus_point',  $filter_bus_point);
		$this->assign('filter_cityarea',  $filter_cityarea);
		$this->assign('filter_renshou_zengxian',  $filter_renshou_zengxian);
		$this->assign('filter_is_invit',  $filter_is_invit);
		$this->assign('filter_is_cert_medical',  $filter_is_cert_medical);
		$this->assign('filter_cert_chengji_score',  $filter_cert_chengji_score);
		
		//$this->assign('filter_starttime_birthday',  $filter_starttime_birthday);
		//$this->assign('filter_endtime_birthday',  $filter_endtime_birthday);
		//$this->assign('filter_level',  $filter_level);
		//$this->assign('filter_birth_month',  $filter_birth_month);



        $this->assign('filter_role',   $filter_role);
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order_orient',   $f_order_orient);
        $this->assign('f_direc',   $f_direc );
		
		
		//echo $sqlWhere;exit;
		/*
		if($sqlWhere == "status < 250"){
			$page_size='';
		}
		else{
			$page_size='100000000';
		}
		*/
		$page_size='20';
		
		
		
		
		if(isset($_POST['is_export']) && $_POST['is_export']==1){
			//导出
			
			ini_set('memory_limit', '2048M');
			set_time_limit(3600);

			if($_SERVER["HTTP_HOST"]=='cdmalasong.loc'){
			$myTime=microtime(true);
			$this->set_log_sql("---start export---");		
			$this->set_log_sql("memory:".number_format(memory_get_peak_usage()/1024/1024, 2)."M, time:". number_format(microtime(true)-$myTime,4).'s');
			}
			
			$rst=$this->GeneralActionForListing('order_orient', $sqlWhere, $sqlorder_orient, '100000000', 'M');
			
			$toShow['banner'] = $rst['dataset'];
			
			//echo "<pre>";print_r($rst['dataset']);exit;
			
			
			require_once APP_PATH .'Lib/phpexcel/Classes/PHPExcel.php'; 
			
			// Create new PHPExcel object
        	$objPHPExcel = new PHPExcel();
			
	        // Set properties
	        $objPHPExcel->getProperties()->setCreator("JHM")
            ->setLastModifiedBy("JHM")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Excel5 file");
			
			
			
	        $i = 1;
	        // Add some data
	        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '流水ID编号')
            ->setCellValue('B'.$i, '报名申请时间')
            
            ->setCellValue('C'.$i, '队长姓名')
            ->setCellValue('D'.$i, '性别(1代表男，2代表女)')
            ->setCellValue('E'.$i, '生日')
            ->setCellValue('F'.$i, '证件类型(1身份证，2军官证，3护照，4台胞证，5回乡证)')
            ->setCellValue('G'.$i, '证件号码')
            ->setCellValue('H'.$i, '通讯地址')
            ->setCellValue('I'.$i, '手机')
            ->setCellValue('J'.$i, '国家/地区')
            ->setCellValue('K'.$i, '血型')
            ->setCellValue('L'.$i, '衣服尺码')
            ->setCellValue('M'.$i, '紧急联系人')
            ->setCellValue('N'.$i, '紧急联系人电话')
            
            ->setCellValue('O'.$i, '赛队名称')
            ->setCellValue('P'.$i, '参赛号')
            
            ->setCellValue('Q'.$i, '组员1姓名')
            ->setCellValue('R'.$i, '性别(1代表男，2代表女)')
            ->setCellValue('S'.$i, '生日')
            ->setCellValue('T'.$i, '证件类型(1身份证，2军官证，3护照，4台胞证，5回乡证)')
            ->setCellValue('U'.$i, '证件号码')
            ->setCellValue('V'.$i, '通讯地址')
            ->setCellValue('W'.$i, '手机')
            ->setCellValue('X'.$i, '国家/地区')
            ->setCellValue('Y'.$i, '血型')
            ->setCellValue('Z'.$i, '衣服尺码')
            ->setCellValue('AA'.$i, '紧急联系人')
            ->setCellValue('AB'.$i, '紧急联系人电话')
            
            ->setCellValue('AC'.$i, '组员2姓名')
            ->setCellValue('AD'.$i, '性别(1代表男，2代表女)')
            ->setCellValue('AE'.$i, '生日')
            ->setCellValue('AF'.$i, '证件类型(1身份证，2军官证，3护照，4台胞证，5回乡证)')
            ->setCellValue('AG'.$i, '证件号码')
            ->setCellValue('AH'.$i, '通讯地址')
            ->setCellValue('AI'.$i, '手机')
            ->setCellValue('AJ'.$i, '国家/地区')
            ->setCellValue('AK'.$i, '血型')
            ->setCellValue('AL'.$i, '衣服尺码')
            ->setCellValue('AM'.$i, '紧急联系人')
            ->setCellValue('AN'.$i, '紧急联系人电话')
            
            ->setCellValue('AO'.$i, '组员3姓名')
            ->setCellValue('AP'.$i, '性别(1代表男，2代表女)')
            ->setCellValue('AQ'.$i, '生日')
            ->setCellValue('AR'.$i, '证件类型(1身份证，2军官证，3护照，4台胞证，5回乡证)')
            ->setCellValue('AS'.$i, '证件号码')
            ->setCellValue('AT'.$i, '通讯地址')
            ->setCellValue('AU'.$i, '手机')
            ->setCellValue('AV'.$i, '国家/地区')
            ->setCellValue('AW'.$i, '血型')
            ->setCellValue('AX'.$i, '衣服尺码')
            ->setCellValue('AY'.$i, '紧急联系人')
            ->setCellValue('AZ'.$i, '紧急联系人电话')
            
            
            //->setCellValue('Q'.$i, '是否需要人寿赠险(1是，2否)')
            //->setCellValue('R'.$i, '邀请码')
            //->setCellValue('S'.$i, '邀请码是否免费(1免费，2收费)')
            //->setCellValue('U'.$i, '体检证明')
            //->setCellValue('V'.$i, '完赛证书成绩')
            //->setCellValue('W'.$i, '成绩证明')
            //->setCellValue('X'.$i, '摆渡点')
            //->setCellValue('Y'.$i, '详细资料时间')
            //->setCellValue('Z'.$i, '订单号')
            //->setCellValue('AA'.$i, '交易号')
            //->setCellValue('AB'.$i, '总金额')
            //->setCellValue('AC'.$i, '支付方式(0代表未定义，1代表支付宝，2代表微信，9代表线下支付)')
            //->setCellValue('AD'.$i, '支付状态(0代表未支付，1代表已支付，2代表待确认)')
            //->setCellValue('AE'.$i, '支付时间')
            //->setCellValue('AG'.$i, '抽签状态(0待抽签，1已中签，2未中签)')
            //->setCellValue('AH'.$i, '审核状态(0待审核，1审核通过，2审核拒绝)')
            //->setCellValue('AI'.$i, '审核备注')
            //->setCellValue('J'.$i, '邮箱地址')
            //->setCellValue('B'.$i, '报名类型(1代表全程马拉松，2代表半程马拉松，3代表迷你马拉松，4代表全程公益，5代表半程公益)')
            //->setCellValue('W'.$i, '紧急联系人关系')
            //->setCellValue('Y'.$i, '紧急联系人地址')
            //->setCellValue('O'.$i, '最好成绩项目')
            //->setCellValue('T'.$i, '是否医护跑者(1是，2否) ')
            //->setCellValue('AG'.$i, '报名渠道')
            //->setCellValue('AJ'.$i, '送货区')
            //->setCellValue('AK'.$i, '送货地址')
            //->setCellValue('AL'.$i, '送货邮箱')
            ;
            
			
  			foreach ($toShow['banner'] as $k=>$row){
				
				if($_SERVER["HTTP_HOST"]=='cdmalasong.loc'){
				if($i%100==0){
					$this->set_log_sql("{$i}-->memory:".number_format(memory_get_peak_usage()/1024/1024, 2)."M, time:". number_format(microtime(true)-$myTime,4).'s');
				}
				}
                
                $i ++;
  				
  				//echo "<pre>";print_r($row);exit;
  				
  				
				/*
                $order_orient_id=$toShow['banner'][$k]['id'];
                
                $order_no='';
                if($toShow['banner'][$k]['order_no']!=''){
                	$order_no="'".$toShow['banner'][$k]['order_no'];
                }
                
                $trade_no='';
                if($toShow['banner'][$k]['trade_no']!=''){
                	$trade_no="'".$toShow['banner'][$k]['trade_no'];
                }
                
                
                $m_id_number='';
                if($toShow['banner'][$k]['m_id_number']!=''){
                	$m_id_number="'".$toShow['banner'][$k]['m_id_number'];
                }
                
                
                $m_birth_day='';
                if($toShow['banner'][$k]['m_birth_day']!='0000-00-00'){
                	$m_birth_day=$toShow['banner'][$k]['m_birth_day'];
                }
                
                
                $m_sex=$toShow['banner'][$k]['m_sex'];
                if($toShow['banner'][$k]['user_type']==2){
                	$m_sex='';
                }
                if($m_sex==1){
                	$m_sex='男';
                }
                if($m_sex==2){
                	$m_sex='女';
                }
                
                
                $m_info='';
                if($toShow['banner'][$k]['user_type']==2){
                	$m_info=implode("|", $order_orient_team_arr[$order_orient_id]['info']);
                }
                
                
                $p_info='';
                if(!empty($order_orient_product_arr[$order_orient_id]['info'])){
                	$p_info=implode("|", $order_orient_product_arr[$order_orient_id]['info']);
                }
                
                
                
                $p_sex=$toShow['banner'][$k]['p_sex'];
                if($toShow['banner'][$k]['p_realname']==''){
                	$p_sex='';
                }
                if($p_sex==1){
                	$p_sex='男';
                }
                if($p_sex==2){
                	$p_sex='女';
                }
                
                
                
                
                if($toShow['banner'][$k]['user_type']==2){
                	$toShow['banner'][$k]['m_id_type']='';
                }
                */
                
                /*
                if(!empty($row['cert_medical'])){
	        		//$cert_medical='http://'.$_SERVER["HTTP_HOST"].'/public/cert_medical/'.$row['cert_medical'];
	        		$cert_medical='http://'.$_SERVER["HTTP_HOST"].$row['cert_medical'];
				}
				else{
					$cert_medical='';
				}
				
  				
  				if(!empty($row['cert_chengji'])){
	        		//$cert_chengji='http://'.$_SERVER["HTTP_HOST"].'/public/cert_chengji/'.$row['cert_chengji'];
	        		$cert_chengji='http://'.$_SERVER["HTTP_HOST"].$row['cert_chengji'];
				}
				else{
					$cert_chengji='';
				}
				
				
  			
  		      $status_attach_reason=$row['status_attach_reason'];
  			$status_attach_reason=str_replace("\r\n"," [Enter] ",$status_attach_reason);
                $status_attach_reason=str_replace("\n"," [Enter] ",$status_attach_reason);
                $status_attach_reason=str_replace("\r","",$status_attach_reason);
                */
                
  				
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $row['id'])
                    ->setCellValueExplicit('B'.$i, $row['addtime_apply'], PHPExcel_Cell_DataType::TYPE_STRING)
                    
                    ->setCellValueExplicit('C'.$i, $row['realname'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D'.$i, $row['sex'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('E'.$i, $row['birth_day'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F'.$i, $row['id_type'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G'.$i, $row['id_number'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('H'.$i, $row['address'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('I'.$i, $row['mobile'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('J'.$i, $row['cityarea'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('K'.$i, $row['blood'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('L'.$i, $row['cloth_size'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('M'.$i, $row['ec_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('N'.$i, $row['ec_phone'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	->setCellValueExplicit('O'.$i, $row['running_group_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('P'.$i, $row['match_code'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	 ->setCellValueExplicit('Q'.$i, $row['realname_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('R'.$i, $row['sex_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('S'.$i, $row['birth_day_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('T'.$i, $row['id_type_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('U'.$i, $row['id_number_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('V'.$i, $row['address_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('W'.$i, $row['mobile_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('X'.$i, $row['cityarea_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('Y'.$i, $row['blood_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('Z'.$i, $row['cloth_size_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AA'.$i, $row['ec_name_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AB'.$i, $row['ec_phone_1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	
                	 ->setCellValueExplicit('AC'.$i, $row['realname_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('AD'.$i, $row['sex_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AE'.$i, $row['birth_day_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('AF'.$i, $row['id_type_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('AG'.$i, $row['id_number_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AH'.$i, $row['address_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AI'.$i, $row['mobile_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AJ'.$i, $row['cityarea_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AK'.$i, $row['blood_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AL'.$i, $row['cloth_size_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AM'.$i, $row['ec_name_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AN'.$i, $row['ec_phone_2'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	
                	 ->setCellValueExplicit('AO'.$i, $row['realname_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('AP'.$i, $row['sex_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AQ'.$i, $row['birth_day_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('AR'.$i, $row['id_type_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('AS'.$i, $row['id_number_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AT'.$i, $row['address_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AU'.$i, $row['mobile_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AV'.$i, $row['cityarea_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AW'.$i, $row['blood_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AX'.$i, $row['cloth_size_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AY'.$i, $row['ec_name_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValueExplicit('AZ'.$i, $row['ec_phone_3'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	
                	//->setCellValueExplicit('AG'.$i, $row['status_apply'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AH'.$i, $row['status_attach'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AI'.$i, $status_attach_reason, PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	
                	
                	//->setCellValueExplicit('Q'.$i, $row['renshou_zengxian'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('R'.$i, $row['invit_code'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('S'.$i, $row['is_free'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('T'.$i, $row['addtime_apply'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	//->setCellValueExplicit('U'.$i, $cert_medical, PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('V'.$i, $row['best_chengji_score'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('W'.$i, $cert_chengji, PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('X'.$i, $row['bus_point'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('Y'.$i, $row['addtime_attach'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	//->setCellValueExplicit('Z'.$i, $row['order_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AA'.$i, $row['trade_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AB'.$i, $row['amount_total'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AC'.$i, $row['payMode'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AD'.$i, $row['isPay'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AE'.$i, $row['payDateTime'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	
                	//->setCellValueExplicit('J'.$i, $row['email'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('W'.$i, $row['ec_relation'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('Y'.$i, $row['ec_address'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('O'.$i, $row['best_chengji_item'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('T'.$i, $row['medical_runner'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AG'.$i, $row['reg_channel'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AJ'.$i, $row['p_district'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValueExplicit('AK'.$i, $row['p_address'], PHPExcel_Cell_DataType::TYPE_STRING)
                	//->setCellValue('AL'.$i, $row['p_email'])
                	;
                    
  				
			}
			
			
	        // Rename sheet
	        $objPHPExcel->getActiveSheet()->setTitle('报名定向赛列表');


	        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
	        $objPHPExcel->setActiveSheetIndex(0);
		  
	        
	        
	        /*
	        //导出.xls方式
	        //echo BASE_UPLOAD_PATH;exit;
	        $output_filepath= BASE_UPLOAD_PATH.time().'.xls';
	        //echo $output_filepath;exit;
	        //header('Content-Type: application/vnd.ms-excel');
	        //header('Content-Disposition: attachment;filename="报名定向赛列表.xls"');
	        //header('Cache-Control: max-age=0');
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	        $objWriter->save($output_filepath);
	        exit;
	        */
	        
	        
	        
	        //echo str_replace('.php', '.csv', __FILE__);exit;
	        //header("Content-Type: text/csv;charset=UTF-8");  
	        //header('Content-Disposition: attachment; filename="报名定向赛列表.csv"');  
	        //header('Cache-Control:must-revalidate,post-check=0,pre-check=0');  
	        //header('Expires:0');  
	        //header('Pragma:public'); 
	        
	        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV')->setDelimiter(',')
              //                                                    ->setEnclosure('"')
              //                                                    ->setLineEnding("\r\n")
              //                                                    ->setSheetIndex(0)
              //                                                    ->save(str_replace('.php', '.csv', __FILE__));
              //exit;
              
	        /*
	        //研究，但暂时无法实现导出csv。
	        
	        //导出.csv方式
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="报名定向赛列表.csv"');
	        header('Cache-Control: max-age=0');
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
	        $objWriter->save('php://output');
	        exit;
	        
	        header('Content-Type: application/vnd.ms-excel');
	        //header("Content-Type: text/csv;charset=UTF-8");  
	        header('Content-Disposition: attachment; filename="报名定向赛列表.csv"');  
	        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');  
	        header('Expires:0');  
	        header('Pragma:public'); 
	        $objWriter = new PHPExcel_Writer_CSV($objPHPExcel,'CSV');
	        $objWriter->save("php://output");
	        exit;
	        */
	        
	        
	        /*
	        if($_SERVER["HTTP_HOST"]=='cdmalasong.loc'){
	        //导出.xls方式
	        $output_filepath= BASE_UPLOAD_PATH.time().'.xls';
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	        $objWriter->save($output_filepath);
	        exit;
	        }
	        */
	        
	        //导出.xls方式
	        // Redirect output to a client’s web browser (Excel5)
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="报名定向赛列表.xls"');
	        header('Cache-Control: max-age=0');
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	        $objWriter->save('php://output');
	        exit;
	        
	        
        }
        else{
	        //搜索查询显示
			//$rst=$this->GeneralActionForListing('order_orient', $sqlWhere, $sqlorder_orient, $page_size, 'M', false, 'catalog_id,stage_id');
			$rst=$this->GeneralActionForListing('order_orient', $sqlWhere, $sqlorder_orient, $page_size, 'M');
	        //echo "<pre>";print_r($rst);exit;
        }
		
		
		$PageTitle = L('报名定向赛列表');
		$PageMenu = array(
			//array( U('order_orient/create'), L('添加报名定向赛') ),
            //array( U('order_orient/export'), L('导出报名定向赛') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	//查看报名定向赛详情
	public function edit_show()
	{	
		
        
		$bus_point_list=$this->get_bus_point();
        $this->assign('bus_point_list', $bus_point_list );
        
        
        
		//国家地区
		$cityarea_list=$this->get_cityarea();
        $this->assign('cityarea_list', $cityarea_list );
        
        
        
		
		if( isset($_GET['id']) ){
			$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
		}

        $order_orientMod = M('order_orient');
	    
	    $info =   $order_orientMod->find($id);
	    if($info) {
	        
	        //echo "<pre>";print_r($info);exit;
	        
	        $order_orient_id=$info['id'];
	        
	        
	        
	        
	        
	        $order_orient_team_list=array();
	        if($info['user_type']==2){
		        $order_orient_teamMod = M('order_orient_team');
		        $order_orient_team_list = $order_orient_teamMod->where(" order_orient_id='".$order_orient_id."' " )->select();
		        //echo "<pre>";print_r($order_orient_team_list);exit;
	        }
	        $this->assign('order_orient_team_list', $order_orient_team_list);
	        
	        
	        $order_orient_product_list=array();
	        $rder_productMod = M('order_orient_product');
	        $order_orient_product_list = $rder_productMod->where(" order_orient_id='".$order_orient_id."' " )->select();
	        //echo "<pre>";print_r($order_orient_product_list);exit;
	        $this->assign('order_orient_product_list', $order_orient_product_list);
	        
	        
	        
    		
            
            $m_birth_day='';
            if($info['m_birth_day']=='0000-00-00'){
            	$info['m_birth_day']='';
            }
            
            
            if($info['m_sex']==1){
            	$info['m_sex']='男';
            }
            if($info['m_sex']==2){
            	$info['m_sex']='女';
            }
            if($info['user_type']==2){
            	$info['m_sex']='';
            }
            
            
            
            
            if($info['p_sex']==1){
            	$info['p_sex']='男';
            }
            if($info['p_sex']==2){
            	$info['p_sex']='女';
            }
            if($info['p_realname']==''){
            	$info['p_sex']='';
            }
            
            
            
            
            
            if($info['user_type']==2){
            	$info['m_id_type']='';
            }
            
            
            
            $this->assign('info', $info);
            
	        
	        
	    }else{
	        $this->error('报名定向赛数据读取错误');
	    }

		$PageTitle = L('编辑报名定向赛');
		$PageMenu = array(
				//array( U('order_orient/create'), L('添加报名定向赛') ),
				//array( U('order_orient/listing'), L('报名定向赛列表') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
		
	}
	
	
    //导出报名定向赛
    public function export()
    {
        $CityMod = M('order_orient');
        $toShow['banner'] = $CityMod->where(" status=1 and order_orientname!=''  " )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "报名定向赛ID编号".$expstr."手机"
            .$expstr."姓名".$expstr."性别".$expstr."生日"
            .$expstr."地址".$expstr."兴趣".$expstr."卡号"
            .$expstr."等级(0代表普通，1代表VIP)"
            .$expstr."创建时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
                /*
                if ($toShow['banner'][$k]['gender']=="1"){
                    $gender_show="男";
                }
                if ($toShow['banner'][$k]['gender']=="2"){
                    $gender_show="女";
                }

                if ($toShow['banner'][$k]['is_agree']=="1"){
                    $is_agree_show="是";
                }
                if ($toShow['banner'][$k]['is_agree']=="0"){
                    $is_agree_show="否";
                }

				
				if($toShow['banner'][$k]['mobile']!=""){
					$mobile_show="'".$toShow['banner'][$k]['mobile'];
				}
				else{
					$mobile_show="";
				}
				*/
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['order_orientname']
                    .$expstr.$toShow['banner'][$k]['realname'].$expstr.$toShow['banner'][$k]['gender'].$expstr.$toShow['banner'][$k]['birthday']
                    .$expstr.$toShow['banner'][$k]['address'].$expstr.$toShow['banner'][$k]['xingqu'].$expstr.$toShow['banner'][$k]['kahao']
                    .$expstr.$toShow['banner'][$k]['level']
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

		$bus_point_list=$this->get_bus_point();
        $this->assign('bus_point_list', $bus_point_list );
        
        
		//国家地区
		$cityarea_list=$this->get_cityarea();
        $this->assign('cityarea_list', $cityarea_list );
        
        
        
        //$CityMod = M('hangye');
        //$hangye_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc')->select();
        //echo "<pre>";print_r($hangye_list);exit;
        //$this->assign('hangye_list', $hangye_list);



        if(isset($_POST['dosubmit'])){
        //echo "<pre>";print_r($_POST);exit;
	        $order_orientMod = M('order_orient');
			
			$rst=$this->Checkorder_orientData_Post();
			
			if (false === $order_orientMod->create()) {
				$this->error($module->getError());
			}
			
	        if($order_orientMod->create()) {

	        	//echo "<pre>";print_r($order_orientMod);exit;

	        	//使用 $order_orientMod->email
        		$rst=$this->Checkorder_orientData_Mod($order_orientMod);
	        	$order_orientMod->create_time=time();
	        	$order_orientMod->password=md5($order_orientMod->password);
	        	
	        	$result =   $order_orientMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($order_orientMod->getError());
	        }
			
		}else{

			$PageTitle = L('添加报名定向赛');
			$PageMenu = array(
					array( U('order_orient/listing'), L('报名定向赛列表') ),
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
		
		$bus_point_list=$this->get_bus_point();
        $this->assign('bus_point_list', $bus_point_list );
        
        
        
		//国家地区
		$cityarea_list=$this->get_cityarea();
        $this->assign('cityarea_list', $cityarea_list );
        
        
        
        //$CityMod = M('hangye');
        //$hangye_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc')->select();
        //echo "<pre>";print_r($hangye_list);exit;
        //$this->assign('hangye_list', $hangye_list);


    	///注意：老报名定向赛报名定向赛已填写情况下不允许修改报名定向赛名，报名定向赛名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复
		
		//echo "<pre>";print_r($_POST);exit;
		
		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            $rst=$this->Checkorder_orientData_Post($id);

        	///密码为空则不修改密码
        	//if( isset($_POST['password'])){
            //    if($_POST['password'] != '' ){
        	//	    $_POST['password'] = md5($_POST['password']);
        	//    }
        	//    else{
            //        unset( $_POST['password'] );
        	//    }
            //}





                //获取更新之前的订单信息
	        $order_orientMod = M('order_orient');
	        $order_orient_info =   $order_orientMod->find($id);
	        //echo "<pre>";print_r($order_orient_info);exit;
	        //echo "<pre>";print_r($_POST);exit;
	        //echo $order_orient_info['status_apply'];exit;
	        //echo $order_orient_info['status_attach'];exit;
	        //echo $_POST['status_apply'];exit;
	        //echo $_POST['status_attach'];exit;
	        
	        
	        

	        $order_orientMod = M('order_orient');

	        if($order_orientMod->create()) {
	        	
                $rst=$this->Checkorder_orientData_Mod($order_orientMod,$id);
                
                //echo $id;exit;
                
                
                
                
                
                
                
                $order_orientMod->modify_time=time();
				$result =   $order_orientMod->save();
	            
	            if($result) {
	            	
	            	
	            	//设为已中签
	            	if(  ($order_orient_info['status_apply']==0 || $order_orient_info['status_apply']==2) && $_POST['status_apply']==1     ){
	            		
	            		
			        	//邮件通知
			        	if($this->open_email_msg==1){
						$to=$order_orient_info['email'];
						$name=$order_orient_info['realname'];
						$subject='成都国际马拉松赛组委会通知';
						if($order_orient_info['cat_id']==1){
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛马拉松项目，请于8月20日17:00前填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						if($order_orient_info['cat_id']==2){
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛半程马拉松项目，请于8月20日17:00前填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						if($order_orient_info['cat_id']==3){
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛迷你马拉松项目，请于8月20日17:00前填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						
						$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
			        	}
			        	
			        	//短信通知
			        	if($this->open_sms_msg==1){
			        		
			        		if($order_orient_info['cat_id']==1){
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛马拉松项目，请于8月20日17:00前填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						if($order_orient_info['cat_id']==2){
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛半程马拉松项目，请于8月20日17:00前填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						if($order_orient_info['cat_id']==3){
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛迷你马拉松项目，请于8月20日17:00前填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						
						header("Content-type:text/html; charset=UTF-8");
						require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
						$clapi  = new ChuanglanSmsApi();
						$code = mt_rand(100000,999999);
						$result_sms = $clapi->sendSMS($order_orient_info['mobile'], $msg_body);
						//echo "<pre>";print_r($result_sms);exit;
			        	}
			        	
			        	
	            	}
	            	
	            	
	            	
	            	//设为审核通过
	            	if( ($order_orient_info['status_attach']==0 || $order_orient_info['status_attach']==2) && $_POST['status_attach']==1){
	            		
			        	//邮件通知
			        	if($this->open_email_msg==1){
			        		$to=$order_orient_info['email'];
						$name=$order_orient_info['realname'];
						$subject='成都国际马拉松赛组委会通知';
						if($order_orient_info['is_free']==1){ //邀请码免费
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您通过东风日产2017成都国际马拉松赛的参赛资料审核并报名成功，您可于9月8日10:00查询参赛号码，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						else{
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您已通过东风日产2017成都国际马拉松赛的参赛资料审核，撒花~~请您于8月21日10:00至8月23日17:00支付报名费，报名完成以支付成功为准，感谢您的支持和参与！期待在赛道上与您相遇！【成都国际马拉松组委会】';
						}
						$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
						//var_dump($result_email);exit;
			        	}
			        	
			        	//短信通知
			        	if($this->open_sms_msg==1){
			        		if($order_orient_info['is_free']==1){ //邀请码免费
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您通过东风日产2017成都国际马拉松赛的参赛资料审核并报名成功，您可于9月8日10:00查询参赛号码，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						else{
						$msg_body='尊敬的'.$order_orient_info['realname'].'，恭喜您已通过东风日产2017成都国际马拉松赛的参赛资料审核，请您在8月21日10:00至8月23日17:00期间支付报名费，报名完成以支付成功为准，感谢您的支持和参与！【成都国际马拉松组委会】';
						}
						header("Content-type:text/html; charset=UTF-8");
						require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
						$clapi  = new ChuanglanSmsApi();
						$code = mt_rand(100000,999999);
						$result_sms = $clapi->sendSMS($order_orient_info['mobile'], $msg_body);
						//echo "<pre>";print_r($result_sms);exit;
			        	}
			        	
	            	}
	            	
	            	
	            	
	            	//设为审核拒绝
	            	if( ($order_orient_info['status_attach']==0 || $order_orient_info['status_attach']==1) && $_POST['status_attach']==2){
	            		
			        	//邮件通知
			        	if($this->open_email_msg==1){
						$to=$order_orient_info['email'];
						$name=$order_orient_info['realname'];
						$subject='成都国际马拉松赛组委会通知';
						$msg_body='尊敬的'.$order_orient_info['realname'].'，很遗憾您没有通过东风日产2017成都国际马拉松赛的参赛资料审核，您可于8月20日17:00前重新上传参赛资料；8月21日10:00前未通过资料审核的选手将失去参赛资格，审核通过的选手可根据短信提示支付报名费，感谢您的支持和参与！【成都国际马拉松组委会】';
						$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
			        	}
			        	
			        	//短信通知
			        	if($this->open_sms_msg==1){
			        		$msg_body='尊敬的'.$order_orient_info['realname'].'，很遗憾您没有通过东风日产2017成都国际马拉松赛的参赛资料审核，您可于8月20日17:00前重新上传参赛资料；8月21日10:00前未通过资料审核的选手将失去参赛资格，审核通过的选手可根据短信提示支付报名费，感谢您的支持和参与！【成都国际马拉松组委会】';
						header("Content-type:text/html; charset=UTF-8");
						require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
						$clapi  = new ChuanglanSmsApi();
						$code = mt_rand(100000,999999);
						$result_sms = $clapi->sendSMS($order_orient_info['mobile'], $msg_body);
						//echo "<pre>";print_r($result_sms);exit;
			        	}
			        	
	            	}
	            	
	            	
	            	
	            	
	                $this->success('操作成功！', U('order_orient/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($order_orientMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $order_orientMod = M('order_orient');
		    // 读取数据
		    $data =   $order_orientMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		        $this->assign('info',  $data );
		        
		        
		        /*
		        $order_orientinfo['id']=$id;
		        //我的好友圈=我的下家
				$db_fuid=array();
				$db_fuid[]=$order_orientinfo['id'];
				$db_fuid_str=implode(",", $db_fuid);
		        $andsql = " status=1 and order_orientname!='' ";
		        $andsql .= " and fuid in (".$db_fuid_str.")";
		        
		        $module = M('order_orient');
				$order_orient_arr = $module->where( $andsql )->select();
				$order_orientinfo_level_01=$order_orient_arr;
				//echo "<pre>";print_r($order_orientinfo_level_01);exit;
				//echo count($order_orientinfo_level_01);exit;
		        $this->assign('order_orientinfo_level_01',  $order_orientinfo_level_01 );
		        $this->assign('order_orientinfo_level_01_count',  count($order_orientinfo_level_01) );
		        
		        
		        
		        
		        //我的朋友圈=我的下家的下家
		        $order_orientinfo_level_02=array();
		        $db_fuid=array();
		        
		        if(!empty($order_orientinfo_level_01)){
			        foreach ($order_orientinfo_level_01 as $k=>$v) {
			        	$db_fuid[]=$v['id'];
			        }
			        
			        $db_fuid_str=implode(",", $db_fuid);
			        $andsql = " status=1 and order_orientname!='' ";
			        $andsql .= " and fuid in (".$db_fuid_str.")";
			        
			        $module = M('order_orient');
					$order_orient_arr = $module->where( $andsql )->select();
					$order_orientinfo_level_02=$order_orient_arr;
					
			        
		        }
		        //echo "<pre>";print_r($order_orientinfo_level_02);exit;
		        $this->assign('order_orientinfo_level_02',  $order_orientinfo_level_02 );
		        $this->assign('order_orientinfo_level_02_count',  count($order_orientinfo_level_02) );
		        
		        
		        
		        
				//我的人脉圈=我的下家的下家的下家
		        $order_orientinfo_level_03=array();
		        $db_fuid=array();
		        
		        if(!empty($order_orientinfo_level_02)){
			        foreach ($order_orientinfo_level_02 as $k=>$v) {
			        	$db_fuid[]=$v['id'];
			        }
			        
			        $db_fuid_str=implode(",", $db_fuid);
			        $andsql = " status=1 and order_orientname!='' ";
			        $andsql .= " and fuid in (".$db_fuid_str.")";
			        
			        $module = M('order_orient');
					$order_orient_arr = $module->where( $andsql )->select();
					$order_orientinfo_level_03=$order_orient_arr;
					
			        
		        }
		        //echo "<pre>";print_r($order_orientinfo_level_03);exit;
		        $this->assign('order_orientinfo_level_03',  $order_orientinfo_level_03 );
		        $this->assign('order_orientinfo_level_03_count',  count($order_orientinfo_level_03) );
		        */
		        
		        
		        
		        
		        
		        
		        
		        
		    }else{
		        $this->error('报名定向赛数据读取错误');
		    }
    
			$PageTitle = L('编辑报名定向赛');
			$PageMenu = array(
					//array( U('order_orient/create'), L('添加报名定向赛') ),
					array( U('order_orient/listing'), L('报名定向赛列表') ),
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
		$module = $order_orientMod = M('order_orient');
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
        $order_orientMod = M('order_orient');
        $sql=" id in (".$in.") ";
        $order_orientMod->where($sql)->delete();

        $this->success('删除成功', U('order_orient/listing'));
    }
*/

	private function Checkorder_orientData_Post($order_orient_id=0){
		///检查 $_POST 提交数据
		
			//$order_orientMod = M('order_orient');

			//$result = $order_orientMod->where("order_orientname='%s' and id!=%d ", $_POST['order_orientname'], $order_orient_id )->count();
			//if($result>0){
            //$this->error(L('存在重复的手机'));
            //}
            
			//$result = $order_orientMod->where("email='%s' and id!=%d ", $_POST['email'], $order_orient_id)->count();
			//if($result>0){
            //$this->error(L('存在重复的邮箱'));
            //}
            
            
	}
	

	private function Checkorder_orientData_Mod(&$order_orient, $order_orient_id=0){
		///检查 $order_orient 模型数据。$order_orient->email
			
			$order_orientMod = M('order_orient');
			
			$result = $order_orientMod->where(" match_code!='' and match_code='%s' and id!=%d ", $order_orient->match_code, $order_orient_id )->count();
			if($result>0){
            	$this->error(L('存在重复的参赛号'));
            }
            
			//$result = $order_orientMod->where("order_orientname='%s' and id!=%d ", $order_orient->ordername, $order_orient_id )->count();
			//if($result>0){
            //$this->error(L('存在重复的报名定向赛名'));
            //}
            
			//$result = $order_orientMod->where("email='%s' and id!=%d ", $order_orient->email, $order_orient_id)->count();
			//if($result>0){
            //$this->error(L('存在重复的邮箱'));
            //}
		
	}
	
	
	
	
	
	
    //导出层级 level01
    public function edit_export_level_01()
    {
        
        $id = intval($this->REQUEST('id'), 0);
        
        
        
        $order_orientinfo['id']=$id;
        //我的好友圈=我的下家
		$db_fuid=array();
		$db_fuid[]=$order_orientinfo['id'];
		$db_fuid_str=implode(",", $db_fuid);
        $andsql = " status=1 and order_orientname!='' ";
        $andsql .= " and fuid in (".$db_fuid_str.")";
        
        $module = M('order_orient');
		$order_orient_arr = $module->where( $andsql )->select();
		$order_orientinfo_level_01=$order_orient_arr;
		//echo "<pre>";print_r($order_orientinfo_level_01);exit;
		//echo count($order_orientinfo_level_01);exit;
        $this->assign('order_orientinfo_level_01',  $order_orientinfo_level_01 );
        $this->assign('order_orientinfo_level_01_count',  count($order_orientinfo_level_01) );
        
        
        
        $toShow['banner'] = $order_orientinfo_level_01;
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='order_orient_id_'.$id.'_level_01.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "报名定向赛ID编号".$expstr."手机"
            .$expstr."昵称".$expstr."真实姓名".$expstr."上层报名定向赛id"
            .$expstr."创建时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
                /*
                if ($toShow['banner'][$k]['gender']=="1"){
                    $gender_show="男";
                }
                if ($toShow['banner'][$k]['gender']=="2"){
                    $gender_show="女";
                }

                if ($toShow['banner'][$k]['is_agree']=="1"){
                    $is_agree_show="是";
                }
                if ($toShow['banner'][$k]['is_agree']=="0"){
                    $is_agree_show="否";
                }

				
				if($toShow['banner'][$k]['mobile']!=""){
					$mobile_show="'".$toShow['banner'][$k]['mobile'];
				}
				else{
					$mobile_show="";
				}
				*/
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['order_orientname']
                    .$expstr.$toShow['banner'][$k]['nickname'].$expstr.$toShow['banner'][$k]['realname'].$expstr.$toShow['banner'][$k]['fuid']
                    //.$expstr.$gender_show.$expstr.$toShow['banner'][$k]['birth_year']
                    //.$expstr.$mobile_show.$expstr.$toShow['banner'][$k]['hangye']
                    //.$expstr.$toShow['banner'][$k]['address'].$expstr.$is_agree_show
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
    
    
    
    
	
    //导出层级 level02
    public function edit_export_level_02()
    {
        
        $id = intval($this->REQUEST('id'), 0);
        
        
        
		        
		        
        
        $order_orientinfo['id']=$id;
        //我的好友圈=我的下家
		$db_fuid=array();
		$db_fuid[]=$order_orientinfo['id'];
		$db_fuid_str=implode(",", $db_fuid);
        $andsql = " status=1 and order_orientname!='' ";
        $andsql .= " and fuid in (".$db_fuid_str.")";
        
        $module = M('order_orient');
		$order_orient_arr = $module->where( $andsql )->select();
		$order_orientinfo_level_01=$order_orient_arr;
		//echo "<pre>";print_r($order_orientinfo_level_01);exit;
		//echo count($order_orientinfo_level_01);exit;
        $this->assign('order_orientinfo_level_01',  $order_orientinfo_level_01 );
        $this->assign('order_orientinfo_level_01_count',  count($order_orientinfo_level_01) );
        
        
        
        //我的朋友圈=我的下家的下家
        $order_orientinfo_level_02=array();
        $db_fuid=array();
        
        if(!empty($order_orientinfo_level_01)){
	        foreach ($order_orientinfo_level_01 as $k=>$v) {
	        	$db_fuid[]=$v['id'];
	        }
	        
	        $db_fuid_str=implode(",", $db_fuid);
	        $andsql = " status=1 and order_orientname!='' ";
	        $andsql .= " and fuid in (".$db_fuid_str.")";
	        
	        $module = M('order_orient');
			$order_orient_arr = $module->where( $andsql )->select();
			$order_orientinfo_level_02=$order_orient_arr;
			
	        
        }
        //echo "<pre>";print_r($order_orientinfo_level_02);exit;
        $this->assign('order_orientinfo_level_02',  $order_orientinfo_level_02 );
        $this->assign('order_orientinfo_level_02_count',  count($order_orientinfo_level_02) );
        
        
        
        
        
        
        
        $toShow['banner'] = $order_orientinfo_level_02;
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='order_orient_id_'.$id.'_level_02.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "报名定向赛ID编号".$expstr."手机"
            .$expstr."昵称".$expstr."真实姓名".$expstr."上层报名定向赛id"
            .$expstr."创建时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
                /*
                if ($toShow['banner'][$k]['gender']=="1"){
                    $gender_show="男";
                }
                if ($toShow['banner'][$k]['gender']=="2"){
                    $gender_show="女";
                }

                if ($toShow['banner'][$k]['is_agree']=="1"){
                    $is_agree_show="是";
                }
                if ($toShow['banner'][$k]['is_agree']=="0"){
                    $is_agree_show="否";
                }

				
				if($toShow['banner'][$k]['mobile']!=""){
					$mobile_show="'".$toShow['banner'][$k]['mobile'];
				}
				else{
					$mobile_show="";
				}
				*/
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['order_orientname']
                    .$expstr.$toShow['banner'][$k]['nickname'].$expstr.$toShow['banner'][$k]['realname'].$expstr.$toShow['banner'][$k]['fuid']
                    //.$expstr.$gender_show.$expstr.$toShow['banner'][$k]['birth_year']
                    //.$expstr.$mobile_show.$expstr.$toShow['banner'][$k]['hangye']
                    //.$expstr.$toShow['banner'][$k]['address'].$expstr.$is_agree_show
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
    
    
    
	
    //导出层级 level03
    public function edit_export_level_03()
    {
        
        $id = intval($this->REQUEST('id'), 0);
        
        
        
        $order_orientinfo['id']=$id;
        //我的好友圈=我的下家
		$db_fuid=array();
		$db_fuid[]=$order_orientinfo['id'];
		$db_fuid_str=implode(",", $db_fuid);
        $andsql = " status=1 and order_orientname!='' ";
        $andsql .= " and fuid in (".$db_fuid_str.")";
        
        $module = M('order_orient');
		$order_orient_arr = $module->where( $andsql )->select();
		$order_orientinfo_level_01=$order_orient_arr;
		//echo "<pre>";print_r($order_orientinfo_level_01);exit;
		//echo count($order_orientinfo_level_01);exit;
        $this->assign('order_orientinfo_level_01',  $order_orientinfo_level_01 );
        $this->assign('order_orientinfo_level_01_count',  count($order_orientinfo_level_01) );
        
        
        
        
        //我的朋友圈=我的下家的下家
        $order_orientinfo_level_02=array();
        $db_fuid=array();
        
        if(!empty($order_orientinfo_level_01)){
	        foreach ($order_orientinfo_level_01 as $k=>$v) {
	        	$db_fuid[]=$v['id'];
	        }
	        
	        $db_fuid_str=implode(",", $db_fuid);
	        $andsql = " status=1 and order_orientname!='' ";
	        $andsql .= " and fuid in (".$db_fuid_str.")";
	        
	        $module = M('order_orient');
			$order_orient_arr = $module->where( $andsql )->select();
			$order_orientinfo_level_02=$order_orient_arr;
			
	        
        }
        //echo "<pre>";print_r($order_orientinfo_level_02);exit;
        $this->assign('order_orientinfo_level_02',  $order_orientinfo_level_02 );
        $this->assign('order_orientinfo_level_02_count',  count($order_orientinfo_level_02) );
        
        
        
        
		//我的人脉圈=我的下家的下家的下家
        $order_orientinfo_level_03=array();
        $db_fuid=array();
        
        if(!empty($order_orientinfo_level_02)){
	        foreach ($order_orientinfo_level_02 as $k=>$v) {
	        	$db_fuid[]=$v['id'];
	        }
	        
	        $db_fuid_str=implode(",", $db_fuid);
	        $andsql = " status=1 and order_orientname!='' ";
	        $andsql .= " and fuid in (".$db_fuid_str.")";
	        
	        $module = M('order_orient');
			$order_orient_arr = $module->where( $andsql )->select();
			$order_orientinfo_level_03=$order_orient_arr;
			
	        
        }
        //echo "<pre>";print_r($order_orientinfo_level_03);exit;
        $this->assign('order_orientinfo_level_03',  $order_orientinfo_level_03 );
        $this->assign('order_orientinfo_level_03_count',  count($order_orientinfo_level_03) );
        
        
        
        
        
        $toShow['banner'] = $order_orientinfo_level_03;
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='order_orient_id_'.$id.'_level_03.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "报名定向赛ID编号".$expstr."手机"
            .$expstr."昵称".$expstr."真实姓名".$expstr."上层报名定向赛id"
            .$expstr."创建时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
                /*
                if ($toShow['banner'][$k]['gender']=="1"){
                    $gender_show="男";
                }
                if ($toShow['banner'][$k]['gender']=="2"){
                    $gender_show="女";
                }

                if ($toShow['banner'][$k]['is_agree']=="1"){
                    $is_agree_show="是";
                }
                if ($toShow['banner'][$k]['is_agree']=="0"){
                    $is_agree_show="否";
                }

				
				if($toShow['banner'][$k]['mobile']!=""){
					$mobile_show="'".$toShow['banner'][$k]['mobile'];
				}
				else{
					$mobile_show="";
				}
				*/
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['order_orientname']
                    .$expstr.$toShow['banner'][$k]['nickname'].$expstr.$toShow['banner'][$k]['realname'].$expstr.$toShow['banner'][$k]['fuid']
                    //.$expstr.$gender_show.$expstr.$toShow['banner'][$k]['birth_year']
                    //.$expstr.$mobile_show.$expstr.$toShow['banner'][$k]['hangye']
                    //.$expstr.$toShow['banner'][$k]['address'].$expstr.$is_agree_show
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
    
    
    
    
    
	//导入 报名定向赛
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
			
			require_once APP_PATH .'Lib/phpexcel/Classes/PHPExcel.php'; 
			require_once APP_PATH .'Lib/phpexcel/Classes/PHPExcel/IOFactory.php';
			
			
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format 
			$objPHPExcel = $objReader->load($uploadfile); 


			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); // 取得总行数 
			$highestColumn = $sheet->getHighestColumn(); // 取得总列数
  			$arr_result=array();
  			$strs=array();
			
			$order_orientMod = M('order_orient');
			$table_name=$order_orientMod->getTableName();
			
			
			
			$order_orient_last = $order_orientMod->where(" 1 " )->order(' id desc')->limit('0,1')->select();
			if(!empty($order_orient_last)){
				$order_orient_last=$order_orient_last[0];
				$db_id=$order_orient_last['id']+1;
			}
			else{
				$db_id=1;
			}
			$kahao=10000000+$db_id;
			//echo "<pre>";print_r($order_orient_last);exit;
			//echo $kahao;exit;
			
			$book_err_str="";
			
			if($highestRow>=2){
				
				//先删除旧数据
				//$sql=" 1 ";
            	//$order_orientMod->where($sql)->delete();
				
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
					
		            $result_isset = $order_orientMod->where(" order_orientname='".addslashes($strs[0]) ."' " )->select();
		            if(isset($result_isset[0])){
		            	
		            	if(!empty($strs[0])){
		            	//更新
			            	$sql="update ".$table_name." SET 
				              password='".md5($strs[1])."'
				            , realname='".addslashes($strs[2])."'
				            , level='".addslashes($strs[3])."'
				            , addtime='".addslashes($strs[4])."'
				            , create_time='".strtotime($strs[4])."'
				            where order_orientname='".addslashes($strs[0]) ."' 
				             ";
				            //$result_edit = $order_orientMod->execute($sql);   //导入的时候遇到重复的qq号不处理，不重复的仍然导入，然后导入完毕后，提示重复的qq号是哪些。
				            $book_err_str=$book_err_str.$strs[0]."<br>";
			            }
			            
		            }
		            else{
		            	
		            	if(!empty($strs[0])){
			            	//新增
			            	$sql="insert into ".$table_name." SET 
			            	  order_orientname='".addslashes($strs[0])."'
				            , password='".md5($strs[1])."'
				            , realname='".addslashes($strs[2])."'
				            , level='".addslashes($strs[3])."'
				            , addtime='".addslashes($strs[4])."'
				            , create_time='".strtotime($strs[4])."'
				            , id='".addslashes($db_id)."'
				            , kahao='".addslashes($kahao)."'
				             ";
				            $result_add = $order_orientMod->execute($sql);
				            
				            $db_id=$db_id+1;
				            $kahao=10000000+$db_id;
				            
			            }
		            }
				}
			}
			
			if($book_err_str!=""){
				$book_err_str='存在这些重复的手机号不做处理：<br><br>'.$book_err_str.'<br><br>其他已经成功导入。<br><br><a href="'.U('order_orient/listing_import').'">返回&gt;&gt;</a><br><br>';
				echo $book_err_str;
				exit;
			}
			else{
            	$this->success('导入成功！', U('order_orient/listing_import'));
            	exit;
            }
        }
        
        
        


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		$PageTitle = L('报名定向赛导入');
		$PageMenu = array(
			//array( U('order_orient/create'), L('添加报名定向赛') ),
            //array( U('order_orient/export'), L('导出报名定向赛') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
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
    
    
    //所有摆渡车点
    public function get_bus_point(){
    	$CityMod = M('bus_point');
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



    //所有国家/地区
    public function get_cityarea(){
    	$CityMod = M('guoji');
        $parent_list = $CityMod->field('id,guoji_name')->where(" 1 " )->order('id asc')->select();
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
    
    

	

}
?>