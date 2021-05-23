<?php
class baomingAction extends TAction
{

	
	
	public function test(){
		exit;
		
        if(isset($_SESSION['order_no'])){
        	unset($_SESSION['order_no']);
        }
        if(isset($_SESSION['ticket_type'])){
        	unset($_SESSION['ticket_type']);
        }
        
		exit;
		
    }
    
    
    
	public function token_member_ajax(){
    	//echo "<pre>";print_r($_SESSION);exit;
    	$token_rst=$this->token_member('controller');
    	$token_rst_e=json_encode($token_rst);
		echo $token_rst_e;
		exit;
		
    }
    
    
    /*
    //赛事列表
    public function index(){
    	
    	//echo "<pre>";print_r($_SESSION);exit;
    	
		//记录来源于微信，区别于app
		$_SESSION['is_wexin']=1;
		
		
		$reg_comment=$_SESSION['reg_comment'];
		if(empty($reg_comment)){
			$reg_comment='';
		}
		$this->assign('reg_comment', $reg_comment);
		
		
		//直接通过修改个人信息的url /memeber/index 进来，如果没登陆，会跳登陆页，登陆完毕后，跳本方法，本方法判断，如果之前来自修改个人信息页，则跳回修改个人信息页。
		//$login_after_jump_url=$_SESSION['login_after_jump_url'];  //拿session
		//if(!empty($login_after_jump_url)){
		//	$_SESSION['login_after_jump_url'] = '';
		//	redirect($login_after_jump_url);
		//}
		
		
		//取赛事列表的方法
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.catalog.list';
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		$data['catalog_list'] = empty($api_result['RaceCatalogList'])?array():$api_result['RaceCatalogList'];
		//echo "<pre>";print_r($data['catalog_list']);exit;
		$this->assign('catalog_list', $data['catalog_list']);
		
		
        $this->assign('curmenu', '7');
        $this->display('index');
    }
	*/
	
	
	
	//20160823首页：改为获得赛事id=1和赛事id=9的所有分站列表
    public function index(){
    	
		//记录来源于微信，区别于app
		$_SESSION['is_wexin']=1;
		
		
		$reg_comment=$_SESSION['reg_comment'];
		if(empty($reg_comment)){
			$reg_comment='';
		}
		$this->assign('reg_comment', $reg_comment);
		
		
		
		$stage_all=array();
		
		
		$catalog_id_list=array(1,9);
		//echo "<pre>";print_r($catalog_id_list);exit;
		
		foreach($catalog_id_list as $k_catalog => $v_catalog){
			
			$catalog_id=$v_catalog;
			
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.catalog.info&RaceCatalogId='.$catalog_id;
			$api_para=array();
			$api_result=$this->http_request_url_post($api_url,$api_para);
			//echo "<pre>";print_r($api_result);exit;
			$data['RaceStageList'] = empty($api_result['RaceStageList'])?array():$api_result['RaceStageList'];
			//echo "<pre>";print_r($data['RaceStageList']);exit;
			$RaceStageList=$data['RaceStageList'];
			//echo "<pre>";print_r($RaceStageList);exit;
			
			$CatalogInfo=$api_result['RaceCatalogInfo'];
			//echo "<pre>";print_r($CatalogInfo);exit;
			
			$addtime_t=time();
			$addtime=date('Y-m-d H:i:s',$addtime_t);
			//echo $addtime;exit;
			
			
			if(!empty($RaceStageList)){
				foreach($RaceStageList as $k=>$v){
					$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$v['RaceStageId'];
					//echo $api_url;exit;
					$api_para=array();
					$api_result=$this->http_request_url_post($api_url,$api_para);
					//echo "<pre>";print_r($api_result);exit;
					
					$tmp=array();
					
					$tmp['RaceStageId']=$api_result['RaceStageInfo']['RaceStageId'];
					$tmp['RaceStageName']=$api_result['RaceStageInfo']['RaceStageName'];
					
					$tmp['ApplyStartTime_all']=$api_result['RaceStageInfo']['ApplyStartTime'];
					$tmp['ApplyEndTime_all']=$api_result['RaceStageInfo']['ApplyEndTime'];
					
					
					$tmp['ApplyStartTime']=substr($api_result['RaceStageInfo']['ApplyStartTime'],0,10);
					$tmp['ApplyEndTime']=substr($api_result['RaceStageInfo']['ApplyEndTime'],0,10);
					$tmp['StageStartDate']=substr($api_result['RaceStageInfo']['StageStartDate'],0,10);
					$tmp['StageEndDate']=substr($api_result['RaceStageInfo']['StageEndDate'],0,10);
					
					
					$tmp['StageStatus']=$api_result['RaceStageInfo']['RaceStageStatus'];
					
					if($addtime>=$tmp['ApplyStartTime_all'] && $addtime<=$tmp['ApplyEndTime_all']){
						$tmp['status_text']='报名中';
					}
					else{
						if($addtime>$tmp['ApplyEndTime_all']){
							$tmp['status_text']='报名结束';
						}
						else{
							$tmp['status_text']='敬请期待';
						}
					}
					
					//强制显示报名中，可点击下一步。
					//$tmp['status_text']='报名中';
					
					$tmp['RaceCatalogId']=$api_result['RaceStageInfo']['RaceCatalogId'];
					$tmp['RaceCatalogName']=$CatalogInfo['RaceCatalogName'];
					//$tmp['RaceCatalogIcon']=$CatalogInfo['comment']['RaceCatalogIcon'];   //调赛事的图
					$tmp['RaceCatalogIcon']=$api_result['RaceStageInfo']['comment']['RaceStageIconList'][1]['RaceStageIcon'];   //调分站的图
					
					$stage_all[]=$tmp;
				}
			}
			
		}
		
		
		//重新排序，分站id大的排前面
		$vals = array();
		foreach($stage_all as $key => $row)
		{
			$vals[$key] = $row['RaceStageId'];
		}
		array_multisort($vals, SORT_DESC, $stage_all);
		
		//echo "<pre>";print_r($stage_all);exit;
		$this->assign('stage_all', $stage_all);
		
		
		
		
		$token_rst=$this->token_member('controller');
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		
        $this->assign('curmenu', '7');
        $this->display('index');
    }
    
	
	
	//单个分站信息  http://xracebm201607.loc/baoming/stageinfo/stage_id/33
	public function stageinfo(){
    	
    	
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		
		
		//记录来源于微信，区别于app
		$_SESSION['is_wexin']=1;
		
		
		$reg_comment=$_SESSION['reg_comment'];
		if(empty($reg_comment)){
			$reg_comment='';
		}
		$this->assign('reg_comment', $reg_comment);
		
		
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		
		$stage_all=array();
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
		//echo $api_url;exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		
		$tmp=array();
		
		$tmp['RaceStageId']=$api_result['RaceStageInfo']['RaceStageId'];
		$tmp['RaceStageName']=$api_result['RaceStageInfo']['RaceStageName'];
		$tmp['ApplyStartTime']=substr($api_result['RaceStageInfo']['ApplyStartTime'],0,10);
		$tmp['ApplyEndTime']=substr($api_result['RaceStageInfo']['ApplyEndTime'],0,10);
		$tmp['StageStartDate']=substr($api_result['RaceStageInfo']['StageStartDate'],0,10);
		$tmp['StageEndDate']=substr($api_result['RaceStageInfo']['StageEndDate'],0,10);
		$tmp['StageStatus']=$api_result['RaceStageInfo']['RaceStageStatus'];
		
		
		$tmp['ApplyStartTime_all']=$api_result['RaceStageInfo']['ApplyStartTime'];
		$tmp['ApplyEndTime_all']=$api_result['RaceStageInfo']['ApplyEndTime'];
		
		
		
		if($addtime>=$tmp['ApplyStartTime_all'] && $addtime<=$tmp['ApplyEndTime_all']){
			$tmp['status_text']='报名中';
		}
		else{
			if($addtime>$tmp['ApplyEndTime_all']){
				$tmp['status_text']='报名结束';
			}
			else{
				$tmp['status_text']='敬请期待';
			}
		}
		
		//强制显示报名中，可点击下一步。
		//$tmp['status_text']='报名中';
		
		$tmp['RaceCatalogId']=$api_result['RaceStageInfo']['RaceCatalogId'];
		//$tmp['RaceCatalogName']=$CatalogInfo['RaceCatalogName'];
		//$tmp['RaceCatalogIcon']=$CatalogInfo['comment']['RaceCatalogIcon'];   //调赛事的图
		$tmp['RaceCatalogIcon']=$api_result['RaceStageInfo']['comment']['RaceStageIconList'][1]['RaceStageIcon'];   //调分站的图
		
		//echo "<pre>";print_r($tmp);exit;
		
		$stage_all[]=$tmp;
		
		//echo "<pre>";print_r($stage_all);exit;
		$this->assign('stage_all', $stage_all);
		
		
		
		
		$token_rst=$this->token_member('controller');
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		
        $this->assign('curmenu', '7');
        $this->display('stageinfo');
    }
    
    
    
	
	//分站列表
	public function stagelist(){
    	
    	
		//记录来源于微信，区别于app
		$_SESSION['is_wexin']=1;
		
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.catalog.info&RaceCatalogId='.$catalog_id;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$data['RaceStageList'] = empty($api_result['RaceStageList'])?array():$api_result['RaceStageList'];
		//echo "<pre>";print_r($data['RaceStageList']);exit;
		$RaceStageList=$data['RaceStageList'];
		//echo "<pre>";print_r($RaceStageList);exit;
		
		$CatalogInfo=$api_result['RaceCatalogInfo'];
		//echo "<pre>";print_r($CatalogInfo);exit;
		$this->assign('CatalogInfo', $CatalogInfo);
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		//if('2016-08-05 12:07:57'>'2016-08-05 12:07:58'){
		//	echo "bbb";exit;
		//}
		
		if(!empty($RaceStageList)){
			foreach($RaceStageList as $k=>$v){
				$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$v['RaceStageId'];
				//echo $api_url;exit;
				$api_para=array();
				$api_result=$this->http_request_url_post($api_url,$api_para);
				//echo "<pre>";print_r($api_result);exit;
				$RaceStageList[$k]['ApplyStartTime']=$api_result['RaceStageInfo']['ApplyStartTime'];
				$RaceStageList[$k]['ApplyEndTime']=$api_result['RaceStageInfo']['ApplyEndTime'];
				$RaceStageList[$k]['StageStatus']=$api_result['RaceStageInfo']['RaceStageStatus'];
				
				if($addtime>=$RaceStageList[$k]['ApplyStartTime'] && $addtime<=$RaceStageList[$k]['ApplyEndTime']){
					$RaceStageList[$k]['status_text']='报名中';
				}
				else{
					if($addtime>$RaceStageList[$k]['ApplyEndTime']){
						$RaceStageList[$k]['status_text']='报名结束';
					}
					else{
						$RaceStageList[$k]['status_text']='敬请期待';
					}
				}
				
			}
		}
		//echo "<pre>";print_r($RaceStageList);exit;
		$this->assign('RaceStageList', $RaceStageList);
		
		
		$token_rst=$this->token_member('controller');
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
        $this->assign('curmenu', '7');
        $this->display('stagelist');
    }
    
    
    //赛事介绍公告
    //老版url格式：http://xracebm201607.loc/baoming/intro/9/32/62EED677374C4EEC11ED95D2A03C8072
    //group: http://xracebm201607.loc/baoming/intro/catalog_id/9/stage_id/32/app_token/62EED677374C4EEC11ED95D2A03C8072
	//race:  http://xracebm201607.loc/baoming/intro/catalog_id/1/stage_id/29/app_token/62EED677374C4EEC11ED95D2A03C8072
	public function intro(){
		
		$this_url=$this->get_current_page_url();
		//echo $this_url;exit;
		if(stristr($this_url, 'catalog_id')){
			//新版url格式
			
	    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
			    $catalog_id=$_REQUEST['catalog_id'];
			    $this->assign('catalog_id', $catalog_id);
			}
			else{
			    exit;
			}	
			
			if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
			    $stage_id=$_REQUEST['stage_id'];
			    $this->assign('stage_id', $stage_id);
			}
			else{
			    exit;
			}
			
			if(isset($_REQUEST['app_token']) && !empty($_REQUEST['app_token'])){
			    $app_token=$_REQUEST['app_token'];
			    $this->assign('app_token', $app_token);
			}
			else{
			    $app_token='';
			}
			
		}
		else{
			//老版url格式
			$para_Arr=explode("/", $this_url);
			//echo "<pre>";print_r($para_Arr);exit;
			
			if(isset($para_Arr[5]) && !empty($para_Arr[5])){
			    $catalog_id=$para_Arr[5];
			    $this->assign('catalog_id', $catalog_id);
			}
			else{
			    exit;
			}
			
			if(isset($para_Arr[6]) && !empty($para_Arr[6])){
			    $stage_id=$para_Arr[6];
			    $this->assign('stage_id', $stage_id);
			}
			else{
			    exit;
			}
			
			if(isset($para_Arr[7]) && !empty($para_Arr[7])){
			    $app_token=$para_Arr[7];
			    $this->assign('app_token', $app_token);
			}
			else{
			    $app_token='';
			}
			
		}
		
		
		
		if(isset($app_token) && !empty($app_token)){
			$_SESSION['is_wexin']=0;  //来源于app
		}
		else{
			$_SESSION['is_wexin']=1;  //来源于微信自身
		}
		//echo $_SESSION['is_wexin'];exit;
		
		
		
		$token_rst=$this->token_member('controller',$app_token);
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		//取赛事名字作为title
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.catalog.info&RaceCatalogId='.$catalog_id;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceCatalogInfo = empty($api_result['RaceCatalogInfo'])?array():$api_result['RaceCatalogInfo'];
		//echo "<pre>";print_r($RaceCatalogInfo);exit;
		$_SESSION['RaceCatalogName']=$RaceCatalogInfo['RaceCatalogName'];
		//echo $_SESSION['RaceCatalogName'];exit;
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&UserId='.$userinfo['user_id'];
		//echo $api_url;exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$data['RaceStageInfo'] = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($data['RaceStageInfo']);exit;
		$this->assign('RaceStageInfo', $data['RaceStageInfo']);
		
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('intro');
    }
    
    
    
    
    
    
    //免责申明
	public function statement(){
		
		//echo $_SESSION['app_token'];exit;
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		if(isset($_POST) && !empty($_POST)){
			
			//申明页之后，始终跳到选票页
			$url=U('baoming/ticket', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
			redirect($url);
			exit;
			
			/*
			$need_ticket=1;  //1代表需要显示票入口，0或其他情况代表不需要显示票入口。此标记通过刺猬接口获得
			if($need_ticket==1){
				$url=U('baoming/ticket', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
				redirect($url);
				exit;
			}
			
			
			//跳转到个人/团队二选一的做法：
			//$url=U('baoming/user_type', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
			//redirect($url);
			//exit;
			
			
			//两步合并为一步的做法：
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
			$api_para=array();
			$api_result=$this->http_request_url_post($api_url,$api_para);
			//echo "<pre>";print_r($api_result);exit;
			$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
			//echo "<pre>";print_r($RaceStageInfo);exit;
			$this->assign('RaceStageInfo', $RaceStageInfo);
			$stru=$RaceStageInfo['comment']['RaceStructure'];
			//echo $stru;exit;  //group or race 模式。
			
			if($stru=='group'){
				$url=U('baoming/group_selectlist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
			}
			elseif($stru=='race'){
				$url=U('baoming/race_selectlist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
			}
			else{
				exit;
			}
			
			redirect($url);
			exit;
			*/
			
		}
		
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('statement');
    }
    
    
    
    
    //选单票/通票页
    //示例：http://xracebm201607.loc/baoming/ticket/catalog_id/13/stage_id/29
	public function ticket(){
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		//清空之前所选的票
		if(isset($_SESSION['ticket_type'])){
			unset($_SESSION['ticket_type']);
		}
		
		if(isset($_SESSION['order_no'])){
			unset($_SESSION['order_no']);
		}
		
		
		/*
		$ticket_type=1;
		if(isset($_POST) && !empty($_POST)){
			//echo "<pre>";print_r($_POST);exit;
			
			if($_POST['ticket_type']==1 || $_POST['ticket_type']==2){
				$ticket_type=$_POST['ticket_type'];
			}
			else{
				exit;
			}
			
		}
		*/
		
		//判断是否需要选票：
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
		//echo $api_url;exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		//echo $stru;exit;  //group or race 模式。
		
		
		
		$need_ticket_arr=isset($RaceStageInfo['comment']['PriceList'])?$RaceStageInfo['comment']['PriceList']:array();
		//echo "<pre>";print_r($need_ticket_arr);exit;
		if(!empty($need_ticket_arr)){
			$need_ticket=1;
		}
		else{
			$need_ticket=0;
		}
		
		//$need_ticket=1;  
		//1代表需要显示票入口，0或其他情况代表不需要显示票入口。此标记通过刺猬接口获得。
		if($need_ticket==1){
			//需要选票，呈现页面模板
			
			
		}
		else{
			
			//不需要选票，直接操作后续的：分组-比赛/比赛-分组。
			if($stru=='group'){
				$url=U('baoming/group_selectlist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
			}
			elseif($stru=='race'){
				$url=U('baoming/race_selectlist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
			}
			else{
				exit;
			}
			redirect($url);
			exit;
			
		}
		
		
		
		
		if(isset($_POST) && !empty($_POST)){
			//echo "<pre>";print_r($_POST);exit;
			
			if($_POST['ticket_type']==1 || $_POST['ticket_type']==2){
				$ticket_type=$_POST['ticket_type'];
				$_SESSION['ticket_type']=$ticket_type;
				
			}
			else{
				exit;
			}
			
			
			
			if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
				//所选的是通票
				$url=U('baoming/ticket_2_start', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
				redirect($url);
				exit;
			}
			else{
				//所选的是单票
				if($stru=='group'){
					$url=U('baoming/group_selectlist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
				}
				elseif($stru=='race'){
					$url=U('baoming/race_selectlist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
				}
				else{
					exit;
				}
				redirect($url);
				exit;
			}
			
			
		}
		
		
		$this->assign('curmenu', '7');
        $this->display('ticket');
	}
	
	
	
	//通票情况下跳循环的起始判断页
	//示例：http://xracebm201607.loc/baoming/ticket_start/catalog_id/13/stage_id/29
	public function ticket_2_start(){
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
			//继续循环
			//后续选比赛过程中，如果group_id和race_id重复，则覆盖数据。
			
			
			//判断是否需要选票：
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
			$api_para=array();
			$api_result=$this->http_request_url_post($api_url,$api_para);
			//echo "<pre>";print_r($api_result);exit;
			$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
			//echo "<pre>";print_r($RaceStageInfo);exit;
			$this->assign('RaceStageInfo', $RaceStageInfo);
			$stru=$RaceStageInfo['comment']['RaceStructure'];
			//echo $stru;exit;  //group or race 模式。
			
			if($stru=='group'){
				$url=U('baoming/group_selectlist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
			}
			elseif($stru=='race'){
				$url=U('baoming/race_selectlist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
			}
			else{
				exit;
			}
			redirect($url);
			exit;
			
		}
		else{
			exit;
		}
		
		exit;
		
	}
	
	
	
	
	
    /*
    //group模式，分组列表，个团切换
    示例：http://xracebm201607.loc/baoming/group_selectlist/catalog_id/9/stage_id/32
    */
	public function group_selectlist(){
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		//echo $_SESSION['ticket_type'];exit;
		
		
		//个人标签 分组列表
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&SingleUser=1&TeamUser=0';
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==1){
			$api_url=$api_url.'&RacePriceMode=race';
		}
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
			$api_url=$api_url.'&RacePriceMode=stage';
		}
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		$group_list_1 = empty($api_result['RaceStageInfo']['comment']['SelectedRaceGroup'])?array():$api_result['RaceStageInfo']['comment']['SelectedRaceGroup'];
		//echo "<pre>";print_r($group_list_1);exit;
		
		
		
		
		
		//团队标签 分组列表
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&SingleUser=0&TeamUser=1';
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==1){
			$api_url=$api_url.'&RacePriceMode=race';
		}
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
			$api_url=$api_url.'&RacePriceMode=stage';
		}
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		$group_list_2 = empty($api_result['RaceStageInfo']['comment']['SelectedRaceGroup'])?array():$api_result['RaceStageInfo']['comment']['SelectedRaceGroup'];
		//echo "<pre>";print_r($group_list_2);exit;
		
		
		
		
		//默认user_type为个人
		$user_type=1;
		$user_type_value_1=1;
		$user_type_value_2=2;
		if(empty($group_list_1) && !empty($group_list_2)){
			$user_type=2;
			
			$tmp=$group_list_1;
			$group_list_1=$group_list_2;
			$group_list_2=$tmp;
			
			$user_type_value_1=2;
			$user_type_value_2=1;
		}
		$this->assign('user_type', $user_type);
		$this->assign('group_list_1', $group_list_1);
		$this->assign('group_list_2', $group_list_2);
		$this->assign('user_type_value_1', $user_type_value_1);
		$this->assign('user_type_value_2', $user_type_value_2);
		
		
		if(isset($_POST) && !empty($_POST)){
			
			//echo "<pre>";print_r($_POST);exit;
			
			if($_POST['user_type']==1 || $_POST['user_type']==2){
				$user_type=$_POST['user_type'];
			}
			else{
				exit;
			}
			
			$group_id=$_POST['group_id'];
			
			$url=U('baoming/group_racelist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type  , 'group_id'=>$group_id));
			
			redirect($url);
			exit;
		}
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('group_selectlist');
    }
    
    
    
    
    //race模式，比赛列表，个团切换
    //示例：http://xracebm201607.loc/baoming/race_selectlist/catalog_id/1/stage_id/29
    public function race_selectlist(){
    	
    	//清查订单过期订单
    	$this->checkOrderIsExpire();
    	
    	
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		//获取分站信息及票信息：
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
		//echo $api_url;exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		//echo $stru;exit;  //group or race 模式。
		
		
		$need_ticket_arr=isset($RaceStageInfo['comment']['PriceList'])?$RaceStageInfo['comment']['PriceList']:array();
		//echo "<pre>";print_r($stage_price_arr);exit;
		
		if(!empty($need_ticket_arr)){
			$need_ticket=1;
			$stage_price=!empty($need_ticket_arr[1])?$need_ticket_arr[1]:0;
		}
		else{
			$need_ticket=0;
			$stage_price=0;
		}
		//echo $stage_price;exit;
		
		
		
		
		
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.list&RaceStageId='.$stage_id;
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		$RaceList=$api_result['RaceList'];
		//echo "<pre>";print_r($RaceList);echo "</pre>";exit;
		
		
		
		
		
		//根据user_type是1个人/2团队，以及SingleUser代表支持个人/TeamUser代表支持团队，进行过滤比赛列表。
		$RaceListShaiXuan_1=array();
		$RaceListShaiXuan_2=array();
		if(!empty($RaceList)){
			foreach($RaceList as $k=>$v){
				
				if($v['SingleUser']==1){
					$RaceListShaiXuan_1[$k]=$v;
				}
				
				if($v['TeamUser']==1){
					$RaceListShaiXuan_2[$k]=$v;
				}
				
			}
		}
		//echo "<pre>";print_r($RaceListShaiXuan_1);echo "</pre>";exit;
		//echo "<pre>";print_r($RaceListShaiXuan_2);echo "</pre>";exit;
		
		$now_datetime=date("Y-m-d H:i:s");
		
		
		
		//个人 的比赛列表
		$race_list=array();
		if(!empty($RaceListShaiXuan_1)){
			foreach($RaceListShaiXuan_1 as $k_race=>$v_race){
				//echo "<pre>";print_r($v_race);echo "</pre>";exit;
				
				$race_list[$k_race] = $v_race;
				
				$race_id=$v_race['RaceId'];
				$race_list[$k_race]['disabled']=0;  //0可以选中的比赛，1不可以选中的比赛，如名额不足或其他原因。
				
				//个人用第1个金额
				$race_list[$k_race]['price']=empty($v_race['PriceList'][1])?0:$v_race['PriceList'][1];
				
				
				//通票情况，且价格没拿到的，就用通票价格作为比赛价格
				if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
					if(empty($v_race['PriceList'])){
						$race_list[$k_race]['price']=$stage_price;
					}
				}
				
				
				//单票情况，且价格没拿到的，就用通票价格作为比赛价格（否则价格会显示0）
				if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==1){
					if(empty($v_race['PriceList'])){
						$race_list[$k_race]['price']=$stage_price;
					}
				}
				
				
				
				//获得比赛团队/个人的名额限制总数
				$race_list[$k_race]['limit_number']=$v_race['comment']['SingleUserLimit'];  //总数
				
				
				//计算该比赛的剩余名额，需要考虑到已支付和正在支付的人所占用的名额。
				$and_cond='';
				$cart_type_name='[比赛]';
				$and_cond=$and_cond.' and status=1 ' ;
				$and_cond=$and_cond.' and race_id=' . addslashes($race_id) ;
				$and_cond=$and_cond.' and user_type=1 ';
				$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) ) '  ;
				//echo $and_cond;exit;
				$orderMod = M('order');
		        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        $nums_used=empty($order_data)?0:count($order_data);
		        //var_dump($nums_used);exit;
				
		    	$nums_has = $race_list[$k_race]['limit_number'] - $nums_used ;
		    	$race_list[$k_race]['nums_has']=$nums_has;
		    	
		    	if($nums_used >= $race_list[$k_race]['limit_number'] ) {
		    		$race_list[$k_race]['disabled']=1;
				}
				
				
				//超过比赛报名时间的，不能选
				if($v_race['ApplyStartTime']>$now_datetime || $v_race['ApplyEndTime']<$now_datetime ){
					$race_list[$k_race]['disabled']=1;
				}
				
			}
		}
		//echo "<pre>";print_r($race_list);exit;
		$race_list_1=$race_list;
		
		
		
		
		
		//团队 的比赛列表
		$race_list=array();
		if(!empty($RaceListShaiXuan_2)){
			foreach($RaceListShaiXuan_2 as $k_race=>$v_race){
				//echo "<pre>";print_r($v_race);echo "</pre>";exit;
				
				$race_list[$k_race] = $v_race;
				
				$race_id=$v_race['RaceId'];
				$race_list[$k_race]['disabled']=0;  //0可以选中的比赛，1不可以选中的比赛，如名额不足或其他原因。
				
				//团队用第2个金额
				//$v_race['PriceList'][1]=empty($v_race['PriceList'][1])?0:$v_race['PriceList'][1];
				//$race_list[$k_race]['price']=empty($v_race['PriceList'][2])?$v_race['PriceList'][1]:$v_race['PriceList'][2];
				
				$race_list[$k_race]['price']=empty($v_race['PriceList'][1])?0:$v_race['PriceList'][1];
				
				
				
				//通票情况，且价格没拿到的，就用通票价格作为比赛价格
				if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
					if(empty($v_race['PriceList'])){
						$race_list[$k_race]['price']=$stage_price;
					}
				}
				
				
				
				//单票情况，且价格没拿到的，就用通票价格作为比赛价格（否则价格会显示0）
				if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==1){
					if(empty($v_race['PriceList'])){
						$race_list[$k_race]['price']=$stage_price;
					}
				}
				
				
				
				
				//获得比赛团队/个人的名额限制总数
				$race_list[$k_race]['limit_number']=$v_race['comment']['TeamLimit'];  //总数
				
				
				//计算该比赛的剩余名额，需要考虑到已支付和正在支付的人所占用的名额。
				$and_cond='';
				$cart_type_name='[比赛]';
				$and_cond=$and_cond.' and status=1 ' ;
				$and_cond=$and_cond.' and race_id=' . addslashes($race_id) ;
				$and_cond=$and_cond.' and user_type=2 ';
				$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) ) '  ;
				//echo $and_cond;exit;
				$orderMod = M('order');
		        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        $nums_used=empty($order_data)?0:count($order_data);
		        //var_dump($nums_used);exit;
				
		    	$nums_has = $race_list[$k_race]['limit_number'] - $nums_used ;
		    	$race_list[$k_race]['nums_has']=$nums_has;
		    	
		    	if($nums_used >= $race_list[$k_race]['limit_number'] ) {
		    		$race_list[$k_race]['disabled']=1;
				}
				
				
				//超过比赛报名时间的，不能选
				if($v_race['ApplyStartTime']>$now_datetime || $v_race['ApplyEndTime']<$now_datetime ){
					$race_list[$k_race]['disabled']=1;
				}
				
			}
		}
		//echo "<pre>";print_r($race_list);exit;
		$race_list_2=$race_list;
		
		
		
		
		
		//默认user_type为个人
		$user_type=1;
		$user_type_value_1=1;
		$user_type_value_2=2;
		if(empty($race_list_1) && !empty($race_list_2)){
			$user_type=2;
			
			$tmp=$race_list_1;
			$race_list_1=$race_list_2;
			$race_list_2=$tmp;
			
			$user_type_value_1=2;
			$user_type_value_2=1;
		}
		$this->assign('user_type', $user_type);
		$this->assign('race_list_1', $race_list_1);
		$this->assign('race_list_2', $race_list_2);
		$this->assign('user_type_value_1', $user_type_value_1);
		$this->assign('user_type_value_2', $user_type_value_2);
		
		
		
		if(isset($_POST) && !empty($_POST)){
			
			//echo "<pre>";print_r($_POST);exit;
			
			if($_POST['user_type']==1 || $_POST['user_type']==2){
				$user_type=$_POST['user_type'];
			}
			else{
				exit;
			}
			
			$race_id=$_POST['race_id'];
			
			$url=U('baoming/race_grouplist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type , 'race_id'=>$race_id ));
			

			redirect($url);
			exit;
		}
		
		
    	$this->assign('curmenu', '7');
        $this->display('race_selectlist');
		
    }
    
    
    
    //个人/团队二选一
	public function user_type(){
		
		
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		//获取模式
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		//echo $stru;exit;  //group or race 模式。
		
		
		
		if(isset($_POST) && !empty($_POST)){
			
			if($_POST['user_type']==1 || $_POST['user_type']==2){
				$user_type=$_POST['user_type'];
			}
			else{
				exit;
			}
			
			
			if($stru=='group'){
				$url=U('baoming/group_grouplist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type ));
			}
			elseif($stru=='race'){
				$url=U('baoming/race_racelist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type ));
			}
			else{
				exit;
			}
			
			redirect($url);
			exit;
		}
		
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('user_type');
    }
    
    
    
    
    /*
    //group模式，分组列表
    */
	public function group_grouplist(){
		
		
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==1 || $_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		//group模式下：  
		//个人，通过  http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId=32&SingleUser=1&TeamUser=0 拿  SelectedRaceGroup 并罗列。  
		//团队，通过  http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId=32&SingleUser=0&TeamUser=1 拿  SelectedRaceGroup 并罗列。 
		
		if($user_type==1){
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&SingleUser=1&TeamUser=0';
		}
		else{
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&SingleUser=0&TeamUser=1';
		}
		
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		
		$group_list = empty($api_result['RaceStageInfo']['comment']['SelectedRaceGroup'])?array():$api_result['RaceStageInfo']['comment']['SelectedRaceGroup'];
		//echo "<pre>";print_r($group_list);exit;
		$this->assign('group_list', $group_list);
		
		
		
		if(isset($_POST) && !empty($_POST)){
			
			//echo "<pre>";print_r($_POST);exit;
			$group_id=$_POST['group_id'];
			$url=U('baoming/group_racelist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type  , 'group_id'=>$group_id));
			
			redirect($url);
			exit;
		}
		
		
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('group_grouplist');
    }
    
    
    //group模式 ：比赛列表
    //示例：http://xracebm201607.loc/baoming/group_racelist/catalog_id/9/stage_id/32/user_type/1/group_id/25
    public function group_racelist(){
    	
    	//清查订单过期订单
    	$this->checkOrderIsExpire();
    	
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==1 || $_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    exit;
		}
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		
		//获取分站信息及票信息：
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
		//echo $api_url;exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		//echo $stru;exit;  //group or race 模式。
		
		
		$need_ticket_arr=isset($RaceStageInfo['comment']['PriceList'])?$RaceStageInfo['comment']['PriceList']:array();
		//echo "<pre>";print_r($stage_price_arr);exit;
		
		if(!empty($need_ticket_arr)){
			$need_ticket=1;
			$stage_price=!empty($need_ticket_arr[1])?$need_ticket_arr[1]:0;
		}
		else{
			$need_ticket=0;
			$stage_price=0;
		}
		//echo $stage_price;exit;
		
		
		
		
		
		
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.list&RaceGroupId='.$group_id.'&RaceStageId='.$stage_id;
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		$RaceList=$api_result['RaceList'];
		//echo "<pre>";print_r($RaceList);echo "</pre>";exit;
		
		
		$now_datetime=date("Y-m-d H:i:s");
		//echo $now_datetime;exit;
		$race_list=array();
		
		
		if(!empty($RaceList)){
			foreach($RaceList as $k_race=>$v_race){
				//echo "<pre>";print_r($v_race);echo "</pre>";exit;
				
				$race_list[$k_race] = $v_race;
				
				$race_id=$v_race['RaceId'];
				$race_list[$k_race]['disabled']=0;  //0可以选中的比赛，1不可以选中的比赛，如名额不足或其他原因。
				
				if($user_type==2){
					//团队用第2个金额
					//$v_race['PriceList'][1]=empty($v_race['PriceList'][1])?0:$v_race['PriceList'][1];
					//$race_list[$k_race]['price']=empty($v_race['PriceList'][2])?$v_race['PriceList'][1]:$v_race['PriceList'][2];
					
					$race_list[$k_race]['price']=empty($v_race['PriceList'][1])?0:$v_race['PriceList'][1];
				}
				else{
					//个人用第1个金额
					$race_list[$k_race]['price']=empty($v_race['PriceList'][1])?0:$v_race['PriceList'][1];
				}
				
				
				
				
				//通票情况，且价格没拿到的，就用通票价格作为比赛价格
				if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
					if(empty($v_race['PriceList'])){
						$race_list[$k_race]['price']=$stage_price;
					}
				}
				
				
				
				
				
				
				
				//获得比赛团队/个人的名额限制总数
				if($user_type==2){
					//团队
					$race_list[$k_race]['limit_number']=$v_race['comment']['TeamLimit'];  //总数
				}
				else{
					//个人
					$race_list[$k_race]['limit_number']=$v_race['comment']['SingleUserLimit'];  //总数
				}
				
				
				
				//计算该比赛的剩余名额，需要考虑到已支付和正在支付的人所占用的名额。
				$and_cond='';
				$cart_type_name='[比赛]';
				$and_cond=$and_cond.' and status=1 ' ;
				$and_cond=$and_cond.' and race_id=' . addslashes($race_id) ;
				$and_cond=$and_cond.' and user_type=' . addslashes($user_type) ;
				$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) ) '  ;
				//echo $and_cond;exit;
				$orderMod = M('order');
		        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        $nums_used=empty($order_data)?0:count($order_data);
		        //var_dump($nums_used);exit;
				
		    	$nums_has = $race_list[$k_race]['limit_number'] - $nums_used ;
		    	$race_list[$k_race]['nums_has']=$nums_has;
		    	
		    	if($nums_used >= $race_list[$k_race]['limit_number'] ) {
		    		$race_list[$k_race]['disabled']=1;
				}
				
				
				
				//echo $now_datetime;exit;
				//超过比赛报名时间的，不能选
				if($v_race['ApplyStartTime']>$now_datetime || $v_race['ApplyEndTime']<$now_datetime ){
					$race_list[$k_race]['disabled']=1;
				}
				
				
			}
		}
		
		//echo "<pre>";print_r($race_list);exit;
		$this->assign('race_list', $race_list);
		
		
		if(isset($_POST) && !empty($_POST)){
			
			
			//echo "<pre>";print_r($_POST);exit;
			
			//$race_id=$_POST['race_arr'];
			$race_id=$_POST['race_id'];
			
			if($user_type==1){
				$url=U('baoming/signup_1', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type  , 'group_id'=>$group_id , 'race_id'=>$race_id ));
			}
			elseif($user_type==2){
				$url=U('baoming/signup_2', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type  , 'group_id'=>$group_id , 'race_id'=>$race_id ));
			}
			else{
				exit;
			}
			redirect($url);
			exit;
		}
		
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('group_racelist');
    }
    
    
    
    //race模式：比赛列表
    //示例：http://xracebm201607.loc/baoming/race_racelist/catalog_id/1/stage_id/29/user_type/1
    public function race_racelist(){
    	
    	//清查订单过期订单
    	$this->checkOrderIsExpire();
    	
    	
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		
		if($_REQUEST['user_type']==1 || $_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.list&RaceStageId='.$stage_id;
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		$RaceList=$api_result['RaceList'];
		//echo "<pre>";print_r($RaceList);echo "</pre>";exit;
		
		
		
		
		//根据user_type是1个人/2团队，以及SingleUser代表支持个人/TeamUser代表支持团队，进行过滤比赛列表。
		$RaceListShaiXuan=array();
		if(!empty($RaceList)){
			foreach($RaceList as $k=>$v){
				if($user_type==1){
					if($v['SingleUser']==1){
						$RaceListShaiXuan[$k]=$v;
					}
				}
				elseif($user_type==2){
					if($v['TeamUser']==1){
						$RaceListShaiXuan[$k]=$v;
					}
				}
				else{
				}
			}
		}
		//echo "<pre>";print_r($RaceListShaiXuan);echo "</pre>";exit;
		
		
		$now_datetime=date("Y-m-d H:i:s");
		
		$race_list=array();
		
		
		if(!empty($RaceListShaiXuan)){
			foreach($RaceListShaiXuan as $k_race=>$v_race){
				//echo "<pre>";print_r($v_race);echo "</pre>";exit;
				
				$race_list[$k_race] = $v_race;
				
				$race_id=$v_race['RaceId'];
				$race_list[$k_race]['disabled']=0;  //0可以选中的比赛，1不可以选中的比赛，如名额不足或其他原因。
				
				
				if($user_type==2){
					//团队用第2个金额
					$v_race['PriceList'][1]=empty($v_race['PriceList'][1])?0:$v_race['PriceList'][1];
					$race_list[$k_race]['price']=empty($v_race['PriceList'][2])?$v_race['PriceList'][1]:$v_race['PriceList'][2];
				}
				else{
					//个人用第1个金额
					$race_list[$k_race]['price']=empty($v_race['PriceList'][1])?0:$v_race['PriceList'][1];
				}
				
				
				//获得比赛团队/个人的名额限制总数
				if($user_type==2){
					//团队
					$race_list[$k_race]['limit_number']=$v_race['comment']['TeamLimit'];  //总数
				}
				else{
					//个人
					$race_list[$k_race]['limit_number']=$v_race['comment']['SingleUserLimit'];  //总数
				}
				
				
				
				//计算该比赛的剩余名额，需要考虑到已支付和正在支付的人所占用的名额。
				$and_cond='';
				$cart_type_name='[比赛]';
				$and_cond=$and_cond.' and status=1 ' ;
				$and_cond=$and_cond.' and race_id=' . addslashes($race_id) ;
				$and_cond=$and_cond.' and user_type=' . addslashes($user_type) ;
				$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) ) '  ;
				//echo $and_cond;exit;
				$orderMod = M('order');
		        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        $nums_used=empty($order_data)?0:count($order_data);
		        //var_dump($nums_used);exit;
				
		    	$nums_has = $race_list[$k_race]['limit_number'] - $nums_used ;
		    	$race_list[$k_race]['nums_has']=$nums_has;
		    	
		    	if($nums_used >= $race_list[$k_race]['limit_number'] ) {
		    		$race_list[$k_race]['disabled']=1;
				}
				
				
				//超过比赛报名时间的，不能选
				if($v_race['ApplyStartTime']>$now_datetime || $v_race['ApplyEndTime']<$now_datetime ){
					$race_list[$k_race]['disabled']=1;
				}
				
			}
		}
		
		//echo "<pre>";print_r($race_list);exit;
		$this->assign('race_list', $race_list);
		
		
		if(isset($_POST) && !empty($_POST)){
			
			//echo "<pre>";print_r($_POST);exit;
			$race_id=$_POST['race_arr'];
			
            //$url="baoming/part/".$catalog_id."/".$stage_id."/".$group_id_str."/".$_POST['race_id_str'];
			
			$url=U('baoming/race_grouplist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type , 'race_id'=>$race_id ));
			

			redirect($url);
			exit;
		}
		
		
    	$this->assign('curmenu', '7');
        $this->display('race_racelist');
		
    }
    
    
    
    
    //race模式：分组列表
    //有性别年龄限制的race模式比赛-组：http://xracebm201607.loc/baoming/signup_2/catalog_id/13/stage_id/29/user_type/2/group_id/53/race_id/89
    public function race_grouplist(){
    	
    	
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		
		if($_REQUEST['user_type']==1 || $_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    exit;
		}
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$race_id;
		//echo $api_url;exit;
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==1){
			$api_url=$api_url.'&RacePriceMode=race';
		}
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
			$api_url=$api_url.'&RacePriceMode=stage';
		}
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);echo "</pre>";exit;
		$group_list=$api_result['RaceInfo']['comment']['SelectedRaceGroup'];
		//echo "<pre>";print_r($group_list);echo "</pre>";exit;
		$this->assign('group_list', $group_list);
		
		
		
		
		if(isset($_POST) && !empty($_POST)){
			
			$group_id=$_POST['group_id'];
			
			//如果客户要求暂存分站id、分组id、比赛id，则考虑存session或临时存下数据表。
			
			//echo "<pre>";print_r($_POST);exit;
			//如果客户要求暂存分站id、分组id、比赛id，则考虑存session或临时存下数据表。
			
			if($user_type==1){
				$url=U('baoming/signup_1', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type  , 'group_id'=>$group_id , 'race_id'=>$race_id ));
			}
			elseif($user_type==2){
				$url=U('baoming/signup_2', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type  , 'group_id'=>$group_id , 'race_id'=>$race_id ));
			}
			else{
				exit;
			}
			
			

			redirect($url);
			exit;
		}
		
		
    	$this->assign('curmenu', '7');
        $this->display('race_grouplist');
		
    }
	
	
	//个人 报名信息 
	//group: http://xracebm201607.loc/baoming/signup_1/catalog_id/9/stage_id/32/user_type/1/group_id/25/race_id/79
	//race:  http://xracebm201607.loc/baoming/signup_1/catalog_id/1/stage_id/29/user_type/1/group_id/36/race_id/64
	public function signup_1(){
		
		
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==1){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    exit;
		}
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		
		
        if($userinfo['id_type']=='1'){
        	$userinfo['id_type_show']='身份证';
        }
        elseif($userinfo['id_type']=='2'){
        	$userinfo['id_type_show']='护照';
        }
        elseif($userinfo['id_type']=='3'){
        	$userinfo['id_type_show']='港澳台地区证件';
        }
        else{
         	$userinfo['id_type_show']='港澳台地区证件';
        }
        
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$race_id;
		$api_url=$api_url.'&RacePriceMode=race';
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$raceInfo=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
		
		$detail_tmp['nums_single_total']=(int)$raceInfo['RaceInfo']['comment']['SingleUserLimit'];  //总数
		$detail_tmp['nums_team_total']=(int)$raceInfo['RaceInfo']['comment']['TeamLimit'];  //总数
		
		
		
		if($detail_tmp['nums_team_total']>0){
			$show_team=1;
		}
		else{
			$show_team=0;
		}
		//$show_team=1;  //debug 调试车队的时候，强制写1
		$this->assign('show_team', $show_team);
		
		
		
		
		//车队
		//$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/get_user_teams.json?match_id='.$catalog_id.'&group_id='.$group_id.'&token='.$_SESSION['app_token'];
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/get_wxteams.json?token='.$_SESSION['app_token'];
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//var_dump($api_result);exit;
		//echo "<pre>";print_r($api_result);echo "</pre>";exit;
	    $chedui_list = empty($api_result['wxTeams'])?array():$api_result['wxTeams'];
		//echo "<pre>";print_r($chedui_list);echo "</pre>";exit;
		$this->assign('chedui_list', $chedui_list);
		
		$chedui_str='';
		if(!empty($chedui_list)){
			foreach($chedui_list as $k=>$v){
				
				if($k>0){
					$chedui_str=$chedui_str.",";
				}
				$chedui_str=$chedui_str."{value: '".$v['teamId']."',text: '".$v['teamName']."'}";
				
			}
		}
		$this->assign('chedui_str', $chedui_str);
		
		
    	$this->assign('curmenu', '7');
        $this->display('signup_1');
    }

	
	
	//个人 报名信息 提交
	public function signup_1_sub(){
		
		//echo "<pre>";print_r($_POST);exit;
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		if($_REQUEST['user_type']==1){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			$return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		//清查订单过期订单
		$this->checkOrderIsExpire();
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		//此处通过app接口拿用户基本个人信息，其他附加信息拿post过来的。如没登陆或登陆超时，ajax情况下，需要通知用户。
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		
		
		if(isset($_POST['realname']) && !empty($_POST['realname'])){
		}
		else{
		    $return['success']='请输入姓名';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(isset($_POST['sex']) && !empty($_POST['sex'])){
		}
		else{
		    $return['success']='请输入性别';
	        echo json_encode($return);
	        exit;
		}
		
		if(isset($_POST['birth_day']) && !empty($_POST['birth_day'])){
		}
		else{
		    $return['success']='请输入出生年月';
	        echo json_encode($return);
	        exit;
		}
		
		$is_birthday=strtotime($_POST['birth_day']." 00:00:00");
		if($is_birthday==false){
		    $return['success']='出生年月格式错误';
	        echo json_encode($return);
	        exit;
		}
		
        
        if($_POST['id_type']=='身份证'){
        	$id_type=1;
        }
        elseif($_POST['id_type']=='护照'){
        	$id_type=2;
        }
        elseif($_POST['id_type']=='港澳台地区证件'){
        	$id_type=3;
        }
        else{
         	$return['success']='请选择证件类型';
	        echo json_encode($return);
	        exit;
        }
        
        //echo "<pre>";print_r($_POST);exit;
        $is_id_number=$this->checkIdCard($_POST['id_number']);
        if($id_type==1 && !$is_id_number) {
        	$return['success']='请填写正确的身份证号';
	        echo json_encode($return);
	        exit;
        }
        
        
        
        $prov_city_arr=explode(" ", $_POST['prov_city']);
		$m_province=$prov_city_arr[0];
		$m_city=$prov_city_arr[1];
		$m_district=$prov_city_arr[2];
		
		
		
        $amount_total=0;
        
        
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.catalog.info&RaceCatalogId='.$catalog_id;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$catalogInfo = empty($api_result)?array():$api_result;
		//echo "<pre>";print_r($catalogInfo);exit;
		
		
		
		
		
		if($user_type==1){
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&SingleUser=1&TeamUser=0';
		}
		else{
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&SingleUser=0&TeamUser=1';
		}
		
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$stageInfo = empty($api_result)?array():$api_result;
		//echo "<pre>";print_r($stageInfo);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		//echo $stru;exit;
		$group_list = empty($api_result['RaceStageInfo']['comment']['SelectedRaceGroup'])?array():$api_result['RaceStageInfo']['comment']['SelectedRaceGroup'];
		//echo "<pre>";print_r($group_list);exit;
		
		
		
		if($stru=='group'){
			//group模式
			$stru_id=1;
		}
		else{
			//race模式
			$stru_id=2;
		}
		
		
		
		
		$need_ticket_arr=isset($RaceStageInfo['comment']['PriceList'])?$RaceStageInfo['comment']['PriceList']:array();
		//echo "<pre>";print_r($need_ticket_arr);exit;
		
		if(!empty($need_ticket_arr)){
			$need_ticket=1;
			$stage_price=!empty($need_ticket_arr[1])?$need_ticket_arr[1]:0;
		}
		else{
			$need_ticket=0;
			$stage_price=0;
		}
		//echo $stage_price;exit;
		
		
		
		
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$race_id;
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$raceInfo=$this->http_request_url_post($api_url,$api_para);
	    //echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
		
		
		
		
		
		
		//判断生日和性别
		if($stru=='group'){
			//group模式
			$license_arr=$group_list[$group_id]['comment']['LicenseList'];
		}
		else{
			//race模式
			$license_arr=$raceInfo['RaceInfo']['comment']['SelectedRaceGroup'][$group_id]['comment']['LicenseList'];
		}
		
		//debug，强制指定LicenseList
		//$license_arr=$raceInfo['RaceInfo']['comment']['SelectedRaceGroup'][2]['LicenseList'];  //接口有问题，手动构造数据
		//$license_arr=$raceInfo['RaceInfo']['comment']['SelectedRaceGroup'][3]['LicenseList'];  //接口有问题，手动构造数据
		//echo "<pre>";print_r($raceInfo['RaceInfo']['comment']['SelectedRaceGroup']);exit;  //接口有问题，手动构造数据
		//echo "<pre>";print_r($license_arr);exit;
		
		
		
		//证件类型选身份证的，用身份证提取性别和生日；否则采用填写的性别和生日。
		if($id_type==1){
			$idcard_sex=$this->get_xingbie($_POST['id_number']);   //男or女
			$idcard_birth_arr=$this->get_idcard_birth($_POST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			
			$verify_sex=$idcard_sex;
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
		}
		else{
			$verify_sex=$_POST['sex'];
			$verify_birth=str_replace('-','',$_POST['birth_day']);
			$verify_birthday=$_POST['birth_day'];
		}
		//echo $verify_birthday;exit;
		
		
		
		//身份证与性别生日是否符合
		if($id_type==1){
			$idcard_sex=$this->get_xingbie($_POST['id_number']);   //男or女
			if($idcard_sex!=$_POST['sex']){
				$return['success']='性别与身份证信息不符';
		        echo json_encode($return);
		        exit;
			}
			
			$idcard_birth_arr=$this->get_idcard_birth($_POST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
			if($verify_birthday!=$_POST['birth_day']){
				$return['success']='出生年月与身份证信息不符';
		        echo json_encode($return);
		        exit;
			}
		}
		
		
		
		//年龄跟哪个时间做比较
		$verify_date=$raceInfo['RaceInfo']['ApplyStartTime'];
		$verify_date=substr($verify_date,0,10);
		$verify_date=str_replace('-','',$verify_date);
		
		$verify_cycle=$verify_date-$verify_birth;
		$verify_age=floor($verify_cycle/10000);  //舍去法取整，取实足周岁。
		//echo $verify_age;exit;
		
		if(!empty($license_arr)){
			foreach($license_arr as $k=>$v){
				
				
				if($v['LicenseType']=='sex'){
					if($v['License']==1 && $verify_sex!='男'){
						$return['success']='比赛仅限男性参赛';
				        echo json_encode($return);
				        exit;
					}
					if($v['License']==2 && $verify_sex!='女'){
						$return['success']='比赛仅限女性参赛';
				        echo json_encode($return);
				        exit;
					}
				}
				
				if($v['LicenseType']=='age'){
					if($v['License']['equal']=='<='){
						//年龄小于等于某个数字
						if($verify_age>$v['License']['Age']){
							$return['success']='年龄需要小于等于'.$v['License']['Age'];
					        echo json_encode($return);
					        exit;
						}
					}
					
					if($v['License']['equal']=='>='){
						//年龄大于等于某个数字
						if($verify_age<$v['License']['Age']){
							$return['success']='年龄需要大于等于'.$v['License']['Age'];
					        echo json_encode($return);
					        exit;
						}
					}
					
					if($v['License']['equal']=='<'){
						//年龄小于某个数字
						if($verify_age>=$v['License']['Age']){
							$return['success']='年龄需要小于'.$v['License']['Age'];
					        echo json_encode($return);
					        exit;
						}
					}
					
					if($v['License']['equal']=='>'){
						//年龄大于某个数字
						if($verify_age<=$v['License']['Age']){
							$return['success']='年龄需要大于'.$v['License']['Age'];
					        echo json_encode($return);
					        exit;
						}
					}
					
				}
				
				
				if($v['LicenseType']=='birthday'){
					if($v['License']['equal']=='<=' && ($verify_birthday>$v['License']['Date']) ){
						$return['success']='生日需要小于等于'.$v['License']['Date'];
				        echo json_encode($return);
				        exit;
					}
					
					if($v['License']['equal']=='>=' && ($verify_birthday<$v['License']['Date']) ){
						$return['success']='生日需要大于等于'.$v['License']['Date'];
				        echo json_encode($return);
				        exit;
					}
					
					
					if($v['License']['equal']=='<' && ($verify_birthday>=$v['License']['Date']) ){
						$return['success']='生日需要小于'.$v['License']['Date'];
				        echo json_encode($return);
				        exit;
					}
					
					if($v['License']['equal']=='>' && ($verify_birthday<=$v['License']['Date']) ){
						$return['success']='生日需要大于'.$v['License']['Date'];
				        echo json_encode($return);
				        exit;
					}
					
					
				}
				
			}
		}
		
		
		
		/*
		if($stru=='group'){
			//group模式
			$license_arr=$group_list[$group_id]['comment']['LicenseList'];
		}
		else{
			//race模式
			$license_arr=$raceInfo['RaceInfo']['comment']['SelectedRaceGroup'][$group_id]['LicenseList'];
		}
		
		//echo $_POST['sex'];exit;  //男、女
		//echo $_POST['birth_day'];exit;  //2016-05-01
		if(!empty($license_arr)){
			foreach($license_arr as $k=>$v){
				if($v['LicenseType']=='sex'){
					if($v['License']==1 && $_POST['sex']!='男'){
						$return['success']='比赛仅限男性参赛';
				        echo json_encode($return);
				        exit;
					}
					if($v['License']==2 && $_POST['sex']!='女'){
						$return['success']='比赛仅限女性参赛';
				        echo json_encode($return);
				        exit;
					}
				}
				if($v['LicenseType']=='birthday'){
					if($v['License']['equal']=='<=' && ($_POST['birth_day']<=$v['License']['Date']) ){
						//生日小于等于某天
					}
					else{
						$return['success']='生日需要小于等于'.$v['License']['Date'];
				        echo json_encode($return);
				        exit;
					}
					
					if($v['License']['equal']=='>=' && ($_POST['birth_day']>=$v['License']['Date']) ){
						//生日大于等于某天
					}
					else{
						$return['success']='生日需要大于等于'.$v['License']['Date'];
				        echo json_encode($return);
				        exit;
					}
					
					if($v['License']['equal']=='<' && ($_POST['birth_day']<$v['License']['Date']) ){
						//生日小于某天
					}
					else{
						$return['success']='生日需要小于'.$v['License']['Date'];
				        echo json_encode($return);
				        exit;
					}
					
					if($v['License']['equal']=='>' && ($_POST['birth_day']>$v['License']['Date']) ){
						//生日大于某天
					}
					else{
						$return['success']='生日需要大于'.$v['License']['Date'];
				        echo json_encode($return);
				        exit;
					}
					
				}
			}
		}
		*/
		
		
		
		$detail_tmp=array();
		
		if(isset($raceInfo['RaceInfo']['RaceId']) && $raceInfo['RaceInfo']['RaceId']>0){
			$detail_tmp['race_id']=$raceInfo['RaceInfo']['RaceId'];
			$detail_tmp['race_name']=$raceInfo['RaceInfo']['RaceName'];
		}
		else{
			$return['success']='请先选择比赛';
	        echo json_encode($return);
	        exit;
		}
		
		if(isset($catalogInfo['RaceStageList'][$stage_id]['RaceStageName']) && $catalogInfo['RaceStageList'][$stage_id]['RaceStageName']!=''){
			$detail_tmp['catalog_id']=$catalogInfo['RaceCatalogInfo']['RaceCatalogId'];
			$detail_tmp['catalog_name']=$catalogInfo['RaceCatalogInfo']['RaceCatalogName'];
			
			$detail_tmp['stage_id']=$stage_id;
			$detail_tmp['stage_name']=$catalogInfo['RaceStageList'][$stage_id]['RaceStageName'];
		}
		else{
			$return['success']='请先选择比赛';
	        echo json_encode($return);
	        exit;
		}
		
		
		if($stru=='group'){
			//group模式
			if(isset($group_list[$group_id]['RaceGroupName']) && $group_list[$group_id]['RaceGroupName']!=''){
				$detail_tmp['group_id']=$group_id;
				$detail_tmp['group_name']=$group_list[$group_id]['RaceGroupName'];
			}
			else{
				$return['success']='请先选择比赛';
		        echo json_encode($return);
		        exit;
			}
		}
		else{
			//race模式
			$group_list=$raceInfo['RaceInfo']['comment']['SelectedRaceGroup'];
			if(isset($group_list[$group_id]['RaceGroupName']) && $group_list[$group_id]['RaceGroupName']!=''){
				$detail_tmp['group_id']=$group_id;
				$detail_tmp['group_name']=$group_list[$group_id]['RaceGroupName'];
			}
			else{
				$return['success']='请先选择比赛';
		        echo json_encode($return);
		        exit;
			}
		}
		
		
		//echo "<pre>";print_r($detail_tmp);exit;
		
		
		
		
		//echo "<pre>";print_r($raceInfo);exit;
		if($user_type==2){
			//团队用第2个金额
			//$raceInfo['RaceInfo']['PriceList'][1]=empty($raceInfo['RaceInfo']['PriceList'][1])?0:$raceInfo['RaceInfo']['PriceList'][1];
			//$detail_tmp['price']=empty($raceInfo['RaceInfo']['PriceList'][2])?$raceInfo['RaceInfo']['PriceList'][1]:$raceInfo['RaceInfo']['PriceList'][2];
			$detail_tmp['price']=empty($raceInfo['RaceInfo']['PriceList'][1])?0:$raceInfo['RaceInfo']['PriceList'][1];
		}
		else{
			//个人用第1个金额
			$detail_tmp['price']=empty($raceInfo['RaceInfo']['PriceList'][1])?0:$raceInfo['RaceInfo']['PriceList'][1];
		}
		
		$detail_tmp['number']='1';
		$detail_tmp['price_race']=$detail_tmp['price']*$detail_tmp['number'];
		
		
		
		
		//通票情况下，不是精英的，用通票价格
		$price_type=1;
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
			if(empty($raceInfo['RaceInfo']['PriceList'])){
				$detail_tmp['price_race']=$stage_price;
				$price_type=2;
			}
		}
		//echo $detail_tmp['price_race'];exit;
		
		
		
		
		$amount_total=$detail_tmp['price_race'];  //暂且将比赛总价视作订单付款总价，后面如果选购了产品，再更新amount_total
		$detail_tmp['addtime']=$addtime;
		
		//echo $user_type;exit;
		//获得比赛团队/个人的名额限制总数
		if($user_type==2){
			//团队
			$detail_tmp['limit_number']=$raceInfo['RaceInfo']['comment']['TeamLimit'];  //总数
		}
		else{
			//个人
			$detail_tmp['limit_number']=$raceInfo['RaceInfo']['comment']['SingleUserLimit'];  //总数
		}
		
		//echo "<pre>";print_r($detail_tmp);exit;
		
		
		
	    
		//比赛是否还有剩余名额，需要考虑到已支付和正在支付的人所占用的名额。
		$and_cond='';
		$cart_type_name='[比赛]';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and race_id=' . addslashes($race_id) ;
		$and_cond=$and_cond.' and user_type=' . addslashes($user_type) ;
		$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) ) '  ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $nums_used=empty($order_data)?0:count($order_data);
        //var_dump($nums_used);exit;
		
    	$nums_has = $detail_tmp['limit_number'] - $nums_used ;
    	if($nums_used >= $detail_tmp['limit_number'] ) {
    		$return['success']=$cart_type_name.$detail_tmp['race_name'].'名额不足';
	        echo json_encode($return);
	        exit;
		}
		
        
		
		//更新用户信息。我个人觉得不合理，但客户要求此步需要修改个人信息
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/set_user_profile.json?token='.$_SESSION['app_token'];
		$api_para=array();
		
		$api_para['name']=$_POST['realname'];
		
		if($_POST['sex']=='男'){$sex=1;}
		if($_POST['sex']=='女'){$sex=2;}
		
		$api_para['id_type']=$id_type;
		$api_para['id_number']=$_POST['id_number'];
		//$api_para['expire_day']=$_POST['expire_day'];
		$api_para['birth_day']=$_POST['birth_day'];
		$api_para['sex']=$sex;
		$api_para['province']=$m_province;
		$api_para['city']=$m_city;
		$api_para['address']=$_POST['address'];
		
		$api_para['ec_name']=$_POST['ec_name'];
		//$api_para['ec_relation']=$_POST['ec_relation'];
		$api_para['ec_phone1']=$_POST['ec_phone1'];
		//$api_para['ec_phone2']=$_POST['ec_phone2'];
		$api_para['token']=$_SESSION['app_token'];
		//echo "<pre>";print_r($api_para);exit;
		
		$api_result=$this->http_request_url_post($api_url,$api_para);
	    $user_update_rst = empty($api_result)?array():$api_result;
		//echo "<pre>";print_r($user_update_rst);exit;
		
		if(isset($user_update_rst['user_id']) && $user_update_rst['user_id']>0){
		}
		else{
			$return['success']='个人信息提交失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		$orderMod = M('order');
        $orderMod->status=0;
        $order_id = $orderMod->add();
        //var_dump($order_id);exit;
        
		// , expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->orderExpireTime) )."'   此处未形成订单，先不写过期时间，放到选购产品后面再写过期时间。
		//, expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->signupExpireTime) )."' 
		$orderMod = M('order');
	    $sql=sprintf("update %s SET price_race='".addslashes($detail_tmp['price_race'])."' 
	    , price_type='".addslashes($price_type)."' 
	    , amount_total='".addslashes($amount_total)."' 
	    , isPay='0' 
	    , isExpire='0' 
	    , createDateTime='".addslashes($addtime)."' 
	    , member_id='".addslashes($userinfo['user_id'])."' 
	    , catalog_id='".addslashes($detail_tmp['catalog_id'])."' 
	    , catalog_name='".addslashes($detail_tmp['catalog_name'])."' 
	    , stage_id='".addslashes($detail_tmp['stage_id'])."' 
	    , stage_name='".addslashes($detail_tmp['stage_name'])."' 
	    , group_id='".addslashes($detail_tmp['group_id'])."' 
	    , group_name='".addslashes($detail_tmp['group_name'])."' 
	    , race_id='".addslashes($detail_tmp['race_id'])."' 
	    , race_name='".addslashes($detail_tmp['race_name'])."' 
	    , stru_id='".addslashes($stru_id)."' 
	    , user_type='".addslashes($user_type)."' 
	    , m_realname='".addslashes($_POST['realname'])."' 
	    , m_mobile='".addslashes($userinfo['phone'])."' 
	    , m_sex='".addslashes($sex)."' 
	    , m_id_type='".addslashes($id_type)."' 
	    , m_id_number='".addslashes($_POST['id_number'])."' 
	    , m_birth_day='".addslashes($_POST['birth_day'])."' 
	    , m_province='".addslashes($m_province)."' 
	    , m_city='".addslashes($m_city)."' 
	    , m_district='".addslashes($m_district)."' 
	    , m_address='".addslashes($_POST['address'])."' 
	    , m_email='".addslashes($_POST['email'])."' 
	    , m_ec_name='".addslashes($_POST['ec_name'])."' 
	    , m_ec_phone1='".addslashes($_POST['ec_phone1'])."' 
	    , chedui_name='".addslashes($_POST['chedui_name'])."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    $this->set_log_sql($sql);
	    
	    
	    if(isset($_SESSION['ticket_type'])){
	    	$sql=sprintf("update %s SET ticket_type='".addslashes($_SESSION['ticket_type'])."' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
	    }
	    
	    
	    setcookie("order_id", $order_id , time()+$this->cookieExpireTime, "/");
	    $order_no='';
	    
	    /*
	    //生成order_no
	    $rand_number=mt_rand(100000, 999999);
		$order_no = date("ymdHis").$rand_number;
    	//echo $order_no;exit;
    	
    	$sql=sprintf("update %s SET order_no='".addslashes($order_no)."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    */
	    
	    
	    
	    
	    
        $return = array(
            'order_id' => $order_id,
            'order_no' => $order_no,
            'url_param' => $order_no,
            'amount_total' => $amount_total
        );
		$return['success']='success';
		//echo "<pre>";print_r($return);exit;
		
        echo json_encode($return);
        exit;
        
		
	}
	
	
	
	
	
	
	//团队 报名信息 
	//group: http://xracebm201607.loc/baoming/signup_2/catalog_id/9/stage_id/32/user_type/2/group_id/25/race_id/79
	//race:  http://xracebm201607.loc/baoming/signup_2/catalog_id/1/stage_id/29/user_type/2/group_id/36/race_id/64
	public function signup_2(){
		
		
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    exit;
		}
		
		
		
		
		
		if(isset($_REQUEST['team_id']) && !empty($_REQUEST['team_id'])){
		    $team_id=$_REQUEST['team_id'];
		}
		else{
		    $team_id='';
		}
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		
		
		
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/get_wxteams.json?token='.$_SESSION['app_token'];
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$team_list=$this->http_request_url_post($api_url,$api_para);
		$team_list=empty($team_list['wxTeams'])?array():$team_list['wxTeams'];
		//echo "<pre>";print_r($team_list);echo "</pre>";exit;
		
		
		//获得队伍列表
		$team_arr=array();
		if(!empty($team_list)){
			foreach($team_list as $k=>$v){
				
				if(empty($team_id) && stristr($v['teamId'],'T') ){
					$team_id=$v['teamId'];
				}
				
				$team_arr[$v['teamId']]=$v;
			}
		}
		//echo "<pre>";print_r($team_arr);echo "</pre>";exit;
		
		$team_info=$team_arr[$team_id];
		$team_name=$team_arr[$team_id]['teamName'];
		//echo "<pre>";print_r($team_info);echo "</pre>";exit;
		
		$this->assign('team_arr', $team_arr);   //所有团队列表
		$this->assign('team_id', $team_id);  //当前选择的这个团队id
		$this->assign('team_name', $team_name);   //当前选择的这个团队名字
		$this->assign('team_info', $team_info);   //当前选择的这个团队信息
			
		
		//获得当前队伍的成员列表
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/get_wxteam_members.json?token='.$_SESSION['app_token'];
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		//$team_id=317;
		$api_para['team_id']=$team_id;
		$team_member_list=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($team_member_list);echo "</pre>";exit;
		$team_member_list=empty($team_member_list['wxTeamMembers'])?array():$team_member_list['wxTeamMembers'];
		
		if(!empty($team_member_list)){
			foreach($team_member_list as $k=>$v){
				if($v['idType']==1){
					$team_member_list[$k]['idType_show']='身份证';
				}
				elseif($v['idType']==2){
					$team_member_list[$k]['idType_show']='护照';
				}
				elseif($v['idType']==3){
					$team_member_list[$k]['idType_show']='港澳台地区证件';
				}
				else{
					$team_member_list[$k]['idType_show']='';
				}
			}
		}
		
		//echo "<pre>";print_r($team_member_list);echo "</pre>";exit;
		$this->assign('team_member_list', $team_member_list);
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		
		/*
		if(isset($_POST) && !empty($_POST)){
			
			//echo "<pre>";print_r($_POST);exit;
			
			
			$order_teamMod = M('order_team');
	        $sql=sprintf("DELETE FROM %s 
	        where member_id='".addslashes($userinfo['user_id'])."' and order_id=0 ", $order_teamMod->getTableName() );
	        //echo $sql;exit;
	        $result = $order_teamMod->execute($sql);
	        
	        
			$member_list=explode("|+|", $_POST['member_str']);
			//echo "<pre>";print_r($member_list);exit;
			if(!empty($member_list)){
				foreach($member_list as $k=>$v){
					$member_field=explode("|*|", $v);
					//echo "<pre>";print_r($member_field);exit;
					
			        $sql=sprintf("INSERT %s SET 
			         member_id='".addslashes($userinfo['user_id'])."' 
			        , t_realname='".addslashes($member_field[0])."' 
			        , t_sex='".addslashes($member_field[1])."' 
			        , t_mobile='".addslashes($member_field[4])."' 
			        , t_id_number='".addslashes($member_field[2])."' 
			        , t_birth_day='".addslashes($member_field[3])."' 
			        , addtime='".$addtime."' 
			        ", $order_teamMod->getTableName() );
			        //echo $sql;exit;
			        $result = $order_teamMod->execute($sql);
			        
				}
			}
			
			echo "aaaa";exit;
			
			if($stru=='group'){
				$url=U('baoming/group_grouplist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type ));
			}
			elseif($stru=='race'){
				$url=U('baoming/race_racelist', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id , 'user_type'=>$user_type ));
			}
			else{
				exit;
			}
			
			redirect($url);
			exit;
		}
		*/
		
		
    	$this->assign('curmenu', '7');
        $this->display('signup_2');
    }
	
	
	
	
	
	//团队 报名信息 提交
	public function signup_2_sub(){
		
		//echo "<pre>";print_r($_POST);exit;
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}	
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		if($_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			$return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		if(isset($_REQUEST['team_id']) && !empty($_REQUEST['team_id'])){
		    $team_id=$_REQUEST['team_id'];
		}
		else{
		    $team_id='';
		}
		
		
		
		if(isset($_REQUEST['team_name']) && !empty($_REQUEST['team_name'])){
		    $team_name=$_REQUEST['team_name'];
		}
		else{
		    $team_name='';
		}
		
		
		
		
		//用户填写的参赛团队名
		if(isset($_REQUEST['chedui_name_attend'])){
		    $chedui_name_attend=$_REQUEST['chedui_name_attend'];
		}
		else{
		    $chedui_name_attend='';
		}
		
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		//此处通过app接口拿用户基本个人信息，其他附加信息拿post过来的。如没登陆或登陆超时，ajax情况下，需要通知用户。
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		$order_teamMod = M('order_team');
        $sql=sprintf("DELETE FROM %s 
        where member_id='".addslashes($userinfo['user_id'])."' and order_id=0 ", $order_teamMod->getTableName() );
        //echo $sql;exit;
        $result = $order_teamMod->execute($sql);
        
        
		$member_list=explode("|+|", $_POST['member_str']);
		//echo "<pre>";print_r($member_list);exit;
		if(!empty($member_list)){
			foreach($member_list as $k=>$v){
				$member_field=explode("|*|", $v);
				//echo "<pre>";print_r($member_field);exit;
				
		        $sql=sprintf("INSERT %s SET 
		         member_id='".addslashes($userinfo['user_id'])."' 
		        , t_realname='".addslashes($member_field[0])."' 
		        , t_sex='".addslashes($member_field[1])."' 
		        , t_mobile='".addslashes($member_field[5])."' 
		        , t_id_type='".addslashes($member_field[2])."' 
		        , t_id_number='".addslashes($member_field[3])."' 
		        , t_birth_day='".addslashes($member_field[4])."' 
		        , addtime='".$addtime."' 
		        ", $order_teamMod->getTableName() );
		        //echo $sql;exit;
		        $result = $order_teamMod->execute($sql);
		        
			}
		}
		
		
		
		//清查订单过期订单
		$this->checkOrderIsExpire();
		
		
		
		
        $amount_total=0;
        
        
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.catalog.info&RaceCatalogId='.$catalog_id;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$catalogInfo = empty($api_result)?array():$api_result;
		//echo "<pre>";print_r($catalogInfo);exit;
		
		
		
		
		
		if($user_type==1){
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&SingleUser=1&TeamUser=0';
		}
		else{
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id.'&SingleUser=0&TeamUser=1';
		}
		
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$stageInfo = empty($api_result)?array():$api_result;
		//echo "<pre>";print_r($stageInfo);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		//echo $stru;exit;
		$group_list = empty($api_result['RaceStageInfo']['comment']['SelectedRaceGroup'])?array():$api_result['RaceStageInfo']['comment']['SelectedRaceGroup'];
		//echo "<pre>";print_r($group_list);exit;
		
		
		
		if($stru=='group'){
			//group模式
			$stru_id=1;
		}
		else{
			//race模式
			$stru_id=2;
		}
		
		
		
		$need_ticket_arr=isset($RaceStageInfo['comment']['PriceList'])?$RaceStageInfo['comment']['PriceList']:array();
		//echo "<pre>";print_r($stage_price_arr);exit;
		
		if(!empty($need_ticket_arr)){
			$need_ticket=1;
			$stage_price=!empty($need_ticket_arr[1])?$need_ticket_arr[1]:0;
		}
		else{
			$need_ticket=0;
			$stage_price=0;
		}
		//echo $stage_price;exit;
		
		
		
		
		
		
		
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$race_id;
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$raceInfo=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
		
		
		
		//判断生日和性别
		if($stru=='group'){
			//group模式
			$license_arr=$group_list[$group_id]['comment']['LicenseList'];
		}
		else{
			//race模式
			$license_arr=$raceInfo['RaceInfo']['comment']['SelectedRaceGroup'][$group_id]['comment']['LicenseList'];
		}
		//echo "<pre>";print_r($license_arr);exit;
		
		
		
		
		//判断性别人数
		if($stru=='group'){
			//group模式
			$SexUser_arr=$group_list[$group_id]['comment']['SexUser'];
		}
		else{
			//race模式
			$SexUser_arr=$raceInfo['RaceInfo']['comment']['SexUser'];
		}
		//echo "<pre>";print_r($SexUser_arr);exit;
		
		//男性参赛成员总数
		$sex_1_num=0;
		//女性参赛成员总数
		$sex_2_num=0;
		
		
		
		$and_cond='';
		$and_cond=$and_cond.' and order_id=0 ' ;
		$and_cond=$and_cond.' and member_id="'.addslashes($userinfo['user_id']).'" ';
		//echo $and_cond;exit;
		$order_teamMod = M('order_team');
        $order_team_list = $order_teamMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_team_list);exit;
        
        if(!empty($order_team_list)){
        	foreach($order_team_list as $k_team=>$v_team){
        		//echo $v_team['t_sex'];exit;  //1、2
				//echo $v_team['t_birth_day'];exit;  //2016-05-01
				
				$verify_realname=$v_team['t_realname'];
				
				
				if($v_team['t_id_type']==1){
					$idcard_sex=$this->get_xingbie($v_team['t_id_number']);   //男or女
					$idcard_birth_arr=$this->get_idcard_birth($v_team['t_id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
					
					$verify_sex=$idcard_sex;
					$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
					$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
				}
				else{
					$verify_sex=($v_team['t_sex']==1)?'男':'女';
					$verify_birth=str_replace('-','',$v_team['t_birth_day']);
					$verify_birthday=$v_team['t_birth_day'];
				}
				
				
				if($verify_sex=='男'){
					$sex_1_num=$sex_1_num+1;
				}
				else{
					$sex_2_num=$sex_2_num+1;
				}
				
				
				//年龄跟哪个时间做比较
				$verify_date=$raceInfo['RaceInfo']['ApplyStartTime'];
				$verify_date=substr($verify_date,0,10);
				$verify_date=str_replace('-','',$verify_date);
				
				$verify_cycle=$verify_date-$verify_birth;
				$verify_age=floor($verify_cycle/10000);  //舍去法取整，取实足周岁。
				//echo $verify_age;exit;
				
				
				
				
				
				if(!empty($license_arr)){
					foreach($license_arr as $k=>$v){
						
						
						if($v['LicenseType']=='sex'){
							if($v['License']==1 && $verify_sex!='男'){
								$return['success']=$verify_realname.'：'.'比赛仅限男性参赛';
						        echo json_encode($return);
						        exit;
							}
							if($v['License']==2 && $verify_sex!='女'){
								$return['success']=$verify_realname.'：'.'比赛仅限女性参赛';
						        echo json_encode($return);
						        exit;
							}
						}
						
						
						if($v['LicenseType']=='age'){
							if($v['License']['equal']=='<='){
								//年龄小于等于某个数字
								if($verify_age>$v['License']['Age']){
									$return['success']=$verify_realname.'：'.'年龄需要小于等于'.$v['License']['Age'];
							        echo json_encode($return);
							        exit;
								}
							}
							
							if($v['License']['equal']=='>='){
								//年龄大于等于某个数字
								if($verify_age<$v['License']['Age']){
									$return['success']=$verify_realname.'：'.'年龄需要大于等于'.$v['License']['Age'];
							        echo json_encode($return);
							        exit;
								}
							}
							
							if($v['License']['equal']=='<'){
								//年龄小于某个数字
								if($verify_age>=$v['License']['Age']){
									$return['success']=$verify_realname.'：'.'年龄需要小于'.$v['License']['Age'];
							        echo json_encode($return);
							        exit;
								}
							}
							
							if($v['License']['equal']=='>'){
								//年龄大于某个数字
								if($verify_age<=$v['License']['Age']){
									$return['success']=$verify_realname.'：'.'年龄需要大于'.$v['License']['Age'];
							        echo json_encode($return);
							        exit;
								}
							}
							
						}
						
						
						
						
						if($v['LicenseType']=='birthday'){
							if($v['License']['equal']=='<=' && ($verify_birthday>$v['License']['Date']) ){
								$return['success']=$verify_realname.'：'.'生日需要小于等于'.$v['License']['Date'];
						        echo json_encode($return);
						        exit;
							}
							
							if($v['License']['equal']=='>=' && ($verify_birthday<$v['License']['Date']) ){
								$return['success']=$verify_realname.'：'.'生日需要大于等于'.$v['License']['Date'];
						        echo json_encode($return);
						        exit;
							}
							
							
							if($v['License']['equal']=='<' && ($verify_birthday>=$v['License']['Date']) ){
								$return['success']=$verify_realname.'：'.'生日需要小于'.$v['License']['Date'];
						        echo json_encode($return);
						        exit;
							}
							
							if($v['License']['equal']=='>' && ($verify_birthday<=$v['License']['Date']) ){
								$return['success']=$verify_realname.'：'.'生日需要大于'.$v['License']['Date'];
						        echo json_encode($return);
						        exit;
							}
							
						}
						
						
					}
				}
				
				
				
        	}
        }
        
        
        //参赛成员性别数量限制
        if( !empty($SexUser_arr['Min'][1]) && $sex_1_num<$SexUser_arr['Min'][1] ){
        	$return['success']='男性参赛成员数不能小于'.$SexUser_arr['Min'][1].'人';
	        echo json_encode($return);
	        exit;
        }
        if( !empty($SexUser_arr['Min'][2]) && $sex_2_num<$SexUser_arr['Min'][2] ){
        	$return['success']='女性参赛成员数不能小于'.$SexUser_arr['Min'][2].'人';
	        echo json_encode($return);
	        exit;
        }
        if( !empty($SexUser_arr['Max'][1]) && $sex_1_num>$SexUser_arr['Max'][1] ){
        	$return['success']='男性参赛成员数不能大于'.$SexUser_arr['Max'][1].'人';
	        echo json_encode($return);
	        exit;
        }
        if( !empty($SexUser_arr['Max'][2]) && $sex_2_num>$SexUser_arr['Max'][2] ){
        	$return['success']='女性参赛成员数不能大于'.$SexUser_arr['Max'][2].'人';
	        echo json_encode($return);
	        exit;
        }
        
        
		
		
		//如果所选团队成员人数大于1，需要填写参赛团队名
		if(count($order_team_list)>1 && $chedui_name_attend==''){
			$return['success']='请填写参赛团队名';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		//判断生日和性别
		/*
		if($stru=='group'){
			//group模式
			$license_arr=$group_list[$group_id]['comment']['LicenseList'];
		}
		else{
			//race模式
			$license_arr=$raceInfo['RaceInfo']['comment']['SelectedRaceGroup'][$group_id]['LicenseList'];
		}
		
		$and_cond='';
		$and_cond=$and_cond.' and order_id=0 ' ;
		$and_cond=$and_cond.' and member_id="'.addslashes($userinfo['user_id']).'" ';
		//echo $and_cond;exit;
		$order_teamMod = M('order_team');
        $order_team_list = $order_teamMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_team_list);exit;
        
        if(!empty($order_team_list)){
        	foreach($order_team_list as $k_team=>$v_team){
        		//echo $v_team['t_sex'];exit;  //1、2
				//echo $v_team['t_birth_day'];exit;  //2016-05-01
				if(!empty($license_arr)){
					foreach($license_arr as $k=>$v){
						if($v['LicenseType']=='sex'){
							if($v['License']==1 && $v_team['t_sex']!='1'){
								$return['success']='比赛仅限男性参赛';
						        echo json_encode($return);
						        exit;
							}
							if($v['License']==2 && $v_team['t_sex']!='2'){
								$return['success']='比赛仅限女性参赛';
						        echo json_encode($return);
						        exit;
							}
						}
						if($v['LicenseType']=='birthday'){
							if($v['License']['equal']=='<=' && ($v_team['t_birth_day']<=$v['License']['Date']) ){
								//生日小于等于某天
							}
							else{
								$return['success']='成员'.$v_team['t_realname'].'生日需要小于等于'.$v['License']['Date'];
						        echo json_encode($return);
						        exit;
							}
							
							if($v['License']['equal']=='>=' && ($v_team['t_birth_day']>=$v['License']['Date']) ){
								//生日大于等于某天
							}
							else{
								$return['success']='成员'.$v_team['t_realname'].'生日需要大于等于'.$v['License']['Date'];
						        echo json_encode($return);
						        exit;
							}
							
							if($v['License']['equal']=='<' && ($v_team['t_birth_day']<$v['License']['Date']) ){
								//生日小于某天
							}
							else{
								$return['success']='成员'.$v_team['t_realname'].'生日需要小于'.$v['License']['Date'];
						        echo json_encode($return);
						        exit;
							}
							
							if($v['License']['equal']=='>' && ($v_team['t_birth_day']>$v['License']['Date']) ){
								//生日大于某天
							}
							else{
								$return['success']='成员'.$v_team['t_realname'].'生日需要大于'.$v['License']['Date'];
						        echo json_encode($return);
						        exit;
							}
							
						}
					}
				}
				
        	}
        }
        */
        
		
		
		
		
		
		
		$detail_tmp=array();
		
		if(isset($raceInfo['RaceInfo']['RaceId']) && $raceInfo['RaceInfo']['RaceId']>0){
			$detail_tmp['race_id']=$raceInfo['RaceInfo']['RaceId'];
			$detail_tmp['race_name']=$raceInfo['RaceInfo']['RaceName'];
		}
		else{
			$return['success']='请先选择比赛';
	        echo json_encode($return);
	        exit;
		}
		
		if(isset($catalogInfo['RaceStageList'][$stage_id]['RaceStageName']) && $catalogInfo['RaceStageList'][$stage_id]['RaceStageName']!=''){
			$detail_tmp['catalog_id']=$catalogInfo['RaceCatalogInfo']['RaceCatalogId'];
			$detail_tmp['catalog_name']=$catalogInfo['RaceCatalogInfo']['RaceCatalogName'];
			
			$detail_tmp['stage_id']=$stage_id;
			$detail_tmp['stage_name']=$catalogInfo['RaceStageList'][$stage_id]['RaceStageName'];
		}
		else{
			$return['success']='请先选择比赛';
	        echo json_encode($return);
	        exit;
		}
		
		
		if($stru=='group'){
			//group模式
			if(isset($group_list[$group_id]['RaceGroupName']) && $group_list[$group_id]['RaceGroupName']!=''){
				$detail_tmp['group_id']=$group_id;
				$detail_tmp['group_name']=$group_list[$group_id]['RaceGroupName'];
			}
			else{
				$return['success']='请先选择比赛';
		        echo json_encode($return);
		        exit;
			}
		}
		else{
			//race模式
			$group_list=$raceInfo['RaceInfo']['comment']['SelectedRaceGroup'];
			if(isset($group_list[$group_id]['RaceGroupName']) && $group_list[$group_id]['RaceGroupName']!=''){
				$detail_tmp['group_id']=$group_id;
				$detail_tmp['group_name']=$group_list[$group_id]['RaceGroupName'];
			}
			else{
				$return['success']='请先选择比赛';
		        echo json_encode($return);
		        exit;
			}
		}
		
		
		//echo "<pre>";print_r($detail_tmp);exit;
		
		
		//团队所选的成员人数需要介于TeamUserMin和TeamUserMax之间。
		if($user_type==2){
			if(count($member_list)<$raceInfo['RaceInfo']['comment']['TeamUserMin']){
				$return['success']='所选成员数不能小于'.$raceInfo['RaceInfo']['comment']['TeamUserMin'].'人';
		        echo json_encode($return);
		        exit;
			}
			if(count($member_list)>$raceInfo['RaceInfo']['comment']['TeamUserMax']){
				$return['success']='所选成员数不能大于'.$raceInfo['RaceInfo']['comment']['TeamUserMax'].'人';
		        echo json_encode($return);
		        exit;
			}
		}
		
		
		
		//echo "<pre>";print_r($raceInfo);exit;
		if($user_type==2){
			//团队用第2个金额
			//$raceInfo['RaceInfo']['PriceList'][1]=empty($raceInfo['RaceInfo']['PriceList'][1])?0:$raceInfo['RaceInfo']['PriceList'][1];
			//$detail_tmp['price']=empty($raceInfo['RaceInfo']['PriceList'][2])?$raceInfo['RaceInfo']['PriceList'][1]:$raceInfo['RaceInfo']['PriceList'][2];
			$detail_tmp['price']=empty($raceInfo['RaceInfo']['PriceList'][1])?0:$raceInfo['RaceInfo']['PriceList'][1];
		}
		else{
			//个人用第1个金额
			$detail_tmp['price']=empty($raceInfo['RaceInfo']['PriceList'][1])?0:$raceInfo['RaceInfo']['PriceList'][1];
		}
		
		$detail_tmp['number']='1';
		$detail_tmp['price_race']=$detail_tmp['price']*(ceil(count($member_list)/$detail_tmp['number']));
		//var_dump($detail_tmp['price_race']);exit;
		
		
		
		
		//通票情况下，不是精英的，用通票价格
		$price_type=1;
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
			if(empty($raceInfo['RaceInfo']['PriceList'])){
				$detail_tmp['price_race']=$stage_price;
				$price_type=2;
			}
		}
		//echo $detail_tmp['price_race'];exit;
		
		
		
		
		
		$amount_total=$detail_tmp['price_race'];  //暂且将比赛总价视作订单付款总价，后面如果选购了产品，再更新amount_total
		$detail_tmp['addtime']=$addtime;
		
		//echo $user_type;exit;
		//获得比赛团队/个人的名额限制总数
		if($user_type==2){
			//团队
			$detail_tmp['limit_number']=$raceInfo['RaceInfo']['comment']['TeamLimit'];  //总数
		}
		else{
			//个人
			$detail_tmp['limit_number']=$raceInfo['RaceInfo']['comment']['SingleUserLimit'];  //总数
		}
		
		//echo "<pre>";print_r($detail_tmp);exit;
		
		
		
	    
		//比赛是否还有剩余名额，需要考虑到已支付和正在支付的人所占用的名额。
		$and_cond='';
		$cart_type_name='[比赛]';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and race_id=' . addslashes($race_id) ;
		$and_cond=$and_cond.' and user_type=' . addslashes($user_type) ;
		$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) ) '  ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $nums_used=empty($order_data)?0:count($order_data);
        //var_dump($nums_used);exit;
		
    	$nums_has = $detail_tmp['limit_number'] - $nums_used ;
    	if($nums_used >= $detail_tmp['limit_number'] ) {
    		$return['success']=$cart_type_name.$detail_tmp['race_name'].'名额不足';
	        echo json_encode($return);
	        exit;
		}
		
        
		/*
		//更新用户信息。我个人觉得不合理，但客户要求此步需要修改个人信息
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/set_user_profile.json?token='.$_SESSION['app_token'];
		$api_para=array();
		
		$api_para['name']=$_POST['realname'];
		
		if($_POST['sex']=='男'){$sex=1;}
		if($_POST['sex']=='女'){$sex=2;}
		
		//$api_para['id_type']=$_POST['id_type'];
		$api_para['id_number']=$_POST['id_number'];
		//$api_para['expire_day']=$_POST['expire_day'];
		$api_para['birth_day']=$_POST['birth_day'];
		$api_para['sex']=$sex;
		$api_para['province']=$m_province;
		$api_para['city']=$m_city;
		$api_para['address']=$_POST['address'];
		
		$api_para['ec_name']=$_POST['ec_name'];
		//$api_para['ec_relation']=$_POST['ec_relation'];
		$api_para['ec_phone1']=$_POST['ec_phone1'];
		//$api_para['ec_phone2']=$_POST['ec_phone2'];
		$api_para['token']=$_SESSION['app_token'];
		//echo "<pre>";print_r($api_para);exit;
		
		$api_result=$this->http_request_url_post($api_url,$api_para);
	    $user_update_rst = empty($api_result)?array():$api_result;
		//echo "<pre>";print_r($user_update_rst);exit;
		
		if(isset($user_update_rst['user_id']) && $user_update_rst['user_id']>0){
		}
		else{
			$return['success']='个人信息提交失败';
	        echo json_encode($return);
	        exit;
		}
		*/
		
		$orderMod = M('order');
        $orderMod->status=0;
        $order_id = $orderMod->add();
        //var_dump($order_id);exit;
        
		// , expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->orderExpireTime) )."'   此处未形成订单，先不写过期时间，放到选购产品后面再写过期时间。
		//, expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->signupExpireTime) )."' 
		
	    /*
	    , m_realname='".addslashes($_POST['realname'])."' 
	    , m_mobile='".addslashes($userinfo['phone'])."' 
	    , m_sex='".addslashes($sex)."' 
	    , m_id_number='".addslashes($_POST['id_number'])."' 
	    , m_birth_day='".addslashes($_POST['birth_day'])."' 
	    , m_province='".addslashes($m_province)."' 
	    , m_city='".addslashes($m_city)."' 
	    , m_district='".addslashes($m_district)."' 
	    , m_address='".addslashes($_POST['address'])."' 
	    , m_email='".addslashes($_POST['email'])."' 
	    , m_ec_name='".addslashes($_POST['ec_name'])."' 
	    , m_ec_phone1='".addslashes($_POST['ec_phone1'])."' 
	    , chedui_name='".addslashes($_POST['chedui_name'])."' 
	    */
	    
		$orderMod = M('order');
	    $sql=sprintf("update %s SET price_race='".addslashes($detail_tmp['price_race'])."' 
	    , price_type='".addslashes($price_type)."' 
	    , amount_total='".addslashes($amount_total)."' 
	    , isPay='0' 
	    , isExpire='0' 
	    , createDateTime='".addslashes($addtime)."' 
	    , member_id='".addslashes($userinfo['user_id'])."' 
	    , catalog_id='".addslashes($detail_tmp['catalog_id'])."' 
	    , catalog_name='".addslashes($detail_tmp['catalog_name'])."' 
	    , stage_id='".addslashes($detail_tmp['stage_id'])."' 
	    , stage_name='".addslashes($detail_tmp['stage_name'])."' 
	    , group_id='".addslashes($detail_tmp['group_id'])."' 
	    , group_name='".addslashes($detail_tmp['group_name'])."' 
	    , race_id='".addslashes($detail_tmp['race_id'])."' 
	    , race_name='".addslashes($detail_tmp['race_name'])."' 
	    , stru_id='".addslashes($stru_id)."' 
	    , user_type='".addslashes($user_type)."' 
	    , chedui_id='".addslashes($team_id)."' 
	    , chedui_name='".addslashes($team_name)."' 
	    , chedui_name_attend='".addslashes($chedui_name_attend)."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    $this->set_log_sql($sql);
	    
	    
	    if(isset($_SESSION['ticket_type'])){
	    	$sql=sprintf("update %s SET ticket_type='".addslashes($_SESSION['ticket_type'])."' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
	    }
	    
	    
	    
	    
	    //更新这个用户order_team表的order_id
	    $sql=sprintf("update %s SET order_id='".addslashes($order_id)."' 
	    where member_id='".addslashes($userinfo['user_id'])."' and order_id=0 
	    ", $order_teamMod->getTableName() );
	    //echo $sql;exit;
	    $result = $order_teamMod->execute($sql);
	    
	    
	    
	    //如果所选成员人数大于1人则保存参赛团队名到app的接口
	    
	    //$order_team_list
	    
	    
	    
	    
	    setcookie("order_id", $order_id , time()+$this->cookieExpireTime, "/");
	    $order_no='';
	    
	    /*
	    //生成order_no
	    $rand_number=mt_rand(100000, 999999);
		$order_no = date("ymdHis").$rand_number;
    	//echo $order_no;exit;
    	
    	$sql=sprintf("update %s SET order_no='".addslashes($order_no)."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    
	    */
	    
	    
	    
	    
	    
        $return = array(
            'order_id' => $order_id,
            'order_no' => $order_no,
            'url_param' => $order_no,
            'amount_total' => $amount_total
        );
		$return['success']='success';
		//echo "<pre>";print_r($return);exit;
		
        echo json_encode($return);
        exit;
        
		
	}
	
	
	
	//团队 添加成员
	//http://xracebm201607.loc/baoming/signup_2_add/catalog_id/9/stage_id/32/user_type/2/group_id/25/race_id/79/team_id/asf/
	public function signup_2_add(){
		
		//echo "<pre>";print_r($_POST);echo "</pre>";exit;
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    exit;
		}
		
		
		
		if(isset($_REQUEST['team_id']) && !empty($_REQUEST['team_id'])){
		    $team_id=$_REQUEST['team_id'];
		    $this->assign('team_id', $team_id);
		}
		else{
		    exit;
		}
		
		
        
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		
		
		
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('signup_2_add');
    }
    
    
    //团队 添加成员 提交
	public function signup_2_add_sub(){
		
		
		//echo "<pre>";print_r($_POST);echo "</pre>";exit;
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    exit;
		}
		
		
		
		if(isset($_REQUEST['team_id']) && !empty($_REQUEST['team_id'])){
		    $team_id=$_REQUEST['team_id'];
		    $this->assign('team_id', $team_id);
		}
		else{
		    exit;
		}
		
		
		
		
        if($_POST['id_type']=='身份证'){
        	$id_type=1;
        }
        elseif($_POST['id_type']=='护照'){
        	$id_type=2;
        }
        elseif($_POST['id_type']=='港澳台地区证件'){
        	$id_type=3;
        }
        else{
         	$return['success']='请选择证件类型';
	        echo json_encode($return);
	        exit;
        }
        
        
        
        $is_id_number=$this->checkIdCard($_POST['id_number']);
        if($id_type==1 && !$is_id_number) {
        	$return['success']='请填写正确的身份证号';
	        echo json_encode($return);
	        exit;
        }
        
        
        $is_mobile=$this->isMobile($_POST['mobile']);
        if(!$is_mobile) {
        	$return['success']='请填写正确的手机号';
	        echo json_encode($return);
	        exit;
        }
        
        
        
		//此处通过app接口拿用户基本个人信息，其他附加信息拿post过来的。如没登陆或登陆超时，ajax情况下，需要通知用户。
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		if($_POST['sex']=='男'){$sex=1;}
		if($_POST['sex']=='女'){$sex=2;}
		
		
		
		//身份证与性别生日是否符合
		if($id_type==1){
			$idcard_sex=$this->get_xingbie($_POST['id_number']);   //男or女
			if($idcard_sex!=$_POST['sex']){
				$return['success']='性别与身份证信息不符';
		        echo json_encode($return);
		        exit;
			}
			
			$idcard_birth_arr=$this->get_idcard_birth($_POST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
			if($verify_birthday!=$_POST['birth_day']){
				$return['success']='出生年月与身份证信息不符';
		        echo json_encode($return);
		        exit;
			}
		}
		
		
		//添加成员
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/set_tmp_team_member.json?token='.$_SESSION['app_token'];
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		//name不存在于现有的成员列表，则会自动添加；如果这个name存在于现有的成员列表中，则是更新操作。
		$api_para['name']=$_POST['realname'];
		$api_para['phone']=$_POST['mobile'];
		$api_para['id_type']=$id_type;
		$api_para['id']=$_POST['id_number'];
		$api_para['sex']=$sex;
		$api_para['birth']=$_POST['birth_day'];
		$team_member_add=$this->http_request_url_post($api_url,$api_para);
		if(isset($team_member_add['result']) && $team_member_add['result']==true){
			$return['success']='success';
	        echo json_encode($return);
	        exit;
		}
		else{
			$return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
	}
	
	
	
	
	//团队 修改成员
	//http://xracebm201607.loc/baoming/signup_2_edit/catalog_id/9/stage_id/32/user_type/2/group_id/25/race_id/79/team_id/asf/realname/sdfds
	public function signup_2_edit(){
		
		//echo "<pre>";print_r($_POST);echo "</pre>";exit;
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}
		
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    exit;
		}
		
		
		
		if(isset($_REQUEST['team_id']) && !empty($_REQUEST['team_id'])){
		    $team_id=$_REQUEST['team_id'];
		    $this->assign('team_id', $team_id);
		}
		else{
		    exit;
		}
		
		
        
        
        
        
		if(isset($_REQUEST['realname'])){
		    $realname=$_REQUEST['realname'];
		    $this->assign('realname', $realname);
		}
		else{
		    exit;
		}
		
		$mobile=$_REQUEST['mobile'];
		$this->assign('mobile', $mobile);
		
		$sex=$_REQUEST['sex'];
		$this->assign('sex', $sex);
		
		$id_type=$_REQUEST['id_type'];
		$this->assign('id_type', $id_type);
		
		$id_number=$_REQUEST['id_number'];
		$this->assign('id_number', $id_number);
		
		$birth_day=$_REQUEST['birth_day'];
		$this->assign('birth_day', $birth_day);
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('signup_2_edit');
    }
    
    
    
    
    //团队 修改成员 提交
	public function signup_2_edit_sub(){
		
		
		//echo "<pre>";print_r($_POST);echo "</pre>";exit;
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    exit;
		}
		
		
		
		if(isset($_REQUEST['team_id']) && !empty($_REQUEST['team_id'])){
		    $team_id=$_REQUEST['team_id'];
		    $this->assign('team_id', $team_id);
		}
		else{
		    exit;
		}
		
		
		
		
        if($_POST['id_type']=='身份证'){
        	$id_type=1;
        }
        elseif($_POST['id_type']=='护照'){
        	$id_type=2;
        }
        elseif($_POST['id_type']=='港澳台地区证件'){
        	$id_type=3;
        }
        else{
         	$return['success']='请选择证件类型';
	        echo json_encode($return);
	        exit;
        }
        
		
        $is_id_number=$this->checkIdCard($_POST['id_number']);
        if($id_type==1 && !$is_id_number) {
        	$return['success']='请填写正确的身份证号';
	        echo json_encode($return);
	        exit;
        }
        
        
        $is_mobile=$this->isMobile($_POST['mobile']);
        if(!$is_mobile) {
        	$return['success']='请填写正确的手机号';
	        echo json_encode($return);
	        exit;
        }
        
        
        
		//此处通过app接口拿用户基本个人信息，其他附加信息拿post过来的。如没登陆或登陆超时，ajax情况下，需要通知用户。
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		if($_POST['sex']=='男'){$sex=1;}
		if($_POST['sex']=='女'){$sex=2;}
		
		
		//身份证与性别生日是否符合
		if($id_type==1){
			$idcard_sex=$this->get_xingbie($_POST['id_number']);   //男or女
			if($idcard_sex!=$_POST['sex']){
				$return['success']='性别与身份证信息不符';
		        echo json_encode($return);
		        exit;
			}
			
			$idcard_birth_arr=$this->get_idcard_birth($_POST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
			if($verify_birthday!=$_POST['birth_day']){
				$return['success']='出生年月与身份证信息不符';
		        echo json_encode($return);
		        exit;
			}
		}
		
		
		
		
		
		//如果 realname_old 和 realname不同，则先删除 realname_old 
		if($_POST['realname_old']!=$_POST['realname']){
			//删除成员
			$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/delete_tmp_team_member.json?token='.$_SESSION['app_token'];
			//echo $api_url;echo "<br>";exit;
			$api_para=array();
			//name不存在于现有的成员列表，则会自动添加；如果这个name存在于现有的成员列表中，则是更新操作。
			$api_para['name']=$_POST['realname_old'];
			$team_member_add=$this->http_request_url_post($api_url,$api_para);
		}
		
		//修改成员
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/set_tmp_team_member.json?token='.$_SESSION['app_token'];
		//echo $api_url;echo "<br>";
		$api_para=array();
		//name不存在于现有的成员列表，则会自动添加；如果这个name存在于现有的成员列表中，则是更新操作。
		$api_para['name']=$_POST['realname'];
		$api_para['phone']=$_POST['mobile'];
		$api_para['id_type']=$id_type;
		$api_para['id']=$_POST['id_number'];
		$api_para['sex']=$sex;
		$api_para['birth']=$_POST['birth_day'];
		//echo "<pre>";print_r($api_para);exit;
		
		$team_member_add=$this->http_request_url_post($api_url,$api_para);
		if(isset($team_member_add['result']) && $team_member_add['result']==true){
			$return['success']='success';
	        echo json_encode($return);
	        exit;
		}
		else{
			$return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
	}
	
	
	
	
    //团队 删除成员 提交
	public function signup_2_delete_sub(){
		
		
		//echo "<pre>";print_r($_POST);echo "</pre>";exit;
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    exit;
		}
		
		
		if($_REQUEST['user_type']==2){
			$user_type=$_REQUEST['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		if(isset($_REQUEST['group_id']) && !empty($_REQUEST['group_id'])){
		    $group_id=$_REQUEST['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
		    exit;
		}
		
		
		if(isset($_REQUEST['race_id']) && !empty($_REQUEST['race_id'])){
		    $race_id=$_REQUEST['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
		    exit;
		}
		
		
		
		if(isset($_REQUEST['team_id']) && !empty($_REQUEST['team_id'])){
		    $team_id=$_REQUEST['team_id'];
		    $this->assign('team_id', $team_id);
		}
		else{
		    exit;
		}
		
		
		
		if(isset($_POST['realname']) && !empty($_POST['realname'])){
		    $realname=$_POST['realname'];
		    $this->assign('realname', $realname);
		}
		else{
		    exit;
		}
		//var_dump($realname);exit;
		
		
		//删除成员
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/delete_tmp_team_member.json?token='.$_SESSION['app_token'];
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		//name不存在于现有的成员列表，则会自动添加；如果这个name存在于现有的成员列表中，则是更新操作。
		$api_para['name']=$_POST['realname'];
		$team_member_add=$this->http_request_url_post($api_url,$api_para);
		if(isset($team_member_add['result']) && $team_member_add['result']==true){
			$return['success']='success';
	        echo json_encode($return);
	        exit;
		}
		else{
			$return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
	}
	
	
	
	//产品 列表
	//有产品的分站：http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId=29
	//示例：http://xracebm201607.loc/baoming/product_list/order_id/1903
	public function product_list(){
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		    $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}
		
		if(isset($_COOKIE['order_id']) && $_COOKIE['order_id']==$order_id) {
		}
		else{
		    exit;
		}
		
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		//echo "<pre>";print_r($userinfo);exit;
		
		
	    
		//获得用户之前的参赛信息
		$and_cond='';
		$and_cond=$and_cond.' and status=0 ' ;
		$and_cond=$and_cond.' and member_id=' . addslashes($userinfo['user_id']) ;
		$and_cond=$and_cond.' and id=' . addslashes($order_id) ;
		$and_cond=$and_cond.' and isPay=0 '  ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        
        
        if(empty($order_info)){
		    exit;
		}
		
        
		
    	if(isset($order_info['catalog_id']) && !empty($order_info['catalog_id'])){
		    $catalog_id=$order_info['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
	        exit;
		}	
		
		
		if(isset($order_info['stage_id']) && !empty($order_info['stage_id'])){
		    $stage_id=$order_info['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
	        exit;
		}
		
		
		if($order_info['user_type']==1 || $order_info['user_type']==2){
			$user_type=$order_info['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		
		if(isset($order_info['group_id']) && !empty($order_info['group_id'])){
		    $group_id=$order_info['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
	        exit;
		}
		
		
		if(isset($order_info['race_id']) && !empty($order_info['race_id'])){
		    $race_id=$order_info['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
	        exit;
		}
		
		
		
		//$stage_id=29;    //debug 强制写有产品数据的分站id
		
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		
		$product_list = empty($api_result['RaceStageInfo']['comment']['SelectedProductList'])?array():$api_result['RaceStageInfo']['comment']['SelectedProductList'];
		
		
		//20161008。如果没有商品，则跳过选购商品的步骤
		//$product_list=array();
		if(empty($product_list)){
			$url=U('baoming/ticket_confirm', array( 'order_id'=>$order_id ));
			redirect($url);
			exit;
		}
		
		
		
		$product_box="";
		if(!empty($product_list)){
			foreach($product_list as $k=>$v){
				//echo "<pre>";print_r($v);exit;
				//$product_list[$k]['SkuList']['ProductPrice']=10*$k;  //debug  强制写产品的价格
				
				if(!empty($v['SkuList'])){
					foreach($v['SkuList'] as $k_sku=>$v_sku){
						if(empty($product_list[$k]['default'])){
							$product_list[$k]['default']=$v_sku;
							$product_list[$k]['default']['sku_id']=$k_sku;
						}
					}
				}
				
				$product_box=$product_box."mui('body').on('tap','.sp_".$k."',function() {mui('#sp_".$k."').popover('show');});";
				
			}
		}
		//echo "<pre>";print_r($product_list);exit;
		$this->assign('product_list', $product_list);
		
		
		//echo $product_box;exit;
		/*
		$product_box="mui('body').on('tap','.sp_1',function() {
                mui('#sp_1').popover('show');
            })
            mui('body').on('tap','.sp_5',function() {
                mui('#sp_5').popover('show');
            })";*/
		$this->assign('product_box', $product_box);
		
		
		
		
		
        $this->assign('curmenu', '7');
        $this->display('product_list');
	}
	
	
	
	//产品 选购完毕 提交
	public function product_list_sub(){
		//echo "<pre>";print_r($_POST);
		//exit;
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		    $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}
		
		if(isset($_COOKIE['order_id']) && $_COOKIE['order_id']==$order_id) {
		}
		else{
		    exit;
		}
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		//echo "<pre>";print_r($userinfo);exit;
		
		
	    
		//获得用户之前的参赛信息
		$and_cond='';
		$and_cond=$and_cond.' and status=0 ' ;
		$and_cond=$and_cond.' and member_id=' . addslashes($userinfo['user_id']) ;
		$and_cond=$and_cond.' and id=' . addslashes($order_id) ;
		$and_cond=$and_cond.' and isPay=0 '  ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        
        
        if(empty($order_info)){
		    exit;
		}
		
        
		
    	if(isset($order_info['catalog_id']) && !empty($order_info['catalog_id'])){
		    $catalog_id=$order_info['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
	        exit;
		}	
		
		
		if(isset($order_info['stage_id']) && !empty($order_info['stage_id'])){
		    $stage_id=$order_info['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
	        exit;
		}
		
		
		if($order_info['user_type']==1 || $order_info['user_type']==2){
			$user_type=$order_info['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		
		if(isset($order_info['group_id']) && !empty($order_info['group_id'])){
		    $group_id=$order_info['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
	        exit;
		}
		
		
		if(isset($order_info['race_id']) && !empty($order_info['race_id'])){
		    $race_id=$order_info['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
	        exit;
		}
		
		$product_str=$_POST['product_info'];
		
		if(empty($product_str) || $product_str=='|start||end|'){
			//没有取到产品信息，视为没选产品，直接通过，进入下一步
			$return['success']='success';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		$product_str=str_replace('|start||','',$product_str);
		$product_str=str_replace('||end|','',$product_str);
		//var_dump($product_str);exit;
		$product_arr=explode("||", $product_str);
		//echo "<pre>";print_r($product_arr);exit;
		
		
		if(empty($product_arr)){
			//没有取到产品信息，视为没选产品，直接通过，进入下一步
			$return['success']='success';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		//$stage_id=29;    //debug 强制写有产品数据的分站id
		
		
		
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		
		$product_list = empty($api_result['RaceStageInfo']['comment']['SelectedProductList'])?array():$api_result['RaceStageInfo']['comment']['SelectedProductList'];
		
		
		/*
		if(!empty($product_list)){
			foreach($product_list as $k=>$v){
				//$product_list[$k]['SkuList']['ProductPrice']=10*$k;  //debug  强制写产品的价格
			}
		}
		//echo "<pre>";print_r($product_list);exit;
		*/
		
		
		$price_product=0;
		
		
		
		$order_productMod = M('order_product');
        $sql=sprintf("DELETE FROM %s 
        where member_id='".addslashes($userinfo['user_id'])."' and order_id=".addslashes($order_id)." ", $order_productMod->getTableName() );
        //echo $sql;exit;
        $result = $order_productMod->execute($sql);
        
        
        
		
		foreach($product_arr as $k=>$v){
			
			$product_field=explode("*", $v);
			//echo "<pre>";print_r($product_field);exit;
			
			$product_id=$product_field[0];
			$product_name=$product_field[1];
			$price=$product_field[2];
			$sku_id=$product_field[3];
			$sku_name=$product_field[4];
			$number=$product_field[5];
			
			$price_sub=$price*$number;
			
			
	        $sql=sprintf("INSERT %s SET 
	         member_id='".addslashes($userinfo['user_id'])."' 
	        , order_id='".addslashes($order_id)."' 
	        , product_id='".addslashes($product_id)."' 
	        , product_name='".addslashes($product_name)."' 
	        , sku_id='".addslashes($sku_id)."' 
	        , sku_name='".addslashes($sku_name)."' 
	        , number='".addslashes($number)."' 
	        , price='".addslashes($price)."' 
	        , price_sub='".addslashes($price_sub)."' 
	        , addtime='".$addtime."' 
	        ", $order_productMod->getTableName() );
	        //echo $sql;exit;
	        $result = $order_productMod->execute($sql);
		    
		    //计算产品部分总价，更新订单总价
		    $price_product=$price_product+$price_sub;
		    
		}
		
		
		$amount_total=$order_info['price_race']+$price_product;
		//var_dump($amount_total);exit;
		$orderMod = M('order');
		
		$sql=sprintf("update %s SET price_product='".addslashes($price_product)."' , amount_total='".addslashes($amount_total)."' 
	    where id='".addslashes($order_id)." and isPay=0 ' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    
	    $this->set_log_sql($sql);
		
		$return['success']='success';
        echo json_encode($return);
        exit;
		
	}
	
	
	
	
	//产品 填写收货信息  
	//示例：http://xracebm201607.loc/baoming/product_recieve/order_id/1885
	public function product_recieve(){
		
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		    $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}
		
		if(isset($_COOKIE['order_id']) && $_COOKIE['order_id']==$order_id) {
		}
		else{
		    exit;
		}
		
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		//echo "<pre>";print_r($userinfo);exit;
		
		
	    
		//获得用户之前的参赛信息
		$and_cond='';
		$and_cond=$and_cond.' and status=0 ' ;
		$and_cond=$and_cond.' and member_id=' . addslashes($userinfo['user_id']) ;
		$and_cond=$and_cond.' and id=' . addslashes($order_id) ;
		$and_cond=$and_cond.' and isPay=0 '  ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        
        
        if(empty($order_info)){
		    exit;
		}
		
        
        $this->assign('order_no', $order_info['order_no']);
        
        
		
    	if(isset($order_info['catalog_id']) && !empty($order_info['catalog_id'])){
		    $catalog_id=$order_info['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
	        exit;
		}	
		
		
		if(isset($order_info['stage_id']) && !empty($order_info['stage_id'])){
		    $stage_id=$order_info['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
	        exit;
		}
		
		
		if($order_info['user_type']==1 || $order_info['user_type']==2){
			$user_type=$order_info['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		
		if(isset($order_info['group_id']) && !empty($order_info['group_id'])){
		    $group_id=$order_info['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
	        exit;
		}
		
		
		if(isset($order_info['race_id']) && !empty($order_info['race_id'])){
		    $race_id=$order_info['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
	        exit;
		}
		
		
        $this->assign('curmenu', '7');
        $this->display('product_recieve');
	}
	
	
	
	//产品 填写收货信息  提交
	public function product_recieve_sub(){
		//echo "<pre>";print_r($_POST);exit;
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		    $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}
		
		if(isset($_COOKIE['order_id']) && $_COOKIE['order_id']==$order_id) {
		}
		else{
		    exit;
		}
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		//echo "<pre>";print_r($userinfo);exit;
		
		
	    
		//获得用户之前的参赛信息
		$and_cond='';
		$and_cond=$and_cond.' and status=0 ' ;
		$and_cond=$and_cond.' and member_id=' . addslashes($userinfo['user_id']) ;
		$and_cond=$and_cond.' and id=' . addslashes($order_id) ;
		$and_cond=$and_cond.' and isPay=0 '  ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        
        
        if(empty($order_info)){
		    exit;
		}
		
        
		
    	if(isset($order_info['catalog_id']) && !empty($order_info['catalog_id'])){
		    $catalog_id=$order_info['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
	        exit;
		}	
		
		
		if(isset($order_info['stage_id']) && !empty($order_info['stage_id'])){
		    $stage_id=$order_info['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
	        exit;
		}
		
		
		if($order_info['user_type']==1 || $order_info['user_type']==2){
			$user_type=$order_info['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		
		if(isset($order_info['group_id']) && !empty($order_info['group_id'])){
		    $group_id=$order_info['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
	        exit;
		}
		
		
		if(isset($order_info['race_id']) && !empty($order_info['race_id'])){
		    $race_id=$order_info['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
	        exit;
		}
		
		
		
		
		
		
		if(isset($_POST['realname']) && !empty($_POST['realname'])){
		}
		else{
		    $return['success']='请输入姓名';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(isset($_POST['sex']) && !empty($_POST['sex'])){
		}
		else{
		    $return['success']='请输入性别';
	        echo json_encode($return);
	        exit;
		}
		
		if(isset($_POST['phone']) && !empty($_POST['phone'])){
		}
		else{
		    $return['success']='请输入手机号码';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(isset($_POST['prov_city']) && !empty($_POST['prov_city'])){
		}
		else{
		    $return['success']='请输入所在地址';
	        echo json_encode($return);
	        exit;
		}
		
        
        if(isset($_POST['address']) && !empty($_POST['address'])){
		}
		else{
		    $return['success']='请输入详细地址';
	        echo json_encode($return);
	        exit;
		}
        
        
        $is_mobile=$this->isMobile($_POST['phone']);
        if(!$is_mobile) {
        	$return['success']='请填写正确的手机号';
	        echo json_encode($return);
	        exit;
        }
        
        
        
        $prov_city_arr=explode(" ", $_POST['prov_city']);
		$m_province=$prov_city_arr[0];
		$m_city=$prov_city_arr[1];
		$m_district=$prov_city_arr[2];
		
		
		if($_POST['sex']=='男'){$sex=1;}
		if($_POST['sex']=='女'){$sex=2;}
		
		$orderMod = M('order');
        //var_dump($order_id);exit;
        
		// , expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->orderExpireTime) )."'   此处未形成订单，先不写过期时间，放到选购产品后面再写过期时间。
		//, expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->signupExpireTime) )."' 
		$orderMod = M('order');
	    $sql=sprintf("update %s SET p_realname='".addslashes($_POST['realname'])."' 
	    , p_sex='".addslashes($sex)."' 
	    , p_mobile='".addslashes($_POST['phone'])."' 
	    , p_province='".addslashes($m_province)."' 
	    , p_city='".addslashes($m_city)."' 
	    , p_district='".addslashes($m_district)."' 
	    , p_address='".addslashes($_POST['address'])."' 
	    , p_email='".addslashes($_POST['email'])."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
		
		$return['success']='success';
        echo json_encode($return);
        exit;
		
	}
	
	
	
	
    //订单确认页  单票情况下，进入此页立即生成订单号order_no并跳转到支付页；通票情况下，点确认订单提交此页后生成订单号order_no。
    //示例：http://xracebm201607.loc/baoming/ticket_confirm/order_id/1936
    //示例：http://xracebm201607.loc/baoming/ticket_confirm/order_no/160818174909664701
	public function ticket_confirm(){
		
		
    	if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		}
		else{
			if(isset($_REQUEST['order_no']) && !empty($_REQUEST['order_no'])){
			    $order_no=$_REQUEST['order_no'];
			}
			else{
		    	exit;
			}
		}
		
		
		
    	if(isset($_REQUEST['catalog_id']) && !empty($_REQUEST['catalog_id'])){
		    $catalog_id=$_REQUEST['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
		    //exit;
		}	
		
		if(isset($_REQUEST['stage_id']) && !empty($_REQUEST['stage_id'])){
		    $stage_id=$_REQUEST['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
		    //exit;
		}
		
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo);
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
		
		
		
		//获得用户之前的参赛信息
		$and_cond='';
		if(!empty($order_id)){
			$and_cond=$and_cond.' and id=' . addslashes($order_id) ;
		}
		else{
			$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
		}
		
		$and_cond=$and_cond.' and isPay=0 '  ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $order_info=empty($order_data)?array():$order_data[0];
        $order_list=empty($order_data)?array():$order_data;
        //echo "<pre>";print_r($order_list);exit;
        $this->assign('order_info', $order_info);
        $this->assign('order_list', $order_list);
        
        
        
        if(!empty($order_info)){
		    
		    $order_id=$order_info['id'];
			$order_no=$order_info['order_no'];
			
			$catalog_id=$order_info['catalog_id'];
			$stage_id=$order_info['stage_id'];
			
		}
		else{
			//支付完成后，不允许再次进入此页面。
			if(isset($_SESSION['order_no'])){
			}
			else{
				exit;
			}
		}
		
		$this->assign('order_id', $order_id);
        $this->assign('order_no', $order_no);
        
		$this->assign('catalog_id', $catalog_id);
	    $this->assign('stage_id', $stage_id);
	    
		
		
		
		/*
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId=64';
		//echo $api_url;echo "<br>";exit;
		$api_para=array();
		$raceInfo=$this->http_request_url_post($api_url,$api_para);
	    echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
	    */
		
		
		
		//获取分站信息及票信息：
		$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId='.$stage_id;
		//echo $api_url;exit;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		$RaceStageInfo = empty($api_result['RaceStageInfo'])?array():$api_result['RaceStageInfo'];
		//echo "<pre>";print_r($RaceStageInfo);exit;
		$this->assign('RaceStageInfo', $RaceStageInfo);
		$stru=$RaceStageInfo['comment']['RaceStructure'];
		//echo $stru;exit;  //group or race 模式。
		
		
		$need_ticket_arr=isset($RaceStageInfo['comment']['PriceList'])?$RaceStageInfo['comment']['PriceList']:array();
		//echo "<pre>";print_r($stage_price_arr);exit;
		
		if(!empty($need_ticket_arr)){
			$need_ticket=1;
			$stage_price=!empty($need_ticket_arr[1])?$need_ticket_arr[1]:0;
		}
		else{
			$need_ticket=0;
			$stage_price=0;
		}
		//echo $stage_price;exit;
		
		
		
		
		if(isset($_SESSION['ticket_type']) && $_SESSION['ticket_type']==2){
			//通票情况，需要显示订单里的循环信息。并提示是否继续选择or确认订单 如继续选择，跳ticket_start页；如确认订单，生成订单号order_no，并跳支付页。
			
			
			
			if(isset($_SESSION['order_no']) && !empty($_SESSION['order_no'])){
				$order_no = $_SESSION['order_no'];
			}
			else{
			    $rand_number=mt_rand(100000, 999999);
				$order_no = date("ymdHis").$rand_number;
				$_SESSION['order_no']=$order_no;
		    	//echo $order_no;exit;
			}
			
			
		    $orderMod = M('order');
	    	$sql=sprintf("update %s SET order_no='".addslashes($order_no)."' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
		    
		    
			
			
			
			
			
			//计算当前总价
			$and_cond='';
			$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
			$and_cond=$and_cond.' and isPay=0 '  ;
			$orderMod = M('order');
	        $order_all = $orderMod->where(" 1 ".$and_cond )->select();
	        //echo "<pre>";print_r($order_all);exit;
	        $order_all=empty($order_all)?array():$order_all;
	        $this->assign('order_all', $order_all);
			
			
			$all_amount_total=0;   //最后全部的总价，含产品和比赛。
			$arr_order_id=array();   //所有参加非精英的人，含个人和团队，订单流水order_id
			$arr_people=array();  //所有参加非精英的人，含个人和团队
	        //加上历史循环金额
	        $tongpiao_price=0;  //最终计算的通票的钱
			if(!empty($order_all)){
				foreach($order_all as $k=>$v){
					
					//echo "<pre>";print_r($v);exit;
					
					
					
					//非精英部分
					if($v['price_type']==2){
						$arr_order_id[]=$v['id'];
						
						//选了 非精英 通票的总共多少独立人，含个人和团队
						if($v['user_type']==1){
							$push_value=$v['m_realname']."|".$v['m_id_type']."|".$v['m_id_number'];
							
							if (!in_array($push_value, $arr_people)) {
								$arr_people[]=$push_value;
							}
						}
						else{
							//团队
							$and_cond='';
							$and_cond=$and_cond.' and order_id="'.addslashes($v['id']).'" ' ;
							$and_cond=$and_cond.' and member_id="'.addslashes($userinfo['user_id']).'" ';
							//echo $and_cond;exit;
							$groupBy= 't_realname,t_id_type,t_id_number ';
							$order_teamMod = M('order_team');
					        $order_team_list_groupby = $order_teamMod->where(" 1 ".$and_cond )->group($groupBy)->select();
					        //echo "<pre>";print_r($order_team_list_groupby);exit;
					        //$chengyuan_number=count($order_team_list_groupby);
					        //echo $chengyuan_number;exit;
					        if(!empty($order_team_list_groupby)){
					        	foreach($order_team_list_groupby as $k_team_person=>$v_team_person){
					        		$push_value=$v_team_person['t_realname']."|".$v_team_person['t_id_type']."|".$v_team_person['t_id_number'];
					        		
					        		if (!in_array($push_value, $arr_people)) {
										$arr_people[]=$push_value;
									}
									
					        	}
					        }
						}
					}
					
					//精英部分
					if($v['price_type']==1){
						
						
						//计算人头数，去掉重复的人。
						$chengyuan_number=1;
						if($v['user_type']==2){
							$and_cond='';
							$and_cond=$and_cond.' and order_id="'.addslashes($v['id']).'" ' ;
							$and_cond=$and_cond.' and member_id="'.addslashes($userinfo['user_id']).'" ';
							//echo $and_cond;exit;
							$groupBy= 't_realname,t_id_type,t_id_number ';
							$order_teamMod = M('order_team');
					        $order_team_list_groupby = $order_teamMod->where(" 1 ".$and_cond )->group($groupBy)->select();
					        //echo "<pre>";print_r($order_team_list_groupby);exit;
					        $chengyuan_number=count($order_team_list_groupby);
					        //echo $chengyuan_number;exit;
						}
						
						
						$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$v['race_id'];
						//echo $api_url;echo "<br>";exit;
						$api_para=array();
						$raceInfo=$this->http_request_url_post($api_url,$api_para);
					    //echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
						//echo "<pre>";print_r($raceInfo['RaceInfo']['PriceList']);echo "</pre>";exit;
						
						
						
						
						
						
						
						if($order_info['user_type']==2){
							//团队报名
							$x_price=$raceInfo['RaceInfo']['PriceList'][2];
							
						}
						else{
							//个人报名
							$x_price=$raceInfo['RaceInfo']['PriceList'][1];
							
						}
						
						
						
						$price_race=$x_price*$chengyuan_number;
						
						$all_amount_total=$all_amount_total+$price_race;
						//$amount_total=$price_race+$order_info['price_product'];
						
						$orderMod = M('order');
				    	$sql=sprintf("update %s SET price_race='".addslashes($price_race)."' 
				    		,price_type_2_number='".addslashes($chengyuan_number)."' 
				    		,price_type_2_jieti='".addslashes($x_price)."' 
					    where id='".addslashes($v['id'])."' 
					    ", $orderMod->getTableName() );
					    $result = $orderMod->execute($sql);
					    
					    
					}
					
					
					//echo "<pre>";print_r($arr_people);exit;
					
					//echo $push_value;exit;
					
					
					//下面不要了
					
					/*
					if(empty($stage_price)){
						//单票的情况，累加每个比赛的价格。(这里实际走不到)
						$all_amount_total=$all_amount_total+$v['price_race'];
					}
					else{
						//通票的情况，只计算比赛名字包含精英的比赛价格。如果只选了1个精英的，则不要算上通票的钱。
						
						
						$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$v['race_id'];
						//echo $api_url;echo "<br>";exit;
						$api_para=array();
						$raceInfo=$this->http_request_url_post($api_url,$api_para);
					    //echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
						
						//通票情况下，不是精英的，用通票价格
						if(empty($raceInfo['RaceInfo']['PriceList'])){
							$tongpiao_price=$stage_price;
						}
						else{
							$all_amount_total=$all_amount_total+$v['price_race'];
						}
						
						
					}
					*/
					
					
					//算上产品价格
					$all_amount_total=$all_amount_total+$v['price_product'];
					
					
					//每个订单的商品
					$and_cond='';
					$and_cond=$and_cond.' and order_id=' . addslashes($v['id']) ;
					$order_productMod = M('order_product');
			        $product_list = $order_productMod->where(" 1 ".$and_cond )->select();
			        $order_all[$k]['product_list']=$product_list;
			        //echo "<pre>";print_r($product_list);exit;
			        
					
				}
				
				
				
			}
			
			
			//echo "<pre>";print_r($order_all);exit;
			
			
			
			//计算非精英部分的总价
			if(!empty($arr_people)){
				
				$chengyuan_number=count($arr_people);
				
				if(isset($RaceStageInfo['comment']['PriceList']) && !empty($RaceStageInfo['comment']['PriceList'])){
					
			        $PriceList=$RaceStageInfo['comment']['PriceList'];
			        //echo "<pre>";print_r($PriceList);exit;
					
					
			        ksort($PriceList);
			        //echo "<pre>";print_r($PriceList);exit;
			        
			        $x_price=$PriceList[1];
			        foreach($PriceList as $k=>$v){
			        	if($chengyuan_number>=$k){
			        		$x_price=$v;
			        	}
			        }
				    
					
					//echo $x_price;exit;
					
					$price_race=$x_price*$chengyuan_number;
					
					$all_amount_total=$all_amount_total+$price_race;
					
					//echo $price_race;exit;
					
					
				}
				else{
					//不可能走到这里
					exit;
				}
				
			}
			
			//echo $price_race;exit;
			$str_order_id=implode(",", $arr_order_id);
			//echo $str_order_id;exit;
			//echo "<pre>";print_r($arr_people);exit;
			
			//写入非精英部分的总价
			$orderMod = M('order');
	    	$sql=sprintf("update %s SET price_race='".addslashes($price_race)."' 
	    		, price_type_2_number='".addslashes($chengyuan_number)."' 
	    		, price_type_2_jieti='".addslashes($x_price)."' 
		    where id in (".addslashes($str_order_id).") 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
			//exit;
			
			//echo $all_amount_total;exit;
			
			
			
			//写入全部的总价
			//用最新的总价 all_amount_total 更新到每个循环体的 amount_total 字段里
		    $orderMod = M('order');
			$sql=sprintf("update %s SET amount_total='".addslashes($all_amount_total)."'  
		    where order_no='".addslashes($order_no)."' and isPay=0 
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
		    
		    
		    $this->set_log_sql($sql);
		    
		    
			
			
			
			//通票的情况，不管选多少比赛，都用通票价格。
			//if(!empty($stage_price)){
			//	$all_amount_total=$all_amount_total+$tongpiao_price;
			//}
			//echo $all_amount_total;exit;
			
			
			
			$this->assign('all_amount_total', $all_amount_total);
			$this->assign('all_amount_total_format', number_format($all_amount_total, 2, '.', ''));
			//echo "<pre>";print_r($order_all);exit;
			
			
			
			
			
			
			
			
			
			//此时的$order_all不是最新的，要重新拿一次计算后的、最新的$order_all。
			
			$product_all_arr=array();//所有产品
			$order_price_type_1=array();//price_type为1，用了单票价格的
			$order_price_type_2=array();//price_type为2，用了通票价格的
			
			
			
			$and_cond='';
			$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
			$and_cond=$and_cond.' and isPay=0 '  ;
			$orderMod = M('order');
	        $order_all = $orderMod->where(" 1 ".$and_cond )->select();
	        //echo "<pre>";print_r($order_all);exit;
	        
	        
	        
	        //单票类
			$and_cond='';
			$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
			$and_cond=$and_cond.' and isPay=0 '  ;
			$and_cond=$and_cond.' and price_type=1';
			$orderMod = M('order');
	        $order_price_type_1 = $orderMod->where(" 1 ".$and_cond )->select();
	        //echo "<pre>";print_r($order_price_type_1);exit;
	        
	        
	        
	        //通票类
			$and_cond='';
			$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
			$and_cond=$and_cond.' and isPay=0 '  ;
			$and_cond=$and_cond.' and price_type=2';
			$orderMod = M('order');
	        $order_price_type_2 = $orderMod->where(" 1 ".$and_cond )->select();
	        //echo "<pre>";print_r($order_price_type_2);exit;
	        
	        
	        
	        
	        
	        if(!empty($order_all)){
				foreach($order_all as $k=>$v){
					
					//echo "<pre>";print_r($v);exit;
					
			        //产品类
			        $and_cond='';
					$and_cond=$and_cond.' and order_id=' . addslashes($v['id']) ;
					$order_productMod = M('order_product');
			        $product_list = $order_productMod->where(" 1 ".$and_cond )->select();
			        $order_all[$k]['product_list']=$product_list;
			        //echo "<pre>";print_r($product_list);exit;
			        
			        if(!empty($product_list)){
			        	//产品类
			        	$product_all_arr[]=$product_list;
			        }
			        
			        
			        
				}
				
				
			}
	        
	        //echo "<pre>";print_r($product_all_arr);exit;
	        
	        $order_all=empty($order_all)?array():$order_all;
	        $this->assign('order_all', $order_all);
			
			
			
			$this->assign('order_price_type_1', $order_price_type_1);
			$this->assign('order_price_type_2', $order_price_type_2);
			$this->assign('product_all_arr', $product_all_arr);
			
			
			
			if(isset($_POST) && !empty($_POST)){
				
				//echo "<pre>";print_r($_POST);exit;
				
				
				
				
				
				if(isset($_POST['opert']) && $_POST['opert']=='confirm'){
					//点提交订单
					//echo "<pre>";print_r($_POST);exit;
					
					
					//订单确认status=1，开始计算付款过期时间expireDateTime
					$orderMod = M('order');
			    	$sql=sprintf("update %s SET  status='1' 
			    		, isPay='0' 
        				, isExpire='0' 
        				, expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->orderExpireTime) )."' 
				    where order_no='".addslashes($order_info['order_no'])."' 
				    ", $orderMod->getTableName() );
				    $result = $orderMod->execute($sql);
				    
				    
				    
					
					$url=U('order/pay', array('order_id'=>$order_info['id'] , 'order_no'=>$order_info['order_no'] ));
					redirect($url);
					exit;
					
					
				}
				elseif(isset($_POST['opert']) && $_POST['opert']=='delete'){
					//删除循环体
					//echo "<pre>";print_r($_POST);exit;
					
					$order_id=$_POST['order_id'];
					$order_no=$_POST['order_no'];
					
					//echo $order_id;exit;
					
					$and_cond='';
					$and_cond=$and_cond.' and id=' . addslashes($order_id) ;
					$and_cond=$and_cond.' and member_id=' . addslashes($userinfo['user_id']) ;
					$and_cond=$and_cond.' and isPay=0 '  ;
					$and_cond=$and_cond.' and status=0 '  ;
					//echo $and_cond;exit;
					$orderMod = M('order');
			        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
			        //echo "<pre>";print_r($order_data);exit;
			        $order_info=empty($order_data)?array():$order_data[0];
			        //echo "<pre>";print_r($order_info);exit;
			        $this->assign('order_info', $order_info);
			        
			        if(empty($order_info)){
					    exit;
					}
					
					
					
					$order_productMod = M('order_product');
			        $sql=sprintf("DELETE FROM %s 
			        where member_id='".addslashes($userinfo['user_id'])."' and order_id=".addslashes($order_id)." ", $order_productMod->getTableName() );
			        //echo $sql;exit;
			        $result = $order_productMod->execute($sql);
			        
			        
			        
					$order_teamMod = M('order_team');
			        $sql=sprintf("DELETE FROM %s 
			        where member_id='".addslashes($userinfo['user_id'])."' and order_id=".addslashes($order_id)." ", $order_teamMod->getTableName() );
			        //echo $sql;exit;
			        $result = $order_teamMod->execute($sql);
			        
			        
			        
					$orderMod = M('order');
			        $sql=sprintf("DELETE FROM %s 
			        where id=".addslashes($order_id)." ", $orderMod->getTableName() );
			        //echo $sql;exit;
			        $result = $orderMod->execute($sql);
			        
			        
			        
		    		
			        
					$url=U('baoming/ticket_confirm', array( 'order_no'=>$order_no, 'catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
					redirect($url);
					exit;
					
					
				}
				else{
					//点继续添加
					
					$url=U('baoming/ticket_2_start', array('catalog_id'=>$catalog_id , 'stage_id'=>$stage_id ));
					redirect($url);
					exit;
					
				}
				
			}
			
			
			
			
		    /*
		    //用最新的总价 all_amount_total 更新到每个循环体的 amount_total 字段里
		    $orderMod = M('order');
			$sql=sprintf("update %s SET amount_total='".addslashes($all_amount_total)."'  
		    where order_no='".addslashes($order_no)."'  
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
		    */
		    
		    
		    
		    setcookie("order_id", $order_id , time()+$this->cookieExpireTime, "/");
		    
		    
			
			$this->assign('curmenu', '7');
	        $this->display('ticket_confirm');
		}
		else{
			//单票情况，生成订单号order_no，并立即跳到支付页
			
			
			
			//计算人头数，去掉重复的人。
			$chengyuan_number=1;
			if($order_info['user_type']==2){
				$and_cond='';
				$and_cond=$and_cond.' and order_id="'.addslashes($order_id).'" ' ;
				$and_cond=$and_cond.' and member_id="'.addslashes($userinfo['user_id']).'" ';
				//echo $and_cond;exit;
				$groupBy= 't_realname,t_id_type,t_id_number ';
				$order_teamMod = M('order_team');
		        $order_team_list_groupby = $order_teamMod->where(" 1 ".$and_cond )->group($groupBy)->select();
		        //echo "<pre>";print_r($order_team_list_groupby);exit;
		        $chengyuan_number=count($order_team_list_groupby);
		        //echo $chengyuan_number;exit;
			}
			
			
			$api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.info&RaceId='.$order_info['race_id'];
			//echo $api_url;echo "<br>";exit;
			$api_para=array();
			$raceInfo=$this->http_request_url_post($api_url,$api_para);
		    //echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
			//echo "<pre>";print_r($raceInfo['RaceInfo']['PriceList']);echo "</pre>";exit;
			
			
			if(empty($raceInfo['RaceInfo']['PriceList'])){
				//非精英
				
				if(isset($RaceStageInfo['comment']['PriceList']) && !empty($RaceStageInfo['comment']['PriceList'])){
					
			        $PriceList=$RaceStageInfo['comment']['PriceList'];
			        
					if($order_info['user_type']==2){
						//团队报名
						//$x_price=$raceInfo['RaceInfo']['PriceList'][2];
						
				        ksort($PriceList);
				        //echo "<pre>";print_r($PriceList);exit;
				        
				        $x_price=$PriceList[1];
				        foreach($PriceList as $k=>$v){
				        	if($chengyuan_number>=$k){
				        		$x_price=$v;
				        	}
				        }
				        
						
					}
					else{
						//个人报名
						$x_price=$PriceList[1];
						
					}
					
					
				}
				else{
					//不可能走到这里
					exit;
				}
			}
			else{
				//精英
				
				
				//20161007增加此逻辑
				$arr_people=array();  //所有参加的人，含个人和团队
				
				
				//选了 非精英 通票的总共多少独立人，含个人和团队
				if($order_info['user_type']==1){
					$push_value=$order_info['m_realname']."|".$order_info['m_id_type']."|".$order_info['m_id_number'];
					
					if (!in_array($push_value, $arr_people)) {
						$arr_people[]=$push_value;
					}
				}
				else{
					//团队
					$and_cond='';
					$and_cond=$and_cond.' and order_id="'.addslashes($order_info['id']).'" ' ;
					$and_cond=$and_cond.' and member_id="'.addslashes($userinfo['user_id']).'" ';
					//echo $and_cond;exit;
					$groupBy= 't_realname,t_id_type,t_id_number ';
					$order_teamMod = M('order_team');
			        $order_team_list_groupby = $order_teamMod->where(" 1 ".$and_cond )->group($groupBy)->select();
			        //echo "<pre>";print_r($order_team_list_groupby);exit;
			        //$chengyuan_number=count($order_team_list_groupby);
			        //echo $chengyuan_number;exit;
			        if(!empty($order_team_list_groupby)){
			        	foreach($order_team_list_groupby as $k_team_person=>$v_team_person){
			        		$push_value=$v_team_person['t_realname']."|".$v_team_person['t_id_type']."|".$v_team_person['t_id_number'];
			        		
			        		if (!in_array($push_value, $arr_people)) {
								$arr_people[]=$push_value;
							}
							
			        	}
			        }
				}
				//exit;
				
				
				
				
				//echo "<pre>";print_r($arr_people);exit;
				//echo "<pre>";print_r($raceInfo);echo "</pre>";exit;
				
				//计算总价
				if(!empty($arr_people)){
					
					$chengyuan_number=count($arr_people);
					
					if(isset($raceInfo['RaceInfo']['PriceList']) && !empty($raceInfo['RaceInfo']['PriceList'])){
						
				        $PriceList=$raceInfo['RaceInfo']['PriceList'];
				        //echo "<pre>";print_r($PriceList);exit;
						
						
				        ksort($PriceList);
				        //echo "<pre>";print_r($PriceList);exit;
				        
				        $x_price=$PriceList[1];
				        foreach($PriceList as $k=>$v){
				        	if($chengyuan_number>=$k){
				        		$x_price=$v;
				        	}
				        }
					    
						
						//echo $x_price;exit;
						
						$price_race=$x_price*$chengyuan_number;
						
						$all_amount_total=$all_amount_total+$price_race;
						
						//echo $price_race;exit;
						
						
					}
					else{
						//不可能走到这里
						exit;
					}
					
				}
				
				
				
				if($order_info['user_type']==2){
					//团队报名
					//$x_price=$raceInfo['RaceInfo']['PriceList'][2];
					
					
				}
				else{
					//个人报名
					$x_price=$raceInfo['RaceInfo']['PriceList'][1];
					
				}
				
			}
			
			
			$price_race=$x_price*$chengyuan_number;
			
			$amount_total=$price_race+$order_info['price_product'];
			
			$orderMod = M('order');
	    	$sql=sprintf("update %s SET price_race='".addslashes($price_race)."' 
	    		, price_type_2_number='".addslashes($chengyuan_number)."' 
	    		, price_type_2_jieti='".addslashes($x_price)."' 
	    		, amount_total='".addslashes($amount_total)."'
		    where id='".addslashes($order_id)."' and isPay=0 
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
			
			
			$this->set_log_sql($sql);
			
			
			
			/*
			//单票情况下，团队报名情况下，设置过价格阶梯的，考虑价格阶梯。
			if(isset($RaceStageInfo['comment']['PriceList']) && !empty($RaceStageInfo['comment']['PriceList'])){
				if($order_info['user_type']==2){
					
					$and_cond='';
					$and_cond=$and_cond.' and order_id="'.addslashes($order_id).'" ' ;
					$and_cond=$and_cond.' and member_id="'.addslashes($userinfo['user_id']).'" ';
					//echo $and_cond;exit;
					$groupBy= 't_realname,t_id_type,t_id_number ';
					$order_teamMod = M('order_team');
			        $order_team_list_groupby = $order_teamMod->where(" 1 ".$and_cond )->group($groupBy)->select();
			        //echo "<pre>";print_r($order_team_list_groupby);exit;
			        $chengyuan_number=count($order_team_list_groupby);
			        //echo $chengyuan_number;exit;
			        
			        $PriceList=$RaceStageInfo['comment']['PriceList'];
			        ksort($PriceList);
			        //echo "<pre>";print_r($PriceList);exit;
			        $plus_value=1;
			        foreach($PriceList as $k=>$v){
			        	if($chengyuan_number>=$k){
			        		$x_price=$v;
			        	}
			        }
			        $price_race=$x_price*$chengyuan_number;
			        //echo $price_race;exit;
					
					$amount_total=$price_race+$order_info['price_product'];
					
					$orderMod = M('order');
			    	$sql=sprintf("update %s SET price_race='".addslashes($price_race)."' 
			    		, amount_total='".addslashes($amount_total)."' 
				    where id='".addslashes($order_id)."' 
				    ", $orderMod->getTableName() );
				    $result = $orderMod->execute($sql);
				    
				}
			}
			*/
			
			//生成order_no
		    $rand_number=mt_rand(100000, 999999);
			$order_no = date("ymdHis").$rand_number;
	    	//echo $order_no;exit;
	    	
	    	
	    	//订单确认status=1，开始计算付款过期时间expireDateTime
	    	$orderMod = M('order');
	    	$sql=sprintf("update %s SET order_no='".addslashes($order_no)."' 
	    		, status='1' 
	    		, isPay='0' 
		        , isExpire='0' 
		        , expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->orderExpireTime) )."' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
		    
		    setcookie("order_id", $order_id , time()+$this->cookieExpireTime, "/");
		    
		    
			$url=U('order/pay', array('order_id'=>$order_info['id'] , 'order_no'=>$order_info['order_no'] ));
			redirect($url);
			exit;
		}
		
	}
	
	
	
	
	
	

}
?>