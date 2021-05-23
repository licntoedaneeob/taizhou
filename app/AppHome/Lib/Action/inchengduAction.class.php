<?php
class inchengduAction extends TAction
{

	
	//在成都
	public function index(){
		
		
        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }
        
        if(empty($id)){
        	$id=1;
        }
        
        $NoticeMod = M('inchengdu');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('index');
	}
	

}
?>