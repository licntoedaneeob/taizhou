<?php
/**
 * 基础Action
 */
class TAction extends Action {
	
	
	public $api_prefix_base = 'http://www.paobuqu.com/v4';   //统一账号接口
	
	public $api_prefix_namespace = 'taizhou';    //用户命名空间，在同一个命名空间下的user_name是唯一的。
	
	
	//启用邮件通知，1启用，0禁用
      public $open_email_msg = 1;
      
	//启用短信通知，1启用，0禁用
      public $open_sms_msg = 1;
      
	public $api_port = 8080;   //58080测试环境，8080正式环境
	
	public $orderExpireTime = 1800;   //提交订单后，订单有效时间 30分钟 60*30
	
	public $signupExpireTime = 86400;   //提交个人信息后，订单有效时间 24小时 60*60*24 
	
	public $cookieExpireTime = 86400;   //cookie有效时间 24小时 60*60*24 
	
	//订单号order_id的随机数组
    public $orderNoRandomArr = array(3,5,8,1,9,7,0,4,2,6);

    //订单号order_id的随机数组位数
    public $orderNoRandomArrLen = 3;

    //订单号随机数总位数
    public $orderNoRandomLen = 6;
    
	
	
    /**
     * 初始化公用模块
     * @access private
     * @return void
     */
	private function initModule(){
	}

    /**
     * 初始化系统配置信息
     * @access private
     * @return void
     */
	private function initSetting(){
	}

    /**
     * Action 初始化
     * @access protected
     * @return void
     */
	function _initialize() {		
//		include ROOT_PATH.'/includes/lib_common.php';	

		$this->SiteHome = defined( 'CGIWWW_HOME' ) ? CGIWWW_HOME : "http://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']==80?'':':'.$_SERVER['SERVER_PORT']).__ROOT__."/../";
		$this->assign('SiteHome',$this->SiteHome);

		$this->initModule();

		$this->initSetting();
		
		
		//$curr_page=$this->get_current_page_url();
		//echo $curr_page;exit;
		
		///登陆后Session
		
		
		//是否微信浏览器
		$is_wxBrowser=$this->is_wxBrowser();
		$this->assign('is_wxBrowser', $is_wxBrowser);
		
		
		//session永不过期 方法1：
		//ini_set('session.gc_maxlifetime', 3156000); //设置时间
		//$gc_maxlifetime=ini_get('session.gc_maxlifetime');//得到ini中设定值
		//echo $gc_maxlifetime;exit;
		
		//session永不过期 方法2：
		//$_SESSION['count']; // 注册Session变量Count  
		//isset($PHPSESSID)?session_id($PHPSESSID):$PHPSESSID = session_id();   // 如果设置了$PHPSESSID，就将SessionID赋值为$PHPSESSID，否则生成SessionID  
		//$_SESSION['count']++; // 变量count加1  
		//setcookie('PHPSESSID', $PHPSESSID, time()+3156000); // 储存SessionID到Cookie中  
		
		
		
        //分享到朋友圈
        //$share_info=$this->get_share_url_info();
        //echo "<pre>";print_r($share_info);exit;
        //$this->assign('share_info', $share_info);
        
        
        //$signPackage=$this->weixin_get_sign();
        //echo "<pre>";print_r($weixin_sign);exit;
        //$this->assign('signPackage', $signPackage);
        
        
        
        //是否手机客户端
        //$isMobiAgent=$this->isMobiAgent();
        //if($isMobiAgent){
        	//redirect(URL_MOBILE_SITE);
        //}
        
        
        /*
        //响应式跳转。pc版写：
        $user_agent=$this->user_agent();
        if($user_agent=='mobile'){
        	
        	//特殊页面定制跳转。 如手机里打开EDM的链接看到的是pc版，但需要跳转到手机版。
        	$current_url=$this->get_current_page_url();
        	$current_url=str_replace(BASE_URL,URL_MOBILE_SITE,$current_url);
        	redirect($current_url);
        	exit;
        	//if(stristr($current_url, '/project/country')){
        	//	$current_url=str_replace(BASE_URL,URL_MOBILE_SITE,$current_url);
        	//	redirect($current_url);
        	//}
        	
        	redirect(URL_MOBILE_SITE);
        }
        
        
        //响应式跳转。mobile版写：
        $user_agent=$this->user_agent();
        if($user_agent=='tablet' || $user_agent=='desktop'){
        	
        	//特殊页面定制跳转。 如手机里打开EDM的链接看到的是pc版，但需要跳转到手机版。
        	$current_url=$this->get_current_page_url();
        	$current_url=str_replace(BASE_URL,URL_PUBLIC,$current_url);
        	redirect($current_url);
        	exit;
        	//if(stristr($current_url, '/project/country')){
        	//	$current_url=str_replace(BASE_URL,URL_MOBILE_SITE,$current_url);
        	//	redirect($current_url);
        	//}
        	
        	redirect(URL_PUBLIC);
        }
        */
		
        
        
        //家庭
        //$CityMod_small = M('pc_ranch_class');
        //$ranch_class_list = $CityMod_small->where(" status=1 " )->order('sort asc , id asc')->select();
        //$this->assign('ranch_class_list', $ranch_class_list);
		//echo "<pre>";print_r($ranch_class_list);exit;
        
        
        
        
        $this_url=$this->get_current_page_url();
        //$this_uri=str_replace(BASE_URL,'',$this_url);
        $this_uri=str_replace('http://'.$_SERVER["HTTP_HOST"].__ROOT__ ,'',$this_url);
        //var_dump($this_uri);exit;
        //echo $this_uri;exit;
        $this->assign('this_uri', $this_uri);  //首页是/，内页如；/project/country_usa/id/101
        
        
        
        /*
        $curr_page=$this->get_current_page_url();
		//echo $curr_page;exit;
		if(stristr($curr_page, '/exhibition/')) {
			$this->assign('curmenu', '1');
		}
		if(stristr($curr_page, '/product/')) {
			$this->assign('curmenu', '2');
		}
		if(stristr($curr_page, '/exhibitors/')) {
			$this->assign('curmenu', '3');
		}
		if(stristr($curr_page, '/visitors/')) {
			$this->assign('curmenu', '4');
		}
		if(stristr($curr_page, '/event/')) {
			$this->assign('curmenu', '5');
		}
		if(stristr($curr_page, '/press/')) {
			$this->assign('curmenu', '6');
		}
		if(stristr($curr_page, '/contact/')) {
			$this->assign('curmenu', '7');
		}
		if(stristr($curr_page, '/meeting/')) {
			$this->assign('curmenu', '8');
		}
        */
        
		//cdn时间版本号
		$this->assign('cdn_time', '201708130005'); 

		
		
		//首页Banner
	        $CityMod = M('index_banner');
	        $index_banner_list = $CityMod->where(" status=1 " )->order(' sort asc , id asc')->limit('0,1000')->select();
	       // echo "<pre>";print_r($index_banner_list);exit;
	    $this->assign('index_banner_list', $index_banner_list);
	        
	        
	        
	        
		//清查订单过期订单
	        $this->checkOrderIsExpire();
	        
	        
	        
		$this->assign('ShowPageHeader', true);
		$this->assign('ShowPageFooter', true);

		$this->assign('iframe',isset($_REQUEST['iframe']) ? $_REQUEST['iframe'] : '' );
		$def=array(
			'request'=>$_REQUEST
		);	
		$this->assign('def',json_encode($def));
	}

	
    /**
     * Action 失败页面重写
     * @access protected
     * @return void
     */
	protected function error($message, $url_forward='',$ms = 3, $dialog=false, $ajax=false, $returnjs = '')
	{
		$this->jumpUrl = $url_forward;
		$this->waitSecond = $ms;
		$this->assign('dialog',$dialog);
		$this->assign('returnjs',$returnjs);
		parent::error($message, $ajax);
	}
    /**
     * Action 成功页面重写
     * @access protected
     * @return void
     */
	protected function success($message, $url_forward='',$ms = 3, $dialog=false, $ajax=false, $returnjs = '')
	{
		$this->jumpUrl = $url_forward;
		$this->waitSecond = $ms;
		$this->assign('dialog',$dialog);
		$this->assign('returnjs',$returnjs);
		parent::success($message, $ajax);
	}

	/**
	 *--------------------------------------------------------------+
	 * GeneralAction: ForList 
	 *--------------------------------------------------------------+
	 */
	protected function GeneralActionForListing( $moduleName, $queryWhere = '', $queryOrder = '', $paginglimit = '', $moduleType = 'D' , $relation = false, $groupBy = '' )
	{
		import("ORG.Util.Page");

		if( $moduleType == 'D'){
			$module = D($moduleName);  ///有 module 文件的
		}else{
			$module = M($moduleName);  ///无 module 文件的
			$relation = false;
		}

		$qwhere = $queryWhere;
		$rescount = $module->where($qwhere)->count();
		
		$paginglimit = intval($paginglimit);
		
		$paginglimit = $paginglimit < 1 ?  '' : $paginglimit;
		
		$Page = new Page($rescount, $paginglimit);
		$Page->rollPage = $this->SettingPagingRoll;
		$Page->setConfig($this->SettingPagingConfig);
		if($relation){
			$dataset = $module->relation($relation)->where($qwhere)->group($groupBy)->order($queryOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
		}else{
			$dataset = $module->where($qwhere)->group($groupBy)->order($queryOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
		}
		$navg = $Page->show();

		$sequence_number = $Page->firstRow;
		foreach($dataset as $k => $val){
			$dataset[$k]['_sequence_number_'] = ++$sequence_number;
		}
		if( is_null($dataset) || $dataset  === false ){
			$dataset = array();
		}else{
			//$this->assign('_sql_', $module->getLastSql());
			trace($module->getLastSql(), 'TAction-GeneralActionForListing-SQL');
		}

		$this->assign('dataset', $dataset);// 赋值数据集
		$this->assign('page', $navg);// 赋值分页输出

		$rst['dataset']=$dataset;
		$rst['navg']=$navg;
		return $rst;
	}

    /**
     * 
     * @access protected
     * @return void
     */
    public function ModuleDelete($t, $m, $ids, $fldId = 'id',  $fldStatus = 'status'){
    	if( $t == 'D' ){
			$module = D($m);
		}else{
			$module = M($m);
		}
		if( is_null($ids) ){
			return false;
		}
		if (is_array($ids)) {
			$ids = implode(',', $ids);
		} else {
			$ids = intval($ids);
		}
		trace( $ids, 'module-delete-ids');
		$query = "UPDATE %s SET %s = 255 WHERE %s in (%s)";
		return $module->execute( sprintf($query, $module->getTableName(), $fldStatus, $fldId, $ids) );
    }

	
	public function setPagething($PageTitle = '', $PageMenu = array(), $ShowPageHeader = true, $ShowPageFooter = true){
		$this->assign('PageTitle', $PageTitle);
		$this->assign('PageMenu', $PageMenu);
		$this->assign('ShowPageHeader', $ShowPageHeader);
		$this->assign('ShowPageFooter', $ShowPageFooter);
	}
	
	public function REQUEST($name, $def = ''){
		$val = '';
		if( isset($_POST[$name])){
			$val = $_POST[$name];
		}else if( isset($_GET[$name])){
			$val = $_GET[$name];
		}
		if( is_string($val) && $val == '' ){
			return $def;
		}
		return $val;
	
	}
	public function fixSQL($str) {
		return addslashes($str);
	}	
	




    /**
     * 系统邮件发送函数
     * @param string $to    接收邮件者邮箱
     * @param string $name  接收邮件者名称
     * @param string $subject 邮件主题 
     * @param string $body    邮件内容
     * @param string $attachment 附件列表
     * @param string $ssl 代表 SMTPSecure 的属性。默认为0。如果是ssl写1，如果是tls写2。
     * @return boolean 
     * 调用方式：
     $this->think_send_mail($to, $name, $subject, $body);  //无验证
     $this->think_send_mail($to, $name, $subject, $body, 1);  //ssl验证
     $this->think_send_mail($to, $name, $subject, $body, 2);  //tls验证
     */
    function think_send_mail($to, $name, $subject = '', $body = '', $ssl=0,  $attachment = null){
        
        $config = C('THINK_EMAIL');
        
        vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
        $mail             = new PHPMailer(); //PHPMailer对象
        $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();  // 设定使用SMTP服务
        $mail->SMTPDebug  = 0;                     // 0 关闭SMTP调试功能
                                                   // 1 = errors and messages
                                                   // 2 = messages only
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        
        if($ssl==1){
        	$mail->SMTPSecure = 'ssl';                 // 使用安全协议
        }
        elseif($ssl==2){
        	$mail->SMTPSecure = 'tls';                 // 使用安全协议
        }
        else{
        }
        
        $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
        $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
        $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
        $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
        $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
        $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
        $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject    = $subject;
        
        $mail->MsgHTML($body);
        $mail->AddAddress($to, $name);
        if(is_array($attachment)){ // 添加附件
            foreach ($attachment as $file){
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        
        return $mail->Send() ? true : $mail->ErrorInfo;
    }



    ////检查并重设重复文件名
    function checkFileName($filefolder,$filename){
        $i = 1;
        while (file_exists($filefolder."/".$filename)){
            $fn = $filename;
            $dotpos = strrpos($fn,".");
            $mainfn = substr($fn,0,$dotpos);
            $exfn = substr($fn,$dotpos+1,strlen($fn));
            $leftpos = strrpos($mainfn,"[");
            $rightpos = strrpos($mainfn,"]");
            if ($leftpos === false && $rightpos === false){
                $filename = $mainfn."[".$i."]".".".$exfn;
            }else{
                $signnum = substr($mainfn,$leftpos,$rightpos-$leftpos+1);
                $simplenum = substr($signnum,1,strlen($signnum)-2);
                if (is_numeric($simplenum)){
                    $mainfn = str_replace($signnum,"",$mainfn);
                }
                $filename = $mainfn."[".$i."]".".".$exfn;
            }
            $i++;
        }
        return $filename;
    }
    //上传文件
    function uploadImg($photeDir,$temp_name,$file_name)
    {
        $imgPath = $photeDir . '/' . $file_name;
        $dPath = $imgPath;
        @move_uploaded_file($temp_name, $dPath);
    }



	public function get_current_page_url(){
		$current_page_url = 'http';
	    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]== "on") {
	        $current_page_url .= "s";
	    }
	     $current_page_url .= "://";
	     if ($_SERVER["SERVER_PORT"] != "80") {
	    $current_page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	    } else {
	        $current_page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	    }
	    return $current_page_url;
	}


    public function jsonData($error_code=0,$message='',$data=array()) {
        $data = array('error_code'=>$error_code,'message'=>$message,'data'=>$data);
        echo json_encode($data);
        exit;
    }

    //模拟GET请求
    public function http_request_url_get($url='',$para=array())
    {
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt ($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt ($ch, CURLOPT_POSTFIELDS,$para);
        curl_setopt ($ch, CURLOPT_VERBOSE, 0);
        $result = curl_exec($ch);

        if(!$result){
            return '请求失败';
        }
        $result = $this->decode($result);
        return $result;
    }

	//模拟POST请求
    public function http_request_url_post($url='',$para=array())
    {
    	$this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");  //调用第三方短信发送接口，遇到中文乱码的问题，可能可以启用这句，或许可以解决。
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_HEADER, false);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt ($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 4);
        //curl_setopt ($ch, CURLOPT_HTTPHEADER, $this_header);  //调用第三方短信发送接口，遇到中文乱码的问题，可能可以启用这句，或许可以解决。
        curl_setopt ($ch, CURLOPT_POSTFIELDS,http_build_query($para));
        curl_setopt ($ch, CURLOPT_VERBOSE, 0);
        $result = curl_exec($ch);

        if(!$result){
            return '请求失败';
        }
        $result = $this->decode($result);
        return $result;
    }
    

    public function decode($data)
    {
        if(is_array($data))
        {
            foreach($data as $key=>$value)
            {
                $data[$key] = $this->decode($value);
            }
        }
        else
        {
            $data2 = json_decode($data,true);
            if($data2)
            {
                if(is_array($data2)) $data = $this->decode($data2);
            }
        }
        return $data;
    }

	//记录点击量
	public function rec_click_num($table_name,$field_name,$plus_click_num,$id)
    {
    	$UserMod = M($table_name);
        $sql=sprintf("UPDATE %s SET ".$field_name."=".$field_name."+".$plus_click_num." 
        where id='".$id."' ", $UserMod->getTableName() );
        //echo $sql;exit;
        $result = $UserMod->execute($sql);
    }
    
    
    //取页面SEO信息
    public function get_pageseo($uri){
		$module = M('pageseo');
        $result = $module->field('id,title,seo_title,seo_keyword,seo_desc')->where(" title='".addslashes($uri) ."' " )->select();
        $info=isset($result[0])?$result[0]:array();
        return $info;
    }

	//取客户端IP地址
	public function get_customer_ip(){

	  if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	           $ip = getenv("HTTP_CLIENT_IP");
	       else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	           $ip = getenv("HTTP_X_FORWARDED_FOR");
	       else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	           $ip = getenv("REMOTE_ADDR");
	       else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	           $ip = $_SERVER['REMOTE_ADDR'];
	       else
	           $ip = "unknown";
	       
	$user_IP=$ip;

	return $user_IP;
	}
	
	
	//获得客户端IP的方法、来自基础组。
	function get_cliIP() {
	    $cliIP = null;
	    $discern = isset($_SERVER['HTTP_FROM']) ? $_SERVER['HTTP_FROM'] : '';
	    switch ($discern) {
	        case 'ChinaMobile'://移动
	            $cliIP = $_SERVER['HTTP_REALIP'];
	            break;
	        case 'edu'://教育
	        case 'ChinaTelecom'://电信
	            $cliIP = $_SERVER['HTTP_X_REAL_IP'];
	            break;
	        default://网通
	            $cliIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
	            break;
	    }
	    return $cliIP? : $_SERVER['REMOTE_ADDR'];
	}



	//根据客户端IP地址，获取百度api接口当前城市
	public function get_current_city(){
		$api_data=array();
		$api_data['api_province']='';
		$api_data['api_city']='';
		$ip_address=$this->get_customer_ip();
		if($ip_address=="127.0.0.1"){
			$ip_address='114.60.95.180'; //上海市 电信
		}
		//$ip_address='59.44.36.0'; //辽宁省 沈阳市
		$api_url='http://api.map.baidu.com/location/ip?ak=2dc5955b8b84e0b2452054438c95e54f&ip='.$ip_address.'&coor=bd09ll';
		$baidu_rst=file_get_contents($api_url);
		if(!empty($baidu_rst)){
			//echo "<pre>";print_r($baidu_rst);exit;
			$baidu_rst=json_decode($baidu_rst,true);
			if(isset($baidu_rst['content']['address_detail']['city'])){
				$api_city=$baidu_rst['content']['address_detail']['city'];
				$api_data['api_city']=$api_city;
			}
			if(isset($baidu_rst['content']['address_detail']['province'])){
				$api_province=$baidu_rst['content']['address_detail']['province'];
				$api_data['api_province']=$api_province;
			}
		}
		return $api_data;
	}
	
	
	//计算字符串长度，中文占2个字符，英文占1个字符。如“学习”，返回4。如"abc"，返回3。
    public function getStrLen($str){
        $ccLen=(mb_strlen($str, 'utf8') + strlen($str))/2;
        return $ccLen;
    }
    
	//计算字符串长度，中文占1个字符，英文占1个字符。如“学习”，返回2。如"abc"，返回3。
    public function getStrLenSit($str){
    	return mb_strlen($str, 'utf8');
    }
    
	
	//php utf-8 字符串截取 -- 测试下来好象有问题 -- 遇到中文全角引号，无法截取字符，比如 “2008上海国际设计周” 里的引号无法截取。算是一个稳定版本，按字符截取。汉字算2个字符。
	public function utf_substr($str,$len){
		for ($i=0;$i<$len;$i++){
		$temp_str=substr($str,0,1);
		if(ord($temp_str) > 127){
		$i++;
		if($i<$len){
		$new_str[]=substr($str,0,3);
		$str=substr($str,3);
		}
		}
		else {
		$new_str[]=substr($str,0,1);
		$str=substr($str,1);
		}
		}
		return join($new_str);
	}
	
	
	
	//来自公司封装的方法
	
	/**
	 * 取文本摘要
	 * @param unknown $str
	 * @param unknown $len
	 * @param string $suffix
	 * @return string
	 */
	static public function getSummary($str,$len,$suffix='...')
	{
		if(mb_strlen($str)>$len)
		{
			$str = mb_substr($str,0,$len-mb_strlen($suffix)).$suffix;
		}

		return $str;
	}

	/**
	 * 取得文本摘要(按宽度)
	 * @param str $txt			原文本
	 * @param int $width		截取宽度
	 * @param real $zhCharWidth	中文字宽
	 * @param real $enCharWidth	英文字宽
	 * @return str
	 */
	static public function getSummaryByWidth($txt,$width,$zhCharWidth=1.2,$enCharWidth=0.7,$suffix='...')
	{	
		$data = '';
		$w = 0;
		$len = mb_strlen($txt,'UTF-8');
		for($i=0;$i<$len;$i++)
		{
			$char = mb_substr($txt,$i,1,'UTF-8');
			if(strlen($char)==1) $w += $enCharWidth;
			else $w += $zhCharWidth;
			$data .= $char;

			if(($w+strlen($suffix))>=$width)
			{
				if($i<($len-1))
				{
					$data .= $suffix;
					break;					
				}
			}
		}

		return $data;
	}

	/**
	 * 取得文本长度
	 * @param str $txt			文本
	 * @param int $zhCharLen	中文字长
	 * @param int $enCharLen	英文字长
	 * @return int $len			文本长
	 */
	static public function getTxtLen($txt,$zhCharLen=2,$enCharLen=1)
	{
		$len = strlen($txt);
		$mbLen = mb_strlen($txt,'UTF-8');
		$zhLen = ($len - $mbLen)/2;
		$enLen = $mbLen - $zhLen;
		$len = $zhLen * $zhCharLen + $enLen * $enCharLen;
		return $len;
	}
	
	//来自公司封装的方法


	
	/**
	 * 截取UTF-8编码下字符串的函数(来自复制ecshop方法库) 按字截取，不论是汉字还是字母，都算1个字。
	 *
	 * @param   string      $str        被截取的字符串
	 * @param   int         $length     截取的长度
	 * @param   bool        $append     是否附加省略号
	 *
	 * @return  string
	 */
	public function sub_str_ecshop($str, $length = 0, $append = true)
	{
	    $str = trim($str);
	    $strlength = strlen($str);

	    if ($length == 0 || $length >= $strlength)
	    {
	        return $str;
	    }
	    elseif ($length < 0)
	    {
	        $length = $strlength + $length;
	        if ($length < 0)
	        {
	            $length = $strlength;
	        }
	    }

	    if (function_exists('mb_substr'))
	    {
	        $newstr = mb_substr($str, 0, $length, EC_CHARSET);
	    }
	    elseif (function_exists('iconv_substr'))
	    {
	        $newstr = iconv_substr($str, 0, $length, EC_CHARSET);
	    }
	    else
	    {
	        //$newstr = trim_right(substr($str, 0, $length));
	        $newstr = substr($str, 0, $length);
	    }

	    if ($append && $str != $newstr)
	    {
	        $newstr .= '...';
	    }

	    return $newstr;
	}
	
	
	
	
	//获取文件真正的类型，比如一个a.php，重命名为a.php.jpg，则通过这个函数，可以知道这个文件的真实类型是不是jpg而是php
	public function get_file_type($filename){
		$file = file_get_contents($filename);
		//var_dump($file);
		$bin = substr($file,0,2);
		$strInfo = @unpack("C2chars", $bin);
		//echo "<pre>";print_r($strInfo);exit;
		//echo strlen($file);
		$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
		//var_dump($typeCode);exit;
		
		$fileType = '';
		switch ($typeCode)
		{
		case 7790:
		$fileType = 'exe';
		break;
		case 7784:
		$fileType = 'midi';
		break;
		case 8297:
		$fileType = 'rar';
		break;
		case 255216:
		$fileType = 'jpg';
		break;
		case 7173:
		$fileType = 'gif';
		break;
		case 6677:
		$fileType = 'bmp';
		break;
		case 13780:
		$fileType = 'png';
		break;
		
		case 8075:
		$fileType = 'xlsx';
		break;
		case 8075:
		$fileType = 'zip';
		break;
		
		case 55122:
		$fileType = '7z';
		break;
		
		case 3780:
		$fileType = 'pdf';
		break;
		
		case 208207:
		$fileType = 'xls';
		break;
		
		
		
		default:
		$fileType = 'unknown';
		//echo 'unknown';
		}
		//echo $fileType;exit;
		return $fileType;
		//echo 'this is a(an) '.$fileType.' file:' . $typeCode;
	}




	public function isMobiAgent()
	{
		if(isset($_COOKIE['USE_PC_AGENT']))
		{
			return false;
		}

		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			if(preg_match('/(iphone|ipad|iOS|Android|RIM|SymbianOS|NOKIA)/i',$_SERVER['HTTP_USER_AGENT'])) return true;
		}

		return false;
	}
	
	
	public function isAndroid()
	{

		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			if(preg_match('/Android/i',$_SERVER['HTTP_USER_AGENT'])) return true;
		}

		return false;
	}
	
	
	//判断终端设备是否为手机
	public function get_client_os(){
    	$cli_os="web";
    	if ($this->isMobiAgent()) {
			if ($this->isAndroid()) {
				$cli_os = 'Android';
			}
			else {
				$cli_os = 'iOS';
			}
		}
		else{
			$cli_os="web";
		}
		return $cli_os;
    }
    
    
    
    //过滤xss  <script>alert(1);</script>
    public function remove_xss($val) {
	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	   // this prevents some character re-spacing such as <java\0script>
	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
	   // straight replacements, the user should never need these since they're normal characters
	   // this prevents like <IMG SRC=@avascript:alert('XSS')>
	   $search = 'abcdefghijklmnopqrstuvwxyz';
	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   $search .= '1234567890!@#$%^&*()';
	   $search .= '~`";:?+/={}[]-_|\'\\';
	   for ($i = 0; $i < strlen($search); $i++) {
	      // ;? matches the ;, which is optional
	      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
	      // @ @ search for the hex values
	      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
	      // @ @ 0{0,7} matches '0' zero to seven times
	      $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	   }
	   // now the only remaining whitespace attacks are \t, \n, and \r
	   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	   $ra = array_merge($ra1, $ra2);
	   $found = true; // keep replacing as long as the previous round replaced something
	   while ($found == true) {
	      $val_before = $val;
	      for ($i = 0; $i < sizeof($ra); $i++) {
	         $pattern = '/';
	         for ($j = 0; $j < strlen($ra[$i]); $j++) {
	            if ($j > 0) {
	               $pattern .= '(';
	               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
	               $pattern .= '|';
	               $pattern .= '|(�{0,8}([9|10|13]);)';
	               $pattern .= ')*';
	            }
	            $pattern .= $ra[$i][$j];
	         }
	         $pattern .= '/i';
	         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
	         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
	         if ($val_before == $val) {
	            // no replacements were made, so exit the loop
	            $found = false;
	         }
	      }
	   }
	   return $val;
	}


    //判断终端设备是否为pad、手机、PC （方法一）
    public function user_agent($ua = NULL){
        //return 'mobile';  //强制显示手机版
        if($ua==NULL) $ua = $_SERVER['HTTP_USER_AGENT'];
		
        $iphone = strstr(strtolower($ua), 'mobile'); //Search for 'mobile' in user-agent (iPhone have that)
        $android = strstr(strtolower($ua), 'android'); //Search for 'android' in user-agent
        $windowsPhone = strstr(strtolower($ua), 'phone'); //Search for 'phone' in user-agent (Windows Phone uses that)

        $androidTablet = $this->androidTablet($ua); //Do androidTablet function
        $ipad = strstr(strtolower($ua), 'ipad'); //Search for iPad in user-agent

        if($androidTablet || $ipad){ //If it's a tablet (iPad / Android)
            return 'tablet';
        }
        elseif($iphone && !$ipad || $android && !$androidTablet || $windowsPhone){ //If it's a phone and NOT a tablet
            return 'mobile';
        }
        else{ //If it's not a mobile device
            return 'desktop';
        }
    }
    
    public function androidTablet($ua){ //Find out if it is a tablet
        if(strstr(strtolower($ua), 'android') ){//Search for android in user-agent
            if(!strstr(strtolower($ua), 'mobile')){ //If there is no ''mobile' in user-agent (Android have that on their phones, but not tablets)
                return true;
            }
        }
        return false;
    }
    
    //判断终端设备是否为pad、手机、PC （方法二）
    public function UserAgent(){
	    $user_agent = ( !isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];
	    //平板
		if (preg_match("/(pad)/i", strtolower($user_agent))){
			$a = 'tablet';
		}
		//手机
		elseif(trim($user_agent) == '' OR preg_match("/(iphone|ipod|android|nokia|sony|ericsson|mot|htc|samsung|sgh|lg|philips|lenovo|ucweb|opera mobi|windows mobile|blackberry|sharp|sie-|philips|panasonic|alcatel|meizu|netfront|symbian|windowsce|palm|operamini|operamobi|openwave|nexusone|cldc|midp|wap)/i", strtolower($user_agent))) {
			$a = 'mobile';
		}
		//PC
		else{
			$a = 'desktop';
		}
		return $a;
	}


    
    //生成随机英文或数字
    public function MakeRandStr($length) {
		//$possible = "0123456789"."abcdefghijklmnopqrstuvwxyz"."ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$possible = "123456789"."abcdefghijkmnpqrstuvwxyz"."ABCDEFGHIJKLMNPQRSTUVWXYZ"; 
		$str = ""; 
		while(strlen($str) < $length) 
		$str .= substr($possible, (rand() % strlen($possible)), 1); 
		return($str); 
	} 
	
	
	
	
	
	/**
	 * 生成二维码
	 * @param str $data ，可以是某个url链接，如 http://www.baidu.com
	 * @param int $size ，二维码图像大小
	 * @return str
	 * 调用实例：
	 	$tempRoot='D:/www/hongyue/public/';
		$tempBase='/public/';
		$qrUrl = 'http://www.baidu.com';
		$size=5;
		$rst=$this->createQRcode($tempRoot,$tempBase,$qrUrl,$size);
		echo $rst;exit;
	 */
	public function createQRcode($tempRoot, $tempBase, $data,$size=3,$return='url')
	{
		//$deploy['tempRoot']='D:/www/hongyue/public/';
		//$deploy['tempBase']='/public/';
		
		$deploy['tempRoot']=$tempRoot;
		$deploy['tempBase']=$tempBase;
		
        $md5 = md5($data.'|'.$size);
		$filename = 'QRC'.$md5.'.png';
        $md5 = hexdec(substr($md5,0,10));
        $md5 = str_pad($md5,12,'0');
        $folderList = array_slice(str_split($md5,3),0,3);
        $path = $deploy['tempRoot'].'qrc/';
        if(!is_dir($path)) mkdir($path);
        foreach($folderList as $folder)
        {
            $path .= $folder.'/';
            if(!is_dir($path)) mkdir($path);
        }

		$path = $deploy['tempRoot'].'qrc/'.implode('/',$folderList).'/'.$filename;
		$url = $deploy['tempBase'].'qrc/'.implode('/',$folderList).'/'.$filename;
		
		if(!is_file($path))
		{
			require_once APP_PATH .'Lib/QRcode/QRcode.php';   //类库文件位置：/app/AppHome/Lib/QRcode/QRcode.php
			QRcode::png($data,$path,QR_ECLEVEL_H,$size,1);
		}
		
		if($return=='url') return $url;
		elseif($return=='path') return $path;

	}
	
	
	/**
	*  @desc 根据两点间的经纬度计算距离
	*  @param float $lat 纬度值
	*  @param float $lng 经度值
	*/
	public function getDistance($lat1, $lng1, $lat2, $lng2)
	{
		$earthRadius = 6367000;   //地球平均半径 6371.004千米。 $earthRadius 单位为米。
		$lat1 = ($lat1 * pi() ) / 180;
		$lng1 = ($lng1 * pi() ) / 180;
		$lat2 = ($lat2 * pi() ) / 180;
		$lng2 = ($lng2 * pi() ) / 180;
		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		$calculate = $earthRadius * $stepTwo;
		return round($calculate,2);  //$calculate 单位为米。
	}
	
	
	
/////////////// 获取签名 ////////////////////
	
	
	public function weixin_get_sign(){
		
		//默认并建议：url参数不要传过来，php自动获取服务器的url即可。
		if(isset($_REQUEST['url']) && $_REQUEST['url']!=''){
            $get_url=$_REQUEST['url'];
        }
        else{
            $get_url='';
        }
        
		$signPackage = $this->getSignPackage($url);
		$data=$signPackage;
		//$this->jsonData(0,'成功',$data);
        //exit;
        return $data;
	}
	
	
	
	// 获取签名
	public function getSignPackage($get_url='') {
		$jsapiTicket = $this->getJsApiTicket();
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		if(!empty($get_url)){
			$url=$get_url;
		}
		
		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
			"appId"     => WX_APPID,
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage; 
	}
	// 创建随机字符串
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
	// 获取Ticket
	public function getJsApiTicket() {
		
		//2小时有效时间的版本，据说access_token每天只能获取2000次
		$CityMod = M('weixin_setting');
        $JsApiTicket_expire_time = $CityMod->field('value_s')->where(" key_s='JsApiTicket_expire_time' " )->select();
        $JsApiTicket_expire_time = $JsApiTicket_expire_time[0]['value_s'];
        
		$CityMod = M('weixin_setting');
        $JsApiTicket_jsapi_ticket = $CityMod->field('value_s')->where(" key_s='JsApiTicket_jsapi_ticket' " )->select();
        $JsApiTicket_jsapi_ticket = $JsApiTicket_jsapi_ticket[0]['value_s'];
        
		
		if ($JsApiTicket_expire_time < time()) {
			$accessToken = $this->getAccessToken();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$accessToken."";
			$res = json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$JsApiTicket_expire_time = time() + 3600;
				$JsApiTicket_jsapi_ticket = $ticket;
				
				$UserMod = M('weixin_setting');
		        $sql=sprintf("UPDATE %s SET value_s='".$JsApiTicket_expire_time."' where key_s='JsApiTicket_expire_time' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
				$UserMod = M('weixin_setting');
		        $sql=sprintf("UPDATE %s SET value_s='".$JsApiTicket_jsapi_ticket."' where key_s='JsApiTicket_jsapi_ticket' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
			}
		} 
		else {
			$ticket = $JsApiTicket_jsapi_ticket;
		}
		
		return $ticket;
		
	}
	// 获取AccessToken
	public function getAccessToken() {
		
		//2小时有效时间的版本，据说access_token每天只能获取2000次
		$CityMod = M('weixin_setting');
        $AccessToken_expire_time = $CityMod->field('value_s')->where(" key_s='AccessToken_expire_time' " )->select();
        $AccessToken_expire_time = $AccessToken_expire_time[0]['value_s'];
        
		$CityMod = M('weixin_setting');
        $AccessToken_access_token = $CityMod->field('value_s')->where(" key_s='AccessToken_access_token' " )->select();
        $AccessToken_access_token = $AccessToken_access_token[0]['value_s'];
        
        if ($AccessToken_expire_time < time()) {
			// 如果是企业号用以下URL获取access_token
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WX_APPID."&secret=".WX_APPSECRET."";
			$res = json_decode($this->httpGet($url));
			$access_token = $res->access_token;
			if ($access_token) {
				$AccessToken_expire_time = time() + 3600;
				$AccessToken_access_token = $access_token;
				
				
				$UserMod = M('weixin_setting');
		        $sql=sprintf("UPDATE %s SET value_s='".$AccessToken_expire_time."' where key_s='AccessToken_expire_time' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
				$UserMod = M('weixin_setting');
		        $sql=sprintf("UPDATE %s SET value_s='".$AccessToken_access_token."' where key_s='AccessToken_access_token' ", $UserMod->getTableName() );
		        $result = $UserMod->execute($sql);
		        
			}
		} 
		else {
			$access_token = $AccessToken_access_token;
		}
		
        return $access_token;
		
	}
	// Http请求
	public function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		$res = curl_exec($curl);
		curl_close($curl);
		return $res;
	}

	
	
	// 是否微信浏览器
	public function is_wxBrowser() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            $wxBrowser = 0;
        } else {
            $wxBrowser = 1;
        }
		return $wxBrowser;
	}
	


//验证日期
    function isdate($str,$format="Y-m-d"){
        $strArr = explode("-",$str);
        if(empty($strArr)){
            return false;
        }
        foreach($strArr as $val){
            if(strlen($val)<2){
                $val="0".$val;
            }
            $newArr[]=$val;
        }
        $str =implode("-",$newArr);
        $unixTime=strtotime($str);
        $checkDate= date($format,$unixTime);
        if($checkDate==$str)
            return true;
        else
            return false;
    }
    

//验证电话  自行车
	public function  isTel($tel)
	{
		$isMob="/^1[3-5,8]{1}[0-9]{9}$/";
		$isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
		if(!preg_match($isMob, $tel) && !preg_match($isTel, $tel)) return false;

		return true;
	}


//验证手机  自行车
	public function  isMobile($phone)
	{
		if(!preg_match("/1[34578]{1}\d{9}$/", $phone))
		{
			return false;
		}
		return true;
	}



//验证身份证号码  自行车
	public function checkIdCard($idcard)
	{
		// 只能是18位
		if(strlen($idcard)!=18){
			return false;
		}

		// 取出本体码
		$idcard_base = substr($idcard, 0, 17);

		// 取出校验码
		$verify_code = substr($idcard, 17, 1);

		// 加权因子
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

		// 校验码对应值
		$verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

		// 根据前17位计算校验码
		$total = 0;
		for($i=0; $i<17; $i++){
			$total += substr($idcard_base, $i, 1)*$factor[$i];
		}

		// 取模
		$mod = $total % 11;

		// 比较校验码
		if(strtoupper($verify_code) == $verify_code_list[$mod]) return true;
		else return false;
	}



	
	
	
	/**
	 * 功能：通过身份证获得生日
	 */
	function get_idcard_birth($cid)
	{
	    $cid = $this->getIDCard($cid);
		
	    if (!$this->isIdCard($cid)) return '';
	    
	    $year = (int)substr($cid, 6, 4);
	    $bir = substr($cid, 10, 4);
	    $month = (int)substr($bir, 0, 2);
	    $day = (int)substr($bir, 2);
	    
	    $bir_arr['y']=$year;
	    $bir_arr['m']=$month;
	    $bir_arr['d']=$day;
	    
	    $year = substr($cid, 6, 4);
	    $bir = substr($cid, 10, 4);
	    $month = substr($bir, 0, 2);
	    $day = substr($bir, 2);
	    
	    $bir_arr['yy']=$year;
	    $bir_arr['mm']=$month;
	    $bir_arr['dd']=$day;
	    
	    return $bir_arr;
		
	}
	
	
	
	
	/**
	 * 功能：通过身份证获得星座
	 */
	function get_xingzuo($cid)
	{
	    $cid = $this->getIDCard($cid);

	    if (!$this->isIdCard($cid)) return '';
	    $bir = substr($cid, 10, 4);
	    $month = (int)substr($bir, 0, 2);
	    $day = (int)substr($bir, 2);
	    $strValue = '';
	    if (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18))
	    {
	        $strValue = "水瓶座";
	    }
	    else if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20))
	    {
	        $strValue = "双鱼座";
	    }
	    else if (($month == 3 && $day > 20) || ($month == 4 && $day <= 19))
	    {
	        $strValue = "白羊座";
	    }
	    else if (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20))
	    {
	        $strValue = "金牛座";
	    }
	    else if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 21))
	    {
	        $strValue = "双子座";
	    }
	    else if (($month == 6 && $day > 21) || ($month == 7 && $day <= 22))
	    {
	        $strValue = "巨蟹座";
	    }
	    else if (($month == 7 && $day > 22) || ($month == 8 && $day <= 22))
	    {
	        $strValue = "狮子座";
	    }
	    else if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22))
	    {
	        $strValue = "处女座";
	    }
	    else if (($month == 9 && $day >= 23) || ($month == 10 && $day <= 23))
	    {
	        $strValue = "天秤座";
	    }
	    else if (($month == 10 && $day > 23) || ($month == 11 && $day <= 22))
	    {
	        $strValue = "天蝎座";
	    }
	    else if (($month == 11 && $day > 22) || ($month == 12 && $day <= 21))
	    {
	        $strValue = "射手座";
	    }
	    else if (($month == 12 && $day > 21) || ($month == 1 && $day <= 19))
	    {
	        $strValue = "魔羯座";
	    }
	    return $strValue;

	}
	
	/**
	 * 功能：通过身份证获得生肖
	 */
	//根据身份证号，自动返回对应的生肖
	function get_shengxiao($cid)
	{
	    $cid = $this->getIDCard($cid);
	    if (!$this->isIdCard($cid)) return '';
	    $start = 1901;
	    $end = $end = (int)substr($cid, 6, 4);
	    $x = ($start - $end) % 12;
	    $value = "";
	    if ($x == 1 || $x == -11)
	    {
	        $value = "鼠";
	    }
	    if ($x == 0)
	    {
	        $value = "牛";
	    }
	    if ($x == 11 || $x == -1)
	    {
	        $value = "虎";
	    }
	    if ($x == 10 || $x == -2)
	    {
	        $value = "兔";
	    }
	    if ($x == 9 || $x == -3)
	    {
	        $value = "龙";
	    }
	    if ($x == 8 || $x == -4)
	    {
	        $value = "蛇";
	    }
	    if ($x == 7 || $x == -5)
	    {
	        $value = "马";
	    }
	    if ($x == 6 || $x == -6)
	    {
	        $value = "羊";
	    }
	    if ($x == 5 || $x == -7)
	    {
	        $value = "猴";
	    }
	    if ($x == 4 || $x == -8)
	    {
	        $value = "鸡";
	    }
	    if ($x == 3 || $x == -9)
	    {
	        $value = "狗";
	    }
	    if ($x == 2 || $x == -10)
	    {
	        $value = "猪";
	    }
	    return $value;
	}


	
	/**
	 * 功能：通过身份证获得性别
	 */
	public function get_xingbie($cid, $comm = 0)
	{ //根据身份证号，自动返回性别
	    $cid = $this->getIDCard($cid);
	    if (!$this->isIdCard($cid)) return '';
	    $sexint = (int)substr($cid, 16, 1);
	    if ($comm > 0)
	    {
	        return $sexint % 2 === 0 ? '女士' : '先生';
	    }
	    else
	    {
	        return $sexint % 2 === 0 ? '女' : '男';
	    }

	}


	/**
	 * 功能：是否符合身份证格式
	 */
	public function isIdCard($number)
	{ // 检查是否是身份证号
	    $number = $this->getIDCard($number);
	    // 转化为大写，如出现x
	    $number = strtoupper($number);
	    //加权因子
	    $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	    //校验码串
	    $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
	    //按顺序循环处理前17位
	    $sigma = 0;
	    for ($i = 0; $i < 17; $i++)
	    {
	        //提取前17位的其中一位，并将变量类型转为实数
	        $b = (int)$number{$i};

	        //提取相应的加权因子
	        $w = $wi[$i];

	        //把从身份证号码中提取的一位数字和加权因子相乘，并累加
	        $sigma += $b * $w;
	    }
	    //计算序号
	    $snumber = $sigma % 11;

	    //按照序号从校验码串中提取相应的字符。
	    $check_number = $ai[$snumber];

	    if ($number{17} == $check_number)
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}


	/**
	 * 功能：把15位身份证转换成18位
	 *
	 * @param string $idCard
	 * @return newid or id
	 */
	public function getIDCard($idCard)
	{
	    // 若是15位，则转换成18位；否则直接返回ID
	    if (15 == strlen($idCard))
	    {
	        $W = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1);
	        $A = array("1", "0", "X", "9", "8", "7", "6", "5", "4", "3", "2");
	        $s = 0;
	        $idCard18 = substr($idCard, 0, 6) . "19" . substr($idCard, 6);
	        $idCard18_len = strlen($idCard18);
	        for ($i = 0; $i < $idCard18_len; $i++)
	        {
	            $s = $s + substr($idCard18, $i, 1) * $W [$i];
	        }
	        $idCard18 .= $A [$s % 11];
	        return $idCard18;
	    }
	    else
	    {
	        return $idCard;
	    }
	}

	/*
	$身份证号 = '';
	$生日 = strlen($身份证号)==15 ? ('19' . substr($身份证号, 6, 6)) : substr($身份证号, 6, 8);
	$性别 = substr($身份证号, (strlen($身份证号)==15 ? -2 : -1), 1) % 2 ? '男' : '女';
	*/




/////////////// 获取签名 ////////////////////
	
	
	
	//分享到朋友圈的帮忙砍价的url等分享信息
	public function get_share_url_info($uid=0){
		
		//分享当前页面的url
        $share_url=$this->get_current_page_url();
        
        
		//分享首页
        $url_path=U('home/index');
        //echo $url_path;exit;
        $share_url=BASE_URL.$url_path;
        
        
        //$share_url=urlencode($share_url);
        //echo $share_url;exit;
        $share_info['share_url']=$share_url;
        $share_info['share_title']='微官网';
        $share_info['share_desc']='微官网';
        $share_info['share_wxIco']=BASE_URL."/statics/images/share.jpg";
        //$share_info['share_wxIcoW']='469';
        //$share_info['share_wxIcoH']='328';
        
        //$this->assign('share_info', $share_info);
        return $share_info;
	}
	
	
	
	//生成访客id，游客id，guid
	public function createGuid()
    {
        mt_srand((double)microtime()*10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $guid = substr($charid, 0, 8).substr($charid, 8, 4).substr($charid,12, 4).substr($charid,16, 4).substr($charid,20,12);
        return $guid;
    }
	
	
	//过滤邮件内容  来自biofach
	function HTMLEncode2($fString){
		$fString = str_replace( "<", "&lt;" ,$fString);
		$fString = str_replace( ">", "&gt;" ,$fString);
		$fString = str_replace( CHR(34), "&quot;" ,$fString);
		$fString = str_replace( CHR(39), "&#39;" ,$fString);
		$fString = str_replace( CHR(13), "" ,$fString);
		$fString = str_replace( CHR(10), "<br/>" ,$fString);
		return $fString;
	}

	
	/* 二维数组按指定的键值排序
	* $array 数组
	* $key排序键值
	* $type排序方式
	*/
 	function arraySort($arr, $keys, $type = 'asc') 
 	{
 		$keysvalue = $new_array = array();
        foreach ($arr as $k => $v) 
        {
            $keysvalue[$k] = $v[$keys];
        }
        
        if ($type == 'asc') 
        {
            asort($keysvalue);
        } 
        else 
        {
            arsort($keysvalue);
        }
        
        reset($keysvalue);
        
        foreach ($keysvalue as $k => $v) 
        {
            $new_array[$k] = $arr[$k];
        }
        
        return $new_array;
    }
    
    
	
    //根据token拿个人信息
    //type:模板调用时候传ajax, 控制器调用传controller
    //login_after_jump_url：如果是跳超时，则跳登陆页，登陆完毕后，需要跳转到这个页面。所以要将这个页面地址写到session里
    public function token_member($type='ajax',$app_token='')
	{
		$before_login_url=$this->get_current_page_url();
		
		if(isset($_POST['login_after_jump_url']) && !empty($_POST['login_after_jump_url'])){
			$login_after_jump_url=$_POST['login_after_jump_url'];
		}
		else{
			$login_after_jump_url=$before_login_url;
		}
		
		if(empty($app_token)){ 
			$app_token=$_SESSION['app_token'];
			if(empty($app_token)){
				
				if(isset($login_after_jump_url) && $login_after_jump_url!=""){
					$_SESSION['login_after_jump_url'] = $login_after_jump_url;
				}
				
				$token_rst['success']='need_login';
				$token_rst['msg']='请先登陆';
			
			}
			else{
				$app_token=$_SESSION['app_token'];
			}
			
		}
		else{
			$_SESSION['app_token'] = $app_token;
		}
		
		
		$api_url='http://api.xrace.cn:'.$this->api_port.'/xrace/get_user_profile.json?token='.$app_token;
		$api_para=array();
		$api_result=$this->http_request_url_post($api_url,$api_para);
	    $user_info = empty($api_result)?array():$api_result;
		//echo "<pre>";print_r($user_info);exit;
		
		if(isset($user_info['user_auth']['auth_result']) && $user_info['user_auth']['auth_result']==2){
        	$token_rst['success']='success';
			$token_rst['msg']='已通过实名认证';
			$token_rst['user_info']=$user_info;
		}
		else{
			
			if(isset($user_info['user_auth']) || isset($user_info['user_id'])){
				
				$is_wexin=$_SESSION['is_wexin'];
				if($is_wexin==1){
					$token_rst['success']='success';
					$token_rst['msg']='是注册用户';
					$token_rst['user_info']=$user_info;
				}
				else{
					//2015.5.6之前，app进来的用户，遇到这个情况，需要先通过实名认证
					//$token_rst['success']='need_auth';
					//$token_rst['msg']='请前往个人中心完成实名认证';
					
					//2015.5.6之后，钟会遇见你要求改为不用判断是否实名认证，都可以报名
					$token_rst['success']='success';
					$token_rst['msg']='请前往个人中心完成实名认证';
					$token_rst['user_info']=$user_info;
					
				}
				
			}
			else{
				
				//token过期了
				
				if(isset($login_after_jump_url) && $login_after_jump_url!=""){
					$_SESSION['login_after_jump_url'] = $login_after_jump_url;
				}
				
				$token_rst['success']='token_expire';
				$token_rst['msg']='您的会话已超时，请重新登录。';
				
			}
			
		}
		
		//if($type=='ajax'){
		//	$token_rst_e=json_encode($token_rst);
		//	echo $token_rst_e;
		//	exit;
		//}
		//else{
			return $token_rst;
		//}
	}
	
	
	
	//支付完成。 order_no就是out_trade_no。 trade_no就是transaction_id。
	//存在被刷的情况（http://xracebm201607.loc/order/paySuccess/order_no/sss/trade_no/sfds），改为写到weixin和alipay的回调方法里。
	/*
	public function paySuccess($order_no, $trade_no){
		
		
		//$token_rst=$this->token_member('controller');
		//$userinfo=$token_rst['user_info'];
		//echo "<pre>";print_r($userinfo);exit;
		
		//and member_id='".addslashes($userinfo['user_id'])."'
		
    	$OrderMod = M('order');
	    $order_info = $OrderMod->where(" order_no='".addslashes($order_no)."' " )->select();
	    //echo "<pre>";print_r($order_info);echo "<pre>";exit;
	    if(!empty($order_info)){
        	$order_info=$order_info[0];
        	$isPay = $order_info['isPay'];
        	$order_id = $order_info['id'];
        	$status = $order_info['status'];
        	$member_id = $order_info['member_id'];
        	
        	if( ($isPay==0 || $isPay==2) && $status==1 && !empty($trade_no) ) {
        		//更新订单表
            	$payDateTime = date("Y-m-d H:i:s");
            	
            	$sql=sprintf("UPDATE %s SET payDateTime='".addslashes($payDateTime)."' 
		        , isPay='1' 
		        , trade_no='".addslashes($trade_no)."' 
		        , isExpire='1' 
		        where order_no='".addslashes($order_no)."' ", $OrderMod->getTableName() );
		        //echo $sql;exit;
		        //$result = $OrderMod->execute($sql);
		        
		        
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
		
	}
	*/
	
	
	//清查订单过期订单
	public function checkOrderIsExpire(){
		
		//选完产品，进入最后支付界面，才会写入过期时间expireDateTime，超过这个时间还没支付完成的订单，视为过期。
		//没走到最后支付界面的，status仍然是0，等同于无效订单，无需考虑过期问题。
        $now_time = date("Y-m-d H:i:s");
        $orderMod = M('order');
        $sql=sprintf("update %s SET isExpire='1' 
	    where status=1 and isExpire=0 and expireDateTime<'".addslashes($now_time)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    
	    
	    
	    
	    
	    
		/*
		//选完产品，进入最后支付界面，才会写入过期时间expireDateTime，超过这个时间还没支付完成的订单，视为过期。
        $now_time = date("Y-m-d H:i:s");
        $orderMod = M('order');
        $sql=sprintf("update %s SET isExpire='1' 
	    where status=1 and isExpire=0 and expireDateTime<'".addslashes($now_time)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    
	    
	    //提交完个人信息此时status还是0，超过1天，还没进入选产品或最后支付界面的，就是超过1天，status还没变成1的，视为过期。
	    $now_time = date("Y-m-d H:i:s");
        $orderMod = M('order');
        $sql=sprintf("update %s SET isExpire='1' 
	    where status=0 and isExpire=0 and expireDateTime<'".addslashes($now_time)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    */
	    
	    /*
	    //只要是到了过期时间 expireDateTime 的，都视为过期。
	    $now_time = date("Y-m-d H:i:s");
        $orderMod = M('order');
        $sql=sprintf("update %s SET isExpire='1' 
	    where isExpire=0 and expireDateTime<'".addslashes($now_time)."' 
	    ", $orderMod->getTableName() );
	    $result = $orderMod->execute($sql);
	    */
	    
	}
	
	
	
	
	//写入数据库sql语句日志
	public function set_log_sql($sql_txt){
		$addtime_t=time();
		$addtime=date('Y-m-d H:i:s',$addtime_t);
		
		$log_sqlMod = M('log_sql');
        $sql=sprintf("INSERT %s SET 
         addtime='".addslashes($addtime)."' 
        , sql_txt='".addslashes($sql_txt)."' 
        ", $log_sqlMod->getTableName() );
        //echo $sql;exit;
        $result = $log_sqlMod->execute($sql);
	    
		
	}
	
	
	
	
	
	//微信  发客服消息
	/**
     * 发送客服消息
     * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
     * @return boolean|array
     */
	public function sendCustomMessage($data){
		
    	require_once APP_PATH .'Lib/Wechat/Wechat.class.php';   //类库文件位置：/app/AppHome/Lib/Wechat/Wechat.class.php
		
		$options = array(
            'token' => WX_APPID_TOKEN, //填写你设定的key
            'encodingaeskey' => WX_APPID_ENCODINGAESKEY, //填写加密用的EncodingAESKey
            'appid' => WX_APPID, //填写高级调用功能的app id
            'appsecret' => WX_APPSECRET, //填写高级调用功能的密钥
        );
        
        $wechat = new Wechat($options);
        //echo "<pre>";print_r($wechat);exit;
        
        $msg_result = $wechat->sendCustomMessage($data);
        return $msg_result;
		
	}
	
	
	//验证是否正在操作本人的数据
	public function verify_body($order_id=0,$verify=true,$confirm_apply=true){
		//echo "<pre>";print_r($_SESSION);exit;
		if( ($verify==true && !empty($_SESSION['id_type']) && !empty($_SESSION['id_number']) ) || $verify==false ){
			$and_cond='';
			$and_cond=$and_cond.' and status=1 ' ;
			if($confirm_apply==true){
				$and_cond=$and_cond.' and confirm_apply=1 ' ;
			}
			$and_cond=$and_cond.' and id="' . addslashes($order_id) .'" ' ;
			//echo $and_cond;exit;
			$orderMod = M('order');
	        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
	        if(!empty($order_data)){
	        	$order_info=empty($order_data)?array():$order_data[0];
		        //echo "<pre>";print_r($order_info);exit;
		        if(!empty($order_info) && (($verify==true && $_SESSION['id_type']==$order_info['id_type'] && $_SESSION['id_number']==$order_info['id_number']) || $verify==false)  ){
		        	return $order_info;
		        }
		        else{
		        	return false;
		        }
	        }
			else{
				return false;
			}
		}
		return false;
	}
	
	
	
	//验证是否正在操作本人的数据  团队
	public function verify_body_team($order_id=0,$verify=true,$confirm_apply=true){
		//echo "<pre>";print_r($_SESSION);exit;
		if( ($verify==true && !empty($_SESSION['id_type']) && !empty($_SESSION['id_number']) ) || $verify==false ){
			$and_cond='';
			$and_cond=$and_cond.' and status=1 ' ;
			if($confirm_apply==true){
				$and_cond=$and_cond.' and confirm_apply=1 ' ;
			}
			$and_cond=$and_cond.' and id="' . addslashes($order_id) .'" ' ;
			//echo $and_cond;exit;
			$orderMod = M('team');
	        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
	        if(!empty($order_data)){
	        	$order_info=empty($order_data)?array():$order_data[0];
		        //echo "<pre>";print_r($order_info);exit;
		        if(!empty($order_info) && (($verify==true && $_SESSION['id_type']==$order_info['id_type'] && $_SESSION['id_number']==$order_info['id_number']) || $verify==false)  ){
		        	return $order_info;
		        }
		        else{
		        	return false;
		        }
	        }
			else{
				return false;
			}
		}
		return false;
	}
	
	
	//验证是否正在操作本人的数据  定向赛
	public function verify_body_orient($order_id=0,$verify=true,$confirm_apply=true){
		//echo "<pre>";print_r($_SESSION);exit;
		if( ($verify==true && !empty($_SESSION['id_type']) && !empty($_SESSION['id_number']) ) || $verify==false ){
			$and_cond='';
			$and_cond=$and_cond.' and status=1 ' ;
			if($confirm_apply==true){
				$and_cond=$and_cond.' and confirm_apply=1 ' ;
			}
			$and_cond=$and_cond.' and id="' . addslashes($order_id) .'" ' ;
			//echo $and_cond;exit;
			$orderMod = M('order_orient');
	        $order_data = $orderMod->where(" 1 ".$and_cond )->select();
	        if(!empty($order_data)){
	        	$order_info=empty($order_data)?array():$order_data[0];
		        //echo "<pre>";print_r($order_info);exit;
		        if(!empty($order_info) && (($verify==true && $_SESSION['id_type']==$order_info['id_type'] && $_SESSION['id_number']==$order_info['id_number']) || $verify==false)  ){
		        	return $order_info;
		        }
		        else{
		        	return false;
		        }
	        }
			else{
				return false;
			}
		}
		return false;
	}
	
	
	//返回比赛金额
	//1、中国籍选手（含港澳台）：马拉松150元/人，半程马拉松120元/人，迷你马拉松80元/人。
	//2、外籍选手：马拉松300元/人，半程马拉松240元/人，迷你马拉松160元/人。
	public function get_price_race($cat_id=0,$guoji=0){
		if($cat_id==1){
			if($guoji==0 || $guoji==1){
				$price_race=100;    //100
			}
			else{
				$price_race=100;    //100
			}
		}
		elseif($cat_id==2){
			if($guoji==0 || $guoji==1){
				$price_race=80;    //80
			}
			else{
				$price_race=80;     //80
			}
		}
		elseif($cat_id==3){
			if($guoji==0 || $guoji==1){
				$price_race=50;      //50
			}
			else{
				$price_race=50;      //50
			}
		}
		//elseif($cat_id==4){
		//	if($guoji==0 || $guoji==1){
		//		$price_race=1500;
		//	}
		//	else{
		//		$price_race=1500;
		//	}
		//}
		//elseif($cat_id==5){
		//	if($guoji==0 || $guoji==1){
		//		$price_race=1500;
		//	}
		//	else{
		//		$price_race=1500;
		//	}
		//}
		elseif($cat_id==6){  //团队
			if($guoji==0 || $guoji==1){
				$price_race=0.01;
			}
			else{
				$price_race=0.01;
			}
		}
		elseif($cat_id==7){  //团队excel导入
			if($guoji==0 || $guoji==1){
				$price_race=0.01;
			}
			else{
				$price_race=0.01;
			}
		}
		else{
			$price_race=0;
		}
		return $price_race;
	}
	
	
	
	
	//返回比赛各项目名额。如不限制名额，则 limit_number 置0。
	public function get_limit_number($cat_id=0,$order_id=0){
		if($cat_id==1){
			$limit_number=1113;    //原定：2000  20170928=1700   20171020=1113  
		}
		elseif($cat_id==2){
			$limit_number=2926;    //原定：3000   20170928=2600  20171009=2750  20171013=（2876说加50个名额）2926
		}
		elseif($cat_id==3){
			$limit_number=6864;    //原定：5000   20170928=4200  20171010=4191     20171024=4864+2000=6864    20171028=6622
		}
		elseif($cat_id==4){
			$limit_number=0;
		}
		elseif($cat_id==5){
			$limit_number=0;
		}
		elseif($cat_id==6){  //团队
			$limit_number=0;
		}
		elseif($cat_id==7){  //团队excel导入
			$limit_number=0;
		}
		else{
			$limit_number=0;
		}
		
		if($limit_number>0){
			$and_cond='';
			$and_cond=$and_cond.' and cat_id="'.addslashes($cat_id).'" ' ;
			$and_cond=$and_cond.' and status=1 ' ;
			$and_cond=$and_cond.' and status_apply=1 ' ;
			//$and_cond=$and_cond.' and status_attach=1 ' ;   //按审核通过限制名额
			//$and_cond=$and_cond.' and isPay=1 ' ;    //按支付成功限制名额
			$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) )  ' ;    //按支付成功限制名额，正在付款的人也占用名额。
			if(!empty($order_id)){
			$and_cond=$and_cond.' and id!="'.addslashes($order_id).'" ' ;
			}
			//echo $and_cond;exit;
			$orderMod = M('order');
		        $order_data = $orderMod->field('count(id) as is_cunzai_number ')->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        $is_cunzai_number=empty($order_data[0]['is_cunzai_number'])?0:$order_data[0]['is_cunzai_number'];
		        //var_dump($is_cunzai_number);exit;
		        
		        if($is_cunzai_number>=$limit_number){
		        	return 'N';
		        }
		}
		
		return 'Y';
	}
	
	
	
	
	//团队  返回比赛各项目名额。如不限制名额，则 limit_number 置0。
	public function get_limit_number_team($cat_id=0,$order_id=0){
		if($cat_id==1){
			$limit_number=0;
		}
		elseif($cat_id==2){
			$limit_number=0;
		}
		elseif($cat_id==3){
			$limit_number=0;
		}
		elseif($cat_id==4){
			$limit_number=200;
		}
		elseif($cat_id==5){
			$limit_number=200;
		}
		elseif($cat_id==6){  //团队
			$limit_number=0;
		}
		else{
			$limit_number=0;
		}
		
		if($limit_number>0){
			$and_cond='';
			$and_cond=$and_cond.' and cat_id="'.addslashes($cat_id).'" ' ;
			$and_cond=$and_cond.' and status=1 ' ;
			$and_cond=$and_cond.' and status_apply=1 ' ;
			//$and_cond=$and_cond.' and status_attach=1 ' ;   //按审核通过限制名额
			//$and_cond=$and_cond.' and isPay=1 ' ;    //按支付成功限制名额
			$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) )  ' ;    //按支付成功限制名额，正在付款的人也占用名额。
			if(!empty($order_id)){
			$and_cond=$and_cond.' and id!="'.addslashes($order_id).'" ' ;
			}
			//echo $and_cond;exit;
			$orderMod = M('team');
		        $order_data = $orderMod->field('count(id) as is_cunzai_number ')->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        $is_cunzai_number=empty($order_data[0]['is_cunzai_number'])?0:$order_data[0]['is_cunzai_number'];
		        //var_dump($is_cunzai_number);exit;
		        
		        if($is_cunzai_number>=$limit_number){
		        	return 'N';
		        }
		}
		
		return 'Y';
	}
	
	
	//返回比赛各项目名额。如不限制名额，则 limit_number 置0。  定向赛
	public function get_limit_number_orient($cat_id=0,$order_id=0){
		
		if($cat_id==1){
			$limit_number=0;
		}
		elseif($cat_id==2){
			$limit_number=0;
		}
		elseif($cat_id==3){
			$limit_number=0;
		}
		elseif($cat_id==4){
			$limit_number=200;
		}
		elseif($cat_id==5){
			$limit_number=200;
		}
		elseif($cat_id==6){
			$limit_number=126;
		}
		else{
			$limit_number=0;
		}
		
		if($limit_number>0){
			$and_cond='';
			$and_cond=$and_cond.' and cat_id="'.addslashes($cat_id).'" ' ;
			$and_cond=$and_cond.' and status=1 ' ;
			$and_cond=$and_cond.' and confirm_apply=1 ' ;   //已提交
			//$and_cond=$and_cond.' and status_apply=1 ' ;
			//$and_cond=$and_cond.' and status_attach=1 ' ;   //按审核通过限制名额
			//$and_cond=$and_cond.' and isPay=1 ' ;    //按支付成功限制名额
			//$and_cond=$and_cond.' and ( isPay>0 or (isPay=0 and isExpire=0) )  ' ;    //按支付成功限制名额，正在付款的人也占用名额。
			if(!empty($order_id)){
			$and_cond=$and_cond.' and id!="'.addslashes($order_id).'" ' ;
			}
			//echo $and_cond;exit;
			$orderMod = M('order_orient');
		        $order_data = $orderMod->field('count(id) as is_cunzai_number ')->where(" 1 ".$and_cond )->select();
		        //echo "<pre>";print_r($order_data);exit;
		        $is_cunzai_number=empty($order_data[0]['is_cunzai_number'])?0:$order_data[0]['is_cunzai_number'];
		        //var_dump($is_cunzai_number);exit;
		        
		        if($is_cunzai_number>=$limit_number){
		        	return 'N';
		        }
		}
		
		return 'Y';
	}
	
	
	//发短信和邮件通知
	public function send_msg_sms_email($type_id=0){
		
	}
	

}

?>