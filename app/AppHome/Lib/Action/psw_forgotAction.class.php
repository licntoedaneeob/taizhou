<?php
class psw_forgotAction extends TAction
{


    public function index(){
    	
    	
		
        $this->display('index');
    }
	
	
	
	public function index_sub(){
    	
    	$username = $_POST['username'];
		
		if(empty($username)) {
			$return['success']='请输入手机号';
	        echo json_encode($return);
	        exit;
		}
		
		$mobile_correct=$this->isMobile($username);
		if(!$mobile_correct){
			$return['success']='手机号有误';
	        echo json_encode($return);
	        exit;
		}
		
		
		//短信接口必须连正式8080
		$type=0;
	    //$api_url='http://api.xrace.cn:8080/sms/request_sms_code.json?phone='.$username.'&type='.$type;
	    $api_url='http://api.xrace.cn:'.$this->api_port.'/sms/request_sms_code.json?phone='.$username.'&type='.$type;
	    $api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//var_dump($api_result);exit;   //无论是否发送成功，此接口都返回：请求失败
		
		if($api_result['result']==true){
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
    
    
	
	
    public function check_sms_code(){
    	
    	
		$username = $_GET['username'];
		$this->assign('username', $username);
		
        $this->display('check_sms_code');
    }
    
    
    
    public function check_sms_code_sub(){
    	
    	
    	
    	$username = $_POST['username'];
		$smscode = $_POST['smscode'];
		
		if(empty($username)) {
			$return['success']='请输入手机号';
	        echo json_encode($return);
	        exit;
		}
		
		$mobile_correct=$this->isMobile($username);
		if(!$mobile_correct){
			$return['success']='手机号有误';
	        echo json_encode($return);
	        exit;
		}
		
		
		$api_url='http://api.xrace.cn:'.$this->api_port.'/sms/verify_sms_code.json?phone='.$username.'&sms_code='.$smscode;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		if($api_result['result']==true){
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
    
    
    
	
    public function reset_pwd(){
    	
    	
		$username = $_GET['username'];
		$this->assign('username', $username);
		
		$smscode = $_GET['smscode'];
		$this->assign('smscode', $smscode);
		
		
		if(empty($username)) {
			$return['success']='请输入手机号';
	        echo json_encode($return);
	        exit;
		}
		
		$mobile_correct=$this->isMobile($username);
		if(!$mobile_correct){
			$return['success']='手机号有误';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
        $this->display('reset_pwd');
    }
    
    
    
    
    
    public function reset_pwd_sub(){
    	
    	
    	
    	$username = $_POST['username'];
		$smscode = $_POST['smscode'];
		$password = $_POST['password'];
		
		
		if(empty($username)) {
			$return['success']='请输入手机号';
	        echo json_encode($return);
	        exit;
		}
		
		$mobile_correct=$this->isMobile($username);
		if(!$mobile_correct){
			$return['success']='手机号有误';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(empty($password)) {
			$return['success']='请输入密码';
	        echo json_encode($return);
	        exit;
		}
		
		
		$password=md5($password);
		
		
        $api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/find_pwd.json?phone='.$username.'&pwd='.$password.'&sms_code='.$smscode;
        $api_para=array();
        $api_result=$this->http_request_url_post($api_url,$api_para);
        if($api_result['result']==true){
            $return['success']='success';
	        echo json_encode($return);
	        exit;
        } else{
            $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
        }
		
    }
    
    
    
    
	
	


}
?>