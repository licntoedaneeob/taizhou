<?php
class psw_editAction extends TAction
{


    public function index(){
    	
    	
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
        $this->display('index');
    }
	
	
	
	public function index_sub(){
    	
    	
		if(isset($_REQUEST['pwd']) && !empty($_REQUEST['pwd'])){
		    $pwd=$_REQUEST['pwd'];
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
    	if(isset($_REQUEST['pwd_new']) && !empty($_REQUEST['pwd_new'])){
		    $pwd_new=$_REQUEST['pwd_new'];
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(isset($_REQUEST['pwdConf_new']) && !empty($_REQUEST['pwdConf_new'])){
		    $pwdConf_new=$_REQUEST['pwdConf_new'];
		}
		else{
		    $return['success']='操作失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		//echo "<pre>";print_r($_POST);exit;
		
		
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$return['success']=$token_rst['msg'];
	        echo json_encode($return);
	        exit;
		}
		$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		
		//app那边没给修改密码的接口。
		
		$return['success']='操作失败';
        echo json_encode($return);
        exit;
		
		
    }
    
    
	
	
    
	
	


}
?>