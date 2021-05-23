<?php
class playersAction extends TAction
{
	public $MyModelName = 'players';
	public $ViewAgentNoAuth = 'agent-no-premission';
	
	function MyUrl($action, $param = ''){
		return U($this->MyModelName .'/'. $action, 'competition='.$this->competition . ($param != '' ? '&'.$param : ''));
	}
	
	function Competitions(){
		static $infos = array();
		if( empty($infos) ){
			$infos = include(APP_PATH . 'Conf/data.competition_info.php');
		}
		return $infos;
	}
	function Competition($competition = 0){
		$inf = array();
		$Competitions = $this->Competitions();
		if( $competition > 0 && isset($Competitions[$competition]) && is_array($Competitions[$competition]) ){
			$inf = $Competitions[$competition];
		}else{
			$inf = end($Competitions);
		}
		return $inf;
	}
	function Agents(){
		static $infos = array();
		if( empty($infos) ){
			$model = M('agent');
			$res = $model->where('status = 1 ')->select();
			if( !is_null($res) && $res !== false){
				foreach($res as $rec){
					$infos[ $rec['id'] ] = $rec;//$rec[''];
				}
			}
	    	trace( print_r($res, true), 'AGENTS2');
		}
		return $infos;
	}
	
	var $TheCompetition;
	var $competition;
	
	var $CurrAgentInfo = array();
	
	function AgentProperty($name){
		$value = "";
		if( is_array($this->CurrAgentInfo) && !empty($this->CurrAgentInfo) && isset($this->CurrAgentInfo[$name]) ){
			$value = $this->CurrAgentInfo[$name];
		}
		return $value;
	}
	function AgentGameAuth($type = 'a'){
		$result = true;
    	if( $this->CurrUserRole == 'agent' ){
    		if( $type = 'a' ){
    			if( $this->AgentProperty('r_baoming') != '1' && $this->AgentProperty('r_yusai') != '1'){
    				$result = false;
    			}
    		}else 
    		if( $type = 'b' ){
    			if( $this->AgentProperty('r_fusai') != '1'){
    				$result = false;
    			}
    		}else 
    		if( $type = 'c' ){
    			if( $this->AgentProperty('r_juesai') != '1'){
    				$result = false;
    			}
    		}else 
    		if( $type = 'z' ){
				$result = true;
    		} 
    	}
    	return $result;
	}
	
	
	var $Game_Groups = array();
	var $Game_Survey = array();
	var $Game_Areas = array();
	
	function _initialize(){
		parent::_initialize();
    	$this->assign('CompetitionList', $this->Competitions());
		$competition = isset($_REQUEST['competition']) ? intval($_REQUEST['competition']) : 0;
	   	$this->TheCompetition = $this->Competition($competition);
    	$this->competition = intval($this->TheCompetition['serial']);
    	$this->assign('CompetitionInfo', $this->TheCompetition);
    	$this->assign('competition', $this->competition);
    	
    	if( $this->CurrUserRole == 'agent' ){
	    	$module = D('CompetitionAgent');
	    	$res = $module->relation(true)->where('status = 1 and competition = %d and agent = %d', $this->competition, $this->CurrUserCode)->order('sort asc, id')->select();
    		if( !is_null( $res ) && $res !== false ){
    			$this->CurrAgentInfo = $res[0];
    		}
    	}

		$this->Game_Areas = $this->TheCompetition['area'];
    	$this->assign('Game_Areas',  $this->Game_Areas);
 		$this->Game_Groups = isset($this->TheCompetition['conf_group']) ? $this->TheCompetition['conf_group'] : array();
 		$this->assign('Game_Groups',  $this->Game_Groups);
 		$this->Game_Survey = isset($this->TheCompetition['conf_survey']) ? $this->TheCompetition['conf_survey'] : array();
 		$this->assign('Game_Survey',  $this->Game_Survey);
		
    	/// 如果身份是代理商，查看是否有本届权限
    	$GotoView = "";
    	if( $this->CurrUserRole == 'agent' ){
    		if( $this->AgentProperty('r_baoming') != '1' && $this->AgentProperty('r_yusai') != '1' && $this->AgentProperty('r_fusai') != '1' && $this->AgentProperty('r_juesai') != '1' ){
    			$GotoView = "agent-no-premission";
   			}
    	}
    	if( $GotoView != '' ){
			$this->redirect('players' .'/'. $GotoView);
    		return;
    	}
 		
	}
	
	/**
	 *--------------------------------------------------------------+
	 * Action: index (默认操作)
	 *--------------------------------------------------------------+
	 */
    public function index( $competition = 0 )
    {


		$PageTitle = L('比赛管理');
		$PageMenu = array(
			//array( U('account/create'), L('添加管理员') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
        $this->display('index');
    }


	/**
	 *--------------------------------------------------------------+
	 * Action: competition_area_list
	 * - 查看所有报名名单（当前代理商）
	 * - 设置 报名费收讫状态
	 * - 设置 曲目填写完成状态
	 * - 查看所有有效报名名单（当前代理商）（即：报名费已交，且曲目完成）
	 *--------------------------------------------------------------+
	 */
	 
	public function _player_list_read_filter(){
    	$filter = new stdClass();
    	$filter->area   = $this->REQUEST('_filter_area');
    	$filter->group  = $this->REQUEST('_filter_group');
    	$filter->state  = $this->REQUEST('_filter_state');
    	$filter->city   = $this->REQUEST('_filter_city');
    	
    	$filter->state  = ( $filter->state == '' ) ? 1 : $filter->state; /// 默认显示有效数据
    	
    	$filter->state_aoppiano = $this->REQUEST('_filter_state_aoppiano');
    	$filter->state_payment_a  = $this->REQUEST('_filter_state_payment_a');//预选赛缴费
    	$filter->state_payment_b  = $this->REQUEST('_filter_state_payment_b');//分赛区决赛缴费
    	$filter->state_payment_c  = $this->REQUEST('_filter_state_payment_c');//总决赛赛缴费
    	$filter->state_qualify_a  = $this->REQUEST('_filter_state_qualify_a');//晋级预选赛。不用
    	$filter->state_qualify_b  = $this->REQUEST('_filter_state_qualify_b');//晋级分赛区决赛
    	$filter->state_qualify_c  = $this->REQUEST('_filter_state_qualify_c');//晋级总决赛
    	$filter->_search = $this->REQUEST('_f_search');
    	$filter->_order  = $this->REQUEST('_f_order', 'id');
    	$filter->_direc  = strtoupper($this->REQUEST('_f_direc'));
		
    	$this->assign('filter_state',  $filter->state);
    	$this->assign('filter_area',   $filter->area);
    	$this->assign('filter_group',  $filter->group);
    	$this->assign('filter_city',   $filter->city);
    	$this->assign('filter_state_aoppiano',  $filter->state_aoppiano);
    	$this->assign('filter_state_payment_a', $filter->state_payment_a);
    	$this->assign('filter_state_payment_b', $filter->state_payment_b);
    	$this->assign('filter_state_payment_c', $filter->state_payment_c);
    	$this->assign('filter_state_qualify_a', $filter->state_qualify_a);
    	$this->assign('filter_state_qualify_b', $filter->state_qualify_b);
    	$this->assign('filter_state_qualify_c', $filter->state_qualify_c);
    	$this->assign('f_search',  $filter->_search);
    	$this->assign('f_order',   $filter->_order);
    	$this->assign('f_direc',   $filter->_direc );
    	return $filter;
	}
	 
	public function _player_list($competition = 0, $type = 'a', $ListLimiting = ''){
		load("@.lk_function");
    	/// 如果身份是代理商，查看是否有本届权限
    	if( !$this->AgentGameAuth($type) ){
			$this->redirect($this->MyModelName .'/'. $this->ViewAgentNoAuth);
    		return;
    	}

    	$model = D('CompetitionPlayersView');
    	//trace(''. print_r($model, true) .'', 'CompetitionPlayersView - Fields', 'debug');
    	$sqlWhere = "";		
    	$sqlOrder = "";
    	
    	
//    	$filter_area   = $this->REQUEST('_filter_area');
//    	$filter_group  = $this->REQUEST('_filter_group');
//    	$filter_state  = $this->REQUEST('_filter_state');
//    	$filter_city   = $this->REQUEST('_filter_city');
//    	
//    	$filter_state  = ( $filter_state == '' ) ? 1 : $filter_state; /// 默认显示有效数据
//    	
//    	$filter_state_aoppiano = $this->REQUEST('_filter_state_aoppiano');
//    	$filter_state_payment_a  = $this->REQUEST('_filter_state_payment_a');//预选赛缴费
//    	$filter_state_payment_b  = $this->REQUEST('_filter_state_payment_b');//分赛区决赛缴费
//    	$filter_state_payment_c  = $this->REQUEST('_filter_state_payment_c');//总决赛赛缴费
//    	$filter_state_qualify_a  = $this->REQUEST('_filter_state_qualify_a');//晋级预选赛。不用
//    	$filter_state_qualify_b  = $this->REQUEST('_filter_state_qualify_b');//晋级分赛区决赛
//    	$filter_state_qualify_c  = $this->REQUEST('_filter_state_qualify_c');//晋级总决赛
//    	$f_search = $this->REQUEST('_f_search');
//    	$f_order  = $this->REQUEST('_f_order', 'id');
//    	$f_direc  = strtoupper($this->REQUEST('_f_direc'));
    	
    	$filter = $this->_player_list_read_filter();
		$filter_area   = $filter->area;
    	$filter_group  = $filter->group;
    	$filter_state  = $filter->state;
    	$filter_city   = $filter->city;
    	$filter_state_aoppiano = $filter->state_aoppiano;
    	$filter_state_payment_a  = $filter->state_payment_a;//预选赛缴费
    	$filter_state_payment_b  = $filter->state_payment_b;//分赛区决赛缴费
    	$filter_state_payment_c  = $filter->state_payment_c;//总决赛赛缴费
    	$filter_state_qualify_a  = $filter->state_qualify_a;//晋级预选赛。不用
    	$filter_state_qualify_b  = $filter->state_qualify_b;//晋级分赛区决赛
    	$filter_state_qualify_c  = $filter->state_qualify_c;//晋级总决赛
    	$f_search = $filter->_search;
    	$f_order  = $filter->_order;
    	$f_direc  = $filter->_direc;
    	
    	
    	
    	trace(''. $this->CurrUserRole .'', '$this->CurrUserRole', 'debug');
    	$sqlWhere = " 1 = 1 ";
    	if( $this->CurrUserRole == 'agent' ){
    		
    		if( $type == 'a' ){
    			$sqlWhere .= " and f.game_agent = '". $this->CurrUserCode ."' ";
    		}else
    		if( $type == 'b' ){ ///如果是分赛区决赛，对于有权限的代理商，要看赛区
    			$sqlWhere .= " and f.game_area = '". $this->AgentProperty('area') ."' ";
    		}else
    		if( $type == 'c' ){
    			
    		}else
    		if( $type == 'z' ){
    			
    		}
    	}
    	if( $type == 'a' ){
    		
    	}else 
    	if( $type == 'b' ){
    		$sqlWhere .= " and f.state_b_qualify = 1 ";
    	}else 
    	if( $type == 'c' ){
    		$sqlWhere .= " and f.state_c_qualify = 1 ";
    	}else 
    	if( $type == 'z' ){
    		$sqlWhere .= " and (f.award_level <> 1000) ";
    	}

    	$sqlWhere .= " and f.competition = " . $this->competition;
    	$sqlOrder .= " f.playerid DESC ";
    	
    	///列表过滤. HTML: input name="_filter_xxx", PHP: $filter_xxx
    	if( $filter_state != '' ){
    		if( intval($filter_state) == 1 ){
    			$sqlWhere .= " and (f.status = 1) ";
    		}else{
    			$sqlWhere .= " and (f.status = 0) ";    			
    		}
    	}
    	if( $filter_area != '' ){
    		$sqlWhere .= " and f.game_area = '". $this->fixSQL($filter_area)."' ";
    	}
    	if( $filter_city != '' ){
    		$sqlWhere .= " and f.game_city = '". $this->fixSQL($filter_city)."' ";
    	}
    	if( $filter_group != '' ){
    		$sqlWhere .= " and f.game_group = '". $this->fixSQL($filter_group)."' ";
    	}
    	if( $filter_state_aoppiano != '' ){
    		$sqlWhere .= " and f.aop_piano = ". intval($filter_state_aoppiano)." ";
    	}
    	if( $filter_state_payment_a != '' ){
    		$sqlWhere .= " and f.state_a_payment = ". intval($filter_state_payment_a)." ";
    	}    	
    	if( $filter_state_payment_b != '' ){
    		$sqlWhere .= " and f.state_b_payment = ". intval($filter_state_payment_b)." ";
    	}
    	if( $filter_state_payment_b != '' ){
    		$sqlWhere .= " and f.state_c_payment = ". intval($filter_state_payment_c)." ";
    	}
     	if( $filter_state_qualify_a != '' ){
    		$sqlWhere .= " and f.state_a_qualify = ". intval($filter_state_qualify_a)." ";
    	}
     	if( $filter_state_qualify_b != '' ){
    		$sqlWhere .= " and f.state_b_qualify = ". intval($filter_state_qualify_b)." ";
    	}
     	if( $filter_state_qualify_c != '' ){
    		$sqlWhere .= " and f.state_c_qualify = ". intval($filter_state_qualify_c)." ";
    	}
   	
    	///列表搜索. HTML: input name="_f_search", PHP: $f_search
    	if( $f_search != '' ){
    		$sqlWhere .= " and ( "
    			." f.name like '%". $this->fixSQL($f_search)."%' "
    			." or f.address like '%". $this->fixSQL($f_search)."%'"
    			." or f.mobile like '%". $this->fixSQL($f_search)."%' "
    			." or f.phone like '%". $this->fixSQL($f_search)."%' "
    			." or f.school like '%". $this->fixSQL($f_search)."%' "
    			." or f.piano_school like '%". $this->fixSQL($f_search)."%' "
    			." or f.piano_tutor like '%". $this->fixSQL($f_search)."%' "
    			." )";
    	}
    	///列表排序. HTML: input name="_f_order", PHP: $f_order; HTML: input name="_f_direc" , PHP: $f_direc
    	$fields = D('CompetitionPlayers')->getDbFields();
    	if( in_array($f_order, $fields) ){
    		$sqlOrder = ' '. $f_order . ' ';
    	}else{
    		$sqlOrder = ' playerid  ';
    	}
    	if( $f_direc != 'DESC' ){
    		$sqlOrder .= ' ASC';
    	}else{
    		$sqlOrder .= ' DESC';
    	}
    	//trace($sqlOrder, '', 'debug');
    	///回传过滤条件
//    	$this->assign('filter_state',  $filter_state);
//    	$this->assign('filter_area',   $filter_area);
//    	$this->assign('filter_group',  $filter_group);
//    	$this->assign('filter_city',   $filter_city);
//    	$this->assign('filter_state_aoppiano',  $filter_state_aoppiano);
//    	$this->assign('filter_state_payment_a', $filter_state_payment_a);
//    	$this->assign('filter_state_payment_b', $filter_state_payment_b);
//    	$this->assign('filter_state_payment_c', $filter_state_payment_c);
//    	$this->assign('filter_state_qualify_a', $filter_state_qualify_a);
//    	$this->assign('filter_state_qualify_b', $filter_state_qualify_b);
//    	$this->assign('filter_state_qualify_c', $filter_state_qualify_c);
//    	$this->assign('f_search',  $f_search);
//    	$this->assign('f_order',   $f_order);
//    	$this->assign('f_direc',   $f_direc );

    	///获取列表数据集
    	return $this->GeneralActionForListing('CompetitionPlayersView', $sqlWhere, $sqlOrder, $ListLimiting, 'D');

	}


    public function list_a( $competition = 0 )
    {
		$PageTPL = '';
		$PageTitle = L('预选赛名单');
		$PageMenu = array(
			array( 'javascript:form_print_submit();', L('打印当前名单'), '<i class="icon-print"></i>' ),
			array( 'javascript:form_export_submit();', L('导出当前名单'), '<i class="icon-save"></i>' ),
			//array( '#', '·' ),
			array( $this->MyUrl('list_b'), L('分赛区决赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$PageHeadShow = true;
    	$ListType     = 'a';
		$ListLimiting = '';
		
		
		$model = D('CompetitionAgent');
		$res = $model->field('distinct city')->where("status = 1 and competition = ". $this->competition)->select();
    	$this->assign('Game_Citys',   $res );

    	$pl = $this->_player_list($competition, $ListType, $ListLimiting);
    	$model = $pl['model'];
    	trace(print_r($module->DebugOptions, true), 'Players-Options', 'debug');
    	trace(print_r($module->DebugParseOptions, true), 'Players-Options', 'debug');

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
	}
    public function list_a_print( $competition = 0 )
    {
		$PageTPL = '';
		$PageTitle = L('预选赛名单').L('打印');
		$PageMenu = array(
			array( 'javascript:DooPrintContent();', L('开始打印'), '<i class="icon-print"></i>' ),
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
		);
		$PageHeadShow = true;
    	$ListType     = 'a';
		$ListLimiting = false;

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function list_a_export( $competition = 0 ){

		$PageTPL = '';
		$PageTitle = L('预选赛名单').L('导出');
		$PageMenu = array(
			array( $this->MyUrl('list_b'), L('分赛区决赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$PageHeadShow = false;
    	$ListType     = 'a';
		$ListLimiting = false;
		C('SHOW_PAGE_TRACE', false);

		$model = D('CompetitionAgent');
		$res = $model->field('distinct city')->where("status = 1 and competition = ". $this->competition)->select();
    	$this->assign('Game_Citys',   $res );

		$fields = array(
			'userid'			=> '用户ID',
 			'user_username'		=> '用户名',
			'user_email'		=> '邮箱',
			'playerid'			=> '参赛号',
			'name'				=> '姓名',
			'gender'			=> '性别',
			'birthday'			=> '出生日期',
			'idcno'				=> '身份证号',
			'address'			=> '地址',
			'phone'				=> '电话',
			'fax'				=> '传真',
			'school'			=> '学校',
			'file_idcard'		=> '身份证图片',
			'photo_normal'		=> '近期标准像',
			'photo_artistic'	=> '艺术照',
			'guardian_name'		=> '监护人姓名',
			'guardian_relation'	=> '监护人关系',
			'guardian_phone'	=> '监护人电话',
			'piano_age'			=> '学琴年资',
			'piano_school'		=> '学琴院校',
			'piano_tutor'		=> '导师姓名',
			'piano_tutor_phone'	=> '导师电话',
			'piano_level'		=> '钢琴级别',
			'piano_level_year'	=> '级别年份',
			'music_school'		=> '音乐学院',
			'aop_piano'			=> '预约练琴',
			'aop_piano_brand'	=> '预约品牌',
			'game_area'			=> '赛区',
			'game_prov'			=> '赛区省份',
			'game_city'			=> '赛区城市',
			'agent_name'		=> '代理商',
			'game_group'		=> '参赛组别',
			'state_a_payment'		=> '报名费缴费',
			'state_b_payment'		=> '分赛区决赛缴费',
			'state_b_qualify'		=> '晋级分赛区决赛',
			'state_c_payment'		=> '总决赛缴费',
			'state_c_qualify'		=> '晋级总决赛',
			'award_rank'		=> '获奖级别',
			'create_time'		=> '报名时间',
			'track_a1'			=> '预选赛曲目1',
			'track_a2'			=> '预选赛曲目2',
			'track_a3'			=> '预选赛曲目3',
			'track_a4'			=> '预选赛曲目4',
			'track_b1'			=> '分赛区决赛曲目1',
			'track_b2'			=> '分赛区决赛曲目2',
			'track_b3'			=> '分赛区决赛曲目3',
			'track_b4'			=> '分赛区决赛曲目4',
			'track_c1'			=> '总决赛曲目1',
			'track_c2'			=> '总决赛曲目2',
			'track_c3'			=> '总决赛曲目3',
			'track_c4'			=> '总决赛曲目4'
			);
		$special = array(
			'gender' => array('1' => '男', '0' => '女' ),
			'aop_piano' => array('1' => '预约', '0' => '未预约' ),
			'state_a_payment'		=> array('1' => '已缴', '0' => '未缴费' ),
			'state_b_payment'		=> array('1' => '已缴', '0' => '未缴费' ),
			'state_b_qualify'		=> array('1' => '晋级', '0' => '未晋级' ),
			'state_c_payment'		=> array('1' => '已缴', '0' => '未缴费' ),
			'state_c_qualify'		=> array('1' => '晋级', '0' => '未晋级' ),
			'file_idcard'		=> 'url',
			'photo_normal'		=> 'url',
			'photo_artistic'	=> 'url',
			'create_time'		=> 'date'
			);

    	$this->assign('Excel_Fields',   $fields );

		
		$_action = (isset($_REQUEST['_action']) ? $_REQUEST['_action']: "");
		
		if( $_action == "" ){
			$PageHeadShow = true;
			$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
    		$filter = $this->_player_list_read_filter();
			
			
			
	        $this->display('list_a_export-setup');

		}
		
		if( $_action == 'export' ){
		
		
		$excel_fields = (isset($_REQUEST['excel_fields']) ? $_REQUEST['excel_fields']: array());
		$excel_fields = empty($excel_fields) ? $fields : $excel_fields;
		
		
		$ExcelTitle = '预选赛报名名单';
//		header("Content-type:application/vnd.ms-excel");  
//		header("Content-Disposition:attachment;filename=stu_report_".date("Y-m-d").".xls");
//		header("Pragma: no-cache");
//		header("Expires: 0");

    	$datas = $this->_player_list($competition, $ListType, $ListLimiting);

//		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
//        $this->display($PageTPL);

    	$players = $datas['dataset'];
    	
    	Vendor('PHPExcel/PHPExcel', APP_PATH.'../Library', '.php');
    	
    	
    	$excel = new PHPExcel();
		$excel->getProperties()->setCreator("Lookwebs.com")
			->setLastModifiedBy("Danny")
			->setTitle($this->CompetitionInfo['title'])
			->setSubject($ExcelTitle)
			->setDescription($this->CompetitionInfo['title'] .' '. $this->CompetitionInfo['subtitle'] .' '.$ExcelTitle )
			->setKeywords("steinway competition 名单")
			->setCategory("steinway-competition-players");

    	$excel->setActiveSheetIndex(0);

		$excel->getActiveSheet()->setTitle('Players');

		$excel->getActiveSheet()->getStyle('A1')->getFont()->setName('MicrosoftYahei');
		$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
		$excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setName('MicrosoftYahei');
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('A3')->getFont()->setName('MicrosoftYahei');
		$excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
		$excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);

		$excel->getActiveSheet()->setCellValue('A1', $this->CompetitionInfo['title'] );
		$excel->getActiveSheet()->setCellValue('A2', $this->CompetitionInfo['subtitle'] );
		$excel->getActiveSheet()->setCellValue('A3', $ExcelTitle );
//	　　$excel->getActiveSheet()->mergeCells('A1:Z1');
//	　　$excel->getActiveSheet()->mergeCells('A2:Z2');
//	　　$excel->getActiveSheet()->mergeCells('A2:Z2');

    	//SELECT f.playerid AS playerid,f.competition AS competition,f.userid AS userid,f.name AS name,f.gender AS gender,f.birthday AS birthday,f.idcno AS idcno,f.address AS address,f.mobile AS mobile,f.phone AS phone,f.fax AS fax,f.school AS school,f.file_idcard AS file_idcard,f.photo_normal AS photo_normal,f.photo_artistic AS photo_artistic,f.game_area AS game_area,f.game_prov AS game_prov,f.game_city AS game_city,f.game_group AS game_group,f.game_agent AS game_agent,f.piano_age AS piano_age,f.piano_school AS piano_school,f.piano_tutor AS piano_tutor,f.piano_tutor_phone AS piano_tutor_phone,f.music_school AS music_school,f.piano_level AS piano_level,f.piano_level_year AS piano_level_year,f.aop_piano AS aop_piano,f.aop_piano_brand AS aop_piano_brand,f.guardian_name AS guardian_name,f.guardian_relation AS guardian_relation,f.guardian_phone AS guardian_phone,f.track_state AS track_state,f.track_a1 AS track_a1,f.track_a2 AS track_a2,f.track_a3 AS track_a3,f.track_a4 AS track_a4,f.track_a5 AS track_a5,f.track_b1 AS track_b1,f.track_b2 AS track_b2,f.track_b3 AS track_b3,f.track_b4 AS track_b4,f.track_b5 AS track_b5,f.track_c1 AS track_c1,f.track_c2 AS track_c2,f.track_c3 AS track_c3,f.track_c4 AS track_c4,f.track_c5 AS track_c5,f.track_c6 AS track_c6,f.track_c7 AS track_c7,f.track_c8 AS track_c8,f.track_c9 AS track_c9,f.track_c10 AS track_c10,f.state_a_payment AS state_a_payment,f.state_b_payment AS state_b_payment,f.state_c_payment AS state_c_payment,f.state_a_qualify AS state_a_qualify,f.state_b_qualify AS state_b_qualify,f.state_c_qualify AS state_c_qualify,f.award_level AS award_level,f.award_caption AS award_caption,f.award_rank AS award_rank,f.create_time AS create_time,f.modify_time AS modify_time,
    	//User.username AS user_username,User.email AS user_email,User.realname AS user_realname,User.gender AS user_gender,User.mobile AS user_mobile,User.telphone AS user_telphone,User.idcno AS user_idcno,User.prov AS user_prov,User.city AS user_city,User.address AS user_address,User.status AS user_status,
    	//Agent.username AS agent_username,Agent.prov AS agent_prov,Agent.city AS agent_city,Agent.agent_name AS agent_name,Agent.address AS agent_address,Agent.email AS agent_email,Agent.phone AS agent_phone,Agent.longitude AS agent_longitude,Agent.latitude AS agent_latitude,Agent.status AS agent_status 
    	//FROM sway_competition_players f LEFT JOIN sway_user User ON User.id = f.userid LEFT JOIN sway_agent Agent ON Agent.id = f.game_agent WHERE (  f.competition = 6 and f.playerid = 1 ) LIMIT 1   [ RunTime:0.000971s ]
		//用户ID	用户名	邮箱	参赛号	姓名	性别	出生日期	身份证号	地址	电话	传真	学校	身份证图片	近期标准像	艺术照	监护人姓名	监护人关系	监护人电话	赛区	省份	城市	代理商	学琴年资	学琴院校	导师姓名	导师电话	钢琴级别	级别年份	音乐学院	预约练琴	预约品牌	参赛组别	预选赛曲目1	预选赛曲目2	预选赛曲目3	预选赛曲目4

		$Col1 = array();
		
    	
    	$field_keys = array_keys($fields);
		
//		$excel->getActiveSheet()->setCellValue('A1', count($players) );
//		$excel->getActiveSheet()->setCellValue('A2', print_r($players, true) );
		$startRow = 5;
		$indexCol = 0;
		$indexRow = $startRow;

		///foreach($fields as $filed => $title){
		foreach($excel_fields as $field){
			///if( !in_array($field, $excel_fields) ) continue;
			$title = in_array($field, $field_keys) ? $fields[$field] : '[Unknow-'.$field.']';
			//$excel->getActiveSheet()->setCellValue(
			$excel->getActiveSheet()->setCellValueByColumnAndRow( $indexCol, $indexRow, $title );
			$indexCol++;
		}
		$indexCol = 0;
		$indexRow++;
		foreach($players as $no => $record){
			///foreach($fields as $field => $title){
			foreach($excel_fields as $field){
				///if( !in_array($field, $excel_fields) ) continue;
				$title = in_array($field, $field_keys) ? $fields[$field] : '[N/A]';
				$text = $record[$field];
				if( isset($special[$field]) ){
					if( is_array( $special[$field] )){
						$text = $special[$field][ $record[$field] ];
					}else 
					if( $special[$field] == 'date' ){
						$text = date('Y-m-d', $record[$field]);
					}else 
					if( $special[$field] == 'url' ){
						$text = $record[$field];
						if( $text != '' && substr($text, 0, 7) != 'http://' ){
							$text = CGIWWW_URI . $text;
						}
					}
				}
				$excel->getActiveSheet()->setCellValueExplicitByColumnAndRow( $indexCol, $indexRow, $text );
				$indexCol++;
			}
			$indexCol = 0;
			$indexRow++;
		}
		

		$excel->getActiveSheet()->getStyle($startRow)->getFont()->setName('MicrosoftYahei');
		$excel->getActiveSheet()->getStyle($startRow)->getFont()->setBold(true);
		
		$excel->getActiveSheet()->freezePane('F'.($startRow + 1));
		
		$fname = 'steinway-competition-'. $this->competition .'-players-'. date('Ymd-His', time('now')) .'.xlsx';

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$fname.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->save('php://output');
		
		
		}///end export action

//
//		// Redirect output to a client’s web browser (Excel5)
//		header('Content-Type: application/vnd.ms-excel');
//		header('Content-Disposition: attachment;filename="'.$fname.'"');
//		header('Cache-Control: max-age=0');
//		// If you're serving to IE 9, then the following may be needed
//		header('Cache-Control: max-age=1');
//		// If you're serving to IE over SSL, then the following may be needed
//		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
//		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//		header ('Pragma: public'); // HTTP/1.0
//		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
//		$objWriter->save('php://output');
//


    }
    
    public function _ajax_player_state($player = 'id', $fldState = 'status', $type = 'a' ){
    	/// 如果身份是代理商，查看是否有本届权限
    	if( !$this->AgentGameAuth($type) ){
			$this->ajaxReturn(array('status' => 'false', 'message' => '500'));
    		return;
    	}
    	$playerid = intval($this->REQUEST($player));
    	if( $playerid < 1 ){
			$this->ajaxReturn(array('status' => 'false', 'message' => '400'));
			return;
    	}
    	$model = D('CompetitionPlayers');
    	$model = D('CompetitionPlayers');
    	$sqlWhere  = " playerid = ". $playerid;
    	$sqlWhere .= " and competition = " . $this->competition;
    	$sqlOrder = "";
    	if( $this->CurrUserRole == 'agent' ){
    		if( $type == 'a' ){
    			$sqlWhere .= " and game_agent = '". $this->CurrUserCode ."' ";
    		}else
    		if( $type == 'b' ){ ///如果取是分赛区决赛，对于有权限的代理商，要看赛区
    			$sqlWhere .= " and f.game_area = '". $this->AgentProperty('area') ."' ";
    		}else
    		if( $type == 'c' ){ ///如果取是总决赛，对于有权限的代理商，无权
    			$sqlWhere .= ' and 1 = 0 ';
    		}else
    		if( $type == 'z' ){
    			
    		}
    	}
    	///获取列表数据集
    	$record = $model->where($sqlWhere)->select();
    	if( is_null($record) || $record === false || empty($record) || !is_array($record[0]) ){
			$this->ajaxReturn(array('status' => 'false', 'message' => '404'));
			return;
    	}
		$result = $model->execute( sprintf("UPDATE %s SET $fldState=($fldState+1)%%2 where playerid=%d", $model->getTableName(), $playerid) );
    	if( $result === false ){
			$this->ajaxReturn(array('status' => 'false', 'message' => '501'));
			return;
    	}
		$values = $model->where('playerid=%d', $playerid)->find();
		if( is_null($values) || $values === false ){
			$this->ajaxReturn(array('status' => 'false', 'message' => '900'));
		}else{
			$this->ajaxReturn(array('status' => 'true', 'result' => $values[$fldState]));
		}
    }
    public function ajax_list_a_status( $competition = 0 ){
    	$this->_ajax_player_state('id', 'status', 'a');
    }

    public function ajax_list_a_payment( $competition = 0 ){
    	$this->_ajax_player_state('id', 'state_a_payment', 'a');
//    	$playerid = intval($this->REQUEST('id'));
//    	if( $playerid < 1 ){
//			$this->ajaxReturn(array('status' => 'false', 'message' => '400'));
//			return;
//    	}
//    	$model = D('CompetitionPlayers');
//    	$sqlWhere = "playerid = ". $playerid;
//    	$sqlWhere .= " and competition = " . $this->competition;
//    	$sqlOrder = "";
//    	if( $this->CurrUserRole == 'agent' ){
////    		if( $type == 'a' ){
//    			$sqlWhere .= " and game_agent = '". $this->CurrUserCode ."' ";
////    		}else
////    		if( $type == 'b' ){ ///如果是分赛区决赛，对于有权限的代理商，要看赛区
////    			$sqlWhere .= " and f.game_area = '". $this->AgentProperty('area') ."' ";
////    		}else
////    		if( $type == 'c' ){
////    			
////    		}else
////    		if( $type == 'z' ){
////    			
////    		}
//    	}
//    	///获取列表数据集
//    	$record = $model->where($sqlWhere)->select();
//    	if( is_null($record) || $record === false || empty($record) || !is_array($record[0]) ){
//			$this->ajaxReturn(array('status' => 'false', 'message' => '404'));
//			return;
//    	}
//		$result = $model->execute( sprintf("UPDATE %s SET state_a_payment=(state_a_payment+1)%%2 where playerid=%d", $model->getTableName(), $playerid) );
//    	if( $result === false ){
//			$this->ajaxReturn(array('status' => 'false', 'message' => '501'));
//			return;
//    	}
//		$values = $model->where('playerid=%d', $playerid)->find();
//		if( is_null($values) || $values === false ){
//			$this->ajaxReturn(array('status' => 'false', 'message' => '900'));
//		}else{
//			$this->ajaxReturn(array('status' => 'true', 'result' => $values['state_a_payment']));
//		}
    }

    public function list_b( $competition = 0 )
    {
		$PageTPL = '';
		$PageTitle = L('分赛区决赛名单');
		$PageMenu = array(
			array( 'javascript:form_print_submit();', L('打印当前名单'), '<i class="icon-print"></i>' ),
			array( 'javascript:form_export_submit();', L('导出当前名单'), '<i class="icon-save"></i>' ),
			//array( '#', '·' ),
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$PageHeadShow = true;
    	$ListType     = 'b';
		$ListLimiting = '';

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function list_b_print( $competition = 0 )
    {
		$PageTPL = '';
		$PageTitle = L('分赛区决赛名单').L('打印');
		$PageMenu = array(
			array( 'javascript:DooPrintContent();', L('开始打印'), '<i class="icon-print"></i>' ),
			array( $this->MyUrl('list_a'), L('分赛区决赛名单') ),
		);
		$PageHeadShow = true;
    	$ListType     = 'b';
		$ListLimiting = false;

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function list_b_export( $competition = 0 ){

		$PageTPL = '';
		$PageTitle = L('分赛区决赛名单').L('导出');
		$PageMenu = array(
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$PageHeadShow = false;
    	$ListType     = 'b';
		$ListLimiting = false;
		C('SHOW_PAGE_TRACE', false);
		header("Content-type:application/vnd.ms-excel");  
		header("Content-Disposition:attachment;filename=stu_report_".date("Y-m-d").".xls");
		header("Pragma: no-cache");
		header("Expires: 0");

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function ajax_list_b_payment( $competition = 0 ){
    	$this->_ajax_player_state('id', 'state_b_payment', 'b');
    }
    public function ajax_list_b_qualify( $competition = 0 ){
    	$this->_ajax_player_state('id', 'state_b_qualify', 'a');
    }

    public function list_c( $competition = 0 )
    {
		$PageTPL = '';
		$PageTitle = L('分赛区决赛名单');
		$PageMenu = array(
			array( 'javascript:form_print_submit();', L('打印当前名单'), '<i class="icon-print"></i>' ),
			array( 'javascript:form_export_submit();', L('导出当前名单'), '<i class="icon-save"></i>' ),
			//array( '#', '·' ),
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$PageHeadShow = true;
    	$ListType     = 'c';
		$ListLimiting = '';

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function list_c_print( $competition = 0 )
    {
		$PageTPL = '';
		$PageTitle = L('分赛区决赛名单').L('打印');
		$PageMenu = array(
			array( 'javascript:DooPrintContent();', L('开始打印'), '<i class="icon-print"></i>' ),
			array( $this->MyUrl('list_a'), L('分赛区决赛名单') ),
		);
		$PageHeadShow = true;
    	$ListType     = 'c';
		$ListLimiting = false;

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function list_c_export( $competition = 0 ){

		$PageTPL = '';
		$PageTitle = L('分赛区决赛名单').L('导出');
		$PageMenu = array(
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$PageHeadShow = false;
    	$ListType     = 'c';
		$ListLimiting = false;
		
		C('SHOW_PAGE_TRACE', false);
		header("Content-type:application/vnd.ms-excel");  
		header("Content-Disposition:attachment;filename=stu_report_".date("Y-m-d").".xls");
		header("Pragma: no-cache");
		header("Expires: 0");

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function ajax_list_c_payment( $competition = 0 ){
    	$this->_ajax_player_state('id', 'state_c_payment', 'c');
    }
    public function ajax_list_c_qualify( $competition = 0 ){
    	$this->_ajax_player_state('id', 'state_c_qualify', 'b');
    }

    public function list_z( $competition = 0 )
    {
		$PageTPL = '';
		$PageTitle = L('获奖名单');
		$PageMenu = array(
			array( 'javascript:form_print_submit();', L('打印当前名单'), '<i class="icon-print"></i>' ),
			array( 'javascript:form_export_submit();', L('导出当前名单'), '<i class="icon-save"></i>' ),
			//array( '#', '·' ),
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
			array( $this->MyUrl('list_b'), L('分赛区决赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$PageHeadShow = true;
    	$ListType     = 'z';
		$ListLimiting = '';

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function list_z_print( $competition = 0 )
    {
		$PageTPL = '';
		$PageTitle = L('获奖名单').L('打印');
		$PageMenu = array(
			array( 'javascript:DooPrintContent();', L('开始打印'), '<i class="icon-print"></i>' ),
			array( $this->MyUrl('list_a'), L('分赛区决赛名单') ),
		);
		$PageHeadShow = true;
    	$ListType     = 'z';
		$ListLimiting = false;

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }
    public function list_z_export( $competition = 0 ){

		$PageTPL = '';
		$PageTitle = L('获奖名单').L('导出');
		$PageMenu = array(
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
			array( $this->MyUrl('list_b'), L('分赛区决赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$PageHeadShow = false;
    	$ListType     = 'z';
		$ListLimiting = false;
		
		C('SHOW_PAGE_TRACE', false);
		header("Content-type:application/vnd.ms-excel");  
		header("Content-Disposition:attachment;filename=stu_report_".date("Y-m-d").".xls");
		header("Pragma: no-cache");
		header("Expires: 0");

    	$this->_player_list($competition, $ListType, $ListLimiting);

		$this->setPagething( $PageTitle, $PageMenu, $PageHeadShow);
        $this->display($PageTPL);
    }


	/**
	 *--------------------------------------------------------------+
	 * Action: competition_players_a_add
	 *--------------------------------------------------------------+
	 */
    public function player_add( $competition = 0 )
    {
    	/// 如果身份是代理商，查看是否有本届权限
    	if( !$this->AgentGameAuth('a') ){
			$this->ajaxReturn(array('status' => 'false', 'message' => '500'));
    		return;
    	}
    	
// 		$Game_Groups = isset($this->TheCompetition['conf_group']) ? $this->TheCompetition['conf_group'] : array();
// 		$this->assign('Game_Groups',  $Game_Groups);
// 		$Game_Survey = isset($this->TheCompetition['conf_survey']) ? $this->TheCompetition['conf_survey'] : array();
// 		$this->assign('Game_Survey',  $Game_Survey);
    	
		if(isset($_POST['dosubmit'])){
			
			$model = D('CompetitionPlayers');
			///取填写用户ID，查询是否已存在
			$userid = intval($_REQUEST['userid']);
			if( $userid > 0 ){
				$sqlWhere = "competition = ". $this->competition ." and userid = ". $userid;
				$res = $model->where($sqlWhere)->select();
				if( $res === false ){
					$this->error(L('数据库连接失败，请稍后重试！'));
					return;
				}else if( !is_null($res) ){
					foreach($res as $rec){
					$upid = $rec['playerid'];
					if( $rec['status'] == '1' ){
						$this->error(L('用户ID').'('. $userid .')'.L('已有报名信息').','.L('参赛号').'('. $upid .')');
						return;
					}
					}
				}
			}else{
				$this->error(L('必须输入用户账号的用户ID'));
				return;
			}
			///提交用户信息
			///数据校验
			$model->create();
			$result = $model->add();
			if($result){
				///提交用户Survey信息
				$PlayerSurvey = array();
				foreach($this->Game_Survey as $code => $survey){
					$data = array();
					$data['playerid'] = $result;
					$data['userid'] = $userid;
					$data['competition'] = $this->competition;
					$data['survey_code'] = $code;
					$data['survey_value'] = $_REQUEST['survey_'.$code];
					$PlayerSurvey[] = $data;
				}
				$res = D('CompetitionPlayerSurvey')->addAll($PlayerSurvey, array(), true);
				
				$this->success('操作成功！');
			}else{
				$this->error(L('operation_failure'));
			}
			
		}else{
    	
    	/// 从 competition_agent 表中，取 省、市、代理商 数组
    	
    	$sqlWhere = "f.status = 1 and f.r_baoming = 1 and f.competition = ". $this->competition;
    	//$module = D('CompetitionAgent');
    	//$res = $module->relation(true)->where($sqlWhere)->order('sort asc, id')->select();
    	$module = D('CompetitionAgentView');
    	$res = $module->where($sqlWhere)->order('sort asc, id')->select();
    	$Data_Agents = array();
    	if( !is_null($res) && $res !== false ){
    		foreach($res as $rec){
    			$Data_Agents[ $rec['prov'] ][ $rec['city'] ][] = $rec;
    		}
    	}
 		$this->assign('Data_Agents',   $Data_Agents );
 		
 		$tplGameTrackView = 'inc/player_add_track_'. $this->competition;
 		$this->assign('tplGameTrackView',   $tplGameTrackView );
   		
   		$record = array();
 		
    	if( $this->CurrUserRole == 'agent' ){
    		
    		$record['game_prov'] = $this->CurrAccount['User']['prov'];
    		$record['game_city'] = $this->CurrAccount['User']['city'];
    		$record['game_agent'] = $this->CurrAccount['User']['id'];
    		
    	}
 		$this->assign('record',   $record );
    	trace(($this->CurrUserRole), 'this->CurrUserRole', 'debug');
    	trace(print_r($record, true), 'record', 'debug');
    	trace(print_r($this->CurrAccount, true), '$this->Account', 'debug');


   		
   		
   		
		$PageTitle = L('添加报名信息');
		$PageMenu = array(
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
			array( $this->MyUrl('list_b'), L('分赛区决赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
        
        
        }
    }

	/**
	 *--------------------------------------------------------------+
	 * Action: competition_players_a_add
	 *--------------------------------------------------------------+
	 */
    public function player_edit( $competition = 0 )
    {
    	/// 如果身份是代理商，查看是否有本届权限
    	if( !$this->AgentGameAuth('a') ){
			$this->ajaxReturn(array('status' => 'false', 'message' => '500'));
    		return;
    	}
    	$playerid = intval($this->REQUEST('id'));
    	if( $playerid < 1 ){
			$this->error(L('参赛者信息缺失！'), '', 30);
			return;
    	}
    	
		if(isset($_POST['dosubmit'])){
			$model = D('CompetitionPlayers');
			///取填写用户ID，查询是否已存在
			$userid = intval($_REQUEST['userid']);
			if( $userid > 0 ){
				$sqlWhere = "competition = ". $this->competition ." and userid = ". $userid;// ." and playerid = ". $playerid;
				$res = $model->where($sqlWhere)->select();
				if( $res === false ){
					$this->error(L('数据库连接失败，请稍后重试！'));
					return;
//				}else if(is_null($res)){
//					$this->error(L('用户ID').'('. $userid .')'.L('报名信息').'('.$playerid.')'.L('没找到'));
//					return;
//				}else if( is_null($res) || $res == false ){
				}else if(!is_null($res)){
					foreach( $res as $rec ){
						$upid = $rec['playerid'];
						if( $upid != $playerid ){
							///是否要判断状态泥。。。。
							if( $rec['status'] == '1' ){
							$this->error(L('用户ID').'('. $id .')'.L('已有报名信息').','.L('参赛号').'('. $upid .')');
							return;
							}
						}
					}
					
//					$upid = $res[0]['playerid'];
//					if( $upid == $playerid ){
//						$this->error(L('用户ID').'('. $id .')'.L('已有报名信息'));
//						return;
//					}
				}
			}else{
				$this->error(L('必须输入用户账号的用户ID'));
				return;
			}
			///提交用户信息
			///数据校验
			$_POST['playerid'] = $playerid;
			$model = D('CompetitionPlayers');
			if( !$model->create() ){
				$this->error(L('建立数据失败'));
			}
			$result = $model->where('playerid='.$playerid)->save();
			if($result){
				///提交用户Survey信息
				trace(print_r($this->Game_Survey, true), 'S::', 'debug');
				$PlayerSurvey = array();
				foreach($this->Game_Survey as $code => $survey){
					$where = array(); $data = array();
					$where['playerid'] = $playerid;
					$where['competition'] = $this->competition;
					$where['userid'] = $userid;
					
					$data['playerid'] = $playerid;
					$data['competition'] = $this->competition;
					$data['userid'] = $userid;
					$data['survey_code'] = $code;
					$data['survey_value'] = $_REQUEST['survey_'.$code];
					//$PlayerSurvey[] = $data;
					D('CompetitionPlayerSurvey')->data($data)->where($where)->add('', array(), true);
					trace('S:: '. print_r($data, true), '', 'debug');
				}
				//$res = D('CompetitionPlayerSurvey')->addAll($PlayerSurvey, array(), true);
				
				$this->success('操作成功！', U('players/player_edit', 'id='.$playerid), 60);
			}else{
				trace('Save Error: '.$model->error . ' ['.$model->getLastSql('CompetitionPlayers').'] ', 'debug');
				$this->error(L('operation_failure'), '', 60);
			}
			
		}else{
    	
    	/// 从 competition_agent 表中，取 省、市、代理商 数组
    	
    	$sqlWhere = "f.status = 1 and f.r_baoming = 1 and f.competition = ". $this->competition;
    	$module = D('CompetitionAgentView');
    	$res = $module->where($sqlWhere)->order('sort asc, id')->select();
    	$Data_Agents = array();
    	if( !is_null($res) && $res !== false ){
    		foreach($res as $rec){
    			$Data_Agents[ $rec['prov'] ][ $rec['city'] ][] = $rec;
    		}
    	}
 		$this->assign('Data_Agents',   $Data_Agents );
 		
 		$tplGameTrackView = 'inc/player_add_track_'. $this->competition;
 		$this->assign('tplGameTrackView',   $tplGameTrackView );
   		
   		$record = array();
 		
 		$sqlWhere = " f.competition = ". $this->competition ." and f.playerid = ". $playerid;
    	if( $this->CurrUserRole == 'agent' ){
    		if( $this->AgentGameAuth('b') ){
    			$sqlWhere .= " and ( f.game_area = '". $this->AgentProperty('area') ."' ";
    			$sqlWhere .= " or f.game_agent = '". $this->CurrUserCode ."' ) ";
    		}else 
    		if( $this->AgentGameAuth('a') ){
    			$sqlWhere .= " and f.game_agent = '". $this->CurrUserCode ."' ";
    		}
    	}
     	$model = D('CompetitionPlayersView');
     	$record = $model->where($sqlWhere)->find();
    	if( is_null($record) || $record == false ){
			$this->error(L('没有参赛者信息！'), '', 30);
			return;
    	}
//    	if( $this->CurrUserRole == 'agent' ){
//    		$record['game_prov'] = $this->CurrAccount['User']['prov'];
//    		$record['game_city'] = $this->CurrAccount['User']['city'];
//    		$record['game_agent'] = $this->CurrAccount['User']['id'];
//    	}
 		$this->assign('record',   $record );
    	trace(($this->CurrUserRole), 'this->CurrUserRole', 'debug');
    	trace(print_r($record, true), 'record', 'debug');
    	trace(print_r($this->CurrAccount, true), '$this->Account', 'debug');
   		
   		$model = D('CompetitionPlayerSurvey');
 		$sqlWhere = " competition = ". $this->competition ." and playerid = ". $playerid;
 		$survey = $model->where($sqlWhere)->select();
 		$this->assign('survey',   $survey );
   		
		$PageTitle = L('修改报名信息');
		$PageMenu = array(
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
			array( $this->MyUrl('list_b'), L('分赛区决赛名单') ),
			array( $this->MyUrl('list_c'), L('总决赛名单') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
        
        
        }
    }

    public function player_print( $competition = 0, $type = 'a' )
    {
		load("@.lk_function");
    	$GotoView = "";
    	if( $this->CurrUserRole == 'agent' ){
    		if( $this->AgentProperty('r_baoming') != '1' ){
    			$GotoView = "agent-no-premission";
   			}
    	}
    	if( $GotoView != '' ){
     		//$this->display($ViewTPL);
			$this->redirect($this->MyModelName .'/'. $GotoView);
    		return;
    	}
    	
    	
    	$playerid = intval($this->REQUEST('id'));
    	if( $playerid < 1 ){
			$this->error(L('参赛者信息缺失！'), '', 30);
			return;
    	}
    	$model = D('CompetitionPlayersView');

    	$sqlWhere = "f.playerid = ". $playerid;
    	$sqlWhere .= " and f.competition = " . $this->competition;
    	$sqlOrder = "";
    	
    	///获取列表数据集
    	$record = $model->where($sqlWhere)->select();
    	if( is_null($record) || $record === false || empty($record) || !is_array($record[0]) ){
			$this->error(L('参赛者信息不存在！'), '', 30);
			return;
    	}
    	$player = $record[0];
    	
    	///每届的特别设置
    	if($this->competition == 6){
    		$t = $player['game_group'];
    		$player['Flag_IS_PRO'] = ( strpos($t, '专业') !== false ) ? "yes" : "no";
    	}
    	
    	$this->assign('record', $player);
    	//trace(print_r($record, true), 'PLAYER', 'debug');

//		$Game_Survey = isset($this->TheCompetition['conf_survey']) ? $this->TheCompetition['conf_survey'] : array() ;
//		$this->assign('Game_Survey', $Game_Survey);
		
		$PlayerSurvey = array();
		$survey = D('CompetitionPlayerSurvey');
		$res = $survey->where("competition = ". $this->competition . " and playerid = ". $playerid)->select();
    	trace(print_r($res, true), 'res', 'debug');
    	if( !is_null($res) && $res !== false && !empty($res) ){
    		foreach($res as $rec){
    			$c = $rec['survey_code'];
    			$t = '';
    			if( isset( $this->Game_Survey[$c] ) ){
    				$t = $this->Game_Survey[$c]['question'];
    			}
				$rec['survey_name'] = $t;
				$PlayerSurvey[] = $rec;
    		}
    	}
		$this->assign('PlayerSurvey', $PlayerSurvey);
    	trace(print_r($PlayerSurvey, true), 'Survey', 'debug');

 		$tplPlayerPrintTrackView = 'inc/player_print_track_'. $this->competition;
 		$this->assign('tplPlayerPrintTrackView',   $tplPlayerPrintTrackView );

		$PageTitle = L('参赛者信息打印');
		$PageMenu = array(
			array( 'javascript:DooPrintContent();', L('开始打印'), '<i class="icon-print"></i>' ),
			array( $this->MyUrl('list_a'), L('预选赛名单') ),
		);
		$this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }



	public function followup($competition = 0){
 		$this->Game_FQuestion = isset($this->TheCompetition['conf_followup']) ? $this->TheCompetition['conf_followup'] : array();
 		$this->assign('Game_FQuestion',  $this->Game_FQuestion);

		load("@.lk_function");
    	$GotoView = "";
    	if( $this->CurrUserRole == 'agent' ){
    		if( $this->AgentProperty('r_baoming') != '1' ){
    			$GotoView = "agent-no-premission";
   			}
    	}
    	if( $GotoView != '' ){
			$this->redirect($this->MyModelName .'/'. $GotoView);
    		return;
    	}
    	
    	$userid = intval($this->REQUEST('user'));
    	$playerid = intval($this->REQUEST('id'));
    	if( $playerid < 1 ){
			$this->error(L('参赛者信息缺失！'), '', 30);
			return;
    	}
    	$model = D('CompetitionPlayersView');

    	$sqlWhere = "f.playerid = ". $playerid;
    	$sqlWhere .= " and f.competition = " . $this->competition;
    	$sqlOrder = "";
    	
    	///获取列表数据集
    	$record = $model->where($sqlWhere)->select();
    	if( is_null($record) || $record === false || empty($record) || !is_array($record[0]) ){
			$this->error(L('参赛者信息不存在！'), '', 30);
			return;
    	}
    	$player = $record[0];
    	
    	///每届的特别设置
    	if($this->competition == 6){
    		$t = $player['game_group'];
    		$player['Flag_IS_PRO'] = ( strpos($t, '专业') !== false ) ? "yes" : "no";
    	}
    	
    	$this->assign('record', $player);

		if(isset($_POST['dosubmit'])){

	    	$userid = $player['userid'];
			$PlayerSurvey = array();
			foreach($this->Game_FQuestion as $code => $survey){
				$where = array(); $data = array();
				$where['playerid'] = $playerid;
				$where['competition'] = $this->competition;
				$where['userid'] = $userid;
				$where['fquestion_code'] = $code;
				
				$data['playerid'] = $playerid;
				$data['competition'] = $this->competition;
				$data['userid'] = $userid;
				$data['fquestion_code'] = $code;
				$data['fquestion_value'] = $_REQUEST['fquestion_'.$code];
				//$PlayerSurvey[] = $data;
				D('CompetitionPlayerFquestion')->data($data)->where($where)->add('', array(), true);
				//trace('S:: '. print_r($data, true), '', 'debug');
			}

			$this->success('操作成功！', U('players/followup', 'id='.$playerid."&competition = ". $this->competition), 6);

			
		}else{

		$follow = D('CompetitionPlayerFquestion');
    	$sqlWhere = "playerid = ". $playerid;
    	$sqlWhere .= " and competition = " . $this->competition;
    	$sqlOrder = "";
    	$record = $follow->where($sqlWhere)->select();
    	if( is_null($record) || $record === false || empty($record) || !is_array($record[0]) ){
			//$this->error(L('参赛者信息不存在！'), '', 30);
			$record = array();
			//return;
    	}
    	//$follow = $record;
    	$this->assign('fquestion', $record);

        $this->display();
        
        }
	}
}
?>