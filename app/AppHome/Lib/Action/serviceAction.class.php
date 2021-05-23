<?php
class serviceAction extends TAction
{
	
	
	//赛事服务 > 成绩查询  http://cdmalasong.loc/service/result
	public function result(){
    	
        $this->display('result');
    }
	
	//赛事服务 > 成绩查询  提交
	public function result_sub(){
    	
    	//echo "<pre>";print_r($_POST);exit;
    	
    	
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
		
		
		if(isset($_REQUEST['realname']) && !empty($_REQUEST['realname'])){
			$realname=$_REQUEST['realname'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		//if(isset($_REQUEST['match_code']) && !empty($_REQUEST['match_code'])){
		//	$match_code=$_REQUEST['match_code'];
		//}
		//else{
		 //   $return['success']='请求失败';
	      //  echo json_encode($return);
	       // exit;
		//}
		
		
		//if(isset($_REQUEST['realname']) && !empty($_REQUEST['realname'])){
		//	$realname=$_REQUEST['realname'];
		//}
		//else{
		//    $return['success']='请求失败';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		/*
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($id_type) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($id_number) .'" ' ;
		$and_cond=$and_cond.' and match_code="' . addslashes($match_code) .'" ' ;
		//$and_cond=$and_cond.' and realname like "%' . addslashes($realname) .'%" ' ;
		//echo $and_cond;exit;
		$orderMod = M('result');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        
        
           	$this->assign('order_info', $order_info);
           	*/
           	
           	
		$div_pic='';
		$api_url='http://ems.irunner.mobi/picapi/getscores';
		//echo $api_url;exit;
		$api_para=array();
		//$api_para['sign_pic']=$sign_pic;
		$api_para['identity']=$id_number;
		$api_para['name']=$realname;
		$api_para['_api_token']='xIop-jK827dxy*1';
		$api_para['race_id']='878';    //878
		//echo $api_url;echo "<br>";
		//echo "<pre>";print_r($api_para);echo "</pre>";
		//exit;
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//var_dump($api_result);
		//echo "<pre>";print_r($api_result);exit;
		if(isset($api_result['code']) && $api_result['code']==0){
			//$return['success']='success';
            //echo json_encode($return);
            //exit;
            $order_info=isset($api_result['data'])?$api_result['data']:array();
              //$order_info=array(111);
		}
		else{
			//$return['success']=$api_result['message'];
			//$return['success']=!empty($api_result['message'])?:'请求失败，请稍后再试。';
		   // echo json_encode($return);
		  //  exit;
		    $order_info=array();
		}
		$this->assign('order_info', $order_info);
           	
           	/*
           	返回data里的字段：
           	[score_id] => 11
            [match_id] => 848
            [course_id] => 275
            [name] =>  卢志刚
            [sex] =>  男
            [age] => 中国
            [bib_no] => 11597
            [id_type] => 身份证
            [identity] => 131023198909061018
            [cell] => 18733697078
            [nationality] => 身份证
            [address] => 
            [gun_time] => 02:54:54
            [net_time] => 02:54:51
            [gun_rank] => 8
            [net_rank] => 8
            [sex_gun_rank] => 
            [sex_net_rank] => 
            [age_range] => 
            [age_gun_rank] => 
            [age_net_rank] => 
            [distance] => 
            [space] => 04:09
            [create_time] => 
            [起点StartPoint] => 
            [5km] => 
            [10km] => 
            [15km] => 
            [20km] => 
            [25km] => 
            [30km] => 
            [35km] => 
            [40km] => 
            [5公里配速] => 
            [10公里配速] => 
            [15公里配速] => 
            [20公里配速] => 
            [25公里配速] => 
            [30公里配速] => 
            [35公里配速] => 
            [40公里配速] => 
           	*/
           	
           	
      //  if(!empty($order_info)){
        //	$_SESSION['id_type']=$order_info['id_type'];
        //	$_SESSION['id_number']=$order_info['id_number'];
        //	$return['success']='success';
		//$return['order_id']=$order_info['id'];
		// echo json_encode($return);
	     //   exit;
        //}
        //else{
       // 	$return['success']='没有查询到结果 No result';
//		$return['order_id']=$order_info['id'];
//		    echo json_encode($return);
//	        exit;
  //      }
        
        
        $this->display('result_sub');
    }
	
	
	
	//赛事服务 > 成绩查询 -> 直接调接口显示证书，无需上传头像 http://cdmalasong.loc/service/result_download_pic/id_number/EP4256181
	public function result_download_pic(){
    	
    	
    	
		if(isset($_REQUEST['id_number']) && !empty($_REQUEST['id_number'])){
			$id_number=$_REQUEST['id_number'];
		}
		else{
		    $return['success']='请求失败';
		    echo $return['success'];exit;
	        echo json_encode($return);
	        exit;
		}
		$this->assign('id_number', $id_number);
		
		
		
		if(isset($_REQUEST['realname']) && !empty($_REQUEST['realname'])){
			$realname=$_REQUEST['realname'];
		}
		else{
		    $return['success']='请求失败';
		    echo $return['success'];exit;
	        echo json_encode($return);
	        exit;
		}
		$this->assign('realname', $realname);
		
		
		
    	   
    	   //直接调签名成品图片api接口做法：
    	   $identity=$id_number;
		//$api_url='http://ems.irunner.mobi/api/showpic?identity='.$identity;  //cdm
		$api_url='http://ems.irunner.mobi/picapi/showpic?_api_token=xIop-jK827dxy*1&race_id=878&type=personal&identity='.$identity.'&name='.$realname;    //taizhou
			//echo $api_url;exit;
		
		//直接引用接口方背景图做法：
		$pic_url=$api_url;
		$this->assign('pic_url', $pic_url);
		    
        $this->display('result_download_pic');
    }
	
	
	
	
	
	//赛事服务 > 照片查询  http://cdmalasong.loc/service/photo
	public function photo(){
    	
        $this->display('photo');
    }
	
	//赛事服务 > 照片查询  提交
	public function photo_sub(){
    	
    	//echo "<pre>";print_r($_POST);exit;
    	
    	
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
		
		
		if(isset($_REQUEST['match_code']) && !empty($_REQUEST['match_code'])){
			$match_code=$_REQUEST['match_code'];
		}
		else{
		    $return['success']='请求失败';
	        echo json_encode($return);
	        exit;
		}
		
		
		//if(isset($_REQUEST['realname']) && !empty($_REQUEST['realname'])){
		//	$realname=$_REQUEST['realname'];
		//}
		//else{
		//    $return['success']='请求失败';
	      //  echo json_encode($return);
	      //  exit;
		//}
		
		
		$and_cond='';
		$and_cond=$and_cond.' and status=1 ' ;
		$and_cond=$and_cond.' and id_type="' . addslashes($id_type) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($id_number) .'" ' ;
		$and_cond=$and_cond.' and match_code="' . addslashes($match_code) .'" ' ;
		//$and_cond=$and_cond.' and realname like "%' . addslashes($realname) .'%" ' ;
		//echo $and_cond;exit;
		$orderMod = M('result');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        
        
           	$this->assign('order_info', $order_info);
           	
           	
           	
           	//echo SERVICE_PHOTO_UPLOAD;exit;   //D:\www\cdmalasong/public/service_photo
		$final_path = SERVICE_PHOTO_UPLOAD.'/'.$order_info['match_code'];
		//echo $final_path;exit; 
		
		$path_dir=SERVICE_PHOTO_UPLOAD_URI.'/'.$order_info['match_code']; 
		//echo $path_dir;exit;    //public/service_photo
		$ar_files=array();
		$lists = new DirectoryIterator($final_path);
		foreach ($lists as  $k=>$fileinfo) {
		    if ($fileinfo->isFile()) {
		    	$file_info=array();
		        list($pic_width, $pic_height, $pic_type, $pic_attr)=getimagesize($final_path.$fileinfo->getFilename());
				$file_info['filename']=$fileinfo->getFilename();
		        $file_info['img_uri']=$path_dir.'/'.$fileinfo->getFilename();
		        $file_info['creatime']=strftime("%Y-%m-%d",$fileinfo->getCTime());
		        $file_info['pic_width']=$pic_width;
		        $file_info['pic_height']=$pic_height;
		        $file_info['size']=round(($fileinfo->getSize()/1024),2);
		        $file_info['pic_type']=end(explode('.', $fileinfo->getFilename()));
		        $file_info['img_url']=$final_src.$fileinfo->getFilename();
		        $ar_files[]=$file_info;
		    }
		}
		//echo "<pre>";print_r($ar_files);exit;
	$this->assign('ar_files', $ar_files);


      //  if(!empty($order_info)){
        //	$_SESSION['id_type']=$order_info['id_type'];
        //	$_SESSION['id_number']=$order_info['id_number'];
        //	$return['success']='success';
		//$return['order_id']=$order_info['id'];
		// echo json_encode($return);
	     //   exit;
        //}
        //else{
       // 	$return['success']='没有查询到结果 No result';
//		$return['order_id']=$order_info['id'];
//		    echo json_encode($return);
//	        exit;
  //      }
        
        
        $this->display('photo_sub');
    }
    
	
	//赛事服务 > 证书查询  http://cdmalasong.loc/service/cert
	public function cert(){
	    	
	        $this->display('cert');
	    }
	
	
	
	//赛事服务 > 证书查询  提交
	public function cert_sub(){
    	
    	//echo "<pre>";print_r($_POST);exit;
    	
    	
		//echo "<pre>";print_r($_REQUEST);exit;
		
		//if(isset($_REQUEST['id_type']) && !empty($_REQUEST['id_type'])){
		//	$id_type=$_REQUEST['id_type'];
		//}
		//else{
		//    $return['success']='请求失败';
	    //    echo json_encode($return);
	      //  exit;
		//}
		
		
		
		if(isset($_REQUEST['id_number']) && !empty($_REQUEST['id_number'])){
			$id_number=$_REQUEST['id_number'];
		}
		else{
		    $return['success']='请求失败';
		    echo $return['success'];exit;
	       // echo json_encode($return);
	        //exit;
		}
		
		
		//if(isset($_REQUEST['match_code']) && !empty($_REQUEST['match_code'])){
		//	$match_code=$_REQUEST['match_code'];
		//}
		//else{
		//    $return['success']='请求失败';
	    //    echo json_encode($return);
	    //    exit;
		//}
		
		
		if(isset($_REQUEST['realname']) && !empty($_REQUEST['realname'])){
			$realname=$_REQUEST['realname'];
		}
		else{
		    $return['success']='请求失败';
		    echo $return['success'];exit;
	     // echo json_encode($return);
	       // exit;
		}
		
		
		
		$and_cond='';
		//$and_cond=$and_cond.' and status=1 ' ;
		//$and_cond=$and_cond.' and id_type="' . addslashes($id_type) .'" ' ;
		$and_cond=$and_cond.' and realname="' . addslashes($realname) .'" ' ;
		$and_cond=$and_cond.' and id_number="' . addslashes($id_number) .'" ' ;
		//$and_cond=$and_cond.' and match_code="' . addslashes($match_code) .'" ' ;
		//$and_cond=$and_cond.' and realname like "%' . addslashes($realname) .'%" ' ;
		//echo $and_cond;exit;
		$orderMod = M('cert');
        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
        $order_info=empty($order_data)?array():$order_data[0];
        //echo "<pre>";print_r($order_info);exit;
        
        
           	$this->assign('order_info', $order_info);
           	
           	
      //  if(!empty($order_info)){
        //	$_SESSION['id_type']=$order_info['id_type'];
        //	$_SESSION['id_number']=$order_info['id_number'];
        //	$return['success']='success';
		//$return['order_id']=$order_info['id'];
		// echo json_encode($return);
	     //   exit;
        //}
        //else{
       // 	$return['success']='没有查询到结果 No result';
//		$return['order_id']=$order_info['id'];
//		    echo json_encode($return);
//	        exit;
  //      }
        
        
        $this->display('cert_sub');
    }
	
	
	
	//赛事服务 > 完赛视频下载  http://cdmalasong.loc/service/finish_match_video
	public function finish_match_video(){
	    	
	        $this->display('finish_match_video');
	    }
	
	
	
	
	//赛事服务 > 酒店套餐等预定  http://cdmalasong.loc/service/reserve
	public function reserve(){
	    	
	    	
		
        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }
        
        if(empty($id)){
        	$id=1;
        }
        $this->assign('id', $id);
        
        $NoticeMod = M('service');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
	        $this->display('reserve');
	    }
	    
	    
	    
	//纪念品  http://cdmalasong.loc/service/special
	public function special(){
	    	
	    	
		
        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }
        
        if(empty($id)){
        	$id=1;
        }
        
        $NoticeMod = M('special');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
	        $this->display('special');
	    }
	    
	    
	    


	//领物须知 http://cdmalasong.loc/service/ling_wu_xu_zhi
    public function ling_wu_xu_zhi(){
		$this->display('ling_wu_xu_zhi');
    }
    
    

    //加载签名页 提交 http://cdmalasong.loc/service/loadsign
    public function loadsign(){
    	$this->display('loadsign');
    }
    
    //保存签名 签名图保存到本地 提交 http://cdmalasong.loc/service/savesign?myword=说句心里话&filestring=照片文件二进制码之类的内容
    public function savesign_org(){
    	
    	//$_POST['filestring']='aaa';
    	//$this->game1end();
    	//$game_id=$this->game1start();
    	
    	$game_id=1;
    	$style=1;
    	$user_id=0;
    	
		//if(isset($_REQUEST['style']) && ($_REQUEST['style']==1 || $_REQUEST['style']==2 || $_REQUEST['style']==3) ){
		//	$style=$_REQUEST['style'];
		//}
		//else{
		//	$this->jsonData(1,'参数错误');
        //    exit;
		//}
		
		
		/*
        if(isset($_REQUEST['myword']) && $_REQUEST['myword']!=''){
            $myword=$_REQUEST['myword'];
        }
        else{
            $this->jsonData(1,'参数错误');
            exit;
        }
        
        */
		
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
		$dest = UPLOAD_SIGN_PATH.$path;
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
		
		//echo $_REQUEST['sign_pic'];exit;
		$_POST['filestring']=$_REQUEST['sign_pic'];
		
		
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
			
			/*
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
            */
			
			
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
		
		
		/*
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
		*/
		
		
		
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
		
		echo "finish";exit;
		
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

    //保存签名 签名图保存到客户api接口  提交 http://cdmalasong.loc/service/savesign?identity=31022119741219601X&sign_pic=照片文件二进制码之类的内容
    public function savesign(){
    	
		
        if(isset($_REQUEST['identity']) && !empty($_REQUEST['identity'])){
            $identity=$_REQUEST['identity'];
        }
        else{
            $return['success']='参数错误';
		    echo json_encode($return);
		    exit;
        }
        //$identity='31022119741219601X';
        
        if(isset($_REQUEST['sign_pic']) && !empty($_REQUEST['sign_pic'])){
            $sign_pic=$_REQUEST['sign_pic'];
        }
        else{
            $return['success']='参数错误';
		    echo json_encode($return);
		    exit;
        }
        
		
		//echo $_REQUEST['sign_pic'];exit;
		//$_POST['filestring']=$_REQUEST['sign_pic'];
		//$_POST['identity']='310105196901010001';
		$div_pic='';
		$api_url='http://ems.irunner.mobi/picapi/savesign';
		//echo $api_url;exit;
		$api_para=array();
		$api_para['sign_pic']=$sign_pic;
		$api_para['identity']=$identity;
		$api_para['_api_token']='xIop-jK827dxy*1';
		$api_para['race_id']='878';
		//echo $api_url;echo "<br>";
		//echo "<pre>";print_r($api_para);echo "</pre>";
		//exit;
		$api_result=$this->http_request_url_post($api_url,$api_para);
		//var_dump($api_result);
		//echo "<pre>";print_r($api_result);exit;
		if(isset($api_result['code']) && $api_result['code']==0){
			$return['success']='success';
            echo json_encode($return);
            exit;
		}
		else{
			$return['success']=$api_result['message'];
			//$return['success']=!empty($api_result['message'])?:'请求失败，请稍后再试。';
		    echo json_encode($return);
		    exit;
		}
		/*
		Array
		(
		    [code] => -4
		    [message] => 选手表中不存在此人
		)
		*/
		
		
		$return['success']='请求失败，请稍后再试。';
	    echo json_encode($return);
	    exit;
		//echo "finish";exit;
		
        
    }
    




    //显示签名 客户api接口  提交 http://cdmalasong.loc/service/showsign?identity=31022119741219601X
    public function showsign(){
    	
        
        if(isset($_REQUEST['identity']) && !empty($_REQUEST['identity'])){
            $identity=$_REQUEST['identity'];
        }
        else{
            $return['success']='参数错误';
		    echo json_encode($return);
		    exit;
        }
        //$identity='31022119741219601X';
        
		
		
		$api_url='http://ems.irunner.mobi/api/showpic?identity='.$identity;
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
		
		
		
    	$game_id=1;
    	$style=1;
    	$user_id=0;
    	
        $is_android=1;
        
		//$path = "game_".$game_id."_style_".$style."_time_".date('ymdHis')."_".rand(10,99).".png";
		$path = $identity."_time_".date('ymdHis').".png";
		//$path = $identity."_time_".date('ymdHis').".jpg";
		
		//$path='game_1_style_1_time_150716161543_1.png';
		$dest = UPLOAD_SIGN_PATH.$path;
		//echo $dest;exit;
		
		//echo $_REQUEST['sign_pic'];exit;
		//$_POST['filestring']=$api_result;
		
		$f = fopen($dest,'w');
		fwrite($f,file_get_contents($api_url));
		fclose($f);
		exit;
		
		/*
		$f = fopen($dest,'w');
		$img = $_POST['filestring'];
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace('data:image/gif;base64,', '', $img);
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$datas = base64_decode($img);
		fwrite($f,$datas);
		fclose($f);
		
		exit;
		
		if(isset($api_result['data']['runner_id']) && !empty($api_result['data']['runner_id'])){
			$runner_id=$api_result['data']['runner_id'];
			$div_pic='<img src="http://ems.irunner.mobi/quer12345678/race/client/regquery?runner_id='.$runner_id.'&inajax=1">';
		}
		*/
		/*
		Array
		(
		    [code] => -4
		    [message] => 选手表中不存在此人
		)
		*/
	}





}
?>