<?php
/**
 * 简单用户系统演示
 * * 单表（账号信息、个人信息合一）
 *
 */
class festivalphotoAction extends TAction
{
	
	/**
	 *--------------------------------------------------------------+
	 * Action: index 
	 *--------------------------------------------------------------+
	 */
//    public function index()
//    {
//    	$this->assign('DefaultPage', U('public/main'));
//        $this->display('index');
//    }



    /**
     *--------------------------------------------------------------+
     * Action: 一级分类列表
     *--------------------------------------------------------------+
     */
    public function listing_classfather()
    {

        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='删除'){
            if( empty($_POST['id']) ){
                $this->error(L('请选择要删除的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }

            //删除连带数据

            //删除自身数据
            $NewsMod = M('festival_photo_class');
            $sql=" id in (".$in.") ";
            $NewsMod->where($sql)->delete();

            $this->success('删除成功', U('festivalphoto/listing_classfather'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC";

        $sqlWhere .= " and parent_id=0 ";

        $filter_state = $this->REQUEST('_filter_state');
        if( $filter_state != '' ){
            $sqlWhere .= " and status = ". intval($filter_state)." ";
        }

        $f_search = $this->REQUEST('_f_search');
        if( $f_search != '' ){
            $sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' )";
        }

        $this->ModManager = M('festival_photo_class');
        $f_order = $this->REQUEST('_f_order', 'id');
        $fields = $this->ModManager->getDbFields();
        //echo "<pre>";print_r($fields);exit;
        if( in_array($f_order, $fields) ){
            $sqlOrder = $f_order . ' ';
        }else{
            $sqlOrder = 'id ';
        }
        $f_direc = strtoupper($this->REQUEST('_f_direc'));if($f_direc==""){$f_direc='DESC';}
        if( $f_direc != 'DESC' ){
            $sqlOrder .= 'ASC';
        }else{
            $sqlOrder .= 'DESC';
        }

        ///回传过滤条件
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('festival_photo_class', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        $PageTitle = L('一级分类列表');
        $PageMenu = array(
            array( U('festivalphoto/create_classfather'), L('添加一级分类') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }


    /**
     *--------------------------------------------------------------+
     * Action: 一级分类 改状态
     *--------------------------------------------------------------+
     */
    public function ajax_change_status_classfather()
    {
        $module = $UserMod = M('festival_photo_class');
        $id 	= intval($_REQUEST['id']);
        //$type 	= isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'status';
        $status = $type=($type+1)%2;

        $result = $module->execute( sprintf("UPDATE %s SET status=(status+1)%%2 where id=%d", $module->getTableName(), $id) );
        $values = $module->where('id=%d', $id)->find();
        if( is_null($values) || $values === false ){
            $this->ajaxReturn(array('status' => 'false'));
        }else{
            $this->ajaxReturn(array('status' => 'true', 'result' => $values['status']));
        }
    }



    /**
     *--------------------------------------------------------------+
     * Action: 一级分类 添加
     *--------------------------------------------------------------+
     */
    public function create_classfather()
    {

        if(isset($_POST['dosubmit'])){

            $NewsMod = M('festival_photo_class');

            //$rst=$this->CheckNewsData_Post();

            if (false === $NewsMod->create()) {
                $this->error($module->getError());
            }

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod);
                $NewsMod->create_time=time();

                $result =   $NewsMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{

            $PageTitle = L('添加一级分类');
            $PageMenu = array(
                array( U('festivalphoto/listing_classfather'), L('一级分类列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }


    /**
     *--------------------------------------------------------------+
     * Action:  一级分类 修改
     *--------------------------------------------------------------+
     */
    public function edit_classfather()
    {
        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckNewsData_Post($id);

            $NewsMod = M('festival_photo_class');

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod,$id);
                $NewsMod->modify_time=time();

                $result =   $NewsMod->save();
                if($result) {
                    $this->success('操作成功！', U('festivalphoto/edit_classfather', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $NewsMod = M('festival_photo_class');
            // 读取数据
            $data =   $NewsMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑一级分类');
            $PageMenu = array(
                array( U('festivalphoto/create_classfather'), L('添加一级分类') ),
                array( U('festivalphoto/listing_classfather'), L('一级分类列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }


    //通过二级分类的 parent_id 找一级分类
    public function getParentClass($parent_id){
        //echo $parent_id;exit;
        $CityMod = M('festival_photo_class');
        $result = $CityMod->where(" status=1 and id='".$parent_id."' " )->order('id ASC')->select();
        //echo "<pre>";print_r($result);exit;
        if(isset($result[0])){
            $datas = $result[0];
        }
        else{
            $datas = array();
        }
        return $datas;
    }

    //找所有一级分类
    public function getParentClassList(){
        //echo $parent_id;exit;
        $CityMod = M('festival_photo_class');
        $result = $CityMod->where(" status=1 and parent_id='0' " )->order('id ASC')->select();
        //echo "<pre>";print_r($result);exit;
        $datas = $result;
        return $datas;
    }


    //通过一级分类的 id 找二级分类
    public function getSonClass($parent_id){
        $CityMod = M('festival_photo_class');
        $result = $CityMod->where(" status=1 and parent_id='".$parent_id."' " )->order('id ASC')->select();
        //echo "<pre>";print_r($result);exit;
        $datas = $result;
        return $datas;
    }


    //找所有一级分类和二级分类
    public function getAllClassList(){

        $CityMod = M('festival_photo_class');
        $parent_list = $CityMod->where(" status=1 and parent_id='0' " )->order('id ASC')->select();
        //echo "<pre>";print_r($parent_list);exit;

        $allclasslist=array();
        if(isset($parent_list)){
            foreach($parent_list as $k => $v){
                $allclasslist[$v['id']]=$v;
                $son_class_list=$this->getSonClass($v['id']);
                foreach($son_class_list as $k2 => $v2){
                    $v2['title']=$v['title']." -- ".$v2['title'];
                    $allclasslist[$v2['id']]=$v2;
                }
            }
        }
        //echo "<pre>";print_r($allclasslist);exit;
        return $allclasslist;
    }


    /**
     *--------------------------------------------------------------+
     * Action: 二级分类列表
     *--------------------------------------------------------------+
     */
    public function listing_class()
    {

        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='删除'){
            if( empty($_POST['id']) ){
                $this->error(L('请选择要删除的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }

            //删除连带数据

            //删除自身数据
            $NewsMod = M('festival_photo_class');
            $sql=" id in (".$in.") ";
            $NewsMod->where($sql)->delete();

            $this->success('删除成功', U('festivalphoto/listing_class'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC";

        $sqlWhere .= " and parent_id>0 ";

        $filter_state = $this->REQUEST('_filter_state');
        if( $filter_state != '' ){
            $sqlWhere .= " and status = ". intval($filter_state)." ";
        }

        $f_search = $this->REQUEST('_f_search');
        if( $f_search != '' ){
            $sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' )";
        }

        $this->ModManager = M('festival_photo_class');
        $f_order = $this->REQUEST('_f_order', 'id');
        $fields = $this->ModManager->getDbFields();
        //echo "<pre>";print_r($fields);exit;
        if( in_array($f_order, $fields) ){
            $sqlOrder = $f_order . ' ';
        }else{
            $sqlOrder = 'id ';
        }
        $f_direc = strtoupper($this->REQUEST('_f_direc'));if($f_direc==""){$f_direc='DESC';}
        if( $f_direc != 'DESC' ){
            $sqlOrder .= 'ASC';
        }else{
            $sqlOrder .= 'DESC';
        }

        ///回传过滤条件
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('festival_photo_class', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
                $classfather_info=$this->getParentClass($v['parent_id']);
                $rst['dataset'][$k]['title_classfather'] = $classfather_info['title'];
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst);exit;

        $PageTitle = L('二级分类列表');
        $PageMenu = array(
            array( U('festivalphoto/create_class'), L('添加二级分类') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }



    /**
     *--------------------------------------------------------------+
     * Action: 二级分类 改状态
     *--------------------------------------------------------------+
     */
    public function ajax_change_status_class()
    {
        $module = $UserMod = M('festival_photo_class');
        $id 	= intval($_REQUEST['id']);
        //$type 	= isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'status';
        $status = $type=($type+1)%2;

        $result = $module->execute( sprintf("UPDATE %s SET status=(status+1)%%2 where id=%d", $module->getTableName(), $id) );
        $values = $module->where('id=%d', $id)->find();
        if( is_null($values) || $values === false ){
            $this->ajaxReturn(array('status' => 'false'));
        }else{
            $this->ajaxReturn(array('status' => 'true', 'result' => $values['status']));
        }
    }




    /**
     *--------------------------------------------------------------+
     * Action: 二级分类 添加
     *--------------------------------------------------------------+
     */
    public function create_class()
    {

        $fatherclasslist=$this->getParentClassList();
        $this->assign('fatherclasslist', $fatherclasslist );
        //echo "<pre>";print_r($fatherclasslist);exit;

        if(isset($_POST['dosubmit'])){

            $NewsMod = M('festival_photo_class');

            //$rst=$this->CheckNewsData_Post();

            if (false === $NewsMod->create()) {
                $this->error($module->getError());
            }

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod);
                $NewsMod->create_time=time();

                $result =   $NewsMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{

            $PageTitle = L('添加二级分类');
            $PageMenu = array(
                array( U('festivalphoto/listing_class'), L('二级分类列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }





    /**
     *--------------------------------------------------------------+
     * Action:  二级分类 修改
     *--------------------------------------------------------------+
     */
    public function edit_class()
    {

        $fatherclasslist=$this->getParentClassList();
        $this->assign('fatherclasslist', $fatherclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckNewsData_Post($id);

            $NewsMod = M('festival_photo_class');

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod,$id);
                $NewsMod->modify_time=time();

                $result =   $NewsMod->save();
                if($result) {
                    $this->success('操作成功！', U('festivalphoto/edit_class', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $NewsMod = M('festival_photo_class');
            // 读取数据
            $data =   $NewsMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑二级分类');
            $PageMenu = array(
                array( U('festivalphoto/create_class'), L('添加二级分类') ),
                array( U('festivalphoto/listing_class'), L('二级分类列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }





    /**
     *--------------------------------------------------------------+
     * Action: 照片列表
     *--------------------------------------------------------------+
     */
    public function listing_photo()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit']) && $_POST['dosubmit']=='删除'){
            if( empty($_POST['id']) ){
                $this->error(L('请选择要删除的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }

            //删除连带数据

            //删除自身数据
            $NewsMod = M('festival_photo');
            $sql=" id in (".$in.") ";
            $NewsMod->where($sql)->delete();

            $this->success('删除成功', U('festivalphoto/listing_photo'));
            exit;
        }


        /// 载入 lk_cunction. (使用其 LkHTML::ListSort 函数)
        load("@.lk_function");

        ///列表过滤
        $sqlWhere = "status < 250";
        $sqlOrder = " id DESC";

        $filter_state = $this->REQUEST('_filter_state');
        if( $filter_state != '' ){
            $sqlWhere .= " and status = ". intval($filter_state)." ";
        }

        $f_search = $this->REQUEST('_f_search');
        if( $f_search != '' ){
            $sqlWhere .= " and (title like '%". $this->fixSQL($f_search)."%' )";
        }

        $this->ModManager = M('festival_photo');
        $f_order = $this->REQUEST('_f_order', 'id');
        $fields = $this->ModManager->getDbFields();
        //echo "<pre>";print_r($fields);exit;
        if( in_array($f_order, $fields) ){
            $sqlOrder = $f_order . ' ';
        }else{
            $sqlOrder = 'id ';
        }
        $f_direc = strtoupper($this->REQUEST('_f_direc'));if($f_direc==""){$f_direc='DESC';}
        if( $f_direc != 'DESC' ){
            $sqlOrder .= 'ASC';
        }else{
            $sqlOrder .= 'DESC';
        }

        ///回传过滤条件
        $this->assign('filter_state',  $filter_state);
        $this->assign('f_search',  $f_search);
        $this->assign('f_order',   $f_order);
        $this->assign('f_direc',   $f_direc );

        ///获取列表数据集
        $rst=$this->GeneralActionForListing('festival_photo', $sqlWhere, $sqlOrder, '', 'M');
        //echo "<pre>";print_r($rst);exit;

        if(isset($rst['dataset'])){
            foreach($rst['dataset'] as $k => $v){
                if(isset($allclasslist[$v['class_id']])){
                    $rst['dataset'][$k]['class_name']=$allclasslist[$v['class_id']]['title'];
                }
                else{
                    $rst['dataset'][$k]['class_name']="";
                }
            }
            $this->assign('dataset', $rst['dataset']);// 赋值数据集
        }
        //echo "<pre>";print_r($rst);exit;

        $PageTitle = L('精彩照片列表');
        $PageMenu = array(
            array( U('festivalphoto/create_photo'), L('添加精彩照片') ),
        );
        $this->setPagething( $PageTitle, $PageMenu, true);
        $this->display();
    }




    /**
     *--------------------------------------------------------------+
     * Action: 照片 改状态
     *--------------------------------------------------------------+
     */
    public function ajax_change_status_photo()
    {
        $module = $UserMod = M('festival_photo');
        $id 	= intval($_REQUEST['id']);
        //$type 	= isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'status';
        $status = $type=($type+1)%2;

        $result = $module->execute( sprintf("UPDATE %s SET status=(status+1)%%2 where id=%d", $module->getTableName(), $id) );
        $values = $module->where('id=%d', $id)->find();
        if( is_null($values) || $values === false ){
            $this->ajaxReturn(array('status' => 'false'));
        }else{
            $this->ajaxReturn(array('status' => 'true', 'result' => $values['status']));
        }
    }




    /**
     *--------------------------------------------------------------+
     * Action: 照片 添加
     *--------------------------------------------------------------+
     */
    public function create_photo()
    {
        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){

            $NewsMod = M('festival_photo');

            //$rst=$this->CheckNewsData_Post();

            if (false === $NewsMod->create()) {
                $this->error($module->getError());
            }

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod);
                $NewsMod->create_time=time();

                $result =   $NewsMod->add();
                if($result) {
                    $this->success('操作成功！');
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{

            $PageTitle = L('添加精彩照片');
            $PageMenu = array(
                array( U('festivalphoto/listing_photo'), L('精彩照片列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }



    /**
     *--------------------------------------------------------------+
     * Action:  照片 修改
     *--------------------------------------------------------------+
     */
    public function edit_photo()
    {

        $allclasslist=$this->getAllClassList();
        $this->assign('allclasslist', $allclasslist );

        if(isset($_POST['dosubmit'])){
            $id = intval($this->REQUEST('id'), 0);

            //$rst=$this->CheckNewsData_Post($id);

            $NewsMod = M('festival_photo');

            if($NewsMod->create()) {

                //$rst=$this->CheckNewsData_Mod($NewsMod,$id);
                $NewsMod->modify_time=time();

                $result =   $NewsMod->save();
                if($result) {
                    $this->success('操作成功！', U('festivalphoto/edit_photo', array('id'=>$id)) );
                }else{
                    $this->error('写入错误！');
                }
            }else{
                $this->error($NewsMod->getError());
            }

        }else{
            if( isset($_GET['id']) ){
                $id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error(L('参数错误'));
            }

            $NewsMod = M('festival_photo');
            // 读取数据
            $data =   $NewsMod->find($id);
            if($data) {
                $this->record = $data;// 模板变量赋值
            }else{
                $this->error('用户数据读取错误');
            }

            $PageTitle = L('编辑精彩照片');
            $PageMenu = array(
                array( U('festivalphoto/create_photo'), L('添加精彩照片') ),
                array( U('festivalphoto/listing_photo'), L('精彩照片列表') ),
            );
            $this->setPagething( $PageTitle, $PageMenu, true);
            $this->display();
        }
    }










    /*
        function delete()
        {
            if( empty($_POST['id']) ){
                $this->error(L('请选择要删除的数据！'));
            }

            if(is_array($_POST['id'])){
                $ids = $_POST['id'];
                $in = implode(",",$_POST['id']);
            }else{
                $ids[] = $_POST['id'];
                $in = $_POST['id'];
            }

            //删除连带数据

            //删除自身数据
            $UserMod = M('user');
            $sql=" id in (".$in.") ";
            $UserMod->where($sql)->delete();

            $this->success('删除成功', U('user/listing'));
        }
    */

	private function CheckNewsData_Post($user_id=0){
		///检查 $_POST 提交数据

	}
	

	private function CheckNewsData_Mod(&$User, $user_id=0){
		///检查 $User 模型数据。$User->email

	}


}
?>