<?php
class ali_payAction extends TAction
{

	//示例：http://xracebm201607.loc/ali_pay/alipayapi
	public function alipayapi(){
    	
    	//echo "<pre>";print_r($_POST);exit;
    	
    	//[WIDout_trade_no] => 160422120637393798
	    //[WIDsubject] => 2016中国马拉松系列赛报名
	    //[WIDtotal_fee] => 5.00
	    //[WIDshow_url] => http://xracebm201607.loc
	    //[WIDbody] => 
	    
    	
		require_once APP_PATH .'Lib/alipay/alipay.config.php';
    	require_once APP_PATH .'Lib/alipay/lib/alipay_submit.class.php';
    	

		/**************************请求参数**************************/

		//商户订单号，商户网站订单系统中唯一订单号，必填
		$out_trade_no = $_POST['WIDout_trade_no'];

		//订单名称，必填
		$subject = $_POST['WIDsubject'];

		//付款金额，必填
		$total_fee = $_POST['WIDtotal_fee'];

		//收银台页面上，商品展示的超链接，必填
		$show_url = $_POST['WIDshow_url'];

		//商品描述，可空
		$body = $_POST['WIDbody'];
		
		
		//是否根据客户端类型指定支付宝接口service
		//$alipay_config['service'] = "create_direct_pay_by_user";
		//$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";
		
		
	        //手机与pc采用不同的service
	        $user_agent=$this->user_agent();
	        //echo $user_agent;exit;
	        if($user_agent=='mobile'){
	        	$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";
	        }
	        else{
	        	$alipay_config['service'] = "create_direct_pay_by_user";
	        }
	        
	        
	        
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
			"service"       => $alipay_config['service'],
			"partner"       => $alipay_config['partner'],
			"seller_id"  => $alipay_config['seller_id'],
			"payment_type"	=> $alipay_config['payment_type'],
			"notify_url"	=> $alipay_config['notify_url'],
			"return_url"	=> $alipay_config['return_url'],
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
			"out_trade_no"	=> $out_trade_no,
			"subject"	=> $subject,
			"total_fee"	=> $total_fee,
			"show_url"	=> $show_url,
			"body"	=> $body,
			//其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
			//如"参数名"	=> "参数值"   注：上一个参数末尾需要“,”逗号。

		);




    	//记录支付类型（1支付宝/2微支付）
    	$order_no=$out_trade_no;
    	$payMode=1;
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
	


		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;
		
		exit;
		
    }


	//异步通知
	function notify_url()
	{
		require_once APP_PATH .'Lib/alipay/alipay.config.php';
    	require_once APP_PATH .'Lib/alipay/lib/alipay_notify.class.php';
    	
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();

		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代


			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

			//商户订单号

			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号

			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];


			if($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
				//如果有做过处理，不执行商户的业务程序

				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
				
				
				$order_no = $out_trade_no;
				$trade_no = $trade_no;
				
				
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
				        
		        	}
		        }
		        
		        
		        
		        
				echo "success";		//请不要修改或删除
			    exit;
			}
			//else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
				//如果有做过处理，不执行商户的业务程序

				//注意：
				//付款完成后，支付宝系统发送该交易状态通知

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			//}
			else {
			    echo "success";		//其他状态判断。普通即时到帐中，其他状态不用判断，直接打印success。
				exit;
			}
    
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			echo "fail";
			exit;

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}

	
	//页面跳转同步通知
	function return_url()
	{
		require_once APP_PATH .'Lib/alipay/alipay.config.php';
    	require_once APP_PATH .'Lib/alipay/lib/alipay_notify.class.php';
    	
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();

		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

			//商户订单号

			$out_trade_no = $_GET['out_trade_no'];

			//支付宝交易号

			$trade_no = $_GET['trade_no'];

			//交易状态
			$trade_status = $_GET['trade_status'];


		    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
					
					
					/*
						$order_no = $out_trade_no;
						$trade_no = $trade_no;
						
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
						        
						        if(isset($_SESSION['order_no'])){
						        	unset($_SESSION['order_no']);
						        }
						        
				        	}
				        }
				        */
		        
		        
					
					
		        $isSuccess = 1;
		    }
		    else {
		        $isSuccess = 0;
		//      echo "trade_status=".$_GET['trade_status'];
		    }
		//	echo "验证成功<br />";

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
		    //验证失败
		    //如要调试，请看alipay_notify.php页面的verifyReturn函数
		    $isSuccess = 0;
		//    echo "验证失败";
		}


		$this->assign('isSuccess', $isSuccess);
		
		
        $this->display('return_url');
	}
	



}
?>