<?php
/**
 * 简单预约系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class bookingAction extends TAction
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
            $bookingMod = M('booking');
            $sql=" id in (".$in.") ";
            $bookingMod->where($sql)->delete();

            $this->success('删除成功', U('booking/listing'));
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

        $f_search = $this->REQUEST('_f_search');
		if( $f_search != '' ){
			$sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' or phone like '%". $this->fixSQL($f_search)."%' or email like '%". $this->fixSQL($f_search)."%')";
		}

        $this->ModManager = M('booking');
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
        $this->assign('filter_role',   $filter_role);
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

		///获取列表数据集
		$rst=$this->GeneralActionForListing('booking', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

		$PageTitle = L('预约列表');
		$PageMenu = array(
			//array( U('booking/create'), L('添加预约') ),
            array( U('booking/export'), L('导出预约') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
		$this->display();
	}

    //导出预约
    public function export()
    {
        $CityMod = M('booking');
        $toShow['banner'] = $CityMod->where(" status=1 " )->order('id desc')->select();
        //echo "<pre>";print_r($toShow['banner']);exit;

        $start=0;
        $pagenum=1000000;

        $downloadfilename='ExportData.csv';
        $output="";
        $expstr="\t";
        $expenter="\t\n";
        $output .= "预约ID编号".$expstr."姓名"
            .$expstr."邮箱".$expstr."手机"
            .$expstr."预约时间"
            .$expstr."服务中心"
            .$expstr."希望体验的产品"
            .$expstr."建议"
            .$expstr."创建时间"
            .$expenter;

        header('Cache-control: private');
        header('Content-Disposition: attachment; filename='.$downloadfilename);

        if (!empty($toShow['banner'])){
            $k=0;
            do{
                //if ($toShow['banner'][$k]['gender']=="1"){
                //    $gender_show="男";
                //}
                //if ($toShow['banner'][$k]['gender']=="0"){
                //    $gender_show="女";
                //}

                $store_prov_city="";
                if($toShow['banner'][$k]['store_bname']!=""){
                    $store_prov_city=$store_prov_city.$toShow['banner'][$k]['store_bname']." - ";
                }
                if($toShow['banner'][$k]['store_sname']!=""){
                    $store_prov_city=$store_prov_city.$toShow['banner'][$k]['store_sname'];
                }
                if($toShow['banner'][$k]['store_title']!=""){
                    $store_prov_city=$store_prov_city." - ".$toShow['banner'][$k]['store_title'];
                }

                $suggest=$toShow['banner'][$k]['suggest'];

                $suggest=str_replace("\r\n"," [Enter] ",$suggest);
                $suggest=str_replace("\n"," [Enter] ",$suggest);
                $suggest=str_replace("\r","",$suggest);


                $output .= $toShow['banner'][$k]['id'].$expstr.$toShow['banner'][$k]['title']
                    .$expstr.$toShow['banner'][$k]['email'].$expstr.$toShow['banner'][$k]['phone']
                    .$expstr.$toShow['banner'][$k]['book_date']
                    .$expstr.$store_prov_city
                    .$expstr.$toShow['banner'][$k]['product_title']
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
	        $bookingMod = M('booking');
			
			$rst=$this->CheckbookingData_Post();
			
			if (false === $bookingMod->create()) {
				$this->error($module->getError());
			}
			
	        if($bookingMod->create()) {

	        	//echo "<pre>";print_r($bookingMod);exit;

	        	//使用 $bookingMod->email
        		$rst=$this->CheckbookingData_Mod($bookingMod);
	        	$bookingMod->create_time=time();
	        	$bookingMod->password=md5($bookingMod->password);
	        	
	        	$result =   $bookingMod->add();
	            if($result) {
	                $this->success('操作成功！');
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($bookingMod->getError());
	        }
			
		}else{

			$PageTitle = L('添加预约');
			$PageMenu = array(
					array( U('booking/listing'), L('预约列表') ),
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


    	///注意：老预约预约已填写情况下不允许修改预约名，预约名空可设置，需检查唯一
    	///email 改变的话 还要检查 email 不重复

		if(isset($_POST['dosubmit'])){
			$id = intval($this->REQUEST('id'), 0);

            $rst=$this->CheckbookingData_Post($id);

        	///密码为空则不修改密码
        	if( isset($_POST['password'])){
                if($_POST['password'] != '' ){
        		    $_POST['password'] = md5($_POST['password']);
        	    }
        	    else{
                    unset( $_POST['password'] );
        	    }
            }

	        $bookingMod = M('booking');

	        if($bookingMod->create()) {
	        	
                $rst=$this->CheckbookingData_Mod($bookingMod,$id);
                $bookingMod->modify_time=time();

	            $result =   $bookingMod->save();
	            if($result) {
	                $this->success('操作成功！', U('booking/edit', array('id'=>$id)) );
	            }else{
	                $this->error('写入错误！');
	            }
	        }else{
	            $this->error($bookingMod->getError());
	        }

		}else{
			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
			}

	        $bookingMod = M('booking');
		    // 读取数据
		    $data =   $bookingMod->find($id);
		    if($data) {
		        $this->record = $data;// 模板变量赋值
		    }else{
		        $this->error('预约数据读取错误');
		    }
    
			$PageTitle = L('编辑预约');
			$PageMenu = array(
					array( U('booking/create'), L('添加预约') ),
					array( U('booking/listing'), L('预约列表') ),
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
		$module = $bookingMod = M('booking');
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
        $bookingMod = M('booking');
        $sql=" id in (".$in.") ";
        $bookingMod->where($sql)->delete();

        $this->success('删除成功', U('booking/listing'));
    }
*/

	private function CheckbookingData_Post($booking_id=0){
		///检查 $_POST 提交数据
		
			$bookingMod = M('booking');

			$result = $bookingMod->where("bookingname='%s' and id!=%d ", $_POST['bookingname'], $booking_id )->count();
			if($result>0){
            $this->error(L('存在重复的预约名'));
            }
            
			$result = $bookingMod->where("email='%s' and id!=%d ", $_POST['email'], $booking_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
            
	}
	

	private function CheckbookingData_Mod(&$booking, $booking_id=0){
		///检查 $booking 模型数据。$booking->email
		
			$bookingMod = M('booking');
			
			$result = $bookingMod->where("bookingname='%s' and id!=%d ", $booking->bookingname, $booking_id )->count();
			if($result>0){
            $this->error(L('存在重复的预约名'));
            }
            
			$result = $bookingMod->where("email='%s' and id!=%d ", $booking->email, $booking_id)->count();
			if($result>0){
            $this->error(L('存在重复的邮箱'));
            }
		
	}


}
?>