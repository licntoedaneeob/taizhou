<?php
class scanloginAction extends TAction
{

	
	//生成扫码登陆所需的二维码，并埋到页面里。
	// http://mmj.maxbund.com/cms/scanlogin/index
	public function index() {
		
		//var_dump(CGIWWW_HOME);exit;
		
		
		
		$wx_state  = md5(uniqid(rand(), TRUE));
		$_SESSION["wx_state"]    =   $wx_state; //存到SESSION
		//echo $_SESSION["wx_state"];exit;
		
		$goto=U('scanlogin/finish', array('wx_state'=>$wx_state ));
		
		$url=BASE_URL_WEBSITE.U('game/oauth_authorize')."?goto=".$goto."&state=".$wx_state;
		//echo $url;exit;
		
		
		$tempRoot=CGIWWW_ROOT.'public/';
		$tempBase=BASE_URL.'/public/';
		$qrUrl = $url;
		$size=5;
		$qr_pic_url=$this->createQRcode($tempRoot,$tempBase,$qrUrl,$size);
		//echo $qr_pic_url;exit;
		
		
		
		//写入扫码登陆流水表
		$cur_time=time();
		$addtime=date("Y-m-d H:i:s",$cur_time);
		$UserMod = M('weixin_scanlogin');
		$sql=sprintf("INSERT %s SET wx_state='".addslashes($wx_state)."' 
        , is_finish=0 
        , addtime='".$addtime."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
		
		$this->assign('wx_state', $wx_state);
        $this->assign('qr_pic_url', $qr_pic_url);

        $this->display('index');
    }
	
	
	//手机端扫码，授权认证登陆，成功后跳转到此页面。
	// http://mmj.maxbund.com/cms/scanlogin/finish
    public function finish() {
    	
    	
		if(isset($_REQUEST['wx_state'])){
			$wx_state=$_REQUEST['wx_state'];
		}
		else{
			$wx_state='';
		}
		
		if(!empty($wx_state)){
			
			$CityMod = M('weixin_scanlogin');
	        $scanlogin_info = $CityMod->where(" wx_state='".addslashes($wx_state)."' and is_finish=0 " )->select();
	        if(!empty($scanlogin_info)){
	        	if(!empty($_SESSION['WX_INFO']['openid'])){
	        		
	        		$access_token=$this->getAccessToken();
			        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$_SESSION['WX_INFO']['openid']."&lang=zh_CN";
					$user_info = json_decode($this->httpGet($url),true);
					//echo "<pre>";print_r($user_info);exit;
					
					if(isset($user_info['openid']) && $user_info['openid']!=""){
						//获取微信用户信息成功
						
						//注册或调crm接口注册(game控制器里需要去掉注册的代码，或注册代码只能只写一个地方)
						
						//数据库里标记当前wx_state已经完成所有动作，供前端轮巡的js触发跳转
						$mobileMod = M('weixin_scanlogin');
						$sql=sprintf("UPDATE %s SET is_finish=1
				        where wx_state='".addslashes($wx_state)."' 
				        ", $mobileMod->getTableName() );
				        $mobileMod->execute($sql);
				        
					}
					
	        	}
	        }
	        
			echo ' login success ';
			exit;
		}
		
		echo ' login failed ';
		exit;
		
    }
    
    
    //pc前端js轮询此接口，如果返回success，pc前端js跳转到登陆成功后的页面。
    public function wx_is_finishlogin_ajax() {
    	
    	
		if(isset($_REQUEST['wx_state'])){
			$wx_state=$_REQUEST['wx_state'];
		}
		else{
			$wx_state='';
		}
		
		
    	$CityMod = M('weixin_scanlogin');
        $scanlogin_info = $CityMod->where(" wx_state='".addslashes($wx_state)."' and is_finish=1 " )->select();
        if(!empty($scanlogin_info)){
        	$sql=" wx_state='".addslashes($wx_state)."' ";
            $CityMod->where($sql)->delete();
            
            $return['success']='success';
			echo json_encode($return);
			exit;
			
        }
    	
    	$return['success']='failed';
		echo json_encode($return);
		exit;
		
    }
    
    //本方法没什么用，仅仅用来查看当前session里的wx_state的值是什么。
    // http://mmj.maxbund.com/cms/scanlogin/get_wx_state
    public function get_wx_state() {
		echo $_SESSION["wx_state"]."<br>";
		exit;
    }
    
    
    

}
?>