<?php
class informationAction extends TAction
{


	//新闻资讯 列表
	public function news(){

		//置顶推荐的新闻
        $CityMod = M('news');
        $news_top = $CityMod->where(" status=1 and is_top=1 and class_id=11" )->order('is_top desc, sort asc , id desc')->limit('0,10000')->select();
		$this->assign('news_top', $news_top);
        //echo "<pre>";print_r($news_top);exit;


        //置顶推荐的新闻
        //$CityMod = M('pc_news_information');
        //$news_information_top = $CityMod->where(" status=1 and is_top=1 " )->order('is_top desc, sort asc , id desc')->limit('0,10000')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;
        //$CityMod = M('pc_news_ad');
        //$news_information_top = $CityMod->where(" status=1 " )->order('sort asc , id desc')->limit('0,10000')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;




        if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }

        $sqlWhere .= " and class_id=11 ";
        $sqlOrder = " sort asc, id desc ";

        $this->ModManager = M('news');
        $fields = $this->ModManager->getDbFields();

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('news', $sqlWhere, $sqlOrder, '20', 'M');
        //echo "<pre>";print_r($rst['dataset']);exit;
		$news_information_list=empty($rst['dataset'])?array():$rst['dataset'];

		/*
        if(isset($news_information_list)){

            foreach($news_information_list as $k => $v){

				$CityMod = M('cn_news_photo');
		        $photo_list = $CityMod->where(" status=1 and class_id='".addslashes($v['id'])."' " )->order(' sort asc , id desc')->limit('0,10000')->select();
		        //echo "<pre>";print_r($photo_list);exit;

                $news_information_list[$k]['photo_list']=$photo_list;


            }
        }
        */

        //echo "<pre>";print_r($news_information_list);exit;

		$this->assign('news_information_list', $news_information_list);




        //$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '1');
        $this->assign('curmenu_two', '61');
		$this->display('news');
	}


	//新闻资讯 详情页
	public function news_detail(){


        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }

        $NoticeMod = M('news');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }


        //置顶推荐的新闻
        //$CityMod = M('pc_news_information');
        //$news_information_top = $CityMod->where(" status=1 and is_top=1 " )->order('is_top desc, sort asc , id desc')->limit('0,4')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;



		$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '1');
        $this->assign('curmenu_two', '61');
		$this->display('news_detail');
	}







	//公告 列表
	public function notice(){
		//置顶推荐的公告
        $CityMod = M('news');
       $notice_top = $CityMod->where(" status=1 and is_top=1 and class_id=12" )->order('is_top desc, sort asc , id asc')->limit('0,10000')->select();
		$this->assign('notice_top', $notice_top);
        //echo "<pre>";print_r($notice_top);exit;



        //置顶推荐的新闻
        //$CityMod = M('pc_news_information');
        //$news_information_top = $CityMod->where(" status=1 and is_top=1 " )->order('is_top desc, sort asc , id desc')->limit('0,10000')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;
        //$CityMod = M('pc_news_ad');
        //$news_information_top = $CityMod->where(" status=1 " )->order('sort asc , id desc')->limit('0,10000')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;




        if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }

        $sqlWhere .= " and class_id=12 ";
        $sqlOrder = " sort asc, id desc ";

        $this->ModManager = M('news');
        $fields = $this->ModManager->getDbFields();

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('news', $sqlWhere, $sqlOrder, '4', 'M');
        //echo "<pre>";print_r($rst['dataset']);exit;
		$news_information_list=empty($rst['dataset'])?array():$rst['dataset'];

		/*
        if(isset($news_information_list)){

            foreach($news_information_list as $k => $v){

				$CityMod = M('cn_news_photo');
		        $photo_list = $CityMod->where(" status=1 and class_id='".addslashes($v['id'])."' " )->order(' sort asc , id desc')->limit('0,10000')->select();
		        //echo "<pre>";print_r($photo_list);exit;

                $news_information_list[$k]['photo_list']=$photo_list;


            }
        }
        */

        //echo "<pre>";print_r($news_information_list);exit;

		$this->assign('news_information_list', $news_information_list);




        //$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '1');
        $this->assign('curmenu_two', '61');
		$this->display('notice');
	}


	//公告 详情页
	public function notice_detail(){


        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }

        $NoticeMod = M('news');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }


        //置顶推荐的新闻
        //$CityMod = M('pc_news_information');
        //$news_information_top = $CityMod->where(" status=1 and is_top=1 " )->order('is_top desc, sort asc , id desc')->limit('0,4')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;



		$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '1');
        $this->assign('curmenu_two', '61');
		$this->display('notice_detail');
	}




	//精彩图集 列表
	public function picture(){



        //置顶推荐的新闻
        //$CityMod = M('pc_news_information');
        //$news_information_top = $CityMod->where(" status=1 and is_top=1 " )->order('is_top desc, sort asc , id desc')->limit('0,10000')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;
        //$CityMod = M('pc_news_ad');
        //$news_information_top = $CityMod->where(" status=1 " )->order('sort asc , id desc')->limit('0,10000')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;




        if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }

        $sqlWhere .= " and class_id=13 ";
        $sqlOrder = " sort asc, id desc ";

        $this->ModManager = M('picture');
        $fields = $this->ModManager->getDbFields();

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('picture', $sqlWhere, $sqlOrder, '8', 'M');
        //echo "<pre>";print_r($rst['dataset']);exit;
		$news_information_list=empty($rst['dataset'])?array():$rst['dataset'];

		/*
        if(isset($news_information_list)){

            foreach($news_information_list as $k => $v){

				$CityMod = M('cn_news_photo');
		        $photo_list = $CityMod->where(" status=1 and class_id='".addslashes($v['id'])."' " )->order(' sort asc , id desc')->limit('0,10000')->select();
		        //echo "<pre>";print_r($photo_list);exit;

                $news_information_list[$k]['photo_list']=$photo_list;


            }
        }
        */

        //echo "<pre>";print_r($news_information_list);exit;

		$this->assign('news_information_list', $news_information_list);




        //$this->assign('banpic', 'news_txt.png');
      $this->assign('curmenu', '1');
        $this->assign('curmenu_two', '61');
		$this->display('picture');
	}


	//精彩图集 详情页
	public function picture_detail(){


        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }

        $NoticeMod = M('picture');
        // 读取数据
        $data  =   $NoticeMod->find($id);
        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }


        //置顶推荐的新闻
        //$CityMod = M('pc_news_information');
        //$news_information_top = $CityMod->where(" status=1 and is_top=1 " )->order('is_top desc, sort asc , id desc')->limit('0,4')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;



		$this->assign('banpic', 'news_txt.png');
      $this->assign('curmenu', '1');
        $this->assign('curmenu_two', '61');
		$this->display('picture_detail');
	}





	//视频集锦 列表
	public function video(){



        //置顶推荐的新闻
        //$CityMod = M('pc_news_information');
        //$news_information_top = $CityMod->where(" status=1 and is_top=1 " )->order('is_top desc, sort asc , id desc')->limit('0,10000')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;
        //$CityMod = M('pc_news_ad');
        //$news_information_top = $CityMod->where(" status=1 " )->order('sort asc , id desc')->limit('0,10000')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;




        if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }

        $sqlWhere .= " and class_id=14 ";
        $sqlOrder = " sort asc, id desc ";

        $this->ModManager = M('video');
        $fields = $this->ModManager->getDbFields();

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('video', $sqlWhere, $sqlOrder, '8', 'M');
        //echo "<pre>";print_r($rst['dataset']);exit;
		$news_information_list=empty($rst['dataset'])?array():$rst['dataset'];

		/*
        if(isset($news_information_list)){

            foreach($news_information_list as $k => $v){

				$CityMod = M('cn_news_photo');
		        $photo_list = $CityMod->where(" status=1 and class_id='".addslashes($v['id'])."' " )->order(' sort asc , id desc')->limit('0,10000')->select();
		        //echo "<pre>";print_r($photo_list);exit;

                $news_information_list[$k]['photo_list']=$photo_list;


            }
        }
        */

        //echo "<pre>";print_r($news_information_list);exit;

		$this->assign('news_information_list', $news_information_list);




        //$this->assign('banpic', 'news_txt.png');
       $this->assign('curmenu', '1');
        $this->assign('curmenu_two', '61');
		$this->display('video');
	}


	//视频集锦 详情页
	public function video_detail(){


        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }

        $NoticeMod = M('video');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }


        //置顶推荐的新闻
        //$CityMod = M('pc_news_information');
        //$news_information_top = $CityMod->where(" status=1 and is_top=1 " )->order('is_top desc, sort asc , id desc')->limit('0,4')->select();
		//$this->assign('news_information_top', $news_information_top);
        //echo "<pre>";print_r($news_information_top);exit;



		$this->assign('banpic', 'news_txt.png');
       $this->assign('curmenu', '1');
        $this->assign('curmenu_two', '61');
		$this->display('video_detail');
	}











	//旅游指南 列表   /information/tourist
	public function tourist(){


        if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }

        $sqlWhere .= " and class_id=13 ";
        $sqlOrder = " sort asc, id desc ";

        $this->ModManager = M('news');
        $fields = $this->ModManager->getDbFields();

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('news', $sqlWhere, $sqlOrder, '20', 'M');
        //echo "<pre>";print_r($rst['dataset']);exit;
		$news_information_list=empty($rst['dataset'])?array():$rst['dataset'];

		/*
        if(isset($news_information_list)){

            foreach($news_information_list as $k => $v){

				$CityMod = M('cn_news_photo');
		        $photo_list = $CityMod->where(" status=1 and class_id='".addslashes($v['id'])."' " )->order(' sort asc , id desc')->limit('0,10000')->select();
		        //echo "<pre>";print_r($photo_list);exit;

                $news_information_list[$k]['photo_list']=$photo_list;


            }
        }
        */

        //echo "<pre>";print_r($news_information_list);exit;

		$this->assign('news_information_list', $news_information_list);




        //$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '0');
        $this->assign('curmenu_two', '61');
		$this->display('tourist');
	}


	//新闻资讯 详情页
	public function tourist_detail(){


        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }

        $NoticeMod = M('news');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }



		$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '0');
        $this->assign('curmenu_two', '61');
		$this->display('tourist_detail');
	}







	//配套活动 列表   /information/event
	public function event(){


        if(isset($_SESSION['allow_preview']) && $_SESSION['allow_preview']==1){
        	$sqlWhere = "status >=0";
        }
        else{
        	$sqlWhere = "status =1";
        }

        $sqlWhere .= " and class_id=14 ";
        $sqlOrder = " sort asc, id desc ";

        $this->ModManager = M('news');
        $fields = $this->ModManager->getDbFields();

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('news', $sqlWhere, $sqlOrder, '20', 'M');
        //echo "<pre>";print_r($rst['dataset']);exit;
		$news_information_list=empty($rst['dataset'])?array():$rst['dataset'];

		/*
        if(isset($news_information_list)){

            foreach($news_information_list as $k => $v){

				$CityMod = M('cn_news_photo');
		        $photo_list = $CityMod->where(" status=1 and class_id='".addslashes($v['id'])."' " )->order(' sort asc , id desc')->limit('0,10000')->select();
		        //echo "<pre>";print_r($photo_list);exit;

                $news_information_list[$k]['photo_list']=$photo_list;


            }
        }
        */

        //echo "<pre>";print_r($news_information_list);exit;

		$this->assign('news_information_list', $news_information_list);




        //$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '0');
        $this->assign('curmenu_two', '61');
		$this->display('event');
	}


	//新闻资讯 详情页
	public function event_detail(){


        if( isset($_GET['id']) ){
            $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
        }

        $NoticeMod = M('news');
        // 读取数据
        $data =   $NoticeMod->find($id);

        if($data) {
            $this->record = $data;// 模板变量赋值
        }else{
            $this->error('用户数据读取错误');
        }



		$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '0');
        $this->assign('curmenu_two', '61');
		$this->display('event_detail');
	}






	//参赛指南 列表   /information/competition_guide
	public function competition_guide(){



        //$this->assign('banpic', 'news_txt.png');
        $this->assign('curmenu', '2');
        $this->assign('curmenu_two', '61');
		$this->display('competition_guide');
	}


}
?>