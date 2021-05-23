<?php
class homeAction extends TAction
{
	
	
	
	
	//欢迎页 或  建设中页
	public function welcome(){
		
        $this->display('welcome');
        
	}
	
	
	//入口    http://cdmalasong.loc/home/index
	public function index(){
		
		if(isset($_REQUEST['debug'])){
			$debug=$_REQUEST['debug'];
		}
		else{
			$debug='';
		}
		
		//echo $debug;
		//exit;
		
		
		//新闻公告
	        $CityMod = M('news');
	        $news_notice_list = $CityMod->where(" status=1 " )->order(' is_top desc, sort asc , id desc')->limit('0,6')->select();
	        
	        //if(isset($video_list) && !empty($video_list)){
	        	//foreach($video_list as $k=>$v){
	        		//if(!empty($v['content'])){
	        		//	$stars[$k]['content']=strip_tags($v['content']);
	        		//	$len_sub=120;
				//		$len_str=$len_sub*1.5;
				//		if (strlen((string)($stars[$k]['content']))>$len_str){
				//			$stars[$k]['content']=$this->utf_substr($stars[$k]['content'] , $len_sub)."...";
				//		}
	        		//}
	        	//}
	        //}
	        
	        $this->assign('news_notice_list', $news_notice_list);
	        //echo "<pre>";print_r($news_notice_list);exit;
	        
	        
	        
	        
		//精彩图集
	        $CityMod = M('picture');
	        $picture_list = $CityMod->where(" status=1 and class_id=13 " )->order(' sort asc , id desc')->limit('0,1000')->select();
	        
	        //if(isset($picture_list) && !empty($picture_list)){
	        	//foreach($picture_list as $k=>$v){
	        		//if(!empty($v['content'])){
	        		//	$stars[$k]['content']=strip_tags($v['content']);
	        		//	$len_sub=120;
				//		$len_str=$len_sub*1.5;
				//		if (strlen((string)($stars[$k]['content']))>$len_str){
				//			$stars[$k]['content']=$this->utf_substr($stars[$k]['content'] , $len_sub)."...";
				//		}
	        		//}
	        	//}
	        //}
	        
	        $this->assign('picture_list', $picture_list);
	        //echo "<pre>";print_r($picture_list);exit;
	        
	        
	        
	        
		//精彩视频
	        $CityMod = M('video');
	        $video_list = $CityMod->where(" status=1 and class_id=14 " )->order(' is_top desc, sort asc , id desc')->limit('0,1')->select();
	        
	        //if(isset($video_list) && !empty($video_list)){
	        	//foreach($video_list as $k=>$v){
	        		//if(!empty($v['content'])){
	        		//	$stars[$k]['content']=strip_tags($v['content']);
	        		//	$len_sub=120;
				//		$len_str=$len_sub*1.5;
				//		if (strlen((string)($stars[$k]['content']))>$len_str){
				//			$stars[$k]['content']=$this->utf_substr($stars[$k]['content'] , $len_sub)."...";
				//		}
	        		//}
	        	//}
	        //}
	        
	        $this->assign('video_list', $video_list);
	        //echo "<pre>";print_r($video_list);exit;
	        
	        
	        
	        //7个分类做法
	        /*
		//合作伙伴
	        $CityMod = M('media');
	        $media_list = $CityMod->where(" status=1  " )->order(' class_id asc, sort asc , id desc')->limit('0,1000')->select();
	        
	        //if(isset($media_list) && !empty($media_list)){
	        	//foreach($media_list as $k=>$v){
	        		//if(!empty($v['content'])){
	        		//	$stars[$k]['content']=strip_tags($v['content']);
	        		//	$len_sub=120;
				//		$len_str=$len_sub*1.5;
				//		if (strlen((string)($stars[$k]['content']))>$len_str){
				//			$stars[$k]['content']=$this->utf_substr($stars[$k]['content'] , $len_sub)."...";
				//		}
	        		//}
	        	//}
	        //}
	        
	        $this->assign('media_list', $media_list);
	        //echo "<pre>";print_r($media_list);exit;
	        
	        
	        
	        
		//顶级冠名赞助商
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =1";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_1', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
	    
		//顶级合作伙伴
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =2";
        $sqlOrder = "sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_2', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
	    
		//官方合作伙伴
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =3";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_3', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
	    
		//官方赞助商
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =4";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_4', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
		//官方供应商
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =5";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_5', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
		//官方支持商
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =6";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_6', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	        
	        
	        
	        
	        
	        
	        
		//合作媒体
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =7";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_7', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    */
	    
	    
	    
	    /*
	    //21个分类的做法：
	if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media_class', $sqlWhere, $sqlOrder, '10000', 'M');
	    $media_class_list=empty($rst['dataset'])?array():$rst['dataset'];
		//$this->assign('news_list_5', $news_list);
	    //echo "<pre>";print_r($media_class_list);exit;
	    
	    
	    if(!empty($media_class_list)){
	    	$media_class_list_tmp=array();
	    	foreach($media_class_list as $k_class=>$v_class){
	    		
	    		$media_class_list_tmp[$v_class['id']]=$v_class;
	    		
	    	}
	    	$media_class_list=$media_class_list_tmp;
	    }
	    //echo "<pre>";print_r($media_class_list);exit;
	    
	    if(!empty($media_class_list)){
	    	foreach($media_class_list as $k_class=>$v_class){
	    		
	    		$sqlWhere = "status =1";
	    		$sqlWhere .= " and class_id ='".addslashes($v_class['id'])."' ";
	    		$sqlOrder = " sort asc,id asc ";
	    		$pic_list=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
		     $pic_list=empty($pic_list['dataset'])?array():$pic_list['dataset'];
		    //echo "<pre>";print_r($pic_list);exit;
		    $media_class_list[$k_class]['pic_list']=$pic_list;
		    
	    	}
	    }
	    
	    //echo "<pre>";print_r($media_class_list);exit;
	    $this->assign('media_class_list', $media_class_list);
	    
	    */
	    
	    
        //特殊展位
        $CityMod = M('special');
        $index_info = $CityMod->where(" status=1 " )->limit('0,1')->select();
        $index_info = empty($index_info[0])?array():$index_info[0];
        //echo "<pre>";print_r($index_info);exit;
        $this->assign('index_info', $index_info);
        
        
        
        
        //首页合作伙伴 taizhou
        $media_all_list=array();
	if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media_class', $sqlWhere, $sqlOrder, '10000', 'M');
	    $media_class_list=empty($rst['dataset'])?array():$rst['dataset'];
		//$this->assign('news_list_5', $news_list);
	    //echo "<pre>";print_r($media_class_list);exit;
	    if(!empty($media_class_list)){
	    	foreach($media_class_list as $k_class=>$v_class){
	    		
	    		$sqlWhere = "status =1";
	    		$sqlWhere .= " and class_id ='".addslashes($v_class['id'])."' ";
	    		$sqlOrder = " sort asc,id asc ";
	    		$pic_list=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
		     $pic_list=empty($pic_list['dataset'])?array():$pic_list['dataset'];
		    //echo "<pre>";print_r($pic_list);exit;
		    $media_class_list[$k_class]['pic_list']=$pic_list;   //键值是0  1  2
		    
		    $media_all_list[$v_class['id']]=$v_class;    //键值是class_id
		    $media_all_list[$v_class['id']]['pic_list']=$pic_list;
		    
	    	}
	    }
	    
	    //echo "<pre>";print_r($media_class_list);exit;
	    $this->assign('media_class_list', $media_class_list);   //键值是0  1  2
	    //echo "<pre>";print_r($media_all_list);exit;
	    $this->assign('media_all_list', $media_all_list);   //键值是class_id
        
	    
	    $this->assign('curmenu', '0');
	        
		$this->display('index');
		
		
	}
	
	
	/*
	行为发生后触发 发消息 
	https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140549&token=&lang=zh_CN
	客服接口-发消息
	*/
	
	
	//短信demo_sms_paas   http://cdmalasong.loc/home/demo_sms_paas
	public function demo_sms_paas(){
		exit;
		
		header("Content-type:text/html; charset=UTF-8");
		
		require_once APP_PATH .'Lib/sms_paas/ChuanglanSmsHelper/ChuanglanSmsApi.php';
		$clapi  = new ChuanglanSmsApi();
		$code = mt_rand(100000,999999);
		$result = $clapi->sendSMS('13917759443', '【成都马拉松】您好，您的参赛号是'. $code);
		//var_dump($result);exit;   
		/*返回数据格式：string(78) "{"time":"20170703163604","msgId":"17070316360427283","errorMsg":"","code":"0"}"
		{
		    "time": "20170703163604",
		    "msgId": "17070316360427283",
		    "errorMsg": "",
		    "code": "0"
		}
		*/

		if(!is_null(json_decode($result))){
			$output=json_decode($result,true);
			if(isset($output['code'])  && $output['code']=='0'){
				echo '短信发送成功！' ;
			}
			else{
				echo "errorMsg：".$output['errorMsg'];
			}
		}
		else{
			echo "ERROR：".$result;
		}
        	
		exit;
	}
	
	
	
	//测试发邮件  http://cdmalasong.loc/home/sendemailtest
	public function sendemailtest(){
		
		exit;
		
		$to='plusxu@qq.com';
		$name='plusxu';
		$subject='中文邮件test';
		$body='abc';
		$rst=$this->think_send_mail($to, $name, $subject, $body);
		var_dump($rst);exit;
	}
	
	
	
	//合作伙伴    http://cdmalasong.loc/home/partner
	public function partner(){
		
		/*
		//编辑器做法
        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }
        
        if(empty($id)){
        	$id=1;
        }
        
        $NoticeMod = M('partner');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        */
        
        
        
        
         /*
	    //7个分类的做法：
        
		//顶级冠名赞助商
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =1";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_1', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
	    
		//顶级合作伙伴
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =2";
        $sqlOrder = "sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_2', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
	    
		//官方合作伙伴
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =3";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_3', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
	    
		//官方赞助商
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =4";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_4', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
		//官方供应商
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =5";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_5', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
		//官方支持商
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =6";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_6', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    
	    
	    
		//合作媒体
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlWhere .= " and class_id =7";
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
	    $news_list=empty($rst['dataset'])?array():$rst['dataset'];
		$this->assign('news_list_7', $news_list);
	    //echo "<pre>";print_r($news_list);exit;
	    */
	    
	    
	    
	    
	    
	    //21个分类的做法：
	    
	if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }
        
        $sqlOrder = " sort asc,id asc ";
		//echo $sqlWhere;exit;
	    $rst=$this->GeneralActionForListing('media_class', $sqlWhere, $sqlOrder, '10000', 'M');
	    $media_class_list=empty($rst['dataset'])?array():$rst['dataset'];
		//$this->assign('news_list_5', $news_list);
	    //echo "<pre>";print_r($media_class_list);exit;
	    if(!empty($media_class_list)){
	    	foreach($media_class_list as $k_class=>$v_class){
	    		
	    		$sqlWhere = "status =1";
	    		$sqlWhere .= " and class_id ='".addslashes($v_class['id'])."' ";
	    		$sqlOrder = " sort asc,id asc ";
	    		$pic_list=$this->GeneralActionForListing('media', $sqlWhere, $sqlOrder, '10000', 'M');
		     $pic_list=empty($pic_list['dataset'])?array():$pic_list['dataset'];
		    //echo "<pre>";print_r($pic_list);exit;
		    $media_class_list[$k_class]['pic_list']=$pic_list;
		    
	    	}
	    }
	    
	    //echo "<pre>";print_r($media_class_list);exit;
	    $this->assign('media_class_list', $media_class_list);
	    
	    
	    $this->assign('curmenu', '5');
	    
		$this->display('partner');
		
	}
	
	
	
	
	//搜索结果
	public function search($srh_content=''){
		
		if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
			//可以预览隐藏内容
		}
		
		
		$srh_content="";

		if(isset($_POST['srh_content_global'])){
		$srh_content_post = $_POST['srh_content_global'];
		}
		else{
		$srh_content_post = "";
		}
		
		


		if(isset($_GET['srh_content_global'])){
		$srh_content_get = $_GET['srh_content_global'];
		}
		else{
		$srh_content_get = "";
		}


		if ($srh_content_post!=""){
		$srh_content=$srh_content_post;
		}
		if ($srh_content_get!=""){
		$srh_content=$srh_content_get;
		}

		$srh_content_encode=urlencode($srh_content);


		$stars=array();
        $sqlWhere = " 1 ";
        $sqlWhere .= ' and (title like "%'.$srh_content.'%" or content like "%'.$srh_content.'%") ';
        $sqlOrder = " view_allow desc ";
        $this->ModManager = M('cn_cachesearch');
        $fields = $this->ModManager->getDbFields();
        $rst=$this->GeneralActionForListing('cn_cachesearch', $sqlWhere, $sqlOrder, '5', 'M');
        
        if(isset($rst['dataset']) && count($rst['dataset'])>0){
        	$stars=$rst['dataset'];
        	foreach($stars as $k=>$v){
        		if(!empty($v['content'])){
        			$stars[$k]['content']=strip_tags($v['content']);
        			$len_sub=120;
					$len_str=$len_sub*1.5;
					if (strlen((string)($stars[$k]['content']))>$len_str){
						$stars[$k]['content']=$this->utf_substr($stars[$k]['content'] , $len_sub)."...";
					}

        			//$stars[$k]['content']=$this->utf_substr($v['content'],130)."...";
        		}
        	}
        }
        
        //echo "<pre>";print_r($stars);exit;
        $this->assign('stars', $stars);
        //echo $srh_content;exit;
        $this->assign('srh_content', $srh_content);
        
        
        
        
		$this->assign('banpic', 'home_txt.png');
		
		$this->assign('curmenu', '0');
        $this->display('search');
        
	}
	
	
	
	
	
}
?>