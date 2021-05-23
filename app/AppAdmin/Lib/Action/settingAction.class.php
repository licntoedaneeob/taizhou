<?php
/**
 * 简单参与者系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class settingAction extends TAction
{

	
	
	
	//修改全局配置 - 人数路线数
	public function edit()
	{   
        
        $CityMod_percent = M('setting');
	    $index_person_number = $CityMod_percent->field('value_s')->where(" key_s='index_person_number'  " )->select();
        if(isset($index_person_number[0]['value_s'])){
	    	$index_person_number=$index_person_number[0]['value_s'];
		}
		else{
			$index_person_number=0;
		}
        $this->assign('index_person_number', $index_person_number);
		
		
        $CityMod_percent = M('setting');
	    $index_product_number = $CityMod_percent->field('value_s')->where(" key_s='index_product_number'  " )->select();
        if(isset($index_product_number[0]['value_s'])){
	    	$index_product_number=$index_product_number[0]['value_s'];
		}
		else{
			$index_product_number=0;
		}
        $this->assign('index_product_number', $index_product_number);
		
		

    	///注意：老参与者参与者已填写情况下不允许修改参与者名，参与者名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$index_person_number = addslashes($this->REQUEST('index_person_number'));
			$index_product_number = addslashes($this->REQUEST('index_product_number'));
			//echo $value_s;exit;
            
            
        	
        	$UserMod = M('setting');
	        $sql=sprintf("UPDATE %s SET value_s='".$index_person_number."' 
	        where key_s='index_person_number' 
	        ", $UserMod->getTableName() );
	        $result = $UserMod->execute($sql);
	        
	        $UserMod = M('setting');
	        $sql=sprintf("UPDATE %s SET value_s='".$index_product_number."' 
	        where key_s='index_product_number' 
	        ", $UserMod->getTableName() );
	        $result = $UserMod->execute($sql);
	        
	        $this->success('操作成功！', U('setting/edit', array('id'=>'setting')) );
        	
	        
		}
		else{
			
			$PageTitle = L('人数路线数');
			$PageMenu = array(
					//array( U('game/create'), L('添加参与者') ),
					//array( U('game/listing'), L('参与者列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
	}
	
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * 注册用户 
	 *--------------------------------------------------------------+
	 */
	public function listing_user()
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
            $gameMod = M('game');
            $sql=" id in (".$in.") ";
            $gameMod->where($sql)->delete();

            $this->success('删除成功', U('game/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		
		$sqlWhere .= " and headpic!='' ";
		
		
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
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
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
		
		
		
		

        $this->ModManager = M('game');
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
		//$rst=$this->GeneralActionForListing('game', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		$sqlWhere=' 1 ';
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and ouliwei_user.create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and ouliwei_user.create_time < ". $sql_endtime." ";
		}
		
		//$sqlWhere.= " and ouliwei_prize_history.user_id=ouliwei_user.id ";
		//$sqlWhere.= " and ouliwei_prize_history.survey_mobile!='' ";
		//$sqlWhere.= " and (ouliwei_prize_history.prize_type=15 or ouliwei_prize_history.prize_type=20) ";
		//$sqlWhere.= " and bag_address!='' ";
		$sql='select ouliwei_user.id 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			, ouliwei_user.qr 
			, ouliwei_user.create_time 
			from ouliwei_user 
			where '.$sqlWhere.' 
			order by ouliwei_user.create_time desc
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$toShow['banner'] = empty($rst)?array():$rst;
		
		
		if(isset($_POST['action_export']) && $_POST['action_export']=='yes'){
			
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= 
	        	//"编号ID".$expstr.
	        	"创建时间"
	            .$expstr."注册用户手机"
	            .$expstr."注册用户姓名"
	            //.$expstr."是否来源于扫码（1是，0不是）"
	            .$expenter;

	        header('Cache-control: private');
	        header('Content-Disposition: attachment; filename='.$downloadfilename);

	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
	                //$summary=$toShow['banner'][$k]['summary'];
	                //$summary=str_replace("\r\n"," [Enter] ",$summary);
	                //$summary=str_replace("\n"," [Enter] ",$summary);
	                //$summary=str_replace("\r","",$summary);
					
					$userinfo=$toShow['banner'][$k];
					
					//$userinfo['headpic']=empty($userinfo['headpic'])?"":BASE_URL."/public/web_pic/".$userinfo['headpic'];
		        	//$userinfo['headegg']=empty($userinfo['headegg'])?"":BASE_URL."/public/web_resize/".$userinfo['headegg'];
		        	//$userinfo['gamefilter']=empty($userinfo['gamefilter'])?"":BASE_URL."/public/web_filter/".$userinfo['gamefilter'];
		        	
		        	


	                    $output .= 
	                    	//$userinfo['id'].$expstr.
	                    	date('Y-m-d H:i:s',$userinfo['create_time'])
	                        .$expstr.$userinfo['username']
	                        .$expstr.$userinfo['realname']
	                        //.$expstr.$userinfo['qr']
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
        
        
        
		
		$PageTitle = L('注册用户');
		$PageMenu = array(
			//array( U('game/create'), L('添加参与者') ),
            //array( U('game/export'), L('导出参与者') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * 问卷调查
	 *--------------------------------------------------------------+
	 */
	public function listing_survey()
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
            $gameMod = M('game');
            $sql=" id in (".$in.") ";
            $gameMod->where($sql)->delete();

            $this->success('删除成功', U('game/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		
		$sqlWhere .= " and headpic!='' ";
		
		
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
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
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
		
		
		
		

        $this->ModManager = M('game');
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
		//$rst=$this->GeneralActionForListing('game', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		$sqlWhere=' 1 ';
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and ouliwei_prize_history.create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and ouliwei_prize_history.create_time < ". $sql_endtime." ";
		}
		
		
		$sqlWhere.= " and ouliwei_prize_history.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_prize_history.survey_1!='' ";
		//$sqlWhere.= " and ouliwei_game1.user_id=ouliwei_user.id ";
		//$sqlWhere.= " and ouliwei_game1.status=1 ";
		
		//$sqlWhere.= " and (ouliwei_prize_history.prize_type=15 or ouliwei_prize_history.prize_type=20) ";
		//$sqlWhere.= " and bag_address!='' ";
		$sql='select ouliwei_prize_history.* 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			from ouliwei_prize_history , ouliwei_user 
			where '.$sqlWhere.' 
			order by ouliwei_user.create_time desc
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$toShow['banner'] = empty($rst)?array():$rst;
		
		
		if(isset($_POST['action_export']) && $_POST['action_export']=='yes'){
			
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= 
	        	//"编号ID".$expstr.
	        	"问题1"
	        	.$expstr."问题2"
	        	.$expstr."问题3"
	        	.$expstr."问题4"
	        	.$expstr."问题5"
	        	.$expstr."问题6"
	        	.$expstr."问题7"
	        	.$expstr."问题8"
	        	.$expstr."问题9"
	        	.$expstr."问题10"
	        	.$expstr."问题11"
	        	.$expstr."问题12"
	        	.$expstr."问题13"
	        	.$expstr."问题14"
	        	.$expstr."问题15"
	        	.$expstr."问卷调查联系地址"
	        	.$expstr."问卷调查真实姓名"
	        	.$expstr."创建时间"
	            .$expstr."注册用户手机"
	            .$expstr."注册用户姓名"
	            .$expstr."全家福文件名"
	            .$expstr."全家福url"
	            .$expenter;

	        header('Cache-control: private');
	        header('Content-Disposition: attachment; filename='.$downloadfilename);

	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
					$userinfo=$toShow['banner'][$k];
					
		        	
		        	
		        	$sql='select ouliwei_game1.headegg 
					from ouliwei_game1 
					where user_id="'.$userinfo['user_id'].'" and ouliwei_game1.status=1 
					limit 1
					';
					$user_game1 = $Dao->query( $sql );
					//echo "<pre>";print_r($user_game1);
		        	
		        	
		        	if(isset($user_game1[0]['headegg'])){
		        		$userinfo['headegg']=$user_game1[0]['headegg'];
		        		$headegg_url='http://'.$_SERVER["HTTP_HOST"].'/cms/public/web_resize/'.$userinfo['headegg'];
					}
					else{
						$userinfo['headegg']='';
						$headegg_url='';
					}



	                    $output .= 
	                    	//$userinfo['id'].$expstr.
	                    	$userinfo['survey_1']
	                    	.$expstr.$userinfo['survey_2']
	                    	.$expstr.$userinfo['survey_3']
	                    	.$expstr.$userinfo['survey_4']
	                    	.$expstr.$userinfo['survey_5']
	                    	.$expstr.$userinfo['survey_6']
	                    	.$expstr.$userinfo['survey_7']
	                    	.$expstr.$userinfo['survey_8']
	                    	.$expstr.$userinfo['survey_9']
	                    	.$expstr.$userinfo['survey_10']
	                    	.$expstr.$userinfo['survey_11']
	                    	.$expstr.$userinfo['survey_12']
	                    	.$expstr.$userinfo['survey_13']
	                    	.$expstr.$userinfo['survey_14']
	                    	.$expstr.$userinfo['survey_15']
	                    	.$expstr.$userinfo['survey_address']
	                    	.$expstr.$userinfo['survey_mobile']
	                        .$expstr.date('Y-m-d H:i:s',$userinfo['create_time'])
	                        .$expstr.$userinfo['username']
	                        .$expstr.$userinfo['realname']
	                        .$expstr.$userinfo['headegg']
	                        .$expstr.$headegg_url
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
        
        
        
		
		$PageTitle = L('问卷调查');
		$PageMenu = array(
			//array( U('game/create'), L('添加参与者') ),
            //array( U('game/export'), L('导出参与者') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * 兑换15幸福之旅
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
            $gameMod = M('game');
            $sql=" id in (".$in.") ";
            $gameMod->where($sql)->delete();

            $this->success('删除成功', U('game/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		
		$sqlWhere .= " and headpic!='' ";
		
		
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
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
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
		
		
		
		

        $this->ModManager = M('game');
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
		//$rst=$this->GeneralActionForListing('game', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		$sqlWhere=' 1 ';
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and ouliwei_prize_history.create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and ouliwei_prize_history.create_time < ". $sql_endtime." ";
		}
		
		$sqlWhere.= " and ouliwei_prize_history.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_prize_history.survey_mobile!='' ";
		$sqlWhere.= " and ouliwei_prize_history.prize_type=15 ";
		$sqlWhere.= " and ouliwei_game1.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_game1.status=1 ";
		$sql='select ouliwei_prize_history.* 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			, ouliwei_game1.headegg 
			from ouliwei_prize_history , ouliwei_user , ouliwei_game1 
			where '.$sqlWhere.' 
			order by ouliwei_user.create_time desc
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$toShow['banner'] = empty($rst)?array():$rst;
		
		
		if(isset($_POST['action_export']) && $_POST['action_export']=='yes'){
			
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= 
	        	//"编号ID".$expstr.
	        	"联系地址"
	        	.$expstr."手机号"
	        	.$expstr."创建时间"
	            .$expstr."注册用户手机"
	            .$expstr."注册用户姓名"
	            .$expstr."全家福文件名"
	            .$expstr."全家福url"
	            .$expenter;

	        header('Cache-control: private');
	        header('Content-Disposition: attachment; filename='.$downloadfilename);

	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
	                //$summary=$toShow['banner'][$k]['summary'];
	                //$summary=str_replace("\r\n"," [Enter] ",$summary);
	                //$summary=str_replace("\n"," [Enter] ",$summary);
	                //$summary=str_replace("\r","",$summary);
					
					$userinfo=$toShow['banner'][$k];
					
					//$userinfo['headpic']=empty($userinfo['headpic'])?"":BASE_URL."/public/web_pic/".$userinfo['headpic'];
		        	//$userinfo['headegg']=empty($userinfo['headegg'])?"":BASE_URL."/public/web_resize/".$userinfo['headegg'];
		        	//$userinfo['gamefilter']=empty($userinfo['gamefilter'])?"":BASE_URL."/public/web_filter/".$userinfo['gamefilter'];
		        	
		        	
		        	$headegg_url='http://'.$_SERVER["HTTP_HOST"].'/cms/public/web_resize/'.$userinfo['headegg'];


	                    $output .= 
	                    	//$userinfo['id'].$expstr.
	                    	$userinfo['survey_address']
	                    	.$expstr.$userinfo['survey_mobile']
	                        .$expstr.date('Y-m-d H:i:s',$userinfo['create_time'])
	                        .$expstr.$userinfo['username']
	                        .$expstr.$userinfo['realname']
	                        .$expstr.$userinfo['headegg']
	                        .$expstr.$headegg_url
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
        
        
        
		
		$PageTitle = L('兑换幸福之旅');
		$PageMenu = array(
			//array( U('game/create'), L('添加参与者') ),
            //array( U('game/export'), L('导出参与者') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * 兑换20 iphone
	 *--------------------------------------------------------------+
	 */
	public function listing_iphone()
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
            $gameMod = M('game');
            $sql=" id in (".$in.") ";
            $gameMod->where($sql)->delete();

            $this->success('删除成功', U('game/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		
		$sqlWhere .= " and headpic!='' ";
		
		
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
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
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
		
		
		
		

        $this->ModManager = M('game');
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
		//$rst=$this->GeneralActionForListing('game', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		$sqlWhere=' 1 ';
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and ouliwei_prize_history.create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and ouliwei_prize_history.create_time < ". $sql_endtime." ";
		}
		
		$sqlWhere.= " and ouliwei_prize_history.user_id=ouliwei_user.id ";
		//$sqlWhere.= " and ouliwei_prize_history.survey_mobile!='' ";
		$sqlWhere.= " and ouliwei_prize_history.prize_type=20 ";
		$sqlWhere.= " and (ouliwei_prize_history.iphone_is_prize=1 or ouliwei_prize_history.iphone_is_prize=2) ";
		$sql='select ouliwei_prize_history.* 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			from ouliwei_prize_history , ouliwei_user 
			where '.$sqlWhere.' 
			order by ouliwei_user.create_time desc
			';
		echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$toShow['banner'] = empty($rst)?array():$rst;
		
		
		if(isset($_POST['action_export']) && $_POST['action_export']=='yes'){
			
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= 
	        	//"编号ID".$expstr.
	        	"是否中奖（1是，2否）"
	        	.$expstr."真实姓名"
	        	.$expstr."身份证"
	        	.$expstr."手机"
	        	.$expstr."地址"
	        	.$expstr."创建时间"
	            .$expstr."注册用户手机"
	            .$expstr."注册用户姓名"
	            .$expenter;

	        header('Cache-control: private');
	        header('Content-Disposition: attachment; filename='.$downloadfilename);

	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
	                //$summary=$toShow['banner'][$k]['summary'];
	                //$summary=str_replace("\r\n"," [Enter] ",$summary);
	                //$summary=str_replace("\n"," [Enter] ",$summary);
	                //$summary=str_replace("\r","",$summary);
					
					$userinfo=$toShow['banner'][$k];
					
					//$userinfo['headpic']=empty($userinfo['headpic'])?"":BASE_URL."/public/web_pic/".$userinfo['headpic'];
		        	//$userinfo['headegg']=empty($userinfo['headegg'])?"":BASE_URL."/public/web_resize/".$userinfo['headegg'];
		        	//$userinfo['gamefilter']=empty($userinfo['gamefilter'])?"":BASE_URL."/public/web_filter/".$userinfo['gamefilter'];
		        	
		        	if(!empty($userinfo['iphone_idcard'])){
		        		$userinfo['iphone_idcard']="'".$userinfo['iphone_idcard'];
		        	}


	                    $output .= 
	                    	//$userinfo['id'].$expstr.
	                    	$userinfo['iphone_is_prize']
	                    	.$expstr.$userinfo['iphone_realname']
	                    	.$expstr.$userinfo['iphone_idcard']
	                    	.$expstr.$userinfo['iphone_mobile']
	                    	.$expstr.$userinfo['iphone_address']
	                        .$expstr.date('Y-m-d H:i:s',$userinfo['create_time'])
	                        .$expstr.$userinfo['username']
	                        .$expstr.$userinfo['realname']
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
        
        
        
		
		$PageTitle = L('兑换iphone');
		$PageMenu = array(
			//array( U('game/create'), L('添加参与者') ),
            //array( U('game/export'), L('导出参与者') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * 兑换 20元礼包 
	 *--------------------------------------------------------------+
	 */
	public function listing_bag()
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
            $gameMod = M('game');
            $sql=" id in (".$in.") ";
            $gameMod->where($sql)->delete();

            $this->success('删除成功', U('game/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		
		$sqlWhere .= " and headpic!='' ";
		
		
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
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
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
		
		
		
		

        $this->ModManager = M('game');
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
		//$rst=$this->GeneralActionForListing('game', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		$sqlWhere=' 1 ';
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and ouliwei_prize_history.create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and ouliwei_prize_history.create_time < ". $sql_endtime." ";
		}
		
		$sqlWhere.= " and ouliwei_prize_history.user_id=ouliwei_user.id ";
		//$sqlWhere.= " and ouliwei_prize_history.survey_mobile!='' ";
		//$sqlWhere.= " and (ouliwei_prize_history.prize_type=15 or ouliwei_prize_history.prize_type=20) ";
		$sqlWhere.= " and bag_address!='' ";
		$sql='select ouliwei_prize_history.* 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			from ouliwei_prize_history , ouliwei_user 
			where '.$sqlWhere.' 
			order by ouliwei_user.create_time desc
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$toShow['banner'] = empty($rst)?array():$rst;
		
		
		if(isset($_POST['action_export']) && $_POST['action_export']=='yes'){
			
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= 
	        	//"编号ID".$expstr.
	        	"礼包省"
	        	.$expstr."礼包市"
	        	.$expstr."礼包区"
	        	.$expstr."礼包门店名"
	        	.$expstr."礼包门店地址"
	        	.$expstr."创建时间"
	            .$expstr."注册用户手机"
	            .$expstr."注册用户姓名"
	            .$expenter;

	        header('Cache-control: private');
	        header('Content-Disposition: attachment; filename='.$downloadfilename);

	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
	                //$summary=$toShow['banner'][$k]['summary'];
	                //$summary=str_replace("\r\n"," [Enter] ",$summary);
	                //$summary=str_replace("\n"," [Enter] ",$summary);
	                //$summary=str_replace("\r","",$summary);
					
					$userinfo=$toShow['banner'][$k];
					
					//$userinfo['headpic']=empty($userinfo['headpic'])?"":BASE_URL."/public/web_pic/".$userinfo['headpic'];
		        	//$userinfo['headegg']=empty($userinfo['headegg'])?"":BASE_URL."/public/web_resize/".$userinfo['headegg'];
		        	//$userinfo['gamefilter']=empty($userinfo['gamefilter'])?"":BASE_URL."/public/web_filter/".$userinfo['gamefilter'];
		        	
		        	


	                    $output .= 
	                    	//$userinfo['id'].$expstr.
	                    	$userinfo['bag_area']
	                    	.$expstr.$userinfo['bag_city']
	                    	.$expstr.$userinfo['bag_street']
	                    	.$expstr.$userinfo['bag_shopname']
	                    	.$expstr.$userinfo['bag_address']
	                        .$expstr.date('Y-m-d H:i:s',$userinfo['create_time'])
	                        .$expstr.$userinfo['username']
	                        .$expstr.$userinfo['realname']
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
        
        
        
		
		$PageTitle = L('兑换礼包');
		$PageMenu = array(
			//array( U('game/create'), L('添加参与者') ),
            //array( U('game/export'), L('导出参与者') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * 试驾
	 *--------------------------------------------------------------+
	 */
	public function listing_testdrive()
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
            $gameMod = M('game');
            $sql=" id in (".$in.") ";
            $gameMod->where($sql)->delete();

            $this->success('删除成功', U('game/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		
		$sqlWhere .= " and headpic!='' ";
		
		
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
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
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
		
		
		
		

        $this->ModManager = M('game');
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
		//$rst=$this->GeneralActionForListing('game', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		$sqlWhere=' 1 ';
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and ouliwei_testdrive.create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and ouliwei_testdrive.create_time < ". $sql_endtime." ";
		}
		
		$sqlWhere.= " and ouliwei_testdrive.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_testdrive.mobile!='' ";
		//$sqlWhere.= " and (ouliwei_prize_history.prize_type=15 or ouliwei_prize_history.prize_type=20) ";
		//$sqlWhere.= " and bag_address!='' ";
		$sql='select ouliwei_testdrive.* 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			from ouliwei_testdrive , ouliwei_user 
			where '.$sqlWhere.' 
			order by ouliwei_user.create_time desc
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$toShow['banner'] = empty($rst)?array():$rst;
		
		
		if(isset($_POST['action_export']) && $_POST['action_export']=='yes'){
			
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= 
	        	//"编号ID".$expstr.
	        	"试驾姓名"
	        	.$expstr."试驾手机"
	        	.$expstr."试驾省"
	        	.$expstr."试驾市"
        		.$expstr."试驾区"
        		.$expstr."试驾门店名"
        		.$expstr."试驾门店地址"
	        	.$expstr."试驾预约时间"
	        	.$expstr."创建时间"
	            .$expstr."注册用户手机"
	            .$expstr."注册用户姓名"
	            .$expenter;

	        header('Cache-control: private');
	        header('Content-Disposition: attachment; filename='.$downloadfilename);

	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
	                //$summary=$toShow['banner'][$k]['summary'];
	                //$summary=str_replace("\r\n"," [Enter] ",$summary);
	                //$summary=str_replace("\n"," [Enter] ",$summary);
	                //$summary=str_replace("\r","",$summary);
					
					$userinfo=$toShow['banner'][$k];
					
					//$userinfo['headpic']=empty($userinfo['headpic'])?"":BASE_URL."/public/web_pic/".$userinfo['headpic'];
		        	//$userinfo['headegg']=empty($userinfo['headegg'])?"":BASE_URL."/public/web_resize/".$userinfo['headegg'];
		        	//$userinfo['gamefilter']=empty($userinfo['gamefilter'])?"":BASE_URL."/public/web_filter/".$userinfo['gamefilter'];
		        	
		        	


	                    $output .= 
	                    	//$userinfo['id'].$expstr.
	                    	$userinfo['name']
	                    	.$expstr.$userinfo['mobile']
	                    	.$expstr.$userinfo['prov']
	                    	.$expstr.$userinfo['city']
                    		.$expstr.$userinfo['street']
                    		.$expstr.$userinfo['shopname']
                    		.$expstr.$userinfo['address']
	                    	.$expstr.$userinfo['yuyue_time']
	                        .$expstr.date('Y-m-d H:i:s',$userinfo['create_time'])
	                        .$expstr.$userinfo['username']
	                        .$expstr.$userinfo['realname']
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
        
        
        
		
		$PageTitle = L('试驾');
		$PageMenu = array(
			//array( U('game/create'), L('添加参与者') ),
            //array( U('game/export'), L('导出参与者') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	
	
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * 扫过码的用户
	 *--------------------------------------------------------------+
	 */
	public function listing_qr_user()
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
            $gameMod = M('game');
            $sql=" id in (".$in.") ";
            $gameMod->where($sql)->delete();

            $this->success('删除成功', U('game/listing'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

		///列表过滤
		$sqlWhere = "status < 250";
		
		$sqlWhere .= " and headpic!='' ";
		
		
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
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname=='content'){
				$sqlWhere .= " and (email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
			}
			elseif($filter_fieldname==''){
				$sqlWhere .= " and (realname like '%". $this->fixSQL($f_search)."%' or email like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%') ";
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
		
		
		
		

        $this->ModManager = M('game');
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
		//$rst=$this->GeneralActionForListing('game', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;
		
		
		$sqlWhere=' 1 ';
		
		$filter_starttime = $this->REQUEST('_filter_starttime');
		$filter_endtime = $this->REQUEST('_filter_endtime');
		if( $filter_starttime != '' ){
			$sql_starttime=strtotime($filter_starttime);
			$sqlWhere .= " and ouliwei_user_point.create_time >= ". $sql_starttime." ";
		}
		if( $filter_endtime != '' ){
			$sql_endtime=strtotime($filter_endtime)+(24*3600);
			$sqlWhere .= " and ouliwei_user_point.create_time < ". $sql_endtime." ";
		}
		
		$sqlWhere.= " and ouliwei_user_point.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_user_point.source='qr' ";
		
		
		$sql='select ouliwei_user_point.source 
			, ouliwei_user_point.user_id 
			, ouliwei_user_point.create_time as qr_create_time
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			from ouliwei_user_point , ouliwei_user 
			where '.$sqlWhere.' 
			group by ouliwei_user_point.user_id 
			order by ouliwei_user_point.create_time desc
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$toShow['banner'] = empty($rst)?array():$rst;
		
		
		if(isset($_POST['action_export']) && $_POST['action_export']=='yes'){
			
	        $start=0;
	        $pagenum=1000000;

	        $downloadfilename='ExportData.csv';
	        $output="";
	        $expstr="\t";
	        $expenter="\t\n";
	        $output .= 
	        	//"编号ID".$expstr.
	        	"扫码时间"
	            .$expstr."注册用户手机"
	            .$expstr."注册用户姓名"
	            .$expstr."问卷15地址"
	            .$expstr."问卷15姓名"
	            .$expstr."问卷20地址"
	            .$expstr."问卷20姓名"
	            .$expstr."中奖iphone真实姓名"
	        	.$expstr."中奖iphone身份证"
	        	.$expstr."中奖iphone手机"
	        	.$expstr."中奖iphone地址"
	            .$expenter;

	        

	        if (!empty($toShow['banner'])){
	            $k=0;
	            do{
	                
	                //$summary=$toShow['banner'][$k]['summary'];
	                //$summary=str_replace("\r\n"," [Enter] ",$summary);
	                //$summary=str_replace("\n"," [Enter] ",$summary);
	                //$summary=str_replace("\r","",$summary);
					
					$userinfo=$toShow['banner'][$k];
					
					//$userinfo['headpic']=empty($userinfo['headpic'])?"":BASE_URL."/public/web_pic/".$userinfo['headpic'];
		        	//$userinfo['headegg']=empty($userinfo['headegg'])?"":BASE_URL."/public/web_resize/".$userinfo['headegg'];
		        	//$userinfo['gamefilter']=empty($userinfo['gamefilter'])?"":BASE_URL."/public/web_filter/".$userinfo['gamefilter'];
		        	
		        	
		        	
		        	$sql='select create_time as saoma_time
						from ouliwei_user_point 
						where user_id="'.$userinfo['user_id'].'" 
						and source="qr" 
						order by create_time asc limit 1
						';
					//echo $sql;echo "<br>";
					$saoma_info = $Dao->query( $sql );
					
					
		        	
		        	$sql='select survey_address,survey_mobile 
						from ouliwei_prize_history
						where user_id="'.$userinfo['user_id'].'" 
						and prize_type=15 
						and survey_mobile!="" 
						order by id desc limit 1
						';
					//echo $sql;echo "<br>";
					$prize_history_info_15 = $Dao->query( $sql );
					
					
		        	$sql='select survey_address,survey_mobile 
						from ouliwei_prize_history
						where user_id="'.$userinfo['user_id'].'" 
						and prize_type=20 
						and survey_mobile!="" 
						order by id desc limit 1
						';
					//echo $sql;echo "<br>";
					$prize_history_info_20 = $Dao->query( $sql );
		        	
					
		        	$sql='select iphone_is_prize 
		        		,iphone_realname 
		        		,iphone_idcard 
		        		,iphone_mobile 
		        		,iphone_address 
						from ouliwei_prize_history
						where user_id="'.$userinfo['user_id'].'" 
						and prize_type=20 
						and iphone_is_prize=1 
						order by id desc limit 1
						';
					//echo $sql;echo "<br>";
					$prize_iphone = $Dao->query( $sql );
		        	
					
					if(isset($prize_iphone[0]['iphone_idcard']) && $prize_iphone[0]['iphone_idcard']!=""){
						$iphone_idcard="'".$prize_iphone[0]['iphone_idcard'];
					}
					else{
						$iphone_idcard="";
					}
					
	                    $output .= 
	                    	//$userinfo['id'].$expstr.
	                    	date('Y-m-d H:i:s',$saoma_info[0]['saoma_time'])
	                        .$expstr.$userinfo['username']
	                        .$expstr.$userinfo['realname']
	                        .$expstr.$prize_history_info_15[0]['survey_address']
	                        .$expstr.$prize_history_info_15[0]['survey_mobile']
	                        .$expstr.$prize_history_info_20[0]['survey_address']
	                        .$expstr.$prize_history_info_20[0]['survey_mobile']
	                        .$expstr.$prize_iphone[0]['iphone_realname']
	                        .$expstr.$iphone_idcard
	                        .$expstr.$prize_iphone[0]['iphone_mobile']
	                        .$expstr.$prize_iphone[0]['iphone_address']
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
        
        
        
		
		$PageTitle = L('扫过码的用户');
		$PageMenu = array(
			//array( U('game/create'), L('添加参与者') ),
            //array( U('game/export'), L('导出参与者') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	
	
	
	
	
	/**
	 *--------------------------------------------------------------+
	 * 注册用户 
	 *--------------------------------------------------------------+
	 */
	public function listing_all_total()
	{

        
		/*
		上传照片  拼图  路线投票  兑换礼包 --》这几个是要知道当前总人数
		1人玩了2次拼图，算1，还是算2==》》算1
		*/
		
		
		//上传照片人数
		$sqlWhere='';
		$sqlWhere.= " and ouliwei_game1.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_game1.headegg!='' and ouliwei_game1.style!='' ";
		//$sqlWhere.= " and (ouliwei_prize_history.prize_type=15 or ouliwei_prize_history.prize_type=20) ";
		//$sqlWhere.= " and bag_address!='' ";
		$sql='select ouliwei_user.id 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			, ouliwei_user.qr 
			, ouliwei_user.create_time 
			from ouliwei_user , ouliwei_game1 
			where 1 '.$sqlWhere.' 
			group by ouliwei_user.id 
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$num_game1 = empty($rst)?0:count($rst);
		//echo $num_game1;exit;
        $this->assign('num_game1', $num_game1);
        
		
		
		//拼图人数
		$sqlWhere='';
		$sqlWhere.= " and ouliwei_game2.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_game2.usetime!='' ";
		//$sqlWhere.= " and (ouliwei_prize_history.prize_type=15 or ouliwei_prize_history.prize_type=20) ";
		//$sqlWhere.= " and bag_address!='' ";
		$sql='select ouliwei_user.id 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			, ouliwei_user.qr 
			, ouliwei_user.create_time 
			from ouliwei_user , ouliwei_game2 
			where 1 '.$sqlWhere.' 
			group by ouliwei_user.id 
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$num_game2 = empty($rst)?0:count($rst);
		//echo $num_game1;exit;
        $this->assign('num_game2', $num_game2);
        
        
        
        
		//路线投票人数
		$sqlWhere='';
		$sqlWhere.= " and ouliwei_game3.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_game3.site>0 ";
		//$sqlWhere.= " and (ouliwei_prize_history.prize_type=15 or ouliwei_prize_history.prize_type=20) ";
		//$sqlWhere.= " and bag_address!='' ";
		$sql='select ouliwei_user.id 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			, ouliwei_user.qr 
			, ouliwei_user.create_time 
			from ouliwei_user , ouliwei_game3 
			where 1 '.$sqlWhere.' 
			group by ouliwei_user.id 
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$num_game3 = empty($rst)?0:count($rst);
		//echo $num_game1;exit;
        $this->assign('num_game3', $num_game3);
        
        
        
        
        
		//兑换礼包人数
		$sqlWhere='';
		$sqlWhere.= " and ouliwei_prize_history.user_id=ouliwei_user.id ";
		$sqlWhere.= " and ouliwei_prize_history.bag_address!='' ";
		//$sqlWhere.= " and (ouliwei_prize_history.prize_type=15 or ouliwei_prize_history.prize_type=20) ";
		//$sqlWhere.= " and bag_address!='' ";
		$sql='select ouliwei_user.id 
			, ouliwei_user.username 
			, ouliwei_user.realname 
			, ouliwei_user.point_total 
			, ouliwei_user.qr 
			, ouliwei_user.create_time 
			from ouliwei_user , ouliwei_prize_history 
			where 1 '.$sqlWhere.' 
			group by ouliwei_user.id 
			';
		//echo $sql;exit;
		$Dao = M();
		$rst = $Dao->query( $sql );
		//echo "<pre>";print_r($rst);exit;
		$num_bag = empty($rst)?0:count($rst);
		//echo $num_game1;exit;
        $this->assign('num_bag', $num_bag);
        
        
        
        
        
		$PageTitle = L('数据统计');
		$PageMenu = array(
			//array( U('game/create'), L('添加参与者') ),
            //array( U('game/export'), L('导出参与者') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}
	
	
	
	

    //导出参与者
    public function export()
    {
        $CityMod = M('game');
        $toShow['banner'] = $CityMod->where(" status=1 " )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "参与者ID编号".$expstr."联系人".$expstr."联系电话"
            .$expstr."参与者"
            .$expstr."创建时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
                
                $summary=$toShow['banner'][$k]['summary'];
                $summary=str_replace("\r\n"," [Enter] ",$summary);
                $summary=str_replace("\n"," [Enter] ",$summary);
                $summary=str_replace("\r","",$summary);



                    $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['realname'].$expstr.$toShow['banner'][$k]['phone']
                        .$expstr.$summary
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
	        $gameMod = M('game');
			
			$rst=$this->CheckgameData_Post();
			
			if (false === $gameMod->create()) {
				$this->error($module->getError());
			}
			
	        if($gameMod->create()) {

	        	//echo "<pre>";print_r($gameMod);exit;

	        	//使用 $gameMod->email
        		$rst=$this->CheckgameData_Mod($gameMod);
	        	$gameMod->create_time=time();
	        	$gameMod->password=md5($gameMod->password);
	        	
	        	$result =   $gameMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($gameMod->getError());
	        }
			
		}else{

			$PageTitle = L('添加参与者');
			$PageMenu = array(
					array( U('game/listing'), L('参与者列表') ),
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
	public function edit_2()
	{
        $CityMod = M('area');
        $area_list = $CityMod->field('a_id,a_name')->where(" a_pid=0 " )->order('a_id asc')->select();
        //echo "<pre>";print_r($area_list);exit;
        $this->assign('area_list', $area_list);


    	///注意：老参与者参与者已填写情况下不允许修改参与者名，参与者名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            $rst=$this->CheckgameData_Post($id);

        	///密码为空则不修改密码
        	if( isset($_POST['password'])){
                if($_POST['password'] != '' ){
        		    $_POST['password'] = md5($_POST['password']);
        	    }
        	    else{
                    unset( $_POST['password'] );
        	    }
            }

	        $gameMod = M('game');

	        if($gameMod->create()) {
	        	
                $rst=$this->CheckgameData_Mod($gameMod,$id);
                $gameMod->modify_time=time();

	            $result =   $gameMod->save();
	            if($result) {
	                $this->success('操作成功！', U('game/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($gameMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $gameMod = M('game');
		    // 读取数据
		    $data =   $gameMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('参与者数据读取错误');
		    }
    
			$PageTitle = L('编辑参与者');
			$PageMenu = array(
					//array( U('game/create'), L('添加参与者') ),
					array( U('game/listing'), L('参与者列表') ),
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
		$module = $gameMod = M('game');
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
        $gameMod = M('game');
        $sql=" id in (".$in.") ";
        $gameMod->where($sql)->delete();

        $this->success('删除成功', U('game/listing'));
    }
*/

	private function CheckgameData_Post($game_id=0){
		///检查 $_POST 提交数据
		
			$gameMod = M('game');

			$result = $gameMod->where("gamename='%s' and id!=%d ", $_POST['gamename'], $game_id )->count();
			if($result>0){
            $this->error(L('存在重复的参与者名'));
            }
            
			$result = $gameMod->where("email='%s' and id!=%d ", $_POST['email'], $game_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
            
	}
	

	private function CheckgameData_Mod(&$game, $game_id=0){
		///检查 $game 模型数据。$game->email
		
			$gameMod = M('game');
			
			$result = $gameMod->where("gamename='%s' and id!=%d ", $game->gamename, $game_id )->count();
			if($result>0){
            $this->error(L('存在重复的参与者名'));
            }
            
			$result = $gameMod->where("email='%s' and id!=%d ", $game->email, $game_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
		
	}
	
	
	
	//幸福快报、幸福点滴
	public function edit_news()
	{   
        
        

    	///注意：老参与者参与者已填写情况下不允许修改参与者名，参与者名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);
			
			//echo "<pre>";print_r($_POST);exit;
			
            //$rst=$this->CheckactivitymbData_Post($id);

	        $activitymbMod = M('news');

	        if($activitymbMod->create()) {
	        	
                //$rst=$this->CheckactivitymbData_Mod($activitymbMod,$id);
                $activitymbMod->modify_time=time();
                //$activitymbMod->start_time=str_replace("/","-",$this->REQUEST('start_time'));
                //$activitymbMod->end_time=str_replace("/","-",$this->REQUEST('end_time'));

	            $result =   $activitymbMod->save();
	            if($result) {
	                $this->success('操作成功！', U('game/edit_news', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($activitymbMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $gameMod = M('news');
		    // 读取数据
		    $data =   $gameMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('参与者数据读取错误');
		    }
    
			$PageTitle = L('快报点滴');
			$PageMenu = array(
					//array( U('game/create'), L('添加参与者') ),
					//array( U('game/listing'), L('参与者列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
		
		
	}
	
	
	//猜题题目
	public function edit_question()
	{   
        
        

    	///注意：老参与者参与者已填写情况下不允许修改参与者名，参与者名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);
			
			//echo "<pre>";print_r($_POST);exit;
			
            //$rst=$this->CheckactivitymbData_Post($id);

	        $activitymbMod = M('question');

	        if($activitymbMod->create()) {
	        	
                //$rst=$this->CheckactivitymbData_Mod($activitymbMod,$id);
                $activitymbMod->modify_time=time();
                //$activitymbMod->start_time=str_replace("/","-",$this->REQUEST('start_time'));
                //$activitymbMod->end_time=str_replace("/","-",$this->REQUEST('end_time'));

	            $result =   $activitymbMod->save();
	            if($result) {
	                $this->success('操作成功！', U('game/edit_question', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($activitymbMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $gameMod = M('question');
		    // 读取数据
		    $data =   $gameMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('参与者数据读取错误');
		    }
    
			$PageTitle = L('猜题题目');
			$PageMenu = array(
					//array( U('game/create'), L('添加参与者') ),
					//array( U('game/listing'), L('参与者列表') ),
			);
			$this->setPagething( $PageTitle, $PageMenu, true);
			$this->display();
		}
		
		
	}
	
	


}
?>