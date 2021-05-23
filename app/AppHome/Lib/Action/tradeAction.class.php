<?php
class tradeAction extends TAction
{
    public function index()
    {
		$token_rst=$this->token_member('controller');
		if($token_rst['success']!='success'){
			$url=U('login/index');
			redirect($url);
			exit;
		}

        $this->display('index');
    }

    public function pay_n()
    {
        $token_rst=$this->token_member('controller');
        if($token_rst['success']!='success'){
            $url=U('login/index');
            redirect($url);
            exit;
        }

        $this->display('list_pay_n');
    }

    public function pay_a()
    {
        $token_rst=$this->token_member('controller');
        if($token_rst['success']!='success'){
            $url=U('login/index');
            redirect($url);
            exit;
        }

        $this->display('list_pay_a');
    }

    public function getList()
    {
        if(isset($_REQUEST['page']) && isset($_REQUEST['kind']))
        {
            $page = $_REQUEST['page'];
            $kind = $_REQUEST['kind'];
        }
        else{
            exit;
        }

        $token_rst=$this->token_member('controller');
        if($token_rst['success']!='success'){
            $url=U('login/index');
            redirect($url);
            exit;
        }
        $userinfo=$token_rst['user_info'];
        $user_id = $userinfo['user_id'];

        $this->checkOrderIsExpire();

        $per_page = 10;
        $offset = $per_page * ($page - 1);

        $sql_kind = '';
        switch($kind)
        {
            case 1:
                $sql_kind = "";
                break;

            case 2:
                $sql_kind = "and isExpire=0 and isPay=0";
                break;

            case 3:
                $sql_kind = "and isPay=1";
                break;
        }

        $dbPre = C('DB_PREFIX');
        $sql = "SELECT *
                FROM `{$dbPre}order`
                WHERE 1
                AND `status` = 1
                AND member_id = '{$user_id}'
                {$sql_kind}
                GROUP BY order_no
                ORDER BY id DESC
                LIMIT $offset, $per_page";

        $Model = M();
        $result = $Model->query($sql);
        if(empty($result)) return null;

//        echo "<pre>";print_r($result);exit;

        $group = array();
        foreach ($result as $k=>$val)
        {
            $orderMod = M('order');
            $result = $orderMod
                ->where("1 and order_no = {$val['order_no']}")
                ->order("price_race asc, id asc")
                ->select();

            $list = array();
            foreach($result as $row)
            {
                $order_id = $row['id'];

                $orderTeamMod = M('order_team');
                $result_t = $orderTeamMod
                    ->where("1 and order_id='{$order_id}'")
                    ->select();

                $team = empty($result_t) ? array() : $result_t;

                $orderProMod = M('order_product');
                $result_p = $orderProMod
                    ->where("1 and order_id='{$order_id}'")
                    ->select();

                $product = empty($result_p) ? array() : $result_p;

                $list[] = array(
                    'order' => $row,
                    'team' => $team,
                    'product' => $product,
                );
            }

            $group[] = array(
                'total' => $val,
                'list' => $list,
            );
        }

        $this->assign('group', $group);

        $this->display('scroll_list/index');
    }

    public function getList_bak()
    {
        if(isset($_REQUEST['page']) && isset($_REQUEST['kind']))
        {
            $page = $_REQUEST['page'];
            $kind = $_REQUEST['kind'];
        }
        else{
            exit;
        }

        $token_rst=$this->token_member('controller');
        if($token_rst['success']!='success'){
            $url=U('login/index');
            redirect($url);
            exit;
        }
        $userinfo=$token_rst['user_info'];
        $user_id = $userinfo['user_id'];

        $this->checkOrderIsExpire();

        $per_page = 10;
        $offset = $per_page * ($page - 1);

        $sql_kind = '';
        switch($kind)
        {
            case 1:
                $sql_kind = "";
                break;

            case 2:
                $sql_kind = "and isExpire=0 and isPay=0";
                break;

            case 3:
                $sql_kind = "and isPay=1";
                break;
        }

        $orderMod = M('order');
        $result = $orderMod
            ->where("1 and `status`=1 and member_id='{$user_id}' {$sql_kind}")
            ->order("id desc")
            ->limit($offset, $per_page)
            ->select();

        if(empty($result)) return null;

//        echo "<pre>";print_r($result);exit;

        $list = array();
        foreach($result as $row)
        {
            $order_id = $row['id'];

            $orderTeamMod = M('order_team');
            $result_t = $orderTeamMod
                ->where("1 and order_id='{$order_id}'")
                ->select();

            $team = empty($result_t) ? array() : $result_t;

            $orderProMod = M('order_product');
            $result_p = $orderProMod
                ->where("1 and order_id='{$order_id}'")
                ->select();

            $product = empty($result_p) ? array() : $result_p;

            $list[] = array(
                'order' => $row,
                'team' => $team,
                'product' => $product,
            );
        }

        $this->assign('list', $list);

        $this->display('scroll_list/index');

        /*$sql =
            sprintf("SELECT * FROM %s

            where member_id='".addslashes($user_id)."'"

            , $orderMod->getTableName());*/

//        $result = $orderMod->execute($sql);
    }

    public function getDetail()
    {
        if(isset($_REQUEST['id']))
        {
            $order_id = $_REQUEST['id'];
        }
        else{
            exit;
        }

        $token_rst=$this->token_member('controller');
        if($token_rst['success']!='success'){
            $url=U('login/index');
            redirect($url);
            exit;
        }
        $userinfo=$token_rst['user_info'];
        $user_id = $userinfo['user_id'];

        $orderMod = M('order');
        $result = $orderMod
            ->where("1 and `status`=1 and id='{$order_id}' and member_id='{$user_id}'")
            ->select();

        if(empty($result))
        {
            redirect(U('trade/index'));
            exit;
        }

        $order = $result[0];

        $orderTeamMod = M('order_team');
        $result_t = $orderTeamMod
            ->where("1 and order_id='{$order_id}'")
            ->select();

        $team = empty($result_t) ? array() : $result_t;

        $orderProMod = M('order_product');
        $result_p = $orderProMod
            ->where("1 and order_id='{$order_id}'")
            ->select();

        $product = empty($result_p) ? array() : $result_p;

        $payUrl = U('order/pay', array('order_id'=>$order['id'] , 'order_no'=>$order['order_no'] ));

        $this->assign('order', $order);
        $this->assign('team', $team);
        $this->assign('product', $product);
        $this->assign('payUrl', $payUrl);

        $this->display('detail');
    }

    function temp()
    {
        return;
        $orderMod = M('order');
        $result = $orderMod
//            ->where("1 and `status`=0")
            ->order("id desc")
            ->limit(10)
            ->select();

        echo "<pre>";print_r($result);exit;
    }

    function tt()
    {
        exit();
        $api_url='http://api.xrace.cn/?ctl=xrace.config&ac=get.race.stage.info&RaceStageId=35';
        //echo $api_url;exit;
        $api_para=array();
        $api_result=$this->http_request($api_url,$api_para);
        echo "<pre>";print_r($api_result);exit;
    }

}
?>