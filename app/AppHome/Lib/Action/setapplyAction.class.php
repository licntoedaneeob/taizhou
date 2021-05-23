<?php
class setapplyAction extends TAction
{

	
	
	//将数据设为中签  http://cdmalasong.loc/setapply/index
	public function index(){
		
		exit;
		
		ini_set('memory_limit', '1024M');
		set_time_limit(86400);
		
		$and_cond='';
		//echo $and_cond;exit;
		$invitMod = M('order_apply_1');
        $apply_data = $invitMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($apply_data);exit;
        
        if(!empty($apply_data)){
	        foreach($apply_data as $k=>$v){
	        	
	        	$and_cond='';
	        	//$and_cond=$and_cond.' and status=1 and confirm_apply=1 and id_type="'.addslashes($v['id_type']).'" and id_number="'.addslashes($v['id_number']).'"  ';
	        	$and_cond=$and_cond.' and status=1 and confirm_apply=1 and id_number="'.addslashes($v['id_number']).'"  ';
				//echo $and_cond;exit;
				$orderMod = M('order');
		        $order_data = $orderMod->field('id,id_type,id_number,mobile,realname,cat_id,email')->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        if(!empty($order_data)){
		        	$order_info=$order_data[0];
		        	//echo "<pre>";print_r($order_info);exit;
		        	
		        	$orderMod = M('order');
				    $sql=sprintf("update %s SET status_apply='1' 
				    where status=1 and confirm_apply=1 and id_type='".addslashes($order_info['id_type'])."' and id_number='".addslashes($order_info['id_number'])."'
				    ", $orderMod->getTableName() );
				    //echo $sql;exit;
				    $result = $orderMod->execute($sql);
				    
				    
		        	if($order_info['cat_id']==1){
		        	$cat_name='马拉松';
		        	}
		        	if($order_info['cat_id']==2){
		        	$cat_name='半程马拉松';
		        	}
		        	if($order_info['cat_id']==3){
		        	$cat_name='迷你马拉松';
		        	}
		        	
				    
		        }
		        else{
					$order_applyMod = M('order_apply_1');
				    $sql=sprintf("update %s SET is_error='1' 
				    where id='".addslashes($v['id'])."' 
				    ", $order_applyMod->getTableName() );
				    //echo $sql;exit;
				    $result = $order_applyMod->execute($sql);
		        }
	        }
        }
        
        
        
        
		echo "finish";exit;
        exit;
    }
    
    
    
    
	//没中签的设为没中签  http://cdmalasong.loc/setapply/notapply
	public function notapply(){
		
		exit;
		
		
		ini_set('memory_limit', '1024M');
		set_time_limit(86400);
        
        $orderMod = M('order');
	    $sql=sprintf("update %s SET status_apply='2' 
	    where status=1 and confirm_apply=1 and status_apply='0' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
        
		echo "finish";exit;
        exit;
    }
    
    
    
	//中签用户发消息，没中签用户也发消息  http://cdmalasong.loc/setapply/send/start/0/limit/1
	public function send(){
		
		exit;
		
        ini_set('memory_limit', '1024M');
		set_time_limit(86400);
		
		
        if(isset($_REQUEST['start']) && !empty($_REQUEST['start'])){
        	$start=$_REQUEST['start'];
		}
		else{
		  $start=0;
		}
		
		
		
        if(isset($_REQUEST['limit']) && !empty($_REQUEST['limit'])){
        	$limit=$_REQUEST['limit'];
		}
		else{
		  $limit=1;
		}
		
		
		
        $and_cond='';
    	$and_cond=$and_cond.' and status=1 and confirm_apply=1 and send_apply=0 ';
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_list = $orderMod->field('id,mobile,realname,cat_id,email,status_apply')->where(" 1 ".$and_cond )->order('id asc')->limit( $start.','.$limit )->select();
        //echo "<pre>";print_r($order_list);exit;
        
        
        foreach($order_list as $k=>$v){
	        
	        $order_info=$v;
	        //echo "<pre>";print_r($order_info);exit;
	        
		    
        	if($order_info['cat_id']==1){
        	$cat_name='马拉松';
        	}
        	if($order_info['cat_id']==2){
        	$cat_name='半程马拉松';
        	}
        	if($order_info['cat_id']==3){
        	$cat_name='迷你马拉松';
        	}
        	
        	
	        if($order_info['status_apply']==1){
	        	//发中签消息
	        	
	        	/*
			   //邮件通知
	        	if($this->open_email_msg==1){
					$to=$order_info['email'];
					$name=$order_info['realname'];
					$subject='成都国际马拉松赛组委会通知';
					$msg_body='尊敬的'.$order_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛'.$cat_name.'项目，请于8月14日10:00至8月20日17:00前上传参赛资料，填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
					$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
	        	}
	        	*/
	        	
	        	
	        	/*
	        	//短信通知
	        	if($this->open_sms_msg==1){
	        		$msg_body='尊敬的'.$order_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛'.$cat_name.'项目，请于8月14日10:00至8月20日17:00前上传参赛资料，填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
					header("Content-type:text/html; charset=UTF-8");
					require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
					$clapi  = new ChuanglanSmsApi();
					$code = mt_rand(100000,999999);
					$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
					//echo "<pre>";print_r($result_sms);exit;
	        	}
	        	*/
	        	
	        }
	        elseif($order_info['status_apply']==2){
	        	//发未中签消息
	        	
	        	/*
			    //邮件通知
	        	if($this->open_email_msg==1){
					$to=$order_info['email'];
					$name=$order_info['realname'];
					$subject='成都国际马拉松赛组委会通知';
					$msg_body='尊敬的'.$order_info['realname'].'，很遗憾您未中签东风日产2017成都国际马拉松赛，感谢您的支持与参与。【成都国际马拉松组委会】';
					$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
	        	}
	        	*/
	        	
	        	
	        	/*
	        	//短信通知
	        	if($this->open_sms_msg==1){
	        		$msg_body='尊敬的'.$order_info['realname'].'，很遗憾您未中签东风日产2017成都国际马拉松赛，感谢您的支持与参与。【成都国际马拉松组委会】';
					header("Content-type:text/html; charset=UTF-8");
					require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
					$clapi  = new ChuanglanSmsApi();
					$code = mt_rand(100000,999999);
					$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
					//echo "<pre>";print_r($result_sms);exit;
	        	}
	        	*/
	        	
	        }
	        else{
	        
	        }
        	
        	$orderMod = M('order');
		    $sql=sprintf("update %s SET send_apply='1' 
		    where id='".addslashes($order_info['id'])."'
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
		    
		    
        }
        
        
		echo "finish";exit;
        exit;
    }
    
    
    
    
    
    
    
    
	//迷你马拉松中签，且填写摆渡车信息的用户直接中签，且发消息  http://cdmalasong.loc/setapply/send_cat_3/start/0/limit/1
	public function send_cat_3(){
		
		exit;
		
        ini_set('memory_limit', '1024M');
		set_time_limit(86400);
		
		
        if(isset($_REQUEST['start']) && !empty($_REQUEST['start'])){
        	$start=$_REQUEST['start'];
		}
		else{
		  $start=0;
		}
		
		
		
        if(isset($_REQUEST['limit']) && !empty($_REQUEST['limit'])){
        	$limit=$_REQUEST['limit'];
		}
		else{
		  $limit=1;
		}
		
		
		
        $and_cond='';
    	$and_cond=$and_cond.' and status=1 and confirm_apply=1 and send_apply=0 and confirm_attach=1 and bus_point!="" and cat_id=3 ';
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_list = $orderMod->field('id,mobile,realname,cat_id,email,status_apply')->where(" 1 ".$and_cond )->order('id asc')->limit( $start.','.$limit )->select();
        //echo "<pre>";print_r($order_list);exit;
        
        
        foreach($order_list as $k=>$v){
	        
	        $order_info=$v;
	        //echo "<pre>";print_r($order_info);exit;
	        
		    
        	if($order_info['cat_id']==1){
        	$cat_name='马拉松';
        	}
        	if($order_info['cat_id']==2){
        	$cat_name='半程马拉松';
        	}
        	if($order_info['cat_id']==3){
        	$cat_name='迷你马拉松';
        	}
        	
        	
	        if($order_info['status_apply']==1){
	        	//发中签消息
	        	
	        	/*
			   //邮件通知
	        	if($this->open_email_msg==1){
					$to=$order_info['email'];
					$name=$order_info['realname'];
					$subject='成都国际马拉松赛组委会通知';
					$msg_body='尊敬的'.$order_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛'.$cat_name.'项目，请于8月14日10:00至8月20日17:00前上传参赛资料，填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
					$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
	        	}
	        	*/
	        	
	        	
	        	/*
	        	//短信通知
	        	if($this->open_sms_msg==1){
	        		$msg_body='尊敬的'.$order_info['realname'].'，恭喜您已中签东风日产2017成都国际马拉松赛'.$cat_name.'项目，请于8月14日10:00至8月20日17:00前上传参赛资料，填写摆渡信息，感谢您的支持和参与！【成都国际马拉松组委会】';
					header("Content-type:text/html; charset=UTF-8");
					require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
					$clapi  = new ChuanglanSmsApi();
					$code = mt_rand(100000,999999);
					$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
					//echo "<pre>";print_r($result_sms);exit;
	        	}
	        	*/
	        	
	        }
	        elseif($order_info['status_apply']==2){
	        	//发未中签消息
	        	
	        	/*
			    //邮件通知
	        	if($this->open_email_msg==1){
					$to=$order_info['email'];
					$name=$order_info['realname'];
					$subject='成都国际马拉松赛组委会通知';
					$msg_body='尊敬的'.$order_info['realname'].'，很遗憾您未中签东风日产2017成都国际马拉松赛，感谢您的支持与参与。【成都国际马拉松组委会】';
					$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
	        	}
	        	*/
	        	
	        	
	        	/*
	        	//短信通知
	        	if($this->open_sms_msg==1){
	        		$msg_body='尊敬的'.$order_info['realname'].'，很遗憾您未中签东风日产2017成都国际马拉松赛，感谢您的支持与参与。【成都国际马拉松组委会】';
					header("Content-type:text/html; charset=UTF-8");
					require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
					$clapi  = new ChuanglanSmsApi();
					$code = mt_rand(100000,999999);
					$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
					//echo "<pre>";print_r($result_sms);exit;
	        	}
	        	*/
	        	
	        }
	        else{
	        
	        }
        	
        	$orderMod = M('order');
		    $sql=sprintf("update %s SET send_apply='1' 
		    where id='".addslashes($order_info['id'])."'
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
		    
		    
        }
        
        
		echo "finish";exit;
        exit;
    }
    
    
    
    
    
	//参赛号配号  http://taizhou.loc/setapply/code
	public function code(){
		
		//exit;
		
		ini_set('memory_limit', '1024M');
		set_time_limit(86400);
		
		$and_cond='';
		//echo $and_cond;exit;
		$invitMod = M('order_code');
        $apply_data = $invitMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($apply_data);exit;
        
        if(!empty($apply_data)){
	        foreach($apply_data as $k=>$v){
	        	
	        	$and_cond='';
	        	$and_cond=$and_cond.' and status=1 and confirm_apply=1 and status_apply=1 and isPay=1 and status_attach=1  and id_type="'.addslashes($v['id_type']).'"  and id_number="'.addslashes($v['id_number']).'"  ';
				//echo $and_cond;exit;
				$orderMod = M('order');
		        $order_data = $orderMod->field('id,id_type,id_number,mobile,realname,cat_id,email')->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        if(!empty($order_data)){
		        	$order_info=$order_data[0];
		        	//echo "<pre>";print_r($order_info);exit;
		        	
		        	$orderMod = M('order');
				    $sql=sprintf("update %s SET match_code='".addslashes($v['match_code'])."' 
				    where id='".addslashes($order_info['id'])."' 
				    ", $orderMod->getTableName() );
				    //echo $sql;exit;
				    $result = $orderMod->execute($sql);
				    
				    $order_applyMod = M('order_code');
				    $sql=sprintf("update %s SET is_error='2' 
				    where id_number='".addslashes($v['id_number'])."' 
				    ", $order_applyMod->getTableName() );
				    //echo $sql;exit;
				    $result = $order_applyMod->execute($sql);
				    
		        }
		        else{
					$order_applyMod = M('order_code');
				    $sql=sprintf("update %s SET is_error='1' 
				    where id_number='".addslashes($v['id_number'])."' 
				    ", $order_applyMod->getTableName() );
				    //echo $sql;exit;
				    $result = $order_applyMod->execute($sql);
		        }
	        }
        }
        
        
        
		echo "finish";exit;
        exit;
    }
    

}
?>