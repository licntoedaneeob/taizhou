<?php
/**
 * 简单订单系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class tradeAction extends TAction
{
	
	

	/**
	 *--------------------------------------------------------------+
	 * Action: list 订单列表
	 *--------------------------------------------------------------+
	 */
	public function listing()
	{
		
		
		$this->checkOrderTotalAmout();
		
		

        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='删除'){
        	/*
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
            $orderMod = M('order');
            $sql=" id in (".$in.") ";
            $orderMod->where($sql)->delete();

            $this->success('删除成功', U('order/listing'));
            exit;
            */
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
            
			
			$orderMod = M('order');
		    $sql=sprintf("update %s SET level='0' 
		    where ".$sql_where." 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
		    
            $this->success('批量设为普通等级成功', U('order/listing'));
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
            
			
			$orderMod = M('order');
		    $sql=sprintf("update %s SET level='1' 
		    where ".$sql_where." 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
		    
            $this->success('批量设为VIP等级成功', U('order/listing'));
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
			
			
			
			//需要分配卡券的订单列表
			$order_list=$ids;
			//echo "<pre>";print_r($order_list);exit;
			$cur_time=time();
			
			//echo "<pre>";print_r($_POST);exit;
			
			if(!empty($order_list)){
				
				$order_quan_historyMod = M('order_quan_history');
				
				$number=intval($this->REQUEST('quan_number'), 0);
				
	            if($number>=1 && is_numeric($number)){
		            $x=0;
		            do{
		            	foreach($order_list as $k_order=>$v_order){
		            		
		            		
					        //兑换码
					        $title=$this->shortCode(uniqid());
					        
					        $order_quan_historyMod->create_time=$cur_time;
					        $order_quan_historyMod->modify_time=$cur_time;
					        $order_quan_historyMod->addtime=date("Y-m-d H:i:s",$cur_time);
					        $order_quan_historyMod->order_id=$v_order;
					        $order_quan_historyMod->class_id=$quan_info['id'];
					        $order_quan_historyMod->title=$title;
					        $order_quan_historyMod->is_used=1;
					        //echo "<pre>";print_r($order_quan_historyMod);exit;
					        $order_quan_history_id = $order_quan_historyMod->add();
					        //var_dump($order_quan_history_id);exit;
					        
		            	}
		            	$x=$x+1;
		            }while($x<$number);
	            }
	            
            }
            
		    
            $this->success('批量分配卡券成功', U('order/listing'));
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
        
        
        $quan_historyMod = M('order_quan_history');
        $quan_history_list = $quan_historyMod->where(" status=1 " )->select();
        //echo "<pre>";print_r($quan_history_list);exit;
        $quan_history_arr=array();
        if(!empty($quan_history_list)){
        	foreach($quan_history_list as $k=>$v){
        		$quan_summary=$quan_arr[$v['class_id']]['title'].' '.$quan_arr[$v['class_id']]['start_time'].' ~ '.$quan_arr[$v['class_id']]['end_time'].' '.$v['title'];
        		if(isset($quan_history_arr[$v['order_id']]['quan_summary'])){
        			$quan_history_arr[$v['order_id']]['quan_summary']=$quan_history_arr[$v['order_id']]['quan_summary']."<br>".$quan_summary;
        		}
        		else{
        			$quan_history_arr[$v['order_id']]['quan_summary']=$quan_summary;
        		}
        	}
        }
        //echo "<pre>";print_r($quan_history_arr);exit;
        $this->assign('quan_history_list', $quan_history_list);
        $this->assign('quan_history_arr', $quan_history_arr);
        */
        
        
        //获得赛事分站列表
        $orderMod = M('order');
        $catalog_stage_list = $orderMod->field('catalog_id,catalog_name,stage_id,stage_name')->where(" status=1 " )->group('catalog_id,stage_id')->select();
        $vals = array();
		foreach($catalog_stage_list as $key => $row)
		{
			$vals[$key] = $row['catalog_id'];
		}
		array_multisort($vals, SORT_ASC, $catalog_stage_list);
		//echo "<pre>";print_r($catalog_stage_list);exit;
        $this->assign('catalog_stage_list', $catalog_stage_list);
        
        
        
        
        
        
        /*
        //获得所有团队成员
        $order_teamMod = M('order_team');
        $order_team_list = $order_teamMod->field('order_id,t_realname,t_sex,t_mobile,t_id_type,t_id_number,t_birth_day')->where(" 1 " )->select();
        $order_team_arr=array();
        if(!empty($order_team_list)){
			foreach($order_team_list as $k => $v){
				$v_tmp=$v;
				$v_tmp['t_sex']=($v_tmp['t_sex']==1)?'男':'女';
				unset($v_tmp['order_id']);
				//echo "<pre>";print_r($v_tmp);exit;
				$order_team_arr[$v['order_id']]['list'][] = $v;
				$info=implode("_", $v_tmp);
				$order_team_arr[$v['order_id']]['info'][] = $info;
				
			}
		}
		//echo "<pre>";print_r($order_team_arr);exit;
        $this->assign('order_team_arr', $order_team_arr);
        
        
        
        
        //获得所有商品
        $order_productMod = M('order_product');
        $order_product_list = $order_productMod->field('order_id,product_name,sku_name,price,number,price_sub')->where(" 1 " )->select();
        $order_product_arr=array();
        if(!empty($order_product_list)){
			foreach($order_product_list as $k => $v){
				$v_tmp=$v;
				unset($v_tmp['order_id']);
				//echo "<pre>";print_r($v_tmp);exit;
				$order_product_arr[$v['order_id']]['list'][] = $v;
				$info=implode("_", $v_tmp);
				$order_product_arr[$v['order_id']]['info'][] = $info;
				
			}
		}
		//echo "<pre>";print_r($order_product_arr);exit;
        $this->assign('order_product_arr', $order_product_arr);
        */
        
        
        
        //获得所有项目，归类到各自的订单号名下
        $orderMod = M('order');
        $order_race_list = $orderMod->field('id,trade_no,order_no,group_name,race_name,user_type')->where(" 1 and status=1 " )->select();
        $order_race_arr=array();
        if(!empty($order_race_list)){
			foreach($order_race_list as $k => $v){
				if($v['user_type']==1){
					$user_type_show='个人';
				}
				elseif($v['user_type']==2){
					$user_type_show='团队';
				}
				else{
					$user_type_show='';
				}
				
				$a=U('order/edit_show', array('id'=>$v['id']));
				//echo $a;exit;
				
				$order_race_arr[$v['order_no']]['list_a'][] = '<a href="'.$a.'">'.$v['race_name'].' '.$user_type_show.'</a>';
				$order_race_arr[$v['order_no']]['list'][] = $v['race_name'].' '.$user_type_show;
				
				$info_tmp=implode(" | ", $order_race_arr[$v['order_no']]['list']);
				$order_race_arr[$v['order_no']]['info'] = $info_tmp;
				
				$br_tmp=implode("<br>", $order_race_arr[$v['order_no']]['list_a']);
				$order_race_arr[$v['order_no']]['br'] = $br_tmp;
				
				
				
			}
		}
		//echo "<pre>";print_r($order_race_arr);exit;
        $this->assign('order_race_arr', $order_race_arr);
        
        
        
        
        
		
        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status=1 ";
		//$sqlWhere .= " and ordername!='' ";
		$sqlOrder = "id DESC";
		
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
			elseif($filter_fieldname=='m_mobile'){
				$sqlWhere .= " and (m_mobile like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='m_realname'){
				$sqlWhere .= " and (m_realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (order_no like '%". $this->fixSQL($f_search)."%' or trade_no like '%". $this->fixSQL($f_search)."%' or m_mobile like '%". $this->fixSQL($f_search)."%' or m_realname like '%". $this->fixSQL($f_search)."%' )";
			}
			else{
			}
		}
		
		
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sql_starttime=date('Y-m-d H:i:s',$sql_starttime);
			$sqlWhere .= " and createDateTime >= '". $this->fixSQL($sql_starttime)."' ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sql_endtime=date('Y-m-d H:i:s',$sql_endtime);
			$sqlWhere .= " and createDateTime < '". $this->fixSQL($sql_endtime)."' ";
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
		
		
		
		
		
		$filter_catalog_stage = $this->REQUEST('_filter_catalog_stage');
		if( $filter_catalog_stage==='' ){
		}
		else{
			$catalog_stage=explode("|", $filter_catalog_stage);
			$catalog_id=$catalog_stage[0];
			$stage_id=$catalog_stage[1];
			$sqlWhere .= " and catalog_id = '". $this->fixSQL($catalog_id)."' and stage_id = '". $this->fixSQL($stage_id)."' ";
		}
		
		
		
		
		
		

        $this->ModManager = M('order');
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
        
        
        //强制排序
        $sqlOrder = 'order_no DESC, id DESC';
        //echo $sqlOrder;exit;

		///回传过滤条件
		$this->assign('filter_fieldname',  $filter_fieldname);
		$this->assign('filter_starttime',  $filter_starttime);
		$this->assign('filter_endtime',  $filter_endtime);
		
		
		$this->assign('filter_payMode',  $filter_payMode);
		$this->assign('filter_isPay',  $filter_isPay);
		$this->assign('filter_catalog_stage',  $filter_catalog_stage);
		
		//$this->assign('filter_starttime_birthday',  $filter_starttime_birthday);
		//$this->assign('filter_endtime_birthday',  $filter_endtime_birthday);
		//$this->assign('filter_level',  $filter_level);
		//$this->assign('filter_birth_month',  $filter_birth_month);



        $this->assign('filter_role',   $filter_role);
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
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
			$rst=$this->GeneralActionForListing('order', $sqlWhere, $sqlOrder, '100000000', 'M', false , 'order_no');
			
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
            ->setCellValue('A'.$i, '订单号')
            ->setCellValue('B'.$i, '支付交易号')
            ->setCellValue('C'.$i, '支付方式(0代表未定义，1代表支付宝，2代表微信，9代表线下支付)')
            ->setCellValue('D'.$i, '总金额')
            
            ->setCellValue('E'.$i, '支付状态(0代表未支付，1代表已支付，2代表待确认)')
            ->setCellValue('F'.$i, '站点')
            ->setCellValue('G'.$i, '报名项目')
            ;
            
            /*
            ->setCellValue('H'.$i, '赛事')
            ->setCellValue('I'.$i, '站点')
            ->setCellValue('J'.$i, '票类型(1代表单票，2代表通票)')
            ->setCellValue('K'.$i, '结构模式(1代表group模式，2代表race模式)')
            ->setCellValue('L'.$i, '组别')
            ->setCellValue('M'.$i, '比赛')
            ->setCellValue('N'.$i, '参赛体制(1代表个人，2代表团队)')
            
            ->setCellValue('O'.$i, '个人姓名')
            ->setCellValue('P'.$i, '个人手机')
            ->setCellValue('Q'.$i, '个人性别')
            ->setCellValue('R'.$i, '个人证件类型(1代表身份证，2代表护照，3代表港澳台)')
            ->setCellValue('S'.$i, '个人证件')
            ->setCellValue('T'.$i, '个人生日')
            ->setCellValue('U'.$i, '个人省')
            ->setCellValue('V'.$i, '个人市')
            ->setCellValue('W'.$i, '个人区')
            ->setCellValue('X'.$i, '个人地址')
            ->setCellValue('Y'.$i, '个人邮箱')
            ->setCellValue('Z'.$i, '个人紧急联系人')
            ->setCellValue('AA'.$i, '个人紧急联系手机')
            
            ->setCellValue('AB'.$i, '参赛团队名')
            ->setCellValue('AC'.$i, '团队成员信息')
            
            ->setCellValue('AD'.$i, '商品信息')
            ->setCellValue('AE'.$i, '送货姓名')
            ->setCellValue('AF'.$i, '送货性别')
            ->setCellValue('AG'.$i, '送货手机')
            ->setCellValue('AH'.$i, '送货省')
            ->setCellValue('AI'.$i, '送货市')
            ->setCellValue('AJ'.$i, '送货区')
            ->setCellValue('AK'.$i, '送货地址')
            ->setCellValue('AL'.$i, '送货邮箱')
            */
            
            
			
  			foreach ($toShow['banner'] as $k=>$row){
                
                $i ++;
  				
  				//echo "<pre>";print_r($row);exit;
  				
  				
				
                $order_id=$toShow['banner'][$k]['id'];
                
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
                	$m_info=implode("|", $order_team_arr[$order_id]['info']);
                }
                
                
                $p_info='';
                if(!empty($order_product_arr[$order_id]['info'])){
                	$p_info=implode("|", $order_product_arr[$order_id]['info']);
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
                
                
  				
  				
  				
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$i, $row['order_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B'.$i, $row['trade_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('C'.$i, $row['payMode'])
                    ->setCellValue('D'.$i, $row['amount_total'])
                    
                    ->setCellValue('E'.$i, $row['isPay'])
                    ->setCellValue('F'.$i, $row['stage_name'])
                    ->setCellValue('G'.$i, $order_race_arr[$row['order_no']]['info'])
                    ;
                    
                    /*
                	->setCellValue('E'.$i, $row['amount_total'])
                    ->setCellValue('F'.$i, $row['payMode'])
                    ->setCellValue('G'.$i, $row['isPay'])
                	
                	->setCellValue('H'.$i, $row['catalog_name'])
                	->setCellValue('I'.$i, $row['stage_name'])
                	->setCellValue('J'.$i, $row['ticket_type'])
                	->setCellValue('K'.$i, $row['stru_id'])
                	->setCellValue('L'.$i, $row['group_name'])
                	->setCellValue('M'.$i, $row['race_name'])
                	->setCellValue('N'.$i, $row['user_type'])
                	
                	->setCellValue('O'.$i, $row['m_realname'])
                	->setCellValueExplicit('P'.$i, $row['m_mobile'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValue('Q'.$i, $m_sex)
                	->setCellValue('R'.$i, $row['m_id_type'])
                	->setCellValueExplicit('S'.$i, $row['m_id_number'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValue('T'.$i, $m_birth_day)
                	->setCellValue('U'.$i, $row['m_province'])
                	->setCellValue('V'.$i, $row['m_city'])
                	->setCellValue('W'.$i, $row['m_district'])
                	->setCellValue('X'.$i, $row['m_address'])
                	->setCellValue('Y'.$i, $row['m_email'])
                	->setCellValue('Z'.$i, $row['m_ec_name'])
                	->setCellValueExplicit('AA'.$i, $row['m_ec_phone1'], PHPExcel_Cell_DataType::TYPE_STRING)
                	
                	->setCellValue('AB'.$i, $row['chedui_name_attend'])
                	->setCellValue('AC'.$i, $m_info)
                	
                	->setCellValue('AD'.$i, $p_info)
                	->setCellValue('AE'.$i, $row['p_realname'])
                	->setCellValue('AF'.$i, $p_sex)
                	->setCellValueExplicit('AG'.$i, $row['p_mobile'], PHPExcel_Cell_DataType::TYPE_STRING)
                	->setCellValue('AH'.$i, $row['p_province'])
                	->setCellValue('AI'.$i, $row['p_city'])
                	->setCellValue('AJ'.$i, $row['p_district'])
                	->setCellValue('AK'.$i, $row['p_address'])
                	->setCellValue('AL'.$i, $row['p_email'])
                	*/
                	
                    
  				
			}
			
			
	        // Rename sheet
	        $objPHPExcel->getActiveSheet()->setTitle('奥林体育-报名列表');


	        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
	        $objPHPExcel->setActiveSheetIndex(0);


	        // Redirect output to a client’s web browser (Excel5)
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="奥林体育-报名列表.xls"');
	        header('Cache-Control: max-age=0');

	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	        $objWriter->save('php://output');
	        exit;
	        
	        
			
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= "订单流水ID编号"
	        	.$expstr."报名时间"
	        	.$expstr."订单号"
	            .$expstr."支付交易号"
	            .$expstr."总金额"
	            .$expstr."支付方式(0代表未定义，1代表支付宝，2代表微信，9代表线下支付)"
	            .$expstr."支付状态(0代表未支付，1代表已支付，2代表待确认)"
	            
	            .$expstr."赛事"
	            .$expstr."站点"
	            .$expstr."票类型(1代表单票，2代表通票)"
	            .$expstr."结构模式(1代表group模式，2代表race模式)"
	            .$expstr."组别"
	            .$expstr."比赛"
	            
	            .$expstr."参赛体制(1代表个人，2代表团队)"
	            
	            .$expstr."个人姓名"
	            .$expstr."个人手机"
	            .$expstr."个人性别"
	            .$expstr."个人证件类型(1代表身份证，2代表护照，3代表港澳台地区证件)"
	            .$expstr."个人证件"
	            .$expstr."个人生日"
	            .$expstr."个人省"
	            .$expstr."个人市"
	            .$expstr."个人区"
	            .$expstr."个人地址"
	            .$expstr."个人邮箱"
	            .$expstr."个人紧急联系人"
	            .$expstr."个人紧急联系手机"
	            .$expstr."参赛团队名"
	            .$expstr."团队成员信息"
	            
	            .$expstr."商品信息"
	            .$expstr."送货姓名"
	            .$expstr."送货性别"
	            .$expstr."送货手机"
	            .$expstr."送货省"
	            .$expstr."送货市"
	            .$expstr."送货区"
	            .$expstr."送货地址"
	            .$expstr."送货邮箱"
	            
	            
	            .$expenter;
			
	        
			
	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
	                $order_id=$toShow['banner'][$k]['id'];
	                
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
	                	$m_info=implode("|", $order_team_arr[$order_id]['info']);
	                }
	                
	                
	                $p_info='';
	                if(!empty($order_product_arr[$order_id]['info'])){
	                	$p_info=implode("|", $order_product_arr[$order_id]['info']);
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
	                
	                
	                
	                $output .= $toShow['banner'][$k]['id']
	                	.$expstr.$toShow['banner'][$k]['createDateTime']
	                	.$expstr.$order_no
	                    .$expstr.$trade_no
	                    .$expstr.$toShow['banner'][$k]['amount_total']
	                    .$expstr.$toShow['banner'][$k]['payMode']
	                    .$expstr.$toShow['banner'][$k]['isPay']
	                    
	                    .$expstr.$toShow['banner'][$k]['catalog_name']
	                    .$expstr.$toShow['banner'][$k]['stage_name']
	                    .$expstr.$toShow['banner'][$k]['ticket_type']
	                    .$expstr.$toShow['banner'][$k]['stru_id']
	                    .$expstr.$toShow['banner'][$k]['group_name']
	                    .$expstr.$toShow['banner'][$k]['race_name']
	                    
	                    .$expstr.$toShow['banner'][$k]['user_type']
	                    
	                    .$expstr.$toShow['banner'][$k]['m_realname']
	                    .$expstr.$toShow['banner'][$k]['m_mobile']
	                    .$expstr.$m_sex
	                    .$expstr.$toShow['banner'][$k]['m_id_type']
	                    .$expstr.$m_id_number
	                    .$expstr.$m_birth_day
	                    .$expstr.$toShow['banner'][$k]['m_province']
	                    .$expstr.$toShow['banner'][$k]['m_city']
	                    .$expstr.$toShow['banner'][$k]['m_district']
	                    .$expstr.$toShow['banner'][$k]['m_address']
	                    .$expstr.$toShow['banner'][$k]['m_email']
	                    .$expstr.$toShow['banner'][$k]['m_ec_name']
	                    .$expstr.$toShow['banner'][$k]['m_ec_phone1']
	                    .$expstr.$toShow['banner'][$k]['chedui_name_attend']
	                    .$expstr.$m_info
	                    
	                    .$expstr.$p_info
	                    .$expstr.$toShow['banner'][$k]['p_realname']
	                    .$expstr.$p_sex
	                    .$expstr.$toShow['banner'][$k]['p_mobile']
	                    .$expstr.$toShow['banner'][$k]['p_province']
	                    .$expstr.$toShow['banner'][$k]['p_city']
	                    .$expstr.$toShow['banner'][$k]['p_district']
	                    .$expstr.$toShow['banner'][$k]['p_address']
	                    .$expstr.$toShow['banner'][$k]['p_email']
	                    
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
        else{
	        //搜索查询显示
			//$rst=$this->GeneralActionForListing('order', $sqlWhere, $sqlOrder, $page_size, 'M', false, 'catalog_id,stage_id');
			
			$rst=$this->GeneralActionForListing('order', $sqlWhere, $sqlOrder, $page_size, 'M', false , 'order_no');
	        //echo "<pre>";print_r($rst);exit;
        }
		
		
		$PageTitle = L('交易列表');
		$PageMenu = array(
			//array( U('order/create'), L('添加订单') ),
            //array( U('order/export'), L('导出订单') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	//查看订单详情
	public function edit_show()
	{	
		
        
		
		
		if( isset($_GET['id']) ){
			$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
		}
		
		
        $orderMod = M('order');
	    $big_info =   $orderMod->find($id);
	    
	    if($big_info) {
	        
	        //echo "<pre>";print_r($big_info);exit;
	        $order_no=$big_info['order_no'];
	        
	        $order_list = $orderMod->where(" order_no='".addslashes($order_no)."' " )->select();
	        //echo "<pre>";print_r($order_list);exit;
	        
	        
	        if(!empty($order_list)){
	        	foreach($order_list as $k=>$info){
	        		$order_id=$info['id'];
	        		
	        		
			        $order_team_list=array();
			        if($info['user_type']==2){
				        $order_teamMod = M('order_team');
				        $order_team_list = $order_teamMod->where(" order_id='".$order_id."' " )->select();
				        //echo "<pre>";print_r($order_team_list);exit;
			        }
			        //$this->assign('order_team_list', $order_team_list);
			        $info['order_team_list']=$order_team_list;
	        		
	        		
			        
			        $order_product_list=array();
			        $rder_productMod = M('order_product');
			        $order_product_list = $rder_productMod->where(" order_id='".$order_id."' " )->select();
			        //echo "<pre>";print_r($order_product_list);exit;
			        //$this->assign('order_product_list', $order_product_list);
			        $info['order_product_list']=$order_product_list;
			        
			        
			        
		            
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
		            
			        
			        $order_list[$k]=$info;
	        		
	        		//echo "<pre>";print_r($order_list[$k]);exit;
	        		
	        	}
	        }
	        
	        
	        //echo "<pre>";print_r($order_list);exit;
    		
            
            $this->assign('big_info', $big_info);
            $this->assign('order_list', $order_list);
            
	        
	        
	    }else{
	        $this->error('订单数据读取错误');
	    }

		$PageTitle = L('编辑订单');
		$PageMenu = array(
				//array( U('order/create'), L('添加订单') ),
				//array( U('order/listing'), L('订单列表') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
		
	}
	
	
    //导出订单
    public function export()
    {
        $CityMod = M('order');
        $toShow['banner'] = $CityMod->where(" status=1 and ordername!=''  " )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "订单ID编号".$expstr."手机"
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
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['ordername']
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

        $CityMod = M('hangye');
        $hangye_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc')->select();
        //echo "<pre>";print_r($hangye_list);exit;
        $this->assign('hangye_list', $hangye_list);



        if(isset($_POST['dosubmit'])){
        //echo "<pre>";print_r($_POST);exit;
	        $orderMod = M('order');
			
			$rst=$this->CheckorderData_Post();
			
			if (false === $orderMod->create()) {
				$this->error($module->getError());
			}
			
	        if($orderMod->create()) {

	        	//echo "<pre>";print_r($orderMod);exit;

	        	//使用 $orderMod->email
        		$rst=$this->CheckorderData_Mod($orderMod);
	        	$orderMod->create_time=time();
	        	$orderMod->password=md5($orderMod->password);
	        	
	        	$result =   $orderMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($orderMod->getError());
	        }
			
		}else{

			$PageTitle = L('添加订单');
			$PageMenu = array(
					array( U('order/listing'), L('订单列表') ),
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


    	///注意：老订单订单已填写情况下不允许修改订单名，订单名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            $rst=$this->CheckorderData_Post($id);

        	///密码为空则不修改密码
        	if( isset($_POST['password'])){
                if($_POST['password'] != '' ){
        		    $_POST['password'] = md5($_POST['password']);
        	    }
        	    else{
                    unset( $_POST['password'] );
        	    }
            }

	        $orderMod = M('order');

	        if($orderMod->create()) {
	        	
                $rst=$this->CheckorderData_Mod($orderMod,$id);
                $orderMod->modify_time=time();

	            //$result =   $orderMod->save();
	            
	            if($result) {
	                $this->success('操作成功！', U('order/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($orderMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $orderMod = M('order');
		    // 读取数据
		    $data =   $orderMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		        
		        
		        
		        /*
		        $orderinfo['id']=$id;
		        //我的好友圈=我的下家
				$db_fuid=array();
				$db_fuid[]=$orderinfo['id'];
				$db_fuid_str=implode(",", $db_fuid);
		        $andsql = " status=1 and ordername!='' ";
		        $andsql .= " and fuid in (".$db_fuid_str.")";
		        
		        $module = M('order');
				$order_arr = $module->where( $andsql )->select();
				$orderinfo_level_01=$order_arr;
				//echo "<pre>";print_r($orderinfo_level_01);exit;
				//echo count($orderinfo_level_01);exit;
		        $this->assign('orderinfo_level_01',  $orderinfo_level_01 );
		        $this->assign('orderinfo_level_01_count',  count($orderinfo_level_01) );
		        
		        
		        
		        
		        //我的朋友圈=我的下家的下家
		        $orderinfo_level_02=array();
		        $db_fuid=array();
		        
		        if(!empty($orderinfo_level_01)){
			        foreach ($orderinfo_level_01 as $k=>$v) {
			        	$db_fuid[]=$v['id'];
			        }
			        
			        $db_fuid_str=implode(",", $db_fuid);
			        $andsql = " status=1 and ordername!='' ";
			        $andsql .= " and fuid in (".$db_fuid_str.")";
			        
			        $module = M('order');
					$order_arr = $module->where( $andsql )->select();
					$orderinfo_level_02=$order_arr;
					
			        
		        }
		        //echo "<pre>";print_r($orderinfo_level_02);exit;
		        $this->assign('orderinfo_level_02',  $orderinfo_level_02 );
		        $this->assign('orderinfo_level_02_count',  count($orderinfo_level_02) );
		        
		        
		        
		        
				//我的人脉圈=我的下家的下家的下家
		        $orderinfo_level_03=array();
		        $db_fuid=array();
		        
		        if(!empty($orderinfo_level_02)){
			        foreach ($orderinfo_level_02 as $k=>$v) {
			        	$db_fuid[]=$v['id'];
			        }
			        
			        $db_fuid_str=implode(",", $db_fuid);
			        $andsql = " status=1 and ordername!='' ";
			        $andsql .= " and fuid in (".$db_fuid_str.")";
			        
			        $module = M('order');
					$order_arr = $module->where( $andsql )->select();
					$orderinfo_level_03=$order_arr;
					
			        
		        }
		        //echo "<pre>";print_r($orderinfo_level_03);exit;
		        $this->assign('orderinfo_level_03',  $orderinfo_level_03 );
		        $this->assign('orderinfo_level_03_count',  count($orderinfo_level_03) );
		        */
		        
		        
		        
		        
		        
		        
		        
		        
		    }else{
		        $this->error('订单数据读取错误');
		    }
    
			$PageTitle = L('编辑订单');
			$PageMenu = array(
					array( U('order/create'), L('添加订单') ),
					array( U('order/listing'), L('订单列表') ),
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
		$module = $orderMod = M('order');
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
        $orderMod = M('order');
        $sql=" id in (".$in.") ";
        $orderMod->where($sql)->delete();

        $this->success('删除成功', U('order/listing'));
    }
*/

	private function CheckorderData_Post($order_id=0){
		///检查 $_POST 提交数据
		
			$orderMod = M('order');

			$result = $orderMod->where("ordername='%s' and id!=%d ", $_POST['ordername'], $order_id )->count();
			if($result>0){
            $this->error(L('存在重复的手机'));
            }
            
			//$result = $orderMod->where("email='%s' and id!=%d ", $_POST['email'], $order_id)->count();
			//if($result>0){
            //$this->error(L('存在重复的邮箱'));
            //}
            
	}
	

	private function CheckorderData_Mod(&$order, $order_id=0){
		///检查 $order 模型数据。$order->email
		
			$orderMod = M('order');
			
			$result = $orderMod->where("ordername='%s' and id!=%d ", $order->ordername, $order_id )->count();
			if($result>0){
            $this->error(L('存在重复的订单名'));
            }
            
			$result = $orderMod->where("email='%s' and id!=%d ", $order->email, $order_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
		
	}
	
	
	
	
	
	
    //导出层级 level01
    public function edit_export_level_01()
    {
        
        $id = intval($this->REQUEST('id'), 0);
        
        
        
        $orderinfo['id']=$id;
        //我的好友圈=我的下家
		$db_fuid=array();
		$db_fuid[]=$orderinfo['id'];
		$db_fuid_str=implode(",", $db_fuid);
        $andsql = " status=1 and ordername!='' ";
        $andsql .= " and fuid in (".$db_fuid_str.")";
        
        $module = M('order');
		$order_arr = $module->where( $andsql )->select();
		$orderinfo_level_01=$order_arr;
		//echo "<pre>";print_r($orderinfo_level_01);exit;
		//echo count($orderinfo_level_01);exit;
        $this->assign('orderinfo_level_01',  $orderinfo_level_01 );
        $this->assign('orderinfo_level_01_count',  count($orderinfo_level_01) );
        
        
        
        $toShow['banner'] = $orderinfo_level_01;
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='order_id_'.$id.'_level_01.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "订单ID编号".$expstr."手机"
            .$expstr."昵称".$expstr."真实姓名".$expstr."上层订单id"
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
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['ordername']
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
        
        
        
		        
		        
        
        $orderinfo['id']=$id;
        //我的好友圈=我的下家
		$db_fuid=array();
		$db_fuid[]=$orderinfo['id'];
		$db_fuid_str=implode(",", $db_fuid);
        $andsql = " status=1 and ordername!='' ";
        $andsql .= " and fuid in (".$db_fuid_str.")";
        
        $module = M('order');
		$order_arr = $module->where( $andsql )->select();
		$orderinfo_level_01=$order_arr;
		//echo "<pre>";print_r($orderinfo_level_01);exit;
		//echo count($orderinfo_level_01);exit;
        $this->assign('orderinfo_level_01',  $orderinfo_level_01 );
        $this->assign('orderinfo_level_01_count',  count($orderinfo_level_01) );
        
        
        
        //我的朋友圈=我的下家的下家
        $orderinfo_level_02=array();
        $db_fuid=array();
        
        if(!empty($orderinfo_level_01)){
	        foreach ($orderinfo_level_01 as $k=>$v) {
	        	$db_fuid[]=$v['id'];
	        }
	        
	        $db_fuid_str=implode(",", $db_fuid);
	        $andsql = " status=1 and ordername!='' ";
	        $andsql .= " and fuid in (".$db_fuid_str.")";
	        
	        $module = M('order');
			$order_arr = $module->where( $andsql )->select();
			$orderinfo_level_02=$order_arr;
			
	        
        }
        //echo "<pre>";print_r($orderinfo_level_02);exit;
        $this->assign('orderinfo_level_02',  $orderinfo_level_02 );
        $this->assign('orderinfo_level_02_count',  count($orderinfo_level_02) );
        
        
        
        
        
        
        
        $toShow['banner'] = $orderinfo_level_02;
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='order_id_'.$id.'_level_02.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "订单ID编号".$expstr."手机"
            .$expstr."昵称".$expstr."真实姓名".$expstr."上层订单id"
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
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['ordername']
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
        
        
        
        $orderinfo['id']=$id;
        //我的好友圈=我的下家
		$db_fuid=array();
		$db_fuid[]=$orderinfo['id'];
		$db_fuid_str=implode(",", $db_fuid);
        $andsql = " status=1 and ordername!='' ";
        $andsql .= " and fuid in (".$db_fuid_str.")";
        
        $module = M('order');
		$order_arr = $module->where( $andsql )->select();
		$orderinfo_level_01=$order_arr;
		//echo "<pre>";print_r($orderinfo_level_01);exit;
		//echo count($orderinfo_level_01);exit;
        $this->assign('orderinfo_level_01',  $orderinfo_level_01 );
        $this->assign('orderinfo_level_01_count',  count($orderinfo_level_01) );
        
        
        
        
        //我的朋友圈=我的下家的下家
        $orderinfo_level_02=array();
        $db_fuid=array();
        
        if(!empty($orderinfo_level_01)){
	        foreach ($orderinfo_level_01 as $k=>$v) {
	        	$db_fuid[]=$v['id'];
	        }
	        
	        $db_fuid_str=implode(",", $db_fuid);
	        $andsql = " status=1 and ordername!='' ";
	        $andsql .= " and fuid in (".$db_fuid_str.")";
	        
	        $module = M('order');
			$order_arr = $module->where( $andsql )->select();
			$orderinfo_level_02=$order_arr;
			
	        
        }
        //echo "<pre>";print_r($orderinfo_level_02);exit;
        $this->assign('orderinfo_level_02',  $orderinfo_level_02 );
        $this->assign('orderinfo_level_02_count',  count($orderinfo_level_02) );
        
        
        
        
		//我的人脉圈=我的下家的下家的下家
        $orderinfo_level_03=array();
        $db_fuid=array();
        
        if(!empty($orderinfo_level_02)){
	        foreach ($orderinfo_level_02 as $k=>$v) {
	        	$db_fuid[]=$v['id'];
	        }
	        
	        $db_fuid_str=implode(",", $db_fuid);
	        $andsql = " status=1 and ordername!='' ";
	        $andsql .= " and fuid in (".$db_fuid_str.")";
	        
	        $module = M('order');
			$order_arr = $module->where( $andsql )->select();
			$orderinfo_level_03=$order_arr;
			
	        
        }
        //echo "<pre>";print_r($orderinfo_level_03);exit;
        $this->assign('orderinfo_level_03',  $orderinfo_level_03 );
        $this->assign('orderinfo_level_03_count',  count($orderinfo_level_03) );
        
        
        
        
        
        $toShow['banner'] = $orderinfo_level_03;
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='order_id_'.$id.'_level_03.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "订单ID编号".$expstr."手机"
            .$expstr."昵称".$expstr."真实姓名".$expstr."上层订单id"
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
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['ordername']
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
    
    
    
    
    
	//导入 订单
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
			
			$orderMod = M('order');
			$table_name=$orderMod->getTableName();
			
			
			
			$order_last = $orderMod->where(" 1 " )->order(' id desc')->limit('0,1')->select();
			if(!empty($order_last)){
				$order_last=$order_last[0];
				$db_id=$order_last['id']+1;
			}
			else{
				$db_id=1;
			}
			$kahao=10000000+$db_id;
			//echo "<pre>";print_r($order_last);exit;
			//echo $kahao;exit;
			
			$book_err_str="";
			
			if($highestRow>=2){
				
				//先删除旧数据
				//$sql=" 1 ";
            	//$orderMod->where($sql)->delete();
				
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
					
		            $result_isset = $orderMod->where(" ordername='".addslashes($strs[0]) ."' " )->select();
		            if(isset($result_isset[0])){
		            	
		            	if(!empty($strs[0])){
		            	//更新
			            	$sql="update ".$table_name." SET 
				              password='".md5($strs[1])."'
				            , realname='".addslashes($strs[2])."'
				            , level='".addslashes($strs[3])."'
				            , addtime='".addslashes($strs[4])."'
				            , create_time='".strtotime($strs[4])."'
				            where ordername='".addslashes($strs[0]) ."' 
				             ";
				            //$result_edit = $orderMod->execute($sql);   //导入的时候遇到重复的qq号不处理，不重复的仍然导入，然后导入完毕后，提示重复的qq号是哪些。
				            $book_err_str=$book_err_str.$strs[0]."<br>";
			            }
			            
		            }
		            else{
		            	
		            	if(!empty($strs[0])){
			            	//新增
			            	$sql="insert into ".$table_name." SET 
			            	  ordername='".addslashes($strs[0])."'
				            , password='".md5($strs[1])."'
				            , realname='".addslashes($strs[2])."'
				            , level='".addslashes($strs[3])."'
				            , addtime='".addslashes($strs[4])."'
				            , create_time='".strtotime($strs[4])."'
				            , id='".addslashes($db_id)."'
				            , kahao='".addslashes($kahao)."'
				             ";
				            $result_add = $orderMod->execute($sql);
				            
				            $db_id=$db_id+1;
				            $kahao=10000000+$db_id;
				            
			            }
		            }
				}
			}
			
			if($book_err_str!=""){
				$book_err_str='存在这些重复的手机号不做处理：<br><br>'.$book_err_str.'<br><br>其他已经成功导入。<br><br><a href="'.U('order/listing_import').'">返回&gt;&gt;</a><br><br>';
				echo $book_err_str;
				exit;
			}
			else{
            	$this->success('导入成功！', U('order/listing_import'));
            	exit;
            }
        }
        
        
        


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		$PageTitle = L('订单导入');
		$PageMenu = array(
			//array( U('order/create'), L('添加订单') ),
            //array( U('order/export'), L('导出订单') ),
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


	

}
?>