<?php
class indexAction extends TAction
{
	/**
	 *--------------------------------------------------------------+
	 * Action: index (默认操作)
	 *--------------------------------------------------------------+
	 */
    public function index()
    {
    	if( !session('cmscp_account') ){
			$this->redirect('public/login');
		}

		//echo "<pre>";print_r($this->CurrAccount);exit;
		
		if ($this->CurrAccount['Account-Role']=='administrator' || $this->CurrAccount['Account-Role']=='admin'){
    		$this->assign('DefaultPage', U('public/main'));
    		$this->display('index');
    		exit;
    	}
    	if ($this->CurrAccount['Account-Role']=='sales'){
    		$this->assign('DefaultPage', U('public/main'));
    		$this->display('index');
    		exit;
    	}
    	
    	$this->assign('DefaultPage', U('public/main'));
    	$this->display('index');
    	exit;
    	
    }

    /**
     * 当前位置
     * @access public
     * @return Image
     */
	public function CurrentPostion(){
		
	}

//	/*当前位置*/
//    public function current_pos()
//    {
//        $group_id = intval($_REQUEST['tag']);
//        $menuid = intval($_REQUEST['menuid']);
//
//        $r = M('node')->field('group_id,module_name,action_name')->where('id='.$menuid)->find();
//        if($r)
//        {
//            $group_id = $r['group_id'];
//        }
//
//        $group = M('group')->field('title')->where('id='.$group_id)->find();
//        if($group)
//        {
//            echo $group['title'];
//        }
//        if($r)
//        {
//            echo '->'.$r['module_name'].'->'.$r['action_name'];
//        }
//        exit;
//    }
}
?>