<?php
class orderAction extends TAction
{

	
	//示例：http://xracebm201607.loc/order/pay/order_id/1883/order_no/160729104137504413
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
		
		
	    
		//获得用户之前的参赛信息
		$and_cond='';
		//$and_cond=$and_cond.' and member_id=' . addslashes($userinfo['user_id']) ;
		$and_cond=$and_cond.' and id=' . addslashes($order_id) ;
		$and_cond=$and_cond.' and isPay=0 '  ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        $this->assign('order_info', $order_info);
        
        if(empty($order_info)){
		    exit;
		}
		
        
		
    	if(isset($order_info['catalog_id']) && !empty($order_info['catalog_id'])){
		    $catalog_id=$order_info['catalog_id'];
		    $this->assign('catalog_id', $catalog_id);
		}
		else{
	        exit;
		}	
		
		
		if(isset($order_info['stage_id']) && !empty($order_info['stage_id'])){
		    $stage_id=$order_info['stage_id'];
		    $this->assign('stage_id', $stage_id);
		}
		else{
	        exit;
		}
		
		
		if($order_info['user_type']==1 || $order_info['user_type']==2){
			$user_type=$order_info['user_type'];
			$this->assign('user_type', $user_type);
		}
		else{
			exit;
		}
		
		
		if(isset($order_info['group_id']) && !empty($order_info['group_id'])){
		    $group_id=$order_info['group_id'];
		    $this->assign('group_id', $group_id);
		}
		else{
	        exit;
		}
		
		
		if(isset($order_info['race_id']) && !empty($order_info['race_id'])){
		    $race_id=$order_info['race_id'];
		    $this->assign('race_id', $race_id);
		}
		else{
	        exit;
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
		if($is_wxBrowser==1){
    	}
    	else{
    		//如不是来源于微信（app端进入），则不允许微支付，只能支付宝支付
    		$payMode=1;
    	}
    	
    	if($payMode!=1){
    		$payMode=2;
    	}
    	//echo $payMode;exit;
    	
    	
    	
    	
		$and_cond='';
		$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
		$and_cond=$and_cond.' and isPay=0 '  ;
		$orderMod = M('order');
        $order_all = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_all);exit;
        $order_all=empty($order_all)?array():$order_all;
        $this->assign('order_all', $order_all);
		
		
        //加上历史循环金额
		if(!empty($order_all)){
			foreach($order_all as $k=>$v){
				
				//订单确认页已经算好总价，此处不再重复计算。取任意一笔订单的amount_total作为总价来显示到页面里。
				//$all_amount_total=$all_amount_total+$v['price_race'];
				//$all_amount_total=$all_amount_total+$v['price_product'];
				
				//每个订单的商品
				$and_cond='';
				$and_cond=$and_cond.' and order_id=' . addslashes($v['id']) ;
				$order_productMod = M('order_product');
		        $product_list = $order_productMod->where(" 1 ".$and_cond )->select();
		        $order_all[$k]['product_list']=$product_list;
		        //echo "<pre>";print_r($product_list);exit;
		        
				
			}
		}
		
		$all_amount_total=$order_info['amount_total'];
		//echo $all_amount_total;exit;
		$this->assign('amount_total', $all_amount_total);
		$this->assign('all_amount_total', $all_amount_total);
		$this->assign('all_amount_total_format', number_format($all_amount_total, 2, '.', ''));
		//echo "<pre>";print_r($order_all);exit;
		$this->assign('order_all', $order_all);
		
		
    	
    	
    	
    	
    	
		
		//此时的$order_all不是最新的，要重新拿一次计算后的、最新的$order_all。
		
		$product_all_arr=array();//所有产品
		$order_price_type_1=array();//price_type为1，用了单票价格的
		$order_price_type_2=array();//price_type为2，用了通票价格的
		
		
        //单票类
		$and_cond='';
		$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
		$and_cond=$and_cond.' and isPay=0 '  ;
		$and_cond=$and_cond.' and price_type=1';
		$orderMod = M('order');
        $order_price_type_1 = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_price_type_1);exit;
        
        
        
        //通票类
		$and_cond='';
		$and_cond=$and_cond.' and order_no=' . addslashes($order_no) ;
		$and_cond=$and_cond.' and isPay=0 '  ;
		$and_cond=$and_cond.' and price_type=2';
		$orderMod = M('order');
        $order_price_type_2 = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_price_type_2);exit;
        
        
        
        if(!empty($order_all)){
			foreach($order_all as $k=>$v){
				
				//echo "<pre>";print_r($v);exit;
				
		        //产品类
		        $and_cond='';
				$and_cond=$and_cond.' and order_id=' . addslashes($v['id']) ;
				$order_productMod = M('order_product');
		        $product_list = $order_productMod->where(" 1 ".$and_cond )->select();
		        $order_all[$k]['product_list']=$product_list;
		        //echo "<pre>";print_r($product_list);exit;
		        
		        if(!empty($product_list)){
		        	//产品类
		        	$product_all_arr[]=$product_list;
		        }
		        
		        
		        
			}
			
			
		}
        
		
		$this->assign('order_price_type_1', $order_price_type_1);
		$this->assign('order_price_type_2', $order_price_type_2);
		$this->assign('product_all_arr', $product_all_arr);
		
    	
    	
    	
    	
    	
    	
    	
    	
    	$this->assign('order_info', $order_info);
    	$this->assign('payMode', $payMode);
    	$this->assign('order_id', $order_info['id']);
    	$this->assign('order_no', $order_info['order_no']);
    	//$this->assign('amount_total', $order_info['amount_total']);
    	
		
		
	    /*
		//获得所购买的产品信息
		$and_cond='';
		//$and_cond=$and_cond.' and member_id=' . addslashes($userinfo['user_id']) ;
		$and_cond=$and_cond.' and order_id=' . addslashes($order_id) ;
		//echo $and_cond;exit;
		$order_productMod = M('order_product');
        $product_list = $order_productMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($product_list);exit;
        $this->assign('product_list', $product_list);
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
        
        $OrderMod = M('order');
        $sql=sprintf("UPDATE %s SET payMode='".addslashes($payMode)."' 
        where order_no='".addslashes($order_no)."' ", $OrderMod->getTableName() );
        //echo $sql;exit;
        $result = $OrderMod->execute($sql);
        
        
        $return['success']='success';
        echo json_encode($return);
        exit;
        
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