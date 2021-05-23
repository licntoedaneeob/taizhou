<?php
class wxpayAction extends TAction
{
	
	//调试支付完毕后的操作是否正确
	//示例：http://cdmalasong.loc/wxpay/notify_url_test
	public function notify_url_test(){
		
		exit;
		
		$order_no = '160905131626539083';
		$trade_no = '123456';
		
		//$this->paySuccess($order_no, $trade_no);
		
		
    	$OrderMod = M('order');
	    $order_info = $OrderMod->where(" order_no='".addslashes($order_no)."' " )->select();
	    //echo "<pre>";print_r($order_info);echo "<pre>";exit;
	    if(!empty($order_info)){
        	$order_info=$order_info[0];
        	$isPay = $order_info['isPay'];
        	$order_id = $order_info['id'];
        	$status = $order_info['status'];
        	$member_id = $order_info['member_id'];
        	
        	if( ($isPay==0 || $isPay==2) && $status==1 ) {
        		//更新订单表
            	$payDateTime = date("Y-m-d H:i:s");
            	
		        //echo "<pre>";print_r($order_info);exit;
		        
            	
            	
            	$sql=sprintf("UPDATE %s SET payDateTime='".addslashes($payDateTime)."' 
		        , isPay='1' 
		        , trade_no='".addslashes($trade_no)."' 
		        , isExpire='1' 
		        where order_no='".addslashes($order_no)."' ", $OrderMod->getTableName() );
		        //echo $sql;exit;
		        $result = $OrderMod->execute($sql);
		        
		        
		        
		        
		        //更新团队报名数据到app提供的接口
		        if($order_info['user_type']==2){
		        	//写入每个成员的信息
		        	
					$and_cond='';
					$and_cond=$and_cond.' and order_id="'.addslashes($order_id).'" ';
					//echo $and_cond;exit;
					$order_teamMod = M('order_team');
			        $order_team_list = $order_teamMod->where(" 1 ".$and_cond )->select();
			        //echo "<pre>";print_r($order_team_list);exit;
			        
			        
			        //临时团队的成员创建到app接口的系统中
			        if(stristr($order_info['chedui_id'],'T')){
			        	if(!empty($order_team_list)){
			        		foreach($order_team_list as $k_team=>$v_team){
			        			$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/save_tbd_user.json?token='.$_SESSION['app_token'];
								//echo $api_url;echo "<br>";exit;
								$api_para=array();
								$api_para['phone']=$v_team['t_mobile'];
								$api_para['name']=$v_team['t_realname'];
								$api_para['id_type']=$v_team['t_id_type'];
								$api_para['id_number']=$v_team['t_id_number'];
								$api_para['sex']=$v_team['t_sex'];
								$api_para['birth_day']=$v_team['t_birth_day'];
								$save_tbd_user_rst=$this->http_request_url_post($api_url,$api_para);
								//echo "<pre>";print_r($save_tbd_user_rst);exit;
								
			        		}
			        	}
			        }
					
		        	
		        	//写入参赛团队名
					$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/put_bm_team.json?token='.$_SESSION['app_token'];
					//echo $api_url;echo "<br>";exit;
					$api_para=array();
					$api_para['name']=$order_info['chedui_name_attend'];
					$api_para['order_id']=$order_id;
					$put_bm_team_rst=$this->http_request_url_post($api_url,$api_para);
					//echo "<pre>";print_r($put_bm_team_rst);exit;
					
					
					if(isset($put_bm_team_rst['id'])){
			        	//写入团队id和用户id关系
						$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/put_bm_team_member.json?token='.$_SESSION['app_token'];
						//echo $api_url;echo "<br>";exit;
						$api_para=array();
						$api_para['bm_team_id']=$put_bm_team_rst['id'];
						$api_para['member_id']=$member_id;
						//echo "<pre>";print_r($api_para);exit;
						$put_bm_team_member_rst=$this->http_request_url_post($api_url,$api_para);
						//echo "<pre>";print_r($put_bm_team_member_rst);exit;
					}
					
		        }
		        //更新团队报名数据到app提供的接口
		        
            	
		        
		        
		        if(isset($_SESSION['order_no'])){
		        	unset($_SESSION['order_no']);
		        }
		        if(isset($_SESSION['ticket_type'])){
		        	unset($_SESSION['ticket_type']);
		        }
		        
		        
		        //同步到user_race用户报名记录表
			    $api_url=BASE_URL."/superdb/user_race_add.php";
		        //echo $api_url;exit;
				$api_para=array();
				$api_para['order_info']=$order_info;
				//echo "<pre>";print_r($api_para);echo "<pre>";exit;
				$api_result=$this->http_request_url_post($api_url,$api_para);
				//echo "<pre>";print_r($api_result);echo "<pre>";exit;
				//同步到user_race用户报名记录表
		        
		        
		        
		        
		        
		        
	        	
	        	//之前会发email，此次不知道是否需要，暂且注释掉。而且此次没有输入email的栏位。
	            //$this->sendMail($order_id, $email, $data);
	            
        	}
        }
		echo "finish";
		exit;
	}



	//示例：http://cdmalasong.loc/wxpay/api/showwxpaytitle/1
	public function api(){
    	
    	//$order_no=$_POST['wx_out_trade_no'];
    	//$total_fee=$_POST['wx_total_fee'];
    	
    	$order_no=$_REQUEST['wx_out_trade_no'];
    	$total_fee=$_REQUEST['wx_total_fee'];
    	
    	$showwxpaytitle=$_GET['showwxpaytitle'];
    	//echo "<pre>";print_r($_REQUEST);exit;
    	
    	
    	/*
    	//启用这段会报错，暂时不清楚原因。
    	//记录支付类型（1支付宝/2微支付）
    	$payMode=2;
    	$OrderMod = M('order');
	    $order_info = $OrderMod->where(" order_no='".addslashes($order_no)."' " )->select();
	    //echo "<pre>";print_r($order_info);echo "<pre>";exit;
	    if(!empty($order_info)){
        	$order_info=$order_info[0];
        	if( ($order_info['isPay']==0 || $order_info['isPay']==2) && $order_info['status']==1 ) {
		        		
	        $OrderMod = M('order');
	        $sql=sprintf("UPDATE %s SET payMode='".addslashes($payMode)."' 
	        where order_no='".addslashes($order_no)."' ", $OrderMod->getTableName() );
	        //echo $sql;exit;
	        $result = $OrderMod->execute($sql);
	        
		}
	}
      */
    	
    	
    	
    	require_once APP_PATH .'Lib/wxpay/WxPayPubHelper/WxPay.pub.config.php';
    	require_once APP_PATH .'Lib/wxpay/WxPayPubHelper/WxPayPubHelper.php';
    	
		
		$this->setConfigInfo();
		
		$jsApi = new JsApi_pub();

		//$this->load->helper('cookie');

		$code = $_GET['code'];

		if ($code == '')
		{
			
			if($order_no == '')
			{
				echo 'Please correct the data submitted';
				exit;
			}

			setcookie("wxpay_order_no", $order_no , time()+$this->cookieExpireTime, "/");
			setcookie("wxpay_total_fee", $total_fee , time()+$this->cookieExpireTime, "/");
			
			//触发微信返回code码
			$url = $jsApi->createOauthUrlForCode(WxPayConf_pub::$JS_API_CALL_URL."?showwxpaytitle=1");
			Header("Location: $url");
			exit;
		}
		else
		{
            //获取code码，以获取openid
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
		}
		
		if(isset($_COOKIE['wxpay_order_no'])) {
			$order_no = $_COOKIE['wxpay_order_no'];
		}
		else {
			echo "cookie failed";
			exit;
		}

		if(isset($_COOKIE['wxpay_total_fee'])) {
			$total_fee = $_COOKIE['wxpay_total_fee'];
		}
		else {
			echo "cookie failed";
			exit;
		}
		
		
		
		
    	$CityMod = M('order');
	    $order_info = $CityMod->where(" order_no='".addslashes($order_no)."' " )->select();
	    if(!empty($order_info)){
        	$order_info=$order_info[0];
	    }
	    else{
	    	echo "get order info failed";
			exit;
	    }
	    
	    if(isset($order_info['amount_total']) && $order_info['amount_total']==$total_fee ){
	    }
	    else{
	    	echo "get order fee failed";
			exit;
	    }
	    
	    

		$unifiedOrder = new UnifiedOrder_pub();

		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		$unifiedOrder->setParameter("openid","$openid");//商品描述
		$unifiedOrder->setParameter("body","微信安全支付");//商品描述
		//自定义订单号，此处仅作举例
		$timeStamp = time();
		//$out_trade_no = WxPayConf_pub::APPID."$timeStamp";
		$unifiedOrder->setParameter("out_trade_no", "$order_no");//商户订单号
		$unifiedOrder->setParameter("total_fee", (string)($total_fee * 100));//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::$NOTIFY_URL);//通知地址
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号
		//$unifiedOrder->setParameter("attach",$from);//附加数据
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID

		$prepay_id = $unifiedOrder->getPrepayId();
		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);

		$jsApiParameters = $jsApi->getParameters();
		//echo $jsApiParameters;

		setcookie("wxpay_order_no","",time()-1);
		setcookie("wxpay_total_fee","",time()-1);

		
		$this->assign('order_no', $order_no);
    	$this->assign('total_fee', $total_fee);
    	$this->assign('jsApiParameters', $jsApiParameters);
    	
    	
        $this->display('api');
        
    }



	function notify_url()
	{
		require_once APP_PATH .'Lib/wxpay/log_.php';
		require_once APP_PATH .'Lib/wxpay/WxPayPubHelper/WxPay.pub.config.php';
		require_once APP_PATH .'Lib/wxpay/WxPayPubHelper/WxPayPubHelper.php';
		

		$this->setConfigInfo();

		//使用通用通知接口
		$notify = new Notify_pub();

		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify->saveData($xml);

		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;

		//==商户根据实际情况设置相应的处理流程，此处仅作举例=======

		//以log文件形式记录回调信息
		$log_ = new Log_();
		$log_name = UPLOAD_WEIXIN_PATH."weixin/notify_url.log";//log文件路径
		$log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");

		if($notify->checkSign() == TRUE)
		{
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
			}
			else{
				//此处应该更新一下订单状态，商户自行增删操作
				$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");

				$order_no = $notify->data["out_trade_no"];
				$trade_no = $notify->data["transaction_id"];
				
				//$this->paySuccess($order_no, $trade_no);
				
				
				
		    	$OrderMod = M('order');
			    $order_info = $OrderMod->where(" order_no='".addslashes($order_no)."' " )->select();
			    //echo "<pre>";print_r($order_info);echo "<pre>";exit;
			    if(!empty($order_info)){
		        	$order_info=$order_info[0];
		        	$isPay = $order_info['isPay'];
		        	$order_id = $order_info['id'];
		        	$status = $order_info['status'];
		        	$member_id = $order_info['member_id'];
		        	
		        	if( ($isPay==0 || $isPay==2) && $status==1 ) {
		        		//更新订单表
		            	$payDateTime = date("Y-m-d H:i:s");
		            	
		            	
				        //echo "<pre>";print_r($order_info);exit;
				        
		            	
		            	$sql=sprintf("UPDATE %s SET payDateTime='".addslashes($payDateTime)."' 
				        , isPay='1' 
				        , trade_no='".addslashes($trade_no)."' 
				        , isExpire='1' 
				        where order_no='".addslashes($order_no)."' ", $OrderMod->getTableName() );
				        //echo $sql;exit;
				        $result = $OrderMod->execute($sql);
				        
				        
				        
				        
				        
				        //迷你跑支付完成后直接通过审核
				        if($order_info['cat_id']==3){
				        	$sql=sprintf("UPDATE %s SET status_attach='1' where order_no='".addslashes($order_no)."' ", $OrderMod->getTableName() );
					        //echo $sql;exit;
					        $result = $OrderMod->execute($sql);
			        	}
			        	
			        	
				        
				        
				        if($order_info['cat_id']==1){
			        	$cat_name='马拉松';
			        	}
			        	if($order_info['cat_id']==2){
			        	$cat_name='半程马拉松';
			        	}
			        	if($order_info['cat_id']==3){
			        	$cat_name='迷你马拉松';
			        	}
			        	
			        	//短信通知
			        	if($this->open_sms_msg==1){
			        	$msg_body='尊敬的'.$order_info['realname'].'，您已成功支付2017台州国际马拉松赛'.$cat_name.'项目的参赛费，请等待审核。';
					$api_url=$this->api_prefix_base.'/service/sendsms';
					//echo $api_url;exit;
					$api_para=array();
					$api_para['mobile']=$order_info['mobile'];   
					$api_para['content']=$msg_body;   
					$api_para['type']='paotuan';   
					$api_para['customSignature']='台州国际马拉松赛';   
					$api_para['namespace']=$this->api_prefix_namespace;
					//echo $api_url;echo "<br>";
					//echo "<pre>";print_r($api_para);echo "</pre>";
					//exit;
					$api_result=$this->http_request_url_post($api_url,$api_para);
					//var_dump($api_result);
					//echo "<pre>";print_r($api_result);exit;
					if(isset($api_result['code']) && $api_result['code']==0){
					}
					else{
					}
					}
					
					
				        /*
			        	if($order_info['cat_id']==1){
			        	$cat_name='马拉松';
			        	}
			        	if($order_info['cat_id']==2){
			        	$cat_name='半程马拉松';
			        	}
			        	if($order_info['cat_id']==3){
			        	$cat_name='迷你马拉松';
			        	}
			        	if($order_info['cat_id']==4){
			        	$cat_name='公益马拉松';
			        	}
			        	if($order_info['cat_id']==5){
			        	$cat_name='公益半程马拉松';
			        	}
			        	
			        	//邮件通知
			        	if($this->open_email_msg==1){
						$to=$order_info['email'];
						$name=$order_info['realname'];
						$subject='成都国际马拉松赛组委会通知';
						$msg_body='尊敬的'.$order_info['realname'].'，恭喜您已成功报名东风日产2017成都国际马拉松赛'.$cat_name.'项目，撒花~您可于9月8日10:00登陆官网查询参赛号码，感谢您的支持和参与！期待在赛道上与您相遇！【成都国际马拉松组委会】';
						$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
			        	}
			        	
			        	//短信通知
			        	if($this->open_sms_msg==1){
			        	$msg_body='尊敬的'.$order_info['realname'].'，恭喜您已成功报名东风日产2017成都国际马拉松赛'.$cat_name.'项目，撒花~您可于9月8日10:00登陆官网查询参赛号码，感谢您的支持和参与！期待在赛道上与您相遇！【成都国际马拉松组委会】';
						header("Content-type:text/html; charset=UTF-8");
						require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
						$clapi  = new ChuanglanSmsApi();
						$code = mt_rand(100000,999999);
						$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
						//echo "<pre>";print_r($result_sms);exit;
			        	}
			        	*/
			        	
			        	
				        
				        if(isset($_SESSION['order_no'])){
				        	unset($_SESSION['order_no']);
				        }
				        
				        
			        	
			        	//之前会发email，此次不知道是否需要，暂且注释掉。而且此次没有输入email的栏位。
			            //$this->sendMail($order_id, $email, $data);
			            
		        	}
		        }
				
				
				
			}

			//商户自行增加处理流程,
			//例如：更新订单状态
			//例如：数据库操作
			//例如：推送支付完成信息
		}
		
		exit;
	}

	function setConfigInfo()
	{
		WxPayConf_pub::$APPID = WX_APPID;
		WxPayConf_pub::$APPSECRET = WX_APPSECRET;
		WxPayConf_pub::$MCHID = WX_MCHID;
		WxPayConf_pub::$KEY = WX_KEY;
		WxPayConf_pub::$JS_API_CALL_URL = BASE_URL."/wxpay/api";
		WxPayConf_pub::$NOTIFY_URL = BASE_URL."/wxpay/notify_url";
		
	}
	



}
?>