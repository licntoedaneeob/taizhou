<?php
class homeAction extends TAction
{
	
	
	//首页  点击进入   http://xracebm201607.loc/home/index
	public function index(){
		
		$url=U('baoming/index');
		redirect($url);
		exit;
		
		
		
		//记录来源于微信，区别于app
		$_SESSION['is_wexin']=1;
		$app_token=$_SESSION['app_token']; 
		
		if(!empty($app_token)){
			$token_rst=$this->token_member('controller',$app_token);
			//echo "<pre>";print_r($token_rst);exit;
			
			if($token_rst['success']=='success'){
				$url=U('baoming/index');
			}
			else{
				$url=U('login/index');
			}
			
		}
		else{
			$url=U('login/index');
		}
		
		redirect($url);
		exit;
		
	}
	
	
	
	
	
}
?>