<?php
class baomingAction extends TAction
{

	
	
	//报名须知  http://cdmalasong.loc/baoming/rule
	public function rule(){
		
		
        $id=1;
        $NoticeMod = M('rule');
        $rule_1 =   $NoticeMod->find($id);
		$this->assign('rule_1', $rule_1);
		
        
        $id=2;
        $NoticeMod = M('rule');
        $rule_2 =   $NoticeMod->find($id);
		$this->assign('rule_2', $rule_2);
		
		
        $id=3;
        $NoticeMod = M('rule');
        $rule_3 =   $NoticeMod->find($id);
		$this->assign('rule_3', $rule_3);
		
        
    	$this->assign('curmenu', '7');
        $this->display('rule');
    }
	
	
	
	// 检查验证码  ajax
	public function validate_sub(){
		
		//启用则不判断验证码
		//$return['success']='success';
	        //echo json_encode($return);
	        //exit;
	        
	        
		if (isset($_REQUEST['validate']) && !empty($_REQUEST['validate'])){
                if ($_SESSION["s_validate"]==$_REQUEST['validate']){
                	$return['success']='success';
			        echo json_encode($return);
			        exit;
                }
                else{
                    $return['success']='请输入正确的验证码 Please input Identifying code';
			        echo json_encode($return);
			        exit;
                }
            }
            else{
                $return['success']='请输入正确的验证码 Please input Identifying code';
		        echo json_encode($return);
		        exit;
            }
            
	}
	
	
	
	//报名申请  http://cdmalasong.loc/baoming/apply
	public function apply(){
		
		$orderMod = M('guoji');
        $guoji_list = $orderMod->where(" 1 " )->order('id asc')->limit('0,1000')->select();
        $this->assign('guoji_list', $guoji_list);
        //echo "<pre>";print_r($guoji_list);exit;
        
    	$this->assign('curmenu', '7');
        $this->display('apply');
    }
	
	
	
	
	//报名申请 提交  http://cdmalasong.loc/baoming/apply_sub?cat_id=1&realname=aaa&sex=1&birth_day=1990-01-01&id_type=1&id_number=310105195508120023&address=bbb&mobile=13911112222&email=ccc@ccc.com&cityarea=中国&blood=O
	public function apply_sub(){
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		
		
		
		//注释则不判断验证码
		if (isset($_REQUEST['validate']) && !empty($_REQUEST['validate'])){
                if ($_SESSION["s_validate"]==$_REQUEST['validate']){
                }
                else{
                    $return['success']='请输入正确的验证码 Please input Identifying code';
			        echo json_encode($return);
			        exit;
                }
            }
            else{
                $return['success']='请输入正确的验证码 Please input Identifying code';
		        echo json_encode($return);
		        exit;
            }
            
            
            
		if(isset($_REQUEST['cat_id']) && !empty($_REQUEST['cat_id'])){
		}
		else{
		    $return['success']='请输入报名类型';
	        echo json_encode($return);
	        exit;
		}
		
		$is_correct_cat=$this->get_price_race($_REQUEST['cat_id']);
		if($is_correct_cat<=0){
			$return['success']='报名类型有误';
	          echo json_encode($return);
	          exit;
		}
		
		
		if(isset($_REQUEST['realname']) && !empty($_REQUEST['realname'])){
		}
		else{
		    $return['success']='请输入姓名';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(isset($_REQUEST['sex']) && !empty($_REQUEST['sex'])){
		}
		else{
		    $return['success']='请输入性别';
	        echo json_encode($return);
	        exit;
		}
		
		if(isset($_REQUEST['birth_day']) && !empty($_REQUEST['birth_day'])){
		}
		else{
		    $return['success']='请输入出生年月';
	        echo json_encode($return);
	        exit;
		}
		
		$is_birthday=strtotime($_REQUEST['birth_day']." 00:00:00");
		if($is_birthday==false){
		    $return['success']='出生年月格式错误';
	        echo json_encode($return);
	        exit;
		}
		
        
        if(isset($_REQUEST['id_type']) && !empty($_REQUEST['id_type'])){
        	$id_type=$_REQUEST['id_type'];
		}
		else{
		    $return['success']='请选择证件类型';
	        echo json_encode($return);
	        exit;
		}
		
        
        //echo "<pre>";print_r($_REQUEST);exit;
        $is_id_number=$this->checkIdCard($_REQUEST['id_number']);
        if($id_type==1 && !$is_id_number) {
        	$return['success']='请填写正确的身份证号';
	        echo json_encode($return);
	        exit;
        }
        
        
		if(isset($_REQUEST['address']) && !empty($_REQUEST['address'])){
		}
		else{
		    $return['success']='请输入现居住地';
	        echo json_encode($return);
	        exit;
		}
		
		
        
		if(isset($_REQUEST['mobile']) && !empty($_REQUEST['mobile'])){
		}
		else{
		    $return['success']='请输入手机';
	        echo json_encode($return);
	        exit;
		}
		
		
        $is_mobile=$this->isMobile($_REQUEST['mobile']);
        if(!$is_mobile) {
        	$return['success']='请填写正确的手机号';
	        echo json_encode($return);
	        exit;
        }
        
        
        
		if(isset($_REQUEST['email']) && !empty($_REQUEST['email'])){
		}
		else{
		    $return['success']='请输入邮箱';
	        echo json_encode($return);
	        exit;
		}
        
        
		if(isset($_REQUEST['cityarea']) && !empty($_REQUEST['cityarea'])){
		}
		else{
		    $return['success']='请输入国家/地区';
	        echo json_encode($return);
	        exit;
		}
        
        
		if(isset($_REQUEST['blood']) && !empty($_REQUEST['blood'])){
		}
		else{
		    $return['success']='请输入血型';
	        echo json_encode($return);
	        exit;
		}
        
        
        
        	//判断名额数量是否已满
        	$limit_number=$this->get_limit_number($_REQUEST['cat_id']);  
        	if($limit_number=='N'){
        	  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	        echo json_encode($return);
	        exit;
        	}
		
		
		//判断生日和性别
		//证件类型选身份证的，用身份证提取性别和生日；否则采用填写的性别和生日。
		if($id_type==1){
			$idcard_sex=$this->get_xingbie($_REQUEST['id_number']);   //男or女
			$idcard_birth_arr=$this->get_idcard_birth($_REQUEST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			
			$verify_sex=$idcard_sex;
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
		}
		else{
			$verify_sex=$_REQUEST['sex'];
			$verify_birth=str_replace('-','',$_REQUEST['birth_day']);
			$verify_birthday=$_REQUEST['birth_day'];
		}
		//echo $verify_birthday;exit;
		
		
		
		//身份证与性别生日是否符合
		if($id_type==1){
			$idcard_sex=$this->get_xingbie($_REQUEST['id_number']);   //男or女
			if($idcard_sex=='男'){
				$idcard_sex=1;
			}
			if($idcard_sex=='女'){
				$idcard_sex=2;
			}
			if($idcard_sex!=$_REQUEST['sex']){
				$return['success']='性别与身份证信息不符';
		        echo json_encode($return);
		        exit;
			}
			
			$idcard_birth_arr=$this->get_idcard_birth($_REQUEST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
			if($verify_birthday!=$_REQUEST['birth_day']){
				$return['success']='出生年月与身份证信息不符';
		        echo json_encode($return);
		        exit;
			}
		}
		
		
		//年龄跟哪个时间做比较...提示语：抱歉，请您按照各项目的年龄限定进行报名。
		//1、马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）70岁以下（1947年1月1日以后出生）；
		//2、半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）70岁以下（1947年1月1日以后出生）；
		//3、欢乐跑项目选手年龄限10岁以上（2007年12 月31 日以前出生）；
		//4、公益马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）70岁以下（1947年1月1日以后出生）；
		//5、公益半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）70岁以下（1947年1月1日以后出生）；
		
		if($id_type==1){
			$idcard_birth_arr=$this->get_idcard_birth($_REQUEST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$birth_age_permit=$verify_birth;
		}
		else{
			$birth_age_permit=str_replace('-','',$_REQUEST['birth_day']);
		}
		if($_REQUEST['cat_id']==1){
			if($birth_age_permit>=19470101 && $birth_age_permit<=19971231){
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==2){
			if($birth_age_permit>=19470101 && $birth_age_permit<=20011231){
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==3){
			if($birth_age_permit<=20071231){
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==4){
			if($birth_age_permit>=19470101 && $birth_age_permit<=19971231){
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==5){
			if($birth_age_permit>=19470101 && $birth_age_permit<=20011231){
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		else{
			$return['success']='请输入报名类型';
		        echo json_encode($return);
		        exit;
		}
		
		
		
		//证件号已经提交过报名申请...
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and confirm_apply=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($_REQUEST['id_type']) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($_REQUEST['id_number']) .'" ' ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $nums_used=empty($order_data)?0:count($order_data);
        //var_dump($nums_used);exit;
		
    	if($nums_used > 0 ) {
    		$return['success']='您的证件号码已提交，请勿重复报名。请前往首页查询报名状态！';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		$cityarea=$_REQUEST['cityarea'];
		if ($cityarea=="中国" || $cityarea=="军人"){
		$guoji="0";
		}
		else if ($cityarea=="中华台北" || $cityarea=="中国香港" || $cityarea=="中国澳门" ){
		$guoji="1";
		}
		else{
		$guoji="2";
		}
		//var_dump($guoji);exit;

        
        	$price_race=$this->get_price_race($_REQUEST['cat_id'],$guoji);
        	$amount_total=$price_race;
        	//var_dump($price_race);exit;
        	
		
		$orderMod = M('order');
        $orderMod->status=0;
        $order_id = $orderMod->add();
        //var_dump($order_id);exit;
        
		$orderMod = M('order');
	    $sql=sprintf("update %s SET cat_id='".addslashes($this->remove_xss($_REQUEST['cat_id']))."' 
	    , realname='".addslashes($this->remove_xss($_REQUEST['realname']))."' 
	    , sex='".addslashes($this->remove_xss($_REQUEST['sex']))."' 
    	, birth_day='".addslashes($this->remove_xss($_REQUEST['birth_day']))."' 
    	, id_type='".addslashes($this->remove_xss($_REQUEST['id_type']))."' 
    	, id_number='".addslashes($this->remove_xss($_REQUEST['id_number']))."' 
    	, address='".addslashes($this->remove_xss($_REQUEST['address']))."' 
    	, mobile='".addslashes($this->remove_xss($_REQUEST['mobile']))."' 
    	, email='".addslashes($this->remove_xss($_REQUEST['email']))."' 
    	, cityarea='".addslashes($this->remove_xss($_REQUEST['cityarea']))."' 
    	, guoji='".addslashes($this->remove_xss($guoji))."' 
    	, blood='".addslashes($this->remove_xss($_REQUEST['blood']))."' 
    	, price_race='".addslashes($price_race)."' 
    	, amount_total='".addslashes($amount_total)."' 
    	, addtime_apply='".addslashes($this->remove_xss($addtime))."' 
	    , status='1' 
	    	, reg_channel='官网' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    //$this->set_log_sql($sql);
	    
	    
	    
	    
             $_SESSION['id_type']=$_REQUEST['id_type'];
        	$_SESSION['id_number']=$_REQUEST['id_number'];
        	
        	
	    
        $return = array(
            'order_id' => $order_id,
        );
		$return['success']='success';
		//echo "<pre>";print_r($return);exit;
		
        echo json_encode($return);
        exit;
        
	}
	
	
	
	
	//报名申请  确认页  http://cdmalasong.loc/baoming/apply_confirm?order_id=2385
	public function apply_confirm(){
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		$order_info=$this->verify_body($order_id,true,false);
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		
		
		
		if($order_info['confirm_apply']==1){
			$return['success']='您基本资料已经确认提交，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('apply_confirm');
    }
    
    
    
	//报名申请  确认页 提交  http://cdmalasong.loc/baoming/apply_confirm_sub?order_id=2407
	public function apply_confirm_sub(){
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		$order_info=$this->verify_body($order_id,true,false);
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		//echo "<pre>";print_r($order_info);exit;
		
		
		
		if($order_info['confirm_apply']==1){
			$return['success']='您基本资料已经确认提交，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		//判断名额数量是否已满
        	$limit_number=$this->get_limit_number($order_info['cat_id']);  
        	if($limit_number=='N'){
        	  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	        echo json_encode($return);
	        exit;
        	}
        	
        	
		
		//证件号已经提交过报名申请...
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and confirm_apply=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($order_info['id_type']) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($order_info['id_number']) .'" ' ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $nums_used=empty($order_data)?0:count($order_data);
        //var_dump($nums_used);exit;
		
    	if($nums_used > 0 ) {
    		$return['success']='您的证件号码已提交，请勿重复报名。请前往首页查询报名状态！';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		$cityarea=$_REQUEST['cityarea'];
		if ($cityarea=="中国" || $cityarea=="军人"){
		$guoji="0";
		}
		else if ($cityarea=="中华台北" || $cityarea=="中国香港" || $cityarea=="中国澳门" ){
		$guoji="1";
		}
		else{
		$guoji="2";
		}
		//var_dump($guoji);exit;

        
        	$price_race=$this->get_price_race($_REQUEST['cat_id'],$guoji);
        	$amount_total=$price_race;
        	//var_dump($price_race);exit;
        	
		
		//$orderMod = M('order');
        //$orderMod->status=0;
        //$order_id = $orderMod->add();
        //var_dump($order_id);exit;
        
		$orderMod = M('order');
	    $sql=sprintf("update %s SET confirm_apply='1' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    //$this->set_log_sql($sql);
	    
	    
             $_SESSION['id_type']=$order_info['id_type'];
        	$_SESSION['id_number']=$order_info['id_number'];
        	
        	
        	
        	//邮件通知
        	if($this->open_email_msg==1){
			$to=$order_info['email'];
			$name=$order_info['realname'];
			$subject='成都国际马拉松通知';
			$msg_body='尊敬的'.$order_info['realname'].'，感谢您参与2017年成都国际马拉松赛，您的报名已提交，请等待组委会审核和抽签！ 请在抽签结束后到报名查询菜单里查看是否中签，感谢您的参与！';
			$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
        	}
        	
        	//短信通知
        	if($this->open_sms_msg==1){
        		$msg_body='尊敬的'.$order_info['realname'].'，感谢您参与2017年成都国际马拉松赛，您的报名已提交，请等待组委会审核和抽签！ 请在抽签结束后到报名查询菜单里查看是否中签，感谢您的参与！';
			header("Content-type:text/html; charset=UTF-8");
			require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
			$clapi  = new ChuanglanSmsApi();
			$code = mt_rand(100000,999999);
			$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
			//echo "<pre>";print_r($result_sms);exit;
        	}
        	
        	
        	//公益跑不抽签，直接设为中签
        	if($order_info['cat_id']==4 || $order_info['cat_id']==5){
        	$orderMod = M('order');
		    $sql=sprintf("update %s SET status_apply='1' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
		    
	        	//邮件通知
	        	if($this->open_email_msg==1){
				$to=$order_info['email'];
				$name=$order_info['realname'];
				$subject='成都国际马拉松通知';
				$msg_body='尊敬的'.$order_info['realname'].'，恭喜您在2017年成都国际马拉松赛中签！ 请到网站报名查询栏目中进行下一步操作！';
				$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
	        	}
	        	
	        	//短信通知
	        	if($this->open_sms_msg==1){
	        		$msg_body='尊敬的'.$order_info['realname'].'，恭喜您在2017年成都国际马拉松赛中签！ 请到网站报名查询栏目中进行下一步操作！';
				header("Content-type:text/html; charset=UTF-8");
				require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
				$clapi  = new ChuanglanSmsApi();
				$code = mt_rand(100000,999999);
				$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
				//echo "<pre>";print_r($result_sms);exit;
	        	}
	        	
        	}
        	
	    
        $return = array(
            'order_id' => $order_id,
        );
		$return['success']='success';
		//echo "<pre>";print_r($return);exit;
		
        echo json_encode($return);
        exit;
        
	}
	
	
	//报名申请  结果页  http://cdmalasong.loc/baoming/apply_finish?order_id=2385
	public function apply_finish(){
		
             
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
        	
	    	 $url=U('baoming/order_search_finish', array('order_id'=>$order_id ));
		redirect($url);
		exit;
		
		
    	//$this->assign('curmenu', '7');
        //$this->display('apply_finish');
    }
	
	
	//报名详细资料  http://cdmalasong.loc/baoming/attach?order_id=2385
	public function attach(){
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		$order_info=$this->verify_body($order_id);
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		
		
		if($order_info['status_apply']!=1){
			$return['success']='您尚未中签';
	        echo json_encode($return);
	        exit;
		}
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		$CityMod = M('bus_point');
        $bus_point_list = $CityMod->field('id,title')->where(" status=1 " )->order('id asc')->select();
        //echo "<pre>";print_r($bus_point_list);exit;
		$this->assign('bus_point_list', $bus_point_list);


    	$this->assign('curmenu', '7');
        $this->display('attach');
    }

	
	
	
	//报名详细资料 提交  http://cdmalasong.loc/baoming/attach_sub?order_id=2385&cert_medical=ggg&best_chengji_item=hhh&best_chengji_score=iii&cert_chengji=jjj&bus_point=kkk&cloth_size=XL&ec_name=LLL&ec_relation=夫妻&ec_mobile=13699999999&ec_phone=62001111&ec_address=MMM
	public function attach_sub(){
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		$order_info=$this->verify_body($order_id);
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
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		//判断名额数量是否已满
        	$limit_number=$this->get_limit_number($order_info['cat_id']);  
        	if($limit_number=='N'){
        	  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	        echo json_encode($return);
	        exit;
        	}
        	
        	
        	
		//echo "<pre>";print_r($order_info);exit;
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		//echo $addtime;exit;
		
		
		//if(isset($_REQUEST['cert_medical']) && !empty($_REQUEST['cert_medical'])){
		//}
		//else{
		    //$return['success']='请输入体检证明';
	        //echo json_encode($return);
	        //exit;
		//}
		
		
		
		if(isset($_REQUEST['best_chengji_item']) && !empty($_REQUEST['best_chengji_item'])){
		}
		else{
		    $return['success']='请输入最好成绩项目';
	        echo json_encode($return);
	        exit;
		}
		
		
		if(isset($_REQUEST['best_chengji_score']) && !empty($_REQUEST['best_chengji_score'])){
		}
		else{
		    $return['success']='请输入最好成绩时间';
	        echo json_encode($return);
	        exit;
		}
		
		//if(isset($_REQUEST['cert_chengji']) && !empty($_REQUEST['cert_chengji'])){
		//}
		//else{
		 //   $return['success']='请输入成绩证明';
	      //  echo json_encode($return);
	        //exit;
		//}
		
		
        
        if(isset($_REQUEST['bus_point']) && !empty($_REQUEST['bus_point'])){
		}
		else{
		    $return['success']='请输入摆渡车点';
	        echo json_encode($return);
	        exit;
		}
		
        
        
        
		if(isset($_REQUEST['cloth_size']) && !empty($_REQUEST['cloth_size'])){
		}
		else{
		    $return['success']='请输入衣服尺码';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		if(isset($_REQUEST['medical_runner']) && !empty($_REQUEST['medical_runner'])){
		}
		else{
		    $return['success']='请输入是否医护跑者';
	        echo json_encode($return);
	        exit;
		}
		
		
        
		//if(isset($_REQUEST['reg_channel']) && !empty($_REQUEST['reg_channel'])){
		//}
		//else{
		 //   $return['success']='请输入报名渠道';
	      //  echo json_encode($return);
	       // exit;
		//}
        
        
        
		
		
		if(isset($_REQUEST['ec_name']) && !empty($_REQUEST['ec_name'])){
		}
		else{
		    $return['success']='请输入紧急联系人姓名';
	        echo json_encode($return);
	        exit;
		}
		
		if(isset($_REQUEST['ec_relation']) && !empty($_REQUEST['ec_relation'])){
		}
		else{
		    $return['success']='请输入与联系人关系';
	        echo json_encode($return);
	        exit;
		}
		
        
		//if(isset($_REQUEST['ec_mobile']) && !empty($_REQUEST['ec_mobile'])){
		//}
		//else{
		 //   $return['success']='请输入紧急联系人手机';
	      //  echo json_encode($return);
	       // exit;
		//}
		
		
     //   $is_mobile=$this->isMobile($_REQUEST['ec_mobile']);
       // if(!$is_mobile) {
        //	$return['success']='请填写正确的紧急联系人手机';
	      //  echo json_encode($return);
	       // exit;
        //}
        
        
        
		if(isset($_REQUEST['ec_phone']) && !empty($_REQUEST['ec_phone'])){
		}
		else{
		    $return['success']='请输入紧急联系人电话';
	        echo json_encode($return);
	        exit;
		}
        
        
		if(isset($_REQUEST['ec_address']) && !empty($_REQUEST['ec_address'])){
		}
		else{
		    $return['success']='请输入紧急联系人电话';
	        echo json_encode($return);
	        exit;
		}
        
        	
		if(isset($_REQUEST['renshou_zengxian']) && !empty($_REQUEST['renshou_zengxian'])){
		}
		else{
		    $return['success']='请输入是否需要中意人寿赠险 ';
	        echo json_encode($return);
	        exit;
		}
        
        
        
        
        //体检证明
	        $photo_normal="";
			if ($_FILES['photo_normal']['name'] != "") {
				
				
				if($_FILES['photo_normal']['size']>PIC_SIZE_LIMIT){
					//$linkurl=__ROOT__."/mytuijian/create";
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("最大上传文件限制为 10MB");history.back();</script>';
					exit;
				}
				
				
				$imgFolder_relative=CERT_MEDICAL_UPLOAD_URI;
				
	            $imgFolder=ROOT_PATH.$imgFolder_relative;
				
	            $filename = $this->checkFileName($imgFolder, $_FILES['photo_normal']['name']);

	            $file11   =   basename($filename);
	            $aa=explode(".",$file11);
	            $aa_num=count($aa)-1;

	            $fname="cert_medical_".time()."_".rand(10,99);

	            $filename=$fname.".".$aa[$aa_num];
	            $this->uploadImg($imgFolder, $_FILES['photo_normal']['tmp_name'], $filename);

	            $photo_normal=__ROOT__.$imgFolder_relative."/". $filename;
	            
	            
	            $dest=ROOT_PATH.$photo_normal;
	            
				$file_type=$this->get_file_type($dest); 
				
				//if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png" && $file_type!="rar" && $file_type!="zip" )
				if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png" && $file_type!="rar" && $file_type!="zip" && $file_type!="pdf"  )
				{
					@unlink($dest);
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("上传照片限定jpg、gif、png、rar、zip类型");history.back();</script>';
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("体检证明限定jpg、gif、png、rar、zip、pdf类型");history.back();</script>';
					exit;
					//$this->jsonData(1,'上传照片限定jpg、gif、png、rar、zip类型');
				    //exit;
				}
				
	        }
	        
	        
	        
	        
        //成绩证明
	        $photo_normal2="";
			if ($_FILES['photo_normal2']['name'] != "") {
				
				
				if($_FILES['photo_normal2']['size']>PIC_SIZE_LIMIT){
					//$linkurl=__ROOT__."/mytuijian/create";
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("最大上传文件限制为 10MB");history.back();</script>';
					exit;
				}
				
				
				$imgFolder_relative=CERT_CHENGJI_UPLOAD_URI;
				
	            $imgFolder=ROOT_PATH.$imgFolder_relative;
				
	            $filename = $this->checkFileName($imgFolder, $_FILES['photo_normal2']['name']);

	            $file11   =   basename($filename);
	            $aa=explode(".",$file11);
	            $aa_num=count($aa)-1;

	            $fname="cert_chengji_".time()."_".rand(10,99);

	            $filename=$fname.".".$aa[$aa_num];
	            $this->uploadImg($imgFolder, $_FILES['photo_normal2']['tmp_name'], $filename);

	            $photo_normal2=__ROOT__.$imgFolder_relative."/". $filename;
	            
	            
	            $dest=ROOT_PATH.$photo_normal2;
	            
				$file_type=$this->get_file_type($dest); 
				
				//if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png" && $file_type!="rar" && $file_type!="zip" )
				if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png" && $file_type!="rar" && $file_type!="zip" && $file_type!="pdf"  )
				{
					@unlink($dest);
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("上传照片限定jpg、gif、png、rar、zip类型");history.back();</script>';
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("成绩证明限定jpg、gif、png、rar、zip、pdf类型");history.back();</script>';
					exit;
					//$this->jsonData(1,'上传照片限定jpg、gif、png、rar、zip类型');
				    //exit;
				}
				
	        }
	        
	        
		
        //var_dump($order_id);exit;
        
        
        
        $tmp_info=array();
        $tmp_info['best_chengji_item']=$_REQUEST['best_chengji_item'];
        $tmp_info['best_chengji_score']=$_REQUEST['best_chengji_score'];
        $tmp_info['bus_point']=$_REQUEST['bus_point'];
        $tmp_info['cloth_size']=$_REQUEST['cloth_size'];
        $tmp_info['medical_runner']=$_REQUEST['medical_runner'];
        $tmp_info['running_group_name']=$_REQUEST['running_group_name'];
        $tmp_info['ec_name']=$_REQUEST['ec_name'];
        $tmp_info['ec_relation']=$_REQUEST['ec_relation'];
        $tmp_info['ec_phone']=$_REQUEST['ec_phone'];
        $tmp_info['ec_address']=$_REQUEST['ec_address'];
        $tmp_info['renshou_zengxian']=$_REQUEST['renshou_zengxian'];
        
        
        
	    if(!empty($photo_normal)){
	    	$tmp_info['cert_medical']=$photo_normal;
	    }
	    
	    if(!empty($photo_normal2)){
	    		$tmp_info['cert_chengji']=$photo_normal2;
	    }
	    
        	$_SESSION['tmp_info']=$tmp_info;
        
        
        	
        /*
		$orderMod = M('order');
	    $sql=sprintf("update %s SET 
	     best_chengji_item='".addslashes($this->remove_xss($_REQUEST['best_chengji_item']))."' 
	    , best_chengji_score='".addslashes($this->remove_xss($_REQUEST['best_chengji_score']))."' 
    	, bus_point='".addslashes($this->remove_xss($_REQUEST['bus_point']))."' 
    	, cloth_size='".addslashes($this->remove_xss($_REQUEST['cloth_size']))."' 
    	, medical_runner='".addslashes($this->remove_xss($_REQUEST['medical_runner']))."' 
    	, reg_channel='".addslashes($this->remove_xss($_REQUEST['reg_channel']))."' 
    	, ec_name='".addslashes($this->remove_xss($_REQUEST['ec_name']))."' 
    	, ec_relation='".addslashes($this->remove_xss($_REQUEST['ec_relation']))."' 
    	, ec_phone='".addslashes($this->remove_xss($_REQUEST['ec_phone']))."' 
    	, ec_address='".addslashes($this->remove_xss($_REQUEST['ec_address']))."' 
    	, addtime_attach='".addslashes($this->remove_xss($addtime))."' 
	    , status_attach='0' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    
	    if(!empty($photo_normal)){
			$orderMod = M('order');
		    $sql=sprintf("update %s SET cert_medical='".addslashes($this->remove_xss($photo_normal))."' 
		      where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
		    
	    }
	    
	    
	    if(!empty($photo_normal2)){
	    $orderMod = M('order');
	    $sql=sprintf("update %s SET  cert_chengji='".addslashes($this->remove_xss($photo_normal2))."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    }
	    
	    */
	    
	    //$this->set_log_sql($sql);
	    
	    
	    
	    
	    
	    $url=U('baoming/attach_confirm', array('order_id'=>$order_id ));
		redirect($url);
		exit;
	    
	    
	    
	    /*
	    
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
	    
	    
	    
	    //如果是审核失败，重新修改详细资料，则可能已经支付过，此时不用再支付。
	    //echo $order_info['isPay'];exit;
	    if($order_info['isPay']==1){
	    	$_SESSION['id_type']=$order_info['id_type'];
        	$_SESSION['id_number']=$order_info['id_number'];
        	
	    	 $url=U('baoming/order_search_finish', array('order_id'=>$order_id ));
		redirect($url);
		exit;
		}
		
		
	    //支付前设置$_COOKIE['order_id']，用于支付时验证是否本人发起的支付请求。
	    setcookie("order_id", $order_id , time()+$this->cookieExpireTime, "/");  
	    $url=U('order/pay', array('order_id'=>$order_id , 'order_no'=>$order_no ));
	redirect($url);
		exit;
			*/
				
        }
	
	
	
	
	
	//报名详细资料  确认页 
	public function attach_confirm(){
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		$order_info=$this->verify_body($order_id);
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		//echo "<pre>";print_r($order_info);exit;
		//echo "<pre>";print_r($_SESSION['tmp_info']);exit;
		$order_info=array_merge( $order_info, $_SESSION['tmp_info']);
		//echo "<pre>";print_r($order_info);exit;
		$this->assign('order_info', $order_info);
		
		
		//if($order_info['confirm_attach']==1){
		//	$return['success']='您详细资料已经确认提交，无法再修改。';
	      //  echo json_encode($return);
	       // exit;
		//}
		
		
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
    	$this->assign('curmenu', '7');
        $this->display('attach_confirm');
	}
	
	
	
	
	
	//报名详细资料  确认页  提交 
	public function attach_confirm_sub(){
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
		
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		$order_info=$this->verify_body($order_id);
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		
		
		
		
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		//判断名额数量是否已满
        	$limit_number=$this->get_limit_number($order_info['cat_id']);  
        	if($limit_number=='N'){
        	  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	        echo json_encode($return);
	        exit;
        	}
		
		
		
		
		
		$_REQUEST['best_chengji_item']=$_SESSION['tmp_info']['best_chengji_item'];
		$_REQUEST['best_chengji_score']=$_SESSION['tmp_info']['best_chengji_score'];
		$_REQUEST['bus_point']=$_SESSION['tmp_info']['bus_point'];
		$_REQUEST['cloth_size']=$_SESSION['tmp_info']['cloth_size'];
		$_REQUEST['medical_runner']=$_SESSION['tmp_info']['medical_runner'];
		$_REQUEST['running_group_name']=$_SESSION['tmp_info']['running_group_name'];
		$_REQUEST['ec_name']=$_SESSION['tmp_info']['ec_name'];
		$_REQUEST['ec_relation']=$_SESSION['tmp_info']['ec_relation'];
		$_REQUEST['ec_phone']=$_SESSION['tmp_info']['ec_phone'];
		$_REQUEST['ec_address']=$_SESSION['tmp_info']['ec_address'];
		$_REQUEST['renshou_zengxian']=$_SESSION['tmp_info']['renshou_zengxian'];
		
		$photo_normal=$_SESSION['tmp_info']['cert_medical'];
		$photo_normal2=$_SESSION['tmp_info']['cert_chengji'];
		
		
		$orderMod = M('order');
	    $sql=sprintf("update %s SET 
	     best_chengji_item='".addslashes($this->remove_xss($_REQUEST['best_chengji_item']))."' 
	    , best_chengji_score='".addslashes($this->remove_xss($_REQUEST['best_chengji_score']))."' 
    	, bus_point='".addslashes($this->remove_xss($_REQUEST['bus_point']))."' 
    	, cloth_size='".addslashes($this->remove_xss($_REQUEST['cloth_size']))."' 
    	, medical_runner='".addslashes($this->remove_xss($_REQUEST['medical_runner']))."' 
    	, running_group_name='".addslashes($this->remove_xss($_REQUEST['running_group_name']))."' 
    	, ec_name='".addslashes($this->remove_xss($_REQUEST['ec_name']))."' 
    	, ec_relation='".addslashes($this->remove_xss($_REQUEST['ec_relation']))."' 
    	, ec_phone='".addslashes($this->remove_xss($_REQUEST['ec_phone']))."' 
    	, ec_address='".addslashes($this->remove_xss($_REQUEST['ec_address']))."' 
    	, renshou_zengxian='".addslashes($this->remove_xss($_REQUEST['renshou_zengxian']))."' 
    	, addtime_attach='".addslashes($this->remove_xss($addtime))."' 
	    , status_attach='0' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    
	    if(!empty($photo_normal)){
			$orderMod = M('order');
		    $sql=sprintf("update %s SET cert_medical='".addslashes($this->remove_xss($photo_normal))."' 
		      where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    $result = $orderMod->execute($sql);
		    
	    }
	    
	    
	    if(!empty($photo_normal2)){
	    $orderMod = M('order');
	    $sql=sprintf("update %s SET  cert_chengji='".addslashes($this->remove_xss($photo_normal2))."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    }
	    
	    
	    
	    
		
		
		$orderMod = M('order');
	    $sql=sprintf("update %s SET confirm_attach='1' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    
	    
	    
	    
	    
	    
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
	    
	    
	    
	    //如果是审核失败，重新修改详细资料，则可能已经支付过，此时不用再支付。
	    //echo $order_info['isPay'];exit;
	    if($order_info['isPay']==1){
	    	$_SESSION['id_type']=$order_info['id_type'];
        	$_SESSION['id_number']=$order_info['id_number'];
        	
	    	 $url=U('baoming/order_search_finish', array('order_id'=>$order_id ));
		redirect($url);
		exit;
		}
		
	    //支付前设置$_COOKIE['order_id']，用于支付时验证是否本人发起的支付请求。
	    setcookie("order_id", $order_id , time()+$this->cookieExpireTime, "/");  
	    
	    $url=U('order/pay', array('order_id'=>$order_id , 'order_no'=>$order_no ));
		redirect($url);
		exit;
		
	}
	
	
	
	
	
	
	
	//根据证件类型、证件号、手机查询  http://cdmalasong.loc/baoming/order_search
	public function order_search(){
		
    	$this->assign('curmenu', '7');
        $this->display('order_search');
		
	}
	
	
	
	
	//根据证件类型、证件号、手机查询  提交  http://cdmalasong.loc/baoming/order_search_sub?id_type=1&id_number=310105195508120023&mobile=13911112222
	public function order_search_sub(){
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
		if(isset($_REQUEST['id_type']) && !empty($_REQUEST['id_type'])){
			$id_type=$_REQUEST['id_type'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		if(isset($_REQUEST['id_number']) && !empty($_REQUEST['id_number'])){
			$id_number=$_REQUEST['id_number'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		if(isset($_REQUEST['mobile']) && !empty($_REQUEST['mobile'])){
			$mobile=$_REQUEST['mobile'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and confirm_apply=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($id_type) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($id_number) .'" ' ;
		$and_cond=$and_cond.' and mobile="' . addslashes($mobile) .'" ' ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        
        if(!empty($order_info)){
        	$_SESSION['id_type']=$order_info['id_type'];
        	$_SESSION['id_number']=$order_info['id_number'];
        	
        	$return['success']='success';
		$return['order_id']=$order_info['id'];
		 echo json_encode($return);
	        exit;
        }
        else{
        	$return['success']='没有查询到结果 No result';
		$return['order_id']=$order_info['id'];
		    echo json_encode($return);
	        exit;
        
        }
	    
        exit;
        
	}
	
	
	
	
	
	
	//根据证件类型、证件号、手机查询  提交  搜索结果页 http://cdmalasong.loc/baoming/order_search_finish?order_id=2385
	public function order_search_finish(){
		
		
    		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		    $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}	
		
		
		
		$order_info=$this->verify_body($order_id);
		//echo "<pre>";print_r($order_info);exit;
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		
		
		
		$this->assign('curmenu', '7');
		
		
		if($order_info['status_attach']==0){
			if($order_info['status_apply']==0){
	             	$this->display('order_search_finish_status_apply_0');    //待抽签
			}
			elseif($order_info['status_apply']==1){
				if($order_info['confirm_attach']==1){
					if($order_info['isPay']==1){
						$this->display('order_search_finish_status_attach_0');   //填完详细资料、已支付、待审核
					}
					else{
						$this->display('order_search_finish_status_attach_0_payment');    //填完详细资料、未支付、待审核
					}
				}
				else{
	             		$this->display('order_search_finish_status_apply_1');   //未填详细资料、未支付、待审核
	            	}
			}
			elseif($order_info['status_apply']==2){
	             	$this->display('order_search_finish_status_apply_2');  //未中签
			}
			else{
			}
		}
		elseif($order_info['status_attach']==1){
			$this->display('order_search_finish_status_attach_1');   //审核通过
		}
		elseif($order_info['status_attach']==2){
			$this->display('order_search_finish_status_attach_2');   //审核拒绝
		}
		else{
		}
		
		
		
		exit;
    	   //$this->assign('curmenu', '7');
        //$this->display('order_search_finish');
	}
	
	

}
?>