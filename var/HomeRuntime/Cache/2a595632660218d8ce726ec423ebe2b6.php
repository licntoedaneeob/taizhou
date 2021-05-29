<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1">
<meta name="keywords" content="2017台州国际马拉松官网,台州马拉松，全程马拉松，" />
<meta name="description" content="2017台州国际马拉松官网-和合山海，穿越未来。2017台州国际马拉松为专业跑友提供线上报名与支付,跑友可查询参赛号及下载成绩证书,并可随时了解2017台州国际马拉松的实时资讯。" />
<meta name="author" content="">
<title>2017台州国际马拉松赛</title>
<link rel="stylesheet" href="<?php echo STATICSPATH;?>css/bootstrap.css">
<link rel="stylesheet" href="<?php echo STATICSPATH;?>css/fontawesome/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo STATICSPATH;?>css/swiper.css">
<link rel="stylesheet" href="<?php echo STATICSPATH;?>css/style.css">

<script type="text/javascript" src="<?php echo STATICSPATH;?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo STATICSPATH;?>js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo STATICSPATH;?>js/app/main.js"></script>
</head>
<body>
<header id="navbar" role="banner" class="navbar navbar-static-top navbar-default">
<div class="container">
    <div class="navbar-header">
        <a class="logo pull-left" href="<?php echo U('/');?>" title="首页">
        <img src="<?php echo STATICSPATH;?>images/tz_logo.png" alt="首页">
        </a>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </button>
    </div>
    <div class="navbar-collapse collapse">
        <nav role="navigation">
            <ul class="menu nav navbar-nav ">
                <li class="<?php if($curmenu==0){echo 'active';}?>" ><a href="<?php echo U('/');?>">首页<span>HOME</span></a></li>
                <li class="<?php if($curmenu==1){echo 'active';}?>" ><a href="#" data-toggle="dropdown"  class="dropdown-toggle">新闻公告<span>NEWS</span></a>
                   <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a href="<?php echo U('information/news');?>">新闻</a></li>
                        <li><a href="<?php echo U('information/notice');?>">公告</a></li>
                       
                    </ul>
                </li>
                <li class="<?php if($curmenu==2){echo 'active';}?>" >
                    <a href="#" data-toggle="dropdown"  class="dropdown-toggle">参赛指南<span>COMPETITION GUIDE</span></a>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a href="<?php echo U('about/index', array('id'=>21 ));?>">竞赛规程</a></li>
                        <li><a href="<?php echo U('about/index', array('id'=>22 ));?>">比赛路线</a></li>
                        <li><a href="<?php echo U('about/index', array('id'=>23 ));?>">常见问题</a></li>
                    </ul>
                </li>
                <li class="<?php if($curmenu==3){echo 'active';}?>" ><a href="#" data-toggle="dropdown"  class="dropdown-toggle">报名参赛<span>ENTER FOR COMPETITION</span></a>
                   <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a href="<?php echo U('about/index', array('id'=>31 ));?>">报名须知</a></li>
                        <!--<?php if(date('YmdHis')>'20170928100000'){ ?><li><a href="<?php echo U('baoming/statement');?>">报名通道</a></li><?php }else{ ?><li><a href="#">报名通道</a></li><?php } ?>-->
                        <?php if(date('YmdHis')>'20170928100000'){ ?><li><a href="<?php echo U('baoming/order_search');?>">报名查询</a></li><?php }else{ ?><li><a href="#">报名查询</a></li><?php } ?>
                        <li><a href="<?php echo U('service/cert');?>">2016台马证书下载</a></li>
                    </ul>
                </li>
                <li class="<?php if($curmenu==4){echo 'active';}?>" >
                    <a href="#" data-toggle="dropdown"  class="dropdown-toggle">赛后查询<span>POST GAME QUERY</span></a>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a href="<?php echo U('service/result');?>">成绩查询</a></li>
                        <li><a href="<?php echo U('service/result');?>">证书下载</a></li>
                        <li><a href="http://iranshao.com/races/3112/albums?year=2017" target="_blank">照片查询</a></li>
                    </ul>
                </li>
                <li class="<?php if($curmenu==5){echo 'active';}?>" >
                    <a href="<?php echo U('home/partner');?>">合作伙伴<span>SPONSOR</span></a>
                </li>
                <li class="<?php if($curmenu==6){echo 'active';}?>" >
                    <a href="#" data-toggle="dropdown"  class="dropdown-toggle">关于我们<span>ABOUT US</span></a>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a href="<?php echo U('about/index', array('id'=>61 ));?>">赛事简介</a></li>
                        <li><a href="<?php echo U('about/index', array('id'=>62 ));?>">联系我们</a></li>
                 
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- <div class="header-tools">
            <a href="#" class="hidden-xs"><i class="icon-search icon-2x"></i></a>
            <div class="visible-xs">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="请输入搜索内容">
                    <span class="input-group-btn"><button class="btn btn-default" type="button"><i class="icon-search"></i></button></span>
                </div>
            </div>
        </div> -->
    </div>
        <div class="navbar-header-right">
        <a class="pull-right" href="<?php echo U('/');?>" title="首页">
        <img src="<?php echo STATICSPATH;?>images/tz_logo_r2.png" alt="首页">
        </a>
        </div>
    </div>
    
</div>
    
</header>
<!-- //header -->


<div class="main-container container">
    <!-- <div class="row">
        <div class="tools-bar">
            <div class="countdown pull-left">
                <strong>赛事倒计时<span>COUNTDOWN</span></strong>
                <div class="bd time">
                    
                </div>
            </div>
            <div class="pull-left rlink">
                <a href="#"><i class="icon-user"></i>注册</a>丨<a href="#">登录</a>
            </div>
        </div>
    </div> -->
    <div class="row">
    	<ol class="breadcrumb">
            <li><a href="<?php echo U('/');?>">首页</a></li>
            <li class="active">新闻公告</li>
        </ol>
        <div class="two-new">
            <div class="two-news-item">
                <!--
                <div class="two-news-tit">
                    <h4 class="pull-left">台州马拉松 > 新闻</h4>
                </div>
                <div class="three-news-head">
                    <h2 class="text-center"><?php echo $record['title'];?></h2>
                </div>
                -->
                
                
                <!--<div class="date"><?php echo $record['pub_time'];?></div>-->
                <h3 class="title-m v2"><span>新闻 NEWS BULLETIN</span></h3>
                
                <div class="content-full">
                	<h2 class="text-center"><?php echo $record['title'];?></h2>
                    <?php echo $record['content'];?>
                </div>
                
            </div>
            <div class="three-news-footer center-block text-center">
               <a href="javascript:history.back();"><img src="<?php echo STATICSPATH;?>images/tz-icon-fanhui.png" alt="">返回</a>
            </div>    
        </div>
       <!-- //news-item -->
    </div>
</div>




<footer class="footer container-fluid">
    <section class="row">
        <div class="container">
            <div class="row">
            <div class="col-sm-2 foot-item">
                <img class="footer_logo" src="<?php echo STATICSPATH;?>images/tz_footlogo.png" alt="">
            </div>
            <div class="col-sm-8 foot-item">
                <div class="row">
                    <div class="col-xs-7">
                        <h3>组织机构</h3>
                        <p>主办单位: <span>中国田径协会&nbsp;&nbsp;浙江省体育局 </span> &nbsp;&nbsp;<span>台州市人民政府</span>
                        <p>承办单位: <span>浙江省田径协会</span>&nbsp;&nbsp;<span>台州市体育局</span>
                        <p>协办单位: <span>台州市田径协会</span>
                        <p>运营单位: <span>温州青鸟体育文化传播有限公司</span>
                    </div>
                   <div class="col-xs-5">
                        <h3>赛事资讯</h3>
                         <p>招商热线: <span>15988925596</span></p>
                        <p>微信公众号: <span>TZMLS0576</span></p>
                        <p>电话: <span>0576-8816 0967</span><br />
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;0576-8810 0917</p>
                        <p>官方邮箱: tzmls0576@163.com<br />
                          技术支持：<a href="http://ems.irunner.mobi/" target="_blank">比赛助手</a><br />
                          <br />
                        </p>
                    </div>
                    
                </div>
            </div>
            <div class="col-sm-2">
                        <h3>微信</h3>
                        <img src="<?php echo STATICSPATH;?>images/tz_foot_wx.png" alt="">
            </div>
            </div>
        </div>
    </section>
    <!-- /.block -->
</footer>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?34fe4a32e5c2adbd3aaa97a683778c52";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>

</body>
</html>