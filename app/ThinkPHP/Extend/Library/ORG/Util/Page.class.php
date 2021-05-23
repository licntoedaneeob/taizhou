<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// |         lanfengye <zibin_5257@163.com>
// +----------------------------------------------------------------------

class Page {
    
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制 (theme分页模板，支持html标签写法)
    protected $config  =    array('header'=>'个','prev'=>'上页','next'=>'下页','first'=>'最前页','last'=>'最后页','theme'=>' 共 %totalRow% %header% 当前 %nowPage%/%totalPage% 页 &nbsp;&nbsp; %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 默认分页变量名
    protected $varPage;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$parameter='',$url='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';

        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        if ($upRow>0){
            $upPage     =   "<a href='".str_replace('__PAGE__',$upRow,$url)."'>".$this->config['prev']."</a>";
        }else{
            $upPage     =   '';
        }

        if ($downRow <= $this->totalPages){
            $downPage   =   "<a href='".str_replace('__PAGE__',$downRow,$url)."'>".$this->config['next']."</a>";
        }else{
            $downPage   =   '';
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst   =   '';
            $prePage    =   '';
        }else{
            $preRow     =   $this->nowPage-1;
            if($preRow <= 1){
                $preRow = 1;
            }
            $prePage    =   "<a href='".str_replace('__PAGE__',$preRow,$url)."' >上一页</a>";
            $theFirst   =   "<a href='".str_replace('__PAGE__',1,$url)."' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage   =   '';
            $theEnd     =   '';
        }else{
            $nextRow    =   $this->nowPage+1;
            if($nextRow >= $this->totalPages){
                $nextRow = $this->totalPages;
            }
            $theEndRow  =   $this->totalPages;

            $nextPage   =   "<a href='".str_replace('__PAGE__',$nextRow,$url)."' >下一页</a>";
            $theEnd     =   "<a href='".str_replace('__PAGE__',$theEndRow,$url)."' >".$this->config['last']."</a>";
        }
        // 1 2 3 4 5
        
        
        
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page       =   ($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= "&nbsp;<a href='".str_replace('__PAGE__',$page,$url)."'>&nbsp;".$page."&nbsp;</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= "&nbsp;<span class='current'>".$page."</span>";
                }
            }
        }

        $upPage="";
        $downPage="";

        $pageStr     =   str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),
            $this->config['theme']
            );



if($this->totalPages<=1){
    return "";
}











$show_start="no";
$show_end="no";

$palist_num=10;


if($this->nowPage==1){
$show_start="yes";
}
if($this->nowPage==$this->totalPages){
$show_end="yes";
}


if (($this->nowPage%$palist_num)!=0){
$page_start=$this->nowPage-($this->nowPage%$palist_num)+1;
}
if (($this->nowPage%$palist_num)==0){
$page_start=$this->nowPage-$palist_num+1;
}

$x=$page_start;
$i=1;
$palist=array();
if ($this->totalPages!=0){
do {
$palist[$i]['id']=$x;


$i=$i+1;
$x=$x+1;


}while($i<=$palist_num && $x<=$this->totalPages);
}


if (($this->totalPages-$page_start)>=$palist_num){
$show_lastpage='yes';
}
if (($this->totalPages-$page_start)<$palist_num){
$show_lastpage='no';
}

//echo "<pre>";print_r($palist);exit;








if ($this->nowPage > $palist_num){
$show_first_page_content="<a><li class='' style='cursor:default;color:#9d9d9d;'>...</li></a>";
}
else{
$show_first_page_content="";
}



if ($show_lastpage == "yes"){
$show_last_page_content="<a><li class='' style='cursor:default;color:#9d9d9d;'>...</li></a>";
}
else{
$show_last_page_content="";
}







$preUrl=str_replace('__PAGE__',$preRow,$url);
$nextUrl=str_replace('__PAGE__',$nextRow,$url);



/*
//一枪头显示所有分页
$k_page=1;
$page_body_center="";
do{

if($k_page==$this->nowPage){
    $page_cur_show='lrBtnActive';
}
else{
    $page_cur_show='';
}

$page_body_center=$page_body_center.'<a href="'.str_replace('__PAGE__',$k_page,$url).'"><li class="'.$page_cur_show.'">'.$k_page.'</li></a>';


$k_page=$k_page+1;
}while($k_page<=$this->totalPages);
*/




//只显示palist数目的分页
$page_body_center="";
foreach ($palist as $key => $item) {
$k_page=$page_start+$key-1;
if($k_page==$this->nowPage){
    $page_cur_show='active';
}
else{
    $page_cur_show='';
}
$page_body_center=$page_body_center.'<li class="'.$page_cur_show.'"><a href="'.str_replace('__PAGE__',$k_page,$url).'">'.$k_page.'</a></li>';
}
//echo $page_body_center;exit;








//分页样式，前台。
 $pageStr = '<li><a href="'.$preUrl.'"><i class="icon-chevron-left"></i></a></li>
			
			'.$show_first_page_content.$page_body_center.$show_last_page_content.'
			
			<li><a href="'.$nextUrl.'"><i class="icon-chevron-right"></i></a></li>
			';


            //echo $pageStr;exit;


        return $pageStr;
    }

}