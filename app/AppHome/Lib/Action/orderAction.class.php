<?php
class orderAction extends TAction
{

	
	//示例：http://cdmalasong.loc/order/pay?order_id=2385&order_no=170701104129826626
	//order_id为必传，如果传了order_no，则只验证order_no是否与数据库里的order_no一致，不再验证cookie里的order_id。目的是为了调试方便，也是为其他开发端接入做支付链接做准备。
	//进入此页面需要先登陆更为合理。但是支付宝需要右上角浏览器里打开，导致还需再登陆一次，不太友好，故注释需要登陆的相关代码
	public function pay(){
    	
    	
    	if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		    $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['order_no']) && !empty($_REQUEST['order_no'])){
		}
		else{
			if(isset($_COOKIE['order_id']) && $_COOKIE['order_id']==$order_id) {
			}
			else{
			    //exit;
			}
		}
		
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		
		
		/*
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		$this->assign('userinfo', $userinfo);
		*/
		
		
		
		$order_info=$this->verify_body($order_id,false);
		//echo "<pre>";print_r($order_info);exit;
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		
		if($order_info['status_apply']!=1){
			$return['success']='您尚未中签';
	        echo json_encode($return);
	        exit;
		}
		
		if($order_info['isPay']!=0){
			$return['success']='您的支付状态异常，无法再支付。';
	        echo json_encode($return);
	        exit;
		}
		
		//if($order_info['status_attach']!=1){
		//	$return['success']='您详细资料尚未审核通过，无法支付。';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		//if($order_info['status_attach']==1){
		//	$return['success']='您详细资料已经审核通过，无法再支付。';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
		
		
		if(empty($order_info['order_no'])){
	    //支付之前生成order_no
	    $rand_number=mt_rand(100000, 999999);
		$order_no = date("ymdHis").$rand_number;
		//$_SESSION['order_no']=$order_no;
    	//echo $order_no;exit;
		
	    $orderMod = M('order');
    	$sql=sprintf("update %s SET order_no='".addslashes($order_no)."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    
	    
	    $order_info['order_no']=$order_no;
	    }
	    
	    
	    
		
		if(isset($order_info['order_no']) && !empty($order_info['order_no']) ){
		    $order_no=$order_info['order_no'];
		    $this->assign('order_no', $order_no);
		}
		else{
		    exit;
		}
		
		
		
		
		if(isset($_REQUEST['order_no']) && !empty($_REQUEST['order_no']) ){
			if($order_info['order_no']==$_REQUEST['order_no']){
			}
			else{
		    	exit;
			}
		}
		else{
		    
		}
		//echo $order_no;exit;
		
		
		
		//0未定义;1支付宝;2微信;9pc;
		$payMode=$order_info['payMode'];
		$is_wxBrowser=$this->is_wxBrowser();
		//var_dump($is_wxBrowser);exit;
		if($is_wxBrowser==1){
			//$payMode=2;  //如果是微信浏览器，强制用payMode模式=2
    	}
    	else{
    		//如不是来源于微信（app端进入），则不允许微支付，只能支付宝支付
    		$payMode=1;
    	}
    	
    	if($payMode!=1){
    		$payMode=2;
    	}
    	//echo $payMode;exit;
    	
    	
    	//获取并写入总金额
    	$price_race=$this->get_price_race($order_info['cat_id'],$order_info['guoji']);
    	//echo $price_race;exit;
    	$amount_total=$price_race;
    	$orderMod = M('order');
    	$sql=sprintf("update %s SET price_race='".addslashes($price_race)."' 
    		, amount_total='".addslashes($amount_total)."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    
	    
	    
		//判断名额数量是否已满（只要提交过报名申请，就能有付费资格拿到名额。）
		$limit_number=$this->get_limit_number($order_info['cat_id'],$order_id); 
        	if($limit_number=='N'){
        	 // echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("报名类型名额已满 The quota for this Registration type is full");history.back();</script>';
		// exit;
        	}
        	
	    
	    
	    //订单确认status=1，开始计算付款过期时间expireDateTime
	    	$orderMod = M('order');
	    	$sql=sprintf("update %s SET isExpire='0' 
		        , expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->orderExpireTime) )."' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
		    
		    
		    
	    
    	
    	
		$this->assign('amount_total', $amount_total);
		$this->assign('amount_total_format', number_format($amount_total, 2, '.', ''));
		
    	$this->assign('order_info', $order_info);
    	$this->assign('payMode', $payMode);
    	$this->assign('order_id', $order_info['id']);
    	$this->assign('order_no', $order_info['order_no']);
    	
    	/*
    	//生成微信支付二维码
		//$tempRoot='./Uploads/wxpay/';
		//$tempBase='/Uploads/wxpay/';
		$tempRoot=WXPAY_SCAN_UPLOAD.'/';
		$tempBase=WXPAY_SCAN_UPLOAD_URI.'/';
		$qrUrl = BASE_URL.U('wxpay/api', array('showwxpaytitle'=>1,'wx_total_fee'=>$amount_total,'wx_out_trade_no'=>$order_info['order_no']));
		//echo $qrUrl;exit;
		$size=5;
		$qrcode_scan_url=$this->createQRcode($tempRoot,$tempBase,$qrUrl,$size);
		//echo $rst;exit;
		//$this->qrcode_scan_url = $qrcode_scan_url;
		$this->assign('qrcode_scan_url', $qrcode_scan_url);
    	*/
    	
    	
		
        $this->display('pay');
    }
	
	
	//http://xracebm201607.loc/order/pay/order_id/1542/order_no/160422120637393798  支付页面，提交时ajax调用，保存支付方式。
	function paySubmit()
	{
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
    	if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='failed';
	        echo json_encode($return);
	        exit;
		}	
		
		
		
    	if(isset($_REQUEST['order_no']) && !empty($_REQUEST['order_no'])){
		    $order_no=$_REQUEST['order_no'];
		}
		else{
		    $return['success']='failed';
	        echo json_encode($return);
	        exit;
		}	
		
		
		if(isset($_REQUEST['payMode']) && !empty($_REQUEST['payMode'])){
		    $payMode=$_REQUEST['payMode'];
		}
		else{
		    $return['success']='failed';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		
		/*
		$OrderMod = M('order');
        $sql=sprintf("UPDATE %s SET payMode='".addslashes($payMode)."' 
        , isPay='0' 
        , isExpire='0' 
        , expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->orderExpireTime) )."' 
        where id='".addslashes($order_id)."' ", $OrderMod->getTableName() );
        //echo $sql;exit;
        $result = $OrderMod->execute($sql);
        */
        
        
        //记录支付类型（1支付宝/2微支付）
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
        
        
        
        $return['success']='success';
        echo json_encode($return);
        exit;
        
	}
	
	
	
	
	
	
	
	//pc端微信扫码支付
	//示例：http://cdmalasong.loc/order/wxpay_scan?order_id=2385&order_no=170701104129826626
	public function wxpay_scan(){
    	
    	
    	if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		    $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}	
		
		
		if(isset($_REQUEST['order_no']) && !empty($_REQUEST['order_no'])){
		}
		else{
			if(isset($_COOKIE['order_id']) && $_COOKIE['order_id']==$order_id) {
			}
			else{
			    //exit;
			}
		}
		
		
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		
		
		/*
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}
		
		$userinfo=$token_rst['user_info'];
		$userinfo['birth_day']=substr($userinfo['birth_day'],0,10);
		$this->assign('userinfo', $userinfo);
		*/
		
		
		
		$order_info=$this->verify_body($order_id,false);
		//echo "<pre>";print_r($order_info);exit;
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		
		if($order_info['status_apply']!=1){
			$return['success']='您尚未中签';
	        echo json_encode($return);
	        exit;
		}
		
		if($order_info['isPay']!=0){
			$return['success']='您的支付状态异常，无法再支付。';
	        echo json_encode($return);
	        exit;
		}
		
		
		//if($order_info['status_attach']!=1){
		//	$return['success']='您详细资料尚未审核通过，无法支付。';
	      //  echo json_encode($return);
	       // exit;
		//}
		
		//if($order_info['status_attach']==1){
		//	$return['success']='您详细资料已经审核通过，无法再支付。';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
		
		
		
		
		if(isset($order_info['order_no']) && !empty($order_info['order_no']) ){
		    $order_no=$order_info['order_no'];
		    $this->assign('order_no', $order_no);
		}
		else{
		    exit;
		}
		
		
		
		
		if(isset($_REQUEST['order_no']) && !empty($_REQUEST['order_no']) ){
			if($order_info['order_no']==$_REQUEST['order_no']){
			}
			else{
		    	exit;
			}
		}
		else{
		    
		}
		//echo $order_no;exit;
		
		
		
		//0未定义;1支付宝;2微信;9pc;
		$payMode=$order_info['payMode'];
		$is_wxBrowser=$this->is_wxBrowser();
		//var_dump($is_wxBrowser);exit;
		if($is_wxBrowser==1){
			//$payMode=2;  //如果是微信浏览器，强制用payMode模式=2
    	}
    	else{
    		//如不是来源于微信（app端进入），则不允许微支付，只能支付宝支付
    		$payMode=1;
    	}
    	
    	if($payMode!=1){
    		$payMode=2;
    	}
    	//echo $payMode;exit;
    	
    	
    	//获取并写入总金额
    	$price_race=$this->get_price_race($order_info['cat_id'],$order_info['guoji']);
    	//echo $price_race;exit;
    	$amount_total=$price_race;
    	$orderMod = M('order');
    	$sql=sprintf("update %s SET price_race='".addslashes($price_race)."' 
    		, amount_total='".addslashes($amount_total)."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    
	    
	    
	    
		//判断名额数量是否已满（只要提交过报名申请，就能有付费资格拿到名额。）
        	$limit_number=$this->get_limit_number($order_info['cat_id'],$order_id); 
        	if($limit_number=='N'){
        	  //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("报名类型名额已满 The quota for this Registration type is full");history.back();</script>';
		 //exit;
        	}
        	
	    
	    //订单确认status=1，开始计算付款过期时间expireDateTime
	    	$orderMod = M('order');
	    	$sql=sprintf("update %s SET isExpire='0' 
		        , expireDateTime='".date("Y-m-d H:i:s", ($addtime_t + $this->orderExpireTime) )."' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
		    
		    
		    
	    
	    
	    //记录支付类型（1支付宝/2微支付）
	    $payMode=2;
	    $OrderMod = M('order');
	        $sql=sprintf("UPDATE %s SET payMode='".addslashes($payMode)."' 
	        where id='".addslashes($order_id)."'  ", $OrderMod->getTableName() );
	        //echo $sql;exit;
	        $result = $OrderMod->execute($sql);
	        
	
    	
    	
		$this->assign('amount_total', $amount_total);
		$this->assign('amount_total_format', number_format($amount_total, 2, '.', ''));
		
    	$this->assign('order_info', $order_info);
    	$this->assign('payMode', $payMode);
    	$this->assign('order_id', $order_info['id']);
    	$this->assign('order_no', $order_info['order_no']);
    	
    	
    	//生成微信支付二维码
		//$tempRoot='./Uploads/wxpay/';
		//$tempBase='/Uploads/wxpay/';
		$tempRoot=WXPAY_SCAN_UPLOAD.'/';
		$tempBase=WXPAY_SCAN_UPLOAD_URI.'/';
		$qrUrl = BASE_URL.U('wxpay/api', array('showwxpaytitle'=>1,'wx_total_fee'=>$amount_total,'wx_out_trade_no'=>$order_info['order_no']));
		//echo $qrUrl;exit;
		$size=4;
		$qrcode_scan_url=$this->createQRcode($tempRoot,$tempBase,$qrUrl,$size);
		//echo $rst;exit;
		//$this->qrcode_scan_url = $qrcode_scan_url;
		$this->assign('qrcode_scan_url', $qrcode_scan_url);
    	
    	
    	
		
        $this->display('wxpay_scan');
    }
	
	
	
	
	//微支付页面，支付完毕后，ajax调用。
	function wx_paySuccess()
	{
		
		
		//微支付完成后 运行到这里
		
		if(isset($_REQUEST['order_no']) && !empty($_REQUEST['order_no'])){
		    $order_no=$_REQUEST['order_no'];
		}
		else{
		    $return['success']='failed';
	        echo json_encode($return);
	        exit;
		}	
		
		/*
		$token_rst=$this->token_member('controller');
		$userinfo=$token_rst['user_info'];
		
		if($token_rst['success']=='success' && !empty($userinfo['user_id']) ){
		}
		else{
			$return['success']='failed';
	        echo json_encode($return);
	        exit;
		}
		*/
		
		
		//and member_id='".addslashes($userinfo['user_id'])."'
    	$OrderMod = M('order');
	    $order_info = $OrderMod->where(" order_no='".addslashes($order_no)."' " )->select();
	    if(!empty($order_info)){
        	$order_info=$order_info[0];
        	$isPay = $order_info['isPay'];
        	
        	//如果isPay已经是2，说明确认中，不用任何操作。如果isPay已经是1，说明微支付支付成功且回调成功，不用任何操作。否则置2。
        	if($isPay == 2) {
        		//return $this->get_json_data(Config_sys::ERRCODE_FAIL, '确认中');
        		$return['success']='确认中';
		        echo json_encode($return);
		        exit;
        	}
            elseif($isPay == 1) {
            	//return $this->get_json_data(Config_sys::ERRCODE_FAIL, '已成功支付');
            	$return['success']='已成功支付';
		        echo json_encode($return);
		        exit;
            }
            else{
            	$sql=sprintf("UPDATE %s SET isPay='2' 
	        	where order_no='".addslashes($order_no)."' ", $OrderMod->getTableName() );
	        	$result = $OrderMod->execute($sql);
            }
            
	        
        	
        	$return['success']='success';
	        echo json_encode($return);
	        exit;
	        
	        
	    }
	    else{
	    	$return['success']='failed';
	        echo json_encode($return);
	        exit;
	    }
	    	
	    	
	}
	
	
	
	

}
?>