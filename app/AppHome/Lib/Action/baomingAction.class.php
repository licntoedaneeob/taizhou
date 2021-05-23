<?php
class baomingAction extends TAction
{

	
	
	//报名尚未开始，敬请等候。  http://cdmalasong.loc/baoming/wait
	public function wait(){
		
    	$this->assign('curmenu', '3');
        $this->display('wait');
    }
    
    
    
	//报名截止。  http://cdmalasong.loc/baoming/stop
	public function stop(){
		
		
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
	        	
	        	$cat_name='半程马拉松';
	        	$order_info['realname']='小明';
	        	$order_info['mobile']='13917759443';
	        	
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
			echo "<pre>";print_r($api_result);exit;
			if(isset($api_result['code']) && $api_result['code']==0){
			}
			else{
			}
			}
			
		*/
		
		
		/*
		//mobile:手机号
		//content:内容
		//type：类型。比赛助手为 bisai, 跑团助手为 paotuan
		//customSignature:自定义短信签名（需要先在平台申请审核）    台州国际马拉松赛
		
		$api_url=$this->api_prefix_base.'/service/sendsms';
		//echo $api_url;exit;
		$api_para=array();
		$api_para['mobile']='13917759443';   
		$api_para['content']='报名成功';   
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
		*/
		
		
        
    	$this->assign('curmenu', '3');
        $this->display('stop');
    }
    
    
    
	//参赛声明  http://cdmalasong.loc/baoming/statement
	public function statement(){
		
        
    	$this->assign('curmenu', '3');
        $this->display('statement');
    }
    
    
    
    
	//报名须知  http://cdmalasong.loc/baoming/rule
	public function rule(){
		
		//报名须知
	        $id=31;
	        $NoticeMod = M('about');
	        $rule_1 =   $NoticeMod->find($id);
		$this->assign('rule_1', $rule_1);
		
		
        
    	$this->assign('curmenu', '3');
        $this->display('rule');
    }
	
	
	
	
	
	//报名选择个人还是团队。  http://cdmalasong.loc/baoming/apply_choose
	public function apply_choose(){
		
        
    	$this->assign('curmenu', '3');
        $this->display('apply_choose');
    }
    
    
    
	
	//团队报名。  http://cdmalasong.loc/baoming/team
	public function team(){
		
        exit;
    	$this->assign('curmenu', '3');
        $this->display('team');
    }
    
    
    
	//团队 报名申请 提交  http://cdmalasong.loc/baoming/team_sub?cat_id=1&realname=aaa&sex=1&birth_day=1990-01-01&id_type=1&id_number=310105195508120023&address=bbb&mobile=13911112222&email=ccc@ccc.com&cityarea=中国&blood=O
	public function team_sub(){
		
		//echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		//exit;
		
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
            
            
            
            
            
		$order_info=false;
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
			
			$order_info=$this->verify_body($order_id,true,false);
			if($order_info==false){
			    $return['success']='验证失败';
		        echo json_encode($return);
		        exit;
			}
			//echo "<pre>";print_r($order_info);exit;
			
			if($order_info['status_attach']==1){
				$return['success']='您详细资料已经审核通过，无法再修改。';
		        echo json_encode($return);
		        exit;
			}
			
		}
		else{
		    $order_id='';
		}
		//var_dump($order_id);exit;
		
		
		
            
            
            $_REQUEST['cat_id']=7;   //团队导入excel类型
            
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
		    $return['success']='请输入领队姓名';
	        echo json_encode($return);
	        exit;
		}
		
		/*
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
		*/
        
        //echo "<pre>";print_r($_REQUEST);exit;
        $is_id_number=$this->checkIdCard($_REQUEST['id_number']);
        if($id_type==1 && !$is_id_number) {
        	$return['success']='请填写正确的身份证号';
	        echo json_encode($return);
	        exit;
        }
        
        /*
		if(isset($_REQUEST['address']) && !empty($_REQUEST['address'])){
		}
		else{
		    $return['success']='请输入通讯地址';
	        echo json_encode($return);
	        exit;
		}
		*/
		
        
		if(isset($_REQUEST['mobile']) && !empty($_REQUEST['mobile'])){
		}
		else{
		    $return['success']='请输入领队手机号码';
	        echo json_encode($return);
	        exit;
		}
		
		
        $is_mobile=$this->isMobile($_REQUEST['mobile']);
        if(!$is_mobile) {
        	$return['success']='请填写正确的领队手机号码';
	        echo json_encode($return);
	        exit;
        }
        
        
        /*
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
        
        
        
		if(isset($_REQUEST['cloth_size']) && !empty($_REQUEST['cloth_size'])){
		}
		else{
		    $return['success']='请输入衣服尺码';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		//if(isset($_REQUEST['medical_runner']) && !empty($_REQUEST['medical_runner'])){
		//}
		//else{
		//    $return['success']='请输入是否医护跑者';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
        
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
		
		//if(isset($_REQUEST['ec_relation']) && !empty($_REQUEST['ec_relation'])){
		//}
		//else{
		//    $return['success']='请输入与联系人关系';
	    //    echo json_encode($return);
	      //  exit;
		//}
		
        
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
        
        
		//if(isset($_REQUEST['ec_address']) && !empty($_REQUEST['ec_address'])){
		//}
		//else{
		//    $return['success']='请输入紧急联系人地址';
	     //   echo json_encode($return);
	      //  exit;
		//}
		
        
		//if(isset($_REQUEST['renshou_zengxian']) && !empty($_REQUEST['renshou_zengxian'])){
		//}
		//else{
		//    $return['success']='请输入是否需要中意人寿赠险 GENERALI CHINA free insurance';
	    //    echo json_encode($return);
	    //    exit;
		//}
		
		*/
		
		
		$invit_data=array();
		$invit_code=empty($_REQUEST['invit_code'])?'':$_REQUEST['invit_code'];
		$is_free=2;
		if(isset($_REQUEST['invit_code']) && !empty($_REQUEST['invit_code'])){
			
	      	//验证邀请码是否正确
				$and_cond='';
				$and_cond=$and_cond.' and cat_id="' . addslashes($_REQUEST['cat_id']) .'" ' ;
				$and_cond=$and_cond.' and invit_code="' . addslashes($_REQUEST['invit_code']) .'" ' ;
				$and_cond=$and_cond.' and exp_time>"' . date('YmdHis') .'" ' ;
				//echo $and_cond;exit;
				$invitMod = M('invit');
		        $invit_data = $invitMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($invit_data);exit;
		        if(empty($invit_data)){
		         $return['success']='邀请码错误 Invitation code is error ';
			        echo json_encode($return);
			        exit;
		        }
		        
		        $invit_data=empty($invit_data)?array():$invit_data[0];
		        //echo "<pre>";print_r($invit_data);exit;
		        
		       if($invit_data['order_id']>0){
		       	   if(!empty($order_info) && $order_info['invit_code']==$invit_data['invit_code']){
		       	   }
		       	   else{
			       	   $return['success']='邀请码已经被使用 Invitation code is used ';
				        echo json_encode($return);
				        exit;
			   	   }
		       }
		       $is_free=$invit_data['is_free'];
		        //echo "<pre>";print_r($invit_data);exit;
		}
		
		
		
        
        	//判断名额数量是否已满
        	$limit_number=$this->get_limit_number($_REQUEST['cat_id']);  
        	if($limit_number=='N'){
        	  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	        echo json_encode($return);
	        exit;
        	}
		
		/*
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
		*/
		
		
		//判断性别与项目是否可以报名
		//if($_REQUEST['sex']==2 && $_REQUEST['cat_id']==1){
		// $return['success']='全程马拉松仅限男子报名 Marathon is just for Male';
	        //echo json_encode($return);
	        //exit;
		//}
		
		
		
		
		
		
		
		//年龄跟哪个时间做比较...提示语：抱歉，请您按照各项目的年龄限定进行报名。
		//1、马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）70岁以下（1947年1月1日以后出生）；
		//2、半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）70岁以下（1947年1月1日以后出生）；
		//3、迷你马拉松项目选手年龄限10岁以上（2007年12 月31 日以前出生）；
		//4、公益马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）70岁以下（1947年1月1日以后出生）；
		//5、公益半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）70岁以下（1947年1月1日以后出生）；
		/*
		20170805改为：
		1、马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）；
		2、半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）；
		3、迷你马拉松项目选手年龄限15岁以上（2002年12 月31 日以前出生）；
		*/
		/*
		20170925 年龄限制 taizhou
		1.马拉松项目年龄限20岁以上（1997年当年出生），70岁以下（1947年后出生）;
		2.半程马拉松项目年龄限16岁以上（2001年当年出生），70岁以下（1947年后出生）；注： 18岁以下未成年人参赛及65周岁以上参赛者，组委会需要求其监护人或法定代理人签署参赛声明。
		3.迷你马拉松项目年龄在15岁以下（2002年以后出生）的参赛者须在监护人陪同下参加；
		*/
		/*
		if($id_type==1){
			$idcard_birth_arr=$this->get_idcard_birth($_REQUEST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$birth_age_permit=$verify_birth;
		}
		else{
			$birth_age_permit=str_replace('-','',$_REQUEST['birth_day']);
		}
		if($_REQUEST['cat_id']==1){
			if($birth_age_permit>=19470101 && $birth_age_permit<=19971231)
			//if($birth_age_permit<=19971231)
			{
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==2){
			if($birth_age_permit>=19470101 && $birth_age_permit<=20011231)
			//if($birth_age_permit<=20011231)
			{
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==3){
			//if($birth_age_permit<=20071231)
			if($birth_age_permit<=20021231)
			{
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
		*/
		
		
		
		//证件号已经提交过报名申请...
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and confirm_apply=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($_REQUEST['id_type']) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($_REQUEST['id_number']) .'" ' ;
		
		 if(!empty($order_info)){
		 	 $and_cond=$and_cond.' and id!="' . addslashes($order_id) .'" ' ;
		 }
		
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $nums_used=empty($order_data)?0:count($order_data);
        //var_dump($nums_used);exit;
		
		//领队不判断是否报过名
    	//if($nums_used > 0 ) {
    		//$return['success']='您的证件号码已提交，请勿重复报名。请前往首页查询报名状态！';
	        //echo json_encode($return);
	        //exit;
		//}
		
		
		
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
        	
        	
		
		if(empty($order_id)){
			$is_insert=1;  //新增
		$orderMod = M('order');
        $orderMod->status=0;
        $order_id = $orderMod->add();
        //var_dump($order_id);exit;
        
		$orderMod = M('order');
	    $sql=sprintf("update %s SET addtime_apply='".addslashes($this->remove_xss($addtime))."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
        }
        else{
        	$is_insert=2;  //编辑
        }
        
        
		$orderMod = M('order');
	    $sql=sprintf("update %s SET address='".addslashes($this->remove_xss($_REQUEST['address']))."' 
    	, mobile='".addslashes($this->remove_xss($_REQUEST['mobile']))."' 
    	, email='".addslashes($this->remove_xss($_REQUEST['email']))."' 
    	, cityarea='".addslashes($this->remove_xss($_REQUEST['cityarea']))."' 
    	, guoji='".addslashes($this->remove_xss($guoji))."' 
    	, blood='".addslashes($this->remove_xss($_REQUEST['blood']))."' 
    	, cloth_size='".addslashes($this->remove_xss($_REQUEST['cloth_size']))."' 
    	, running_group_name='".addslashes($this->remove_xss($_REQUEST['running_group_name']))."' 
    	, ec_name='".addslashes($this->remove_xss($_REQUEST['ec_name']))."' 
    	, ec_phone='".addslashes($this->remove_xss($_REQUEST['ec_phone']))."' 
    	, price_race='".addslashes($price_race)."' 
    	, amount_total='".addslashes($amount_total)."' 
    	, is_free='".addslashes($this->remove_xss($is_free))."' 
    	, invit_code='".addslashes($this->remove_xss($invit_code))."' 
	    , status='1' 
	    , reg_channel='官网' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	   
	   //  , addtime_apply='".addslashes($this->remove_xss($addtime))."' 
	   //, renshou_zengxian='".addslashes($this->remove_xss($_REQUEST['renshou_zengxian']))."' 
	    //, ec_relation='".addslashes($this->remove_xss($_REQUEST['ec_relation']))."' 
	    //, ec_address='".addslashes($this->remove_xss($_REQUEST['ec_address']))."' 
	    //, medical_runner='".addslashes($this->remove_xss($_REQUEST['medical_runner']))."' 
	    //$this->set_log_sql($sql);
	    
	    
	    //20170817 17点之后，不能再修改 姓名、性别、证件、生日、参赛项目
	    if((date('YmdHis')<'20170811170000' && $is_insert==2) || $is_insert==1){
			$orderMod = M('order');
		    $sql=sprintf("update %s SET cat_id='".addslashes($this->remove_xss($_REQUEST['cat_id']))."' 
		    , realname='".addslashes($this->remove_xss($_REQUEST['realname']))."' 
		    , sex='".addslashes($this->remove_xss($_REQUEST['sex']))."' 
	    	, birth_day='".addslashes($this->remove_xss($_REQUEST['birth_day']))."' 
	    	, id_type='".addslashes($this->remove_xss($_REQUEST['id_type']))."' 
	    	, id_number='".addslashes($this->remove_xss($_REQUEST['id_number']))."' 
	    	   where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
	    }
	    
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
	
    
	//团队 报名详细资料 提交  http://cdmalasong.loc/baoming/attach_sub?order_id=2385&cert_medical=ggg&best_chengji_item=hhh&best_chengji_score=iii&cert_chengji=jjj&bus_point=kkk&cloth_size=XL&ec_name=LLL&ec_relation=夫妻&ec_mobile=13699999999&ec_phone=62001111&ec_address=MMM
	public function team_attach_sub(){
		
		
		//echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		//exit;
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
		
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
		
		//不用考虑是否中签，最后确认提交的时候，会全部设为中签
		//if($order_info['status_apply']!=1){
		//	$return['success']='您尚未中签';
	      // echo json_encode($return);
	      // exit;
		//}
		
		
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
		
		
		
		//if(isset($_REQUEST['best_chengji_item']) && !empty($_REQUEST['best_chengji_item'])){
		//}
		//else{
		//    $return['success']='请输入最好成绩项目';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
	
		 	 
		 	 
		//if(isset($_REQUEST['best_chengji_score']) && !empty($_REQUEST['best_chengji_score'])){
		//}
		//else{
		 //   $return['success']='请输入完赛证书成绩';
	      //  echo json_encode($return);
	       // exit;
		//}
		
		//if(isset($_REQUEST['cert_chengji']) && !empty($_REQUEST['cert_chengji'])){
		//}
		//else{
		 //   $return['success']='请输入成绩证明';
	      //  echo json_encode($return);
	        //exit;
		//}
		
		
        
        //if(isset($_REQUEST['bus_point']) && !empty($_REQUEST['bus_point'])){
		//}
		//else{
		//    $return['success']='请输入摆渡车点';
	     //   echo json_encode($return);
	      //  exit;
		//}
		
        
        
        /*
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
        */
        	
		//if(isset($_REQUEST['renshou_zengxian']) && !empty($_REQUEST['renshou_zengxian'])){
		//}
		//else{
		 //   $return['success']='请输入是否需要中意人寿赠险 ';
	     //   echo json_encode($return);
	      //  exit;
		//}
        
        
        
        
        //体检证明    团队：团队报名信息表
	        $photo_normal="";
			if ($_FILES['photo_normal']['name'] != "") {
				
				
				if($_FILES['photo_normal']['size']>PIC_SIZE_LIMIT){
					//$linkurl=__ROOT__."/mytuijian/create";
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("最大上传文件限制为 10MB");history.back();</script>';
					exit;
				}
				
				
				$imgFolder_relative=TEAM_LISTINFO_UPLOAD_URI;
				
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
				//if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png" && $file_type!="rar" && $file_type!="zip" && $file_type!="pdf"  )
				//if ($file_type!="jpg" && $file_type!="png"  )
				if ($file_type!="xls" && $file_type!="xlsx"   )
				{
					@unlink($dest);
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("上传照片限定jpg、gif、png、rar、zip类型");history.back();</script>';
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("体检证明限定jpg、gif、png、rar、zip、pdf类型");history.back();</script>';
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("体检证明限定jpg、png类型");history.back();</script>';
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("团队报名信息表限定xls、xlsx类型");history.back();</script>';
					exit;
					//$this->jsonData(1,'上传照片限定jpg、gif、png、rar、zip类型');
				    //exit;
				}
				
	        }
	        else{
	        	$error_info='请上传团队报名信息表';
			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
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
				//if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png" && $file_type!="rar" && $file_type!="zip" && $file_type!="pdf"  )
				if ($file_type!="jpg" && $file_type!="png"  )
				{
					@unlink($dest);
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("上传照片限定jpg、gif、png、rar、zip类型");history.back();</script>';
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("成绩证明限定jpg、gif、png、rar、zip、pdf类型");history.back();</script>';
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("成绩证明限定jpg、png类型");history.back();</script>';
					exit;
					//$this->jsonData(1,'上传照片限定jpg、gif、png、rar、zip类型');
				    //exit;
				}
				
	        }
	        
	        
		
        //var_dump($order_id);exit;
        
       
       //表单里填写的：
        $team_realname=$_REQUEST['realname'];
        $team_id_number=$_REQUEST['id_number'];
        $team_mobile=$_REQUEST['mobile'];
        $member_key_renshu=0;  //团队成员实际填写行数
        $member_list=array();  //团队成员二维数组
        $id_number_arr=array();  //团队成员证件号码一维数组列表
        $skip_follow=0;  //1不再读取并跳过后续行，0读取后续行
        
        //$error_info='格式错误';
        //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
	  
        
	    if(!empty($photo_normal)){
	    	
	    	//最终文件上传位置
			$uploadfile=$dest;
			//echo $uploadfile;exit;
	    	
			require_once APP_PATH .'Lib/phpexcel/Classes/PHPExcel.php'; 
			require_once APP_PATH .'Lib/phpexcel/Classes/PHPExcel/IOFactory.php';
			
			
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format 
			//echo $uploadfile;exit;
			$objPHPExcel = $objReader->load($uploadfile); 


			$sheet = $objPHPExcel->getSheet(0); 
			
			$highestRow = $sheet->getHighestRow(); // 取得总行数 
			
			$highestColumn = $sheet->getHighestColumn(); // 取得总列数
  			$arr_result=array();
  			$strs=array();
			
			//$qqbookMod = M('qqbook');
			//$table_name=$qqbookMod->getTableName();
			
			
			
			if($highestRow>=2){
				
				//先删除旧数据
				//$sql=" 1 ";
            	//$qqbookMod->where($sql)->delete();
				
				//$j=2 代表从第2行开始获取数据
				for($j=2;$j<=$highestRow;$j++){ 
					
					unset($arr_result);
				    unset($strs);
				 	for($k='A';$k<= $highestColumn;$k++){ 
				     	//读取单元格
				  		//$arr_result  .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue().',';   //如果含公式，则拿到的是公式
				  		$arr_result  .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getCalculatedValue().',';    //如果含公式，则拿到的是公式计算后的最终结果
				    }
					$strs=explode(",",$arr_result);
					
					
					
					//查看excel表内数据情况
					//echo $j;echo "<br>";
					//echo "<pre>";print_r($strs);echo "</pre>";
					
					
					
					if($j==3){
						//逐行调试：
							//echo $j;echo "<br>";
							//echo "<pre>";print_r($strs);echo "</pre>";
							//exit;
					        if(!empty($team_realname) &&  $team_realname==$strs[2]){
					        }
					        else{
					        $error_info='领队姓名 Leaders\' Name 输入有误。页面填写的领队姓名与Excel填写的领队姓名不一致。';
					        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
						  }
					        
					        $team_running_group_name=$strs[11];
					        if(!empty($team_running_group_name)){
					        }
					        else{
					        $error_info='团队名称 Group Name 输入有误';
					        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
					        }
					        
					        
					}
					
					
					
					if($j==4){
						//逐行调试：
							//echo $j;echo "<br>";
							//echo "<pre>";print_r($strs);echo "</pre>";
							//exit;
					        
						  $team_email=$strs[2];
					        if(!empty($team_email)){
					        }
					        else{
					        $error_info='电子邮件 E-mail 输入有误';
					        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
						  }
					        
					        $team_address=$strs[11];
					        if(!empty($team_address)){
					        }
					        else{
					        $error_info='联系地址 Address 输入有误';
					        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
					        }
					}
					
					
					
					if($j==5){
						//逐行调试：
							//echo $j;echo "<br>";
							//echo "<pre>";print_r($strs);echo "</pre>";
							//exit;
						  if(!empty($team_mobile) &&  $team_mobile==$strs[2]){
					        }
					        else{
					        $error_info='手机号 Celphone Number 输入有误。页面填写的领队手机号与Excel填写的领队手机号不一致。';
					        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
						  }
					        
					        $team_renshu=$strs[11];
					        if(!empty($team_renshu)){
					        }
					        else{
					        $error_info='参队人数 No. of Participants 输入有误';
					        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
					        }
					}
					
					if($j>=8 && $j<370){
						//逐行调试：
							//echo $j;echo "<br>";
							//echo "<pre>";print_r($strs);echo "</pre>";
							//exit;
							
							//if($j==8){
								//查看excel表内数据情况
								//echo $j;echo "<br>";
								//echo "<pre>";print_r($strs);echo "</pre>";
								//exit;
							//}
						
						if(isset($strs[0]) && stristr($strs[0],'参赛声明')){
							$skip_follow=1;
						}
						
						if($skip_follow==0){
						if(!empty($strs[1])){
							
							
							$member_xuhao=$member_key_renshu+1;   //序号=键值+1
							
							
							$member_list[$member_key_renshu]['realname']=$strs[1];
							$member_list[$member_key_renshu]['cat_id_show']=$strs[2];
							if($member_list[$member_key_renshu]['cat_id_show']=='马拉松' || $member_list[$member_key_renshu]['cat_id_show']=='全程马拉松'){
								$member_list[$member_key_renshu]['cat_id']='1';
							}
							if($member_list[$member_key_renshu]['cat_id_show']=='半程马拉松'){
								$member_list[$member_key_renshu]['cat_id']='2';
							}
							if($member_list[$member_key_renshu]['cat_id_show']=='迷你马拉松'){
								$member_list[$member_key_renshu]['cat_id']='3';
							}
							$member_list[$member_key_renshu]['sex_show']=$strs[4];
							if($member_list[$member_key_renshu]['sex_show']=='男' || $member_list[$member_key_renshu]['sex_show']=='男Male' || $member_list[$member_key_renshu]['sex_show']=='男M'){
								$member_list[$member_key_renshu]['sex']='1';
							}
							if($member_list[$member_key_renshu]['sex_show']=='女' || $member_list[$member_key_renshu]['sex_show']=='女Female' || $member_list[$member_key_renshu]['sex_show']=='女F'){
								$member_list[$member_key_renshu]['sex']='2';
							}
							$member_list[$member_key_renshu]['cityarea']=$strs[5];
							$member_list[$member_key_renshu]['minzu']=$strs[6];
							$member_list[$member_key_renshu]['id_type_show']=$strs[7];
							if($member_list[$member_key_renshu]['id_type_show']=='身份证'){
								$member_list[$member_key_renshu]['id_type']='1';
							}
							if($member_list[$member_key_renshu]['id_type_show']=='军官证'){
								$member_list[$member_key_renshu]['id_type']='2';
							}
							if($member_list[$member_key_renshu]['id_type_show']=='护照' || $member_list[$member_key_renshu]['id_type_show']=='护照Passport'){
								$member_list[$member_key_renshu]['id_type']='3';
							}
							$member_list[$member_key_renshu]['id_number']=$strs[8];
							$member_list[$member_key_renshu]['birth_day']=$strs[9];
							$member_list[$member_key_renshu]['mobile']=$strs[10];
							
							//$member_list[$member_key_renshu]['renshou_zengxian_show']=$strs[11];
							//if($member_list[$member_key_renshu]['renshou_zengxian_show']=='是'){
							//	$member_list[$member_key_renshu]['renshou_zengxian']='1';
							//}
							//if($member_list[$member_key_renshu]['renshou_zengxian_show']=='否'){
							//	$member_list[$member_key_renshu]['renshou_zengxian']='2';
							//}
							
							//$member_list[$member_key_renshu]['danwei']=$strs[11];
							//$member_list[$member_key_renshu]['danwei_xingzhi']=$strs[12];
							//$member_list[$member_key_renshu]['zhiwei']=$strs[13];
							
							$member_list[$member_key_renshu]['ec_name']=$strs[11];
							$member_list[$member_key_renshu]['ec_phone']=$strs[12];
							$member_list[$member_key_renshu]['blood']=$strs[13];
							//$member_list[$member_key_renshu]['xueli']=$strs[15];
							//$member_list[$member_key_renshu]['yueshouru']=$strs[18];
							//$member_list[$member_key_renshu]['shengao']=$strs[19];
							//$member_list[$member_key_renshu]['tizhong']=$strs[20];
							$member_list[$member_key_renshu]['cloth_size']=$strs[14];
							
							
							$cityarea=$member_list[$member_key_renshu]['cityarea'];
							if ($cityarea=="中国" || $cityarea=="军人" || $cityarea=="中华人民共和国"){
							$guoji="0";
							}
							else if ($cityarea=="中华台北" || $cityarea=="中国香港" || $cityarea=="中国澳门" ){
							$guoji="1";
							}
							else{
							$guoji="2";
							}
							//var_dump($guoji);exit;
							$member_list[$member_key_renshu]['guoji']=$guoji;


							
							//	if($j==12){
								//查看excel表内数据情况
								//echo $j;echo "<br>";
								//echo "<pre>";print_r($member_list[$member_key_renshu]);echo "</pre>";
								//exit;
							//}
						
						
							//成员信息验证开始
							
							if(empty($member_list[$member_key_renshu]['realname'])){
								$error_info='成员'.$member_xuhao.' 全名 Full Name 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['cat_id'])){
								$error_info='成员'.$member_xuhao.' 参加项目 Project 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['sex'])){
								$error_info='成员'.$member_xuhao.' 性别 Gender 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['id_type'])){
								$error_info='成员'.$member_xuhao.' 证件类型 Certificate Type 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['id_number'])){
								$error_info='成员'.$member_xuhao.' 证件号码 ID Number 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['birth_day'])){
								$error_info='成员'.$member_xuhao.' 出生日期 DOB 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['mobile'])){
								$error_info='成员'.$member_xuhao.' 手机号 Cellphone Number 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['ec_name'])){
								$error_info='成员'.$member_xuhao.' 紧急联系人 Emergency Contacter 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['ec_phone'])){
								$error_info='成员'.$member_xuhao.' 紧急联系电话 Emergency Contact Number 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['blood'])){
								$error_info='成员'.$member_xuhao.' 血型 Blood Type 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							if(empty($member_list[$member_key_renshu]['cloth_size'])){
								$error_info='成员'.$member_xuhao.' 服装大小 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							
							
								//if($j==12){
								//查看excel表内数据情况
								//var_dump($member_list[$member_key_renshu]['id_number']);exit;
								//echo $member_list[$member_key_renshu]['id_number'];exit;
								//echo $j;echo "<br>";
								//echo "<pre>";print_r($member_list[$member_key_renshu]);echo "</pre>";
								//exit;
						       //}
						
							
							//验证身份证
						        //echo "<pre>";print_r($_REQUEST);exit;
						        $is_id_number=$this->checkIdCard($member_list[$member_key_renshu]['id_number']);
						        if($member_list[$member_key_renshu]['id_type']==1 && !$is_id_number) {
						        	//if($member_list[$member_key_renshu]['id_number']=='33108219911115855X'){
						        	//}
						        	//else{
						        	$error_info='成员'.$member_xuhao.' 证件号码 ID Number '.$member_list[$member_key_renshu]['id_number'].' 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
					        		//}
						        }
						        
						        
							//验证手机
						        $is_mobile=$this->isMobile($member_list[$member_key_renshu]['mobile']);
						        if(!$is_mobile) {
						        	$error_info='成员'.$member_xuhao.' 手机号 Cellphone Number 输入有误';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
						        }
						        
							
							
							//判断生日和性别
							//证件类型选身份证的，用身份证提取性别和生日；否则采用填写的性别和生日。
							if($member_list[$member_key_renshu]['id_type']==1){
								$idcard_sex=$this->get_xingbie($member_list[$member_key_renshu]['id_number']);   //男or女
								$idcard_birth_arr=$this->get_idcard_birth($member_list[$member_key_renshu]['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
								
								$verify_sex=$idcard_sex;
								$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
								$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
							}
							else{
								$verify_sex=$member_list[$member_key_renshu]['sex'];
								$verify_birth=str_replace('-','',$member_list[$member_key_renshu]['birth_day']);
								$verify_birthday=$member_list[$member_key_renshu]['birth_day'];
							}
							//echo $verify_birthday;exit;
							
							
							
							//身份证与性别生日是否符合
							if($member_list[$member_key_renshu]['id_type']==1){
								$idcard_sex=$this->get_xingbie($member_list[$member_key_renshu]['id_number']);   //男or女
								if($idcard_sex=='男'){
									$idcard_sex=1;
								}
								if($idcard_sex=='女'){
									$idcard_sex=2;
								}
								if($idcard_sex!=$member_list[$member_key_renshu]['sex']){
									$error_info='成员'.$member_xuhao.' 性别与身份证信息不符';
					        			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
								}
								
								$idcard_birth_arr=$this->get_idcard_birth($member_list[$member_key_renshu]['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
								$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
								$verify_birthday=$idcard_birth_arr['yy'].'-'.$idcard_birth_arr['mm'].'-'.$idcard_birth_arr['dd'];
								if($verify_birthday!=$member_list[$member_key_renshu]['birth_day']){
									//$error_info='成员'.$member_xuhao.' 出生年月与身份证信息不符' .$member_list[$member_key_renshu]['id_number'].'  '.$member_list[$member_key_renshu]['birth_day']  ;     //如果提示“出生年月与身份证信息不符”，留意是否导入的excel生日这列单元格格式要改为【文本】，先复制到txt里，单元格格式改为【文本】之后，再粘贴进去。
					        			$error_info='成员'.$member_xuhao.' 出生年月与身份证信息不符'; 
					        			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
								}
							}
							
							
							
							//验证生日
							if (strtotime($member_list[$member_key_renshu]['birth_day']) == false ) {
								$error_info='成员'.$member_xuhao.' 出生年月有误，请输入YYYY-mm-dd格式。';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
													
							
							
							//判断性别与项目是否可以报名
							//if($_REQUEST['sex']==2 && $_REQUEST['cat_id']==1){
							// $return['success']='全程马拉松仅限男子报名 Marathon is just for Male';
						        //echo json_encode($return);
						        //exit;
							//}
							
							
							
							//20170925 年龄限制 taizhou
							//1.马拉松项目年龄限20岁以上（1997年当年出生），70岁以下（1947年后出生）;
							//2.半程马拉松项目年龄限16岁以上（2001年当年出生），70岁以下（1947年后出生）；注： 18岁以下未成年人参赛及65周岁以上参赛者，组委会需要求其监护人或法定代理人签署参赛声明。
							//3.迷你马拉松项目年龄在15岁以下（2002年以后出生）的参赛者须在监护人陪同下参加；
							if($member_list[$member_key_renshu]['id_type']==1){
								$idcard_birth_arr=$this->get_idcard_birth($member_list[$member_key_renshu]['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
								$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
								$birth_age_permit=$verify_birth;
							}
							else{
								$birth_age_permit=str_replace('-','',$member_list[$member_key_renshu]['birth_day']);
							}
							if($member_list[$member_key_renshu]['cat_id']==1){
								if($birth_age_permit>=19470101 && $birth_age_permit<=19971231)
								//if($birth_age_permit<=19971231)
								{
								}
								else{
									$error_info='成员'.$member_xuhao.$member_list[$member_key_renshu]['realname'].' 请您按照各项目的年龄限定进行报名';
					        			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
								}
							}
							elseif($member_list[$member_key_renshu]['cat_id']==2){
								if($birth_age_permit>=19470101 && $birth_age_permit<=20011231)
								//if($birth_age_permit<=20011231)
								{
								}
								else{
									$error_info='成员'.$member_xuhao.$member_list[$member_key_renshu]['realname'].' 请您按照各项目的年龄限定进行报名';
					        			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
								}
							}
							elseif($member_list[$member_key_renshu]['cat_id']==3){
								//if($birth_age_permit<=20071231)
								if($birth_age_permit<=20021231)
								{
								}
								/*
								else{
									if(!empty($_REQUEST['id_type_guard']) && !empty($_REQUEST['id_number_guard'])){
										  
											//判断监护人是否已经报名并且支付...
											$and_cond='';
											$and_cond=$and_cond.' and status=1 ' ;
											$and_cond=$and_cond.' and confirm_apply=1 ' ;
											$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) )  ' ;    //按支付成功限制名额，正在付款的人也占用名额。
											$and_cond=$and_cond.' and id_type="' . addslashes($_REQUEST['id_type_guard']) .'" ' ;
											$and_cond=$and_cond.' and id_number="' . addslashes($_REQUEST['id_number_guard']) .'" ' ;
											$and_cond=$and_cond.' and cat_id=3 ' ;
											//$and_cond=$and_cond.' and id_number_guard="" ' ;   //监护人本人必须是以成人方式报名
											 if(!empty($order_info)){
											 	 $and_cond=$and_cond.' and id!="' . addslashes($order_id) .'" ' ;
											 }
											//echo $and_cond;exit;
											$orderMod = M('order');
									        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
									        //echo "<pre>";print_r($order_data);exit;
									        $nums_used=empty($order_data)?0:count($order_data);
									        //var_dump($nums_used);exit;
											
									    		if($nums_used > 0 ) {
									    		 // $return['success']='mini 15 ok';
										        //echo json_encode($return);
										        //exit;
										        
											}
											else{
											    $error_info='成员'.$member_xuhao.' 监护人证件验证不通过。监护人证件需要已经报名迷你马拉松项目，并且完成支付。';
					        					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
											}
											
									}
									else{
									$error_info='成员'.$member_xuhao.' 请您按照各项目的年龄限定进行报名';
					        			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
								     }
								}
								*/
								else{
									
								      if( ($team_realname=='许挺强' && $member_key_renshu==14) ){
									}
									elseif( ($team_realname=='OBED' && $team_mobile=='18221376343') ){
									}
									elseif( ($team_realname=='钱总推荐名额' && $team_mobile=='13911112222') ){
									}
									elseif( ($team_realname=='小乔推荐3' && $team_mobile=='13911112222') ){
									}
									elseif( ($team_realname=='跑跑网名单' && $team_mobile=='13911112222') ){
									}
									elseif( ($team_realname=='方仁杰' && $team_mobile=='13566419076') ){
									}
									elseif( ($team_realname=='推荐4' && $team_mobile=='13911112222') ){
									}
									elseif( ($team_realname=='王晓剑' && $team_mobile=='13586098787') ){
									}
									elseif( ($team_realname=='李建长' && $team_mobile=='18367666921') ){
									}
									elseif( ($team_realname=='吴笛' && $team_mobile=='15990655031') ){
									}
									elseif( ($team_realname=='爱燃烧平台名单' && $team_mobile=='13911112222') ){
									}
									elseif( ($team_realname=='17001报名表' && $team_mobile=='13911112222') ){
									}
									else{
									$error_info='成员'.$member_xuhao.$member_list[$member_key_renshu]['realname'].' 请您按照各项目的年龄限定进行报名';
					        			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
					        			}
					        			
								}
							}
							elseif($member_list[$member_key_renshu]['cat_id']==4){
								if($birth_age_permit>=19470101 && $birth_age_permit<=19971231){
								}
								else{
									$error_info='成员'.$member_xuhao.' 请您按照各项目的年龄限定进行报名';
					        			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
								}
							}
							elseif($member_list[$member_key_renshu]['cat_id']==5){
								if($birth_age_permit>=19470101 && $birth_age_permit<=20011231){
								}
								else{
									$error_info='成员'.$member_xuhao.' 请您按照各项目的年龄限定进行报名';
					        			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
								}
							}
							else{
								$error_info='成员'.$member_xuhao.' 请输入参赛项目';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							
							
							
							
							//证件号已经提交过报名申请...
							$and_cond='';
							$and_cond=$and_cond.' and status=1 ' ;
							$and_cond=$and_cond.' and confirm_apply=1 ' ;
							$and_cond=$and_cond.' and id_type="' . addslashes($member_list[$member_key_renshu]['id_type']) .'" ' ;
							$and_cond=$and_cond.' and id_number="' . addslashes($member_list[$member_key_renshu]['id_number']) .'" ' ;
							 if(!empty($order_info)){
							 	 $and_cond=$and_cond.' and id!="' . addslashes($order_id) .'" ' ;
							 }
							//echo $and_cond;exit;
							$orderMod = M('order');
					        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
					        //echo "<pre>";print_r($order_data);exit;
					        $nums_used=empty($order_data)?0:count($order_data);
					        //var_dump($nums_used);exit;
							
					    		if($nums_used > 0 ) {
					    		$error_info='成员'.$member_xuhao.' 证件号码 系统内已经存在，无法重复提交。请前往报名查询。';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
					    		}
							
							
							//echo "<pre>";print_r($id_number_arr);echo "</pre>";
							//echo $member_list[$member_key_renshu]['id_number'];echo "<br>";
							if (in_array($member_list[$member_key_renshu]['id_number'] , $id_number_arr )) {
							    	$error_info='成员'.$member_xuhao.' 证件号码，与其他团队成员证件号码重复。';
					        		echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
							}
							else{
								$id_number_arr[]=$member_list[$member_key_renshu]['id_number'];
							}
							
							
							
							
							if( ($team_realname=='陶海勇' && $team_mobile=='18357606699') ){
							}
							elseif( ($team_realname=='OBED' && $team_mobile=='18221376343') ){
							}
							elseif( ($team_realname=='钱总推荐名额' && $team_mobile=='13911112222') ){
							}
							elseif( ($team_realname=='小乔推荐3' && $team_mobile=='13911112222') ){
							}
							elseif( ($team_realname=='跑跑网名单' && $team_mobile=='13911112222') ){
							}
							elseif( ($team_realname=='推荐4' && $team_mobile=='13911112222') ){
							}
							elseif( ($team_realname=='方仁杰' && $team_mobile=='13566419076') ){
							}
							elseif( ($team_realname=='王晓剑' && $team_mobile=='13586098787') ){
							}
							elseif( ($team_realname=='李建长' && $team_mobile=='18367666921') ){
							}
							elseif( ($team_realname=='吴笛' && $team_mobile=='15990655031') ){
							}
							elseif( ($team_realname=='爱燃烧平台名单' && $team_mobile=='13911112222') ){
							}
							elseif( ($team_realname=='17001报名表' && $team_mobile=='13911112222') ){
							}
							else{
				        	//判断名额数量是否已满
				        	$limit_number=$this->get_limit_number($member_list[$member_key_renshu]['cat_id']);  
				        	if($limit_number=='N'){
				        	  $error_info='报名类型名额已满 The quota for this Registration type is full';
					          echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
				        	}
				        	}
				        	
				        	
							
							//成员信息验证结束
							
							
							$member_key_renshu=$member_key_renshu+1;
							
							
							
							
					       }
					}
					}
					
					
		            //$result_isset = $qqbookMod->where(" username='".addslashes($strs[0]) ."' " )->select();
		            //if(isset($result_isset[0])){
		            	//更新
		            	//$sql="update ".$table_name." SET 
			            // title='".addslashes($strs[1])."'
			            //, backup_time='".addslashes($strs[2])."'
			            //where username='".addslashes($strs[0]) ."' 
			            // ";
			            //$result_edit = $qqbookMod->execute($sql);
		            //}
		            //else{
		            	//新增
		            	//$sql="insert into ".$table_name." SET 
		            	//  username='".addslashes($strs[0])."'
			            //, title='".addslashes($strs[1])."'
			            //, backup_time='".addslashes($strs[2])."'
			            //, create_time='".time()."'
			            // ";
			            //$result_add = $qqbookMod->execute($sql);
		            //}
				}
			}
			
			
			
			
			
			if($member_key_renshu>20){
			}
			else{
				if( ($team_realname=='许挺强' && $member_key_renshu==14) ){
				}
				elseif( ($team_realname=='OBED' && $team_mobile=='18221376343') ){
				}
				elseif( ($team_realname=='钱总推荐名额' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='小乔推荐3' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='跑跑网名单' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='方仁杰' && $team_mobile=='13566419076') ){
				}
				elseif( ($team_realname=='推荐4' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='王晓剑' && $team_mobile=='13586098787') ){
				}
				elseif( ($team_realname=='李建长' && $team_mobile=='18367666921') ){
				}
				elseif( ($team_realname=='吴笛' && $team_mobile=='15990655031') ){
				}
				elseif( ($team_realname=='爱燃烧平台名单' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='17001报名表' && $team_mobile=='13911112222') ){
				}
				else{
					$error_info='团队报名限20人以上';
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
				}
			}
			
			
			
			if($member_key_renshu==$team_renshu && $member_key_renshu>0){
			}
			else{
			$error_info='参队人数 No. of Participants 与 实际填报人数 不符';
			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("'.$error_info.'");history.back();</script>';exit;
			}
			
			//echo "<pre>";print_r($member_list);echo "</pre>";
			//echo $member_key_renshu;echo "<br>";
			//echo "view over";exit;
			
			
			
			
			
			
	    }
        
        
        //excel分析结束
        //echo "excel finish";
        //exit;
        
        
        if(!empty($member_list)){
        	$fanslistMod = M('order');
		$andsql=" and team_id='".addslashes($order_id)."' ";
    		//$fans_list = $fanslistMod->where(" 1 ".$andsql." ")->select();
    		$sql=" team_id='".addslashes($order_id)."' ";
             $fanslistMod->where($sql)->delete();
            
            
            
        	foreach($member_list as $k=>$v){
        		 
        		 
        		 	$price_race=$this->get_price_race($v['cat_id'],$v['guoji']);
		        	$amount_total=$price_race;
		        	//var_dump($price_race);exit;
        		 
        		 $team_detailMod = M('order');
		        $team_detailMod->status=0;
		        $team_detail_id = $team_detailMod->add();
		        //var_dump($order_id);exit;
		        
			    $sql=sprintf("update %s SET realname='".addslashes($this->remove_xss($v['realname']))."' 
		    		, cat_id_show='".addslashes($this->remove_xss($v['cat_id_show']))."' 
		    		, cat_id='".addslashes($this->remove_xss($v['cat_id']))."' 
		    		, sex_show='".addslashes($this->remove_xss($v['sex_show']))."' 
		    		, sex='".addslashes($this->remove_xss($v['sex']))."' 
		    		, cityarea='".addslashes($this->remove_xss($v['cityarea']))."' 
		    		, minzu='".addslashes($this->remove_xss($v['minzu']))."' 
		    		, id_type_show='".addslashes($this->remove_xss($v['id_type_show']))."' 
		    		, id_type='".addslashes($this->remove_xss($v['id_type']))."' 
		    		, id_number='".addslashes($this->remove_xss($v['id_number']))."' 
		    		, birth_day='".addslashes($this->remove_xss($v['birth_day']))."' 
		    		, mobile='".addslashes($this->remove_xss($v['mobile']))."' 
		    		, danwei='".addslashes($this->remove_xss($v['danwei']))."' 
		    		, danwei_xingzhi='".addslashes($this->remove_xss($v['danwei_xingzhi']))."' 
		    		, zhiwei='".addslashes($this->remove_xss($v['zhiwei']))."' 
		    		, ec_name='".addslashes($this->remove_xss($v['ec_name']))."' 
		    		, ec_phone='".addslashes($this->remove_xss($v['ec_phone']))."' 
		    		, blood='".addslashes($this->remove_xss($v['blood']))."' 
		    		, xueli='".addslashes($this->remove_xss($v['xueli']))."' 
		    		, yueshouru='".addslashes($this->remove_xss($v['yueshouru']))."' 
		    		, shengao='".addslashes($this->remove_xss($v['shengao']))."' 
		    		, tizhong='".addslashes($this->remove_xss($v['tizhong']))."' 
		    		, cloth_size='".addslashes($this->remove_xss($v['cloth_size']))."' 
		    		, renshou_zengxian='".addslashes($this->remove_xss($v['renshou_zengxian']))."' 
		    		, team_id='".addslashes($this->remove_xss($order_id))."' 
			    	, status='1' 
			    	, guoji='".addslashes($this->remove_xss($v['guoji']))."' 
			    	, addtime_apply='".addslashes($this->remove_xss($addtime))."' 
			    	, confirm_apply='1' 
			    	, status_apply='1' 
			    	, confirm_attach='1' 
			    	, price_race='".addslashes($this->remove_xss($price_race))."'  
			    	, amount_total='".addslashes($this->remove_xss($amount_total))."'  
			    	, reg_channel='官网' 
			    	, team_realname='".addslashes($this->remove_xss($team_realname))."' 
		    		, team_id_number='".addslashes($this->remove_xss($team_id_number))."' 
		    		, team_mobile='".addslashes($this->remove_xss($team_mobile))."' 
		    		, team_running_group_name='".addslashes($this->remove_xss($team_running_group_name))."' 
		    		, team_email='".addslashes($this->remove_xss($team_email))."' 
		    		, team_address='".addslashes($this->remove_xss($team_address))."' 
		    		, team_renshu='".addslashes($this->remove_xss($team_renshu))."' 
			    where id='".addslashes($team_detail_id)."' 
			    ", $team_detailMod->getTableName() );
			    //echo $sql;exit;
			    $result = $team_detailMod->execute($sql);
			    
			    
			    
					    
			      if( ($team_realname=='许挺强' && $member_key_renshu==14) ){
				}
				elseif( ($team_realname=='OBED' && $team_mobile=='18221376343') ){
				}
				elseif( ($team_realname=='钱总推荐名额' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='小乔推荐3' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='跑跑网名单' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='方仁杰' && $team_mobile=='13566419076') ){
				}
				elseif( ($team_realname=='推荐4' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='王晓剑' && $team_mobile=='13586098787') ){
				}
				elseif( ($team_realname=='李建长' && $team_mobile=='18367666921') ){
				}
				elseif( ($team_realname=='吴笛' && $team_mobile=='15990655031') ){
				}
				elseif( ($team_realname=='爱燃烧平台名单' && $team_mobile=='13911112222') ){
				}
				elseif( ($team_realname=='17001报名表' && $team_mobile=='13911112222') ){
				}
				else{
					
					    
					if($v['cat_id']==1){
			        	$cat_name='马拉松';
			        	}
			        	if($v['cat_id']==2){
			        	$cat_name='半程马拉松';
			        	}
			        	if($v['cat_id']==3){
			        	$cat_name='迷你马拉松';
			        	}
			        	//短信通知
			        	if($this->open_sms_msg==1){
				        	$msg_body='尊敬的'.$v['realname'].'，您的团队参赛数据已经录入系统，请前往报名查询通道上传证明并进行支付。';
						$api_url=$this->api_prefix_base.'/service/sendsms';
						//echo $api_url;exit;
						$api_para=array();
						$api_para['mobile']=$v['mobile'];   
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
					
				}
					
					
			    
			    
			    
			    
        	}
        }
        
        
        
        
        $tmp_info=array();
        //$tmp_info['best_chengji_item']=$_REQUEST['best_chengji_item'];
        $tmp_info['best_chengji_score']=$_REQUEST['best_chengji_score'];
        $tmp_info['bus_point']=$_REQUEST['bus_point'];
        /*
        $tmp_info['cloth_size']=$_REQUEST['cloth_size'];
        $tmp_info['medical_runner']=$_REQUEST['medical_runner'];
        $tmp_info['running_group_name']=$_REQUEST['running_group_name'];
        $tmp_info['ec_name']=$_REQUEST['ec_name'];
        $tmp_info['ec_relation']=$_REQUEST['ec_relation'];
        $tmp_info['ec_phone']=$_REQUEST['ec_phone'];
        $tmp_info['ec_address']=$_REQUEST['ec_address'];
        */
        //$tmp_info['renshou_zengxian']=$_REQUEST['renshou_zengxian'];
        
        
        
	    if(!empty($photo_normal)){
	    	$tmp_info['cert_medical']=$photo_normal;
	    }
	    
	    if(!empty($photo_normal2)){
	    		$tmp_info['cert_chengji']=$photo_normal2;
	    }
	    
	    
	    //echo "<pre>";print_r($tmp_info);exit;
	    
	    
	    /*
		$orderMod = M('order');
	    $sql=sprintf("update %s SET 
	    best_chengji_score='".addslashes($this->remove_xss($_REQUEST['best_chengji_score']))."' 
    	, addtime_attach='".addslashes($this->remove_xss($addtime))."' 
	    , status_attach='0' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    */
	    
	    
	    
	   
	
        //更新团队基本信息
	    	$orderMod = M('order');
	    $sql=sprintf("update %s SET 
	        team_realname='".addslashes($this->remove_xss($team_realname))."' 
    		, team_id_number='".addslashes($this->remove_xss($team_id_number))."' 
    		, team_mobile='".addslashes($this->remove_xss($team_mobile))."' 
    		, team_running_group_name='".addslashes($this->remove_xss($team_running_group_name))."' 
    		, team_email='".addslashes($this->remove_xss($team_email))."' 
    		, team_address='".addslashes($this->remove_xss($team_address))."' 
    		, team_renshu='".addslashes($this->remove_xss($team_renshu))."' 
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
	    
	    
		
		//$orderMod = M('order');
	    //$sql=sprintf("update %s SET confirm_attach='1' 
	    //where id='".addslashes($order_id)."' 
	    //", $orderMod->getTableName() );
	    //echo $sql;exit;
	    //$result = $orderMod->execute($sql);
	    
	    
	    
	    
        	//$_SESSION['tmp_info']=$tmp_info;
        
        
        
        	
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
	    
	    
	    
	    
	    
	    
	    //跳转到支付页的做法（团队报名）
	    
		//$orderMod = M('order');
	    //$sql=sprintf("update %s SET confirm_apply='1' 
	    //where id='".addslashes($order_id)."' 
	    //", $orderMod->getTableName() );
	    //echo $sql;exit;
	    //$result = $orderMod->execute($sql);
	    	
        	//	$status_apply=1;
        	//$orderMod = M('order');
		   // $sql=sprintf("update %s SET status_apply='1' 
		   // where id='".addslashes($order_id)."' 
		   // ", $orderMod->getTableName() );
		    //echo $sql;exit;
		   // $result = $orderMod->execute($sql);
		    
        
        	//	$status_apply=1;
        	//$orderMod = M('order');
		   // $sql=sprintf("update %s SET status_apply='1' 
		   // where id='".addslashes($order_id)."' 
		   // ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    //$result = $orderMod->execute($sql);
		    
		    //发短信给团队成员，提醒他们支付。跳转到导入成功页
		    $url=U('baoming/team_finish', array('order_id'=>$order_id ));
		      //location.href="/baoming/order_search_finish/order_id/"+ret.order_id; 
		      //echo "ok";exit;
		      redirect($url);
		exit;
		      
		    
		    /*
		    //判断是否支付、如果需要支付，跳支付页，如果已经支付，跳成功页
		    if( $order_info['isPay'] >0){
		    	$url=U('baoming/order_search_finish', array('order_id'=>$order_id ));
		      redirect($url);
		exit;
		    }
		    else{
	    		$url=U('order/pay', array('order_id'=>$order_id ));
	    		redirect($url);
		exit;
	    	     }
		*/
		
		
	    exit;
	    
	    
	    
	    
	    //跳转到确认页的做法（个人报名）
	    //$url=U('baoming/apply_confirm', array('order_id'=>$order_id ));
	//	redirect($url);
	//	exit;
		
		
		
		
		
	    
	    //$url=U('baoming/attach_confirm', array('order_id'=>$order_id ));
		//redirect($url);
		//exit;
	    
	    
	    
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
	
	
    
	//团队 导入excel完成页  http://cdmalasong.loc/baoming/team_finish?order_id=2385
	public function team_finish(){
		
		
		
		//echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		//exit;
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
		
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
		
		
    	$this->assign('curmenu', '3');
        $this->display('team_finish');
		
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
	
	
	
	//报名申请  
	//邀请码  http://cdmalasong.loc/baoming/apply/code/abcd5678/order_id/2449
	//公益跑  http://cdmalasong.loc/baoming/apply/ingress/welfare
	public function apply(){
		
		//echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		//exit;
		
		$invit_cat_id=0;
		$invit_code='';
		if(isset($_REQUEST['code']) && !empty($_REQUEST['code'])){
			
			//if(date('YmdHis')>'20170831235959'){
			//	echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
			//	        exit;
			//}
			
			if(date('YmdHis')>'20170911180000'){
				echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
				        exit;
			}
			
			
			//有code，验证。如正确，根据code对应的项目，锁死只能报这个。如code对应的项目不对，出空白页，简单出一段提示文字，如"参数错误"。
			//code错误不存在：简单出一段提示文字，如"参数错误"。
			
			$invit_data=array();
			$invit_code=$_REQUEST['code'];
			if(isset($invit_code) && !empty($invit_code)){
				
		      	//验证邀请码是否正确
					$and_cond='';
					$and_cond=$and_cond.' and invit_code="' . addslashes($invit_code) .'" ' ;
					$and_cond=$and_cond.' and exp_time>"' . date('YmdHis') .'" ' ;
					//echo $and_cond;exit;
					$invitMod = M('invit');
			        $invit_data = $invitMod->where(" 1 ".$and_cond )->select();
			        //echo "<pre>";print_r($invit_data);exit;
			        if(empty($invit_data)){
			         echo "参数错误";
				        exit;
			        }
			        
			        
			       $invit_data=empty($invit_data)?array():$invit_data[0];
			       //echo "<pre>";print_r($invit_data);exit;
			       
			       if($invit_data['order_id']>0){
			       	   echo "您的邀请码已经被使用";
				        exit;
			       }
			       
			       
			       $is_free=$invit_data['is_free'];
			        //echo "<pre>";print_r($invit_data);exit;
			        $invit_cat_id=$invit_data['cat_id'];
			}
			
		}
		elseif(isset($_REQUEST['ingress']) && $_REQUEST['ingress']=='welfare'){
			if(date('YmdHis')>'20170816100000' && date('YmdHis')<'20170905120000' ){
				
		      }
		      else{
		      	 echo "报名已结束，感谢您的关注！";
				exit;
		      }
		}
		else{
		    //无code，正常进入
		}
		
		//var_dump($code);exit;
		//echo $invit_cat_id;exit;
		$this->assign('invit_cat_id', $invit_cat_id);
		$this->assign('invit_code', $invit_code);
		
		
		
		$order_info=false;
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
			
			
			$order_info=$this->verify_body($order_id);
			if($order_info==false){
			    $return['success']='验证失败';
		        echo json_encode($return);
		        exit;
			}
			//echo "<pre>";print_r($order_info);exit;
			
			
			
			if($order_info['status_attach']==1){
				$return['success']='您详细资料已经审核通过，无法再修改。';
		        echo json_encode($return);
		        exit;
			}
			
			
		}
		else{
		    $order_id='';
		}
		//var_dump($order_id);exit;
		
		
		
		
		//if($order_info['confirm_apply']==1){
		//	$return['success']='您基本资料已经确认提交，无法再修改。';
	     //   echo json_encode($return);
	      //  exit;
		//}
		
		$this->assign('order_id', $order_id);
		$this->assign('order_info', $order_info);
		
		
		//用户报名截止，但是修改报名资料仍然可以
		if(empty($order_info)){
			if(isset($_REQUEST['code']) && !empty($_REQUEST['code'])){
				//邀请码用户，仍然可以报名
			}
			elseif(isset($_REQUEST['ingress']) && $_REQUEST['ingress']=='welfare'){
				if(date('YmdHis')>'20170816100000' && date('YmdHis')<'20170905120000' ){
					
			      }
			      else{
			      	 echo "报名已结束，感谢您的关注！";
					 exit;
			      }
			}
			else{
				//非邀请码用户，不能截止报名
				if(date('YmdHis')>'20171028170000'){
					echo "报名已结束，感谢您的关注！";
					exit;
			    }
			}
		}
		
		
		
		$orderMod = M('guoji');
        $guoji_list = $orderMod->where(" 1 " )->order('sort asc,guoji_name_abbr asc')->limit('0,1000')->select();
        $this->assign('guoji_list', $guoji_list);
        //echo "<pre>";print_r($guoji_list);exit;
        
        
    	$this->assign('curmenu', '3');
        $this->display('apply');
    }
	
	
	
	
	//报名申请 提交  http://cdmalasong.loc/baoming/apply_sub?cat_id=1&realname=aaa&sex=1&birth_day=1990-01-01&id_type=1&id_number=310105195508120023&address=bbb&mobile=13911112222&email=ccc@ccc.com&cityarea=中国&blood=O
	public function apply_sub(){
		
		//echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		//exit;
		
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
            
            
            
            
            
		$order_info=false;
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
			
			$order_info=$this->verify_body($order_id,true,false);
			if($order_info==false){
			    $return['success']='验证失败';
		        echo json_encode($return);
		        exit;
			}
			//echo "<pre>";print_r($order_info);exit;
			
			if($order_info['status_attach']==1){
				$return['success']='您详细资料已经审核通过，无法再修改。';
		        echo json_encode($return);
		        exit;
			}
			
		}
		else{
		    $order_id='';
		}
		//var_dump($order_id);exit;
		
		
		
            
            
            
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
		    $return['success']='请输入通讯地址';
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
        
        
        
		if(isset($_REQUEST['cloth_size']) && !empty($_REQUEST['cloth_size'])){
		}
		else{
		    $return['success']='请输入衣服尺码';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		//if(isset($_REQUEST['medical_runner']) && !empty($_REQUEST['medical_runner'])){
		//}
		//else{
		//    $return['success']='请输入是否医护跑者';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
        
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
		
		//if(isset($_REQUEST['ec_relation']) && !empty($_REQUEST['ec_relation'])){
		//}
		//else{
		//    $return['success']='请输入与联系人关系';
	    //    echo json_encode($return);
	      //  exit;
		//}
		
        
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
        
        
		//if(isset($_REQUEST['ec_address']) && !empty($_REQUEST['ec_address'])){
		//}
		//else{
		//    $return['success']='请输入紧急联系人地址';
	     //   echo json_encode($return);
	      //  exit;
		//}
		
        
		//if(isset($_REQUEST['renshou_zengxian']) && !empty($_REQUEST['renshou_zengxian'])){
		//}
		//else{
		//    $return['success']='请输入是否需要中意人寿赠险 GENERALI CHINA free insurance';
	    //    echo json_encode($return);
	    //    exit;
		//}
		
		
		
		$invit_data=array();
		$invit_code=empty($_REQUEST['invit_code'])?'':$_REQUEST['invit_code'];
		$is_free=2;
		if(isset($_REQUEST['invit_code']) && !empty($_REQUEST['invit_code'])){
			
	      	//验证邀请码是否正确
				$and_cond='';
				$and_cond=$and_cond.' and cat_id="' . addslashes($_REQUEST['cat_id']) .'" ' ;
				$and_cond=$and_cond.' and invit_code="' . addslashes($_REQUEST['invit_code']) .'" ' ;
				$and_cond=$and_cond.' and exp_time>"' . date('YmdHis') .'" ' ;
				//echo $and_cond;exit;
				$invitMod = M('invit');
		        $invit_data = $invitMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($invit_data);exit;
		        if(empty($invit_data)){
		         $return['success']='邀请码错误 Invitation code is error ';
			        echo json_encode($return);
			        exit;
		        }
		        
		        $invit_data=empty($invit_data)?array():$invit_data[0];
		        //echo "<pre>";print_r($invit_data);exit;
		        
		       if($invit_data['order_id']>0){
		       	   if(!empty($order_info) && $order_info['invit_code']==$invit_data['invit_code']){
		       	   }
		       	   else{
			       	   $return['success']='邀请码已经被使用 Invitation code is used ';
				        echo json_encode($return);
				        exit;
			   	   }
		       }
		       $is_free=$invit_data['is_free'];
		        //echo "<pre>";print_r($invit_data);exit;
		}
		
		
		
        
        	//判断名额数量是否已满（支付占用名额）
        	if(!empty($order_info) && $order_info['isPay']>=0){
        	}
        	else{
        	$limit_number=$this->get_limit_number($_REQUEST['cat_id']);  
        	if($limit_number=='N'){
        	  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	        echo json_encode($return);
	        exit;
        	}
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
		
		
		
		//判断性别与项目是否可以报名
		//if($_REQUEST['sex']==2 && $_REQUEST['cat_id']==1){
		// $return['success']='全程马拉松仅限男子报名 Marathon is just for Male';
	        //echo json_encode($return);
	        //exit;
		//}
		
		
		//年龄跟哪个时间做比较...提示语：抱歉，请您按照各项目的年龄限定进行报名。
		//1、马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）70岁以下（1947年1月1日以后出生）；
		//2、半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）70岁以下（1947年1月1日以后出生）；
		//3、迷你马拉松项目选手年龄限10岁以上（2007年12 月31 日以前出生）；
		//4、公益马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）70岁以下（1947年1月1日以后出生）；
		//5、公益半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）70岁以下（1947年1月1日以后出生）；
		/*
		20170805改为：
		1、马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）；
		2、半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）；
		3、迷你马拉松项目选手年龄限15岁以上（2002年12 月31 日以前出生）；
		*/
		/*
		20170925 年龄限制 taizhou
		1.马拉松项目年龄限20岁以上（1997年当年出生），70岁以下（1947年后出生）;
		2.半程马拉松项目年龄限16岁以上（2001年当年出生），70岁以下（1947年后出生）；注： 18岁以下未成年人参赛及65周岁以上参赛者，组委会需要求其监护人或法定代理人签署参赛声明。
		3.迷你马拉松项目年龄在15岁以下（2002年以后出生）的参赛者须在监护人陪同下参加；
		*/
		if($id_type==1){
			$idcard_birth_arr=$this->get_idcard_birth($_REQUEST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$birth_age_permit=$verify_birth;
		}
		else{
			$birth_age_permit=str_replace('-','',$_REQUEST['birth_day']);
		}
		if($_REQUEST['cat_id']==1){
			if($birth_age_permit>=19470101 && $birth_age_permit<=19971231)
			//if($birth_age_permit<=19971231)
			{
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==2){
			if($birth_age_permit>=19470101 && $birth_age_permit<=20011231)
			//if($birth_age_permit<=20011231)
			{
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==3){
			//if($birth_age_permit<=20071231)
			if($birth_age_permit<=20021231)
			{
			}
			else{
				
				if(!empty($_REQUEST['id_type_guard']) && !empty($_REQUEST['id_number_guard'])){
					  
						//判断监护人是否已经报名并且支付...
						$and_cond='';
						$and_cond=$and_cond.' and status=1 ' ;
						$and_cond=$and_cond.' and confirm_apply=1 ' ;
						$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) )  ' ;    //按支付成功限制名额，正在付款的人也占用名额。
						$and_cond=$and_cond.' and id_type="' . addslashes($_REQUEST['id_type_guard']) .'" ' ;
						$and_cond=$and_cond.' and id_number="' . addslashes($_REQUEST['id_number_guard']) .'" ' ;
						$and_cond=$and_cond.' and cat_id=3 ' ;
						//$and_cond=$and_cond.' and id_number_guard="" ' ;   //监护人本人必须是以成人方式报名
						 if(!empty($order_info)){
						 	 $and_cond=$and_cond.' and id!="' . addslashes($order_id) .'" ' ;
						 }
						//echo $and_cond;exit;
						$orderMod = M('order');
				        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
				        //echo "<pre>";print_r($order_data);exit;
				        $nums_used=empty($order_data)?0:count($order_data);
				        //var_dump($nums_used);exit;
						
				    		if($nums_used > 0 ) {
				    		 // $return['success']='mini 15 ok';
					        //echo json_encode($return);
					        //exit;
					        
						}
						else{
							$return['success']='监护人证件验证不通过。监护人证件需要已经报名迷你马拉松项目，并且完成支付。';
					        echo json_encode($return);
					        exit;
						}
						
				}
				else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。迷你马拉松项目年龄在15岁以下（2002年以后出生）的参赛者须在监护人陪同下参加。请填写监护人证件。';
			        echo json_encode($return);
			        exit;
			     }
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
		
		 if(!empty($order_info)){
		 	 $and_cond=$and_cond.' and id!="' . addslashes($order_id) .'" ' ;
		 }
		
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
        	
        	
		
		if(empty($order_id)){
			$is_insert=1;  //新增
		$orderMod = M('order');
        $orderMod->status=0;
        $order_id = $orderMod->add();
        //var_dump($order_id);exit;
        
		$orderMod = M('order');
	    $sql=sprintf("update %s SET addtime_apply='".addslashes($this->remove_xss($addtime))."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
        }
        else{
        	$is_insert=2;  //编辑
        }
        
        
		$orderMod = M('order');
	    $sql=sprintf("update %s SET address='".addslashes($this->remove_xss($_REQUEST['address']))."' 
    	, mobile='".addslashes($this->remove_xss($_REQUEST['mobile']))."' 
    	, email='".addslashes($this->remove_xss($_REQUEST['email']))."' 
    	, cityarea='".addslashes($this->remove_xss($_REQUEST['cityarea']))."' 
    	, guoji='".addslashes($this->remove_xss($guoji))."' 
    	, blood='".addslashes($this->remove_xss($_REQUEST['blood']))."' 
    	, cloth_size='".addslashes($this->remove_xss($_REQUEST['cloth_size']))."' 
    	, running_group_name='".addslashes($this->remove_xss($_REQUEST['running_group_name']))."' 
    	, ec_name='".addslashes($this->remove_xss($_REQUEST['ec_name']))."' 
    	, ec_phone='".addslashes($this->remove_xss($_REQUEST['ec_phone']))."' 
    	, price_race='".addslashes($price_race)."' 
    	, amount_total='".addslashes($amount_total)."' 
    	, is_free='".addslashes($this->remove_xss($is_free))."' 
    	, invit_code='".addslashes($this->remove_xss($invit_code))."' 
    	, renshou_zengxian='".addslashes($this->remove_xss($_REQUEST['renshou_zengxian']))."' 
    	, id_type_guard='".addslashes($this->remove_xss($_REQUEST['id_type_guard']))."' 
    	, id_number_guard='".addslashes($this->remove_xss($_REQUEST['id_number_guard']))."' 
	    , status='1' 
	    , reg_channel='官网' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	   
	   //  , addtime_apply='".addslashes($this->remove_xss($addtime))."' 
	   //, renshou_zengxian='".addslashes($this->remove_xss($_REQUEST['renshou_zengxian']))."' 
	    //, ec_relation='".addslashes($this->remove_xss($_REQUEST['ec_relation']))."' 
	    //, ec_address='".addslashes($this->remove_xss($_REQUEST['ec_address']))."' 
	    //, medical_runner='".addslashes($this->remove_xss($_REQUEST['medical_runner']))."' 
	    //$this->set_log_sql($sql);
	    
	    
	    //20170817 17点之后，不能再修改 姓名、性别、证件、生日、参赛项目
	    if((date('YmdHis')<'20170811170000' && $is_insert==2) || $is_insert==1){
			$orderMod = M('order');
		    $sql=sprintf("update %s SET cat_id='".addslashes($this->remove_xss($_REQUEST['cat_id']))."' 
		    , realname='".addslashes($this->remove_xss($_REQUEST['realname']))."' 
		    , sex='".addslashes($this->remove_xss($_REQUEST['sex']))."' 
	    	, birth_day='".addslashes($this->remove_xss($_REQUEST['birth_day']))."' 
	    	, id_type='".addslashes($this->remove_xss($_REQUEST['id_type']))."' 
	    	, id_number='".addslashes($this->remove_xss($_REQUEST['id_number']))."' 
	    	, renshou_zengxian='".addslashes($this->remove_xss($_REQUEST['renshou_zengxian']))."' 
	    	   where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
	    }
	    
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
		
		//echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		//exit;
		
		
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
		
		
		
		//if($order_info['confirm_apply']==1){
		//	$return['success']='您基本资料已经确认提交，无法再修改。';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		

    	$this->assign('curmenu', '3');
        $this->display('apply_confirm');
    }
    
    
    
	//报名申请  确认页 提交  http://cdmalasong.loc/baoming/apply_confirm_sub?order_id=2407
	public function apply_confirm_sub(){
		
		//echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		//exit;
		
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
		
		
		
		//if($order_info['confirm_apply']==1){
		//	$return['success']='您基本资料已经确认提交，无法再修改。';
	      //  echo json_encode($return);
	       // exit;
		//}
		
		
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		
		
		$invit_data=array();
		$invit_code=$order_info['invit_code'];
		$is_free=$order_info['is_free'];
		if(isset($order_info['invit_code']) && !empty($order_info['invit_code'])){
			
	      	//验证邀请码是否正确
				$and_cond='';
				$and_cond=$and_cond.' and cat_id="' . addslashes($order_info['cat_id']) .'" ' ;
				$and_cond=$and_cond.' and invit_code="' . addslashes($order_info['invit_code']) .'" ' ;
				$and_cond=$and_cond.' and exp_time>"' . date('YmdHis') .'" ' ;
				//echo $and_cond;exit;
				$invitMod = M('invit');
		        $invit_data = $invitMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($invit_data);exit;
		        if(empty($invit_data)){
		         $return['success']='邀请码错误 Invitation code is error ';
			        echo json_encode($return);
			        exit;
		        }
		        
		        $invit_data=empty($invit_data)?array():$invit_data[0];
		        //echo "<pre>";print_r($invit_data);exit;
		        
		       if($invit_data['order_id']>0){
		       	    if(!empty($order_info) && $order_info['invit_code']==$invit_data['invit_code']){
		       	   }
		       	   else{
		       	  $return['success']='邀请码已经被使用 Invitation code is used ';
			        echo json_encode($return);
			        exit;
			         }
		       }
		       $is_free=$invit_data['is_free'];
		        //echo "<pre>";print_r($invit_data);exit;
		}
		
		
		
		
		
		
		
		
		
		//判断名额数量是否已满（支付占用名额）
		if(!empty($order_info) && $order_info['isPay']>=0){
        	}
        	else{
        	$limit_number=$this->get_limit_number($order_info['cat_id']);  
        	if($limit_number=='N'){
        	  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	        echo json_encode($return);
	        exit;
        	}
        	}
        	
		
		//证件号已经提交过报名申请...
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and confirm_apply=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($order_info['id_type']) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($order_info['id_number']) .'" ' ;
		
		 if(!empty($order_info)){
		 	 $and_cond=$and_cond.' and id!="' . addslashes($order_id) .'" ' ;
		 }
		
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

        
        	//$price_race=$this->get_price_race($_REQUEST['cat_id'],$guoji);
        	//$amount_total=$price_race;
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
	    
	    
	    
	    //邀请码用户为免费用户，直接设为费用为0，且已支付。
    	if($order_info['is_free']==1){
    		$price_race=0;
    		$amount_total=$price_race;
    		$payDateTime = date("Y-m-d H:i:s");
    		
    		$OrderMod = M('order');
        	$sql=sprintf("UPDATE %s SET payDateTime='".addslashes($payDateTime)."' 
	        , isPay='1' 
	        , isExpire='1' 
	        , price_race='".addslashes($price_race)."' 
	        , amount_total='".addslashes($amount_total)."' 
	        where id='".addslashes($order_id)."' ", $OrderMod->getTableName() );
	        $result = $OrderMod->execute($sql);
    	}
	    
	    
	    
	    if(!empty($invit_data['id'])){
		$orderMod = M('invit');
	    $sql=sprintf("update %s SET order_id='".addslashes($order_id)."' 
	    where id='".addslashes($invit_data['id'])."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    }
	    
	    //$this->set_log_sql($sql);
	    
	    
             $_SESSION['id_type']=$order_info['id_type'];
        	$_SESSION['id_number']=$order_info['id_number'];
        	
        	
        	
        	if($order_info['cat_id']==1){
        	$cat_name='马拉松';
        	}
        	if($order_info['cat_id']==2){
        	$cat_name='半程马拉松';
        	}
        	if($order_info['cat_id']==3){
        	$cat_name='迷你马拉松';
        	}
        	
        	/*
        	//邮件通知
        	if($this->open_email_msg==1){
			$to=$order_info['email'];
			$name=$order_info['realname'];
			$subject='马拉松赛组委会通知';
			//$msg_body='尊敬的'.$order_info['realname'].'，感谢您参与2017年马拉松赛，您的报名已提交，请等待组委会审核和抽签！ 请在抽签结束后到报名查询菜单里查看是否中签，感谢您的参与！';
			if(!empty($invit_data['id'])){
				$msg_body='尊敬的'.$order_info['realname'].'，感谢您报名'.$cat_name.'项目，您的报名信息已经提交，请于8月14日10:00上传体检证明、成绩证书并填写摆渡信息，感谢您的参与！【马拉松组委会】';
			}
			else{
				$msg_body='尊敬的'.$order_info['realname'].'，感谢您报名'.$cat_name.'项目，您的报名信息已经提交，请耐心等待组委会抽签！抽签结果将于8月14日10:00发布，请及时通过官网首页“状态查询”功能查看是否中签，感谢您的参与！【马拉松组委会】';
			}
			$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
        	}
        	
        	//短信通知
        	if($this->open_sms_msg==1){
        	//$msg_body='尊敬的'.$order_info['realname'].'，感谢您参与2017年马拉松赛，您的报名已提交，请等待组委会审核和抽签！ 请在抽签结束后到报名查询菜单里查看是否中签，感谢您的参与！';
        	if(!empty($invit_data['id'])){
				$msg_body='尊敬的'.$order_info['realname'].'，感谢您报名'.$cat_name.'项目，您的报名信息已经提交，请于8月14日10:00上传体检证明、成绩证书并填写摆渡信息，感谢您的参与！【马拉松组委会】';
			}
			else{
				$msg_body='尊敬的'.$order_info['realname'].'，感谢您报名'.$cat_name.'项目，您的报名信息已经提交，请耐心等待组委会抽签！抽签结果将于8月14日10:00发布，请及时通过官网首页“状态查询”功能查看是否中签，感谢您的参与！【马拉松组委会】';
			}
			header("Content-type:text/html; charset=UTF-8");
			require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
			$clapi  = new ChuanglanSmsApi();
			$code = mt_rand(100000,999999);
			$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
			//echo "<pre>";print_r($result_sms);exit;
        	}
        	*/
        	
        	
        	//全部设为直接中签
        	$status_apply=0;
        	//公益跑不抽签，直接设为中签
        	if($order_info['cat_id']==4 || $order_info['cat_id']==5 ||  !empty($invit_data['id']) || 1==1  ){
        		
        		$status_apply=1;
        		
        	$orderMod = M('order');
		    $sql=sprintf("update %s SET status_apply='1' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
		    
		    /*
	        	//邮件通知
	        	if($this->open_email_msg==1){
				$to=$order_info['email'];
				$name=$order_info['realname'];
				$subject='马拉松赛组委会通知';
				$msg_body='尊敬的'.$order_info['realname'].'，恭喜您在2017年马拉松赛中签！ 请到网站报名查询栏目中进行下一步操作！';
				$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
	        	}
	        	
	        	//短信通知
	        	if($this->open_sms_msg==1){
	        		$msg_body='尊敬的'.$order_info['realname'].'，恭喜您在2017年马拉松赛中签！ 请到网站报名查询栏目中进行下一步操作！';
				header("Content-type:text/html; charset=UTF-8");
				require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
				$clapi  = new ChuanglanSmsApi();
				$code = mt_rand(100000,999999);
				$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
				//echo "<pre>";print_r($result_sms);exit;
	        	}
	        	*/
        	
        	}
        	
	    
        $return = array(
            'order_id' => $order_id,
            'status_apply' => $status_apply,
            'cat_id' => $order_info['cat_id'],
            'bus_point' => $order_info['bus_point'],
            'isPay' => $order_info['isPay'],
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
		
		echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		exit;
		
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
		
		//echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		//exit;
		
		//echo "<pre>";print_r($_REQUEST);exit;
		
		
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
		
		//不用考虑是否中签，最后确认提交的时候，会全部设为中签
		//if($order_info['status_apply']!=1){
		//	$return['success']='您尚未中签';
	      // echo json_encode($return);
	      // exit;
		//}
		
		
		if($order_info['status_attach']==1){
			$return['success']='您详细资料已经审核通过，无法再修改。';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
		//判断名额数量是否已满（支付占用名额）
		if(!empty($order_info) && $order_info['isPay']>=0){
        	}
        	else{
        	$limit_number=$this->get_limit_number($order_info['cat_id']);  
        	if($limit_number=='N'){
        	  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	        echo json_encode($return);
	        exit;
        	}
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
		
		
		
		//if(isset($_REQUEST['best_chengji_item']) && !empty($_REQUEST['best_chengji_item'])){
		//}
		//else{
		//    $return['success']='请输入最好成绩项目';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
	
		 	 
		 	 
		//if(isset($_REQUEST['best_chengji_score']) && !empty($_REQUEST['best_chengji_score'])){
		//}
		//else{
		 //   $return['success']='请输入完赛证书成绩';
	      //  echo json_encode($return);
	       // exit;
		//}
		
		//if(isset($_REQUEST['cert_chengji']) && !empty($_REQUEST['cert_chengji'])){
		//}
		//else{
		 //   $return['success']='请输入成绩证明';
	      //  echo json_encode($return);
	        //exit;
		//}
		
		
        
        //if(isset($_REQUEST['bus_point']) && !empty($_REQUEST['bus_point'])){
		//}
		//else{
		//    $return['success']='请输入摆渡车点';
	     //   echo json_encode($return);
	      //  exit;
		//}
		
        
        
        /*
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
        */
        	
		//if(isset($_REQUEST['renshou_zengxian']) && !empty($_REQUEST['renshou_zengxian'])){
		//}
		//else{
		 //   $return['success']='请输入是否需要中意人寿赠险 ';
	     //   echo json_encode($return);
	      //  exit;
		//}
        
        
        
        if( !empty($order_info['team_id']) ){
        
		$orderMod = M('order');
	    $sql=sprintf("update %s SET 
	    danwei='".addslashes($this->remove_xss($_REQUEST['danwei']))."' 
    	, danwei_xingzhi='".addslashes($this->remove_xss($_REQUEST['danwei_xingzhi']))."' 
	    , zhiwei='".addslashes($this->remove_xss($_REQUEST['zhiwei']))."' 
	    , xueli='".addslashes($this->remove_xss($_REQUEST['xueli']))."' 
	    , yueshouru='".addslashes($this->remove_xss($_REQUEST['yueshouru']))."' 
	    , shengao='".addslashes($this->remove_xss($_REQUEST['shengao']))."' 
	    , tizhong='".addslashes($this->remove_xss($_REQUEST['tizhong']))."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
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
				//if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png" && $file_type!="rar" && $file_type!="zip" && $file_type!="pdf"  )
				if ($file_type!="jpg" && $file_type!="png"  )
				{
					@unlink($dest);
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("上传照片限定jpg、gif、png、rar、zip类型");history.back();</script>';
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("体检证明限定jpg、gif、png、rar、zip、pdf类型");history.back();</script>';
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("体检证明限定jpg、png类型");history.back();</script>';
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
				//if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png" && $file_type!="rar" && $file_type!="zip" && $file_type!="pdf"  )
				if ($file_type!="jpg" && $file_type!="png"  )
				{
					@unlink($dest);
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("上传照片限定jpg、gif、png、rar、zip类型");history.back();</script>';
					//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("成绩证明限定jpg、gif、png、rar、zip、pdf类型");history.back();</script>';
					echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("成绩证明限定jpg、png类型");history.back();</script>';
					exit;
					//$this->jsonData(1,'上传照片限定jpg、gif、png、rar、zip类型');
				    //exit;
				}
				
	        }
	        
	        
		
        //var_dump($order_id);exit;
        
        
        
        $tmp_info=array();
        //$tmp_info['best_chengji_item']=$_REQUEST['best_chengji_item'];
        $tmp_info['best_chengji_score']=$_REQUEST['best_chengji_score'];
        $tmp_info['bus_point']=$_REQUEST['bus_point'];
        /*
        $tmp_info['cloth_size']=$_REQUEST['cloth_size'];
        $tmp_info['medical_runner']=$_REQUEST['medical_runner'];
        $tmp_info['running_group_name']=$_REQUEST['running_group_name'];
        $tmp_info['ec_name']=$_REQUEST['ec_name'];
        $tmp_info['ec_relation']=$_REQUEST['ec_relation'];
        $tmp_info['ec_phone']=$_REQUEST['ec_phone'];
        $tmp_info['ec_address']=$_REQUEST['ec_address'];
        */
        //$tmp_info['renshou_zengxian']=$_REQUEST['renshou_zengxian'];
        
        
        
	    if(!empty($photo_normal)){
	    	$tmp_info['cert_medical']=$photo_normal;
	    }
	    
	    if(!empty($photo_normal2)){
	    		$tmp_info['cert_chengji']=$photo_normal2;
	    }
	    
	    
	    //echo "<pre>";print_r($tmp_info);exit;
	    
	    
	    
		$orderMod = M('order');
	    $sql=sprintf("update %s SET 
	    best_chengji_score='".addslashes($this->remove_xss($_REQUEST['best_chengji_score']))."' 
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
	    
	    
	    
	    
        	//$_SESSION['tmp_info']=$tmp_info;
        
        
        
        	
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
	    
	    
	    
	    $url=U('baoming/apply_confirm', array('order_id'=>$order_id ));
		redirect($url);
		exit;
	    
	    //$url=U('baoming/attach_confirm', array('order_id'=>$order_id ));
		//redirect($url);
		//exit;
	    
	    
	    
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
		
		echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		exit;
		
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
		
		echo "感谢您报名20107马拉松赛，报名工作已截止，感谢您的参与！";
		exit;
		
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
		
		
		
		
		
		//$_REQUEST['best_chengji_item']=$_SESSION['tmp_info']['best_chengji_item'];
		$_REQUEST['best_chengji_score']=$_SESSION['tmp_info']['best_chengji_score'];
		$_REQUEST['bus_point']=$_SESSION['tmp_info']['bus_point'];
		/*
		$_REQUEST['cloth_size']=$_SESSION['tmp_info']['cloth_size'];
		$_REQUEST['medical_runner']=$_SESSION['tmp_info']['medical_runner'];
		$_REQUEST['running_group_name']=$_SESSION['tmp_info']['running_group_name'];
		$_REQUEST['ec_name']=$_SESSION['tmp_info']['ec_name'];
		$_REQUEST['ec_relation']=$_SESSION['tmp_info']['ec_relation'];
		$_REQUEST['ec_phone']=$_SESSION['tmp_info']['ec_phone'];
		$_REQUEST['ec_address']=$_SESSION['tmp_info']['ec_address'];
		*/
		//$_REQUEST['renshou_zengxian']=$_SESSION['tmp_info']['renshou_zengxian'];
		
		$photo_normal=$_SESSION['tmp_info']['cert_medical'];
		$photo_normal2=$_SESSION['tmp_info']['cert_chengji'];
		
		
		$orderMod = M('order');
	    $sql=sprintf("update %s SET 
	    best_chengji_score='".addslashes($this->remove_xss($_REQUEST['best_chengji_score']))."' 
    	, bus_point='".addslashes($this->remove_xss($_REQUEST['bus_point']))."' 
    	, addtime_attach='".addslashes($this->remove_xss($addtime))."' 
	    , status_attach='0' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    /*
	    best_chengji_item='".addslashes($this->remove_xss($_REQUEST['best_chengji_item']))."' 
	    , cloth_size='".addslashes($this->remove_xss($_REQUEST['cloth_size']))."' 
    	, medical_runner='".addslashes($this->remove_xss($_REQUEST['medical_runner']))."' 
    	, running_group_name='".addslashes($this->remove_xss($_REQUEST['running_group_name']))."' 
    	, ec_name='".addslashes($this->remove_xss($_REQUEST['ec_name']))."' 
    	, ec_relation='".addslashes($this->remove_xss($_REQUEST['ec_relation']))."' 
    	, ec_phone='".addslashes($this->remove_xss($_REQUEST['ec_phone']))."' 
    	, ec_address='".addslashes($this->remove_xss($_REQUEST['ec_address']))."' 
	    */
	    //, renshou_zengxian='".addslashes($this->remove_xss($_REQUEST['renshou_zengxian']))."' 
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
	    
	    
	   
	    
		//公益跑的人，不用审核，直接支付 (20170818又说：还是需要先审核)
		if($order_info['cat_id']==4 || $order_info['cat_id']==5){
		//$orderMod = M('order');
	    //$sql=sprintf("update %s SET status_attach='1' 
	   // where id='".addslashes($order_id)."' 
	    //", $orderMod->getTableName() );
	    //$result = $orderMod->execute($sql);
	    }
	    
	    
	    
	    
	    //迷你马拉松的人，不用审核，直接支付
	    if($order_info['cat_id']==3){
		    	$orderMod = M('order');
		    $sql=sprintf("update %s SET status_attach='1' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
		    
		    
		    if($order_info['is_free']==1){ //邀请码免费
		        	//邮件通知
		        	if($this->open_email_msg==1){
					$to=$order_info['email'];
					$name=$order_info['realname'];
					$subject='马拉松赛组委会通知';
					$msg_body='尊敬的'.$order_info['realname'].'，恭喜您已成功报名迷你马拉松项目，您可于9月8日10:00查询参赛号码，感谢您的支持和参与！【马拉松组委会】';
					$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
		        	}
		        	
		        	//短信通知
		        	if($this->open_sms_msg==1){
		        		$msg_body='尊敬的'.$order_info['realname'].'，恭喜您已成功报名迷你马拉松项目，您可于9月8日10:00查询参赛号码，感谢您的支持和参与！【马拉松组委会】';
					header("Content-type:text/html; charset=UTF-8");
					require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
					$clapi  = new ChuanglanSmsApi();
					$code = mt_rand(100000,999999);
					$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
					//echo "<pre>";print_r($result_sms);exit;
		        	}
		    }
	    }
	    
	    
	    
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
	    
	    
	    
	    
	    
	    
	    //20170804改为详细资料填完，不能支付，需要审核通过后，进入查询通道才能支付。所以详细资料填完，直接出等待审核的界面
	    $_SESSION['id_type']=$order_info['id_type'];
        	$_SESSION['id_number']=$order_info['id_number'];
        	 $url=U('baoming/order_search_finish', array('order_id'=>$order_id ));
		redirect($url);
		exit;
	    
	    
	    
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
		
    	$this->assign('curmenu', '3');
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
		
		
		/*
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
            	//$return['success']='请输入正确的验证码 Please input Identifying code';
		        //echo json_encode($return);
		        //exit;
            }
            */
            
            
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and confirm_apply=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($id_type) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($id_number) .'" ' ;
		$and_cond=$and_cond.' and mobile="' . addslashes($mobile) .'" ' ;
		//echo $and_cond;exit;
		$orderMod = M('order');
        $order_data = $orderMod->where(" 1 ".$and_cond )->order('isPay desc')->select(); 
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
	    }
	    
	    
	    
	    
	    
		
		$this->assign('curmenu', '3');
		
		
		if($order_info['status_attach']==0){
			if($order_info['status_apply']==0){
	             	$this->display('order_search_finish_status_apply_0');    //待抽签
			}
			elseif($order_info['status_apply']==1){
				if($order_info['confirm_attach']==1){
					//$this->display('order_search_finish_status_attach_0');   //填完详细资料、待审核、未支付
					
					if($order_info['isPay']==1){
						$this->display('order_search_finish_status_applyattach_1');   //已支付、等待审核
					}
					else{
						$this->display('order_search_finish_status_applyattach_1_payment');    //未支付、等待审核
					}
					
				}
				else{
					    if(!empty($order_info['invit_code'])){
					    	$this->display('order_search_finish_status_apply_1_invit');   //邀请码用户，已中签，不能让他填写详细资料页
					    }
					    else{
	             			$this->display('order_search_finish_status_apply_1');   //未填详细资料、待审核、未支付
	             		}
	            	}
			}
			elseif($order_info['status_apply']==2){
	             	$this->display('order_search_finish_status_apply_2');  //未中签
			}
			else{
			}
		}
		elseif($order_info['status_attach']==1){
			if($order_info['isPay']==1){
				$this->display('order_search_finish_status_attach_1');   //审核通过、已支付
			}
			else{
				$this->display('order_search_finish_status_attach_1_payment');    //审核通过、未支付
			}
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
	
	
	
	
	
	//报名申请  定向赛
	//示例： http://cdmalasong.loc/baoming/apply_orient
	public function apply_orient(){
		
		if(date('YmdHis')>'20170906170000'){
				echo "城市定向赛报名已结束，感谢您的关注！";
				exit;
		    }
		    
		
		$orderMod = M('guoji');
        $guoji_list = $orderMod->where(" 1 " )->order('sort asc,guoji_name_abbr asc')->limit('0,1000')->select();
        $this->assign('guoji_list', $guoji_list);
        //echo "<pre>";print_r($guoji_list);exit;
        
        
    	$this->assign('curmenu', '7');
        $this->display('apply_orient');
    }
	
	
	
	
	//报名申请  隐藏 定向赛
	//示例： http://cdmalasong.loc/baoming/apply_orient_hidden
	public function apply_orient_hidden(){
		
		if(date('YmdHis')>'20180906170000'){
				echo "城市定向赛报名已结束，感谢您的关注！";
				exit;
		    }
		    
		
		$orderMod = M('guoji');
        $guoji_list = $orderMod->where(" 1 " )->order('sort asc,guoji_name_abbr asc')->limit('0,1000')->select();
        $this->assign('guoji_list', $guoji_list);
        //echo "<pre>";print_r($guoji_list);exit;
        
        
    	$this->assign('curmenu', '7');
        $this->display('apply_orient');
    }
	
	
	
	
	//报名申请 定向赛  提交  http://cdmalasong.loc/baoming/apply_orient_sub?cat_id=1&realname=aaa&sex=1&birth_day=1990-01-01&id_type=1&id_number=310105195508120023&address=bbb&mobile=13911112222&email=ccc@ccc.com&cityarea=中国&blood=O
	public function apply_orient_sub(){
		
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
            
            
            
            
            /*
		$order_info=false;
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
			
			$order_info=$this->verify_body($order_id);
			if($order_info==false){
			    $return['success']='验证失败';
		        echo json_encode($return);
		        exit;
			}
			//echo "<pre>";print_r($order_info);exit;
			
			if($order_info['status_attach']==1){
				$return['success']='您详细资料已经审核通过，无法再修改。';
		        echo json_encode($return);
		        exit;
			}
			
		}
		else{
		    $order_id='';
		}
		//var_dump($order_id);exit;
		*/
		
		
            
            
            
		if(isset($_REQUEST['cat_id']) && !empty($_REQUEST['cat_id'])){
		}
		else{
		    $return['success']='请输入报名类型';
	        echo json_encode($return);
	        exit;
		}
		
		//$is_correct_cat=$this->get_price_race($_REQUEST['cat_id']);
		//if($is_correct_cat<=0){
		//	$return['success']='报名类型有误';
	      //    echo json_encode($return);
	      //    exit;
		//}
		
		
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
		    $return['success']='请输入通讯地址';
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
        
        
        
		//if(isset($_REQUEST['email']) && !empty($_REQUEST['email'])){
		//}
		//else{
		//    $return['success']='请输入邮箱';
	     //   echo json_encode($return);
	      //  exit;
		//}
        
        
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
        
        
        
		if(isset($_REQUEST['cloth_size']) && !empty($_REQUEST['cloth_size'])){
		}
		else{
		    $return['success']='请输入衣服尺码';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		//if(isset($_REQUEST['medical_runner']) && !empty($_REQUEST['medical_runner'])){
		//}
		//else{
		//    $return['success']='请输入是否医护跑者';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
        
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
		
		//if(isset($_REQUEST['ec_relation']) && !empty($_REQUEST['ec_relation'])){
		//}
		//else{
		//    $return['success']='请输入与联系人关系';
	    //    echo json_encode($return);
	      //  exit;
		//}
		
        
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
        
        
		//if(isset($_REQUEST['ec_address']) && !empty($_REQUEST['ec_address'])){
		//}
		//else{
		//    $return['success']='请输入紧急联系人地址';
	     //   echo json_encode($return);
	      //  exit;
		//}
		
        
		//if(isset($_REQUEST['renshou_zengxian']) && !empty($_REQUEST['renshou_zengxian'])){
		//}
		//else{
		//    $return['success']='请输入是否需要中意人寿赠险 GENERALI CHINA free insurance';
	    //    echo json_encode($return);
	    //    exit;
		//}
		
		
		
		/*
		$invit_data=array();
		$invit_code=empty($_REQUEST['invit_code'])?'':$_REQUEST['invit_code'];
		$is_free=2;
		if(isset($_REQUEST['invit_code']) && !empty($_REQUEST['invit_code'])){
			
	      	//验证邀请码是否正确
				$and_cond='';
				$and_cond=$and_cond.' and cat_id="' . addslashes($_REQUEST['cat_id']) .'" ' ;
				$and_cond=$and_cond.' and invit_code="' . addslashes($_REQUEST['invit_code']) .'" ' ;
				//echo $and_cond;exit;
				$invitMod = M('invit');
		        $invit_data = $invitMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($invit_data);exit;
		        if(empty($invit_data)){
		         $return['success']='邀请码错误 Invitation code is error ';
			        echo json_encode($return);
			        exit;
		        }
		        
		        $invit_data=empty($invit_data)?array():$invit_data[0];
		        //echo "<pre>";print_r($invit_data);exit;
		        
		       if($invit_data['order_id']>0){
		       	   if(!empty($order_info) && $order_info['invit_code']==$invit_data['invit_code']){
		       	   }
		       	   else{
			       	   $return['success']='邀请码已经被使用 Invitation code is used ';
				        echo json_encode($return);
				        exit;
			   	   }
		       }
		       $is_free=$invit_data['is_free'];
		        //echo "<pre>";print_r($invit_data);exit;
		}
		*/
		
		
        
        	//判断名额数量是否已满
        	$limit_number=$this->get_limit_number_orient($_REQUEST['cat_id']);  
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
		//3、迷你马拉松项目选手年龄限10岁以上（2007年12 月31 日以前出生）；
		//4、公益马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）70岁以下（1947年1月1日以后出生）；
		//5、公益半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）70岁以下（1947年1月1日以后出生）；
		/*
		20170805改为：
		1、马拉松项目选手年龄限20岁以上（1997年 12月 31日以前出生）；
		2、半程马拉松项目选手年龄限16岁以上（2001年12 月31 日以前出生）；
		3、迷你马拉松项目选手年龄限15岁以上（2002年12 月31 日以前出生）；
		*/
		/*
		if($id_type==1){
			$idcard_birth_arr=$this->get_idcard_birth($_REQUEST['id_number']);   //$idcard_birth_arr['yy']  $idcard_birth_arr['mm']  $idcard_birth_arr['dd']
			$verify_birth=$idcard_birth_arr['yy'].$idcard_birth_arr['mm'].$idcard_birth_arr['dd'];
			$birth_age_permit=$verify_birth;
		}
		else{
			$birth_age_permit=str_replace('-','',$_REQUEST['birth_day']);
		}
		if($_REQUEST['cat_id']==1){
			//if($birth_age_permit>=19470101 && $birth_age_permit<=19971231)
			if($birth_age_permit<=19971231)
			{
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==2){
			//if($birth_age_permit>=19470101 && $birth_age_permit<=20011231)
			if($birth_age_permit<=20011231)
			{
			}
			else{
				$return['success']='抱歉，请您按照各项目的年龄限定进行报名。';
			        echo json_encode($return);
			        exit;
			}
		}
		elseif($_REQUEST['cat_id']==3){
			//if($birth_age_permit<=20071231)
			if($birth_age_permit<=20021231)
			{
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
		*/
		
		
		//证件号已经提交过报名申请...
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and confirm_apply=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($_REQUEST['id_type']) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($_REQUEST['id_number']) .'" ' ;
		
		 if(!empty($order_info)){
		 	 $and_cond=$and_cond.' and id!="' . addslashes($order_id) .'" ' ;
		 }
		
		//echo $and_cond;exit;
		$orderMod = M('order_orient');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        //echo "<pre>";print_r($order_data);exit;
        $nums_used=empty($order_data)?0:count($order_data);
        //var_dump($nums_used);exit;
		
    	if($nums_used > 0 ) {
    		//$return['success']='您的证件号码已提交，请勿重复报名。请前往首页查询报名状态！';
    		$return['success']='您的证件号码已提交，请勿重复报名。';
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

        
        	//$price_race=$this->get_price_race($_REQUEST['cat_id'],$guoji);
        	$price_race=0;
        	$amount_total=$price_race;
        	//var_dump($price_race);exit;
		
		
		if(empty($order_id)){
			$is_insert=1;  //新增
		$orderMod = M('order_orient');
        $orderMod->status=0;
        $order_id = $orderMod->add();
        //var_dump($order_id);exit;
        
		$orderMod = M('order_orient');
	    $sql=sprintf("update %s SET addtime_apply='".addslashes($this->remove_xss($addtime))."' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
        }
        else{
        	$is_insert=2;  //编辑
        }
        
        
		$orderMod = M('order_orient');
	    $sql=sprintf("update %s SET address='".addslashes($this->remove_xss($_REQUEST['address']))."' 
    	, mobile='".addslashes($this->remove_xss($_REQUEST['mobile']))."' 
    	, email='".addslashes($this->remove_xss($_REQUEST['email']))."' 
    	, cityarea='".addslashes($this->remove_xss($_REQUEST['cityarea']))."' 
    	, guoji='".addslashes($this->remove_xss($guoji))."' 
    	, blood='".addslashes($this->remove_xss($_REQUEST['blood']))."' 
    	, cloth_size='".addslashes($this->remove_xss($_REQUEST['cloth_size']))."' 
    	, running_group_name='".addslashes($this->remove_xss($_REQUEST['running_group_name']))."' 
    	, ec_name='".addslashes($this->remove_xss($_REQUEST['ec_name']))."' 
    	, ec_phone='".addslashes($this->remove_xss($_REQUEST['ec_phone']))."' 
    	, price_race='".addslashes($price_race)."' 
    	, amount_total='".addslashes($amount_total)."' 
    	, is_free='".addslashes($this->remove_xss($is_free))."' 
    	, invit_code='".addslashes($this->remove_xss($invit_code))."' 
	    , status='1' 
	    , reg_channel='官网' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	   
	   //  , addtime_apply='".addslashes($this->remove_xss($addtime))."' 
	   //, renshou_zengxian='".addslashes($this->remove_xss($_REQUEST['renshou_zengxian']))."' 
	    //, ec_relation='".addslashes($this->remove_xss($_REQUEST['ec_relation']))."' 
	    //, ec_address='".addslashes($this->remove_xss($_REQUEST['ec_address']))."' 
	    //, medical_runner='".addslashes($this->remove_xss($_REQUEST['medical_runner']))."' 
	    //$this->set_log_sql($sql);
	    
	    
	    //20170817 17点之后，不能再修改 姓名、性别、证件、生日、参赛项目
	    if((date('YmdHis')<'20170811170000' && $is_insert==2) || $is_insert==1){
			$orderMod = M('order_orient');
		    $sql=sprintf("update %s SET cat_id='".addslashes($this->remove_xss($_REQUEST['cat_id']))."' 
		    , realname='".addslashes($this->remove_xss($_REQUEST['realname']))."' 
		    , sex='".addslashes($this->remove_xss($_REQUEST['sex']))."' 
	    	, birth_day='".addslashes($this->remove_xss($_REQUEST['birth_day']))."' 
	    	, id_type='".addslashes($this->remove_xss($_REQUEST['id_type']))."' 
	    	, id_number='".addslashes($this->remove_xss($_REQUEST['id_number']))."' 
	    	   where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
	    }
	    
	    
	    
	    
	    $orderMod = M('order_orient');
	    $sql=sprintf("update %s SET  realname_1='".addslashes($this->remove_xss($_REQUEST['realname_1']))."' 
	    	  , sex_1='".addslashes($this->remove_xss($_REQUEST['sex_1']))."' 
	    	  , birth_day_1='".addslashes($this->remove_xss($_REQUEST['birth_day_1']))."' 
	    	  , id_type_1='".addslashes($this->remove_xss($_REQUEST['id_type_1']))."' 
	    	  , id_number_1='".addslashes($this->remove_xss($_REQUEST['id_number_1']))."' 
	    	  , address_1='".addslashes($this->remove_xss($_REQUEST['address_1']))."' 
	    	  , mobile_1='".addslashes($this->remove_xss($_REQUEST['mobile_1']))."' 
    	        , cityarea_1='".addslashes($this->remove_xss($_REQUEST['cityarea_1']))."' 
    	        , blood_1='".addslashes($this->remove_xss($_REQUEST['blood_1']))."' 
    	        , cloth_size_1='".addslashes($this->remove_xss($_REQUEST['cloth_size_1']))."' 
    	    	  , ec_name_1='".addslashes($this->remove_xss($_REQUEST['ec_name_1']))."' 
    	        , ec_phone_1='".addslashes($this->remove_xss($_REQUEST['ec_phone_1']))."' 
    	   where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
		    
	    
	    
	    $orderMod = M('order_orient');
	    $sql=sprintf("update %s SET  realname_2='".addslashes($this->remove_xss($_REQUEST['realname_2']))."' 
	    	  , sex_2='".addslashes($this->remove_xss($_REQUEST['sex_2']))."' 
	    	  , birth_day_2='".addslashes($this->remove_xss($_REQUEST['birth_day_2']))."' 
	    	  , id_type_2='".addslashes($this->remove_xss($_REQUEST['id_type_2']))."' 
	    	  , id_number_2='".addslashes($this->remove_xss($_REQUEST['id_number_2']))."' 
	    	  , address_2='".addslashes($this->remove_xss($_REQUEST['address_2']))."' 
	    	  , mobile_2='".addslashes($this->remove_xss($_REQUEST['mobile_2']))."' 
    	        , cityarea_2='".addslashes($this->remove_xss($_REQUEST['cityarea_2']))."' 
    	        , blood_2='".addslashes($this->remove_xss($_REQUEST['blood_2']))."' 
    	        , cloth_size_2='".addslashes($this->remove_xss($_REQUEST['cloth_size_2']))."' 
    	    	  , ec_name_2='".addslashes($this->remove_xss($_REQUEST['ec_name_2']))."' 
    	        , ec_phone_2='".addslashes($this->remove_xss($_REQUEST['ec_phone_2']))."' 
    	   where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    $orderMod = M('order_orient');
	    $sql=sprintf("update %s SET  realname_3='".addslashes($this->remove_xss($_REQUEST['realname_3']))."' 
	    	  , sex_3='".addslashes($this->remove_xss($_REQUEST['sex_3']))."' 
	    	  , birth_day_3='".addslashes($this->remove_xss($_REQUEST['birth_day_3']))."' 
	    	  , id_type_3='".addslashes($this->remove_xss($_REQUEST['id_type_3']))."' 
	    	  , id_number_3='".addslashes($this->remove_xss($_REQUEST['id_number_3']))."' 
	    	  , address_3='".addslashes($this->remove_xss($_REQUEST['address_3']))."' 
	    	  , mobile_3='".addslashes($this->remove_xss($_REQUEST['mobile_3']))."' 
    	        , cityarea_3='".addslashes($this->remove_xss($_REQUEST['cityarea_3']))."' 
    	        , blood_3='".addslashes($this->remove_xss($_REQUEST['blood_3']))."' 
    	        , cloth_size_3='".addslashes($this->remove_xss($_REQUEST['cloth_size_3']))."' 
    	    	  , ec_name_3='".addslashes($this->remove_xss($_REQUEST['ec_name_3']))."' 
    	        , ec_phone_3='".addslashes($this->remove_xss($_REQUEST['ec_phone_3']))."' 
    	   where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    
	    
	    
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
	
	
	//报名申请  定向赛 确认页  http://cdmalasong.loc/baoming/apply_orient_confirm?order_id=2385
	public function apply_orient_confirm(){
		
		
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		$order_info=$this->verify_body_orient($order_id,true,false);
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		
		
		
		//if($order_info['confirm_apply']==1){
		//	$return['success']='您基本资料已经确认提交，无法再修改。';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
		
		
		//if($order_info['status_attach']==1){
	//		$return['success']='您详细资料已经审核通过，无法再修改。';
	  //      echo json_encode($return);
	    //    exit;
		//}
		
		
		
    	$this->assign('curmenu', '7');
        $this->display('apply_orient_confirm');
    }
    
    
    
	//报名申请  定向赛 确认页 提交  http://cdmalasong.loc/baoming/apply_orient_confirm_sub?order_id=2407
	public function apply_orient_confirm_sub(){
		
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
		
		
		$order_info=$this->verify_body_orient($order_id,true,false);
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		//echo "<pre>";print_r($order_info);exit;
		
		
		
		//if($order_info['confirm_apply']==1){
		//	$return['success']='您基本资料已经确认提交，无法再修改。';
	      //  echo json_encode($return);
	       // exit;
		//}
		
		
		
		
		//if($order_info['status_attach']==1){
		//	$return['success']='您详细资料已经审核通过，无法再修改。';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
		
		
		
		/*
		$invit_data=array();
		$invit_code=$order_info['invit_code'];
		$is_free=$order_info['is_free'];
		if(isset($order_info['invit_code']) && !empty($order_info['invit_code'])){
			
	      	//验证邀请码是否正确
				$and_cond='';
				$and_cond=$and_cond.' and cat_id="' . addslashes($order_info['cat_id']) .'" ' ;
				$and_cond=$and_cond.' and invit_code="' . addslashes($order_info['invit_code']) .'" ' ;
				//echo $and_cond;exit;
				$invitMod = M('invit');
		        $invit_data = $invitMod->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($invit_data);exit;
		        if(empty($invit_data)){
		         $return['success']='邀请码错误 Invitation code is error ';
			        echo json_encode($return);
			        exit;
		        }
		        
		        $invit_data=empty($invit_data)?array():$invit_data[0];
		        //echo "<pre>";print_r($invit_data);exit;
		        
		       if($invit_data['order_id']>0){
		       	    if(!empty($order_info) && $order_info['invit_code']==$invit_data['invit_code']){
		       	   }
		       	   else{
		       	  $return['success']='邀请码已经被使用 Invitation code is used ';
			        echo json_encode($return);
			        exit;
			         }
		       }
		       $is_free=$invit_data['is_free'];
		        //echo "<pre>";print_r($invit_data);exit;
		}
		*/
		
		
		
		
		
		
		
		
		//判断名额数量是否已满
        	//$limit_number=$this->get_limit_number($order_info['cat_id']);  
        	//if($limit_number=='N'){
        	//  $return['success']='报名类型名额已满 The quota for this Registration type is full';
	       // echo json_encode($return);
	       // exit;
        	//}
        	
        	
		
		//证件号已经提交过报名申请...
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and confirm_apply=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($order_info['id_type']) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($order_info['id_number']) .'" ' ;
		
		 if(!empty($order_info)){
		 	 $and_cond=$and_cond.' and id!="' . addslashes($order_id) .'" ' ;
		 }
		
		//echo $and_cond;exit;
		$orderMod = M('order_orient');
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

        
        	//$price_race=$this->get_price_race($_REQUEST['cat_id'],$guoji);
        	//$amount_total=$price_race;
        	//var_dump($price_race);exit;
        	
		
		//$orderMod = M('order');
        //$orderMod->status=0;
        //$order_id = $orderMod->add();
        //var_dump($order_id);exit;
        
		$orderMod = M('order_orient');
	    $sql=sprintf("update %s SET confirm_apply='1' 
	    where id='".addslashes($order_id)."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    
	    
	    /*
	    //邀请码用户为免费用户，直接设为费用为0，且已支付。
    	if($order_info['is_free']==1){
    		$price_race=0;
    		$amount_total=$price_race;
    		$payDateTime = date("Y-m-d H:i:s");
    		
    		$OrderMod = M('order');
        	$sql=sprintf("UPDATE %s SET payDateTime='".addslashes($payDateTime)."' 
	        , isPay='1' 
	        , isExpire='1' 
	        , price_race='".addslashes($price_race)."' 
	        , amount_total='".addslashes($amount_total)."' 
	        where id='".addslashes($order_id)."' ", $OrderMod->getTableName() );
	        $result = $OrderMod->execute($sql);
    	}
	    */
	    
	    
	    /*
	    if(!empty($invit_data['id'])){
		$orderMod = M('invit');
	    $sql=sprintf("update %s SET order_id='".addslashes($order_id)."' 
	    where id='".addslashes($invit_data['id'])."' 
	    ", $orderMod->getTableName() );
	    //echo $sql;exit;
	    $result = $orderMod->execute($sql);
	    }
	    */
	    
	    //$this->set_log_sql($sql);
	    
	    
             $_SESSION['id_type']=$order_info['id_type'];
        	$_SESSION['id_number']=$order_info['id_number'];
        	
        	
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
        	*/
        	
        	/*
        	//邮件通知
        	if($this->open_email_msg==1){
			$to=$order_info['email'];
			$name=$order_info['realname'];
			$subject='马拉松赛组委会通知';
			//$msg_body='尊敬的'.$order_info['realname'].'，感谢您参与2017年马拉松赛，您的报名已提交，请等待组委会审核和抽签！ 请在抽签结束后到报名查询菜单里查看是否中签，感谢您的参与！';
			if(!empty($invit_data['id'])){
				$msg_body='尊敬的'.$order_info['realname'].'，感谢您报名'.$cat_name.'项目，您的报名信息已经提交，请于8月14日10:00上传体检证明、成绩证书并填写摆渡信息，感谢您的参与！【马拉松组委会】';
			}
			else{
				$msg_body='尊敬的'.$order_info['realname'].'，感谢您报名'.$cat_name.'项目，您的报名信息已经提交，请耐心等待组委会抽签！抽签结果将于8月14日10:00发布，请及时通过官网首页“状态查询”功能查看是否中签，感谢您的参与！【马拉松组委会】';
			}
			$result_email=$this->think_send_mail($to, $name, $subject, $msg_body);
        	}
        	
        	//短信通知
        	if($this->open_sms_msg==1){
        	//$msg_body='尊敬的'.$order_info['realname'].'，感谢您参与2017年马拉松赛，您的报名已提交，请等待组委会审核和抽签！ 请在抽签结束后到报名查询菜单里查看是否中签，感谢您的参与！';
        	if(!empty($invit_data['id'])){
				$msg_body='尊敬的'.$order_info['realname'].'，感谢您报名'.$cat_name.'项目，您的报名信息已经提交，请于8月14日10:00上传体检证明、成绩证书并填写摆渡信息，感谢您的参与！【马拉松组委会】';
			}
			else{
				$msg_body='尊敬的'.$order_info['realname'].'，感谢您报名'.$cat_name.'项目，您的报名信息已经提交，请耐心等待组委会抽签！抽签结果将于8月14日10:00发布，请及时通过官网首页“状态查询”功能查看是否中签，感谢您的参与！【马拉松组委会】';
			}
			header("Content-type:text/html; charset=UTF-8");
			require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
			$clapi  = new ChuanglanSmsApi();
			$code = mt_rand(100000,999999);
			$result_sms = $clapi->sendSMS($order_info['mobile'], $msg_body);
			//echo "<pre>";print_r($result_sms);exit;
        	}
        	*/
        	
        	/*
        	$status_apply=0;
        	//公益跑不抽签，直接设为中签
        	if($order_info['cat_id']==4 || $order_info['cat_id']==5 ||  !empty($invit_data['id']) ){
        		
        		$status_apply=1;
        		
        	$orderMod = M('order');
		    $sql=sprintf("update %s SET status_apply='1' 
		    where id='".addslashes($order_id)."' 
		    ", $orderMod->getTableName() );
		    //echo $sql;exit;
		    $result = $orderMod->execute($sql);
		    
        	}
        	*/
	    
        $return = array(
            'order_id' => $order_id,
        //    'status_apply' => $status_apply,
            'cat_id' => $order_info['cat_id'],
     //       'bus_point' => $order_info['bus_point'],
        );
		$return['success']='success';
		//echo "<pre>";print_r($return);exit;
		
        echo json_encode($return);
        exit;
        
	}
	
	
	
	//报名申请  结果页  http://cdmalasong.loc/baoming/apply_orient_finish?order_id=2385
	public function apply_orient_finish(){
		
             
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
			$order_id=$_REQUEST['order_id'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
        	
	   //	 $url=U('baoming/order_search_finish', array('order_id'=>$order_id ));
		//redirect($url);
		//exit;
		
		
		$order_info=$this->verify_body_orient($order_id);
		//echo "<pre>";print_r($order_info);exit;
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		
		
	    
    	$this->assign('curmenu', '7');
        $this->display('apply_orient_finish');
    }
	
	
	
	//领物通知函 http://cdmalasong.loc/baoming/ling_wu_loadsign?order_id=48838
	public function ling_wu_loadsign(){
		
		
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
		
		
		/*
		$div_pic='';
		$api_url='http://ems.irunner.mobi/api/getrunner?identity='.$order_info['id_number'];
		//echo $api_url;exit;
		$api_para=array();
		//echo $api_url;echo "<br>";
		//echo "<pre>";print_r($api_para);echo "</pre>";
		//exit;
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//var_dump($api_result);
		//echo "<pre>";print_r($api_result);exit;
		if(isset($api_result['data']['runner_id']) && !empty($api_result['data']['runner_id'])){
			$runner_id=$api_result['data']['runner_id'];
			$div_pic='<img src="http://ems.irunner.mobi/quer12345678/race/client/regquery?runner_id='.$runner_id.'&inajax=1">';
		}
		
		
		
		
    	   $this->assign('div_pic', $div_pic);
    	   */
    	   
    	   
    	   
    	   
    	   
    	   //直接调签名成品图片api接口做法：
    	   $identity=$order_info['id_number'];
		//$api_url='http://ems.irunner.mobi/api/showpic?identity='.$identity;  //cdm
		$api_url='http://ems.irunner.mobi/picapi/showpic?_api_token=xIop-jK827dxy*1&race_id=878&identity='.$identity;  //taizhou
		//	echo $api_url;exit;
		//echo $api_url;exit;
		//$api_para=array();
		//$api_para['identity']=$identity;
		//echo $api_url;echo "<br>";
		//echo "<pre>";print_r($api_para);echo "</pre>";
		//exit;
		//$api_result=$this->http_request_url_get($api_url,$api_para);
		//echo $api_result;exit;
		//var_dump($api_result);
		//echo "<pre>";print_r($api_result);exit;
		
		
		/*
		//把默认背景图保存到本地的做法：
    	$game_id=1;
    	$style=1;
    	$user_id=0;
        $is_android=1;
        
        $path = $identity."_sign.jpg";
		//$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".rand(10,99).".png";
		//$path = $identity."_time_".date('ymdHis').".png";
		//$path = $identity."_time_".date('ymdHis').".jpg";
		
		//$path='game_1_style_1_time_150716161543_1.png';
		$dest = UPLOAD_SIGN_PATH.$path;
		//echo $dest;exit;
		
		
		//echo $_REQUEST['sign_pic'];exit;
		//$_POST['filestring']=$api_result;
		
	      if(!file_exists($dest)){
			$f = fopen($dest,'w');
			fwrite($f,file_get_contents($api_url));
			fclose($f);
			//exit;
		}
		
		
		$pic_url=SIGN_UPLOAD_URI.'/'.$path;
		*/
		
		//直接引用接口方背景图做法：
		$pic_url=$api_url;
		$this->assign('pic_url', $pic_url);
		//echo $pic_url;exit;
		
		
			
        $this->display('ling_wu_loadsign');
	}
	
	
	//领物通知函  生成图片 http://cdmalasong.loc/baoming/ling_wu_showsign/identity/31022119741219601X/order_id/2454
	public function ling_wu_showsign(){
		
		
    		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])){
		    $order_id=$_REQUEST['order_id'];
		    $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}	
		
		
		
			if(isset($_REQUEST['identity']) && !empty($_REQUEST['identity'])){
		    $identity=$_REQUEST['identity'];
		   // $this->assign('order_id', $order_id);
		}
		else{
		    exit;
		}	
		
		//$identity='31022119741219601X';
		//echo $identity;exit;
		
		
		$order_info=$this->verify_body($order_id);
		//echo "<pre>";print_r($order_info);exit;
		if($order_info==false){
		    $return['success']='验证失败';
	        echo json_encode($return);
	        exit;
		}
		$this->assign('order_info', $order_info);
		
		
		if($identity!=$order_info['id_number']){
		$return['success']='验证失败';
		echo $return['success'];exit;
		}
		
		
		//$api_url='http://ems.irunner.mobi/api/showpic?identity='.$identity;  //cdm
		$api_url='http://ems.irunner.mobi/picapi/showpic?_api_token=xIop-jK827dxy*1&race_id=878&identity='.$identity;  //taizhou
		//echo $api_url;exit;
		//$api_para=array();
		//$api_para['identity']=$identity;
		//echo $api_url;echo "<br>";
		//echo "<pre>";print_r($api_para);echo "</pre>";
		//exit;
		//$api_result=$this->http_request_url_get($api_url,$api_para);
		//echo $api_result;exit;
		//var_dump($api_result);
		//echo "<pre>";print_r($api_result);exit;
		
		
			/*
		//把默认背景图保存到本地的做法：
    	$game_id=1;
    	$style=1;
    	$user_id=0;
        $is_android=1;
        
        $path = $identity."_sign.jpg";
		//$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".rand(10,99).".png";
		//$path = $identity."_time_".date('ymdHis').".png";
		//$path = $identity."_time_".date('ymdHis').".jpg";
		
		//$path='game_1_style_1_time_150716161543_1.png';
		$dest = UPLOAD_SIGN_PATH.$path;
		//echo $dest;exit;
		
		//echo $_REQUEST['sign_pic'];exit;
		//$_POST['filestring']=$api_result;
		
		$f = fopen($dest,'w');
		fwrite($f,file_get_contents($api_url));
		fclose($f);
		//exit;
		
		
		$pic_url=SIGN_UPLOAD_URI.'/'.$path;
		*/
		
		//直接引用接口方背景图做法：
		$pic_url=$api_url;
		$this->assign('pic_url', $pic_url);
    	
        $this->display('ling_wu_showsign');
	}
	
	
	
	//下载报名确认函 http://cdmalasong.loc/baoming/order_search_paper?order_id=48838
	public function order_search_paper(){
		
		
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
		
		
		$div_pic='';
		$api_url='http://ems.irunner.mobi/api/getrunner?identity='.$order_info['id_number'];
		//echo $api_url;exit;
		$api_para=array();
		//echo $api_url;echo "<br>";
		//echo "<pre>";print_r($api_para);echo "</pre>";
		//exit;
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//var_dump($api_result);
		//echo "<pre>";print_r($api_result);exit;
		if(isset($api_result['data']['runner_id']) && !empty($api_result['data']['runner_id'])){
			$runner_id=$api_result['data']['runner_id'];
			$div_pic='<img src="http://ems.irunner.mobi/quer12345678/race/client/regquery?runner_id='.$runner_id.'&inajax=1">';
		}
		
		
		
		
    	   $this->assign('div_pic', $div_pic);
        $this->display('order_search_paper');
	}
	

}
?>