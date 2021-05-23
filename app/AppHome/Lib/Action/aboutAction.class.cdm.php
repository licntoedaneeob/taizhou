<?php
class aboutAction extends TAction
{

	//关于我们  信息反馈  http://cdmalasong.loc/about/contact
	public function contact(){
	    	
	    	
	        $id=2;
	        
	        $NoticeMod = M('about');
	        // 读取数据
	        $data =   $NoticeMod->find($id);

	        if($data) {
	            $this->record = $data;// 模板变量赋值
	        }else{
	            $this->error('用户数据读取错误');
	        }
	        
	        
	        $this->display('contact');
	    }
	
	//关于我们  信息反馈  提交  http://cdmalasong.loc/about/contact_sub?dosubmit=yes&realname=小明1&mobile=13988882222&summary=没问题
	public function contact_sub(){
	    	 
		//echo "<pre>";print_r($_POST);exit;
		
		
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
            
            
            
		if(isset($_REQUEST['dosubmit'])){
			
			
			$addtime_t=time();
			$addtime=date('Y-m-d H:i:s',$addtime_t);
			$user_id=0;
			
			$UserMod = M('contact');
		        $sql=sprintf("INSERT %s SET realname='".addslashes($this->remove_xss($_REQUEST['realname']))."' 
		        , user_id='".addslashes($user_id)."'
		        , mobile='".addslashes($this->remove_xss($_REQUEST['mobile']))."'
		        , summary='".addslashes($this->remove_xss($_REQUEST['summary']))."'
		        , sex='".addslashes($this->remove_xss($_REQUEST['sex']))."'
		        , title='".addslashes($this->remove_xss($_REQUEST['title']))."'
		        , email='".addslashes($this->remove_xss($_REQUEST['email']))."'
		        , create_time='".$addtime_t."' 
		        , modify_time='".$addtime_t."' 
		        , addtime='".$addtime."' 
		        ", $UserMod->getTableName() );
		        //echo $sql;exit;
		        $result = $UserMod->execute($sql);
	        
	        
	        //redirect(U('qa/qa_attend_finish', array('class_id'=>$_POST['class_id'] )));
	        //exit;
	        
	        $return['success']='success';
	        echo json_encode($return);
	        exit;
        
        }
        
        //$return['success']='failed';
        //echo json_encode($return);
        exit;
        
        }
	 
	 
	 
	 
	 //关于我们  联系我们   
	public function contactus(){
	    	
	    	
	        $id=2;
	        
	        $NoticeMod = M('about');
	        // 读取数据
	        $data =   $NoticeMod->find($id);

	        if($data) {
	            $this->record = $data;// 模板变量赋值
	        }else{
	            $this->error('用户数据读取错误');
	        }
	        
	        
	        $this->display('contactus');
	    }
	    
	    //关于我们  赛事简介 
	public function intro(){
	    	
	    	
	        $id=1;
	        
	        $NoticeMod = M('about');
	        // 读取数据
	        $data =   $NoticeMod->find($id);

	        if($data) {
	            $this->record = $data;// 模板变量赋值
	        }else{
	            $this->error('用户数据读取错误');
	        }
	        
	        
	        
	        $this->display('intro');
	    }
	    
	    
	
	

}
?>