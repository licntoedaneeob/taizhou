<?php
class regAction extends TAction
{


    public function index(){
    	
    	
		/*
		//debug 本地调试注册功能的时候，只需开启这段，其他地方不用改。
		$data['openid'] = 'abc';
		$this->load->view('reg', $data);
		return;
		//debug 本地调试注册功能的时候，只需开启这段，其他地方不用改。
		*/
		
		
		/*
		$code = $_GET['code'];;
		if(empty($code))
		{
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".WX_APPID."&redirect_uri=".BASE_URL."/reg/index?response_type=code&scope=snsapi_base&state=1#wechat_redirect";
			redirect($url);
		}
		else
		{
			$this->load->library('Weixinsdk');
			$openid = $this->weixinsdk->get_openid($code);
		}

		if(isset($openid) && $openid)
		{
			$data['openid'] = $openid;
		}
		else
		{
			redirect("login");
		}
		*/
		
		
        $this->display('index');
    }

	
	
    public function send_mobile_verify()
	{
		
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
	
	
    public function index_sub(){
		
		
		//echo "<pre>";print_r($_POST);exit;
		
		$username = $_POST['username'];
		$password = $_POST['password'];
		$mobile_verification = $_POST['mobile_verification'];
        
        if(empty($username)){
        	$return['success']='操作失败';
	        echo json_encode($return);
	        exit;
        }
        
        if(empty($password)){
        	$return['success']='操作失败';
	        echo json_encode($return);
	        exit;
        }
        
        if(empty($mobile_verification)){
        	$return['success']='操作失败';
	        echo json_encode($return);
	        exit;
        }
        
        $mobile_correct=$this->isMobile($username);
		if(!$mobile_correct){
			$return['success']='手机号有误';
	        echo json_encode($return);
	        exit;
		}
		
		
		$api_url='http://api.xrace.cn:'.$this->api_port.'/sms/verify_sms_code.json?phone='.$username.'&sms_code='.$mobile_verification;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//echo "<pre>";print_r($api_result);exit;
		if($api_result['result']==true){
		}
		else{
			$return['success']='验证码输入错误';
	        echo json_encode($return);
	        exit;
		}
		
        
        
        $password=md5($password);
        
        
        $reg_mode='20160510';  //20160511 或 20160510
        
        if($reg_mode=='20160511'){
			//2016.5.11后的注册机制
			$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/registry_by_phone2.json?phone='.$username.'&pwd='.$password.'&sms_code='.$mobile_verification;
			$api_para=array();
			$api_result=$this->http_request_url_post($api_url,$api_para);
			//var_dump($api_result);echo "<br><br>";
		    $user_reg = empty($api_result)?array():$api_result;
			//echo "<pre>";print_r($user_reg);exit;
			
			if(isset($user_reg['comment'])){
				
				//调app的接口登陆
				$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/login_by_phone.json?phone='.$username.'&pwd='.$password;
				$api_para=array();
				$api_result=$this->http_request_url_post($api_url,$api_para);
			    $user_login = empty($api_result)?array():$api_result;
				//echo "<pre>";print_r($user_login);exit;
				
				if(isset($user_login['access_token']) && !empty($user_login['access_token'])){
	        	
		        	$app_token=$user_login['access_token'];
		        	
		        	$token_rst=$this->token_member('controller',$app_token);
		        	
		        	
					if(isset($token_rst['success']) && $token_rst['success']=='success'){
				    	$return['success']='success';
					}
					else{
						$return['success']=$token_rst['msg'];
					}
					
	        	}
				else{
					$return['success']='操作失败';
		        }
		        
				
				if( !empty($user_reg['comment']) ){
					$_SESSION['reg_comment']=$user_reg['comment'];
					
					$return['success']='success';
			        echo json_encode($return);
			        exit;
				}
				else{
					$return['success']='success';
			        echo json_encode($return);
			        exit;
				}
			}
			else{
				if(isset($user_reg['error_code']) && $user_reg['error_code']==1003){
					$return['success']='您已注册过啦';
				}
				else{
					$return['success']='注册失败';
				}
				echo json_encode($return);
			    exit;
			}
			
			
		}
		else{
			//2016.5.10前的注册机制
			$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/registry_by_phone.json?phone='.$username.'&pwd='.$password.'&sms_code='.$mobile_verification;
			$api_para=array();
			$api_result=$this->http_request_url_post($api_url,$api_para);
			$user_reg = empty($api_result)?array():$api_result;
		    
			if(isset($user_reg['user_id']) && $user_reg['user_id']>0){
				
				//调app的接口登陆
				$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/login_by_phone.json?phone='.$username.'&pwd='.$password;
				$api_para=array();
				$api_result=$this->http_request_url_post($api_url,$api_para);
			    $user_login = empty($api_result)?array():$api_result;
				//echo "<pre>";print_r($user_login);exit;
				
				if(isset($user_login['access_token']) && !empty($user_login['access_token'])){
	        	
		        	$app_token=$user_login['access_token'];
		        	
		        	$token_rst=$this->token_member('controller',$app_token);
		        	
		        	
					if(isset($token_rst['success']) && $token_rst['success']=='success'){
				    	$return['success']='success';
					}
					else{
						$return['success']=$token_rst['msg'];
					}
					
	        	}
				else{
					$return['success']='操作失败';
		        }
		        
				
			}
			else{
		        if(isset($user_reg['error_code'])){
		        	
					if(isset($user_reg['error_code']) && $user_reg['error_code']==1003){
						$return['success']='您已注册过啦';
					}
					else{
						$return['success']='注册失败';
					}
					
		        }
		        else{
		        	$return['success']='操作失败';
		        }
	        }
	        
			echo json_encode($return);
			exit;
		}
		
		
	}


}
?>