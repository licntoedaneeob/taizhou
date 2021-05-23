<?php
class gameAction extends TAction
{
	
	//授权初始页url：http://kanjia.loc/game/oauth_authorize?goto=http%253A%252F%252Fkanjia.loc%252Fhome%252Fcut%252Fuid%252Fabc001
	public function oauth_authorize(){
		
		if(isset($_REQUEST['state'])){
			$state=$_REQUEST['state'];
		}
		else{
			$state="STATE";
		}
		
		if(isset($_REQUEST['goto'])){
			$goto=$_REQUEST['goto'];
		}
		else{
			$goto="";
		}
		//echo $goto;exit;
		$goto=urlencode($goto);
		//echo $goto;exit;
		
		$redirect_uri=BASE_URL_WEBSITE.U('game/oauth_token')."?goto=".$goto;
		//echo $redirect_uri;exit;
		$redirect_uri=urlencode($redirect_uri);
		//echo $redirect_uri;exit;
		
		
		$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.WX_APPID.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
		//echo $url;exit;
		redirect($url);
		exit;
		
	}
	
	
	//用拿到的code，换取access_token：
	public function oauth_token(){
		
		if(isset($_REQUEST['state'])){
			$state=$_REQUEST['state'];
		}
		else{
			$state='STATE';
		}
		
		if(isset($_REQUEST['code'])){
			$code=$_REQUEST['code'];
		}
		else{
			$code='';
		}
		
		if(isset($_REQUEST['goto'])){
			$goto=$_REQUEST['goto'];
		}
		else{
			$goto="";
		}
		
		//echo $state."<br>";
		//echo $code."<br>";
		//echo $goto."<br>";
		//exit;
		
		$get_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.WX_APPID.'&secret='.WX_APPSECRET.'&code='.$code.'&grant_type=authorization_code';
		$get_return = file_get_contents($get_url);
		$get_return = (array)json_decode($get_return);
		//echo "<pre>";print_r($get_return);
		
		$_SESSION['WX_INFO']=$get_return;
		//echo "kk<pre>";print_r($_SESSION);echo "</pre>";echo "<br><br>";
		
		
		//homeAction.class.php里的这个地方也要改
		if(isset($_SESSION['WX_INFO']['access_token']) && isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
			
			$openid=$_SESSION['WX_INFO']['openid'];
			
			//echo "aa<pre>";print_r($_SESSION['WX_INFO']);echo "</pre>";
			
			
			//拿微信用户信息
			$get_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$_SESSION['WX_INFO']['access_token'].'&openid='.$_SESSION['WX_INFO']['openid'].'&lang=zh_CN';
			$get_return = file_get_contents($get_url);
			$userinfo = (array)json_decode($get_return);
			
			//echo "bb<pre>";print_r($userinfo);echo "</pre>";exit;
			//echo "rr<pre>";print_r($userinfo);echo "</pre>";echo "<br><br>";
			
			
			//网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
			//$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$_SESSION['WX_INFO']['access_token']."&openid=".$_SESSION['WX_INFO']['openid'];
			//echo $url;echo "<br><br>";
			//$get_return_userinfo = file_get_contents($url);
			//$get_return_userinfo = (array)json_decode($get_return_userinfo);
			//echo "uu<pre>";print_r($get_return_userinfo);echo "</pre>";echo "<br><br>";
			
			
		
		
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
		
		//exit;
		
		if(!empty($goto)){
			//echo "gg<pre>";print_r($_SESSION);exit;
			
			$url=$goto;
		}
		else{
			//echo "hh<pre>";print_r($_SESSION);exit;
			//echo "xx<pre>";print_r($_SESSION);echo "</pre>";echo "<br><br>";exit;
			
			$url=U('home/index');
		}
		
        redirect($url);
		exit;
	}
	
	
	
	
	
	
	
	public function oauth_authorize_test(){
		
		$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.WX_APPID.'&redirect_uri=http%3A%2F%2Fcbmee.maxbund.com%2Fgame%2Foauth_token_test&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
		redirect($url);
		exit;
		
	}
	
	
	
	
	public function oauth_token_test(){
		
		if(isset($_REQUEST['code'])){
			$code=$_REQUEST['code'];
		}
		else{
			$code='';
		}
		
		$get_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.WX_APPID.'&secret='.WX_APPSECRET.'&code='.$_REQUEST['code'].'&grant_type=authorization_code';
		$get_return = file_get_contents($get_url);
		$get_return = (array)json_decode($get_return);
		//echo "<pre>";print_r($get_return);
		
		$_SESSION['WX_INFO']=$get_return;
		
		
		if(isset($_SESSION['WX_INFO']['access_token']) && isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
			
			$openid=$_SESSION['WX_INFO']['openid'];
			
			//echo "aa<pre>";print_r($_SESSION['WX_INFO']);echo "</pre>";
			echo $_SESSION['WX_INFO']['access_token'];echo "<br><br>";
			
			//拿微信用户信息
			$get_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$_SESSION['WX_INFO']['access_token'].'&openid='.$_SESSION['WX_INFO']['openid'].'&lang=zh_CN';
			$get_return = file_get_contents($get_url);
			$userinfo = (array)json_decode($get_return);
			
			//echo "bb<pre>";print_r($userinfo);echo "</pre>";exit;
			
			
			$CityMod = M('user');
	        $user_data = $CityMod->field('id')->where(" openid='".addslashes($openid)."' " )->select();
	        if(!empty($user_data)){
	        	//更新
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
	        
		
		}
		
		//exit;
		
		$url='/game/uploadheadpic_weixin_test';
		$a='<br><a href="'.$url.'">'.$url.'</a><br>';
		echo $a;
		//redirect($url);
		exit;
	}
	
	
	
	
	//每隔30秒刷token：http://cbmee.maxbund.com/game/oauth_refresh_token
	public function oauth_refresh_token(){
		
		if(isset($_SESSION['WX_INFO']['refresh_token'])){
		
			//第三步：刷新access_token（如果需要）
			$get_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.WX_APPID.'&grant_type=refresh_token&refresh_token='.$_SESSION['WX_INFO']['refresh_token'].'';
			$get_return = file_get_contents($get_url);
			$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
		
		}
		
		$this->jsonData(0,'成功');
        exit;
        
	}
	
	
	
	
	
    //回答问题是否已满5次
    public function question_is_limit(){
    	
        
    	if(isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
            $openid=$_SESSION['WX_INFO']['openid'];
        }
        else{
        	$this->jsonData(1,'请先授权微信认证');
            exit;
        }
        
        
        $question_limit=5;
        
        $CityMod = M('game');
        $question_info = $CityMod->field('count(id) as num')->where(" openid='".addslashes($openid)."' and is_correct>0 " )->select();
        if(isset($question_info[0]['num']) && $question_info[0]['num']>=$question_limit){
        	$this->jsonData(1,'答题已满'.$question_limit.'次');
            exit;
        }
        
        $this->jsonData(0,'成功');
    }
	
	
	
	
	
    //回答问题
    public function question(){
    	
        
    	if(isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
            $openid=$_SESSION['WX_INFO']['openid'];
        }
        else{
        	$this->jsonData(1,'请先授权微信认证');
            exit;
        }
        
        
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
        
        
        if(isset($_REQUEST['question']) && $_REQUEST['question']!=''){
            $question=$_REQUEST['question'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        if(isset($_REQUEST['answer']) && $_REQUEST['answer']!=''){
            $answer=$_REQUEST['answer'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        if(isset($_REQUEST['is_correct']) && $_REQUEST['is_correct']!=''){
            $is_correct=$_REQUEST['is_correct'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        
        
        
        $question_limit=5;
        
        $CityMod = M('game');
        $question_info = $CityMod->field('count(id) as num')->where(" openid='".addslashes($openid)."' and is_correct>0 " )->select();
        if(isset($question_info[0]['num']) && $question_info[0]['num']>=$question_limit){
        	$this->jsonData(1,'答题已满'.$question_limit.'次');
            exit;
        }
        
        
        
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET question='".addslashes($question)."' 
        , answer='".addslashes($answer)."'
        , is_correct='".addslashes($is_correct)."'
         where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
        $this->jsonData(0,'成功');
    }
	
	
	
	
	
	
    //获得当前用户个人信息  获得当前幸福值
    public function getuserinfo(){
    	
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
        
        $CityMod = M('user');
        $info = $CityMod->field('username, realname , point_total')->where(" id='".addslashes($user_id)."' " )->select();
        $username=isset($info[0]['username'])?$info[0]['username']:"";
        $realname=isset($info[0]['realname'])?$info[0]['realname']:"";
        $point_total=isset($info[0]['point_total'])?$info[0]['point_total']:"";
        
		$data['username']=$username;
		$data['realname']=$realname;
		$data['point_total']=$point_total;
        $this->jsonData(0,'成功',$data);
    }
	
	
	
	
    //游戏1上传头像  提交
    public function game1uploadheadpic(){
    	
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
    	$game_id=1;
    	
		if(isset($_REQUEST['style']) && ($_REQUEST['style']==1 || $_REQUEST['style']==2 || $_REQUEST['style']==3) ){
			$style=$_REQUEST['style'];
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
        
        
		//$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".rand(10,99).".png";
		$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".$user_id.".png";
		
		$path='game_1_style_1_time_150716161543_1.png';
		$dest = BASE_UPLOAD_PATH.$path;
		//echo $dest;exit;
		
		
		/*
		//base64模式接受上传数据
		$f = fopen($dest,'w');
		$img = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABkAAD/4QMraHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjMtYzAxMSA2Ni4xNDU2NjEsIDIwMTIvMDIvMDYtMTQ6NTY6MjcgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzYgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjdENEMwOUUwMzQ4MDExRTRCRTFEODY4NEM4NEQ2OEVFIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjdENEMwOUUxMzQ4MDExRTRCRTFEODY4NEM4NEQ2OEVFIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6N0Q0QzA5REUzNDgwMTFFNEJFMUQ4Njg0Qzg0RDY4RUUiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6N0Q0QzA5REYzNDgwMTFFNEJFMUQ4Njg0Qzg0RDY4RUUiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAABAQEBAQEBAQEBAQEBAQEBAQEB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';
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
		$dest='D:\www\ouliwei\test\ouliwei\cms/public/web_pic/game_1_style_1_time_150716161543_1.jpg';
		
		$file_type=$this->get_file_type($dest); 
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
		
		
		$point=5;
		
		
		$UserMod = M('game1');
        $sql=sprintf("INSERT %s SET headpic='".addslashes($path)."' 
        , imagerotate='".addslashes($pic_imagerotate)."' 
        , style='".addslashes($style)."' 
        , user_id='".addslashes($user_id)."' 
        , create_time='".time()."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        $UserMod = M('user_point');
        $sql=sprintf("INSERT %s SET point='".$point."' 
        , source='game1' 
        , user_id='".addslashes($user_id)."' 
        , create_time='".time()."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        $CityMod = M('user_point');
        $point_total = $CityMod->field('sum(point) as point_total , user_id')->where(" user_id='".addslashes($user_id)."' " )->select();
        $point_total=isset($point_total[0]['point_total'])?$point_total[0]['point_total']:0;
        
        
        $UserMod = M('user');
        $sql=sprintf("UPDATE %s SET point_total='".$point_total."' 
        where id='".addslashes($user_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
		$data['headpic']=BASE_URL."/public/web_pic/".$path;
		$data['style']=$style;
		$data['point_total']=$point_total;
        $this->jsonData(0,'成功',$data);
        
    }
    
    
    
    
    //游戏1上传头像  查看当前用户最近一次提交的头像
    public function game1getheadpic(){
    	
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
    	$game_id=2;
    	
		
        
        $CityMod = M('game1');
        $info = $CityMod->field('headpic, style , user_id')->where(" user_id='".addslashes($user_id)."' " )->order('id desc')->select();
        //echo "<pre>";print_r($info);exit;
        $headpic=isset($info[0]['headpic'])?$info[0]['headpic']:"";
        $style=isset($info[0]['style'])?$info[0]['style']:"";
        
        
		$data['headpic']=BASE_URL."/public/web_pic/".$headpic;
		$data['style']=$style;
        $this->jsonData(0,'成功',$data);
        
    }
    
    
    
    
    
    
    
    
    //游戏2幸福拼图
    public function game2picture(){
    	
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
    	$game_id=2;
    	
		if(isset($_REQUEST['is_finish']) && ($_REQUEST['is_finish']==1 || $_REQUEST['is_finish']==0 ) ){
			$is_finish=$_REQUEST['is_finish'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
		$UserMod = M('game2');
        $sql=sprintf("INSERT %s SET is_finish='".addslashes($is_finish)."' 
        , user_id='".addslashes($user_id)."' 
        , create_time='".time()."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        if($is_finish==1){
        	$point=5;
        }
        else{
        	$point=0;
        }
        
        if($is_finish==1){
	        $UserMod = M('user_point');
	        $sql=sprintf("INSERT %s SET point='".$point."' 
	        , source='game2' 
	        , user_id='".addslashes($user_id)."' 
	        , create_time='".time()."' 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
        }
        
        
        $CityMod = M('user_point');
        $point_total = $CityMod->field('sum(point) as point_total , user_id')->where(" user_id='".addslashes($user_id)."' " )->select();
        $point_total=isset($point_total[0]['point_total'])?$point_total[0]['point_total']:0;
        
        
        $UserMod = M('user');
        $sql=sprintf("UPDATE %s SET point_total='".$point_total."' 
        where id='".addslashes($user_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
		$data['point_total']=$point_total;
        $this->jsonData(0,'成功',$data);
        
    }
    
    
    
    
    
    //游戏3幸福之旅  投票
    public function game3site(){
    	
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
    	$game_id=3;
    	
		if(isset($_REQUEST['site']) && ($_REQUEST['site']==1 || $_REQUEST['site']==2 || $_REQUEST['site']==3 ) ){
			$site=$_REQUEST['site'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		$point=5;
		
		$UserMod = M('game3');
        $sql=sprintf("INSERT %s SET site='".addslashes($site)."' 
        , user_id='".addslashes($user_id)."' 
        , create_time='".time()."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
    	
        $UserMod = M('user_point');
        $sql=sprintf("INSERT %s SET point='".$point."' 
        , source='game3' 
        , user_id='".addslashes($user_id)."' 
        , create_time='".time()."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
    	
        
        
        $CityMod = M('user_point');
        $point_total = $CityMod->field('sum(point) as point_total , user_id')->where(" user_id='".addslashes($user_id)."' " )->select();
        $point_total=isset($point_total[0]['point_total'])?$point_total[0]['point_total']:0;
        
        
        $UserMod = M('user');
        $sql=sprintf("UPDATE %s SET point_total='".$point_total."' 
        where id='".addslashes($user_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
        $UserMod = M('game3_site_ticket');
        $sql=sprintf("UPDATE %s SET site".$site."_ticket=site".$site."_ticket+1  
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        
        $CityMod = M('game3_site_ticket');
        $rst = $CityMod->field('site1_ticket,site2_ticket,site3_ticket')->where(" id='1' " )->select();
        $site1_ticket=isset($rst[0]['site1_ticket'])?$rst[0]['site1_ticket']:0;
        $site2_ticket=isset($rst[0]['site2_ticket'])?$rst[0]['site2_ticket']:0;
        $site3_ticket=isset($rst[0]['site3_ticket'])?$rst[0]['site3_ticket']:0;
        
        
        $data['site1_ticket']=$site1_ticket;
        $data['site2_ticket']=$site2_ticket;
        $data['site3_ticket']=$site3_ticket;
		$data['point_total']=$point_total;
        $this->jsonData(0,'成功',$data);
        
    }
    
    
    
    
    //游戏3幸福之旅  当前票数情况
    public function game3siteticket(){
    	
        
        $CityMod = M('game3_site_ticket');
        $rst = $CityMod->field('site1_ticket,site2_ticket,site3_ticket')->where(" id='1' " )->select();
        $site1_ticket=isset($rst[0]['site1_ticket'])?$rst[0]['site1_ticket']:0;
        $site2_ticket=isset($rst[0]['site2_ticket'])?$rst[0]['site2_ticket']:0;
        $site3_ticket=isset($rst[0]['site3_ticket'])?$rst[0]['site3_ticket']:0;
        
        
        $data['site1_ticket']=$site1_ticket;
        $data['site2_ticket']=$site2_ticket;
        $data['site3_ticket']=$site3_ticket;
        $this->jsonData(0,'成功',$data);
        
    }
    
    
    
    
    //是否允许兑换15分幸福值活动
    public function prize_allow_15( $thisfunc=false ){
    
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
        $prize_type=15;
        
        
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and bag_address!='' " )->select();
        
        if(!empty($rst)){
          	$this->jsonData(1,'您已经参加过'.$prize_type.'分幸福值活动');
        }
        
        
        $CityMod = M('user');
        $userinfo = $CityMod->field('id,point_total')->where(" id='".$user_id."' " )->select();
        //echo "<pre>";print_r($userinfo);exit;
        $point_total=isset($userinfo[0]['point_total'])?$userinfo[0]['point_total']:0;
        //echo $point_total;exit;
        
        if($point_total<$prize_type){
        	$this->jsonData(1,'您的幸福值不够');
        }
        
        if($thisfunc==false){
        	$this->jsonData(0,'成功');
        }
        if($thisfunc==true){
        	return true;
        }
        
    }
    
    
    
    
    
    //是否允许兑换20分幸福值活动(抽奖)
    public function prize_allow_20( $thisfunc=false ){
    
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
        $prize_type=20;
        
        
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and bag_address!='' " )->select();
        
        if(!empty($rst)){
          	$this->jsonData(1,'您已经参加过'.$prize_type.'分幸福值活动');
        }
        
        
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and iphone_is_prize!='0'  " )->select();
        
        if(!empty($rst)){
          	$this->jsonData(1,'您已经参加过'.$prize_type.'分幸福值活动。');
        }
        
        
        $now_date=date('Y-m-d');
        
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and bag_address!='' and now_date='".$now_date."' " )->select();
        
        if(!empty($rst)){
          	$this->jsonData(1,'您今天已经参加过'.$prize_type.'分幸福值活动');
        }
        
        
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and iphone_is_prize!='0' and now_date='".$now_date."'  " )->select();
        
        if(!empty($rst)){
          	$this->jsonData(1,'您今天已经参加过'.$prize_type.'分幸福值活动。');
        }
        
        
        $CityMod = M('user');
        $userinfo = $CityMod->field('id,point_total')->where(" id='".$user_id."' " )->select();
        //echo "<pre>";print_r($userinfo);exit;
        $point_total=isset($userinfo[0]['point_total'])?$userinfo[0]['point_total']:0;
        //echo $point_total;exit;
        
        if($point_total<$prize_type){
        	$this->jsonData(1,'您的幸福值不够');
        }
        
        
        if($thisfunc==false){
        	$this->jsonData(0,'成功');
        }
        if($thisfunc==true){
        	return true;
        }
        
    }
    
    
    
    
    	
    	
    
    
    //提交问卷
    public function prize_survey(){
    	
        
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
		
        
    	
		if(isset($_REQUEST['prize_type']) && ($_REQUEST['prize_type']==15 || $_REQUEST['prize_type']==20 ) ){
			$prize_type=$_REQUEST['prize_type'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		if(isset($_REQUEST['survey_address']) && $_REQUEST['survey_address']!="" ){
			$survey_address=$_REQUEST['survey_address'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
		if(isset($_REQUEST['survey_mobile']) && $_REQUEST['survey_mobile']!="" ){
			$survey_mobile=$_REQUEST['survey_mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
		if($_SESSION['userinfo']['username']!=$survey_mobile){
			$this->jsonData(1,'您提交的手机与您当前登陆的手机号不符，请使用您当前登陆的手机号参与活动。');
            exit;
		}
		
		
		if($prize_type==15){
			$resp=$this->prize_allow_15( true );
		}
		
		if($prize_type==20){
			$resp=$this->prize_allow_20( true );
		}
		
		//var_dump($resp);exit;
		
		if($resp==true){
			//可以继续
		}
		
		
		
		$now_date=date('Y-m-d');
		$survey_1=isset($_REQUEST['survey_1'])?$_REQUEST['survey_1']:'';
		$survey_2=isset($_REQUEST['survey_2'])?$_REQUEST['survey_2']:'';
		$survey_3=isset($_REQUEST['survey_3'])?$_REQUEST['survey_3']:'';
		$survey_4=isset($_REQUEST['survey_4'])?$_REQUEST['survey_4']:'';
		$survey_5=isset($_REQUEST['survey_5'])?$_REQUEST['survey_5']:'';
		$survey_6=isset($_REQUEST['survey_6'])?$_REQUEST['survey_6']:'';
		$survey_7=isset($_REQUEST['survey_7'])?$_REQUEST['survey_7']:'';
		$survey_8=isset($_REQUEST['survey_8'])?$_REQUEST['survey_8']:'';
		$survey_9=isset($_REQUEST['survey_9'])?$_REQUEST['survey_9']:'';
		$survey_10=isset($_REQUEST['survey_10'])?$_REQUEST['survey_10']:'';
		$survey_address=isset($_REQUEST['survey_address'])?$_REQUEST['survey_address']:'';
		$survey_mobile=isset($_REQUEST['survey_mobile'])?$_REQUEST['survey_mobile']:'';
		
		
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and now_date='".$now_date."' " )->select();
        if(isset($rst[0])){
        	//更新
        	$prize_history_id=$rst[0]['id'];
        	
        	$UserMod = M('prize_history');
	        $sql=sprintf("UPDATE %s SET 
	         survey_1='".$survey_1."' 
	        , survey_2='".$survey_2."' 
	        , survey_3='".$survey_3."' 
	        , survey_4='".$survey_4."' 
	        , survey_5='".$survey_5."' 
	        , survey_6='".$survey_6."' 
	        , survey_7='".$survey_7."' 
	        , survey_8='".$survey_8."' 
	        , survey_9='".$survey_9."' 
	        , survey_10='".$survey_10."' 
	        , survey_address='".$survey_address."' 
	        , survey_mobile='".$survey_mobile."' 
	        where id=".$prize_history_id." 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
        }
        else{
        	//新增
        	
	        $UserMod = M('prize_history');
	        $sql=sprintf("INSERT %s SET user_id='".$user_id."' 
	        , prize_type='".$prize_type."' 
	        , now_date='".$now_date."' 
	        , survey_1='".$survey_1."' 
	        , survey_2='".$survey_2."' 
	        , survey_3='".$survey_3."' 
	        , survey_4='".$survey_4."' 
	        , survey_5='".$survey_5."' 
	        , survey_6='".$survey_6."' 
	        , survey_7='".$survey_7."' 
	        , survey_8='".$survey_8."' 
	        , survey_9='".$survey_9."' 
	        , survey_10='".$survey_10."' 
	        , survey_address='".$survey_address."' 
	        , survey_mobile='".$survey_mobile."' 
	        , create_time='".time()."' 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
        }
        
        $this->jsonData(0,'成功');
        
    }
    
    
    
    
    
    
    //幸福礼包领取（15分活动的情况 或 20分活动没中奖的情况）
    public function prize_bag(){
    	
        
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
    	
		if(isset($_REQUEST['prize_type']) && ($_REQUEST['prize_type']==15 || $_REQUEST['prize_type']==20 ) ){
			$prize_type=$_REQUEST['prize_type'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		if($prize_type==15){
			$resp=$this->prize_allow_15( true );
		}
		
		if($prize_type==20){
			$resp=$this->prize_allow_20( true );
		}
		
		//var_dump($resp);exit;
		
		if($resp==true){
			//可以继续
		}
		
		
		
		
		if($prize_type==15){
			//扣除幸福值
	        $point='-15';
	        $UserMod = M('user_point');
	        $sql=sprintf("INSERT %s SET point='".$point."' 
	        , source='prize15' 
	        , user_id='".addslashes($user_id)."' 
	        , create_time='".time()."' 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
	        
	        $CityMod = M('user_point');
	        $point_total = $CityMod->field('sum(point) as point_total , user_id')->where(" user_id='".addslashes($user_id)."' " )->select();
	        $point_total=isset($point_total[0]['point_total'])?$point_total[0]['point_total']:0;
	        
	        
	        $UserMod = M('user');
	        $sql=sprintf("UPDATE %s SET point_total='".$point_total."' 
	        where id='".addslashes($user_id)."' 
	        ", $UserMod->getTableName() );
	        $result = $UserMod->execute($sql);
	        //扣除幸福值
        }
        
		
		
		$now_date=date('Y-m-d');
		$bag_area=isset($_REQUEST['bag_area'])?$_REQUEST['bag_area']:'';
		$bag_address=isset($_REQUEST['bag_address'])?$_REQUEST['bag_address']:'';
		
		
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and now_date='".$now_date."' " )->select();
        if(isset($rst[0])){
        	//更新
        	$prize_history_id=$rst[0]['id'];
        	
        	$UserMod = M('prize_history');
	        $sql=sprintf("UPDATE %s SET 
	         bag_area='".$bag_area."' 
	        , bag_address='".$bag_address."' 
	        where id=".$prize_history_id." 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
        }
        else{
        	//新增
        	
	        $UserMod = M('prize_history');
	        $sql=sprintf("INSERT %s SET user_id='".$user_id."' 
	        , prize_type='".$prize_type."' 
	        , now_date='".$now_date."' 
	        , bag_area='".$bag_area."' 
	        , bag_address='".$bag_address."' 
	        , create_time='".time()."' 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
        }
        
        $this->jsonData(0,'成功');
        
    }
    
    
    
    
    //请求抽iphone奖
    public function prize_iphone(){
    	
        
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
    	
		$prize_type=20;
		
		
		if($prize_type==15){
			$resp=$this->prize_allow_15( true );
		}
		
		if($prize_type==20){
			$resp=$this->prize_allow_20( true );
		}
		
		//var_dump($resp);exit;
		
		if($resp==true){
			//可以继续
		}
		
		
		
		$rand_num=rand(0, 10000);
		$is_prize_key=9;   //rand_num等于is_prize_key的时候，为中奖。
		
		if($rand_num==$is_prize_key){
			$iphone_is_prize="1";
		}
		else{
			$iphone_is_prize="2";
		}
		
		//echo $rand_num;exit;
		
		$now_date=date('Y-m-d');
		
		
		
		$CityMod = M('prize_history');
        $sury = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and now_date='".$now_date."' and survey_mobile!='' " )->select();
        if(empty($sury)){
        	$this->jsonData(1,'请先完成问卷调查');
        }
		
		
		$CityMod = M('prize_history');
        $sury = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and iphone_is_prize=1 " )->select();
        if(!empty($sury)){
        	$iphone_is_prize="2";
        	//$this->jsonData(1,'您已经中过奖了');
        }
        
        
        
        
        
        
        //扣除幸福值
        $point='-20';
        $UserMod = M('user_point');
        $sql=sprintf("INSERT %s SET point='".$point."' 
        , source='prize20' 
        , user_id='".addslashes($user_id)."' 
        , create_time='".time()."' 
        ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
        
        
        $CityMod = M('user_point');
        $point_total = $CityMod->field('sum(point) as point_total , user_id')->where(" user_id='".addslashes($user_id)."' " )->select();
        $point_total=isset($point_total[0]['point_total'])?$point_total[0]['point_total']:0;
        
        
        $UserMod = M('user');
        $sql=sprintf("UPDATE %s SET point_total='".$point_total."' 
        where id='".addslashes($user_id)."' 
        ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        //扣除幸福值
        
        
        
        
		
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and now_date='".$now_date."' " )->select();
        if(isset($rst[0])){
        	//更新
        	$prize_history_id=$rst[0]['id'];
        	
        	$UserMod = M('prize_history');
	        $sql=sprintf("UPDATE %s SET 
	         iphone_is_prize='".$iphone_is_prize."' 
	        where id=".$prize_history_id." 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
        }
        else{
        	//新增
        	
	        $UserMod = M('prize_history');
	        $sql=sprintf("INSERT %s SET user_id='".$user_id."' 
	        , prize_type='".$prize_type."' 
	        , now_date='".$now_date."' 
	        , iphone_is_prize='".$iphone_is_prize."' 
	        , create_time='".time()."' 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
        }
        
        $data['iphone_is_prize']=$iphone_is_prize;
        $this->jsonData(0,'成功',$data);
        
    }
    
    
    
    
    
    
    //抽iphone中奖后提交个人信息
    public function prize_iphone_userinfo(){
    	
        
    	if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
            $user_id=$_SESSION['userinfo']['id'];
        }
        else{
        	$this->jsonData(1,'请先登陆。');
            exit;
        }
        
    	
    	
		if(isset($_REQUEST['iphone_realname']) && $_REQUEST['iphone_realname']!="" ){
			$iphone_realname=$_REQUEST['iphone_realname'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
		if(isset($_REQUEST['iphone_idcard']) && $_REQUEST['iphone_idcard']!="" ){
			$iphone_idcard=$_REQUEST['iphone_idcard'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
		if(isset($_REQUEST['iphone_mobile']) && $_REQUEST['iphone_mobile']!="" ){
			$iphone_mobile=$_REQUEST['iphone_mobile'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
		if(isset($_REQUEST['iphone_address']) && $_REQUEST['iphone_address']!="" ){
			$iphone_address=$_REQUEST['iphone_address'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
		
		
		$prize_type=20;
		
		
		//if($prize_type==15){
		//	$resp=$this->prize_allow_15( true );
		//}
		
		//if($prize_type==20){
		//	$resp=$this->prize_allow_20( true );
		//}
		
		//var_dump($resp);exit;
		
		if($resp==true){
			//可以继续
		}
		
		
		
		
		$now_date=date('Y-m-d');
		
		
		
        
        
		
		
		$CityMod = M('prize_history');
        $sury = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and now_date='".$now_date."' and survey_mobile!='' " )->select();
        if(empty($sury)){
        	$this->jsonData(1,'请先完成问卷调查');
        }
		
		
		$CityMod = M('prize_history');
        $sury = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and now_date='".$now_date."' and iphone_is_prize=1 " )->select();
        if(empty($sury)){
        	$this->jsonData(1,'您需要先中奖，之后才能填写个人信息');
        }
		
		
		$CityMod = M('prize_history');
        $sury = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and now_date='".$now_date."' and iphone_mobile!=''  " )->select();
        if(!empty($sury)){
        	$this->jsonData(1,'您已经填写过个人信息');
        }
		
		
		
		if($_SESSION['userinfo']['username']!=$iphone_mobile){
			$this->jsonData(1,'您提交的手机与您当前登陆的手机号不符，请使用您当前登陆的手机号参与活动。');
            exit;
		}
		
		
		
		
        $CityMod = M('prize_history');
        $rst = $CityMod->field('id')->where(" user_id='".$user_id."' and prize_type='".$prize_type."' and now_date='".$now_date."' and iphone_is_prize=1 " )->select();
        if(isset($rst[0])){
        	//更新
        	$prize_history_id=$rst[0]['id'];
        	
        	$UserMod = M('prize_history');
	        $sql=sprintf("UPDATE %s SET 
	         iphone_realname='".$iphone_realname."' 
	        ,iphone_idcard='".$iphone_idcard."' 
	        ,iphone_mobile='".$iphone_mobile."' 
	        ,iphone_address='".$iphone_address."' 
	        where id=".$prize_history_id." 
	        ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $result = $UserMod->execute($sql);
	        
        }
        else{
        	$this->jsonData(1,'失败');
        }
        
        
        $this->jsonData(0,'成功');
        
    }
    
    
    
    //end
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
	//日志。
    public function logs(){
    	
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
        
        if(isset($_REQUEST['btn']) && $_REQUEST['btn']>0 ){
			$btn=$_REQUEST['btn'];
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
        $LogMod = M('log');
        $LogMod->btn=$btn;
        $LogMod->game_id=$game_id;
        $LogMod->addtime=date("Y-m-d H:i:s",time());
        $LogMod->add();
        
        $data['game_id']=$game_id;
        $this->jsonData(0,'成功',$data);
        exit;
    }
    
    
	
    //登记用户进入活动，点击开始参与活动。
    public function userstart(){
    	
    	
    	if(isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
            $openid=$_SESSION['WX_INFO']['openid'];
        }
        else{
        	$openid='';
        }
        
        
        
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
        	$game_id=$_SESSION['game_id'];
            $data['game_id']=$game_id;
            
            $this->jsonData(0,'成功',$data);
            exit;
        }
        else{
        	$cli_os=$this->get_client_os();
            $cur_time=time();
            
            $UserMod = M('game');
            $UserMod->openid=$openid;
			$UserMod->cli_os=$cli_os;
            $UserMod->modify_time=$cur_time;
            $UserMod->create_time=$cur_time;
            $UserMod->addtime=date("Y-m-d H:i:s",$cur_time);

            $game_id = $UserMod->add();

            if($game_id>0){
                $data['game_id']=$game_id;
                $_SESSION['game_id']=$game_id;
                
                $this->jsonData(0,'成功',$data);
                exit;
            }
            else{
                $this->jsonData(1,'失败');
                exit;
            }
        }
    }
    
    
    //点击重新参与活动
    public function restart(){
    	$this->clear_game();
        $this->userstart();
    }
    


    //验证兑换码是否正确
    public function checkcode($code=""){
        $para['no']=$code;
        $para['sign']=md5('game_2014_beta2'.$code);
        //echo "<pre>";print_r($para);exit;
        $req_url='http://g2.reloadbuzz.com/crunchywafer/checkCode';
        $result=$this->http_request_url_post($req_url,$para);
        //echo "<pre>";print_r($result);exit;
        //var_dump(json_decode($result));exit;
        //$rst=str_replace('callback(','',$result);
        //$rst=str_replace(');','',$rst);
        //$rst_arr=json_decode($rst,true);
        //$rst_arr['code']=0;  //强制返回验证成功
        if(isset($result['code']) && $result['code']==0){
        	return true;
        }
        
        return false;
    }



    //兑换码是否被使用过
    public function isused($code=""){
    	//不判断是否重复使用
    	return false;
    	
        $CityMod = M('game');
        $isused_code = $CityMod->field('id,code')->where(" code='".addslashes($code)."' and code_isused=1 " )->order('id desc')->select();
        if($isused_code==false){
            return false;
        }
        else{
            return true;
        }
    }


    //使用兑换码
    public function usecode(){
		$code=$_REQUEST['code'];
		
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }

        $checkcode=$this->checkcode($code);
        //var_dump($checkcode);exit;
        if($checkcode==false){
            $data['game_id']=$game_id;
            $data['code']=$code;
            $this->jsonData(1,'兑换码验证失败',$data);
            exit;
        }
		
        $isusedcode=$this->isused($code);
        if($isusedcode==true){
            $data['game_id']=$game_id;
            $data['code']=$code;
            $this->jsonData(1,'该兑换码已被使用过',$data);
            exit;
        }
		
        $UserMod = M('game');
        $sql=sprintf("UPDATE %s SET code_isused='1',code='".addslashes($code)."' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        $data['game_id']=$game_id;
        $data['code']=$code;
        $this->jsonData(0,'成功',$data);
        exit;
        

        if($result==1){
            $data['game_id']=$game_id;
            $data['code']=$code;
            $this->jsonData(0,'成功',$data);
            exit;
        }
        else{
            $data['game_id']=$game_id;
            $data['code']=$code;
            $this->jsonData(1,'失败',$data);
            exit;
        }

    }
	
	
	//获得当前用户所有信息
	public function userinfo(){
		
		if(isset($_REQUEST['game_id']) && $_REQUEST['game_id']>0 ){
			$game_id=$_REQUEST['game_id'];
		}
		else{
			if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
			    $game_id=$_SESSION['game_id'];
			}
			else{
			    $this->jsonData(1,'请先参与游戏');
			    exit;
			}
		}
		
		
		
		
		$CityMod = M('game');
        $userinfo = $CityMod->field('id as game_id
        ,headpic
        ,headegg
        ,gamefilter
        ,isfenxiang
        
        ')->where(" id='".$game_id."' " )->order('id desc')->limit('0,1')->select();
        if(isset($userinfo[0])){
        	$userinfo=$userinfo[0];
        	$userinfo['headpic']=empty($userinfo['headpic'])?"":BASE_URL."/public/web_pic/".$userinfo['headpic'];
        	$userinfo['headegg']=empty($userinfo['headegg'])?"":BASE_URL."/public/web_resize/".$userinfo['headegg'];
        	$userinfo['gamefilter']=empty($userinfo['gamefilter'])?"":BASE_URL."/public/web_filter/".$userinfo['gamefilter'];
        	
        	//echo "<pre>";print_r($userinfo);exit;
        	
        	$data=$userinfo;
        	
            $this->jsonData(0,'成功',$data);
            exit;
        }
        else{
        	$data['game_id']=$game_id;
            $data['code']=$code;
            $data=array();
            $this->jsonData(1,'失败',$data);
            exit;
        }
	}

	 
	 

	 //叠加相框合成最终效果图
	 public function gamefinish(){
	 	
	 	
	 	if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
        
		
		$CityMod = M('game');
		$userinfo = $CityMod->field('id,headegg')->where(" id='".addslashes($game_id)."' " )->order('id desc')->limit('0,1')->select();
        //echo "<pre>";print_r($userinfo[0]);exit;
        if(!isset($userinfo[0])){
        	$this->jsonData(1,'请先参与游戏');
            exit;
        }
        $userinfo=$userinfo[0];
        
		
		$org_path_1=empty($userinfo['headegg'])?"":BASE_PIC_RESIZE_PATH.$userinfo['headegg'];
		
		//调试
		//$org_path_1='D:/www/nac/public/web_resize/1812_resize_150719190818_24.png';   //本地环境
		//$org_path_1='D:/www/ford/ccsfriend/cbmee/public/web_resize/1812_resize_150719190818_24.png';  //服务器环境
		
		$background_path=BASE_PIC_BACKGROUND_PATH.'game_background.png';
		
		
		$path=$game_id."_filter_".date('ymdHis')."_".rand(10,99).".png";
		$out_path=BASE_PIC_FILTER_PATH.$path;
		
		/*
		裁切的图：346*385
		相框底图：545*553
		宽相减除以2，高相减除以2。
		距离左边99.5   距离上边84
		带阴影：95   71
		不带阴影：45   47
		*/
		
		$dst_x_1=45; //父亲坐标，以左上角为基点
		$dst_y_1=47; //父亲坐标，以左上角为基点
		$dst_x_2=""; //母亲坐标，以左上角为基点
		$dst_y_2=""; //母亲坐标，以左上角为基点
		$dst_x_3=""; //小孩坐标，以左上角为基点
		$dst_y_3=""; //小孩坐标，以左上角为基点
		
		
		$percent="";
		$percent_show="";
		$score_x="";
		$score_y="";
		$percent_x="";
		$percent_y="";
		
		
		$filter_rst=$this->combine_pic_single($background_path,$out_path,$org_path_1,$dst_x_1,$dst_y_1,$score,$percent_show,$score_x,$score_y,$percent_x,$percent_y);
		
		
		if($filter_rst==true){
	        $UserMod = M('game');
	        $sql=sprintf("UPDATE %s SET gamefilter='".$path."'  where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
	        $result = $UserMod->execute($sql);
	        
	        $data['game_id']=$game_id;
	        $data['pic_url']=BASE_URL."/public/web_filter/".$path;
	        $this->jsonData(0,'成功',$data);
        }
        else{
        	$this->jsonData(1,'图片合成失败');
            exit;
        }
        exit;
	 }
	 
	 
	 
	 
	 
	 
	 
	 
	 //图片合成（单张）
	 public function combine_pic($org_path,$background_path,$out_path,$dst_x,$dst_y){
	 	
		//$org_path ='D:/www/cuicuisha/gd_test/out.jpg'; //原始图片（此处原始图，即之前裁切后的图片）
		//$background_path = "D:/www/cuicuisha/gd_test/back.png";  //底图
		//$out_path = 'D:/www/cuicuisha/gd_test/gamepic.jpg';  //目标图片

		//$dst_x=550;  //原始图坐落到背景画布的坐标x（根据需求写死），以左上角为基点
		//$dst_y=250;  //原始图坐落到背景画布的坐标y（根据需求写死），以左上角为基点
		
		//叠加图片及图片合成

		$img_background_info=getimagesize($background_path);
		//echo "<pre>";print_r($img_background_info);exit;
		$bg_width=$img_background_info[0];
		$bg_height=$img_background_info[1];


		$img_org_info=getimagesize($org_path);
		//echo "<pre>";print_r($img_org_info);exit;
		$org_width=$img_org_info[0];
		$org_height=$img_org_info[1];



		$image_dst = imagecreatetruecolor($bg_width, $bg_height);  //创建一个新的目标图像

		//imagealphablending($image_dst,false);  //合并图像的时候，这句千万不能加上，否则合并出来的上层图片会遮挡下层图片。
		imagesavealpha($image_dst,true);  //这里很重要,意思是不要丢了$thumb图像的透明色;



		$white=imagecolorallocate($image_dst,255,255,255); 
		$black=imagecolorallocate($image_dst,0,0,0); 
		$red=imagecolorallocate($image_dst,255,0,0); 
		imagefill($image_dst,0,0,$white); 

		imagejpeg($image_dst, $out_path, 100);


		$img_org = @imagecreatefromjpeg($org_path);  //从原始图获取所有内容
		imagecopy($image_dst,$img_org,$dst_x,$dst_y,0,0,$org_width,$org_height);  //将原始图叠加到背景画布上



		$img_bg = @imagecreatefrompng($background_path);  //从原始图获取所有内容
		imagecopy($image_dst,$img_bg,0,0,0,0,$bg_width,$bg_height);  //将原始图叠加到背景画布上


		imagejpeg($image_dst, $out_path, 100);
	 	return true;
	 }
	 
	 
	 
	 
	 //图片合成（multi多张）
	 public function combine_pic_multi($background_path,$out_path,$org_path_1="",$dst_x_1="",$dst_y_1="",$org_path_2="",$dst_x_2="",$dst_y_2="",$org_path_3="",$dst_x_3="",$dst_y_3="",$score="",$percent="",$score_x="",$score_y="",$percent_x="",$percent_y=""){
	 	
		//$org_path ='D:/www/cuicuisha/gd_test/out.jpg'; //原始图片（此处原始图，即之前裁切后的图片）
		//$background_path = "D:/www/cuicuisha/gd_test/back.png";  //底图
		//$out_path = 'D:/www/cuicuisha/gd_test/gamepic.jpg';  //目标图片

		//$dst_x=550;  //原始图坐落到背景画布的坐标x（根据需求写死），以左上角为基点
		//$dst_y=250;  //原始图坐落到背景画布的坐标y（根据需求写死），以左上角为基点
		
		//叠加图片及图片合成

		$img_background_info=getimagesize($background_path);
		//echo "<pre>";print_r($img_background_info);exit;
		$bg_width=$img_background_info[0];
		$bg_height=$img_background_info[1];


		$img_org_info=getimagesize($org_path_1);
		$org_width_1=$img_org_info[0];
		$org_height_1=$img_org_info[1];

		$img_org_info=getimagesize($org_path_2);
		$org_width_2=$img_org_info[0];
		$org_height_2=$img_org_info[1];
		
		
		$img_org_info=getimagesize($org_path_3);
		$org_width_3=$img_org_info[0];
		$org_height_3=$img_org_info[1];
		
		

		$image_dst = imagecreatetruecolor($bg_width, $bg_height);  //创建一个新的目标图像

		//imagealphablending($image_dst,false);  //合并图像的时候，这句千万不能加上，否则合并出来的上层图片会遮挡下层图片。
		imagesavealpha($image_dst,true);  //这里很重要,意思是不要丢了$thumb图像的透明色;

		$white=imagecolorallocate($image_dst,255,255,255); 
		$black=imagecolorallocate($image_dst,0,0,0); 
		$red=imagecolorallocate($image_dst,255,0,0); 
		imagefill($image_dst,0,0,$white); 

		imagejpeg($image_dst, $out_path, 100);
		
		if($org_path_1!="" && $dst_x_1!="" && $dst_y_1!=""){
			//合并第1张原始图
			//echo $dst_x_1;echo "<br>";
			//echo $dst_y_1;echo "<br>";
			//echo $org_width_1;echo "<br>";
			//echo $org_height_1;echo "<br>";echo "<br>";
			$img_org = @imagecreatefromjpeg($org_path_1);  //从原始图获取所有内容
			imagecopy($image_dst,$img_org,$dst_x_1,$dst_y_1,0,0,$org_width_1,$org_height_1);  //将原始图叠加到背景画布上
		}
		
		if($org_path_2!="" && $dst_x_2!="" && $dst_y_2!=""){
			//合并第2张原始图
			//echo $dst_x_2;echo "<br>";
			//echo $dst_y_2;echo "<br>";
			//echo $org_width_2;echo "<br>";
			//echo $org_height_2;echo "<br>";echo "<br>";
			$img_org = @imagecreatefromjpeg($org_path_2);  //从原始图获取所有内容
			imagecopy($image_dst,$img_org,$dst_x_2,$dst_y_2,0,0,$org_width_2,$org_height_2);  //将原始图叠加到背景画布上
		}
		
		if($org_path_3!="" && $dst_x_3!="" && $dst_y_3!=""){
			//合并第3张原始图
			//echo $dst_x_3;echo "<br>";
			//echo $dst_y_3;echo "<br>";
			//echo $org_width_3;echo "<br>";
			//echo $org_height_3;echo "<br>";echo "<br>";
			$img_org = @imagecreatefromjpeg($org_path_3);  //从原始图获取所有内容
			imagecopy($image_dst,$img_org,$dst_x_3,$dst_y_3,0,0,$org_width_3,$org_height_3);  //将原始图叠加到背景画布上
		}
		

		$img_bg = @imagecreatefrompng($background_path);  //从原始图获取所有内容
		imagecopy($image_dst,$img_bg,0,0,0,0,$bg_width,$bg_height);  //将原始图叠加到背景画布上
		
		
		//无法指定文字大小和字体的做法：
		//$textcolor = imagecolorallocate($im, 0, 0, 255);  //水印文字
		//imagestring($image_dst, 4, 100, 100, "286", $textcolor);
		//$textcolor = imagecolorallocate($im, 0, 0, 255);  //水印文字
		//imagestring($image_dst, 4, 200, 200, "10%", $textcolor);
		
		
		if($score!=""){
			//可指定文字大小和字体的做法：
			$black = imagecolorallocate($im, 0, 0, 0);
			$font = ROOT_PATH.'/statics/font/arial.ttf';
			//image 图像资源 , size 字体大小 , angle 角度 , x , y , color , fontfile , text 
			$text = $score;
			imagettftext($image_dst, 16, 0, $score_x, $score_y, $black, $font, $text);
			$text = $percent;
			imagettftext($image_dst, 16, 0, $percent_x, $percent_y, $black, $font, $text);
		}
		
		
		imagejpeg($image_dst, $out_path, 100);
		//imagepng($image_dst, $out_path);
		
	 	return true;
	 }
	 
	 
	 
	 
	 //图片合成（1张）
	 public function combine_pic_single($background_path,$out_path,$org_path_1="",$dst_x_1="",$dst_y_1="",$score="",$percent="",$score_x="",$score_y="",$percent_x="",$percent_y=""){
	 	
		//$org_path ='D:/www/cuicuisha/gd_test/out.jpg'; //原始图片（此处原始图，即之前裁切后的图片）
		//$background_path = "D:/www/cuicuisha/gd_test/back.png";  //底图
		//$out_path = 'D:/www/cuicuisha/gd_test/gamepic.jpg';  //目标图片

		//$dst_x=550;  //原始图坐落到背景画布的坐标x（根据需求写死），以左上角为基点
		//$dst_y=250;  //原始图坐落到背景画布的坐标y（根据需求写死），以左上角为基点
		
		//叠加图片及图片合成

		$img_background_info=getimagesize($background_path);
		//echo "<pre>";print_r($img_background_info);exit;
		$bg_width=$img_background_info[0];
		$bg_height=$img_background_info[1];


		$img_org_info=getimagesize($org_path_1);
		$org_width_1=$img_org_info[0];
		$org_height_1=$img_org_info[1];

		
		$image_dst = imagecreatetruecolor($bg_width, $bg_height);  //创建一个新的目标图像

		//imagealphablending($image_dst,false);  //合并图像的时候，这句千万不能加上，否则合并出来的上层图片会遮挡下层图片。
		imagesavealpha($image_dst,true);  //这里很重要,意思是不要丢了$thumb图像的透明色;

		$white=imagecolorallocate($image_dst,255,255,255); 
		$black=imagecolorallocate($image_dst,0,0,0); 
		$red=imagecolorallocate($image_dst,255,0,0); 
		imagefill($image_dst,0,0,$white); 

		imagejpeg($image_dst, $out_path, 100);
		
		if($org_path_1!="" && $dst_x_1!="" && $dst_y_1!=""){
			//合并第1张原始图
			//echo $dst_x_1;echo "<br>";
			//echo $dst_y_1;echo "<br>";
			//echo $org_width_1;echo "<br>";
			//echo $org_height_1;echo "<br>";echo "<br>";
			$img_org = @imagecreatefromjpeg($org_path_1);  //从原始图获取所有内容
			imagecopy($image_dst,$img_org,$dst_x_1,$dst_y_1,0,0,$org_width_1,$org_height_1);  //将原始图叠加到背景画布上
		}
		

		$img_bg = @imagecreatefrompng($background_path);  //从原始图获取所有内容
		imagecopy($image_dst,$img_bg,0,0,0,0,$bg_width,$bg_height);  //将原始图叠加到背景画布上
		
		
		//无法指定文字大小和字体的做法：
		//$textcolor = imagecolorallocate($im, 0, 0, 255);  //水印文字
		//imagestring($image_dst, 4, 100, 100, "286", $textcolor);
		//$textcolor = imagecolorallocate($im, 0, 0, 255);  //水印文字
		//imagestring($image_dst, 4, 200, 200, "10%", $textcolor);
		
		
		if($score!=""){
			//可指定文字大小和字体的做法：
			$black = imagecolorallocate($im, 0, 0, 0);
			$font = ROOT_PATH.'/statics/font/arial.ttf';
			//image 图像资源 , size 字体大小 , angle 角度 , x , y , color , fontfile , text 
			$text = $score;
			imagettftext($image_dst, 16, 0, $score_x, $score_y, $black, $font, $text);
			$text = $percent;
			imagettftext($image_dst, 16, 0, $percent_x, $percent_y, $black, $font, $text);
		}
		
		
		imagejpeg($image_dst, $out_path, 100);
		//imagepng($image_dst, $out_path);
		
	 	return true;
	 }
	 
	 
	 //生成相册图
	 public function makealbum(){
	 	if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
        
		$CityMod = M('game');
        $userinfo = $CityMod->field('id as game_id
        ,head1egg
        ,head2egg
        ,head3egg
        ')->where(" id='".$game_id."' " )->order('id desc')->limit('0,1')->select();
        if(isset($userinfo[0])){
        	$userinfo=$userinfo[0];
        	//echo "<pre>";print_r($userinfo);exit;
        	
        	$org_path_1=empty($userinfo['head1egg'])?"":BASE_PIC_RESIZE_PATH.$userinfo['head1egg'];
			$org_path_2=empty($userinfo['head2egg'])?"":BASE_PIC_RESIZE_PATH.$userinfo['head2egg'];
			$org_path_3=empty($userinfo['head3egg'])?"":BASE_PIC_RESIZE_PATH.$userinfo['head3egg'];
			
        	if($userinfo['head1egg']=="" && $userinfo['head2egg']=="" && $userinfo['head3egg']=="" ){
	        	$data['game_id']=$game_id;
	            $this->jsonData(1,'失败',$data);
	            exit;
        	}
        	
        	$album_arr[]=1;
	        $album_arr[]=2;
	        $album_arr[]=3;
	        $album_arr[]=4;
	        $album_arr[]=5;
	        
	        //循环遍历5个相册底图
	        foreach ($album_arr as $k => $v) {
	        	
	        	$album_no=$v;
	        	
	        	if($userinfo['head1egg']=="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
					$background_path=BASE_PIC_BACKGROUND_ALBUM_PATH.'album'.$album_no.'/'.'album_'.$album_no.'_role_0_0_1.png';
				}
				elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
					$background_path=BASE_PIC_BACKGROUND_ALBUM_PATH.'album'.$album_no.'/'.'album_'.$album_no.'_role_0_1_0.png';
				}
				elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
					$background_path=BASE_PIC_BACKGROUND_ALBUM_PATH.'album'.$album_no.'/'.'album_'.$album_no.'_role_0_1_1.png';
				}
				elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']=="" ){
					$background_path=BASE_PIC_BACKGROUND_ALBUM_PATH.'album'.$album_no.'/'.'album_'.$album_no.'_role_1_0_0.png';
				}
				elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
					$background_path=BASE_PIC_BACKGROUND_ALBUM_PATH.'album'.$album_no.'/'.'album_'.$album_no.'_role_1_0_1.png';
				}
				elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
					$background_path=BASE_PIC_BACKGROUND_ALBUM_PATH.'album'.$album_no.'/'.'album_'.$album_no.'_role_1_1_0.png';
				}
				elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
					$background_path=BASE_PIC_BACKGROUND_ALBUM_PATH.'album'.$album_no.'/'.'album_'.$album_no.'_role_1_1_1.png';
				}
				else{
					$this->jsonData(1,'缺少头像图');
		            exit;
				}
				
				
				
				if($album_no==1){
					//这里还分7这种情况
					if($userinfo['head1egg']=="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=250; //小孩坐标，以左上角为基点
						$dst_y_3=635; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=385; //母亲坐标，以左上角为基点
						$dst_y_2=530; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=395; //母亲坐标，以左上角为基点
						$dst_y_2=520; //母亲坐标，以左上角为基点
						$dst_x_3=260; //小孩坐标，以左上角为基点
						$dst_y_3=665; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']=="" ){
						$dst_x_1=70; //父亲坐标，以左上角为基点
						$dst_y_1=512; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=65; //父亲坐标，以左上角为基点
						$dst_y_1=502; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=245; //小孩坐标，以左上角为基点
						$dst_y_3=640; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=75; //父亲坐标，以左上角为基点
						$dst_y_1=512; //父亲坐标，以左上角为基点
						$dst_x_2=385; //母亲坐标，以左上角为基点
						$dst_y_2=530; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=80; //父亲坐标，以左上角为基点
						$dst_y_1=522; //父亲坐标，以左上角为基点
						$dst_x_2=385; //母亲坐标，以左上角为基点
						$dst_y_2=510; //母亲坐标，以左上角为基点
						$dst_x_3=250; //小孩坐标，以左上角为基点
						$dst_y_3=675; //小孩坐标，以左上角为基点
					}
					else{
						$this->jsonData(1,'参数错误');
			            exit;
					}
				}
				
				
				if($album_no==2){
					//这里还分7这种情况
					if($userinfo['head1egg']=="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=260; //小孩坐标，以左上角为基点
						$dst_y_3=600; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=385; //母亲坐标，以左上角为基点
						$dst_y_2=465; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=385; //母亲坐标，以左上角为基点
						$dst_y_2=465; //母亲坐标，以左上角为基点
						$dst_x_3=280; //小孩坐标，以左上角为基点
						$dst_y_3=580; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']=="" ){
						$dst_x_1=125; //父亲坐标，以左上角为基点
						$dst_y_1=445; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=115; //父亲坐标，以左上角为基点
						$dst_y_1=445; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=290; //小孩坐标，以左上角为基点
						$dst_y_3=580; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=130; //父亲坐标，以左上角为基点
						$dst_y_1=455; //父亲坐标，以左上角为基点
						$dst_x_2=380; //母亲坐标，以左上角为基点
						$dst_y_2=465; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=135; //父亲坐标，以左上角为基点
						$dst_y_1=455; //父亲坐标，以左上角为基点
						$dst_x_2=395; //母亲坐标，以左上角为基点
						$dst_y_2=465; //母亲坐标，以左上角为基点
						$dst_x_3=280; //小孩坐标，以左上角为基点
						$dst_y_3=570; //小孩坐标，以左上角为基点
					}
					else{
						$this->jsonData(1,'参数错误');
			            exit;
					}
				}
				
				
				if($album_no==3){
					//这里还分7这种情况
					if($userinfo['head1egg']=="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=250; //小孩坐标，以左上角为基点
						$dst_y_3=505; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=355; //母亲坐标，以左上角为基点
						$dst_y_2=420; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=365; //母亲坐标，以左上角为基点
						$dst_y_2=425; //母亲坐标，以左上角为基点
						$dst_x_3=250; //小孩坐标，以左上角为基点
						$dst_y_3=515; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']=="" ){
						$dst_x_1=100; //父亲坐标，以左上角为基点
						$dst_y_1=445; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=95; //父亲坐标，以左上角为基点
						$dst_y_1=435; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=250; //小孩坐标，以左上角为基点
						$dst_y_3=505; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=95; //父亲坐标，以左上角为基点
						$dst_y_1=455; //父亲坐标，以左上角为基点
						$dst_x_2=355; //母亲坐标，以左上角为基点
						$dst_y_2=425; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=85; //父亲坐标，以左上角为基点
						$dst_y_1=455; //父亲坐标，以左上角为基点
						$dst_x_2=395; //母亲坐标，以左上角为基点
						$dst_y_2=435; //母亲坐标，以左上角为基点
						$dst_x_3=250; //小孩坐标，以左上角为基点
						$dst_y_3=505; //小孩坐标，以左上角为基点
					}
					else{
						$this->jsonData(1,'参数错误');
			            exit;
					}
				}
				
				
				if($album_no==4){
					//这里还分7这种情况
					if($userinfo['head1egg']=="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=280; //小孩坐标，以左上角为基点
						$dst_y_3=515; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=385; //母亲坐标，以左上角为基点
						$dst_y_2=435; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=395; //母亲坐标，以左上角为基点
						$dst_y_2=435; //母亲坐标，以左上角为基点
						$dst_x_3=280; //小孩坐标，以左上角为基点
						$dst_y_3=525; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']=="" ){
						$dst_x_1=135; //父亲坐标，以左上角为基点
						$dst_y_1=445; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=135; //父亲坐标，以左上角为基点
						$dst_y_1=445; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=280; //小孩坐标，以左上角为基点
						$dst_y_3=515; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=135; //父亲坐标，以左上角为基点
						$dst_y_1=465; //父亲坐标，以左上角为基点
						$dst_x_2=395; //母亲坐标，以左上角为基点
						$dst_y_2=435; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=125; //父亲坐标，以左上角为基点
						$dst_y_1=455; //父亲坐标，以左上角为基点
						$dst_x_2=410; //母亲坐标，以左上角为基点
						$dst_y_2=435; //母亲坐标，以左上角为基点
						$dst_x_3=270; //小孩坐标，以左上角为基点
						$dst_y_3=525; //小孩坐标，以左上角为基点
					}
					else{
						$this->jsonData(1,'参数错误');
			            exit;
					}
				}
				
				
				
				if($album_no==5){
					//这里还分7这种情况
					if($userinfo['head1egg']=="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=320; //小孩坐标，以左上角为基点
						$dst_y_3=510; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=415; //母亲坐标，以左上角为基点
						$dst_y_2=510; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']=="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=""; //父亲坐标，以左上角为基点
						$dst_y_1=""; //父亲坐标，以左上角为基点
						$dst_x_2=380; //母亲坐标，以左上角为基点
						$dst_y_2=500; //母亲坐标，以左上角为基点
						$dst_x_3=315; //小孩坐标，以左上角为基点
						$dst_y_3=525; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']=="" ){
						$dst_x_1=170; //父亲坐标，以左上角为基点
						$dst_y_1=445; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']=="" && $userinfo['head3egg']!="" ){
						$dst_x_1=160; //父亲坐标，以左上角为基点
						$dst_y_1=455; //父亲坐标，以左上角为基点
						$dst_x_2=""; //母亲坐标，以左上角为基点
						$dst_y_2=""; //母亲坐标，以左上角为基点
						$dst_x_3=295; //小孩坐标，以左上角为基点
						$dst_y_3=535; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']=="" ){
						$dst_x_1=180; //父亲坐标，以左上角为基点
						$dst_y_1=455; //父亲坐标，以左上角为基点
						$dst_x_2=420; //母亲坐标，以左上角为基点
						$dst_y_2=500; //母亲坐标，以左上角为基点
						$dst_x_3=""; //小孩坐标，以左上角为基点
						$dst_y_3=""; //小孩坐标，以左上角为基点
					}
					elseif($userinfo['head1egg']!="" && $userinfo['head2egg']!="" && $userinfo['head3egg']!="" ){
						$dst_x_1=150; //父亲坐标，以左上角为基点
						$dst_y_1=445; //父亲坐标，以左上角为基点
						$dst_x_2=430; //母亲坐标，以左上角为基点
						$dst_y_2=500; //母亲坐标，以左上角为基点
						$dst_x_3=300; //小孩坐标，以左上角为基点
						$dst_y_3=525; //小孩坐标，以左上角为基点
					}
					else{
						$this->jsonData(1,'参数错误');
			            exit;
					}
				}
				
				
				$path=$game_id."_album_".$album_no."_".date('ymdHis')."_".rand(10,99).".png";
				
				$out_path=BASE_PIC_ALBUM_PATH.$path;
				
				$album_rst=$this->combine_pic_multi($background_path,$out_path,$org_path_1,$dst_x_1,$dst_y_1,$org_path_2,$dst_x_2,$dst_y_2,$org_path_3,$dst_x_3,$dst_y_3);
				
				if($album_rst==true){
			        $UserMod = M('game');
			        $sql=sprintf("UPDATE %s SET album".addslashes($album_no)."='".$path."' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
			        $result = $UserMod->execute($sql);
			        
			        $key_album='album'.addslashes($album_no);
			        $data[$key_album]=BASE_URL."/public/web_album/".$path;
			        
		        }
		        else{
		        	$this->jsonData(1,'生成相册失败');
		            exit;
		        }
		        
			}
			
			
			if(isset($data['album1'])){
				$data['game_id']=$game_id;
				$this->jsonData(0,'成功',$data);
				exit;
			}
			else{
				$data['game_id']=$game_id;
	            $this->jsonData(1,'失败',$data);
	            exit;
			}
        }
        else{
        	$data['game_id']=$game_id;
            $this->jsonData(1,'失败',$data);
            exit;
        }
		
		exit;
	 }
	 
	 
	 
	 //重新玩游戏，清理SESSION
	 public function clear_game(){
	 	if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            unset($_SESSION['game_id']);
        }
	 }
	
	
	
	
	//上传人物原始图(js已经提交到微信服务器，请求php去微信服务器获取) test测试  直接请求一个图片url获取并保存图片的实例
    public function uploadheadpic_weixin_test(){
		
		$openid='xxxx';
        $game_id=1;
        //http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=sw4IQ9_TEZZCwAL-DRVsLr0VHBgBOAhLROAdZ9i2GL2DkomMIC6GaMaL6kACdxxYJ_utk1wDCa_UhIzeaM6NNDeDUMmw_sn8GMQaH8VoLjg&media_id=BFjezF-HCz6YwCVYqHKOSb0dSbxHpEFrNINsQgK8YuGA7PZSuPAkeOAedbWoyrCE
		//http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=OezXcEiiBSKSxW0eoylIeOLdVqgl91CQfORI5kdcor6Hwxt5mhBZ0V8JygyC3mmvYNBWENs_7a2WqVzzspPdNzfWzorF9U0pQU02ktJhaebyYgUVV5bO1SHseIeAenJ2lmaGWiuclVOTeeNP1xIC1g&media_id=BFjezF-HCz6YwCVYqHKOSb0dSbxHpEFrNINsQgK8YuGA7PZSuPAkeOAedbWoyrCE
		
        $media_id='BFjezF-HCz6YwCVYqHKOSb0dSbxHpEFrNINsQgK8YuGA7PZSuPAkeOAedbWoyrCE';
        echo $media_id;echo "<br><br>";
        
		if(1==1){
			
			echo "now:".$_SESSION['WX_INFO']['access_token'];echo "<br><br>";
			
			//第三步：刷新access_token（如果需要）
			//$get_url='https://ruby-china-files.b0.upaiyun.com/user/large_avatar/871.jpg';
			//$get_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$_SESSION['WX_INFO']['access_token'].'&media_id='.$media_id.'';
			$get_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=sw4IQ9_TEZZCwAL-DRVsLr0VHBgBOAhLROAdZ9i2GL2DkomMIC6GaMaL6kACdxxYJ_utk1wDCa_UhIzeaM6NNDeDUMmw_sn8GMQaH8VoLjg&media_id=BFjezF-HCz6YwCVYqHKOSb0dSbxHpEFrNINsQgK8YuGA7PZSuPAkeOAedbWoyrCE';
			echo $get_url;echo "<br><br>";
			$get_return = file_get_contents($get_url);
			//$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
			
			var_dump($get_return);echo "<br><br>";
			
			$is_android=0;
        	
        	
			$path = $game_id."_head_".date('ymdHis')."_".rand(10,99).".png";
			$dest = BASE_UPLOAD_PATH.$path;
			
			echo $dest;echo "<br><br>";
			
			if(!empty($get_return)){
				
				//模仿复制 php://input 模式上传
				$f = fopen($dest,'w');
				fwrite($f,$get_return);
				fclose($f);
				
				
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
				
				
				$file_type=$this->get_file_type($dest);
				if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png"){
					@unlink($dest);
					$this->jsonData(1,'图片上传限定jpg、gif、png类型');
				    exit;
				}
				
				//if($debug==1){
				//	echo "get file type:";
				//	echo $file_type;
				//	echo "<br>";
				//}
				
				
				$file_z=filesize($dest);
				$f_size_limit_byte=BASE_FILE_SIZE_LIMIT*1024*1024;
				
				if ($file_z>$f_size_limit_byte){
					@unlink($dest);
					$this->jsonData(1,'图片上传限定'.BASE_FILE_SIZE_LIMIT.'M');
				    exit;
				}
				
				
				
				$UserMod = M('game');
		        $sql=sprintf("UPDATE %s SET headpic='".addslashes($path)."' , imagerotate='".addslashes($pic_imagerotate)."' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
		        echo $sql;echo "<br><br>";
		        
		        //if($debug==1){
				//	echo "sql:";
				//	echo $sql;
				//	echo "<br>";
				//}
				
		        
		        //if($debug==1){
				//	exit;
				//}
				
				exit;
				
		        
				$data['game_id']=$game_id;
				$data['pic_url']=BASE_URL."/public/web_pic/".$path;
		        $this->jsonData(0,'成功',$data);
	        }
	        
		}
		
		$this->jsonData(0,'失败');
        
    }
    
    
    
    //微信  媒体文件 上传 (不需要做，因为：就是因为安卓无法上传，所以才需要让js去实现这块技术)
    public function uploadheadpic_weixin_upload(){
    	
    	
    	if(isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
            $openid=$_SESSION['WX_INFO']['openid'];
        }
        else{
        	$this->jsonData(1,'请先授权微信认证');
            exit;
        }
        
        
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
    }
    
    
    
    //上传人物原始图(js已经提交到微信服务器，请求php去微信服务器获取)
    public function uploadheadpic_weixin(){
		
		
    	if(isset($_SESSION['WX_INFO']['openid']) && $_SESSION['WX_INFO']['openid']!=""){
            $openid=$_SESSION['WX_INFO']['openid'];
        }
        else{
        	$this->jsonData(1,'请先授权微信认证');
            exit;
        }
        
        
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
        
        if(isset($_REQUEST['media_id']) && $_REQUEST['media_id']!=""){
            $media_id=$_REQUEST['media_id'];
        }
        else{
            $media_id=0;
        }
        
        if(isset($_REQUEST['type']) && $_REQUEST['type']!=""){
            $type=$_REQUEST['type'];
        }
        else{
            $type=".png";
        }
        
        if(isset($_REQUEST['token']) && $_REQUEST['token']!=""){
            $token=$_REQUEST['token'];
        }
        else{
            $token="";
        }
        
        
        if(isset($_REQUEST['debug']) && $_REQUEST['debug']==1){
            $debug=$_REQUEST['debug'];
        }
        else{
            $debug=0;
        }
        
        
		if(isset($_SESSION['WX_INFO']['access_token']) && $_SESSION['WX_INFO']['access_token']!=""){
			
			//第三步：刷新access_token（如果需要）
			$get_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$media_id.'';
			$get_return = file_get_contents($get_url);
			//$get_return = (array)json_decode($get_return);
			//echo "<pre>";print_r($get_return);
			
        	
        	
			$path = $game_id."_head_".date('ymdHis')."_".rand(10,99).$type;
			$dest = BASE_UPLOAD_PATH.$path;
			
			if(!empty($get_return)){
				
				//$this->jsonData(1,'文件内容:'.$get_return);
				//exit;
				
				//模仿复制 php://input 模式上传
				$f = fopen($dest,'w');
				fwrite($f,$get_return);
				fclose($f);
				
				
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
				
				
				$file_type=$this->get_file_type($dest);
				if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png"){
					
					//$this->jsonData(1,'图片上传限定jpg、gif、png类型:'.$dest.'。文件内容:'.$get_return.'。接口url：'.$get_url);
				    //exit;
				    
					@unlink($dest);
					$this->jsonData(1,'图片上传限定jpg、gif、png类型');
				    exit;
				}
				
				//if($debug==1){
				//	echo "get file type:";
				//	echo $file_type;
				//	echo "<br>";
				//}
				
				
				$file_z=filesize($dest);
				$f_size_limit_byte=BASE_FILE_SIZE_LIMIT*1024*1024;
				
				if ($file_z>$f_size_limit_byte){
					
					@unlink($dest);
					$this->jsonData(1,'图片上传限定'.BASE_FILE_SIZE_LIMIT.'M');
				    exit;
				}
				
				
				
				$UserMod = M('game');
		        $sql=sprintf("UPDATE %s SET headpic='".addslashes($path)."' , imagerotate='".addslashes($pic_imagerotate)."' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
		        //if($debug==1){
				//	echo "sql:";
				//	echo $sql;
				//	echo "<br>";
				//}
				
		        
		        //if($debug==1){
				//	exit;
				//}
				
		        
				$data['game_id']=$game_id;
				$data['pic_url']=BASE_URL."/public/web_pic/".$path;
		        $this->jsonData(0,'成功',$data);
	        }
	        else{
	        	$this->jsonData(1,'图片获取失败');
	        }
		}
		
		$this->jsonData(0,'失败');
        
    }
    
    
    
    
    //上传头像图
    public function uploadheadpic(){
		
		
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
        
        if(isset($_REQUEST['is_android']) && $_REQUEST['is_android']==1){
            $is_android=$_REQUEST['is_android'];
        }
        else{
            $is_android=0;
        }
        
        
        
		$path = $game_id."_head_".date('ymdHis')."_".rand(10,99).".png";
		$dest = BASE_UPLOAD_PATH.$path;
		
		/*
		//base64模式接受上传数据
		$f = fopen($dest,'w');
		$img = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABkAAD/4QMraHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjMtYzAxMSA2Ni4xNDU2NjEsIDIwMTIvMDIvMDYtMTQ6NTY6MjcgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzYgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjdENEMwOUUwMzQ4MDExRTRCRTFEODY4NEM4NEQ2OEVFIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjdENEMwOUUxMzQ4MDExRTRCRTFEODY4NEM4NEQ2OEVFIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6N0Q0QzA5REUzNDgwMTFFNEJFMUQ4Njg0Qzg0RDY4RUUiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6N0Q0QzA5REYzNDgwMTFFNEJFMUQ4Njg0Qzg0RDY4RUUiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAABAQEBAQEBAQEBAQEBAQEBAQEB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';
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace('data:image/gif;base64,', '', $img);
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$datas = base64_decode($img);
		fwrite($f,$datas);
		fclose($f);
		*/
		
		
		/*
		用 $_FILES 接收，可解决微信摄像头问题。前端是用 FormData 技术提交过来的。
		*/
		
		
		//if(isset($_REQUEST['debug']) && $_REQUEST['debug']==1){
        //    $debug=$_REQUEST['debug'];
        //}
        //else{
        //    $debug=0;
        //}
        
        //if($debug==1){
	        
	        //echo "FILES:";echo "<br>";
	        //echo "<pre>";print_r($_FILES);echo "</pre>";
	        //echo "<br>";
	        
	        //echo "POST:";echo "<br>";
	        //echo "<pre>";print_r($_POST);echo "</pre>";
	        //echo "<br>";
	        
	        //exit;
        
        //}
        
		
		
		$pic_imagerotate='';
		
		if($_FILES){
			//上传方式：表单
			$input = key($_FILES);
			
			//if($debug==1){
			//	echo "input:";
			//	echo $input;
			//	echo "<br>";
			//}
			
			
			if(!move_uploaded_file($_FILES[$input]['tmp_name'],$dest)) {
				
				//if($debug==1){
				//	echo "uploaded error:";
				//	echo "<br>";
				//}
				
				$data['game_id']=$game_id;
		        $this->jsonData(1,'失败');
		        exit;
			}
			else{
				
				//if($debug==1){
				//	echo "uploaded success:";
				//	echo $dest;
				//	echo "<br>";
				//}
				
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
		
		//模拟测试数据
		//$path='1684_head_150717212541_48.png';
		//$dest='D:\www\nac/public/web_pic/1684_head_150717212541_48.png';
		
		
		
		$file_type=$this->get_file_type($dest);
		if ($file_type!="jpg" && $file_type!="gif" && $file_type!="png"){
			@unlink($dest);
			$this->jsonData(1,'图片上传限定jpg、gif、png类型');
		    exit;
		}
		
		//if($debug==1){
		//	echo "get file type:";
		//	echo $file_type;
		//	echo "<br>";
		//}
		
		
		$file_z=filesize($dest);
		$f_size_limit_byte=BASE_FILE_SIZE_LIMIT*1024*1024;
		
		if ($file_z>$f_size_limit_byte){
			@unlink($dest);
			$this->jsonData(1,'图片上传限定'.BASE_FILE_SIZE_LIMIT.'M');
		    exit;
		}
		
		
		
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET headpic='".addslashes($path)."' , imagerotate='".addslashes($pic_imagerotate)."' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
        //if($debug==1){
		//	echo "sql:";
		//	echo $sql;
		//	echo "<br>";
		//}
		
        
        
        //if($debug==1){
		//	exit;
		//}
		
        
		$data['game_id']=$game_id;
		$data['pic_url']=BASE_URL."/public/web_pic/".$path;
        $this->jsonData(0,'成功',$data);
        
    }
    
    
    
    
    
    //对人物图片做裁切
    public function resizeheadpic(){
		
		
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
        
		if(isset($_REQUEST['drag_w']) && isset($_REQUEST['drag_h']) && isset($_REQUEST['src_x']) && isset($_REQUEST['src_y']) && isset($_REQUEST['src_w']) && isset($_REQUEST['src_h']) ){
			$drag_w=$_REQUEST['drag_w'];
			$drag_h=$_REQUEST['drag_h'];
			$src_x=$_REQUEST['src_x'];
			$src_y=$_REQUEST['src_y'];
			$src_w=$_REQUEST['src_w'];
			$src_h=$_REQUEST['src_h'];
			$dst_w=isset($_REQUEST['dst_w'])?$_REQUEST['dst_w']:$src_w;
			$dst_h=isset($_REQUEST['dst_h'])?$_REQUEST['dst_h']:$src_h;
		}
		else{
			$this->jsonData(1,'参数错误');
            exit;
		}
		
		
        
        //判断当前角色的原始图是否已经上传
        $CityMod = M('game');
        $userinfo = $CityMod->field('id,headpic')->where(" id='".addslashes($game_id)."' " )->order('id desc')->limit('0,1')->select();
        //echo "<pre>";print_r($userinfo[0]);exit;
        if(isset($userinfo[0])){
        	$userinfo=$userinfo[0];
        	if(!empty($userinfo['headpic'])){
        		$org_path=BASE_UPLOAD_PATH.$userinfo['headpic'];
        	}
        	else{
	        	$this->jsonData(1,'请先上传原始图');
	            exit;
        	}
        	
        	$path=$game_id."_resize_".date('ymdHis')."_".rand(10,99).".png";
        	$out_path = BASE_PIC_RESIZE_PATH.$path;
        	//echo $out_path;exit;
        	
			$exif = exif_read_data($org_path);
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
            
            //测试：强制旋转xx角度
            //$pic_imagerotate='90';
            $resize_rst=$this->resize_pic($org_path,$out_path,$src_x,$src_y,$src_w,$src_h,$dst_w,$dst_h,$drag_w,$drag_h,$pic_imagerotate);
        	
        	sleep(1);
        	
        	if($resize_rst==true){
        		$UserMod = M('game');
		        $sql=sprintf("UPDATE %s SET headegg='".addslashes($path)."' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
				$data['game_id']=$game_id;
				$data['pic_url']=BASE_URL."/public/web_resize/".$path;
		        $this->jsonData(0,'成功',$data);
        	}
        	else{
        		$this->jsonData(1,'图片裁切失败');
            	exit;
        	}
        	
        }
        else{
        	$this->jsonData(1,'请先参与游戏');
            exit;
        }
        
    }
    
    
    //裁切图片(前端不做drag拉伸缩放)
    public function resize_pic_not_drag($org_path,$out_path,$src_x,$src_y,$src_w,$src_h,$dst_w="",$dst_h=""){
		
		//$out_path = 'D:/www/cuicuisha/gd_test/out.jpg';  //目标图片
		//$org_path ='D:/www/cuicuisha/gd_test/1.jpg'; //原始图片

		//$src_x=10; //原始图的x坐标，以左上角为基点（前端传入）
		//$src_y=10; //原始图的y坐标，以左上角为基点（前端传入）
		//$src_w=30; //原始图截取的width（前端传入）
		//$src_h=40; //原始图截取的height（前端传入）

		$dst_x=0; //目标图的x坐标，固定写0
		$dst_y=0; //目标图的y坐标，固定写0
		//$dst_w=30; //目标图重新缩放的width，同时也是目标图对象的width，如此值等于src_w，则不失真，否则一定是失真的。（前端传入）
		//$dst_h=40; //目标图重新缩放的height，同时也是目标图对象的width，如此值等于src_h，则不失真，否则一定是失真的。（前端传入）

		if(empty($dst_w)){
			$dst_w=$src_w;
		}
		
		if(empty($dst_h)){
			$dst_h=$src_h;
		}
		
		
		//裁切原始图片
		
		$img_org_info=getimagesize($org_path); 
		//echo "<pre>";print_r($img_org_info);exit;

		switch ($img_org_info[2]) { 
		    case 1: 
				//如果是GIF图片，直接缩略成背景色是白色的GIF图片
				//$rst_for_gif=ImageResize($org_path,$width,$height,$outputname);
				//if ($rst_for_gif=="success"){
				//return $rst_for_gif;
				//}
				//$bgcolor = ImageColorTransparent($image,$bgcolor) ;
				
				$image =imagecreatefromgif($org_path); 
				break; 
		    
		    case 2: 
			    $image =imagecreatefromjpeg($org_path); 
			    break; 
		    
		    case 3: 
			    $image =imagecreatefrompng($org_path); 
			    break; 
		    
		    default: 
			    return "请上传jpg、gif、或png格式的文件";
			    
		}
		
		
		$width=$dst_w;
		$height=$dst_h;
		$image_dst = imagecreatetruecolor($width, $height);  //创建一个新的目标图像
		imagealphablending($image_dst,false);  //这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
		imagesavealpha($image_dst,true);  //这里很重要,意思是不要丢了$thumb图像的透明色;
		
		$white=imagecolorallocate($image_dst,255,255,255); 
		$black=imagecolorallocate($image_dst,0,0,0); 
		$red=imagecolorallocate($image_dst,255,0,0); 
		imagefill($image_dst,0,0,$white); 
		
		
		//复制图像的部分区块并调整大小
		//参数含义：$dst_image , $src_image , $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h 以左上角为基点
		
		imagecopyresampled($image_dst, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		//imagejpeg($image_dst, $out_path, 100);
		imagepng($image_dst, $out_path);
		
		//echo 'resize success';
		return true;
    }
    
    
    //裁切图片(前端先drag拉伸缩放过)
    public function resize_pic($org_path,$out_path,$src_x,$src_y,$src_w,$src_h,$dst_w="",$dst_h="",$drag_w="",$drag_h="",$pic_imagerotate=""){
		
		//$out_path = 'D:/www/cuicuisha/gd_test/out.jpg';  //目标图片
		//$org_path ='D:/www/cuicuisha/gd_test/1.jpg'; //原始图片

		//$src_x=10; //原始图的x坐标，以左上角为基点（前端传入）,是滤镜放大/缩小后的x坐标
		//$src_y=10; //原始图的y坐标，以左上角为基点（前端传入）,是滤镜放大/缩小后的y坐标
		//$src_w=30; //原始图截取的width（前端传入）,是滤镜放大/缩小后截取选区的宽
		//$src_h=40; //原始图截取的height（前端传入）,是滤镜放大/缩小后的坐标截取选区的高

		$dst_x=0; //目标图的x坐标，固定写0
		$dst_y=0; //目标图的y坐标，固定写0
		//$dst_w=30; //目标图重新缩放的width，同时也是目标图对象的width。如不传，默认取src_w。
		//$dst_h=40; //目标图重新缩放的height，同时也是目标图对象的width。如不传，默认取src_y。

		if(empty($dst_w)){
			$dst_w=$src_w;
		}
		
		if(empty($dst_h)){
			$dst_h=$src_h;
		}
		
		
		//先按滤镜drag定的宽高缩放原始图
		$image_drag = imagecreatetruecolor($drag_w, $drag_h);  //创建一个新的目标图像
		imagealphablending($image_drag,false);  //这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
		imagesavealpha($image_drag,true);  //这里很重要,意思是不要丢了$thumb图像的透明色;
		
		$white=imagecolorallocate($image_drag,255,255,255); 
		$black=imagecolorallocate($image_drag,0,0,0); 
		$red=imagecolorallocate($image_drag,255,0,0); 
		imagefill($image_drag,0,0,$white); 
		
		
		
		
		
		//缩放到drag图里
		
		$img_org_info=getimagesize($org_path); 
		//echo "<pre>";print_r($img_org_info);exit;
		
		$org_width=$img_org_info[0];
		$org_height=$img_org_info[1];
		
		switch ($img_org_info[2]) { 
		    case 1: 
				//如果是GIF图片，直接缩略成背景色是白色的GIF图片
				//$rst_for_gif=ImageResize($org_path,$width,$height,$outputname);
				//if ($rst_for_gif=="success"){
				//return $rst_for_gif;
				//}
				//$bgcolor = ImageColorTransparent($image,$bgcolor) ;
				
				$image =imagecreatefromgif($org_path); 
				break; 
		    
		    case 2: 
		    	
			    $image =imagecreatefromjpeg($org_path); 
			    break; 
		    
		    case 3: 
		    	
			    $image =imagecreatefrompng($org_path); 
			    break; 
		    
		    default: 
			    return "请上传jpg、gif、或png格式的文件";
			    
		}
		
		//旋转角度
		/*
		$filename = 'D:/www/cuicuisha/public/web_resize/test.png';
		$degrees = 180;
		//header('Content-type: image/png');
		$source = imagecreatefrompng($filename);
		$rotate = imagerotate($source, $degrees, 0);
		//imagepng($rotate);
		imagepng($rotate, $out_path); 
		exit;
		*/
		
		
		//$pic_imagerotate=180;
		if($pic_imagerotate!=""){
			$org_path_rotate=$org_path."._temp_.png";
			$rotate = imagerotate($image, $pic_imagerotate, 0);
			imagepng($rotate, $org_path_rotate);
			//echo $org_path_rotate;exit;
			sleep(1);
			
			$org_path=$org_path_rotate;
			
			$img_org_info=getimagesize($org_path); 
			//echo "<pre>";print_r($img_org_info);exit;
			
			$org_width=$img_org_info[0];
			$org_height=$img_org_info[1];
			
			switch ($img_org_info[2]) { 
			    case 1: 
					//如果是GIF图片，直接缩略成背景色是白色的GIF图片
					//$rst_for_gif=ImageResize($org_path,$width,$height,$outputname);
					//if ($rst_for_gif=="success"){
					//return $rst_for_gif;
					//}
					//$bgcolor = ImageColorTransparent($image,$bgcolor) ;
					
					$image =imagecreatefromgif($org_path); 
					break; 
			    
			    case 2: 
			    	
				    $image =imagecreatefromjpeg($org_path); 
				    break; 
			    
			    case 3: 
			    	
				    $image =imagecreatefrompng($org_path); 
				    break; 
			    
			    default: 
				    return "请上传jpg、gif、或png格式的文件";
				    
			}
		}
		
		
		imagecopyresampled($image_drag, $image, $dst_x, $dst_y, 0, 0, $drag_w, $drag_h, $org_width, $org_height);
		
		
		
		//缩放到目标图里
		$image_dst = imagecreatetruecolor($dst_w, $dst_h);  //创建一个新的目标图像
		imagealphablending($image_dst,false);  //这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
		imagesavealpha($image_dst,true);  //这里很重要,意思是不要丢了$thumb图像的透明色;
		
		$white=imagecolorallocate($image_dst,255,255,255); 
		$black=imagecolorallocate($image_dst,0,0,0); 
		$red=imagecolorallocate($image_dst,255,0,0); 
		imagefill($image_dst,0,0,$white); 
		
		
		//复制图像的部分区块并调整大小
		//参数含义：$dst_image , $src_image , $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h 以左上角为基点
		
		imagecopyresampled($image_dst, $image_drag, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		
		imagejpeg($image_dst, $out_path, 100);
		//imagepng($image_dst, $out_path);
		
		//echo 'resize success';
		return true;
    }
    
    
    
    
    //上传头像特效图
    public function savefilter(){
		
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
        
		$path = $game_id."_filter_".date('ymdHis')."_".rand(10,99).".png";
		$dest = BASE_UPLOAD_PATH.$path;
		
		
		if($_FILES){
			//上传方式：表单
			$input = key($_FILES);
			if(!move_uploaded_file($_FILES[$input]['tmp_name'],$dest)) {
				$data['game_id']=$game_id;
		        $this->jsonData(1,'失败');
		        exit;
			}
		}
		elseif(isset($GLOBALS['HTTP_RAW_POST_DATA'])){ 
			//上传方式：原始POST
			$f = fopen($dest,'w');
			fwrite($f,$GLOBALS['HTTP_RAW_POST_DATA']);
			fclose($f);
		}
		else{ 
			//上传方式：客户端提交
			$f = fopen($dest,'w');
			fwrite($f,file_get_contents('php://input'));
			fclose($f);
		}
		
		
		$UserMod = M('game');
        $sql=sprintf("UPDATE %s SET filterpic='".addslashes($path)."' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);
        
		$data['game_id']=$game_id;
		$data['pic_url']=BASE_URL."/public/web_pic/".$path;
        $this->jsonData(0,'成功',$data);
        
    }
    
	
    
	
    //做已分享的标记
    public function fenxiang(){

        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
		
        $UserMod = M('game');
        $sql=sprintf("UPDATE %s SET isfenxiang='1' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);

        if($result==1){
            $data['game_id']=$game_id;
            $this->jsonData(0,'成功',$data);
            exit;
        }
        else{
            $data['game_id']=$game_id;
            $this->jsonData(1,'失败',$data);
            exit;
        }

    }
	
	
	//验证是否分享过
    public function isfenxiang(){

        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
		
		$CityMod = M('game');
        $fenxiang = $CityMod->field('id,isfenxiang')->where(" id='".addslashes($game_id)."' " )->order('id desc')->limit('0,1')->select();
        if(isset($fenxiang[0]['isfenxiang']) && $fenxiang[0]['isfenxiang']==1){
        	$data['game_id']=$game_id;
        	$data['isfenxiang']=$fenxiang[0]['isfenxiang'];
            $this->jsonData(0,'成功',$data);
            exit;
        }
        else{
        	$data['game_id']=$game_id;
        	$data['isfenxiang']=0;
        	$this->jsonData(1,'失败',$data);
            exit;
        }
    }
    
    
    
    //用户提交个人手机等联系信息
    public function setuserinfo(){
		
        if(isset($_SESSION['game_id']) && $_SESSION['game_id']>0){
            $game_id=$_SESSION['game_id'];
        }
        else{
            $this->jsonData(1,'请先参与游戏');
            exit;
        }
		
		$mobile=isset($_REQUEST['mobile'])?$_REQUEST['mobile']:"";
		
        $UserMod = M('game');
        $sql=sprintf("UPDATE %s SET mobile='".addslashes($mobile)."' where id='".addslashes($game_id)."' ", $UserMod->getTableName() );
        $result = $UserMod->execute($sql);

        if($result==1){
            $data['game_id']=$game_id;
            $this->jsonData(0,'成功',$data);
            exit;
        }
        else{
            $data['game_id']=$game_id;
            $this->jsonData(1,'失败',$data);
            exit;
        }

    }
	
	
    
    
    
	
	//获取某个公众号下所有的粉丝用户列表  http://www.sagaci.com.cn/home/getfanslist_test  测试
	public function getfanslist_test() {
		
		set_time_limit(0);
		
	    
		
		
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
        
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WX_APPID_LIANMESHA."&secret=".WX_APPSECRET_LIANMESHA."";
		$get_return = json_decode($this->httpGet($url),true);
		if( !isset($get_return['access_token']) ){
			//echo '获取access_token失败！';
			//exit;
			echo "b";exit;
			
			$return['success']='获取access_token失败！';
		    echo json_encode($return);
		    exit;
		}
		
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
			
			
			/*
			$v['openid']="";
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$get_return['access_token']."&openid=oEy59uFMXayYUQD5RfvBuZnKVHbg".$v['openid']."";
			//echo $url;exit;
			$user_info = json_decode($this->httpGet($url),true);
			echo "<pre>";print_r($user_info);exit;
			*/
			
			
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
		//echo "<pre>";print_r($fans_list);echo "</pre>";exit;
		
		
		
		if(isset($fans_list) && !empty($fans_list)){
			
			
			$UserMod = M('user');
            
            $sql=sprintf("UPDATE %s SET 
		        is_fans='0' ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $UserMod->execute($sql);
		    
		    
			foreach($fans_list as $k=>$v){
				
				
				sleep(3);
				
				//不同公众号对应相同微信用户的openid是不同的，所以无法拿openid去比对
				//拿订阅号的openid的用户的昵称，然后去比对昵称
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$get_return['access_token']."&openid=".$v."";
				//echo $url;echo "<br>";
				$user_info = json_decode($this->httpGet($url),true);
				
				if(isset($user_info['nickname']) && $user_info['nickname']!=""){
					
					$nickname=$user_info['nickname'];
					//echo $nickname;exit;
					//$nickname=1;
					
					$is_fans=1;
		        	
			        $sql=sprintf("UPDATE %s SET 
			         is_fans='".addslashes($is_fans)."' 
			        where nickname=".addslashes($nickname)." 
			        ", $UserMod->getTableName() );
			        //echo $sql;exit;
			        $UserMod->execute($sql);
				
				}
				
			}
		}
		
		echo "finish";
		exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;
		
		
		//echo "finish";
		//exit;
		$return['success']='success';
	    echo json_encode($return);
	    exit;
	    
	}
	
    
    
    
	//获取某个公众号下所有的粉丝用户列表  http://www.sagaci.com.cn/home/getfanslist_test  测试
	public function getfanslist_nickname() {
		
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
		
		
		
		$fanslistMod = M('fanslist');
		$andsql=" 1 ";
    	$fans_list = $fanslistMod->where(" 1 ".$andsql." ")->order('modify_time asc')->limit('0,10')->select();
    	
    	echo "<pre>";print_r($fans_list);echo "</pre>";exit;
    	
		    	
            $sql=" id > 0 ";
            $fanslistMod->where($sql)->delete();
            
		
		if(isset($fans_list) && !empty($fans_list)){
			
			
			$UserMod = M('user');
            
            $sql=sprintf("UPDATE %s SET 
		        is_fans='0' ", $UserMod->getTableName() );
	        //echo $sql;exit;
	        $UserMod->execute($sql);
		    
		    
			foreach($fans_list as $k=>$v){
				
				
				sleep(3);
				
				//不同公众号对应相同微信用户的openid是不同的，所以无法拿openid去比对
				//拿订阅号的openid的用户的昵称，然后去比对昵称
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$get_return['access_token']."&openid=".$v."";
				//echo $url;echo "<br>";
				$user_info = json_decode($this->httpGet($url),true);
				
				if(isset($user_info['nickname']) && $user_info['nickname']!=""){
					
					$nickname=$user_info['nickname'];
					//echo $nickname;exit;
					//$nickname=1;
					
					$is_fans=1;
		        	
			        $sql=sprintf("UPDATE %s SET 
			         is_fans='".addslashes($is_fans)."' 
			        where nickname=".addslashes($nickname)." 
			        ", $UserMod->getTableName() );
			        //echo $sql;exit;
			        $UserMod->execute($sql);
				
				}
				
			}
		}
		
		echo "finish";
		exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;exit;
		
		
		//echo "finish";
		//exit;
		$return['success']='success';
	    echo json_encode($return);
	    exit;
	    
	}
	
    
    

}
?>