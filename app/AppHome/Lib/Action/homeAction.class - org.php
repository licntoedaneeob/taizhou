<?php
class homeAction extends TAction
{
	
	/*
	行为发生后触发 发消息 
	https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140549&token=&lang=zh_CN
	客服接口-发消息
	
	
	*/
	
	public function test(){
		
		if(isset($_REQUEST['debug'])){
			$debug=$_REQUEST['debug'];
		}
		else{
			$debug='';
		}
		
		echo $debug;
		echo "home page";
		exit;
	}
	
	//以下为所有项目通用
	
	//微信接口调试（含媒体文件上传等）：http://mp.weixin.qq.com/debug
	
	
	//授权登陆  入口    http://lkkfood.loc/cms/home/index
	public function index(){
		
		if(isset($_REQUEST['debug'])){
			$debug=$_REQUEST['debug'];
		}
		else{
			$debug='';
		}
		
		echo $debug;
		echo "home page";
		exit;
		
		//exit;
		
		/*
		if(isset($_REQUEST['game_2_id'])){
			$game_2_id=$_REQUEST['game_2_id'];
		}
		else{
			$game_2_id='';
		}
		
		
		$url='/game2/?game_2_id='.$game_2_id;
		*/
		
		$url='/';
		
		$goto=$url;
		
		
		$openid=$this->get_openid($goto);
		
		//var_dump($openid);exit;
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        //发送客服消息
        //$msg_data['touser']='openid123';
        //$msg_data['msgtype']='text';
        //$msg_data['text']['content']=urlencode('消息内容123');
        //$this->sendCustomMessage($msg_data);
		
		
		
		//redirect($url);
		//exit;
		
		$this->display('index');
		
		
	}
	
	
	
	//获得微信签名 http://ririzhu.loc/cms/home/weixin_sign_ajax?url=http://www.baidu.com
	public function weixin_sign_ajax(){
		
		//默认并建议：url参数不要传过来，php自动获取服务器的url即可。
		if(isset($_REQUEST['url']) && $_REQUEST['url']!=''){
            $url=$_REQUEST['url'];
        }
        else{
            $url='';
        }
        
		$signPackage = $this->getSignPackage($url);
		$data=$signPackage;
		$this->jsonData(0,'成功',$data);
        exit;
        
	}
	
	
	
	//授权登陆  查看当前token，获得媒体文件等接口要用到此token    http://lkkfood.loc/cms/home/index_token
	public function index_token(){
		
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        //var_dump($openid);exit;
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
        $access_token=$this->getAccessToken();
		echo $access_token;exit;
		
		
	}
	
	
	
	
	//验证当前是否有openid并获取openid
	public function get_openid($goto=""){
		
		//echo "<pre>";print_r($_SESSION);exit;
		
    	if(isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
            $openid=$_SESSION['WX_INFO']['openid'];
        }
        else{
        	
			if( (isset($_REQUEST['debug']) && $_REQUEST['debug']==1) || ($_SERVER["HTTP_HOST"]=='inshop.loc') ){
				//调试html、css的模式
	        }
	        else{
	        	$url=U('game/oauth_authorize')."?goto=".$goto;
		        redirect($url);
	        }
	        
        }
        
        //测试从朋友圈进来 http://huijiayou.loc/home/cut?uid=1698
        if( (isset($_REQUEST['debug']) && $_REQUEST['debug']==1) || ($_SERVER["HTTP_HOST"]=='inshop.loc') ){
			$openid='abc001';   //user_id=1697
			//$openid='abc002';    //user_id=1699
			//$openid='o08-yuBVHgnyVXAGYI1vIU5gF1NQ';    //user_id=1728
        }
        
        
        //$CityMod = M('user');
	    //$user_data = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
        //echo "<pre>";print_r($user_data);echo "</pre>";exit;
        
        
        //有可能后台删了这个openid的用户，而SESSION里的WX_INFO还没到7200秒，所以这种情况下，会出现不会重新创建用户的bug，故改之。
        //gameAction.class.php里的这个地方也要改
        if(isset($_SESSION['WX_INFO']['access_token']) && isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
			
			$openid=$_SESSION['WX_INFO']['openid'];
			
			//echo "aa<pre>";print_r($_SESSION['WX_INFO']);echo "</pre>";
			
			
			//拿微信用户信息
			$get_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$_SESSION['WX_INFO']['access_token'].'&openid='.$_SESSION['WX_INFO']['openid'].'&lang=zh_CN';
			$get_return = file_get_contents($get_url);
			$userinfo = (array)json_decode($get_return);
			
			//echo "bb<pre>";print_r($userinfo);echo "</pre>";
		
		
			$CityMod = M('user');
	        $user_data = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
	        if(!empty($user_data)){
	        	
	        	$user_data=$user_data[0];
	        	
	        	//更新
	        	//前台判断，如果当前openid（当前用户）数据库里没昵称，且微信api拿到的昵称不为空，就更新昵称字段， 否则其他情况前台代码逻辑里都不更新昵称字段。
	        	if($user_data['nickname']=='' && $userinfo['nickname']!=''){
	        		
					$UserMod = M('user');
			        $sql=sprintf("UPDATE %s SET nickname='".addslashes($userinfo['nickname'])."' 
			        , sex='".addslashes($userinfo['sex'])."' 
			        , province='".addslashes($userinfo['province'])."' 
			        , city='".addslashes($userinfo['city'])."' 
			        , country='".addslashes($userinfo['country'])."' 
			        , headimgurl='".addslashes($userinfo['headimgurl'])."' 
			         where openid='".addslashes($openid)."' ", $UserMod->getTableName() );
			        //echo $sql;
			        $result = $UserMod->execute($sql);
			        
		        }
		        
	        }
	        else{
	        	//新增
		        $UserMod = M('user');
		        $sql=sprintf("INSERT %s SET openid='".$openid."' 
		        , nickname='".addslashes($userinfo['nickname'])."' 
		        , sex='".addslashes($userinfo['sex'])."' 
		        , province='".addslashes($userinfo['province'])."' 
		        , city='".addslashes($userinfo['city'])."' 
		        , country='".addslashes($userinfo['country'])."' 
		        , headimgurl='".addslashes($userinfo['headimgurl'])."' 
		        , create_time='".time()."' 
		        ", $UserMod->getTableName() );
		        //echo $sql;
		        $result = $UserMod->execute($sql);
	        }
	        
	        
	        
	        $CityMod = M('user');
	        $user_data = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
	        if(isset($user_data[0]['username']) && $user_data[0]['username']!=''){
	        }
	        else{
		    	//没注册过，需要更新fuid。注册过，则不用更新fuid
		    	if(isset($_SESSION['fuid']) && !empty($_SESSION['fuid']) ){
		    		//记录上家user_id
		    		$mobileMod = M('user');
					$sql=sprintf("UPDATE %s SET fuid='".addslashes($_SESSION['fuid'])."' 
			        where openid='".addslashes($openid)."' 
			        ", $mobileMod->getTableName() );
			        $mobileMod->execute($sql);
		    	}
	        }
	        
	        
		}
		
		
        
        return $openid;
	}
	
	
	
	
	
	//获得openid
	public function get_openid_ajax(){
		
		//echo "<pre>";print_r($_SESSION);exit;
		
    	if(isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
            $openid=$_SESSION['WX_INFO']['openid'];
        }
        else{
        	
			if( (isset($_REQUEST['debug']) && $_REQUEST['debug']==1) || ($_SERVER["HTTP_HOST"]=='inshop.loc') ){
				//调试html、css的模式
				$openid='';
	        }
	        else{
	        	$openid='';
	        }
	        
        }
        
        
        
        //测试从朋友圈进来 http://huijiayou.loc/home/cut?uid=1698
        if(empty($openid)){
	        if( (isset($_REQUEST['debug']) && $_REQUEST['debug']==1) || ($_SERVER["HTTP_HOST"]=='inshop.loc') ){
				$openid='abc001';   //user_id=1697
				//$openid='abc002';    //user_id=1699
				//$openid='o08-yuBVHgnyVXAGYI1vIU5gF1NQ';    //user_id=1728
	        }
        }
        
        return $openid;
	}
	
	
	//通过openid那user_id及用户基本信息
	public function get_userinfo($openid=0){
		
		$andsql=" and openid='".addslashes($openid)."' ";
    	$CityMod = M('user');
        $rst = $CityMod->where(" 1 ".$andsql." ")->select();
        if(isset($rst[0])){
        	$userinfo=$rst[0];
        	
        	
        	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
	            $userinfo=$_SESSION['userinfo'];
	        }
        	
        	
        }
        else{
        	$userinfo=array();
        }
        
        return $userinfo;
	}
	
	
	
	//以上为所有项目通用
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//以下为逗咖大力项目
	
	
	//记录开始游戏  http://inshop.loc/cms/home/douka_dali_game1start
	public function douka_dali_game1start(){
		
		//$_SESSION['game_id']=2; //指定game_id操作数据。
		
		if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
		    $game_id=$_SESSION['game_id'];
		}
		else{
		    $cli_os=$this->get_client_os();
	        $cur_time=time();
	        
	        $UserMod = M('game');
	        $UserMod->cli_os=$cli_os;
	        $UserMod->modify_time=$cur_time;
	        $UserMod->create_time=$cur_time;
	        $UserMod->addtime=date("Y-m-d H:i:s",$cur_time);
	        $game_id = $UserMod->add();
	        
	        $_SESSION['game_id']=$game_id;
		}
		
        if($game_id>0){
        	return $game_id;
            //$this->jsonData(0,'成功',$data);
            //exit;
        }
        else{
            return 0;
            //$this->jsonData(1,'失败');
            //exit;
        }
        
	}
	
	
	//记录结束游戏（提交个人信息就算结束）  http://inshop.loc/cms/home/douka_dali_game1end
	public function douka_dali_game1end(){
		
		if(isset($_SESSION['game_id'])){
		    unset($_SESSION['game_id']);
		}
		
        return true;
        
	}
	
	
	
	
	//上传原始头像 提交  http://inshop.loc/cms/home/douka_dali_headpic_wx?media_id=yyyyyyy&type=png
    public function douka_dali_headpic_wx(){
    	
    	//$this->douka_dali_game1end();
    	
    	$game_id=$this->douka_dali_game1start();
    	//echo $game_id;exit;
    	
    	
    	//$style=1;
    	//$user_id=0;
    	//$is_android=1;
    	
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        //var_dump($openid);exit;
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
        
        if(isset($_REQUEST['media_id']) && $_REQUEST['media_id']!=""){
            $media_id=$_REQUEST['media_id'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        //echo $media_id;exit;
        
        
		if(isset($_REQUEST['type']) && $_REQUEST['type']!=""){
            $type=$_REQUEST['type'];
        }
        else{
            $type="png";
        }
        
        //echo $type;exit;
        
        
        $access_token=$this->getAccessToken();
		//echo $access_token;exit;
		$date_ymdHis=date('ymdHis');
		
        if(!empty($access_token) && !empty($media_id)){
        	
        	
			
			/*
			//可以拿到图片的写法
			
			$path = "game_".$game_id."_wx_".$date_ymdHis."_".rand(10,99)."_userid_".$user_id.".".$type;
			$dest = BASE_WX_HEADPIC_PATH.$path;
			//echo $dest;exit;
			
			$get_url='http://img01.sogoucdn.com/v2/thumb/resize/w/120/h/80/zi/on/iw/90.0/ih/60.0?t=2&url=http%3A%2F%2Fwww.arkzy.com%2Fuploads%2Fimage%2F20161215%2F5bbdb3b0bdf4929b522ec0a7ee20b5a6.jpg&appid=200524&referer=http://www.arkzy.com/huuxc/';
			$get_return = file_get_contents($get_url);
			
			//模仿复制 php://input 模式上传
			$f = fopen($dest,'w');
			fwrite($f,$get_return);
			fclose($f);
			
			
			//获取图片并转为base64
			$file= $dest;
			$fp  = fopen($file, 'rb', 0);
			//$wx_headpic_base64=chunk_split(base64_encode(fread($fp,filesize($file))));
			$wx_headpic_base64=base64_encode(fread($fp,filesize($file)));
			fclose($fp);
			
			echo $wx_headpic_base64;exit;
			exit;
			*/
			
			
			
			$path = "game_".$game_id."_wx_".$date_ymdHis."_".rand(10,99)."_userid_".$user_id.".".$type;
			$dest = BASE_WX_HEADPIC_PATH.$path;
			//echo $dest;exit;
			
			
			//获取微信媒体文件
			$get_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$media_id.'';
			//echo $get_url;exit;
			$get_return = file_get_contents($get_url);
			//$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
			
			
			//var_dump($get_return);exit;
			if(!empty($get_return)){
				
				
				//模仿复制 php://input 模式上传
				$f = fopen($dest,'w');
				fwrite($f,$get_return);
				fclose($f);
				
				sleep(1);
				
				$cur_time=time();
				$UserMod = M('game');
		        $sql=sprintf("UPDATE %s SET wx_headpic_path='".addslashes($path)."' 
		        , wx_headpic_media_id='".addslashes($media_id)."' 
		        , openid='".addslashes($openid)."' 
		        , user_id='".addslashes($user_id)."' 
		        , wx_nickname='".addslashes($userinfo['nickname'])."' 
		        , wx_headimgurl='".addslashes($userinfo['headimgurl'])."' 
		        where id='".addslashes($game_id)."' 
		        ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
		        
		        //获取图片并转为base64
				$file= $dest;
				$fp  = fopen($file, 'rb', 0);
				//$wx_headpic_base64=chunk_split(base64_encode(fread($fp,filesize($file))));
				$wx_headpic_base64=base64_encode(fread($fp,filesize($file)));
				fclose($fp);
				
		        
		        
				$data['game_id']=$game_id;
				$data['media_id']=$media_id;
				$data['headpic']=BASE_URL."/public/wx_headpic/".$path;
				$data['headpic_base64']=$wx_headpic_base64;
				$this->jsonData(0,'成功',$data);
	        }
	        else{
	        	$this->jsonData(1,'媒体文件获取失败');
	        	exit;
	        }
	        
        	
        }
        else{
        	$this->jsonData(1,'参数错误');
            exit;
        }
        
        
    }
    
    
    
    //获取假分数
	//http://inshop.loc/cms/home/douka_dali_get_score_jia
	public function douka_dali_get_score_jia(){
		
		
		
    	$game_id=$this->douka_dali_game1start();
    	//echo $game_id;exit;
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        //var_dump($openid);exit;
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		//echo $user_id;exit;
		//echo $game_id;exit;
		
		
		//$cur_time=time();
        //$addtime=date("Y-m-d H:i:s",$cur_time);
        //$time_start=date("Y-m-d",$cur_time)." 00:00:00";
        //$time_end=date("Y-m-d",$cur_time)." 23:59:59";
        //$customer_ip=$this->get_customer_ip();
        
		//$CityMod = M('game');
        //$number_today = $CityMod->field('sum(ticket_num) as num ')->where(" addtime>='".$time_start."' and addtime<='".$time_end."' and user_id='".addslashes($user_id)."' " )->select();
        //$number_today=isset($number_today[0]['num'])?$number_today[0]['num']:0;
        //echo $number_today;exit;
		//if($number_today>=3){
		//	$this->jsonData(1,'每天抽奖最多'.$chou_limit.'次');
		//    exit;
		//}
		
		
        
        
        //抽奖
        $is_prize=0;       //0代表没中奖，1-59分。
        $all_num[1]=1300;  //60-69分，大力士奖，当前奖品可中奖总数
        $all_num[2]=120;   //80-89分，季军，当前奖品可中奖总数
        $all_num[3]=150;   //70-79分，幸运奖，当前奖品可中奖总数
        $all_num[4]=100;   //90-99分，亚军，当前奖品可中奖总数
		
        $score_jia[0]=50;  //0代表没中奖，1-59分。
        $score_jia[1]=60;  //60-69分，大力士奖，当前奖品可中奖总数
        $score_jia[2]=80;  //80-89分，季军，当前奖品可中奖总数
        $score_jia[3]=70;  //70-79分，幸运奖，当前奖品可中奖总数
        $score_jia[4]=90;  //90-99分，亚军，当前奖品可中奖总数
        
        $is_prize_yes=1;
        $rand_num=rand(1, 3);   // 40%概率中奖。1~3里面命中1就算中奖。然后去$is_prize_key里随便挑一个没满额的奖品。
        
    	$is_prize_key=rand(1, 4);   //rand_num等于is_prize_key的时候，为中奖。  1为60-69分，2为80-89分，3为70-79分，4为90-99分
        
        //echo $is_prize_yes;echo "<br>";
        //echo $rand_num;echo "<br>";
        //echo $is_prize_key;echo "<br>";
        //exit;;
        
        
        
        
        
        
        //先按没中奖的情况对数据做初始化
        $score_jia_db=rand(1, 59);
        
		$CityMod = M('game');
        $game_info = $CityMod->field('count(id) as num ')->where(" score_jia<".$score_jia_db." " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        //echo "<pre>";print_r($game_info);exit;
        $low_num=!empty($game_info['num'])?$game_info['num']:0;
        //echo $low_num;exit;
        
        $CityMod = M('game');
        $game_info = $CityMod->field('count(id) as num ')->where(" 1 " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        $total_num=!empty($game_info['num'])?$game_info['num']:0;
        //echo $total_num;exit;
        
        if($total_num>0){
        	$percent=round($low_num/$total_num*100);
        }
        else{
        	$percent=100;
        }
        //var_dump($percent);exit;
        $percent_db=$percent;
        
        
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET is_prize='".$is_prize."' 
         , score_jia='".$score_jia_db."' 
            , percent='".$percent_db."' 
        	, openid='".addslashes($openid)."' 
	        , user_id='".addslashes($user_id)."' 
	        , wx_nickname='".addslashes($userinfo['nickname'])."' 
	        , wx_headimgurl='".addslashes($userinfo['headimgurl'])."' 
	    
        where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
        //echo $sql;
        $result = $UserMod->execute($sql);
        
        
        
        
        if($rand_num==$is_prize_yes){
        	$CityMod = M('game');
	        $data_num = $CityMod->field('count(id) as num ')->where(" is_prize='".$is_prize_key."' " )->select();
	        $data_num=$data_num[0]['num'];
	        
	        //当前中奖人数小于可中奖总数
	        if($data_num<$all_num[$is_prize_key]){
	        	
	        	$CityMod = M('game');
		        $person_zhong = $CityMod->field('count(id) as num ')->where(" is_prize>0 and user_id='".addslashes($user_id)."' " )->select();
		        $person_zhong=$person_zhong[0]['num'];
		        //echo $person_zhong;echo "<br>";
		        
		        //当前手机没中过奖
		        if($person_zhong==0){
		        	
		        	$is_prize=$is_prize_key;
		        	
		        	if($is_prize==1){
		        		$score_jia_db=rand(60, 69);
		        	}
		        	elseif($is_prize==2){
		        		$score_jia_db=rand(80, 89);
		        	}
		        	elseif($is_prize==3){
		        		$score_jia_db=rand(70, 79);
		        	}
		        	elseif($is_prize==4){
		        		$score_jia_db=rand(90, 99);
		        	}
		        	else{
		        		exit;
		        	}
		        	
		        	
					$CityMod = M('game');
			        $game_info = $CityMod->field('count(id) as num ')->where(" score_jia<".$score_jia_db." " )->select();
			        $game_info = !empty($game_info)?$game_info[0]:array();
			        //echo "<pre>";print_r($game_info);exit;
			        $low_num=!empty($game_info['num'])?$game_info['num']:0;
			        //echo $low_num;exit;
		        	
		        	
			        $CityMod = M('game');
			        $game_info = $CityMod->field('count(id) as num ')->where(" 1 " )->select();
			        $game_info = !empty($game_info)?$game_info[0]:array();
			        $total_num=!empty($game_info['num'])?$game_info['num']:0;
			        //echo $total_num;exit;
			        
		        	
			        if($total_num>0){
			        	$percent=round($low_num/$total_num*100);
			        }
			        else{
			        	$percent=100;
			        }
			        //var_dump($percent);exit;
			        $percent_db=$percent;
			        
			        
		        	
		        	
		        	$UserMod = M('game');
			        $sql=sprintf("UPDATE %s SET is_prize='".$is_prize."' 
			         , score_jia='".$score_jia_db."' 
			         , percent='".$percent_db."' 
			        where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
			        //echo $sql;
			        $result = $UserMod->execute($sql);
			        
		        }
		        
	        }
	        
        }
        
        $data['game_id']=$game_id;
        $data['score_jia']=$score_jia_db;
        $data['percent']=$percent_db;
        $data['is_prize']=$is_prize;
		$this->jsonData(0,'成功',$data);
        
	}
	
	
	
	//提交个人信息
	//http://inshop.loc/cms/home/douka_dali_userinfo?realname=小明&mobile=13988887777&address=南京路100号&score=33&percent=90&filestring=图片base64内容
	public function douka_dali_userinfo(){
		
		//调试写入图片时开启
		//$_POST['filestring']='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';
		
		//$user_id=0;
		
		
    	$game_id=$this->douka_dali_game1start();
    	//echo $game_id;exit;
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        //var_dump($openid);exit;
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		
		
		
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=''){
		    $realname=$_REQUEST['realname'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile']!=''){
		    $mobile=$_REQUEST['mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['address']) && $_REQUEST['address']!=''){
		    $address=$_REQUEST['address'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
        //分数
        if(isset($_REQUEST['score']) && $_REQUEST['score']!=''){
		    $score=$_REQUEST['score'];
		}
		else{
			$score='';
		}
		
		//击败百分比
		if(isset($_REQUEST['percent']) && $_REQUEST['percent']!=''){
		    $percent=$_REQUEST['percent'];
		}
		else{
			$percent='';
		}
		
        
		
		/*
		if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
		    $openid=$_REQUEST['openid'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
		
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=''){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		
		if(isset($_REQUEST['wx_headimgurl']) && $_REQUEST['wx_headimgurl']!=''){
		    $wx_headimgurl=$_REQUEST['wx_headimgurl'];
		}
		else{
			$wx_headimgurl='';
		}
		
		*/
		
		
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    $addtime=date("Y-m-d H:i:s",$cur_time);
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        
        $UserMod = M('game');
        $sql=sprintf("UPDATE %s SET openid='".addslashes($openid)."' 
        , user_id='".addslashes($user_id)."' 
        , wx_nickname='".addslashes($userinfo['nickname'])."' 
        , wx_headimgurl='".addslashes($userinfo['headimgurl'])."' 
        , realname='".addslashes($realname)."' 
        , mobile='".addslashes($mobile)."' 
        , address='".addslashes($address)."' 
        where id='".addslashes($game_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
		/*
        $UserMod = M('game');
        $UserMod->user_id=$user_id;
        $UserMod->modify_time=$cur_time;
        $UserMod->create_time=$cur_time;
        $UserMod->addtime=$addtime;
        $UserMod->realname=$realname;
        $UserMod->mobile=$mobile;
        $UserMod->address=$address;
        //$UserMod->openid=$openid;
        //$UserMod->wx_nickname=$wx_nickname;
        //$UserMod->wx_headimgurl=$wx_headimgurl;
        //$UserMod->user_id=$user_id;
        //$UserMod->wx_nickname=$userinfo['nickname'];
        //$UserMod->wx_headimgurl=$userinfo['headimgurl'];
        $game_id = $UserMod->add();
        */
        
        
        
        
        //保存百分比和分数
		//$UserMod = M('game');
        //$sql=sprintf("UPDATE %s SET percent='".addslashes($percent)."' 
        //, score='".addslashes($score)."' 
        //where id='".addslashes($game_id)."' 
        //", $UserMod->getTableName() );
        //$result = $UserMod->execute($sql);
        
        
        $UserMod = M('game');
        $sql=sprintf("UPDATE %s SET 
         score='".addslashes($score)."' 
        where id='".addslashes($game_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
        
        
        
        //传图片部分：
        
        if(isset($_REQUEST['is_android']) && $_REQUEST['is_android']==1){
            $is_android=$_REQUEST['is_android'];
        }
        else{
            $is_android=0;
        }
        
        
        
        $is_android=1;
        
		//$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".rand(10,99).".png";
		//$path = "game_".$game_id."_headpic_".$style."_time_".date('ymdHis')."_".$user_id.".png";
		$path = "game_".$game_id."_time_".date('ymdHis')."_".rand(10,99).".png";
		
		
		//$path='game_1_style_1_time_150716161543_1.png';
		$dest = BASE_UPLOAD_HEADPIC_PATH.$path;
		//echo $dest;exit;
		
        
        
		/*
		//base64模式接受上传数据
		$f = fopen($dest,'w');
		$img = 'data:image/jpeg;base64,/9j/4QAYRXhhtcHRrPXYv//Z';
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace('data:image/gif;base64,', '', $img);
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$datas = base64_decode($img);
		fwrite($f,$datas);
		fclose($f);
		*/
		
		
		
		$pic_imagerotate='';
		$upload_method='';
		
		if($_FILES){
			$upload_method='FILES';
			
			//上传方式：表单
			$input = key($_FILES);
			if(!move_uploaded_file($_FILES[$input]['tmp_name'],$dest)) {
				$this->jsonData(1,'失败');
		        exit;
			}
			else{
				
				//判断EXIF头信息模式解决旋转90度问题
				$exif = exif_read_data($dest);
				$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
				if($ort==""){
					$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
				}
				switch($ort){
					case 1: // nothing
						break;
        			case 2: // horizontal flip
        				break;
	                case 3: // 180 rotate left  //向左旋转180度
	                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
	                    $pic_imagerotate='180';
	                    break;
	                case 4: // vertical flip
            			break;
            		case 5: // vertical flip + 90 rotate right
				        $pic_imagerotate='-90';
				        break;
	                case 6: // 90 rotate right  //向右旋转90度
	                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
	                    $pic_imagerotate='-90';
	                    break;
	                case 7: // horizontal flip + 90 rotate right
				        $pic_imagerotate='-90';
				        break;
	                case 8:    // 90 rotate left  //向左旋转90度
	                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
	                    $pic_imagerotate='90';
	                    break;
	            }
	            //echo "<pre>";print_r($exif);exit;
	            //var_dump($pic_imagerotate);exit;
			}
		}
		elseif(isset($_POST['filestring'])){ 
			
			$upload_method='filestring';
			
			//上传方式：原始POST
			//$f = fopen($dest,'w');
			//fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = $GLOBALS['HTTP_RAW_POST_DATA'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = $_POST['filestring'];
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题（示例：http://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation）
				$f = fopen($dest,'w');
				fwrite($f,$_POST['filestring']);
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
            
			
			
		}
		elseif(isset($GLOBALS['HTTP_RAW_POST_DATA'])){ 
			
			$upload_method='HTTP_RAW_POST_DATA';
			
			//上传方式：原始POST
			//$f = fopen($dest,'w');
			//fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = $GLOBALS['HTTP_RAW_POST_DATA'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = $GLOBALS['HTTP_RAW_POST_DATA'];
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题（示例：http://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation）
				$f = fopen($dest,'w');
				fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
            
			
			
		}
		else{
			 
			$upload_method='php_input';
			
			//上传方式：客户端提交
			//$f = fopen($dest,'w');
			//fwrite($f,file_get_contents('php://input'));
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = file_get_contents('php://input');
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = file_get_contents('php://input');
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题
				$f = fopen($dest,'w');
				fwrite($f,file_get_contents('php://input'));
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
			
			
			/*
			$upload_method='php_input_2';
			if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
			  $type = $result[2];
			  $new_file = "./test.{$type}";
			  if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
			    echo '新文件保存成功：', $new_file;
			  }
			}
			*/
			
			/*
			$upload_method='php_input_2';
			if (preg_match('/^(data:\s*image\/(\w+);base64,)/', file_get_contents('php://input'), $result)){
			  $type = $result[2];
			  //$new_file = "./test.{$type}";
			  $new_file = $dest;
			  if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', file_get_contents('php://input'))))){
			    //echo '新文件保存成功：', $new_file;
			    $upload_method='php_input_3';
			  }
			}
			*/
			
			
		}
		
		
		//echo $exif['Orientation'];exit;
		//echo "<pre>";print_r($exif);exit;
		//$dest='D:\green\054.jpg';
		//echo $dest;exit;
		
		
		$verify_pic=0;  //1验证图片类型，0不验证图片类型
		if($verify_pic==1){
			$file_type=$this->get_file_type($dest); 
			//$file_type='png';  //开启调试，通过图片类型验证
			//echo $file_type;exit;
			if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png"){
				@unlink($dest);
				$this->jsonData(1,'图片上传限定jpg、gif、png类型');
			    exit;
			}
		}
		
		
		$file_z=filesize($dest);
		$f_size_limit_byte=BASE_FILE_SIZE_LIMIT*1024*1024;
		
		if ($file_z>$f_size_limit_byte){
			@unlink($dest);
			$this->jsonData(1,'图片上传限定'.BASE_FILE_SIZE_LIMIT.'M');
		    exit;
		}
		
		
		/*
		//裁切
		$path_egg = "game_".$game_id."_egg_".$style."_time_".date('ymdHis')."_".$user_id.".png";
		$dest_egg = BASE_PIC_RESIZE_PATH.$path_egg;
		$out_path=$dest_egg;
		$org_path=$dest;
    	$src_w=540;
    	$src_h=588;
    	$this->zoom($org_path,$out_path,$src_w,$src_h);	
		//裁切
		*/
		
        
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET headpic='".addslashes($path)."' 
        , imagerotate='".addslashes($pic_imagerotate)."' 
        , upload_method='".addslashes($upload_method)."' 
        where id='".addslashes($game_id)."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
        
        
        /*
    	$UserMod = M('game');
        $sql=sprintf("INSERT %s SET user_id='".addslashes($user_id)."' 
        , modify_time='".$cur_time."' 
        , create_time='".$cur_time."' 
        , addtime='".$addtime."' 
        , realname='".addslashes($realname)."' 
        , mobile='".addslashes($mobile)."' 
        , address='".addslashes($address)."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        */
        
       // headpic='".addslashes($path)."' 
       // , imagerotate='".addslashes($pic_imagerotate)."' 
        	
        	
        
        
        
        
        
        //整个游戏流程结束的时候调用
        $this->douka_dali_game1end();
        
        
        $data['game_id']=$game_id;
        //$data['user_id']=$user_id;
		$data['realname']=$realname;
		$data['mobile']=$mobile;
		$data['address']=$address;
		$data['score']=$score;
		$data['percent']=$percent;
		$data['headpic_url']=BASE_URL."/public/web_headpic/".$path;
        $this->jsonData(0,'成功',$data);
        
        
        
	}
	
	
	
	
	//提交祝福语
	//http://inshop.loc/cms/home/douka_dali_get_userinfo_by_game_id?game_id=124
	public function douka_dali_get_userinfo_by_game_id(){
		
		//$user_id=0;
		
		/*
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		*/
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		
		
		if(isset($_REQUEST['game_id']) && $_REQUEST['game_id']>0){
		    $game_id=$_REQUEST['game_id'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		/*
		if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
		    $openid=$_REQUEST['openid'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
		
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=''){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		
		if(isset($_REQUEST['wx_headimgurl']) && $_REQUEST['wx_headimgurl']!=''){
		    $wx_headimgurl=$_REQUEST['wx_headimgurl'];
		}
		else{
			$wx_headimgurl='';
		}
		
		*/
		
		
		$CityMod = M('game');
        $game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        $game_info=isset($game_info[0])?$game_info[0]:array();
        //echo "<pre>";print_R($game_info);exit;
        
        
        $data['game_id']=$game_id;
        //$data['user_id']=$user_id;
		$data['realname']=$game_info['realname'];
		$data['mobile']=$game_info['mobile'];
		$data['address']=$game_info['address'];
		$data['score']=$game_info['score'];
		$data['percent']=$game_info['percent'];
		$data['headpic_url']=BASE_URL."/public/web_headpic/".$game_info['headpic'];
        $this->jsonData(0,'成功',$data);
        
        
        
	}
	
	
	public function douka_base64_demo(){
	
        //获取图片并转为base64
		$file= 'D:\www\douka_dali\cms\public\wx_headpic\game_126_wx_161221003059_73_userid_13342.png';
		$fp  = fopen($file, 'rb', 0);
		//$wx_headpic_base64=chunk_split(base64_encode(fread($fp,filesize($file))));
		$wx_headpic_base64=base64_encode(fread($fp,filesize($file)));
		fclose($fp);
		echo $wx_headpic_base64;exit;
	}	
	
	
	//实测成功
	public function douka_base64_demo2(){
		$base64_image_content='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';
		$dest='D:\www\douka_dali\cms\public\web_headpic\a.png';
		
		$f = fopen($dest,'w');
		$img = $base64_image_content;
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace('data:image/gif;base64,', '', $img);
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$datas = base64_decode($img);
		fwrite($f,$datas);
		fclose($f);
		exit;
		
	}
	
	//实测不成功
	public function douka_base64_demo3(){
		$base64_image_content='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';
		$dest='D:\www\douka_dali\cms\public\web_headpic\b.png';
		
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
		  $type = $result[2];
		  //$new_file = "./test.{$type}";
		  $new_file = $dest;
		  if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
		    echo 'success：', $new_file;
		    //$upload_method='php_input_3';
		  }
		}
		echo "test";
		echo $type;exit;
	}
	
	//以上为逗咖大力项目
	
	
	//以下为逗咖祝福语项目
	
	
	//提交祝福语
	//http://doukazhufu.loc/cms/home/douka_zhufu?realname=小明&title=祝福语
	public function douka_zhufu(){
		
		$user_id=0;
		
		/*
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		*/
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		
		
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=''){
		    $realname=$_REQUEST['realname'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		/*
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile']!=''){
		    $mobile=$_REQUEST['mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['address']) && $_REQUEST['address']!=''){
		    $address=$_REQUEST['address'];
		}
		else{
			//$this->jsonData(1,'参数错误');
		    //exit;
		}
        */
        
        
        
		if(isset($_REQUEST['title']) && $_REQUEST['title']!=''){
		    $title=$_REQUEST['title'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
        
        
		
		/*
		if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
		    $openid=$_REQUEST['openid'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
		
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=''){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		
		if(isset($_REQUEST['wx_headimgurl']) && $_REQUEST['wx_headimgurl']!=''){
		    $wx_headimgurl=$_REQUEST['wx_headimgurl'];
		}
		else{
			$wx_headimgurl='';
		}
		
		*/
		
		
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    $addtime=date("Y-m-d H:i:s",$cur_time);
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        
    	$UserMod = M('game');
        $sql=sprintf("INSERT %s SET user_id='".addslashes($user_id)."' 
        , modify_time='".$cur_time."' 
        , create_time='".$cur_time."' 
        , addtime='".$addtime."' 
        , realname='".addslashes($realname)."' 
        , title='".addslashes($title)."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
        //$data['user_id']=$user_id;
		$data['realname']=$realname;
		$data['title']=$title;
        $this->jsonData(0,'成功',$data);
        
        
        
        
	}
	
	
	
	
	//提交祝福语
	//http://doukazhufu.loc/cms/home/douka_zhufu_userinfo?realname=小明&mobile=13988887777
	public function douka_zhufu_userinfo(){
		
		$user_id=0;
		
		/*
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		*/
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		
		
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=''){
		    $realname=$_REQUEST['realname'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile']!=''){
		    $mobile=$_REQUEST['mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		/*
		if(isset($_REQUEST['address']) && $_REQUEST['address']!=''){
		    $address=$_REQUEST['address'];
		}
		else{
			//$this->jsonData(1,'参数错误');
		    //exit;
		}
        
        
        
        
		if(isset($_REQUEST['title']) && $_REQUEST['title']!=''){
		    $title=$_REQUEST['title'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        */
        
        
        
		
		/*
		if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
		    $openid=$_REQUEST['openid'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
		
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=''){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		
		if(isset($_REQUEST['wx_headimgurl']) && $_REQUEST['wx_headimgurl']!=''){
		    $wx_headimgurl=$_REQUEST['wx_headimgurl'];
		}
		else{
			$wx_headimgurl='';
		}
		
		*/
		
		
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    $addtime=date("Y-m-d H:i:s",$cur_time);
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        
    	$UserMod = M('game_2');
        $sql=sprintf("INSERT %s SET user_id='".addslashes($user_id)."' 
        , modify_time='".$cur_time."' 
        , create_time='".$cur_time."' 
        , addtime='".$addtime."' 
        , realname='".addslashes($realname)."' 
        , mobile='".addslashes($mobile)."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
        //$data['user_id']=$user_id;
		$data['realname']=$realname;
		$data['mobile']=$mobile;
        $this->jsonData(0,'成功',$data);
        
        
        
        
	}
	
	
	//以上为逗咖祝福语项目
	
	
	//以下为李锦记-招牌菜项目
	
	
	//提交个人信息  提交抽奖申请
	//http://lkkfood.loc/cms/home/lkkfood_prize
	public function lkkfood_prize(){
		
		
		$openid=$this->get_openid_ajax();
		//echo $openid;exit;
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        //$chou_limit=3;  //每天抽奖最多3次。
        //$this->jsonData(1,'每天抽奖最多'.$chou_limit.'次');
		//exit;
		    
		    
		    
		    
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			$this->jsonData(1,'请通过微信授权参与活动');
		    exit;
		}
		
		
		
		
		
		//每天有三次抽奖机会
		$chou_limit=3;  //每天抽奖最多3次。
		
		$cur_time=time();
        $addtime=date("Y-m-d H:i:s",$cur_time);
        $time_start=date("Y-m-d",$cur_time)." 00:00:00";
        $time_end=date("Y-m-d",$cur_time)." 23:59:59";
        $customer_ip=$this->get_customer_ip();
        
		$CityMod = M('prize_history');
        $number_today = $CityMod->field('sum(ticket_num) as num ')->where(" addtime>='".$time_start."' and addtime<='".$time_end."' and user_id='".addslashes($user_id)."' " )->select();
        $number_today=isset($number_today[0]['num'])?$number_today[0]['num']:0;
        //echo $number_today;exit;
		if($number_today>=3){
			$this->jsonData(1,'每天抽奖最多'.$chou_limit.'次');
		    exit;
		}
		
		
        //抽奖
        $is_prize_type=1;    //is_prize_type=1 是奖品1，is_prize_type=2 是奖品2 ，当前只能是一个奖品处于被抽的状态。
        $is_prize=0;    //大于0就是中奖
        $all_num=250;   //当前奖品可中奖总数，当前只能是1个奖品处于被抽的状态。20161203=180，20161209=250，
        $rand_num=rand(1, 1);   //30分之1的概率
    	$is_prize_key=1;   //rand_num等于is_prize_key的时候，为中奖。
        
        if($rand_num==$is_prize_key){
        	$CityMod = M('user');
	        $data_num = $CityMod->field('count(id) as num ')->where(" is_prize='".$is_prize_type."' " )->select();
	        $data_num=$data_num[0]['num'];
	        
	        //当前中奖人数小于可中奖总数
	        if($data_num<$all_num){
	        	
	        	$CityMod = M('user');
		        $person_zhong = $CityMod->field('count(id) as num ')->where(" is_prize>0 and id='".addslashes($user_id)."' " )->select();
		        $person_zhong=$person_zhong[0]['num'];
		        //echo $person_zhong;exit;
		        
		        //当前手机没中过奖
		        if($person_zhong==0){
		        	$is_prize=$is_prize_type;
		        	
		        	$UserMod = M('user');
			        $sql=sprintf("UPDATE %s SET is_prize='".$is_prize."' 
			         , addtime='".$addtime."' 
			        where id='".addslashes($user_id)."' ", $UserMod->getTableName() );
			        //echo $sql;
			        $result = $UserMod->execute($sql);
			        
		        }
		        
	        }
	        
        }
        
        
        //记录抽奖日志
        $UserMod = M('prize_history');
        $sql=sprintf("INSERT %s SET user_id='".addslashes($user_id)."' 
        , modify_time='".$cur_time."' 
        , create_time='".$cur_time."' 
        , addtime='".$addtime."' 
        , wx_openid='".addslashes($openid)."' 
        , wx_nickname='".addslashes($userinfo['nickname'])."' 
        , wx_headimgurl='".addslashes($userinfo['headimgurl'])."' 
        , customer_ip='".addslashes($customer_ip)."' 
        , is_prize='".addslashes($is_prize)."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
        
        if($user_id>0){
        	$userdata['is_prize']=$is_prize;
            $data=$userdata;
            $this->jsonData(0,'成功',$data);
            exit;
        }
        else{
            $this->jsonData(1,'失败');
            exit;
        }
        
	
	}
	
	
	
	
	//提交个人信息  提交抽奖申请
	//http://lkkfood.loc/cms/home/lkkfood_userinfo?realname=小明&mobile=13911112222&title=小南国&address=南京路100号
	public function lkkfood_userinfo(){
		
		
		//$user_id=0;
		
		
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		
		
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=''){
		    $realname=$_REQUEST['realname'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile']!=''){
		    $mobile=$_REQUEST['mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['address']) && $_REQUEST['address']!=''){
		    $address=$_REQUEST['address'];
		}
		else{
			//$this->jsonData(1,'参数错误');
		    //exit;
		}
        
        
        
        
		if(isset($_REQUEST['title']) && $_REQUEST['title']!=''){
		    $title=$_REQUEST['title'];
		}
		else{
			//$this->jsonData(1,'参数错误');
		    //exit;
		}
        
        
        
        
		
		/*
		if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
		    $openid=$_REQUEST['openid'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
		
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=''){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		
		if(isset($_REQUEST['wx_headimgurl']) && $_REQUEST['wx_headimgurl']!=''){
		    $wx_headimgurl=$_REQUEST['wx_headimgurl'];
		}
		else{
			$wx_headimgurl='';
		}
		
		*/
		
		
		
		
		
		$CityMod = M('user');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' and is_prize>0 " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        //echo "<pre>";print_r($game_info);exit;
        
        if(empty($game_info)){
        	$this->jsonData(1,'您需要先中奖然后才能填写个人信息');
		    exit;
        }
        
        
        if(!empty($game_info['mobile'])){
        	$this->jsonData(1,'您已经提交过个人信息了');
		    exit;
        }
        
        
		
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        
    	$UserMod = M('user');
        $sql=sprintf("UPDATE %s SET 
         realname='".addslashes($realname)."' 
        ,mobile='".addslashes($mobile)."' 
        ,address='".addslashes($address)."' 
        ,title='".addslashes($title)."' 
        where id=".addslashes($userinfo['id'])." 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
        $data['user_id']=$user_id;
		$data['realname']=$realname;
		$data['mobile']=$mobile;
		$data['address']=$address;
		$data['title']=$title;
        $this->jsonData(0,'成功',$data);
	}
	
	
	
	//以上为李锦记-招牌菜项目
	
	
	
	//以下为逗咖项目
	
	
	
	//提交个人信息  提交抽奖申请
	//http://douka.loc/cms/home/douka_save_userinfo?realname=小明&mobile=13911112222&address=上海
	//http://douka.loc/cms/home/douka_save_userinfo?realname=小明&mobile=13911112222&address=南京路100号&openid=abcd1234&wx_nickname=微信昵称&wx_headimgurl=微信头像url
	public function douka_save_userinfo(){
		
		
		$user_id=0;
		
		
		/*
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		*/
		
		
		
		
		
		
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=''){
		    $realname=$_REQUEST['realname'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile']!=''){
		    $mobile=$_REQUEST['mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['address']) && $_REQUEST['address']!=''){
		    $address=$_REQUEST['address'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
        
        
        
		
		/*
		if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
		    $openid=$_REQUEST['openid'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
		
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=''){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		
		if(isset($_REQUEST['wx_headimgurl']) && $_REQUEST['wx_headimgurl']!=''){
		    $wx_headimgurl=$_REQUEST['wx_headimgurl'];
		}
		else{
			$wx_headimgurl='';
		}
		
		*/
		
		
		
		
		/*
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        //echo "<pre>";print_r($game_info);exit;
        
        if(!empty($game_info)){
        	$this->jsonData(1,'已经提交过抽奖申请');
		    exit;
        }
        */
        
		
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        $UserMod = M('game');
        $UserMod->cli_os=$cli_os;
        $UserMod->modify_time=$cur_time;
        $UserMod->create_time=$cur_time;
        $UserMod->addtime=date("Y-m-d H:i:s",$cur_time);
        $UserMod->realname=$realname;
        $UserMod->mobile=$mobile;
        $UserMod->address=$address;
        //$UserMod->openid=$openid;
        //$UserMod->wx_nickname=$wx_nickname;
        //$UserMod->wx_headimgurl=$wx_headimgurl;
        //$UserMod->user_id=$user_id;
        //$UserMod->wx_nickname=$userinfo['nickname'];
        //$UserMod->wx_headimgurl=$userinfo['headimgurl'];
        $game_id = $UserMod->add();
        
        $data['game_id']=$game_id;
		$data['realname']=$realname;
		$data['mobile']=$mobile;
		$data['address']=$address;
        $this->jsonData(0,'成功',$data);
	}
	
	
	
	//以上为逗咖项目
	
	
	
	
	
	
	//以下为猜灯谜项目
	
	
	//是否提交过个人信息
	//http://riddle.loc/cms/home/riddle_is_save_user_info?openid=abcd1234
	public function riddle_is_save_user_info(){
		
		$user_id=0;
		
		
		/*
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
        
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		*/
		
		
		
		
		
		if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
		    $openid=$_REQUEST['openid'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
        
		
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        //echo "<pre>";print_r($game_info);exit;
        
        if(!empty($game_info)){
        	$data['is_saved']=1;
        }
        else{
        	$data['is_saved']=0;
        }
        
        $this->jsonData(0,'成功',$data);
	}
	
	
	
	
	//提交个人信息  提交抽奖申请
	//http://riddle.loc/cms/home/riddle_save_user_info?realname=小明&mobile=13911112222&address=南京路100号&openid=abcd1234&wx_nickname=微信昵称&wx_headimgurl=微信头像url
	public function riddle_save_user_info(){
		
		
		$user_id=0;
		
		
		/*
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		*/
		
		
		
		
		
		
		if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
		    $openid=$_REQUEST['openid'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
		
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=''){
		    $realname=$_REQUEST['realname'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile']!=''){
		    $mobile=$_REQUEST['mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['address']) && $_REQUEST['address']!=''){
		    $address=$_REQUEST['address'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
        
        
        
		
		
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=''){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		
		if(isset($_REQUEST['wx_headimgurl']) && $_REQUEST['wx_headimgurl']!=''){
		    $wx_headimgurl=$_REQUEST['wx_headimgurl'];
		}
		else{
			$wx_headimgurl='';
		}
		
		
		
		
		
		
		
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        //echo "<pre>";print_r($game_info);exit;
        
        if(!empty($game_info)){
        	$this->jsonData(1,'已经提交过抽奖申请');
		    exit;
        }
        
        
		
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        $UserMod = M('game');
        $UserMod->cli_os=$cli_os;
        $UserMod->modify_time=$cur_time;
        $UserMod->create_time=$cur_time;
        $UserMod->addtime=date("Y-m-d H:i:s",$cur_time);
        $UserMod->realname=$realname;
        $UserMod->mobile=$mobile;
        $UserMod->address=$address;
        $UserMod->openid=$openid;
        $UserMod->wx_nickname=$wx_nickname;
        $UserMod->wx_headimgurl=$wx_headimgurl;
        //$UserMod->user_id=$user_id;
        //$UserMod->wx_nickname=$userinfo['nickname'];
        //$UserMod->wx_headimgurl=$userinfo['headimgurl'];
        $game_id = $UserMod->add();
        
        $data['game_id']=$game_id;
		$data['realname']=$realname;
		$data['mobile']=$mobile;
		$data['address']=$address;
        $this->jsonData(0,'成功',$data);
	}
	
	
	
	
	//是否提交过个人信息
	//http://riddle.loc/cms/home/is_save_user_info
	public function is_save_user_info(){
		
		$user_id=0;
		
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
        
        
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        //echo "<pre>";print_r($game_info);exit;
        
        if(!empty($game_info)){
        	$data['is_saved']=1;
        }
        else{
        	$data['is_saved']=0;
        }
        
        $this->jsonData(0,'成功',$data);
	}
	
	
	
	
	//提交个人信息  提交抽奖申请
	//http://riddle.loc/cms/home/save_user_info?realname=小明&mobile=13911112222&address=南京路100号
	public function save_user_info(){
		
		
		$user_id=0;
		
		
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
		
		
		
		
		
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=''){
		    $realname=$_REQUEST['realname'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile']!=''){
		    $mobile=$_REQUEST['mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['address']) && $_REQUEST['address']!=''){
		    $address=$_REQUEST['address'];
		}
		else{
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
        
        
        
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		
		
		
		
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        //echo "<pre>";print_r($game_info);exit;
        
        if(!empty($game_info)){
        	$this->jsonData(1,'已经提交过抽奖申请');
		    exit;
        }
        
        
		
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        $UserMod = M('game');
        $UserMod->cli_os=$cli_os;
        $UserMod->modify_time=$cur_time;
        $UserMod->create_time=$cur_time;
        $UserMod->addtime=date("Y-m-d H:i:s",$cur_time);
        $UserMod->realname=$realname;
        $UserMod->mobile=$mobile;
        $UserMod->address=$address;
        $UserMod->user_id=$user_id;
        $UserMod->openid=$openid;
        $UserMod->wx_nickname=$userinfo['nickname'];
        $UserMod->wx_headimgurl=$userinfo['headimgurl'];
        $game_id = $UserMod->add();
        
        $data['game_id']=$game_id;
		$data['realname']=$realname;
		$data['mobile']=$mobile;
		$data['address']=$address;
        $this->jsonData(0,'成功',$data);
	}
	//以上为猜灯谜项目
	
	
	
	
	//以下为亮视教师节项目
	//http://teacher.loc/cms/home/save_score?score=12
	public function save_score(){
		
		$user_id=0;
		
		if(isset($_REQUEST['score']) && is_numeric($_REQUEST['score'])){
		    $score=$_REQUEST['score'];
		}
		else{
			$score='0';
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
        
		$CityMod = M('game');
        $game_info = $CityMod->field('count(id) as num ')->where(" score<".$score." " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        //echo "<pre>";print_r($game_info);exit;
        $low_num=!empty($game_info['num'])?$game_info['num']:0;
        //echo $low_num;exit;
        
        $CityMod = M('game');
        $game_info = $CityMod->field('count(id) as num ')->where(" 1 " )->select();
        $game_info = !empty($game_info)?$game_info[0]:array();
        $total_num=!empty($game_info['num'])?$game_info['num']:0;
        //echo $total_num;exit;
        
        if($total_num>0){
        	$percent=round($low_num/$total_num*100);
        }
        else{
        	$percent=100;
        }
        //var_dump($percent);exit;
        
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        $UserMod = M('game');
        $UserMod->cli_os=$cli_os;
        $UserMod->modify_time=$cur_time;
        $UserMod->create_time=$cur_time;
        $UserMod->addtime=date("Y-m-d H:i:s",$cur_time);
        $UserMod->score=$score;
        $UserMod->percent=$percent;
        $UserMod->user_id=$user_id;
        $game_id = $UserMod->add();
        
        //echo $percent;exit;
        
        $data['game_id']=$game_id;
		$data['score']=$score;
		$data['percent']=$percent;
        $this->jsonData(0,'成功',$data);
	}
	//以上为亮视教师节项目
	
	
	
	
	
	//以下为日日煮项目
	
	
	//授权登陆  入口  活动1   http://ririzhu.loc/cms/home/index
	public function index1(){
		
		exit;
		
		
		//?nickname=1&forwho=test&zhiye=2&ran=1&cidx=2
		
		if(isset($_REQUEST['nickname'])){
			$nickname=urlencode($_REQUEST['nickname']);
		}
		else{
			$nickname='';
		}
		
		
		if(isset($_REQUEST['forwho'])){
			$forwho=urlencode($_REQUEST['forwho']);
		}
		else{
			$forwho='';
		}
		
		
		if(isset($_REQUEST['zhiye'])){
			$zhiye=urlencode($_REQUEST['zhiye']);
		}
		else{
			$zhiye='';
		}
		
		
		if(isset($_REQUEST['ran'])){
			$ran=urlencode($_REQUEST['ran']);
		}
		else{
			$ran='';
		}
		
		
		if(isset($_REQUEST['cidx'])){
			$cidx=urlencode($_REQUEST['cidx']);
		}
		else{
			$cidx='';
		}
		
		
		
		
		
		$url='/game1/?nickname='.$nickname.'&forwho='.$forwho.'&zhiye='.$zhiye.'&ran='.$ran.'&cidx='.$cidx;
		
		
		$goto=$url;
		
		
		
		$goto=$url;
		
		$openid=$this->get_openid($goto);
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
		
		
		redirect($url);
		exit;
		
		
		
	}
	
	
	
	//授权登陆  入口  活动2   http://ririzhu.loc/cms/home/index2
	public function index2(){
		
		if(isset($_REQUEST['game_2_id'])){
			$game_2_id=$_REQUEST['game_2_id'];
		}
		else{
			$game_2_id='';
		}
		
		
		$url='/game2/?game_2_id='.$game_2_id;
		
		
		$goto=$url;
		
		
		$openid=$this->get_openid($goto);
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
		
		redirect($url);
		exit;
		
		
		
	}
	
	
	
	
	//授权登陆  入口  活动3   http://ririzhu.loc/cms/home/index3
	public function index3(){
		
		//if(isset($_REQUEST['game_2_id'])){
		//	$game_2_id=$_REQUEST['game_2_id'];
		//}
		//else{
		//	$game_2_id='';
		//}
		
		
		//$url='/game3/?game_2_id='.$game_2_id;
		$url='/game3/';
		
		$goto=$url;
		
		
		$openid=$this->get_openid($goto);
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
		
		redirect($url);
		exit;
		
		
		
	}
	
	
	
	
	//用户信息 提交 http://ririzhu.loc/cms/home/game_1_cai?nickname=小明&zhiye=工程师&caixi=砖石菜系&forwho=母亲
	public function game_1_cai(){
		
		
		$user_id=0;
		
		
		
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
        
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		
		
		
    	
		if(isset($_REQUEST['nickname']) && $_REQUEST['nickname']!=""){
		    $nickname=$_REQUEST['nickname'];
		}
		else{
			$nickname='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
		if(isset($_REQUEST['zhiye']) && $_REQUEST['zhiye']!=""){
		    $zhiye=$_REQUEST['zhiye'];
		}
		else{
			$zhiye='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['caixi']) && $_REQUEST['caixi']!=""){
		    $caixi=$_REQUEST['caixi'];
		}
		else{
			$caixi='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['forwho']) && $_REQUEST['forwho']!=""){
		    $forwho=$_REQUEST['forwho'];
		}
		else{
			$forwho='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		
        
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    
		
		//$CityMod = M('game_1');
        //$game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        
        $UserMod = M('game_1');
        $UserMod->cli_os=$cli_os;
        $UserMod->modify_time=$cur_time;
        $UserMod->create_time=$cur_time;
        $UserMod->addtime=date("Y-m-d H:i:s",$cur_time);
        $UserMod->nickname=$nickname;
        $UserMod->zhiye=$zhiye;
        $UserMod->caixi=$caixi;
        $UserMod->forwho=$forwho;
        $UserMod->user_id=$user_id;
        $UserMod->openid=$openid;
        $UserMod->wx_nickname=$userinfo['nickname'];
        $UserMod->wx_headimgurl=$userinfo['headimgurl'];
        $game_1_id = $UserMod->add();
        
        
        $data['game_1_id']=$game_1_id;
		$data['nickname']=$nickname;
		$data['zhiye']=$zhiye;
		$data['caixi']=$caixi;
		$data['forwho']=$forwho;
		$data['user_id']=$user_id;
		$data['openid']=$openid;
        $this->jsonData(0,'成功',$data);
        
        
	}
	
	
	
	//上传图片 提交 http://ririzhu.loc/cms/home/game2_zp_pic_upload?filestring=照片文件二进制码之类的内容
    public function game2_zp_pic_upload(){
    	
    	
    	$this->jsonData(1,'感谢您对日日煮的支持！报名已截止，持续关注6月3日“去你的厨房”总决赛！');
        exit;
            
    	//debug
    	//$_POST['filestring']='debug';
    	
    	
    	
		$user_id=0;
		
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		
		//echo $user_id;exit;
    	
    	
		//if(isset($_REQUEST['style']) && ($_REQUEST['style']==1 || $_REQUEST['style']==2 || $_REQUEST['style']==3) ){
		//	$style=$_REQUEST['style'];
		//}
		//else{
		//	$this->jsonData(1,'参数错误');
        //    exit;
		//}
		
		
		
        if(isset($_REQUEST['is_android']) && $_REQUEST['is_android']==1){
            $is_android=$_REQUEST['is_android'];
        }
        else{
            $is_android=0;
        }
        
        
        
        
        $is_android=1;
        
		//$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".rand(10,99).".png";
		$path = "user_id_".$user_id."_openid_".$openid."_time_".date('ymdHis').".png";
		
		
		//$path='game_1_style_1_time_150716161543_1.png';
		$dest = BASE_ZP_PIC_PATH.$path;
		//echo $dest;exit;
		
		
		/*
		//base64模式接受上传数据
		$f = fopen($dest,'w');
		$img = 'data:image/jpeg;base64,/9j/4QAYRXhhtcHRrPXYv//Z';
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace('data:image/gif;base64,', '', $img);
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$datas = base64_decode($img);
		fwrite($f,$datas);
		fclose($f);
		*/
		
		
		
		$pic_imagerotate='';
		
		if($_FILES){
			//上传方式：表单
			$input = key($_FILES);
			if(!move_uploaded_file($_FILES[$input]['tmp_name'],$dest)) {
				$this->jsonData(1,'失败');
		        exit;
			}
			else{
				
				//判断EXIF头信息模式解决旋转90度问题
				$exif = exif_read_data($dest);
				$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
				if($ort==""){
					$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
				}
				switch($ort){
					case 1: // nothing
						break;
        			case 2: // horizontal flip
        				break;
	                case 3: // 180 rotate left  //向左旋转180度
	                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
	                    $pic_imagerotate='180';
	                    break;
	                case 4: // vertical flip
            			break;
            		case 5: // vertical flip + 90 rotate right
				        $pic_imagerotate='-90';
				        break;
	                case 6: // 90 rotate right  //向右旋转90度
	                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
	                    $pic_imagerotate='-90';
	                    break;
	                case 7: // horizontal flip + 90 rotate right
				        $pic_imagerotate='-90';
				        break;
	                case 8:    // 90 rotate left  //向左旋转90度
	                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
	                    $pic_imagerotate='90';
	                    break;
	            }
	            //echo "<pre>";print_r($exif);exit;
	            //var_dump($pic_imagerotate);exit;
			}
		}
		elseif(isset($_POST['filestring'])){ 
			
			//上传方式：原始POST
			//$f = fopen($dest,'w');
			//fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = $GLOBALS['HTTP_RAW_POST_DATA'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = $_POST['filestring'];
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题（示例：http://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation）
				$f = fopen($dest,'w');
				fwrite($f,$_POST['filestring']);
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
            
			
			
		}
		elseif(isset($GLOBALS['HTTP_RAW_POST_DATA'])){ 
			//上传方式：原始POST
			//$f = fopen($dest,'w');
			//fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = $GLOBALS['HTTP_RAW_POST_DATA'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = $GLOBALS['HTTP_RAW_POST_DATA'];
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题（示例：http://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation）
				$f = fopen($dest,'w');
				fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
            
			
			
		}
		else{ 
			//上传方式：客户端提交
			//$f = fopen($dest,'w');
			//fwrite($f,file_get_contents('php://input'));
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = file_get_contents('php://input');
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = file_get_contents('php://input');
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题
				$f = fopen($dest,'w');
				fwrite($f,file_get_contents('php://input'));
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
			
			
			
		}
		
		
		//echo $exif['Orientation'];exit;
		//echo "<pre>";print_r($exif);exit;
		//$dest='D:\www\ouliwei\test\ouliwei\cms/public/web_pic/game_1_style_1_time_150716161543_1.jpg';
		//echo $dest;exit;
		
		$file_type=$this->get_file_type($dest); 
		
		//debug
		//$file_type='png';
		
		if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png"){
			@unlink($dest);
			$this->jsonData(1,'图片上传限定jpg、gif、png类型');
		    exit;
		}
		
		
		
		$file_z=filesize($dest);
		$f_size_limit_byte=BASE_FILE_SIZE_LIMIT*1024*1024;
		
		if ($file_z>$f_size_limit_byte){
			@unlink($dest);
			$this->jsonData(1,'图片上传限定'.BASE_FILE_SIZE_LIMIT.'M');
		    exit;
		}
		
		
		/*
		//裁切
		$path_egg = "game_".$game_id."_egg_".$style."_time_".date('ymdHis')."_".$user_id.".png";
		$dest_egg = BASE_PIC_RESIZE_PATH.$path_egg;
		$out_path=$dest_egg;
		$org_path=$dest;
    	$src_w=540;
    	$src_h=588;
    	$this->zoom($org_path,$out_path,$src_w,$src_h);	
		//裁切
		*/
		
		/*
		$cur_time=time();
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET headpic='".addslashes($path)."' 
        , imagerotate='".addslashes($pic_imagerotate)."' 
        , addtime_headpic='".date("Y-m-d H:i:s",$cur_time)."' 
        , myword='".addslashes($myword)."' 
        where id='".addslashes($game_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        */
        
        
		$data['zp_pic_path']=$path;
		$data['zp_pic_url']=BASE_URL."/public/zp_pic/".$path;
		//$data['style']=$style;
		//$data['point_total']=$point_total;
        $this->jsonData(0,'成功',$data);
        
        
        
    }
    
    
    
    
	//用户信息 提交 http://ririzhu.loc/cms/home/game_2_zp?game_1_id=228&zp_username=Supper小明&zp_pic_path_1=user_id_1697_openid_abc001_time_160519164152.png&zp_pic_path_2=user_id_1697_openid_abc001_time_160519164052.png&zp_zhiye=小职员&zp_shanchang=中餐
	public function game_2_zp(){
		
		
		$this->jsonData(1,'感谢您对日日煮的支持！报名已截止，持续关注6月3日“去你的厨房”总决赛！');
        exit;
        
        
        		
		$user_id=0;
		
		
		
		
    	$vote_datetime=date('YmdHis');
    	if($vote_datetime>=20160531000000){
    		$this->jsonData(1,'感谢您对日日煮的支持！报名已截止，持续关注6月3日“去你的厨房”总决赛！');
            exit;
    	}
    	
    	
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		//echo $user_id;exit;
		
		
		
		if(isset($_REQUEST['game_1_id']) && !empty($_REQUEST['game_1_id'])){
		    $game_1_id=$_REQUEST['game_1_id'];
		}
		else{
			$game_1_id=0;
		}
        
        
        
		if(isset($_REQUEST['zp_username']) && !empty($_REQUEST['zp_username'])){
		    $zp_username=$_REQUEST['zp_username'];
		}
		else{
			$zp_username='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
    	
		if(isset($_REQUEST['zp_pic_path_1']) && $_REQUEST['zp_pic_path_1']!=""){
		    $zp_pic_path_1=$_REQUEST['zp_pic_path_1'];
		}
		else{
			$zp_pic_path_1='';
		}
        
        
		if(isset($_REQUEST['zp_pic_path_2']) && $_REQUEST['zp_pic_path_2']!=""){
		    $zp_pic_path_2=$_REQUEST['zp_pic_path_2'];
		}
		else{
			$zp_pic_path_2='';
		}
		
		
		if(isset($_REQUEST['zp_zhiye']) && $_REQUEST['zp_zhiye']!=""){
		    $zp_zhiye=$_REQUEST['zp_zhiye'];
		}
		else{
			$zp_zhiye='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['zp_shanchang']) && $_REQUEST['zp_shanchang']!=""){
		    $zp_shanchang=$_REQUEST['zp_shanchang'];
		}
		else{
			$zp_shanchang='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		
        
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    
		
		$CityMod = M('game_1');
        $game_1_info = $CityMod->where(" id='".addslashes($game_1_id)."' " )->select();
        $game_1_info = !empty($game_1_info)?$game_1_info[0]:array();
        //echo "<pre>";print_r($game_1_info);exit;
        
        $UserMod = M('game_2');
        $UserMod->cli_os=$cli_os;
        $UserMod->modify_time=$cur_time;
        $UserMod->create_time=$cur_time;
        $UserMod->addtime=isset($game_1_info['addtime'])?$game_1_info['addtime']:'';
        $UserMod->game_1_id=$game_1_id;
        $UserMod->nickname=isset($game_1_info['nickname'])?$game_1_info['nickname']:'';
        $UserMod->zhiye=isset($game_1_info['zhiye'])?$game_1_info['zhiye']:'';
        $UserMod->caixi=isset($game_1_info['caixi'])?$game_1_info['caixi']:'';
        $UserMod->forwho=isset($game_1_info['forwho'])?$game_1_info['forwho']:'';
        $UserMod->user_id=$user_id;
        $UserMod->openid=$openid;
        $UserMod->wx_nickname=$userinfo['nickname'];
        $UserMod->wx_headimgurl=$userinfo['headimgurl'];
        $UserMod->zp_username=$zp_username;
        $UserMod->zp_pic_path_1=$zp_pic_path_1;
        $UserMod->zp_pic_path_2=$zp_pic_path_2;
        $UserMod->zp_zhiye=$zp_zhiye;
        $UserMod->zp_shanchang=$zp_shanchang;
        $UserMod->zp_addtime=date("Y-m-d H:i:s",$cur_time);
        $game_2_id = $UserMod->add();
        
        
        $data['game_1_id']=$game_1_id;
        $data['game_2_id']=$game_2_id;
		$data['nickname']=isset($game_1_info['nickname'])?$game_1_info['nickname']:'';
		$data['zhiye']=isset($game_1_info['zhiye'])?$game_1_info['zhiye']:'';
		$data['caixi']=isset($game_1_info['caixi'])?$game_1_info['caixi']:'';
		$data['forwho']=isset($game_1_info['forwho'])?$game_1_info['forwho']:'';
		$data['user_id']=$user_id;
		$data['openid']=$openid;
		$data['zp_username']=$zp_username;
		$data['zp_pic_path_1']=$zp_pic_path_1;
		$data['zp_pic_path_2']=$zp_pic_path_2;
		$data['zp_zhiye']=$zp_zhiye;
		$data['zp_shanchang']=$zp_shanchang;
        $this->jsonData(0,'成功',$data);
        
        
	}
	
	
	
	
	//用户信息 提交 图片抓取微信服务器上的 http://ririzhu.loc/cms/home/game_2_zp_media_id?game_1_id=228&zp_username=Supper小明&media_id_1=aaabbbccc1&media_id_2=aaabbbccc2&zp_zhiye=小职员&zp_shanchang=中餐
	public function game_2_zp_media_id(){
		
		
    	$this->jsonData(1,'感谢您对日日煮的支持！报名已截止，持续关注6月3日“去你的厨房”总决赛！');
        exit;
            
           
		$user_id=0;
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
		
		if(isset($userinfo['id']) && !empty($userinfo['id'])){
		    $user_id=$userinfo['id'];
		}
		else{
			$user_id=0;
			//$this->jsonData(1,'请通过微信授权参与活动');
		    //exit;
		}
		
		//echo $user_id;exit;
		
		
		
		if(isset($_REQUEST['game_1_id']) && !empty($_REQUEST['game_1_id'])){
		    $game_1_id=$_REQUEST['game_1_id'];
		}
		else{
			$game_1_id=0;
		}
        
        
        
		if(isset($_REQUEST['zp_username']) && !empty($_REQUEST['zp_username'])){
		    $zp_username=$_REQUEST['zp_username'];
		}
		else{
			$zp_username='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
        
        
    	
		if(isset($_REQUEST['media_id_1']) && $_REQUEST['media_id_1']!=""){
		    $media_id_1=$_REQUEST['media_id_1'];
		}
		else{
			$media_id_1='';
		}
        
        
		if(isset($_REQUEST['media_id_2']) && $_REQUEST['media_id_2']!=""){
		    $media_id_2=$_REQUEST['media_id_2'];
		}
		else{
			$media_id_2='';
		}
		
		
		if(isset($_REQUEST['zp_zhiye']) && $_REQUEST['zp_zhiye']!=""){
		    $zp_zhiye=$_REQUEST['zp_zhiye'];
		}
		else{
			$zp_zhiye='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		if(isset($_REQUEST['zp_shanchang']) && $_REQUEST['zp_shanchang']!=""){
		    $zp_shanchang=$_REQUEST['zp_shanchang'];
		}
		else{
			$zp_shanchang='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
		
        
		
		
		$CityMod = M('game_2');
        $game_2_info = $CityMod->where(" openid='".addslashes($openid)."' " )->select();
        //$game_2_info = !empty($game_2_info)?$game_2_info[0]:array();
        //echo "<pre>";print_r($game_2_info);exit;
        if(isset($game_2_info) && !empty($game_2_info)){
		    $this->jsonData(1,'您已经参加报名了');
            exit;
		}
		
		
		
		//获取媒体media_id所需的token要用这种每天只能取2000次的token。
		$accessToken = $this->getAccessToken();
		$date_ymdHis=date('ymdHis');
		
		$zp_pic_path_1='';
        if(!empty($accessToken) && !empty($media_id_1)){
        	
			//获取微信媒体文件
			$get_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$media_id_1.'';
			//echo $get_url;exit;
			$get_return = file_get_contents($get_url);
			//$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
			
			//$path = "user_id_".$user_id."_openid_".$openid."_time_".$date_ymdHis."_".$media_id_1."_1.png";
			$path = "user_id_".$user_id."_openid_".$openid."_time_".$date_ymdHis."_1.png";
			$dest = BASE_ZP_PIC_PATH.$path;
			//echo $dest;exit;
			
			
			if(!empty($get_return)){
				
				//模仿复制 php://input 模式上传
				$f = fopen($dest,'w');
				fwrite($f,$get_return);
				fclose($f);
				
				$zp_pic_path_1=$path;
				
	        }
	        else{
	        	//$this->jsonData(1,'媒体文件获取失败');
	        	//exit;
	        }
        }
        
        
        
		$zp_pic_path_2='';
        if(!empty($accessToken) && !empty($media_id_2)){
        	
			//获取微信媒体文件
			$get_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$media_id_2.'';
			//echo $get_url;exit;
			$get_return = file_get_contents($get_url);
			//$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
			
			//$path = "user_id_".$user_id."_openid_".$openid."_time_".$date_ymdHis."_".$media_id_2."_2.png";
			$path = "user_id_".$user_id."_openid_".$openid."_time_".$date_ymdHis."_2.png";
			$dest = BASE_ZP_PIC_PATH.$path;
			//echo $dest;exit;
			
			
			if(!empty($get_return)){
				
				//模仿复制 php://input 模式上传
				$f = fopen($dest,'w');
				fwrite($f,$get_return);
				fclose($f);
				
				$zp_pic_path_2=$path;
				
	        }
	        else{
	        	//$this->jsonData(1,'媒体文件获取失败');
	        	//exit;
	        }
        }
        
        
        $cli_os=$this->get_client_os();
	    $cur_time=time();
	    
		
		$CityMod = M('game_1');
        $game_1_info = $CityMod->where(" id='".addslashes($game_1_id)."' " )->select();
        $game_1_info = !empty($game_1_info)?$game_1_info[0]:array();
        //echo "<pre>";print_r($game_1_info);exit;
        
        $UserMod = M('game_2');
        $UserMod->cli_os=$cli_os;
        $UserMod->modify_time=$cur_time;
        $UserMod->create_time=$cur_time;
        $UserMod->addtime=isset($game_1_info['addtime'])?$game_1_info['addtime']:'';
        $UserMod->game_1_id=$game_1_id;
        $UserMod->nickname=isset($game_1_info['nickname'])?$game_1_info['nickname']:'';
        $UserMod->zhiye=isset($game_1_info['zhiye'])?$game_1_info['zhiye']:'';
        $UserMod->caixi=isset($game_1_info['caixi'])?$game_1_info['caixi']:'';
        $UserMod->forwho=isset($game_1_info['forwho'])?$game_1_info['forwho']:'';
        $UserMod->user_id=$user_id;
        $UserMod->openid=$openid;
        $UserMod->wx_nickname=$userinfo['nickname'];
        $UserMod->wx_headimgurl=$userinfo['headimgurl'];
        $UserMod->zp_username=$zp_username;
        $UserMod->accessToken=$accessToken;
        $UserMod->media_id_1=$media_id_1;
        $UserMod->zp_pic_path_1=$zp_pic_path_1;
        $UserMod->media_id_2=$media_id_2;
        $UserMod->zp_pic_path_2=$zp_pic_path_2;
        $UserMod->zp_zhiye=$zp_zhiye;
        $UserMod->zp_shanchang=$zp_shanchang;
        $UserMod->zp_addtime=date("Y-m-d H:i:s",$cur_time);
        $game_2_id = $UserMod->add();
        
        
        $data['game_1_id']=$game_1_id;
        $data['game_2_id']=$game_2_id;
		$data['nickname']=isset($game_1_info['nickname'])?$game_1_info['nickname']:'';
		$data['zhiye']=isset($game_1_info['zhiye'])?$game_1_info['zhiye']:'';
		$data['caixi']=isset($game_1_info['caixi'])?$game_1_info['caixi']:'';
		$data['forwho']=isset($game_1_info['forwho'])?$game_1_info['forwho']:'';
		$data['user_id']=$user_id;
		$data['openid']=$openid;
		$data['zp_username']=$zp_username;
		$data['zp_pic_path_1']=$zp_pic_path_1;
		$data['zp_pic_path_2']=$zp_pic_path_2;
		$data['zp_zhiye']=$zp_zhiye;
		$data['zp_shanchang']=$zp_shanchang;
        $this->jsonData(0,'成功',$data);
        
        
	}
	
	
	
	//用户信息 提交 http://ririzhu.loc/cms/home/game_wx_userinfo
	public function game_wx_userinfo(){
		
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	
    	
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
        
		$CityMod = M('game_2');
        $game_2_info = $CityMod->where(" user_id='".addslashes($userinfo['id'])."' " )->order('id desc')->limit('0,1')->select();
        $game_2_info = !empty($game_2_info)?$game_2_info[0]:array();
        //echo "<pre>";print_r($game_2_info);exit;
        
        
        $data['user_id']=$userinfo['id'];
        $data['openid']=$userinfo['openid'];
		$data['nickname']=$userinfo['nickname'];
		$data['headimgurl']=$userinfo['headimgurl'];
		$data['zp_finished']=empty($game_2_info)?0:1;
		$data['zp_username']=$game_2_info['zp_username'];
		$data['zp_zhiye']=$game_2_info['zp_zhiye'];
        $this->jsonData(0,'成功',$data);
        
	}
	
	
	
	
	
    //被投票作品 排行榜   http://ririzhu.loc/cms/home/game_2_zp_paihangbang
    public function game_2_zp_paihangbang(){
    	
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	
    	
		
    	$CityMod = M('game_2');
        $listing = $CityMod->field('id as game_2_id , zp_username  , zp_zhiye , wx_headimgurl , ticket_number ')->where(" 1 ".$andsql )->order('ticket_number desc')->limit('0,50')->select();
        
        
        if(!empty($listing)){
        	foreach($listing as $k=>$v){
        		$listing[$k]['top_number']=$k+1;
        	}
        }
        
        //echo "<pre>";print_r($listing);exit;
        
		$data=$listing;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    
    
    //被投票作品列表   http://ririzhu.loc/cms/home/game_2_zp_list?search_name=Supper小
    public function game_2_zp_list(){
    	
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	
    	
    	
    	
		if(isset($_REQUEST['search_name']) && $_REQUEST['search_name']!=""){
		    $search_name=$_REQUEST['search_name'];
		}
		else{
			$search_name='';
		}
		
		$andsql='';
		if($search_name!=''){
			$andsql=$andsql.' and zp_username like "%'.$search_name.'%" ';
		}
		
    	$CityMod = M('game_2');
        $listing = $CityMod->field('id as game_2_id , zp_username  , zp_zhiye , wx_headimgurl , ticket_number ')->where(" 1 ".$andsql )->order('ticket_number desc')->select();
        
        
        //if(!empty($listing)){
        //	foreach($listing as $k=>$v){
        //		$listing[$k]['media']=BASE_URL."/public/vote_photo_media/".$v['id'].".png";
        //	}
        //}
        
        //echo "<pre>";print_r($listing);exit;
        
		$data=$listing;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
	
	
    //被投票作品详情   http://ririzhu.loc/cms/home/game_2_zp_detail?game_2_id=256
    public function game_2_zp_detail(){
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	
    	
    	
    	
		if(isset($_REQUEST['game_2_id']) && $_REQUEST['game_2_id']!=""){
		    $game_2_id=$_REQUEST['game_2_id'];
		}
		else{
			$game_2_id='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
    	$CityMod = M('game_2');
        $game_2_info = $CityMod->field('id as game_2_id , zp_username , zp_pic_path_1, zp_pic_path_2, zp_zhiye, zp_shanchang , wx_headimgurl , ticket_number ')->where(" id= '".addslashes($game_2_id)."' " )->order('id desc')->select();
        $game_2_info=$game_2_info[0];
        
        $game_2_info['zp_pic_path_1_url']=BASE_URL."/public/zp_pic/".$game_2_info['zp_pic_path_1'];
        $game_2_info['zp_pic_path_2_url']=BASE_URL."/public/zp_pic/".$game_2_info['zp_pic_path_2'];
        
        
        //echo "<pre>";print_r($game_2_info);exit;
        
		$data=$game_2_info;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    
    
    
	
    //对某个作品投票   http://ririzhu.loc/cms/home/game_2_zp_vote?game_2_id=256
    public function game_2_zp_vote(){
    	
    	$this->jsonData(1,'投票已结束，感谢您的参与！');
        exit;
            
            
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	
    	
    	
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
    	
		if(isset($_REQUEST['game_2_id']) && $_REQUEST['game_2_id']!=""){
		    $game_2_id=$_REQUEST['game_2_id'];
		}
		else{
			$game_2_id='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		
    	$vote_datetime=date('YmdHis');
    	if($vote_datetime>=20160531000000){
    		$this->jsonData(1,'投票已结束，感谢您的参与！');
            exit;
    	}
    	
    	
        
        $vote_date_limit=10;   //1个IP单日最多对同1个人投10票
        $cur_time=time();
        $addtime=date("Y-m-d H:i:s",$cur_time);
        $addtime_start=date("Y-m-d",$cur_time)." 00:00:00";
        $addtime_end=date("Y-m-d",$cur_time)." 23:59:59";
        
		$customer_ip=$this->get_customer_ip();
        
        /*
		$CityMod = M('zp_user_history');
        $vote_info = $CityMod->where(" wx_openid='".addslashes($openid)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."' " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info)){
        	$this->jsonData(1,'您今天已经投过票了');
            exit;
        }
        */
        
        
        /*
        //每个微信账号只能投10票
        $CityMod = M('zp_user_history');
        $vote_info = $CityMod->where(" wx_openid='".addslashes($openid)."' " )->select();
        if(!empty($vote_info) && count($vote_info)>=10){
        	$this->jsonData(1,'您的微信账号已经达到投票次数限制');
            exit;
        }
        */
        
        
        //每个微信账号每天只能投10票
        $CityMod = M('zp_user_history');
        $vote_info = $CityMod->where(" wx_openid='".addslashes($openid)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."'  " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info) && count($vote_info)>=$vote_date_limit){
        	$this->jsonData(1,'您今日投票机会已用尽，请明日再来！');
            exit;
        }
        
        
        /*
        $CityMod = M('zp_user_history');
        $vote_info = $CityMod->where(" customer_ip='".addslashes($customer_ip)."' and game_2_id='".addslashes($game_2_id)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."'  " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info) && count($vote_info)>=$vote_date_limit){
        	$this->jsonData(1,'您的IP已经达到投票次数限制');
            exit;
        }
        */
        
        
        $UserMod = M('zp_user_history');
        $sql=sprintf("INSERT %s SET game_2_id='".addslashes($game_2_id)."' 
        , modify_time='".$cur_time."' 
        , create_time='".$cur_time."' 
        , addtime='".$addtime."' 
        , user_id='".addslashes($userinfo['id'])."' 
        , wx_openid='".addslashes($openid)."' 
        , wx_nickname='".addslashes($userinfo['nickname'])."' 
        , wx_headimgurl='".addslashes($userinfo['headimgurl'])."' 
        , customer_ip='".addslashes($customer_ip)."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
		$andsql=" and game_2_id='".addslashes($game_2_id)."' ";
    	$CityMod = M('zp_user_history');
        $rst = $CityMod->field('sum(ticket_num) as num ')->where(" 1 ".$andsql." ")->select();
        $ticket_number=$rst[0]['num'];
        //echo $ticket_number;exit;
        //echo "<pre>";print_r($rst);exit;
        
        
    	$UserMod = M('game_2');
        $sql=sprintf("UPDATE %s SET 
         ticket_number='".addslashes($ticket_number)."'
        where id=".addslashes($game_2_id)." 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
		$data['game_2_id']=$game_2_id;
		$data['ticket_number']=$ticket_number;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    
    
    
    
    //三期.15道菜列表及票数   http://ririzhu.loc/cms/home/game_3_list
    public function game_3_list(){
    	
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	
    	$andsql='';
    	
    	$CityMod = M('game_3');
        $listing = $CityMod->field('id as game_3_id , title , ticket_number ')->where(" 1 ".$andsql )->order('id asc')->select();
        
        
        //if(!empty($listing)){
        //	foreach($listing as $k=>$v){
        //		$listing[$k]['media']=BASE_URL."/public/vote_photo_media/".$v['id'].".png";
        //	}
        //}
        
        //echo "<pre>";print_r($listing);exit;
        
		$data=$listing;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    
    
    
	
    //三期.对15道菜投票 最多可选4个菜   http://ririzhu.loc/cms/home/game_3_vote?game_3_id=1,4,7,15
    public function game_3_vote(){
    	
    	
    	$vote_datetime=date('YmdHis');
    	if($vote_datetime>=20160602182000){
    		$this->jsonData(1,'投票已结束，感谢您的参与！');
            exit;
    	}
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	
    	
    	
        if(empty($openid)){
			$this->jsonData(1,'请通过微信参与活动');
		    exit;
		}
		
		
    	
    	
    	
    	
		if(isset($_REQUEST['game_3_id']) && $_REQUEST['game_3_id']!=""){
		    $game_3_id=$_REQUEST['game_3_id'];
		}
		else{
			$game_3_id='';
			$this->jsonData(1,'参数错误');
		    exit;
		}
		
		$game_3_id_arr = explode(",", $game_3_id);
		
		
		//echo "<pre>";print_r($game_3_id_arr);exit;
		
		
        
        $vote_date_limit=10;   //1个IP单日最多对同1个人投10票
        $cur_time=time();
        $addtime=date("Y-m-d H:i:s",$cur_time);
        $addtime_start=date("Y-m-d",$cur_time)." 00:00:00";
        $addtime_end=date("Y-m-d",$cur_time)." 23:59:59";
        
		$customer_ip=$this->get_customer_ip();
        
        /*
		$CityMod = M('zp_user_history');
        $vote_info = $CityMod->where(" wx_openid='".addslashes($openid)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."' " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info)){
        	$this->jsonData(1,'您今天已经投过票了');
            exit;
        }
        */
        
        
        /*
        //每个微信账号只能投10票
        $CityMod = M('zp_user_history');
        $vote_info = $CityMod->where(" wx_openid='".addslashes($openid)."' " )->select();
        if(!empty($vote_info) && count($vote_info)>=10){
        	$this->jsonData(1,'您的微信账号已经达到投票次数限制');
            exit;
        }
        */
        
        
        //每个微信账号每天只能投10票
        $CityMod = M('game_3');
        $vote_info = $CityMod->where(" wx_openid='".addslashes($openid)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."'  " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info) && count($vote_info)>=$vote_date_limit){
        	$this->jsonData(1,'您今日投票机会已用尽，请明日再来！');
            exit;
        }
        
        
        /*
        $CityMod = M('zp_user_history');
        $vote_info = $CityMod->where(" customer_ip='".addslashes($customer_ip)."' and game_2_id='".addslashes($game_2_id)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."'  " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info) && count($vote_info)>=$vote_date_limit){
        	$this->jsonData(1,'您的IP已经达到投票次数限制');
            exit;
        }
        */
        
        
        if(!empty($game_3_id_arr)){
        	foreach($game_3_id_arr as $k=>$v ){
        		
        		$game_3_id=$v;
        		
		        $UserMod = M('game_3_history');
		        $sql=sprintf("INSERT %s SET game_3_id='".addslashes($game_3_id)."' 
		        , modify_time='".$cur_time."' 
		        , create_time='".$cur_time."' 
		        , addtime='".$addtime."' 
		        , user_id='".addslashes($userinfo['id'])."' 
		        , wx_openid='".addslashes($openid)."' 
		        , wx_nickname='".addslashes($userinfo['nickname'])."' 
		        , wx_headimgurl='".addslashes($userinfo['headimgurl'])."' 
		        , customer_ip='".addslashes($customer_ip)."' 
		        ", $UserMod->getTableName() );
		        //echo $sql;exit;
		        $result = $UserMod->execute($sql);
		        
		        
		        
				$andsql=" and game_3_id='".addslashes($game_3_id)."' ";
		    	$CityMod = M('game_3_history');
		        $rst = $CityMod->field('sum(ticket_num) as num ')->where(" 1 ".$andsql." ")->select();
		        $ticket_number=$rst[0]['num'];
		        //echo $ticket_number;exit;
		        //echo "<pre>";print_r($rst);exit;
		        
		        
		    	$UserMod = M('game_3');
		        $sql=sprintf("UPDATE %s SET 
		         ticket_number='".addslashes($ticket_number)."'
		        where id=".addslashes($game_3_id)." 
		        ", $UserMod->getTableName() );
		        //echo $sql;exit;
		        $result = $UserMod->execute($sql);
		        
        		
        	}
        }
        
        
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    
    //三期.6个人的作品列表   http://ririzhu.loc/cms/home/game_3_zp_list_top6
    public function game_3_zp_list_top6(){
    	
    	
    	
    	$openid=$this->get_openid_ajax();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	
    	
    	
		//$andsql=' and id in (363,405,759,364,640,649) ';
		$andsql=' and id in (363,759,405,640,815,819) ';
		
		
    	$CityMod = M('game_2');
        $listing = $CityMod->field('id as game_2_id , zp_username  , zp_zhiye , wx_headimgurl , ticket_number ')->where(" 1 ".$andsql )->order('id asc')->select();
        
        
        //if(!empty($listing)){
        //	foreach($listing as $k=>$v){
        //		$listing[$k]['media']=BASE_URL."/public/vote_photo_media/".$v['id'].".png";
        //	}
        //}
        
        //echo "<pre>";print_r($listing);exit;
        
		$data=$listing;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    
    //获取当前时间   http://ririzhu.loc/cms/home/get_current_time
    public function get_current_time(){
    	
    	$current_time_t=time();
    	$current_time_f=date("Y-m-d H:i:s",$current_time_t);
    	
		$data['current_time_t']=$current_time_t;
		$data['current_time_f']=$current_time_f;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    //以上为日日煮项目
        
	
	//第一阶段 记录开始游戏  http://rongtai.loc/cms/home/game1start
	public function game1start(){
		
		//$_SESSION['game_id']=2; //指定game_id操作数据。
		
		if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
		    $game_id=$_SESSION['game_id'];
		}
		else{
		    $cli_os=$this->get_client_os();
	        $cur_time=time();
	        
	        $UserMod = M('game');
	        $UserMod->cli_os=$cli_os;
	        $UserMod->modify_time=$cur_time;
	        $UserMod->create_time=$cur_time;
	        $UserMod->addtime=date("Y-m-d H:i:s",$cur_time);
	        $game_id = $UserMod->add();
	        
	        $_SESSION['game_id']=$game_id;
		}
		
        if($game_id>0){
        	return $game_id;
            //$this->jsonData(0,'成功',$data);
            //exit;
        }
        else{
            return 0;
            //$this->jsonData(1,'失败');
            //exit;
        }
        
	}
	
	
	//第一阶段 记录结束游戏（提交个人信息就算结束）  http://rongtai.loc/cms/home/game1end
	public function game1end(){
		
		if(isset($_SESSION['game_id'])){
		    unset($_SESSION['game_id']);
		}
		
        return true;
        
	}
	
	
    //（本接口未使用）上传图片 提交 http://rongtai.loc/cms/home/game1uploadheadpic?myword=说句心里话&filestring=照片文件二进制码之类的内容
    public function game1uploadheadpic(){
    	
    	//$_POST['filestring']='aaa';
    	//$this->game1end();
    	
    	$game_id=$this->game1start();
    	
    	
    	$style=1;
    	$user_id=0;
    	
		//if(isset($_REQUEST['style']) && ($_REQUEST['style']==1 || $_REQUEST['style']==2 || $_REQUEST['style']==3) ){
		//	$style=$_REQUEST['style'];
		//}
		//else{
		//	$this->jsonData(1,'参数错误');
        //    exit;
		//}
		
		
		
		
        if(isset($_REQUEST['myword']) && $_REQUEST['myword']!=''){
            $myword=$_REQUEST['myword'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
		
        if(isset($_REQUEST['is_android']) && $_REQUEST['is_android']==1){
            $is_android=$_REQUEST['is_android'];
        }
        else{
            $is_android=0;
        }
        
        
        
        
        
        
        
        $is_android=1;
        
		//$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".rand(10,99).".png";
		$path = "game_".$game_id."_headpic_".$style."_time_".date('ymdHis')."_".$user_id.".png";
		
		//$path='game_1_style_1_time_150716161543_1.png';
		$dest = BASE_UPLOAD_HEADPIC_PATH.$path;
		//echo $dest;exit;
		
		
		/*
		//base64模式接受上传数据
		$f = fopen($dest,'w');
		$img = 'data:image/jpeg;base64,/9j/4QAYRXhhtcHRrPXYv//Z';
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace('data:image/gif;base64,', '', $img);
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$datas = base64_decode($img);
		fwrite($f,$datas);
		fclose($f);
		*/
		
		
		
		$pic_imagerotate='';
		
		if($_FILES){
			//上传方式：表单
			$input = key($_FILES);
			if(!move_uploaded_file($_FILES[$input]['tmp_name'],$dest)) {
				$this->jsonData(1,'失败');
		        exit;
			}
			else{
				
				//判断EXIF头信息模式解决旋转90度问题
				$exif = exif_read_data($dest);
				$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
				if($ort==""){
					$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
				}
				switch($ort){
					case 1: // nothing
						break;
        			case 2: // horizontal flip
        				break;
	                case 3: // 180 rotate left  //向左旋转180度
	                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
	                    $pic_imagerotate='180';
	                    break;
	                case 4: // vertical flip
            			break;
            		case 5: // vertical flip + 90 rotate right
				        $pic_imagerotate='-90';
				        break;
	                case 6: // 90 rotate right  //向右旋转90度
	                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
	                    $pic_imagerotate='-90';
	                    break;
	                case 7: // horizontal flip + 90 rotate right
				        $pic_imagerotate='-90';
				        break;
	                case 8:    // 90 rotate left  //向左旋转90度
	                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
	                    $pic_imagerotate='90';
	                    break;
	            }
	            //echo "<pre>";print_r($exif);exit;
	            //var_dump($pic_imagerotate);exit;
			}
		}
		elseif(isset($_POST['filestring'])){ 
			
			//上传方式：原始POST
			//$f = fopen($dest,'w');
			//fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = $GLOBALS['HTTP_RAW_POST_DATA'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = $_POST['filestring'];
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题（示例：http://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation）
				$f = fopen($dest,'w');
				fwrite($f,$_POST['filestring']);
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
            
			
			
		}
		elseif(isset($GLOBALS['HTTP_RAW_POST_DATA'])){ 
			//上传方式：原始POST
			//$f = fopen($dest,'w');
			//fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = $GLOBALS['HTTP_RAW_POST_DATA'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = $GLOBALS['HTTP_RAW_POST_DATA'];
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题（示例：http://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation）
				$f = fopen($dest,'w');
				fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
            
			
			
		}
		else{ 
			//上传方式：客户端提交
			//$f = fopen($dest,'w');
			//fwrite($f,file_get_contents('php://input'));
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = file_get_contents('php://input');
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = file_get_contents('php://input');
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题
				$f = fopen($dest,'w');
				fwrite($f,file_get_contents('php://input'));
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
			
			
			
		}
		
		
		//echo $exif['Orientation'];exit;
		//echo "<pre>";print_r($exif);exit;
		//$dest='D:\www\ouliwei\test\ouliwei\cms/public/web_pic/game_1_style_1_time_150716161543_1.jpg';
		//echo $dest;exit;
		
		$file_type=$this->get_file_type($dest); 
		//$file_type='png';
		//echo $file_type;exit;
		if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png"){
			@unlink($dest);
			$this->jsonData(1,'图片上传限定jpg、gif、png类型');
		    exit;
		}
		
		
		$file_z=filesize($dest);
		$f_size_limit_byte=BASE_FILE_SIZE_LIMIT*1024*1024;
		
		if ($file_z>$f_size_limit_byte){
			@unlink($dest);
			$this->jsonData(1,'图片上传限定'.BASE_FILE_SIZE_LIMIT.'M');
		    exit;
		}
		
		
		/*
		//裁切
		$path_egg = "game_".$game_id."_egg_".$style."_time_".date('ymdHis')."_".$user_id.".png";
		$dest_egg = BASE_PIC_RESIZE_PATH.$path_egg;
		$out_path=$dest_egg;
		$org_path=$dest;
    	$src_w=540;
    	$src_h=588;
    	$this->zoom($org_path,$out_path,$src_w,$src_h);	
		//裁切
		*/
		
		
		$cur_time=time();
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET headpic='".addslashes($path)."' 
        , imagerotate='".addslashes($pic_imagerotate)."' 
        , addtime_headpic='".date("Y-m-d H:i:s",$cur_time)."' 
        , myword='".addslashes($myword)."' 
        where id='".addslashes($game_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
        
        //$data['headpic_filename']=$path;
        $data['game_id']=$game_id;
        $data['myword']=$myword;
		$data['headpic']=BASE_URL."/public/web_headpic/".$path;
		//$data['style']=$style;
		//$data['point_total']=$point_total;
        $this->jsonData(0,'成功',$data);
        
        
        
    }
    
    
    
    //上传语音 提交  http://rongtai.loc/cms/home/headpic_wx?myword=说句心里话&token=xxxxxxx&media_id=yyyyyyy&openid=abc123
    public function headpic_wx(){
    	
    	//$this->game1end();
    	
    	$game_id=$this->game1start();
    	//echo $game_id;exit;
    	
    	
    	$style=1;
    	$user_id=0;
    	$is_android=1;
    	
    	
        if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
            $openid=$_REQUEST['openid'];
        }
        else{
        	//$openid='';
            $this->jsonData(1,'参数错误');
            exit;
        }
        
    	
        if(isset($_REQUEST['myword']) && $_REQUEST['myword']!=''){
            $myword=$_REQUEST['myword'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        if(isset($_REQUEST['type']) && $_REQUEST['type']!=""){
            $type=$_REQUEST['type'];
        }
        else{
            $type=".png";
        }
        
        
        if(isset($_REQUEST['media_id']) && $_REQUEST['media_id']!=""){
            $media_id=$_REQUEST['media_id'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        if(isset($_REQUEST['token']) && $_REQUEST['token']!=""){
            $token=$_REQUEST['token'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        
        
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' and headpic!='' and mobile!='' " )->select();
        if(!empty($game_info)){
        	$this->jsonData(1,'您已经参加过上传照片');
            exit;
        }
        
        
        
        
        
        
        if(!empty($token) && !empty($media_id)){
        	
        	
			//获取微信媒体文件
			$get_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$media_id.'';
			//echo $get_url;exit;
			$get_return = file_get_contents($get_url);
			//$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
			
			$path = "game_".$game_id."_headpic_".date('ymdHis')."_".rand(10,99)."_".$user_id.$type;
			$dest = BASE_UPLOAD_HEADPIC_PATH.$path;
			//echo $dest;exit;
			
			
			if(!empty($get_return)){
				
				
				//模仿复制 php://input 模式上传
				$f = fopen($dest,'w');
				fwrite($f,$get_return);
				fclose($f);
				
				
				
				$cur_time=time();
				$UserMod = M('game');
		        $sql=sprintf("UPDATE %s SET headpic='".addslashes($path)."' 
		        , addtime_headpic='".date("Y-m-d H:i:s",$cur_time)."' 
		        , myword='".addslashes($myword)."' 
		        , openid='".addslashes($openid)."' 
		        where id='".addslashes($game_id)."' 
		        ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
		        
		        
		        
				$data['game_id']=$game_id;
				$data['myword']=$myword;
				$data['headpic']=BASE_URL."/public/web_headpic/".$path;
				$this->jsonData(0,'成功',$data);
	        }
	        else{
	        	$this->jsonData(1,'媒体文件获取失败');
	        	exit;
	        }
	        
        	
        }
        else{
        	$this->jsonData(1,'参数错误');
            exit;
        }
        
        
    }
    
    
	
    //上传语音 提交  http://rongtai.loc/cms/home/voice_wx?token=xxxxxxx&media_id=yyyyyyy&openid=abc123
    public function voice_wx(){
    	
    	//$this->game1end();
    	
    	$game_id=$this->game1start();
    	//echo $game_id;exit;
    	
    	
    	$style=1;
    	$user_id=0;
    	$is_android=1;
    	
    	
    	if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
            $openid=$_REQUEST['openid'];
        }
        else{
        	//$openid='';
            $this->jsonData(1,'参数错误');
            exit;
        }
        
    	
        if(isset($_REQUEST['type']) && $_REQUEST['type']!=""){
            $type=$_REQUEST['type'];
        }
        else{
            $type=".mp3";
        }
        
        
        if(isset($_REQUEST['media_id']) && $_REQUEST['media_id']!=""){
            $media_id=$_REQUEST['media_id'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        if(isset($_REQUEST['token']) && $_REQUEST['token']!=""){
            $token=$_REQUEST['token'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' and voice!='' and mobile!='' " )->select();
        if(!empty($game_info)){
        	$this->jsonData(1,'您已经参加过语音大声说出你的爱');
            exit;
        }
        
        
        
        
        if(!empty($token) && !empty($media_id)){
        	
        	
			//获取微信媒体文件
			$get_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$media_id.'';
			//echo $get_url;exit;
			$get_return = file_get_contents($get_url);
			//$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
			
			$path = "game_".$game_id."_voice_".date('ymdHis')."_".rand(10,99)."_".$user_id.$type;
			$dest = BASE_UPLOA_VOICE_PATH.$path;
			//echo $dest;exit;
			
			
			if(!empty($get_return)){
				
				
				//模仿复制 php://input 模式上传
				$f = fopen($dest,'w');
				fwrite($f,$get_return);
				fclose($f);
				
				
				$cur_time=time();
				$UserMod = M('game');
		        $sql=sprintf("UPDATE %s SET voice='".addslashes($path)."' 
		        , addtime_voice='".date("Y-m-d H:i:s",$cur_time)."' 
		        , openid='".addslashes($openid)."' 
		        , token='".addslashes($token)."' 
		        , media_id='".addslashes($media_id)."' 
		        where id='".addslashes($game_id)."' 
		        ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
		        
		        
				$data['game_id']=$game_id;
				$data['voice_url']=BASE_URL."/public/web_voice/".$path;
		        $this->jsonData(0,'成功',$data);
	        }
	        else{
	        	$this->jsonData(1,'媒体文件获取失败');
	        	exit;
	        }
	        
        	
        }
        else{
        	$this->jsonData(1,'参数错误');
            exit;
        }
        
        
    }
    
    
    
    
	
	//用户信息 提交 http://rongtai.loc/cms/home/game1userinfo?realname=小明&mobile=13988887777&region=上海&openid=abc123&wx_nickname=小张&wx_headpic=http://rongtai.maxbund.com/images/share.png
	public function game1userinfo(){
		
    	$game_id=$this->game1start();
    	//echo $game_id;exit;
		
		
    	if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
            $openid=$_REQUEST['openid'];
        }
        else{
        	//$openid='';
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=""){
		    $realname=$_REQUEST['realname'];
		}
		else{
		    $this->jsonData(1,'参数错误');
		    exit;
		}
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile']!=""){
		    $mobile=$_REQUEST['mobile'];
		}
		else{
		    $this->jsonData(1,'参数错误');
		    exit;
		}
		
		if(isset($_REQUEST['region']) && $_REQUEST['region']!=""){
		    $region=$_REQUEST['region'];
		}
		else{
			$region='';
			//$this->jsonData(1,'参数错误');
		    //exit;
		}
		
		
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=""){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		if(isset($_REQUEST['wx_headpic']) && $_REQUEST['wx_headpic']!=""){
		    $wx_headpic=$_REQUEST['wx_headpic'];
		}
		else{
			$wx_headpic='';
		}
		
		
		
		if(isset($_REQUEST['type']) && $_REQUEST['type']!=""){
            $type=$_REQUEST['type'];
        }
        else{
            $type=".png";
        }
        
        
        
        $user_id=0;
        
        
        
		
		$CityMod = M('game');
        $game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        
        if(isset($game_info[0]['headpic']) && $game_info[0]['headpic']!=''){
        	$finish_headpic=1;
        }
        else{
        	$finish_headpic=0;
        	//$this->jsonData(1,'请先上传照片');
	        //exit;
        }
        
        
        if(isset($game_info[0]['voice']) && $game_info[0]['voice']!=''){
        	$finish_voice=1;
        }
        else{
        	$finish_voice=0;
        	//$this->jsonData(1,'请先语音大声说出你的爱');
	        //exit;
        }
        
        
        
        
        if($finish_headpic==0 && $finish_voice==0){
        	$this->jsonData(1,'请先上传照片或语音大声说出你的爱');
	        exit;
        }
        
        
        
        //是否参加过活动 - 上传头像
		$CityMod = M('game');
        $game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        if(isset($game_info[0]['headpic']) && $game_info[0]['headpic']!=''){
        	
        	$CityMod_history = M('game');
        	$game_info_history = $CityMod_history->where(" id!='".addslashes($game_id)."' and headpic!='' and mobile='".addslashes($mobile)."' " )->select();
        	if(!empty($game_info_history)){
        		$this->jsonData(1,'您已经参加过上传照片');
	            exit;
        	}
        	
        	$CityMod_history = M('game');
        	$game_info_history = $CityMod_history->where(" id!='".addslashes($game_id)."' and headpic!='' and openid='".addslashes($openid)."' " )->select();
        	if(!empty($game_info_history)){
        		$this->jsonData(1,'您已经参加过上传照片');
	            exit;
        	}
        	
        }
        
        
        //是否参加过活动 - 语音
		$CityMod = M('game');
        $game_info = $CityMod->where(" id='".addslashes($game_id)."' " )->select();
        if(isset($game_info[0]['voice']) && $game_info[0]['voice']!=''){
        	
        	$CityMod_history = M('game');
        	$game_info_history = $CityMod_history->where(" id!='".addslashes($game_id)."' and voice!='' and mobile='".addslashes($mobile)."' " )->select();
        	if(!empty($game_info_history)){
        		$this->jsonData(1,'您已经参加过语音大声说出你的爱');
	            exit;
        	}
        	
        	$CityMod_history = M('game');
        	$game_info_history = $CityMod_history->where(" id!='".addslashes($game_id)."' and voice!='' and openid='".addslashes($openid)."' " )->select();
        	if(!empty($game_info_history)){
        		$this->jsonData(1,'您已经参加过语音大声说出你的爱');
	            exit;
        	}
        	
        }
        
        
        
		$cur_time=time();
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET realname='".addslashes($realname)."' 
        , mobile='".addslashes($mobile)."' 
        , region='".addslashes($region)."' 
        , addtime_userinfo='".date("Y-m-d H:i:s",$cur_time)."' 
        , openid='".addslashes($openid)."' 
        , wx_nickname='".addslashes($wx_nickname)."' 
        , wx_headpic='".addslashes($wx_headpic)."' 
        where id='".addslashes($game_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
        
        
        if(!empty($wx_headpic)){
			//获取微信媒体文件
			$get_url = $wx_headpic;
			$get_return = file_get_contents($get_url);
			//$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
			
			$path = "wx_".$game_id."_headpic_".date('ymdHis')."_".rand(10,99)."_".$user_id.$type;
			$dest = BASE_WX_HEADPIC_PATH.$path;
			//echo $dest;exit;
			
			if(!empty($get_return)){
				
				//模仿复制 php://input 模式上传
				$f = fopen($dest,'w');
				fwrite($f,$get_return);
				fclose($f);
				
				
				$UserMod = M('game');
		        $sql=sprintf("UPDATE %s SET 
		         wx_headpic_path='".addslashes($path)."' 
		        where id='".addslashes($game_id)."' 
		        ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
		        
				
			}
        }
        
        
        
        
        
        
        
        
        //抽奖
        /*
        第一轮：u盘，80中1，总数300个。
        */
        $is_prize=0;
        $all_num=300;   //可中奖总数
        $rand_num=rand(1, 80);   //30分之1的概率    //$rand_num=rand(1, 80);
        
        //if($from_name=='abc789production' || $to_name=='abc789production' ){
        	//$rand_num=1;
        //}
        
    	$is_prize_key=0;   //rand_num等于is_prize_key的时候，为中奖。
        
        if($rand_num==$is_prize_key){
        	$CityMod = M('game');
        	
	        //$data_num = $CityMod->field('count(id) as num ')->where(" is_prize=1 " )->select();  //U盘
	        $data_num = $CityMod->field('count(id) as num ')->where(" is_prize=1 " )->select();  //蚝油
	        
	        $data_num=$data_num[0]['num'];
	        
	        //当前中奖人数小于可中奖总数
	        if($data_num<$all_num){
	        	
	        	$CityMod = M('game');
	        	
	        	//中奖的判断发生在填写个人信息之后，程序知道手机是什么，可判断是否中过奖。
		        $person_zhong = $CityMod->field('count(id) as num ')->where(" is_prize>0 and mobile='".addslashes($mobile)."' " )->select();
		        $person_zhong=$person_zhong[0]['num'];
		        
		        //中奖的判断发生在填写个人信息之前，程序不知道手机是什么，无法判断是否中过奖。
		        //$person_zhong=0;
		        
		        
		        //当前手机没中过奖
		        if($person_zhong==0){
		        	//第一轮：is_prize=1。第二轮：is_prize=2，以此类推……
		        	//$is_prize=1;  //U盘
		        	$is_prize=1;  //蚝油
		        	
		        	$UserMod = M('game');
			        $sql=sprintf("UPDATE %s SET is_prize='".$is_prize."' 
			        where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
			        //echo $sql;
			        $result = $UserMod->execute($sql);
			        
		        }
		        
	        }
	        
        }
        
        
        //整个游戏流程结束的时候调用
        $this->game1end();
        
        $data['game_id']=$game_id;
		$data['realname']=$realname;
		$data['mobile']=$mobile;
		$data['region']=$region;
		$data['openid']=$openid;
		$data['wx_nickname']=$wx_nickname;
		$data['wx_headpic']=$wx_headpic;
		$data['wx_headpic_path']=BASE_URL."/public/wx_headpic/".$path;
        $this->jsonData(0,'成功',$data);
        
        
	}
	
	
	
	
	//是否参与过 上传照片  home/is_headpic_wx?openid=abc123
	public function is_headpic_wx(){
		
    	if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
            $openid=$_REQUEST['openid'];
        }
        else{
        	//$openid='';
            $this->jsonData(1,'参数错误');
            exit;
        }
        
		
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' and headpic!='' and mobile!='' " )->select();
        if(!empty($game_info)){
        	$data['is_finish']=1;
        }
        else{
        	$data['is_finish']=0;
        }
        
        $this->jsonData(0,'成功',$data);
        
	}
	
	
	
	//是否参与过 语音  home/is_voice_wx?openid=abc123
	public function is_voice_wx(){
		
		
    	if(isset($_REQUEST['openid']) && $_REQUEST['openid']!=''){
            $openid=$_REQUEST['openid'];
        }
        else{
        	//$openid='';
            $this->jsonData(1,'参数错误');
            exit;
        }
        
		
		$CityMod = M('game');
        $game_info = $CityMod->where(" openid='".addslashes($openid)."' and voice!='' and mobile!='' " )->select();
        if(!empty($game_info)){
        	$data['is_finish']=1;
        }
        else{
        	$data['is_finish']=0;
        }
        
        $this->jsonData(0,'成功',$data);
        
        
	}
	
	
	
	
	//投票规则：每个用户每天可以对图文和语音组各进行一次投票。第一天投A  第二天也可以投A。每人每天可以对语音组投票一次  对图文组投票一次。某个IP，一天只能投10票。
	
	
    //投票 针对图文  http://rongtai.loc/cms/home/vote_photo?photo_user_id=5&wx_openid=abc123&wx_nickname=小张&wx_headpic=http://rongtai.maxbund.com/images/share.png
    public function vote_photo(){
    	
    	
    	$vote_datetime=date('YmdHis');
    	if($vote_datetime>=20160509000000){
    		$this->jsonData(1,'投票活动已经结束');
            exit;
    	}
    	
        if(isset($_REQUEST['wx_openid']) && $_REQUEST['wx_openid']!=''){
            $wx_openid=$_REQUEST['wx_openid'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
    	
        if(isset($_REQUEST['photo_user_id']) && $_REQUEST['photo_user_id']!=''){
            $photo_user_id=$_REQUEST['photo_user_id'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        
        if($photo_user_id>=1 && $photo_user_id<=10000){
            
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=""){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		if(isset($_REQUEST['wx_headpic']) && $_REQUEST['wx_headpic']!=""){
		    $wx_headpic=$_REQUEST['wx_headpic'];
		}
		else{
			$wx_headpic='';
		}
		
        
        
        
        $vote_date_limit=10;   //1个IP单日最多对同1个人投10票
        $cur_time=time();
        $addtime=date("Y-m-d H:i:s",$cur_time);
        $addtime_start=date("Y-m-d",$cur_time)." 00:00:00";
        $addtime_end=date("Y-m-d",$cur_time)." 23:59:59";
        
		$customer_ip=$this->get_customer_ip();
        
        
		$CityMod = M('photo_user_history');
        //$vote_info = $CityMod->where(" wx_openid='".addslashes($wx_openid)."' and photo_user_id='".addslashes($photo_user_id)."' " )->select();
        $vote_info = $CityMod->where(" wx_openid='".addslashes($wx_openid)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."' " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info)){
        	$this->jsonData(1,'您今天已经投过票了');
            exit;
        }
        
        
        $CityMod = M('photo_user_history');
        $vote_info = $CityMod->where(" customer_ip='".addslashes($customer_ip)."' and photo_user_id='".addslashes($photo_user_id)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."'  " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info) && count($vote_info)>=$vote_date_limit){
        	$this->jsonData(1,'您的IP已经达到投票次数限制');
            exit;
        }
        
        
        
        $UserMod = M('photo_user_history');
        $sql=sprintf("INSERT %s SET photo_user_id='".addslashes($photo_user_id)."' 
        , modify_time='".$cur_time."' 
        , create_time='".$cur_time."' 
        , addtime='".$addtime."' 
        , wx_openid='".addslashes($wx_openid)."' 
        , wx_nickname='".addslashes($wx_nickname)."' 
        , wx_headpic='".addslashes($wx_headpic)."' 
        , customer_ip='".addslashes($customer_ip)."' 
        ", $UserMod->getTableName() );
        //echo $sql;
        $result = $UserMod->execute($sql);
        
        
        
		$andsql=" and photo_user_id='".addslashes($photo_user_id)."' ";
    	$CityMod = M('photo_user_history');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $ticket_number=$rst[0]['num'];
        //echo $ticket_number;exit;
        //echo "<pre>";print_r($rst);exit;
        
        
    	$UserMod = M('photo_user');
        $sql=sprintf("UPDATE %s SET 
         ticket_number='".addslashes($ticket_number)."'
        where id=".addslashes($photo_user_id)." 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
		$data['photo_user_id']=$photo_user_id;
		$data['ticket_number']=$ticket_number;
		$data['wx_openid']=$wx_openid;
		$data['wx_nickname']=$wx_nickname;
		$data['wx_headpic']=$wx_headpic;
		$this->jsonData(0,'成功',$data);
		
    }
    
	
	
    //投票 针对语音  http://rongtai.loc/cms/home/vote_voice?voice_user_id=5&wx_openid=abc123&wx_nickname=小张&wx_headpic=http://rongtai.maxbund.com/images/share.png
    public function vote_voice(){
    	
    	$vote_datetime=date('YmdHis');
    	if($vote_datetime>=20160509000000){
    		$this->jsonData(1,'投票活动已经结束');
            exit;
    	}
    	
    	
        if(isset($_REQUEST['wx_openid']) && $_REQUEST['wx_openid']!=''){
            $wx_openid=$_REQUEST['wx_openid'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
    	
        if(isset($_REQUEST['voice_user_id']) && $_REQUEST['voice_user_id']!=''){
            $voice_user_id=$_REQUEST['voice_user_id'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        if($voice_user_id>=1 && $voice_user_id<=10000){
            
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
		if(isset($_REQUEST['wx_nickname']) && $_REQUEST['wx_nickname']!=""){
		    $wx_nickname=$_REQUEST['wx_nickname'];
		}
		else{
			$wx_nickname='';
		}
		
		if(isset($_REQUEST['wx_headpic']) && $_REQUEST['wx_headpic']!=""){
		    $wx_headpic=$_REQUEST['wx_headpic'];
		}
		else{
			$wx_headpic='';
		}
		
        
        
        
        $vote_date_limit=10;   //1个IP单日最多对同1个人投10票
        $cur_time=time();
        $addtime=date("Y-m-d H:i:s",$cur_time);
        $addtime_start=date("Y-m-d",$cur_time)." 00:00:00";
        $addtime_end=date("Y-m-d",$cur_time)." 23:59:59";
        
		$customer_ip=$this->get_customer_ip();
        
        
		$CityMod = M('voice_user_history');
        //$vote_info = $CityMod->where(" wx_openid='".addslashes($wx_openid)."' and voice_user_id='".addslashes($voice_user_id)."' " )->select();
        $vote_info = $CityMod->where(" wx_openid='".addslashes($wx_openid)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."' " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info)){
        	$this->jsonData(1,'您今天已经投过票了');
            exit;
        }
        
        
        $CityMod = M('voice_user_history');
        $vote_info = $CityMod->where(" customer_ip='".addslashes($customer_ip)."' and voice_user_id='".addslashes($voice_user_id)."' and addtime>='".addslashes($addtime_start)."' and addtime<='".addslashes($addtime_end)."'  " )->select();
        //var_dump($vote_info);exit;
        if(!empty($vote_info) && count($vote_info)>=$vote_date_limit){
        	$this->jsonData(1,'您的IP已经达到投票次数限制');
            exit;
        }
        
        
        
        $UserMod = M('voice_user_history');
        $sql=sprintf("INSERT %s SET voice_user_id='".addslashes($voice_user_id)."' 
        , modify_time='".$cur_time."' 
        , create_time='".$cur_time."' 
        , addtime='".$addtime."' 
        , wx_openid='".addslashes($wx_openid)."' 
        , wx_nickname='".addslashes($wx_nickname)."' 
        , wx_headpic='".addslashes($wx_headpic)."' 
        , customer_ip='".addslashes($customer_ip)."' 
        ", $UserMod->getTableName() );
        //echo $sql;
        $result = $UserMod->execute($sql);
        
        
        
		$andsql=" and voice_user_id='".addslashes($voice_user_id)."' ";
    	$CityMod = M('voice_user_history');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $ticket_number=$rst[0]['num'];
        //echo $ticket_number;exit;
        //echo "<pre>";print_r($rst);exit;
        
        
    	$UserMod = M('voice_user');
        $sql=sprintf("UPDATE %s SET 
         ticket_number='".addslashes($ticket_number)."'
        where id=".addslashes($voice_user_id)." 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        
		$data['voice_user_id']=$voice_user_id;
		$data['ticket_number']=$ticket_number;
		$data['wx_openid']=$wx_openid;
		$data['wx_nickname']=$wx_nickname;
		$data['wx_headpic']=$wx_headpic;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    
    
    
    
    
    //被投票者列表 针对图文  http://rongtai.loc/cms/home/list_photo
    public function list_photo(){
    	
    	$CityMod = M('photo_user');
        $listing = $CityMod->field('id,nickname,myword,ticket_number,headpic')->where(" 1 ")->order('id desc')->select();
        
        if(!empty($listing)){
        	foreach($listing as $k=>$v){
        		//$listing[$k]['headpic']=BASE_URL."/public/vote_photo_headpic/".$v['id'].".jpg";
        		$listing[$k]['media']=BASE_URL."/public/vote_photo_media/".$v['id'].".png";
        	}
        }
        
		$data=$listing;
		$this->jsonData(0,'成功',$data);
		
    }
    
    
    
    //被投票者列表 针对图文  http://rongtai.loc/cms/home/list_voice
    public function list_voice(){
    	
    	$CityMod = M('voice_user');
        $listing = $CityMod->field('id,nickname,ticket_number,headpic')->where(" 1 ")->order('id asc')->select();
        
        if(!empty($listing)){
        	foreach($listing as $k=>$v){
        		//$listing[$k]['headpic']=BASE_URL."/public/vote_voice_headpic/".$v['id'].".jpg";
        		$listing[$k]['media']=BASE_URL."/public/vote_voice_media/".$v['id'].".mp3";
        	}
        }
        
		$data=$listing;
		$this->jsonData(0,'成功',$data);
		
    }
    
	
	/////////////////////以下不是本项目的接口//////////////////////////////////////
	
	
	
	
	
	
	
	
	
    //上传语音 提交 http://rongtai.loc/home/game1uploadvoice
    public function game1uploadvoice(){
    	
    	//$this->game1end();
    	
    	$game_id=$this->game1start();
    	//echo $game_id;exit;
    	
    	$style=1;
    	$user_id=0;
    	
		//if(isset($_REQUEST['style']) && ($_REQUEST['style']==1 || $_REQUEST['style']==2 || $_REQUEST['style']==3) ){
		//	$style=$_REQUEST['style'];
		//}
		//else{
		//	$this->jsonData(1,'参数错误');
        //    exit;
		//}
		
		
        if(isset($_REQUEST['is_android']) && $_REQUEST['is_android']==1){
            $is_android=$_REQUEST['is_android'];
        }
        else{
            $is_android=0;
        }
        
        $is_android=1;
        
		//$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".rand(10,99).".png";
		$path = "game_".$game_id."_voice_".$style."_time_".date('ymdHis')."_".$user_id.".mp3";
		
		//$path='game_1_style_1_time_150716161543_1.png';
		$dest = BASE_UPLOA_VOICE_PATH.$path;
		//echo $dest;exit;
		
		
		
		
		/*
		//base64模式接受上传数据
		$f = fopen($dest,'w');
		$img = 'data:image/jpeg;base64,/9j/4QAYRXhhtcHRrPXYv//Z';
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace('data:image/gif;base64,', '', $img);
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$datas = base64_decode($img);
		fwrite($f,$datas);
		fclose($f);
		*/
		
		
		
		$pic_imagerotate='';
		
		if($_FILES){
			//上传方式：表单
			$input = key($_FILES);
			if(!move_uploaded_file($_FILES[$input]['tmp_name'],$dest)) {
				$this->jsonData(1,'失败');
		        exit;
			}
			else{
				
				//判断EXIF头信息模式解决旋转90度问题
				$exif = exif_read_data($dest);
				$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
				if($ort==""){
					$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
				}
				switch($ort){
					case 1: // nothing
						break;
        			case 2: // horizontal flip
        				break;
	                case 3: // 180 rotate left  //向左旋转180度
	                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
	                    $pic_imagerotate='180';
	                    break;
	                case 4: // vertical flip
            			break;
            		case 5: // vertical flip + 90 rotate right
				        $pic_imagerotate='-90';
				        break;
	                case 6: // 90 rotate right  //向右旋转90度
	                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
	                    $pic_imagerotate='-90';
	                    break;
	                case 7: // horizontal flip + 90 rotate right
				        $pic_imagerotate='-90';
				        break;
	                case 8:    // 90 rotate left  //向左旋转90度
	                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
	                    $pic_imagerotate='90';
	                    break;
	            }
	            //echo "<pre>";print_r($exif);exit;
	            //var_dump($pic_imagerotate);exit;
			}
		}
		elseif(isset($_POST['filestring'])){ 
			
			//上传方式：原始POST
			//$f = fopen($dest,'w');
			//fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = $GLOBALS['HTTP_RAW_POST_DATA'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = $_POST['filestring'];
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题（示例：http://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation）
				$f = fopen($dest,'w');
				fwrite($f,$_POST['filestring']);
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
            
			
			
		}
		elseif(isset($GLOBALS['HTTP_RAW_POST_DATA'])){ 
			//上传方式：原始POST
			//$f = fopen($dest,'w');
			//fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = $GLOBALS['HTTP_RAW_POST_DATA'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = $GLOBALS['HTTP_RAW_POST_DATA'];
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题（示例：http://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation）
				$f = fopen($dest,'w');
				fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
            
			
			
		}
		else{ 
			//上传方式：客户端提交
			//$f = fopen($dest,'w');
			//fwrite($f,file_get_contents('php://input'));
			//fclose($f);
			
			//base64模式接受上传数据
			/*
			$f = fopen($dest,'w');
			$img = file_get_contents('php://input');
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace('data:image/gif;base64,', '', $img);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$datas = base64_decode($img);
			fwrite($f,$datas);
			fclose($f);
			*/
			
			if($is_android==1){
				$f = fopen($dest,'w');
				$img = file_get_contents('php://input');
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace('data:image/gif;base64,', '', $img);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$datas = base64_decode($img);
				fwrite($f,$datas);
				fclose($f);
			}
			else{
				//判断EXIF头信息模式解决旋转90度问题
				$f = fopen($dest,'w');
				fwrite($f,file_get_contents('php://input'));
				fclose($f);
			}
			
			$exif = exif_read_data($dest);
			$ort = isset($exif['Orientation'])?$exif['Orientation']:"";
			if($ort==""){
				$ort = isset($exif['IFD0']['Orientation'])?$exif['IFD0']['Orientation']:"";
			}
			switch($ort){
				case 1: // nothing
					break;
    			case 2: // horizontal flip
    				break;
                case 3: // 180 rotate left  //向左旋转180度
                    //$image->imagerotate($upload_path . $newfilename, 180, -1);
                    $pic_imagerotate='180';
                    break;
                case 4: // vertical flip
        			break;
        		case 5: // vertical flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 6: // 90 rotate right  //向右旋转90度
                    //$image->imagerotate($upload_path . $newfilename, -90, -1);
                    $pic_imagerotate='-90';
                    break;
                case 7: // horizontal flip + 90 rotate right
			        $pic_imagerotate='-90';
			        break;
                case 8:    // 90 rotate left  //向左旋转90度
                    //$image->imagerotate($upload_path . $newfilename, 90, -1);
                    $pic_imagerotate='90';
                    break;
            }
			
			
			
		}
		
		
		//echo $exif['Orientation'];exit;
		//echo "<pre>";print_r($exif);exit;
		//$dest='D:\www\ouliwei\test\ouliwei\cms/public/web_pic/game_1_style_1_time_150716161543_1.jpg';
		
		
		//$file_type=$this->get_file_type($dest); 
		//if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png"){
		//	@unlink($dest);
		//	$this->jsonData(1,'图片上传限定jpg、gif、png类型');
		//    exit;
		//}
		
		
		$file_z=filesize($dest);
		$f_size_limit_byte=BASE_FILE_SIZE_LIMIT*1024*1024;
		
		if ($file_z>$f_size_limit_byte){
			@unlink($dest);
			$this->jsonData(1,'图片上传限定'.BASE_FILE_SIZE_LIMIT.'M');
		    exit;
		}
		
		
		
		
		/*
		//裁切
		$path_egg = "game_".$game_id."_egg_".$style."_time_".date('ymdHis')."_".$user_id.".png";
		$dest_egg = BASE_PIC_RESIZE_PATH.$path_egg;
		$out_path=$dest_egg;
		$org_path=$dest;
    	$src_w=540;
    	$src_h=588;
    	$this->zoom($org_path,$out_path,$src_w,$src_h);	
		//裁切
		*/
		
		
		$cur_time=time();
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET voice='".addslashes($path)."' 
        , addtime_voice='".date("Y-m-d H:i:s",$cur_time)."' 
        where id='".addslashes($game_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
        echo "finish";exit;
        exit;
        
        
        //$UserMod = M('user');
        //$sql=sprintf("UPDATE %s SET point_total='".$point_total."' 
        //where id='".addslashes($user_id)."' 
        //", $UserMod->getTableName() );
        //$result = $UserMod->execute($sql);
        
        
        $data['headpic_filename']=$path;
		$data['headpic']=BASE_URL."/public/web_pic/".$path;
		$data['headegg']=BASE_URL."/public/web_resize/".$path_egg;
		$data['style']=$style;
		$data['point_total']=$point_total;
        $this->jsonData(0,'成功',$data);
        
    }
    
    
	
    
    
	
    
    
	
	//灭火器   授权页  提交姓名、电话、灭火数量   http://miehuoqi.loc/cms/home/userinfo?realname=小明&phone=13988887777&number=15
	// http://lafan.loc/home/userinfo?realname=小明&phone=13988887777&number=15
	public function userinfo(){
		
		if(isset($_REQUEST['realname']) && $_REQUEST['realname']!=""){
		    $realname=$_REQUEST['realname'];
		}
		else{
		    $this->jsonData(1,'参数错误');
		    exit;
		}
		
		if(isset($_REQUEST['phone']) && $_REQUEST['phone']!=""){
		    $phone=$_REQUEST['phone'];
		}
		else{
		    $this->jsonData(1,'参数错误');
		    exit;
		}
		
		if(isset($_REQUEST['number']) && $_REQUEST['number']!=""){
		    $number=$_REQUEST['number'];
		}
		else{
			$number='0';
		    //$this->jsonData(1,'参数错误');
		    //exit;
		}
		
		
		$cli_os=$this->get_client_os();
        $cur_time=time();
        
        
        
        
        //查看当前用户是否关注过官方公众号
		$access_token=$this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$_SESSION['WX_INFO']['openid']."&lang=zh_CN";
		$user_info = json_decode($this->httpGet($url),true);
		//echo "dddee<pre>";print_r($user_info);exit;
		//$user_info['subscribe']    0 or 1
		if(isset($user_info['subscribe']) && $user_info['subscribe']==1){
			$subscribe=1;
		}
		else{
			$subscribe=0;
		}
		
		
		//来源
		if(isset($_SESSION['source'])){
			$source=$_SESSION['source'];
		}
		else{
			$source='';
		}
		
		
        $UserMod = M('game');
        $sql=sprintf("INSERT %s SET cli_os='".$cli_os."' 
        , modify_time='".$cur_time."' 
        , create_time='".$cur_time."' 
        , addtime='".date("Y-m-d H:i:s",$cur_time)."' 
        , realname='".addslashes($realname)."' 
        , phone='".addslashes($phone)."' 
        , number='".addslashes($number)."' 
        , subscribe='".addslashes($subscribe)."' 
        , source='".addslashes($source)."' 
        ", $UserMod->getTableName() );
        //echo $sql;
        $result = $UserMod->execute($sql);
        
        
		
		
        if(1==1){
        	
            $data['realname']=$realname;
            $data['phone']=$phone;
            $data['number']=$number;
            $data['subscribe']=$subscribe;
            
            $this->jsonData(0,'成功',$data);
            exit;
        }
        else{
            $this->jsonData(1,'失败');
            exit;
        }
        
	}
	
	
	//灭火器  判断是否关注过   http://miehuoqi.loc/cms/home/is_guanzhu
	public function is_guanzhu(){
		
		
		
        //查看当前用户是否关注过官方公众号
		$access_token=$this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$_SESSION['WX_INFO']['openid']."&lang=zh_CN";
		$user_info = json_decode($this->httpGet($url),true);
		//echo "dddee<pre>";print_r($user_info);exit;
		//$user_info['subscribe']    0 or 1
		if(isset($user_info['subscribe']) && $user_info['subscribe']==1){
			$subscribe=1;
		}
		else{
			$subscribe=0;
		}
		
		
        $data['subscribe']=$subscribe;
        
        $this->jsonData(0,'成功',$data);
        exit;
        
        
        
		
		//echo "<pre>";print_r($_SESSION);exit;
		
		
		$access_token=$this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$_SESSION['WX_INFO']['openid']."";
		$user_info = json_decode($this->httpGet($url),true);
		echo "<pre>";print_r($user_info);exit;
		
		
		//https://api.weixin.qq.com/sns/userinfo?access_token
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$_SESSION['WX_INFO']['access_token']."&openid=".$_SESSION['WX_INFO']['openid'];
		echo $url;echo "<br><br>";
		$get_return = file_get_contents($url);
		$get_return = (array)json_decode($get_return);
		echo "<pre>";print_r($get_return);exit;
		
		$user_info = json_decode($this->httpGet($url),true);
		echo "<pre>";print_r($user_info);exit;
		
	}
	
	
	//灭火器   授权页   http://miehuoqi.loc/cms/home/loading?source=share
	// http://miehuoqi.loc/cms/home/loading?source=readmore
	public function loading(){
		
		
		if(isset($_REQUEST['source'])){
			$source=$_REQUEST['source'];
		}
		else{
			$source='';
		}
		
		//echo $source;exit;
		$_SESSION['source']=$source;
		
		$url='/index.html?source='.$source;
		
		$openid=$this->get_openid($url);
		
		redirect($url);
		exit;
		
	}
	
	
	
	
	
	
	
	//首页  点击进入   http://huijiayou.loc/home/index/id/3
	public function index_huijiayou(){
		
		echo "homepage";
		exit;
		
		if(isset($_REQUEST['source'])){
			$source=$_REQUEST['source'];
		}
		else{
			$source='';
		}
		
		//echo $source;exit;
		
		$url='/index.html?source='.$source;
		
		
		redirect($url);
		exit;
		
		
		
		$access_token=$this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$_SESSION['WX_INFO']['openid']."&lang=zh_CN";
		$user_info = json_decode($this->httpGet($url),true);
		echo "dddee<pre>";print_r($user_info);exit;
		//$user_info['subscribe']    0 or 1
		
		
		
		echo "<pre>";print_r($_SESSION);echo "</pre>";
		
		echo "<br><br>";
		
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$_SESSION['WX_INFO']['access_token']."&openid=".$_SESSION['WX_INFO']['openid'];
		echo $url;echo "<br><br>";
		$get_return = file_get_contents($url);
		$get_return = (array)json_decode($get_return);
		
		echo "<pre>";print_r($get_return);exit;
		
		
		
		redirect(U('home/is_guanzhu'));
		//redirect('/index.html');
		exit;
		
		/*
		if(isset($_REQUEST['id'])){
			$id=$_REQUEST['id'];
			var_dump($id);
			echo "<br>";
		}
		echo 'index page';exit;
		*/
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
    	$this->assign('userinfo', $userinfo);
    	
        
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        
        $module = M('index_banner');
        $index_banner = $module->where(" status=1 " )->order('sort asc,id desc')->select();
        //echo "<pre>";print_r($index_banner);exit;
        $this->assign('index_banner', $index_banner);
        
        
        
        
        $module = M('index_product');
        $index_product = $module->where(" status=1 " )->order('sort asc,id desc')->select();
        if(!empty($index_product)){
        	foreach($index_product as $k=>$v){
        		$v['link_url']=str_replace('[手机号]',$userinfo['username'],$v['link_url']);
        		$v['link_url']=str_replace('[真实姓名]',urlencode($userinfo['realname']),$v['link_url']);
        		$index_product[$k]['link_url']=$v['link_url'];
        	}
        }
        //echo "<pre>";print_r($index_product);exit;
        $this->assign('index_product', $index_product);
        
        
        
        $this->display('index');
	}
	
	
	
	//注册   http://huijiayou.loc/home/reg
	public function reg(){
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
        	redirect(U('home/index'));
		}
        
        
        $this->display('reg');
	}
	
	//发送手机验证码短信
    public function send_mobile_verify(){
    	
    	if(isset($_POST['mobile']) && $_POST['mobile']!=""){
    		
    		
			
    		$post_data = array();
			$post_data['userid'] = 1381;
			$post_data['account'] = 'kunlun';
			$post_data['password'] = 'abcd1234';
			
			$randnum=rand(100001, 999999);
			$msg_content='您的验证码是：'.$randnum.'【昆仑】';
			//$post_data['content'] = urlencode($msg_content); //短信内容需要用urlencode编码下
			$post_data['content'] = $msg_content;
			
			$post_data['mobile'] = $_POST['mobile'];
			$post_data['sendtime'] = ''; //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
			$url='http://113.11.210.117:8802/sms.aspx?action=send';
			$o='';
			foreach ($post_data as $k=>$v)
			{
			   $o.="$k=".urlencode($v).'&';
			}
			$post_data=substr($o,0,-1);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
			$result = curl_exec($ch);
    		
    		
    		//$api_data=$this->http_request_url_post($url,$post_data);
			
			
			
	    	$UserMod = M('mobile_verify');
	    	$mobile_verify_table=$UserMod->getTableName();
	    	$mobile_verify = $UserMod->where("mobile='".addslashes($_POST['mobile'])."' ")->count();
			if(!empty($mobile_verify)){
				
		        $sql=sprintf("UPDATE %s SET verification='".addslashes($randnum)."' , status=0
		        where mobile='".addslashes($_POST['mobile'])."' 
		        ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
			}
			else{
				
				$sql=sprintf("INSERT %s SET verification='".addslashes($randnum)."' 
		        , status=0 
		        , mobile='".addslashes($_POST['mobile'])."' 
		        , create_time='".time()."' 
		        ", $UserMod->getTableName() );
		        //echo $sql;exit;
		        $result = $UserMod->execute($sql);
		        
			}
			
			$return['success']='success';
            echo json_encode($return);
            exit;
		}
		$return['success']='false';
        echo json_encode($return);
        exit;
    }
    
    
	public function check_username($user_id=0){
		///检查 $_POST 提交数据
		
			$UserMod = M('user');
			$result = $UserMod->where("username='%s' and id!=%d ", $_POST['username'], $user_id )->count();
			if($result>0){
                $return['success']='mobile_is_cunzai';
		        echo json_encode($return);
		        exit;
            }
            
            return true;
	}
	
	
	//注册(提交 ajax请求)
	public function reg_sub(){
		
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
        	$return['success']='is_login';
	        echo json_encode($return);
	        exit;
		}

        if(isset($_POST['dosubmit'])){
			
            //echo "<pre>";print_r($_POST);exit;
            
            
            $CityMod = M('mobile_verify');
	        $ver_info = $CityMod->field('verification')->where(" mobile='".addslashes($_POST['username'])."' and status=0 " )->select();
	        $verification=isset($ver_info[0]['verification'])?$ver_info[0]['verification']:"";
	        
	        if($verification==$_POST['mobile_verification'] && !empty($_POST['mobile_verification']) ){
	        }
	        else{
	        	$return['success']='mobile_verif_error';
		        echo json_encode($return);
		        exit;
	        }
            

            $UserMod = M('user');
			
            $rst=$this->check_username();
			
			
			$CityMod = M('user');
	        $cunzai_oid = $CityMod->where(" openid='".addslashes($userinfo['openid'])."' " )->select();
        	if(isset($cunzai_oid[0]['username']) && $cunzai_oid[0]['username']!=''){
	        	$return['success']='cunzai_oid_error';
	        	$rpl_username=substr_replace($cunzai_oid[0]['username'], "****", 3, 4);
	        	$return['username']=$rpl_username;
		        echo json_encode($return);
		        exit;
	        }
	        
	        
			
			$mobileMod = M('user');
			$sql=sprintf("UPDATE %s SET username='".addslashes($_POST['username'])."' , password='".md5($_POST['password'])."' 
				 , realname='".addslashes($_POST['realname'])."'
	        where openid='".addslashes($userinfo['openid'])."' 
	        ", $mobileMod->getTableName() );
	        $mobileMod->execute($sql);
	        
	        
	        
			$mobileMod = M('mobile_verify');
			$sql=sprintf("UPDATE %s SET verification='".addslashes($_POST['mobile_verification'])."' , status=1 
	        where mobile='".addslashes($_POST['username'])."' 
	        ", $mobileMod->getTableName() );
	        $mobileMod->execute($sql);
	        
	        
	        
            $module = M('user');
            $result = $module->where(" status=1 and username='".$this->fixSQL($_POST['username']) ."' and password='".md5($this->fixSQL($_POST['password'])) ."' " )->select();
            if(isset($result[0])){
                $_SESSION['is_login']='yes';
                $_SESSION['userinfo']=$result[0];
            }
            
            $return['success']='success';
            echo json_encode($return);
            exit;
            
            
        }

        $return['success']='false';
        echo json_encode($return);
        exit;
	}
	
	
	//登出
	public function logout(){
		if(isset($_SESSION['userinfo'])){
			unset($_SESSION['userinfo']);
		}
		if(isset($_SESSION['is_login'])){
			unset($_SESSION['is_login']);
		}
		
		redirect(U('home/index'));
	}
	
	
	
	
	//登陆 页面 显示 
	public function login(){
		//不需要，直接调用登陆公共模板

        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
        	redirect(U('home/index'));
		}

        $this->display('login');
	}
	
	
	//登陆 提交 （ajax调用）
	public function login_sub(){
		
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
        	$return['success']='is_login';
	        echo json_encode($return);
	        exit;
		}
		
		
		
		
        if(isset($_POST['dosubmit'])){
		
            /*
            $validate=$_POST['validate'];
            if (isset($validate) && !empty($validate)){
                if ($_SESSION["s_validate"]==$validate){
                }
                else{
                    exit;
                }
            }
            else{
                exit;
            }
            */

            $module = M('user');
            $result = $module->where(" status=1 and username='".$this->fixSQL($_POST['username']) ."' and password='".md5($this->fixSQL($_POST['password'])) ."' " )->select();
            if(isset($result[0])){
                $_SESSION['is_login']='yes';
                $_SESSION['userinfo']=$result[0];
                
                
                $return['success']='success';
		        echo json_encode($return);
		        exit;
		        
                //redirect(U('home/index'));
		       	//echo 'success';
            	//exit;
                //echo "<pre>";print_r($_SESSION['userinfo']);exit;
            }
            else{
                
                $return['success']='login_error';
		        echo json_encode($return);
		        exit;
		        
                //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("登陆失败");location.href="/user/login";</script>';
                //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">location.href="/home/login";</script>';
                //echo 'failed';
                //exit;
            }
		}
		
		$return['success']='login_error';
		echo json_encode($return);
		exit;
		
        //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("登陆失败");location.href="/user/login";</script>';
        //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("登陆失败");location.href="/home/login";</script>';
        //echo 'failed';
        //exit;
	}
	
	
	
	//修改密码
    public function modify_password(){
    	
        
        
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $userinfo=$_SESSION['userinfo'];
            $this->assign('userinfo',  $userinfo );
        }
        else{
            redirect(U('home/login'));
        }
        
        
        
    	$this->display('modify_password');
    }
	
	
	
	
	
	
	//修改密码 提交
    public function modify_password_sub(){
    	
        
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $userinfo=$_SESSION['userinfo'];
            $this->assign('userinfo',  $userinfo );
        }
        else{
            $return['success']='failed';
            echo json_encode($return);
            exit;
        }
        
        
        
		if(isset($_POST['dosubmit'])){
			$user_id=$userinfo['id'];
			
			
			$module = M('user');
			$result = $module->where(" status=1 and id='".$this->fixSQL($user_id) ."' and password='".md5($_POST['opassword']) ."' " )->select();
        	if(isset($result[0])){
        		//旧密码正确
        		
				$mobileMod = M('user');
				$sql=sprintf("UPDATE %s SET password='".md5($_POST['password'])."' 
		        where id='".$this->fixSQL($user_id) ."'
		        ", $mobileMod->getTableName() );
		        $mobileMod->execute($sql);
		        
		        
	        	$return['success']='success';
	            echo json_encode($return);
	            exit;
	            
        	}
        	else{
        		$return['success']='opassword_error';
	            echo json_encode($return);
	            exit;
        	}
        	
		}
        
        $return['success']='failed';
        echo json_encode($return);
        exit;
        
    }
	
	
	//忘记密码
	public function show_forget_pwd(){
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
		if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
			redirect(U('home/index'));
		}
		
		$this->display('show_forget_pwd');
	}
	
	
	
	//忘记密码 提交
	public function show_forget_pwd_sub(){
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
        	$return['success']='is_login';
	        echo json_encode($return);
	        exit;
		}

        if(isset($_POST['dosubmit'])){
			
            //echo "<pre>";print_r($_POST);exit;
            
            
            $CityMod = M('mobile_verify');
	        $ver_info = $CityMod->field('verification')->where(" mobile='".addslashes($_POST['username'])."' and status=0 " )->select();
	        $verification=isset($ver_info[0]['verification'])?$ver_info[0]['verification']:"";
	        
	        if($verification==$_POST['mobile_verification'] && !empty($_POST['mobile_verification']) ){
	        }
	        else{
	        	$return['success']='mobile_verif_error';
		        echo json_encode($return);
		        exit;
	        }
            
			
            
			
			$mobileMod = M('user');
			$sql=sprintf("UPDATE %s SET password='".md5($_POST['password'])."' 
	        where username='".addslashes($_POST['username'])."'
	        ", $mobileMod->getTableName() );
	        $mobileMod->execute($sql);
	        
	        
	        
			$mobileMod = M('mobile_verify');
			$sql=sprintf("UPDATE %s SET verification='".addslashes($_POST['mobile_verification'])."' , status=1 
	        where mobile='".addslashes($_POST['username'])."' 
	        ", $mobileMod->getTableName() );
	        $mobileMod->execute($sql);
	        
            
            $return['success']='success';
            echo json_encode($return);
            exit;
            
            
        }

        $return['success']='false';
        echo json_encode($return);
        exit;
        
	}
	
	
	
	
	
	//朋友圈 推广链接 推广二维码
	public function friend(){
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $userinfo=$_SESSION['userinfo'];
            $this->assign('userinfo',  $userinfo );
        }
        else{
            redirect(U('home/login'));
        }
        
        
        //生成推广url
        $url_path=U('home/friend_load').'?uid='.$userinfo['id'];
        $qrUrl=BASE_URL.$url_path;
        $this->assign('qrUrl',  $qrUrl );
        
        //生成二维码
		$tempRoot=ROOT_PATH.'/public/';
		$tempBase=__ROOT__.'/public/';
		$size=5;
		$qrcode_url=$this->createQRcode($tempRoot,$tempBase,$qrUrl,$size);
		$this->assign('qrcode_url',  $qrcode_url );
        
		
		$this->display('friend');
	}
	
	
	
	
	
	//朋友圈 别人点击推广链接后进来
	//http://huijiayou.loc/home/friend_load?uid=1728
	public function friend_load(){
		
		
		if(isset($_REQUEST['uid'])){
			$fuid=$_REQUEST['uid'];
		}
		else{
			$fuid=0;
		}
		$this->assign('fuid', $fuid);
		
		
        $_SESSION['fuid']=$fuid;
        
        
        
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
    	//没注册过，需要更新fuid。注册过，则不用更新fuid
    	if($userinfo['username']==''){
    		//记录上家user_id
    		$mobileMod = M('user');
			$sql=sprintf("UPDATE %s SET fuid='".addslashes($_SESSION['fuid'])."' , addtime='".date('Y-m-d H:i:s')."'
	        where id='".addslashes($userinfo['id'])."'
	        ", $mobileMod->getTableName() );
	        $mobileMod->execute($sql);
    	}
        
        redirect(U('home/index'));
	}
	
	
	
	
	//个人中心 下家层级关系
	public function member(){
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
    	$this->assign('userinfo', $userinfo);
    	
    	
        //分享至朋友圈的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $userinfo=$_SESSION['userinfo'];
            $this->assign('userinfo',  $userinfo );
        }
        else{
            redirect(U('home/login'));
        }
        
        
        //我的推荐人
        if($userinfo['fuid']>0){
			$module = M('user');
			$fuserinfo = $module->where(" status=1 and id='".addslashes($userinfo['fuid']) ."' " )->select();
			$fuserinfo=isset($fuserinfo[0])?$fuserinfo[0]:array();
			if(!empty($fuserinfo)){
				$fnickname=$fuserinfo['nickname'];
			}
			else{
				$fnickname='无';
			}
		}
		else{
			$fnickname='无';
		}
        $this->assign('fnickname',  $fnickname );
        
        
        
        //我的好友圈=我的下家
		$db_fuid=array();
		$db_fuid[]=$userinfo['id'];
		$db_fuid_str=implode(",", $db_fuid);
        $andsql = " status=1 and username!='' ";
        $andsql .= " and fuid in (".$db_fuid_str.")";
        
        $module = M('user');
		$user_arr = $module->where( $andsql )->order('addtime desc,id desc')->select();
		$userinfo_level_01=$user_arr;
		//echo "<pre>";print_r($userinfo_level_01);exit;
		//echo count($userinfo_level_01);exit;
        $this->assign('userinfo_level_01',  $userinfo_level_01 );
        $this->assign('userinfo_level_01_count',  count($userinfo_level_01) );
        
        
        //我的好友圈=我的下家(默认显示最新的5个，点击查看更多。列更多)
        $userinfo_level_01_base=array_slice($userinfo_level_01, 0,5);
        $userinfo_level_01_other=array_slice($userinfo_level_01, 5,1000000);
        //echo "<pre>";print_r($userinfo_level_01_base);exit;
        //echo "<pre>";print_r($userinfo_level_01_other);exit;
        $this->assign('userinfo_level_01_base',  $userinfo_level_01_base );
        $this->assign('userinfo_level_01_other',  $userinfo_level_01_other );
        
        
        
        //我的朋友圈=我的下家的下家
        $userinfo_level_02=array();
        $db_fuid=array();
        
        if(!empty($userinfo_level_01)){
	        foreach ($userinfo_level_01 as $k=>$v) {
	        	$db_fuid[]=$v['id'];
	        }
	        
	        $db_fuid_str=implode(",", $db_fuid);
	        $andsql = " status=1 and username!='' ";
	        $andsql .= " and fuid in (".$db_fuid_str.")";
	        
	        $module = M('user');
			$user_arr = $module->where( $andsql )->select();
			$userinfo_level_02=$user_arr;
			
	        
        }
        //echo "<pre>";print_r($userinfo_level_02);exit;
        $this->assign('userinfo_level_02',  $userinfo_level_02 );
        $this->assign('userinfo_level_02_count',  count($userinfo_level_02) );
        
        
        
        
		//我的人脉圈=我的下家的下家的下家
        $userinfo_level_03=array();
        $db_fuid=array();
        
        if(!empty($userinfo_level_02)){
	        foreach ($userinfo_level_02 as $k=>$v) {
	        	$db_fuid[]=$v['id'];
	        }
	        
	        $db_fuid_str=implode(",", $db_fuid);
	        $andsql = " status=1 and username!='' ";
	        $andsql .= " and fuid in (".$db_fuid_str.")";
	        
	        $module = M('user');
			$user_arr = $module->where( $andsql )->select();
			$userinfo_level_03=$user_arr;
			
	        
        }
        //echo "<pre>";print_r($userinfo_level_03);exit;
        $this->assign('userinfo_level_03',  $userinfo_level_03 );
        $this->assign('userinfo_level_03_count',  count($userinfo_level_03) );
        
        
        
		
		
		$this->display('member');
	}
	
	
	
	
	
	
	public function is_game_over($userinfo){
		
		
        //只要有人中奖，其他用户不管怎么进入，都是你的手速慢了这个页面
		
        
        //总库存
        //$total_kucun=1500;
        $CityMod = M('weixin_setting');
        $total_kucun = $CityMod->field('value_s')->where(" key_s='total_kucun' " )->select();
        $total_kucun= $total_kucun[0]['value_s'];
        
        
        //已有xx人0元抢到   只有是粉丝的用户才占据库存
        $andsql=" and price_now=0 and realname!='' and mobile!='' and address!='' ";
    	$CityMod = M('user');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $finish_number=$rst[0]['num'];
        //echo "<pre>";print_r($rst);exit;
        //echo $finish_number;
        $this->assign('finish_number', $finish_number);
        
        
        $remain_number=$total_kucun-$finish_number;
        if($remain_number<0){
        	$remain_number=0;
        }
        $this->assign('remain_number', $remain_number);
        
        
    	if($remain_number>0){
    		//可以继续帮砍
    	}
    	else{
    		if( !empty($userinfo['realname']) && !empty($userinfo['mobile']) && !empty($userinfo['address']) ){
    			$url=U('home/follow');
	    		redirect($url);
    		}
    		else{
	    		$url=U('home/scan');
		    	redirect($url);
	    	}
    	}
        
        //只要有人中奖，其他用户不管怎么进入，都是你的手速慢了这个页面
		
	}
	
	
	public function is_game_input($userinfo){
		
		//判断有没有填写过姓名、联系手机
		if( !empty($userinfo['realname']) && !empty($userinfo['mobile']) ){
			
		}
		else{
			$current_page_url=$this->get_current_page_url();
			$_SESSION['current_page_url']=$current_page_url;
			//echo $_SESSION['current_page_url'];exit;
			$url=U('home/contact');
	    	redirect($url);
	    	exit;
		}
		
		
		
	}
	
	
	
	
	
	
	//主人分享到朋友圈页   已有xx人抢到  http://huijiayou.loc/home/share
	public function share(){
		
		
		
		$openid=$this->get_openid();
        $userinfo=$this->get_userinfo($openid);
        //echo "<pre>";print_r($userinfo);exit;
        
        $this->is_game_input($userinfo);
        $this->is_game_over($userinfo);
        
        
        //总参与人
        $andsql=" and openid!='' and status=1 ";
    	$CityMod = M('user');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $attend_number=$rst[0]['num'];
        //echo "<pre>";print_r($rst);exit;
        //echo $attend_number;
        $this->assign('attend_number', $attend_number);
        
        
        
        //总库存
        //$total_kucun=1500;
        $CityMod = M('weixin_setting');
        $total_kucun = $CityMod->field('value_s')->where(" key_s='total_kucun' " )->select();
        $total_kucun= $total_kucun[0]['value_s'];
        
        
        
        //已有xx人0元抢到   只有是粉丝的用户才占据库存
        $andsql=" and price_now=0 and realname!='' and mobile!='' and address!='' ";
    	$CityMod = M('user');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $finish_number=$rst[0]['num'];
        //echo "<pre>";print_r($rst);exit;
        //echo $finish_number;
        $this->assign('finish_number', $finish_number);
        
        $remain_number=$total_kucun-$finish_number;
        if($remain_number<0){
        	$remain_number=0;
        }
        $this->assign('remain_number', $remain_number);
        
        
        
        
        //分享至朋友圈的帮忙砍价的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        //已有xx人帮忙砍价，还差xx人
		$andsql=" and user_id_owner='".addslashes($userinfo['id'])."' ";
    	$CityMod = M('user_cut');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $helper_number=$rst[0]['num'];
        $need_helper_number=88-$helper_number;
		if($need_helper_number<0){
			$need_helper_number=0;
		}
		
		$this->assign('helper_number', $helper_number);   //当前xxx人为你砍价
    	$this->assign('need_helper_number', $need_helper_number);   //还差xxx人
    	
        
        //砍满88人，并且有库存的话，直接跳转到填写个人信息的页面
        if ($need_helper_number==0 && $remain_number>0){
        	$url=U('home/contactinfo');
	        redirect($url);
        }
        
        
        $this->display('share');
	}
	
	
	
	//游戏规则   http://huijiayou.loc/home/rule
	public function rule(){
		
		
		
		if(isset($_REQUEST['uid'])){
			$user_id_owner=$_REQUEST['uid'];
		}
		else{
			$user_id_owner=0;
		}
		$this->assign('user_id_owner', $user_id_owner);
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
		
		//分享至朋友圈的帮忙砍价的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        $this->is_game_input($userinfo);
        $this->is_game_over($userinfo);
        
		$this->display('rule');
	}
	
	
	
	//砍价页面(从朋友圈进来的)    http://huijiayou.loc/home/cut?uid=1697
	public function cut(){
		
		
		
		if(isset($_REQUEST['uid'])){
			$user_id_owner=$_REQUEST['uid'];
		}
		else{
			$user_id_owner=0;
		}
		$this->assign('user_id_owner', $user_id_owner);
		
		
		$url_path=U('home/cut').'?uid='.$user_id_owner;
        $currnet_url=BASE_URL.$url_path;
        $currnet_url=urlencode($currnet_url);
        //echo $currnet_url;exit;
        
        
		$openid=$this->get_openid($currnet_url);
		$userinfo=$this->get_userinfo($openid);
		
		$this->is_game_input($userinfo);
		$this->is_game_over($userinfo);
		
		
		
		//分享至朋友圈的帮忙砍价的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        
    	
        //总库存
        //$total_kucun=1500;
        $CityMod = M('weixin_setting');
        $total_kucun = $CityMod->field('value_s')->where(" key_s='total_kucun' " )->select();
        $total_kucun= $total_kucun[0]['value_s'];
        
        
        
        //已有xx人0元抢到   只有是粉丝的用户才占据库存
        $andsql=" and price_now=0 and realname!='' and mobile!='' and address!='' ";
    	$CityMod = M('user');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $finish_number=$rst[0]['num'];
        //echo "<pre>";print_r($rst);exit;
        //echo $finish_number;
        $this->assign('finish_number', $finish_number);
        
        
        $remain_number=$total_kucun-$finish_number;
        if($remain_number<0){
        	$remain_number=0;
        }
        $this->assign('remain_number', $remain_number);
        
        
        
        
        
		
		if($userinfo['id']==$user_id_owner){
			//是本人
			
			
	        
			//已有xxx人为你砍价，还差xxx人
			$andsql=" and user_id_owner='".addslashes($userinfo['id'])."' ";
	    	$CityMod = M('user_cut');
	        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
	        $helper_number=$rst[0]['num'];
	        $need_helper_number=88-$helper_number;
			if($need_helper_number<0){
				$need_helper_number=0;
			}
			
			$this->assign('helper_number', $helper_number);   //当前xxx人为你砍价
        	$this->assign('need_helper_number', $need_helper_number);   //还差xxx人
        	
	        
	        
	        //echo "<pre>";print_r($userinfo);exit;
	        
	        //已经中奖，并且填写过信息了
	        if( !empty($userinfo['realname']) && !empty($userinfo['mobile']) && !empty($userinfo['address']) ){
				$url=U('home/follow');
	        	redirect($url);
			}
			else{
				
		        //砍满88人
		        if ($need_helper_number==0){
		        	if($remain_number>0){
		        		$url=U('home/contactinfo');
			        	redirect($url);
		        	}
		        	else{
		        		$url=U('home/scan');
				    	redirect($url);
		        	}
		        }
		        else{
		        	//没砍满88人
		        	//仍然显示xx帮砍，还差xx人的画面
		        }
		        
			    
		    }
        	
        	
			$this->display('cut_owner');
		}
		else{
			//不是本人，是好友，帮忙砍价的
			
        	if($remain_number>0){
        		//可以继续帮砍
        	}
        	else{
        		$url=U('home/scan');
		    	redirect($url);
        	}
        	
	        
			$this->display('cut_helper');
		}
		
		//$this->display('cut');
	}
	
	
	
	
	//帮忙砍价 提交    http://huijiayou.loc/home/cut_help?uid=1697
	public function cut_help(){
		
		
		
		if(isset($_REQUEST['uid'])){
			$user_id_owner=$_REQUEST['uid'];
		}
		else{
			$user_id_owner=0;
		}
		$this->assign('user_id_owner', $user_id_owner);
		
		
		
		$url_path=U('home/cut').'?uid='.$user_id_owner;
        $currnet_url=BASE_URL.$url_path;
        $currnet_url=urlencode($currnet_url);
        //echo $currnet_url;exit;
        
		$openid=$this->get_openid($currnet_url);
		$userinfo=$this->get_userinfo($openid);
		//echo $userinfo['price_now'];exit;
		
		$this->is_game_input($userinfo);
		$this->is_game_over($userinfo);
		
		
		
        //分享至朋友圈的帮忙砍价的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
		
		if($userinfo['id']==$user_id_owner){
			$this->assign('alert_msg', "请不要帮自己砍价");
        	$this->display('cut_helper');
        	exit;
        	
			//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("请不要帮自己砍价");location.href="'.$url_path.'";</script>';
        }
		
		
		//目前有多少人帮忙砍价
		$andsql=" and user_id_owner='".addslashes($user_id_owner)."' ";
    	$CityMod = M('user_cut');
        $rst = $CityMod->field('count(id) as num')->where(" 1 ".$andsql." ")->select();
        $helper_now=$rst[0]['num'];
        $helper_after=$helper_now+1;
        //echo $helper_after;exit;
        
		$price_cut=100;
		$price_before=8800-($helper_now*100);
		$price_after=$price_before-$price_cut;
		if($price_after<0){
			$price_after=0;
		}
		//echo $price_after;exit;
		
		
    	
    	
		//判断是否帮忙当前主人砍价过
		$andsql=" and user_id_owner='".addslashes($user_id_owner)."' and user_id_helper='".addslashes($userinfo['id'])."' ";
    	$CityMod = M('user_cut');
        $rst = $CityMod->field('id')->where(" 1 ".$andsql." ")->select();
        if(!empty($rst)){
        	
        	$this->assign('alert_msg', "请不要重复砍价");
        	$this->display('cut_helper');
        	exit;
        	//echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("请不要重复砍价");location.href="'.$url_path.'";</script>';
        	
        	
        }
        else{
	        $UserMod = M('user_cut');
	        $sql=sprintf("INSERT %s SET user_id_owner='".addslashes($user_id_owner)."' 
	        , user_id_helper='".addslashes($userinfo['id'])."' 
	        , price_before='".addslashes($price_before)."' 
	        , price_cut='".addslashes($price_cut)."' 
	        , price_after='".addslashes($price_after)."' 
	        , create_time='".time()."' 
	        , modify_time='".time()."' 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
	        
        	$UserMod = M('user');
	        $sql=sprintf("UPDATE %s SET 
	         price_now='".addslashes($price_after)."' 
	        , helper_now='".addslashes($helper_after)."' 
	        , last_cut_time='".time()."' 
	        where id=".addslashes($user_id_owner)." 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
        }
        
        
        
        
        //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("帮忙砍价成功");location.href="'.$url_path.'";</script>';
		$this->display('cut_help');   //砍价成功页面
	}
	
	
	
	
	//点击  我也要抢 之后     http://huijiayou.loc/home/hopejoin
	public function hopejoin(){
		
		
		
		//$openid=$this->get_openid();
        //$userinfo=$this->get_userinfo($openid);
        
        
        //暂且不判断当前用户是否成功抢到，统一都跳到主人分享到朋友圈页
        $url=U('home/share');
        //$url=U('home/scan');
        //echo $url;exit;
        redirect($url);
        
    	//$this->display('hopejoin');
	}
	
	
	
	//填写个人信息
	public function contactinfo(){
		
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
		//echo "<pre>";print_r($userinfo);exit;
		$this->assign('userinfo', $userinfo); 
		
		$this->is_game_input($userinfo);
		$this->is_game_over($userinfo);
		
		
		if( !empty($userinfo['realname']) && !empty($userinfo['mobile']) && !empty($userinfo['address']) ){
			$url=U('home/follow');
        	redirect($url);
		}
		
		
		
        //分享至朋友圈的帮忙砍价的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
        //已有xxx人为你砍价
		$andsql=" and user_id_owner='".addslashes($userinfo['id'])."' ";
    	$CityMod = M('user_cut');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $helper_number=$rst[0]['num'];
        //echo $helper_number;exit;
		$this->assign('helper_number', $helper_number);   //当前xxx人为你砍价
    	
    	
    	
        
		$this->display('contactinfo');
	}
	
	
	//填写个人信息  提交   http://huijiayou.loc/home/contactinfo_sub?realname=abc&mobile=13911112222&address=地址
	public function contactinfo_sub(){
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
		
		$url_path=U('home/contactinfo');
		
		
		if(isset($_REQUEST['realname'])){
			$realname=$_REQUEST['realname'];
		}
		else{
			$realname='';
		}
		
		
		if(isset($_REQUEST['mobile'])){
			$mobile=$_REQUEST['mobile'];
		}
		else{
			$mobile='';
		}
		
		
		if(isset($_REQUEST['address'])){
			$address=$_REQUEST['address'];
		}
		else{
			$address='';
		}
		
		
		if(isset($_REQUEST['weixinhao'])){
			$weixinhao=$_REQUEST['weixinhao'];
		}
		else{
			$weixinhao='';
		}
		
		
		//直接提示没库存了
		//$return['success']='kucun_not_ok';
        //echo json_encode($return);
        //exit;
        
		
		$andsql=" and user_id_owner='".addslashes($userinfo['id'])."' ";
    	$CityMod = M('user_cut');
        $rst = $CityMod->field('count(id) as num')->where(" 1 ".$andsql." ")->select();
        $helper_now=$rst[0]['num'];
        if($helper_now<88){
        	$return['success']='helper_not_ok';
	        echo json_encode($return);
	        exit;
        }
        
        
		//总库存
        //$total_kucun=1500;
        $CityMod = M('weixin_setting');
        $total_kucun = $CityMod->field('value_s')->where(" key_s='total_kucun' " )->select();
        $total_kucun= $total_kucun[0]['value_s'];
        
        
        //已有xx人0元抢到   只有是粉丝的用户才占据库存
        $andsql=" and price_now=0 and realname!='' and mobile!='' and address!='' ";
    	$CityMod = M('user');
        $rst = $CityMod->field('count(id) as num ')->where(" 1 ".$andsql." ")->select();
        $finish_number=$rst[0]['num'];
        
        $remain_number=$total_kucun-$finish_number;
        if($remain_number<=0){
        	$return['success']='kucun_not_ok';
	        echo json_encode($return);
	        exit;
        }
        
        
        
        
        
    	$UserMod = M('user');
        $sql=sprintf("UPDATE %s SET 
         realname='".addslashes($realname)."' 
        ,mobile='".addslashes($mobile)."' 
        ,address='".addslashes($address)."' 
        ,weixinhao='".addslashes($weixinhao)."' 
        where id=".addslashes($userinfo['id'])." 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        $return['success']='success';
        echo json_encode($return);
        exit;
        
        //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("提交个人信息成功");location.href="'.$url_path.'";</script>';
        
		//$this->display('contactinfo_sub');  //缺提交个人信息成功页面
	}
	
	
	
	
	//长按二维码
	public function scan(){
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
		
		
		
        //分享至朋友圈的帮忙砍价的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
		
		if(isset($_REQUEST['success']) && $_REQUEST['success']==1){
			$success=1;
		}
		else{
			$success=0;
		}
		
		$this->assign('success', $success);
	    
	    
	    //判断有没有填写过姓名、联系手机
		if( !empty($userinfo['realname']) && !empty($userinfo['mobile']) ){
			$this->display('scan');    //不需要填写姓名、联系方式
		}
		else{
			$this->display('scan_contact');   //需要填写姓名、联系方式
		}
		
		
	}
	
	
	
	
	
	
	//长按二维码
	public function follow(){
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
		
		
		
        //分享至朋友圈的帮忙砍价的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
		
		if(isset($_REQUEST['success']) && $_REQUEST['success']==1){
			$success=1;
		}
		else{
			$success=0;
		}
		
		$this->assign('success', $success);
	    
		$this->display('follow');
	}
	
	
	
	
	
	
	//预填写个人信息
	public function contact(){
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
		
		
		
        //分享至朋友圈的帮忙砍价的url
        $share_info=$this->get_share_url_info($userinfo['id']);
        //echo "<pre>";print_r($share_info);exit;
        $this->assign('share_info', $share_info);
        
        
        
        $signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        $this->assign('signPackage', $signPackage);
        
        
        
		
		if(isset($_REQUEST['success']) && $_REQUEST['success']==1){
			$success=1;
		}
		else{
			$success=0;
		}
		
		$this->assign('success', $success);
	    
		$this->display('contact');
	}
	
	
	
	//预填写个人信息  提交   http://huijiayou.loc/home/contact_sub?realname=abc&mobile=13911112222
	public function contact_sub(){
		
		
		$openid=$this->get_openid();
		$userinfo=$this->get_userinfo($openid);
		
		$url_path=U('home/contact');
		
		
		if(isset($_REQUEST['realname'])){
			$realname=$_REQUEST['realname'];
		}
		else{
			$realname='';
		}
		
		
		if(isset($_REQUEST['mobile'])){
			$mobile=$_REQUEST['mobile'];
		}
		else{
			$mobile='';
		}
		
		
		if( !empty($userinfo['realname']) && !empty($userinfo['mobile']) ){
			
		}
		else{
			
	    	$UserMod = M('user');
	        $sql=sprintf("UPDATE %s SET 
	         realname='".addslashes($realname)."' 
	        ,mobile='".addslashes($mobile)."' 
	        where id=".addslashes($userinfo['id'])." 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
        
		}
		
        
        $return['success']='success';
        $return['current_page_url']=$_SESSION['current_page_url'];
        unset($_SESSION['current_page_url']);
        echo json_encode($return);
        exit;
        
        //echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><script language="JavaScript">alert("提交个人信息成功");location.href="'.$url_path.'";</script>';
        
		//$this->display('contactinfo_sub');  //缺提交个人信息成功页面
	}
	
	
	
	
	
	
	
	
	/*
	//分享到朋友圈的帮忙砍价的url等分享信息
	public function get_share_url_info($uid=0){
		
		//分享至朋友圈的url
        $url_path=U('home/friend_load').'?uid='.$uid;
        //echo $url_path;exit;
        $share_url=BASE_URL.$url_path;
        //$share_url=urlencode($share_url);
        //echo $share_url;exit;
        $share_info['share_url']=$share_url;
        $share_info['share_desc']='惠加油';
        $share_info['share_wxIco']=BASE_URL."/statics/images/share.png";
        //$share_info['share_wxIcoW']='469';
        //$share_info['share_wxIcoH']='328';
        
        //$this->assign('share_info', $share_info);
        return $share_info;
	}
	*/
	
	
	
	
	
	//获取某个公众号下所有的粉丝用户列表  http://www.sagaci.com.cn/home/getfanslist
	public function getfanslist() {
		
		$return['success']='success';
	    echo json_encode($return);
	    exit;
	    
	    
	    
		set_time_limit(0);
		
		
		//$return['success']='不足1小时，无需执行';
	    //echo json_encode($return);
	    //exit;
	    
	    
	    
		$CityMod = M('weixin_setting');
        $get_fans_expire_time = $CityMod->field('value_s')->where(" key_s='get_fans_time' " )->select();
        $get_fans_expire_time = $get_fans_expire_time[0]['value_s'];
        
        $nowtime=time();
        $cut_time=$nowtime-(1*60*60);
        if($cut_time<$get_fans_expire_time){
        	//不足1小时，无需执行
        	$return['success']='不足1小时，无需执行';
		    echo json_encode($return);
		    exit;
        }
        
        $get_return['access_token']=$this->getAccessTokenLIANMESHA();
        
        
        /*
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WX_APPID_LIANMESHA."&secret=".WX_APPSECRET_LIANMESHA."";
		$get_return = json_decode($this->httpGet($url),true);
		if( !isset($get_return['access_token']) ){
			//echo '获取access_token失败！';
			//exit;
			$return['success']='获取access_token失败！';
		    echo json_encode($return);
		    exit;
		}
		*/
		
		$fans_list=array();
		
		$k=0;
		do{
			
			
			if(isset($user_list['next_openid']) && $user_list['next_openid']!=""){
				$next_openid=$user_list['next_openid'];
				$add_where="&next_openid=".$next_openid;
			}
			else{
				$next_openid='';
				$add_where='';
			}
			
			$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$get_return['access_token'].$add_where."";
			
			//if(isset($user_list['next_openid']) && $user_list['next_openid']!=""){
				//echo $url;exit;
			//}
			
			$user_list = json_decode($this->httpGet($url),true);
			
			//echo "<pre>";print_r($user_list['data']['openid']);echo "</pre>";exit;
			
			//if(isset($next_openid) && $next_openid!=""){
				//echo "<pre>";print_r($user_list);echo "</pre>";exit;
			//}
			
			
			if(isset($user_list['data']['openid']) && !empty($user_list['data']['openid'])){
				$fans_list = array_merge($fans_list, $user_list['data']['openid']);
			}
			
			//echo "<pre>";print_r($fans_list);echo "</pre>";exit;
			
			//echo $user_list['next_openid'];exit;
			
			$k=$k+1;
		}while(isset($user_list['next_openid']) && $user_list['next_openid']!="");
		
		//echo count($fans_list);
		//echo "<pre>";print_r($fans_list);echo "</pre>";
		
		
		
		
		$k=0;
		if(isset($fans_list) && !empty($fans_list)){
			
			$CityMod = M('fanslist');
			
			//超过1天，create_time的时间还没被刷新的视为已经取消关注的粉丝。
			$create_time_delete=time()-(3600*24);
			
            $sql=" create_time < '".$create_time_delete."' ";
            $CityMod->where($sql)->delete();
            
			foreach($fans_list as $k=>$v){
				
				$andsql=" and openid='".addslashes($v)."' ";
		    	$rst = $CityMod->where(" 1 ".$andsql." ")->select();
		        if(isset($rst[0])){
		        	//更新
		        	//已经有这个openid了
		        }
		        else{
		        	//新增
			        $sql=sprintf("INSERT %s SET openid='".addslashes($v)."' , create_time='".time()."'
			        ", $CityMod->getTableName() );
			        //echo $sql;exit;
			        $result = $CityMod->execute($sql);
		        }
		        
			}
		}
		
		
		/*
        //更新用户信息表，设置已经关注的人
    	$UserMod = M('user');
		$andsql=" ";
    	$userlist = $UserMod->where(" 1 ".$andsql." ")->select();
    	if(isset($userlist) && !empty($userlist)){
    		$fanslistMod = M('fanslist');
			foreach($userlist as $k=>$v){
				
				
				//不同公众号对应相同微信用户的openid是不同的，所以无法拿openid去比对
				//拿订阅号的openid的用户的昵称，然后去比对昵称
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$get_return['access_token']."&openid=".$v['openid']."";
				$user_info = json_decode($this->httpGet($url),true);
				
				
				//$nickname=$user_info['nickname'];
				$nickname=1;
				
				$andsql=" and nickname='".addslashes($nickname)."' ";
		    	$isfansinfo = $fanslistMod->where(" 1 ".$andsql." ")->select();
		        if(isset($isfansinfo[0])){
		        	//是粉丝
		        	$is_fans=1;
		        }
		        else{
		        	//不是粉丝
		        	$is_fans=0;
		        }
		        
		        
		        $sql=sprintf("UPDATE %s SET 
		         is_fans='".addslashes($is_fans)."' 
		        where id=".addslashes($v['id'])." 
		        ", $UserMod->getTableName() );
		        //echo $sql;exit;
		        $UserMod->execute($sql);
		        
			}
			
		}
		*/
        
		$UserMod = M('weixin_setting');
        $sql=sprintf("UPDATE %s SET value_s='".$nowtime."' where key_s='get_fans_time' ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
		//echo "finish";
		//exit;
		$return['success']='success';
	    echo json_encode($return);
	    exit;
	    
	    
	}
	
	

	
	
	
	
	
	//获取某个公众号下所有的粉丝用户列表  http://www.sagaci.com.cn/home/getfanslist_test  测试
	public function getfanslist_nickname() {
		$return['success']='success';
	    echo json_encode($return);
	    exit;
	    
		set_time_limit(0);
		
	    /*
		$CityMod = M('weixin_setting');
        $get_fans_expire_time = $CityMod->field('value_s')->where(" key_s='get_fans_time' " )->select();
        $get_fans_expire_time = $get_fans_expire_time[0]['value_s'];
        
        $nowtime=time();
        $cut_time=$nowtime-(1*60*60);
        if($cut_time<$get_fans_expire_time && 1==2){
        	//不足1小时，无需执行
        	echo "a";exit;
			
        	$return['success']='不足1小时，无需执行';
		    echo json_encode($return);
		    exit;
        }
        */
        
        $get_return['access_token']=$this->getAccessTokenLIANMESHA();
        
        
        
        /*
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WX_APPID_LIANMESHA."&secret=".WX_APPSECRET_LIANMESHA."";
		$get_return = json_decode($this->httpGet($url),true);
		if( !isset($get_return['access_token']) ){
			//echo '获取access_token失败！';
			//exit;
			//echo "b";exit;
			
			$return['success']='获取access_token失败！';
		    echo json_encode($return);
		    exit;
		}
		*/
		
		$fans_list=array();
		
		/*
		$k=0;
		do{
			
			
			if(isset($user_list['next_openid']) && $user_list['next_openid']!=""){
				$next_openid=$user_list['next_openid'];
				$add_where="&next_openid=".$next_openid;
			}
			else{
				$next_openid='';
				$add_where='';
			}
			
			
			
			
			$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$get_return['access_token'].$add_where."";
			
			//if(isset($user_list['next_openid']) && $user_list['next_openid']!=""){
				//echo $url;exit;
			//}
			
			$user_list = json_decode($this->httpGet($url),true);
			
			//echo "<pre>";print_r($user_list['data']['openid']);echo "</pre>";exit;
			
			//if(isset($next_openid) && $next_openid!=""){
				//echo "<pre>";print_r($user_list);echo "</pre>";exit;
			//}
			
			
			if(isset($user_list['data']['openid']) && !empty($user_list['data']['openid'])){
				$fans_list = array_merge($fans_list, $user_list['data']['openid']);
			}
			
			//echo "<pre>";print_r($fans_list);echo "</pre>";exit;
			
			//echo $user_list['next_openid'];exit;
			
			$k=$k+1;
		}while(isset($user_list['next_openid']) && $user_list['next_openid']!="");
		*/
		
		
		//echo count($fans_list);
		//echo "<pre>";print_r($fans_list);echo "</pre>";exit;
		
		
		$UserMod = M('user');
		
		$fanslistMod = M('fanslist');
		$andsql="  ";
    	$fans_list = $fanslistMod->where(" 1 ".$andsql." ")->order('modify_time asc')->limit('0,10')->select();
    	
    	//echo "<pre>";print_r($fans_list);echo "</pre>";exit;
    	
		
        //$sql=" id > 0 ";
        //$fanslistMod->where($sql)->delete();
        
		
		if(isset($fans_list) && !empty($fans_list)){
			
			
			foreach($fans_list as $k=>$v){
				
				//sleep(1);
				
				//echo "<pre>";print_r($v);echo "</pre>";exit;
    	
		
		
				
				//不同公众号对应相同微信用户的openid是不同的，所以无法拿openid去比对
				//拿订阅号的openid的用户的昵称，然后去比对昵称
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$get_return['access_token']."&openid=".$v['openid']."";
				//echo $url;echo "<br><br>";
				$user_info = json_decode($this->httpGet($url),true);
				//echo "<pre>";print_r($user_info);echo "</pre>";exit;
    	
				if(isset($user_info['nickname']) && $user_info['nickname']!=""){
					
					$nickname=$user_info['nickname'];
					//echo $nickname;exit;
					//$nickname=1;
					
			        $sql=sprintf("UPDATE %s SET 
			        nickname='".addslashes($nickname)."' 
			        ,modify_time='".time()."' 
			        where openid='".addslashes($v['openid'])."' 
			        ", $fanslistMod->getTableName() );
			        //echo $sql;exit;
			        $fanslistMod->execute($sql);
					
					
					
			        $sql=sprintf("UPDATE %s SET 
			         is_fans='1' 
			        where nickname='".addslashes($nickname)."' 
			        ", $UserMod->getTableName() );
			        //echo $sql;exit;
			        $UserMod->execute($sql);
					
					
				}
				
			}
		}
		
		
		//echo "finish";
		//exit;
		$return['success']='success';
	    echo json_encode($return);
	    exit;
	    
	}
	
	
	
	
	
	// 获取AccessToken for 恋玫莎 LIANMESHA
	public function getAccessTokenLIANMESHA() {
		
		//2小时有效时间的版本，据说access_token每天只能获取2000次
		$CityMod = M('weixin_setting');
        $AccessToken_expire_time = $CityMod->field('value_s')->where(" key_s='AccessToken_expire_time_LIANMESHA' " )->select();
        $AccessToken_expire_time = $AccessToken_expire_time[0]['value_s'];
        
		$CityMod = M('weixin_setting');
        $AccessToken_access_token = $CityMod->field('value_s')->where(" key_s='AccessToken_access_token_LIANMESHA' " )->select();
        $AccessToken_access_token = $AccessToken_access_token[0]['value_s'];
        
        if ($AccessToken_expire_time < time()) {
			// 如果是企业号用以下URL获取access_token
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WX_APPID_LIANMESHA."&secret=".WX_APPSECRET_LIANMESHA."";
			$res = json_decode($this->httpGet($url));
			$access_token = $res->access_token;
			if ($access_token) {
				$AccessToken_expire_time = time() + 7000;
				$AccessToken_access_token = $access_token;
				
				
				$UserMod = M('weixin_setting');
		        $sql=sprintf("UPDATE %s SET value_s='".$AccessToken_expire_time."' where key_s='AccessToken_expire_time_LIANMESHA' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
				$UserMod = M('weixin_setting');
		        $sql=sprintf("UPDATE %s SET value_s='".$AccessToken_access_token."' where key_s='AccessToken_access_token_LIANMESHA' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
			}
		} 
		else {
			$access_token = $AccessToken_access_token;
		}
		
        return $access_token;
		
	}
	
	
	
	
	
	
}
?>