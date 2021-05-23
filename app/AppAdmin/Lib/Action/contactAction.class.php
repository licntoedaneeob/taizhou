<?php
/**
 * 简单信息反馈系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class contactAction extends TAction
{
	
	
	
	
	//中奖概率
	public function edit_prize_percent()
	{   
        
        $CityMod_percent = M('weixin_setting');
	    $prize_percent = $CityMod_percent->field('value_s')->where(" key_s='prize_percent'  " )->select();
        if(isset($prize_percent[0]['value_s']) && $prize_percent[0]['value_s']!=''){
	    	$prize_percent=$prize_percent[0]['value_s'];
		}
		else{
			$prize_percent=0;
		}
        $this->assign('prize_percent', $prize_percent);
		
		
        $CityMod_percent = M('weixin_setting');
	    $prize_open_time = $CityMod_percent->field('value_s')->where(" key_s='prize_open_time'  " )->select();
        if(isset($prize_open_time[0]['value_s'])){
	    	$prize_open_time=$prize_open_time[0]['value_s'];
	    	$prize_open_time=date('Y-m-d H:i:s',$prize_open_time);
		}
		else{
			$prize_open_time=0;
		}
        $this->assign('prize_open_time', $prize_open_time);
		
		

    	///注意：老参与者参与者已填写情况下不允许修改参与者名，参与者名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$value_s = addslashes($this->REQUEST('title'));
			$prize_open_time = addslashes($this->REQUEST('prize_open_time'));
			//echo $value_s;exit;
            
            if($value_s>=0 && $value_s<=100){
            	
            	$UserMod = M('weixin_setting');
		        $sql=sprintf("UPDATE %s SET value_s='".$value_s."' 
		        where key_s='prize_percent' 
		        ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
		        $UserMod = M('weixin_setting');
		        $sql=sprintf("UPDATE %s SET value_s='".strtotime($prize_open_time)."' 
		        where key_s='prize_open_time' 
		        ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
		        $this->success('操作成功！', U('contact/edit_prize_percent', array('id'=>'prize_percent')) );
            }
            else{
		        $this->error('您的输入有误！');
	        }
	        
		}
		else{
			
			$PageTitle = L('中奖概率');
			$PageMenu = array(
					//array( U('contact/create'), L('添加参与者') ),
					//array( U('contact/listing'), L('参与者列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
	}
	
	
	
	
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
            $contactMod = M('contact');
            $sql=" id in (".$in.") ";
            $contactMod->where($sql)->delete();

            $this->success('删除成功', U('contact/listing'));
            exit;
        }
        


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		//$sqlWhere .= " and mobile!='' and score_jia!='' and wx_headpic_path!='' ";
		//$sqlWhere .= " and is_prize>=0 and openid!=''  ";
		//$sqlWhere .= " and user_id_crm!='' ";
		$sqlOrder = " id DESC";
		
		//$sqlWhere .= " and (headpic!='' or  voice!='') and mobile!='' ";
		
		
		$s_time=time()-(SHENHE_CROSS_TIME*3600);   //超过4小时自动审核通过
		
		

		$filter_role = $this->REQUEST('_filter_role');
		if( $filter_role != '' ){
			$sqlWhere .= " and role = '". $this->fixSQL($filter_role)."' ";
		}

		$filter_state = $this->REQUEST('_filter_state');
		if( $filter_state != '' ){
			
			
			
			if ($filter_state=='0'){
				$sqlWhere .= " and status = ". intval($filter_state)."  ";
			}
			if ($filter_state=='1'){
				$sqlWhere .= " and status = ". intval($filter_state)." and create_time<'".$s_time."' ";
			}
			if ($filter_state=='1_4h'){
				$sqlWhere .= " and status = ". intval($filter_state)." and create_time>='".$s_time."' ";
			}
			
		}

		
        
        //新搜索
        $f_search = $this->REQUEST('_f_search');
        $filter_fieldname = $this->REQUEST('_filter_fieldname');
		if( $f_search != '' ){
			if($filter_fieldname=='realname'){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='mobile'){
				$sqlWhere .= " and (mobile like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='summary'){
				$sqlWhere .= " and (summary like '%". $this->fixSQL($f_search)."%') ";
			}
			//elseif($filter_fieldname=='wx_nickname'){
			//	$sqlWhere .= " and (wx_nickname like '%". $this->fixSQL($f_search)."%') ";
			//}
			//elseif($filter_fieldname=='openid'){
			//	$sqlWhere .= " and (openid like '%". $this->fixSQL($f_search)."%') ";
			//}
			//elseif($filter_fieldname=='user_id_crm'){
			//	$sqlWhere .= " and (user_id_crm like '%". $this->fixSQL($f_search)."%') ";
			//}
			//elseif($filter_fieldname=='username_crm'){
			//	$sqlWhere .= " and (username_crm like '%". $this->fixSQL($f_search)."%') ";
			//}
			//elseif($filter_fieldname=='realname_crm'){
			//	$sqlWhere .= " and (realname_crm like '%". $this->fixSQL($f_search)."%') ";
			//}
			//elseif($filter_fieldname=='is_prize'){
			//	$sqlWhere .= " and (is_prize = '". $this->fixSQL($f_search)."') ";
			//}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or mobile like '%". $this->fixSQL($f_search)."%' or summary like '%". $this->fixSQL($f_search)."%'    )";
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
		
		
		//echo $sqlWhere;exit;
		
		
		
		

        $this->ModManager = M('contact');
        $f_order = $this->REQUEST('_f_order', 'modify_time');
        $fields = $this->ModManager->getDbFields();
        if( in_array($f_order, $fields) ){
            $sqlOrder = $f_order . ' ';
        }else{
            $sqlOrder = 'modify_time ';
        }
        
        $f_direc = strtoupper($this->REQUEST('_f_direc'));if($f_direc==""){$f_direc='DESC';}
        if( $f_direc != 'DESC' ){
            $sqlOrder .= 'ASC';
        }else{
            $sqlOrder .= 'DESC';
        }
        
        $sqlOrder = ' id DESC ';
        
        //echo $sqlOrder;exit;

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
		if(isset($_POST['do_search']) && $_POST['do_search']==1){
			$paginglimit=1000000;
		}
		else{
			$paginglimit='';
        }
        //$rst=$this->GeneralActionForListing('contact', $sqlWhere, $sqlOrder, '', 'M');
        $rst=$this->GeneralActionForListing('contact', $sqlWhere, $sqlOrder, $paginglimit, 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		//过滤搜索条件
		//$UserinfoMod = M('user');
		
        //if(isset($rst['dataset'])){
        //    foreach($rst['dataset'] as $k => $v){
				
        		/*
        		$user = $UserinfoMod->field('id,username,realname,point_total')->where(" id='".$v['user_id']."' " )->select();
        		$user=$user[0];
        		
        		$rst['dataset'][$k]['user_id'] = $user['id'];
            	$rst['dataset'][$k]['username'] = $user['username'];
                $rst['dataset'][$k]['realname'] = $user['realname'];
                $rst['dataset'][$k]['point_total'] = $user['point_total'];
                */
                
                
                /*
                if($v['status']==0){
                	$rst['dataset'][$k]['status_now']='审核拒绝';
                }
                if($v['status']==1){
                	if($v['create_time']<$s_time){
                		$rst['dataset'][$k]['status_now']='审核通过';
                	}
                	if($v['create_time']>=$s_time){
                		$rst['dataset'][$k]['status_now']='审核中';
                	}
                }
                */
                
                //$user = $UserinfoMod->field('id,province,city')->where(" id='".$v['user_id']."' " )->select();
        		//$user=$user[0];
        		
        //		$rst['dataset'][$k]['user_province'] = $user['province'];
          //  	$rst['dataset'][$k]['user_city'] = $user['city'];
                
                
                
        //    }
        //    $this->assign('dataset', $rst['dataset']);// 赋值数据集
        //}
        //echo "<pre>";print_r($rst['dataset']);exit;
		
		$this->assign('dataset', $rst['dataset']);// 赋值数据集
		
		
		
        if(isset($_POST['do_search']) && $_POST['do_search']==1){
        	//echo "export";exit;
        	
        	$toShow['banner']=$rst['dataset'];
        	
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= "流水ID编号"
        	.$expstr."提交时间"
            .$expstr."姓名"
            .$expstr."性别(1男2女)"
            .$expstr."联系电话"
            .$expstr."电子邮箱"
            .$expstr."反馈标题"
            .$expstr."反馈内容"
            .$expenter;

        	
        	$UserinfoMod = M('user');
        	
	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
	                $v=$toShow['banner'][$k];
	                
	                
	                //if(isset($allProductList[$v['class_id']])){
	                //    $toShow['banner'][$k]['class_name']=$allProductList[$v['class_id']]['title'];
	                //}
	                //else{
	                //    $toShow['banner'][$k]['class_name']="";
	                //}
	                
	                
	                
	                //$myword=$toShow['banner'][$k]['myword'];
	                //$myword=str_replace("\r\n"," [Enter] ",$myword);
	                //$myword=str_replace("\n"," [Enter] ",$myword);
	                //$myword=str_replace("\r","",$myword);
					
					
	                //if ($toShow['banner'][$k]['device']=="0"){
	                //    $device_show="PC端";
	                //}
	                //if ($toShow['banner'][$k]['device']=="1"){
	                //    $device_show="手机端";
	                //}
	                
	                
	                //$headpic=empty($v['headpic'])?"":BASE_URL_FRONT."/public/web_headpic/".$v['headpic'];
	                //$voice=empty($v['voice'])?"":BASE_URL_FRONT."/public/web_voice/".$v['voice'];
	                //$wx_headpic=empty($v['wx_headpic_path'])?"":BASE_URL_FRONT."/public/wx_headpic/".$v['wx_headpic_path'];
	                
	                
	                
	                /*
	                if($v['status']==0){
	                	$status_now='审核拒绝';
	                }
	                if($v['status']==1){
	                	if($v['create_time']<$s_time){
	                		$status_now='审核通过';
	                	}
	                	if($v['create_time']>=$s_time){
	                		$status_now='审核中';
	                	}
	                }
	                */
	                
	                
	                
	                $summary=str_replace("\r\n"," [Enter] ",$toShow['banner'][$k]['summary']);
	                $summary=str_replace("\n"," [Enter] ",$summary);
	                $summary=str_replace("\r","",$summary);
	                
	                
	                
	                
	                //$headpic=empty($v['headpic'])?"":BASE_URL_FRONT."/public/wx_headpic/".$v['wx_headpic_path'];
	                
	                
	                
	                //$user = $UserinfoMod->field('id,province,city')->where(" id='".$v['user_id']."' " )->select();
	        		//$user=$user[0];
	        		
		        	//	$user_province = $user['province'];
		            //	$user_city = $user['city'];
	                
	                

	                    $output .= $toShow['banner'][$k]['id']
	                    	.$expstr.$toShow['banner'][$k]['addtime']
	                    	.$expstr.$toShow['banner'][$k]['realname']
	                    	.$expstr.$toShow['banner'][$k]['sex']
	                    	.$expstr.$toShow['banner'][$k]['mobile']
	                    	.$expstr.$toShow['banner'][$k]['email']
	                    	.$expstr.$toShow['banner'][$k]['title']
	                    	.$expstr.$summary
	                        .$expenter;


	                $k=$k+1;
	            }while($k<count($toShow['banner']));
	        }

	        $T_text=$output;

			//exit;


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
		
		
		$PageTitle = L('信息反馈列表');
		$PageMenu = array(
			//array( U('contact/create'), L('信息反馈') ),
            //array( U('contact/export'), L('导出信息反馈') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}

    //导出信息反馈
    public function export()
    {exit;
        $CityMod = M('contact');
        $toShow['banner'] = $CityMod->where(" status=1 " )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "信息反馈ID编号".$expstr."昵称"
            .$expstr."职业".$expstr."菜系"
            .$expstr."为谁做这道菜".$expstr."为谁做菜提交时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
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
				
                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['contactname']
                    .$expstr.$toShow['banner'][$k]['nickname'].$expstr.$toShow['banner'][$k]['realname']
                    .$expstr.$gender_show.$expstr.$toShow['banner'][$k]['birth_year']
                    .$expstr.$mobile_show.$expstr.$toShow['banner'][$k]['hangye']
                    .$expstr.$toShow['banner'][$k]['address'].$expstr.$is_agree_show
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
	        $contactMod = M('contact');
			
			$rst=$this->CheckcontactData_Post();
			
			if (false === $contactMod->create()) {
				$this->error($module->getError());
			}
			
	        if($contactMod->create()) {

	        	//echo "<pre>";print_r($contactMod);exit;

	        	//使用 $contactMod->email
        		$rst=$this->CheckcontactData_Mod($contactMod);
	        	$contactMod->create_time=time();
	        	$contactMod->password=md5($contactMod->password);
	        	
	        	$result =   $contactMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($contactMod->getError());
	        }
			
		}else{

			$PageTitle = L('信息反馈');
			$PageMenu = array(
					array( U('contact/listing'), L('信息反馈列表') ),
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
        //$CityMod = M('hangye');
        //$hangye_list = $CityMod->field('id,title')->where(" status=1 " )->order('sort asc')->select();
        //echo "<pre>";print_r($hangye_list);exit;
       // $this->assign('hangye_list', $hangye_list);


    	///注意：老信息反馈信息反馈已填写情况下不允许修改信息反馈名，信息反馈名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

           // $rst=$this->CheckcontactData_Post($id);

/*
        	///密码为空则不修改密码
        	if( isset($_POST['password'])){
                if($_POST['password'] != '' ){
        		    $_POST['password'] = md5($_POST['password']);
        	    }
        	    else{
                    unset( $_POST['password'] );
        	    }
            }*/

	        $contactMod = M('contact');

	        if($contactMod->create()) {
	        	
                //$rst=$this->CheckcontactData_Mod($contactMod,$id);
                $contactMod->modify_time=time();

	            $result =   $contactMod->save();
	            if($result) {
	                $this->success('操作成功！', U('contact/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($contactMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $contactMod = M('contact');
		    // 读取数据
		    $data =   $contactMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('信息反馈读取错误');
		    }
    
			$PageTitle = L('编辑信息反馈');
			$PageMenu = array(
					//array( U('contact/create'), L('信息反馈') ),
					array( U('contact/listing'), L('信息反馈列表') ),
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
		$module = $contactMod = M('contact');
		$id 	= intval($_REQUEST['id']);
		$user_id 	= intval($_REQUEST['user_id']);
		//$type 	= isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'status';
		$status = $type=($type+1)%2;
	    
	    
	    
        
        
	    
	    
		$result = $module->execute( sprintf("UPDATE %s SET status=(status+1)%%2 where id=%d", $module->getTableName(), $id) );
		$values = $module->where('id=%d', $id)->find();
		
		//print_r($values);exit;
		
		//扣除幸福值
        if( $values['status']==1 ){
        	$point='5';
        }
        else{
        	$point='-5';
        }
        
        $UserMod = M('user_point');
        $sql=sprintf("INSERT %s SET point='".$point."' 
        , source='admin_contact' 
        , user_id='".addslashes($user_id)."' 
        , create_time='".time()."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        $CityMod = M('user_point');
        $point_total = $CityMod->field('sum(point) as point_total , user_id')->where(" user_id='".addslashes($user_id)."' " )->select();
        $point_total=isset($point_total[0]['point_total'])?$point_total[0]['point_total']:0;
        
        
        $UserMod = M('user');
        $sql=sprintf("UPDATE %s SET point_total='".$point_total."' 
        where id='".addslashes($user_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        //扣除幸福值
        
        
		
		if( is_null($values) || $values === false ){
			$this->ajaxReturn(array('status' => 'false'));
		}else{
			$this->ajaxReturn(array('status' => 'true', 'result' => $values['status']));
		}
	}




//设为审核通过
	public function ajax_change_status_set_yes()
	{
		$module = $contactMod = M('contact');
		$id 	= intval($_REQUEST['id']);
		$user_id 	= intval($_REQUEST['user_id']);
		$status = $type=($type+1)%2;
	    
	    
	    $min_time=(SHENHE_CROSS_TIME*3600);
		//echo sprintf("UPDATE %s SET status=1 , create_time=create_time-".$min_time." where id=%d", $module->getTableName(), $id);exit;
		$result = $module->execute( sprintf("UPDATE %s SET status=1 , create_time=create_time-".$min_time." where id=%d", $module->getTableName(), $id) );
		
		$this->ajaxReturn(array('status' => 'true'));
		
		
	}
	
	
	
//设为审核拒绝
	public function ajax_change_status_set_no()
	{
		$module = $contactMod = M('contact');
		$id 	= intval($_REQUEST['id']);
		$user_id 	= intval($_REQUEST['user_id']);
		$status = $type=($type+1)%2;
	    
	    
	    $min_time=(SHENHE_CROSS_TIME*3600);
		//echo sprintf("UPDATE %s SET status=1 , create_time=create_time-".$min_time." where id=%d", $module->getTableName(), $id);exit;
		$result = $module->execute( sprintf("UPDATE %s SET status=0 where id=%d", $module->getTableName(), $id) );
		
		$this->ajaxReturn(array('status' => 'true'));
		
		
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
        $contactMod = M('contact');
        $sql=" id in (".$in.") ";
        $contactMod->where($sql)->delete();

        $this->success('删除成功', U('contact/listing'));
    }
*/

	private function CheckcontactData_Post($contact_id=0){
		///检查 $_POST 提交数据
		
			$contactMod = M('contact');

			$result = $contactMod->where("contactname='%s' and id!=%d ", $_POST['contactname'], $contact_id )->count();
			if($result>0){
            $this->error(L('存在重复的信息反馈名'));
            }
            
			$result = $contactMod->where("email='%s' and id!=%d ", $_POST['email'], $contact_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
            
	}
	

	private function CheckcontactData_Mod(&$contact, $contact_id=0){
		///检查 $contact 模型数据。$contact->email
		
			$contactMod = M('contact');
			
			$result = $contactMod->where("contactname='%s' and id!=%d ", $contact->contactname, $contact_id )->count();
			if($result>0){
            $this->error(L('存在重复的信息反馈名'));
            }
            
			$result = $contactMod->where("email='%s' and id!=%d ", $contact->email, $contact_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
		
	}
	
	
	
    //导出活动预约数据
    public function export_attend()
    {
    	
    	//echo "a";exit;
        //$product_list_id=$_GET['product_list_id'];
        
        
	
		
		
		
        $s_time=time()-(SHENHE_CROSS_TIME*3600);   //超过4小时自动审核通过
		
		$andsql="";
        //$andsql .= " and mobile!='' and score_jia!='' and wx_headpic_path!='' ";
        $andsql .= " and is_prize>=0 and openid!=''  ";
        $andsql .= " and user_id_crm!='' ";
        
		//$andsql = " and (headpic!='' or voice!='') and mobile!='' ";
		
		
        //if(!empty($product_list_id)){
        //	$andsql=$andsql." and class_id=".addslashes($product_list_id);
        //}
        
        
		
		
        $CityMod = M('contact');
        $toShow['banner'] = $CityMod->where(" status < 250 ".$andsql )->order('id desc')->select();
        //$toShow['banner'] = $CityMod->where(" status < 250 ".$andsql )->group('openid')->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;
		
		
		
		//$CityMod = M('pc_product_list');
		//$product_list_info =   $CityMod->find($product_list_id);
		//echo "<pre>";print_r($company_event_info);exit;
		
		
		
		/*
		//所有项目
		$CityMod = M('pc_product_list');
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
        */
        
        
		
		
        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "活动ID编号"
        	.$expstr."提交时间"
        	//.$expstr."姓名"
        	//.$expstr."手机"
            //.$expstr."地址"
            .$expstr."经销商ID"
            .$expstr."App账号"
            .$expstr."经销商姓名"
            .$expstr."奖品编号"
           // .$expstr."分数"  //真分数  //.$expstr."假分数"
            //.$expstr."百分比"
            //.$expstr."省份"
            //.$expstr."城市"
            .$expstr."openid"
            .$expstr."微信昵称"
            //.$expstr."上传照片"
            //.$expstr."图片"
            //.$expstr."微信昵称"
            //.$expstr."微信头像"
            //.$expstr."openid"
            //.$expstr."分享过(1是，0否)"
            //.$expstr."分享被多少人点击"
            //.$expstr."微信openid"
            //.$expstr."微信昵称"
            //.$expstr."微信头像"
            .$expenter;

        
        $UserinfoMod = M('user');
        
        if (!empty($toShow['banner'])){
            $k=0;
            do{
                
                
                $v=$toShow['banner'][$k];
                
                
                //if(isset($allProductList[$v['class_id']])){
                //    $toShow['banner'][$k]['class_name']=$allProductList[$v['class_id']]['title'];
                //}
                //else{
                //    $toShow['banner'][$k]['class_name']="";
                //}
                
                
                
                //$myword=$toShow['banner'][$k]['myword'];
                //$myword=str_replace("\r\n"," [Enter] ",$myword);
                //$myword=str_replace("\n"," [Enter] ",$myword);
                //$myword=str_replace("\r","",$myword);
				
				
                //if ($toShow['banner'][$k]['device']=="0"){
                //    $device_show="PC端";
                //}
                //if ($toShow['banner'][$k]['device']=="1"){
                //    $device_show="手机端";
                //}
                
                
                //$headpic=empty($v['headpic'])?"":BASE_URL_FRONT."/public/web_headpic/".$v['headpic'];
                //$voice=empty($v['voice'])?"":BASE_URL_FRONT."/public/web_voice/".$v['voice'];
                //$wx_headpic=empty($v['wx_headpic_path'])?"":BASE_URL_FRONT."/public/wx_headpic/".$v['wx_headpic_path'];
                
                
                
                /*
                if($v['status']==0){
                	$status_now='审核拒绝';
                }
                if($v['status']==1){
                	if($v['create_time']<$s_time){
                		$status_now='审核通过';
                	}
                	if($v['create_time']>=$s_time){
                		$status_now='审核中';
                	}
                }
                */
                
                
                
                
                
                
                
                /*
                //制造假数据
                
                //echo md5($toShow['banner'][$k]['id']);echo '<br>';
                //echo '<br>';
                
                $need_id=$toShow['banner'][$k]['id'];
                
                $first_char=substr(md5($need_id),9,1);
                $first_char_ASCII=ord($first_char);
                
                
                $is_fenxiang_number=0;  //分享被多少人点击
                
                //echo $first_char_ASCII;echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                
                
                
                if($first_char_ASCII>=53 && $first_char_ASCII<=60 ){
                	$is_fenxiang=substr($first_char_ASCII,1,1);
                }
                else{
                	$is_fenxiang=0;
                }
                
                
                
                
                if($is_fenxiang>0){
                	$is_fenxiang_ture=1;  //分享过
                	
	                $pe_char=substr(md5($need_id),12,1);
	                $pe_char_ASCII=ord($pe_char);
	                
	                if($pe_char_ASCII>=30 && $pe_char_ASCII<=60 ){
	                	$pe_value=substr($first_char_ASCII,1,1);
	                }
	                else{
	                	$pe_value=0;
	                }
	                
	                
	                if($pe_value==7){
	                	
	                	$pe_char2=substr(md5($need_id),13,1);
	                	$pe_char2_ASCII=ord($pe_char2);
	                	
	                	if($pe_char2_ASCII>=49 && $pe_char2_ASCII<=50 ){
		                	$pe_value=8;
		                }
		                if($pe_char2_ASCII>=51 && $pe_char2_ASCII<=52 ){
		                	$pe_value=9;
		                }
		                if($pe_char2_ASCII>=53 && $pe_char2_ASCII<=60 ){
		                	$pe_value=10;
		                }
		                
	                
	                }
	                
	                
	                
	                if($pe_value==3){
	                	
	                	$pe_char2=substr(md5($need_id),13,1);
	                	$pe_char2_ASCII=ord($pe_char2);
	                	
	                	
		                if($pe_char2_ASCII>=50 && $pe_char2_ASCII<=52 ){
		                	$pe_value=2;
		                }
		                if($pe_char2_ASCII>=53 && $pe_char2_ASCII<=60 ){
		                	$pe_value=1;
		                }
		                
	                
	                }
	                
	                
	                
	                
	                if($pe_value==10){
	                	
	                	$pe_char2=substr(md5($need_id),14,1);
	                	$pe_char2_ASCII=ord($pe_char2);
	                	//echo $pe_char2_ASCII;echo "<br>";
	                	
		                if($pe_char2_ASCII==49 ){
		                	$pe_value=11;
		                }
		                if($pe_char2_ASCII==50 ){
		                	$pe_value=12;
		                }
		                if($pe_char2_ASCII==51 ){
		                	$pe_value=13;
		                }
		                if($pe_char2_ASCII==52 ){
		                	$pe_value=14;
		                }
		                if($pe_char2_ASCII==53 ){
		                	$pe_value=15;
		                }
		                if($pe_char2_ASCII==55 ){
		                	$pe_value=16;
		                }
		                if($pe_char2_ASCII==101 ){
		                	$pe_value=17;
		                }
		                if($pe_char2_ASCII==102 ){
		                	$pe_value=18;
		                }
		                
	                }
	                
	                
	                
	                $is_fenxiang_number=$pe_value;  //分享被多少人点击
                	
                }
                else{
                	$is_fenxiang_ture=0;  //没分享过
                	$is_fenxiang_number=0;  //分享被多少人点击
                }
                
                
                if($toShow['banner'][$k]['realname']=='尚洁' || $toShow['banner'][$k]['openid']=='oYr8buM6z01pMQ0t-lGm8S6EDiwU'){
                	$is_fenxiang_ture=1;  //分享过
                	$is_fenxiang_number=9;   //分享被多少人点击
                }
                
                
                
                //echo $is_fenxiang;echo '<br>';
                
                */
                
                /*
                $title=str_replace("\r\n"," [Enter] ",$toShow['banner'][$k]['title']);
                $title=str_replace("\n"," [Enter] ",$title);
                $title=str_replace("\r","",$title);
                */
                
                
                
                //$headpic=empty($v['headpic'])?"":BASE_URL_FRONT."/public/wx_headpic/".$v['wx_headpic_path'];
                
                
                
                //$user = $UserinfoMod->field('id,province,city')->where(" id='".$v['user_id']."' " )->select();
        		//$user=$user[0];
        		
        	//	$user_province = $user['province'];
            //	$user_city = $user['city'];
                
                
                





                    $output .= $toShow['banner'][$k]['id']
                    	.$expstr.$toShow['banner'][$k]['addtime']
                    	//.$expstr.$toShow['banner'][$k]['realname']
                    	//.$expstr.$toShow['banner'][$k]['mobile']
                    	//.$expstr.$toShow['banner'][$k]['address']
                    	.$expstr.$toShow['banner'][$k]['user_id_crm']
                    	.$expstr.$toShow['banner'][$k]['username_crm']
                    	.$expstr.$toShow['banner'][$k]['realname_crm']
                    	.$expstr.$toShow['banner'][$k]['is_prize']
                    	//.$expstr.$toShow['banner'][$k]['score_jia']   //.$expstr.$toShow['banner'][$k]['score']
                    	//.$expstr.$toShow['banner'][$k]['percent']
                    	//.$expstr.$toShow['banner'][$k]['wx_province']
                    	//.$expstr.$toShow['banner'][$k]['wx_city']
                    	.$expstr.$toShow['banner'][$k]['openid']
                    	.$expstr.$toShow['banner'][$k]['wx_nickname']
                    	//.$expstr.$headpic
                    	
                    	//.$expstr.$toShow['banner'][$k]['wx_nickname']
                    	//.$expstr.$toShow['banner'][$k]['wx_headimgurl']
                    	//.$expstr.$toShow['banner'][$k]['openid']
                    	//.$expstr.$is_fenxiang_ture
                    	//.$expstr.$is_fenxiang_number
                    	//.$expstr.$toShow['banner'][$k]['openid']
                    	//.$expstr.$myword
                    	//.$expstr.$headpic
                    	//.$expstr.$voice
                    	//.$expstr.$toShow['banner'][$k]['wx_nickname']
                    	//.$expstr.$wx_headpic
                    	//.$expstr.$status_now
                        .$expenter;


                $k=$k+1;
            }while($k<count($toShow['banner']));
        }

        $T_text=$output;

		//exit;


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