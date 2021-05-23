<?php
class memberAction extends TAction
{
	
	
	//我的 个人中心 
	public function center(){
		
		
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
		
		
		$thumb= $userinfo['thumb'] ? $userinfo['thumb'] : STATICSPATH.'images/face-none.png';
		$this->assign('thumb', $thumb);
		
		
		$this->assign('app_token', $_SESSION['app_token']);
		
        $this->display('center');
	}
	
	
	

	//修改个人信息
	public function index(){
		
		
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
		
		
		$thumb= $userinfo['thumb'] ? $userinfo['thumb'] : STATICSPATH.'images/face-none.png';
		$this->assign('thumb', $thumb);
		
		
		$this->assign('app_token', $_SESSION['app_token']);
		
        $this->display('index');
	}
	
	
	//修改个人信息 提交
	public function index_sub(){
		
		
		if(isset($_REQUEST['realname']) && !empty($_REQUEST['realname'])){
		    $realname=$_REQUEST['realname'];
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		if(isset($_REQUEST['id_number']) && !empty($_REQUEST['id_number'])){
		    $id_number=$_REQUEST['id_number'];
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		if(isset($_REQUEST['birth_day']) && !empty($_REQUEST['birth_day'])){
		    $birth_day=$_REQUEST['birth_day'];
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		
        
        $is_id_number=$this->checkIdCard($_POST['id_number']);
        if(!$is_id_number) {
        	$return['success']='请填写正确的身份证号';
	        echo json_encode($return);
	        exit;
        }
        
        
		
		
        $is_birth_day=$this->isdate($_POST['birth_day']);
        if(!$is_birth_day) {
        	$return['success']='请填写正确的出生年月';
	        echo json_encode($return);
	        exit;
        }
        
		//echo "<pre>";print_r($_POST);exit;
		
		
		
		if($_POST['sex']=='男'){$sex=1;}
		if($_POST['sex']=='女'){$sex=2;}
		
		
        $prov_city_arr=explode(" ", $_POST['prov_city']);
		$m_province=$prov_city_arr[0];
		$m_city=$prov_city_arr[1];
		$m_district=$prov_city_arr[2];
		
		
		
		//更新用户信息。
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/set_user_profile.json?token='.$_SESSION['app_token'];
		//echo $api_url;exit;
		$api_para=array();
		
		$api_para['name']=$_POST['realname'];
		$api_para['sex']=$sex;
		//$api_para['id_type']=$_POST['id_type'];
		$api_para['id_number']=$_POST['id_number'];
		//$api_para['expire_day']=$_POST['expire_day'];
		$api_para['birth_day']=$_POST['birth_day'];
		
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
			$return['success']='success';
	        echo json_encode($return);
	        exit;
		}
		else{
			$return['success']='修改个人信息失败';
	        echo json_encode($return);
	        exit;
		}
		
    }
    
    
    
	//登出
    public function logout(){
    	
    	if(isset($_SESSION['app_token'])){
			unset($_SESSION['app_token']);
		}
		
    	redirect(U('home/index'));
    }

	
	


}
?>