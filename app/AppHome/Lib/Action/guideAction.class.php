<?php
class guideAction extends TAction
{

	
	
	
	//参赛指南 > 报名须知
	public function rule(){
		
		
        $id=1;
        
        $NoticeMod = M('rule');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('rule');
	}
	
	
	
	//参赛指南 > 风险提示
	public function risk_tips(){
		
		
        $id=2;
        
        $NoticeMod = M('rule');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('risk_tips');
	}
	
	
	
	//参赛指南 > 参赛声明
	public function statement(){
		
		
        $id=3;
        
        $NoticeMod = M('rule');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('statement');
	}
	
	
	
	
	//参赛指南 > 竞赛规程
	public function regulation(){
		
		
        $id=4;
        
        $NoticeMod = M('rule');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('regulation');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	//参赛指南 > 赛道路线图
	public function route(){
		
        $id=5;
        
        $NoticeMod = M('rule');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('route');
	}
	
	
	
	
	//参赛指南 > 体检报告下载
	public function cert_medical(){
		
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('cert_medical');
	}
	
	
	
	//参赛指南 > Q&A
	public function qa(){
		
        $id=6;
        
        $NoticeMod = M('rule');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('qa');
	}
	
	
	
	//参赛指南 > 领物须知
	public function instruction(){
		
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('instruction');
	}
    
    
    
    
	
	//参赛指南 > 摆渡服务介绍
	public function bus_point(){
		
		
		
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('bus_point');
	}
	
	
	//参赛指南 > 城市定向赛《赛事规程》  
	//示例：http://cdmalasong.loc/guide/orient_statement
	public function orient_statement(){
		
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('orient_statement');
	}
	
	
	
	//参赛指南 > 城市定向赛《报名须知》  
	//示例：http://cdmalasong.loc/guide/orient_rule
	public function orient_rule(){
		
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('orient_rule');
	}
	
	
	
	
	
	//参赛指南 > 急救跑者
	//示例：http://cdmalasong.loc/guide/emergency_runner
	public function emergency_runner(){
		
        $id=7;
        
        $NoticeMod = M('rule');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }
        
        
        
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('emergency_runner');
	}
	
	//参赛指南 > 选择比赛
	//示例：http://cdmalasong.loc/guide/match_choose
	public function match_choose(){
		
        $this->assign('curmenu', '6');
        $this->assign('curmenu_two', '61');
		$this->display('match_choose');
	}
	
	
	
	
	//公益报名须知  http://cdmalasong.loc/guide/welfare_rule
	public function welfare_rule(){
		
		
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
        $this->display('welfare_rule');
    }
	
	
	
	

}
?>