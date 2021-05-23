<?php
class loginAction extends TAction
{


    public function index(){
    	
    	
		//记录来源于微信，区别于app
		$_SESSION['is_wexin']=1;
		
		
        //if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
        //	redirect(U('baoming/index'));
		//}
		
		
        $this->assign('curmenu', '7');
        $this->display('index');
    }


    public function index_sub(){
		
        //if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
        //	$return['success']='is_login';
	    //    echo json_encode($return);
	    //    exit;
		//}
		
		
        if(isset($_POST['dosubmit'])){
			
			$username = $_POST['username'];
	        $password = $_POST['password'];
	        
	        
	        //调app的接口登陆
			$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/login_by_phone.json?phone='.$username.'&pwd='.md5($password);
			$api_para=array();
			$api_result=$this->http_request_url_post($api_url,$api_para);
		    $user_login = empty($api_result)?array():$api_result;
			//echo "<pre>";print_r($user_login);exit;
	        if(isset($user_login['access_token']) && !empty($user_login['access_token'])){
	        	$app_token=$user_login['access_token'];
	        	$token_rst=$this->token_member('controller',$app_token);
				//echo "<pre>";print_r($token_rst);exit;
				if(isset($token_rst['success']) && $token_rst['success']=='success'){
					//$_SESSION['is_login']='yes';
	                //$_SESSION['userinfo']=$result[0];
	                
	                
	                
					if(!empty($_SESSION['login_after_jump_url'])){
						$login_after_jump_url=$_SESSION['login_after_jump_url'];  //拿session
						$return['login_after_jump_url']=$login_after_jump_url;
						$_SESSION['login_after_jump_url'] = '';
					}
					else{
						$return['login_after_jump_url']='';
					}
					
					
			    	$return['success']='success';
			        echo json_encode($return);
			        exit;
				}
				else{
					$return['success']=$token_rst['msg'];
			        echo json_encode($return);
			        exit;
				}
				
				
	        }
	        else{
	        	$return['success']='账号或密码不正确，请重新输入';
		        echo json_encode($return);
		        exit;
	        }
	        
    	}
		
		$return['success']='login_error';
		echo json_encode($return);
		exit;
	}


}
?>