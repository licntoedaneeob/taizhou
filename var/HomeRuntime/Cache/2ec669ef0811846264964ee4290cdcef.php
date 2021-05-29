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

<script src="<?php echo STATICSPATH;?>js/jquery.jcountdown.js"></script>
<script src="<?php echo STATICSPATH;?>js/swiper.min.js"></script>
<script type="text/javascript">
    $(window).load(function() {
        var swiper = new Swiper('.swiper-banner', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev',
                autoplay : 5000
        });
        $(".s-pagination .col-xs-3").click(function(e){
                e.preventDefault()
                swiper.slideTo( $(this).index() )
        });

        var swiperImg = new Swiper('.swiper-img', {
                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev',
                slidesPerView: 3,spaceBetween: 20,
                breakpoints: {
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 10
                    }
                }
        });
        $(".time").countdown({
                date: "2017/11/12 08:00",
                htmlTemplate: "<span class='cd-time'><b>%d</b>天</span><span class='cd-time'><b>%h</b>小时</span><span class='cd-time'><b>%i</b>分钟</span><span class='cd-time'><b>%s</b>秒</span>",
                leadingZero: true

            });

    });
</script>


<div class="container-fluid">
    <div class="row">
        <div class="swiper-container swiper-banner">
            <div class="swiper-wrapper">
            <?php
 foreach ($index_banner_list as $k=>$v) { ?>
                <div class="swiper-slide">
                <?php if(!empty($v['link_url'])){ ?><a href="<?php echo $v['link_url'];?>" target="_blank"><?php } ?><img src="<?php echo STATICSCDN.$v['pic_show'];?>"  alt="" ><?php if(!empty($v['link_url'])){ ?></a><?php } ?>
                </div>
                <?php
 } ?>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            <!-- Add Arrows -->
            <div class="swiper-button-next swiper-button-black" style="background-image: url(images/tz_swiper_button-right.png);"></div>
            <div class="swiper-button-prev swiper-button-black" style="background-image: url(images/tz_swiper_button-left.png);"></div>
        </div>
    </div>

<!-- //swiper -->
</div>
<!-- 倒计时 -->
<div class="container-fluid tz-countdown-main">
    <div class="container">
            <div class="countdown pull-left">
                <strong class="pull-left">赛事倒计时<span>Countdown</span></strong>
                <div class="pull-left rlink">

                </div>
                <div class="bd time">
                </div>
            </div>
            <ul class="menu nav navbar-nav pull-left">
              <li class="nav_icon_bus">
                    <a href="http://live.163.com/room/153905.html" target="_blank">
                    <img src="<?php echo STATICSPATH;?>images/tz_icon_bus.png" alt="">

                    <span>全程直播<br />VIDEO WEBCAST</span>
                    </a>
                </li>
                <li class="nav_icon_guide">
                    <!--<a href="<?php echo U('about/index', array('id'=>32 ));?>">-->
                    <a href="<?php echo U('information/competition_guide');?>">
                    <img src="<?php echo STATICSPATH;?>images/tz_icon_guide.png" alt="">

                        <span>参赛指南 <br />COMPETITION GUIDE</span>
                    </a>
                </li>
                <li class="nav_icon_run">
                	<?php if(date('YmdHis')>'20170928100000'){ ?>
                    <!--<a href="<?php echo U('baoming/statement');?>">-->
                    <a href="<?php echo U('baoming/order_search');?>">
                	<?php }else{ ?>
                	<a href="#">
                	<?php } ?>
                    <img src="<?php echo STATICSPATH;?>images/tz_icon_run.png" alt="">

                        <span> 报名通道 <br />REGISTRATION CHANNEL</span>
                    </a>
                </li>
                <li class="nav_icon_post">
                    <a href="<?php echo STATICSPATH;?>down/health.docx">
                    <img src="<?php echo STATICSPATH;?>images/tz_icon_post.png" alt="">

                    <span> 体检模板下载<br />CERTIFICATE OF HEALTH</span>
                    </a>
                </li>

            </ul>
    </div>
</div>
<!-- //倒计时 -->
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
        <div class="m-news">
            <div class="news-item">
                <div class="news-tit">
                    <h4 class="pull-left">新闻公告</h4>
                    <a class="pull-right" href="<?php echo U('information/news');?>">更多</a>
                </div>
                <div class="news-left-img pull-left">
                    <img src="<?php echo $index_info['pic_b_show'];?>" alt="">
                </div>
                <ul>
                <?php
 foreach ($news_notice_list as $k=>$v) { ?>
                    <li>
                        <span class="date"><?php echo $v['pub_time'];?></span>

                        <a href="<?php  if($v['class_id']==11){ echo U('information/news_detail', array('id'=>$v['id'] ));}else{ echo U('information/notice_detail', array('id'=>$v['id'] )); } ?>"><?php echo $v['title'];?></a>


                    </li>
                    <?php
 } ?>

                </ul>
            </div>
            <div class="news-item">
                <div class="news-tit">
                    <h4 class="pull-left">精彩视频</h4>
                    <a class="pull-right" href="<?php echo U('information/video');?>">更多</a>
                </div>
                <!-- <div class="news-left-video pull-left">
                    <img src="<?php echo STATICSPATH;?>images/tz_video.png" alt="">
                </div> -->
                <div class="content video-iframe-mc">
	                <?php
 $video_url=stristr($video_list[0]['iframe_url'], 'http'); $video_url_del=stristr($video_url, '"'); $video_url=str_replace($video_url_del,'',$video_url); ?>
	                <iframe width="686" height="480" frameborder="0" src="<?php echo $video_url;?>" allowfullscreen></iframe>
                </div>
            </div>

            <div class="news-item">
                <div class="news-tit">
                    <h4 class="pull-left">精彩图集</h4>
                    <a class="pull-right" href="<?php echo U('information/picture');?>">更多</a>
                </div>
                <!-- <div class="tz-good-photo">
                    <img src="" alt="">
                    <img src="" alt="">
                    <img src="" alt="">
                </div> -->
                <div class="col-main">
                <div class="tz-good-photo swiper-container swiper-img">
                    <div class="swiper-wrapper">
                    <?php
 if(!empty($picture_list)){ foreach ($picture_list as $k=>$v) { ?>
                        <div class="swiper-slide"><a href="<?php echo U('information/picture_detail', array('id'=>$v['id'] ));?>"><img src="<?php echo STATICSCDN.$v['pic_show'];?>" alt=""></a></div>
                       <?php
 } } ?>
                         </div>
                        <!-- Add Arrows -->
                        <div class="swiper-button-next  swiper-button-white"></div>
                        <div class="swiper-button-prev  swiper-button-white"></div>
                    </div>
                </div>
                </div>

            <div class="news-item">
                <div class="news-tit">
                    <h4 class="pull-left">赛事简介</h4>
                       <!--<a class="pull-right" href="#">更多</a>-->
                </div>
                <div class="tz-gamereport">
                    <ul>
                        <li class="nav_icon_ssjj">
                            <a href="<?php echo U('about/index', array('id'=>1 ));?>">
                            <img src="<?php echo STATICSPATH;?>images/tz_icon_ssjj.png" alt="">
                            赛事简介
                            </a>
                        </li>
                        <li class="nav_icon_bsxl">
                            <a href="<?php echo U('about/index', array('id'=>2 ));?>">
                            <img src="<?php echo STATICSPATH;?>images/tz_icon_map.png" alt="">
                            比赛线路
                            </a>
                        </li>
                        <li class="nav_icon_tzzm">
                            <a href="<?php echo U('about/index', array('id'=>3 ));?>">
                            <img src="<?php echo STATICSPATH;?>images/tz_icon_ball.png" alt="">
                            兔子招募
                            </a>
                        </li>
                        <li class="nav_icon_zyzzm">
                            <a href="<?php echo U('about/index', array('id'=>4 ));?>">
                            <img src="<?php echo STATICSPATH;?>images/tz_icon_zyzzm.png" alt="">
                            志愿者招募
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

           <!-- <div class="news-item">
                <div class="news-tit">
                    <h4 class="pull-left">相关活动</h4>
                     <a class="pull-right" href="<?php echo U('information/picture');?>">更多</a>
                </div>
                <div class="tz-xgjs">
                    <a href="<?php echo U('about/index', array('id'=>5 ));?>" ><img src="<?php echo STATICSPATH;?>images/tz_jcimg1.png" alt="" ></a>
                    <a href="<?php echo U('about/index', array('id'=>6 ));?>" ><img src="<?php echo STATICSPATH;?>images/tz_jcimg2.png" alt=""  style=" margin-right:0;"></a>
                    <a href="<?php echo U('information/tourist');?>" ><img src="<?php echo STATICSPATH;?>images/tz_jcimg1.png" alt="" ></a>
                    <a href="<?php echo U('information/event');?>" ><img src="<?php echo STATICSPATH;?>images/tz_jcimg2.png" alt=""  style=" margin-right:0;"></a>
                </div>
            </div>-->
        </div>

            <!-- //news-item -->
        <div class="sponsor-right">

        		<?php
 if(!empty($media_all_list[1]['pic_list'])){ ?>
                <div >
                    <h3>顶级冠名赞助商</h3>
                    <?php
 foreach ($media_all_list[1]['pic_list'] as $k=>$v) { ?>
                    <?php if(!empty($v['link_url'])){ ?><a href="<?php echo $v['link_url'];?>" target="_blank"><?php } ?><img src="<?php echo STATICSCDN.$v['pic_show'];?>" alt=""><?php if(!empty($v['link_url'])){ ?></a><?php } ?>
                    <?php
 } ?>
                </div>
                <?php
 } ?>


	          <?php
 if(!empty($media_all_list[2]['pic_list'])){ ?>
                <div class="text-center">
                    <h3>官方战略合作伙伴</h3>
                    <?php
 foreach ($media_all_list[2]['pic_list'] as $k=>$v) { ?>
                    <?php if(!empty($v['link_url'])){ ?><a href="<?php echo $v['link_url'];?>" target="_blank"><?php } ?><img src="<?php echo STATICSCDN.$v['pic_show'];?>" alt=""><?php if(!empty($v['link_url'])){ ?></a><?php } ?>
                    <?php
 } ?>
                </div>
                <?php
 } ?>




	          <?php
 if(!empty($media_all_list[3]['pic_list'])){ ?>

                <?php
 } ?>



            <!-- //sponsor -->
        </div>

        <div class="sponsor-bottom">
            <div class="sponsor-b1">
                <img src="<?php echo STATICSPATH;?>images/tz_sponsor_b1.png" alt="">
            </div>

            <?php
 if(!empty($media_all_list[1]['pic_list'])){ ?>
            <div class="text-center sponsor">
                    <h3>顶级冠名赞助商</h3>
                    <?php
 foreach ($media_all_list[1]['pic_list'] as $k=>$v) { ?>
                    <?php if(!empty($v['link_url'])){ ?><a href="<?php echo $v['link_url'];?>" target="_blank"><?php } ?><img class="center-block" src="<?php echo STATICSCDN.$v['pic_show'];?>" alt=""><?php if(!empty($v['link_url'])){ ?></a><?php } ?>
                    <?php
 } ?>
            </div>
            <?php
 } ?>

	          <?php
 if(!empty($media_all_list[2]['pic_list'])){ ?>
            <div class="text-center sponsor space_long">
                    <h3>官方战略合作伙伴</h3>
                    <?php
 foreach ($media_all_list[2]['pic_list'] as $k=>$v) { ?>
                    <?php if(!empty($v['link_url'])){ ?><a href="<?php echo $v['link_url'];?>" target="_blank"><?php } ?><img class="center-block" src="<?php echo STATICSCDN.$v['pic_show'];?>" alt=""><?php if(!empty($v['link_url'])){ ?></a><?php } ?>
                    <?php
 } ?>
            </div>
            <?php
 } ?>


	           <?php
 if(!empty($media_all_list[3]['pic_list'])){ ?>
            <div class="text-center sponsor">
                    <h3>官方合作伙伴</h3>
                    <?php
 foreach ($media_all_list[3]['pic_list'] as $k=>$v) { ?>
                    <?php if(!empty($v['link_url'])){ ?><a href="<?php echo $v['link_url'];?>" target="_blank"><?php } ?><img class="center-block" src="<?php echo STATICSCDN.$v['pic_show'];?>" alt=""><?php if(!empty($v['link_url'])){ ?></a><?php } ?>
                    <?php
 } ?>
            </div>
            <?php
 } ?>


	           <?php
 if(!empty($media_all_list[4]['pic_list'])){ ?>
            <div class="text-center sponsor">
                    <h3>合作媒体</h3>
                    <?php
 foreach ($media_all_list[4]['pic_list'] as $k=>$v) { ?>
                    <?php if(!empty($v['link_url'])){ ?><a href="<?php echo $v['link_url'];?>" target="_blank"><?php } ?><img class="center-block" src="<?php echo STATICSCDN.$v['pic_show'];?>" alt=""><?php if(!empty($v['link_url'])){ ?></a><?php } ?>
                    <?php
 } ?>
            </div>
            <?php
 } ?>



        </div>
    </div>

   <!--  <div class="row mt20">
        <div class="col-main">
            <div class="row">
                <div class="col-md-6">
                    <div class="reg-box">
                        <div class="hd">
                            我要报名<span>REGISTER</span>
                            <i></i>
                        </div>
                        <div class="bd">
                            <div class="row">
                                <div class="col-xs-6"><a href="#">全程马拉松</a></div>
                                <div class="col-xs-6"><a href="#">半程马拉松</a></div>
                                <div class="col-xs-6"><a href="#">迷你马拉松</a></div>
                                <div class="col-xs-6"><a href="#">志愿者</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="game-info">
                            <div class="row">
                                <div class="col-xs-6"><a href="#"><img src="<?php echo STATICSPATH;?>images/pic_map.png" alt=""></a></div>
                                <div class="col-xs-6"><a href="#"><img src="<?php echo STATICSPATH;?>images/pic_qa.png" alt=""></a></div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- <div class="row mt20">
        <div class="col-main notice-group">
            <div class="row">
                <div class="col-md-4"><div class="tit">报名查询<span>NOTICE</span></div></div>
                <div class="col-md-6">
                        <div class="input-group">
                          <span class="input-group-btn input-name"><input type="text" class="form-control" placeholder="请输入姓名"></span>
                          <input type="text" class="form-control" placeholder="请输入证件号/参赛号">
                          <span class="input-group-btn"><button class="btn btn-default" type="button">查询</button></span>
                        </div>
                </div>
            </div>
        </div>
    </div> -->
    <!--    <div class="row mt20">
        <div class="col-main">
            <div class="swiper-container swiper-img">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><a href="#"><img src="<?php echo STATICSPATH;?>images/img01.jpg" alt=""></a></div>
                    <div class="swiper-slide"><a href="#"><img src="<?php echo STATICSPATH;?>images/img01.jpg" alt=""></a></div>
                    <div class="swiper-slide"><a href="#"><img src="<?php echo STATICSPATH;?>images/img01.jpg" alt=""></a></div>
                    <div class="swiper-slide"><a href="#"><img src="<?php echo STATICSPATH;?>images/img01.jpg" alt=""></a></div>
                    <div class="swiper-slide"><a href="#"><img src="<?php echo STATICSPATH;?>images/img01.jpg" alt=""></a></div>
                    <div class="swiper-slide"><a href="#"><img src="<?php echo STATICSPATH;?>images/img01.jpg" alt=""></a></div>
                    <div class="swiper-slide"><a href="#"><img src="<?php echo STATICSPATH;?>images/img01.jpg" alt=""></a></div>
                    <div class="swiper-slide"><a href="#"><img src="<?php echo STATICSPATH;?>images/img01.jpg" alt=""></a></div>
                </div>
                <div class="swiper-button-next  swiper-button-white"></div>
                <div class="swiper-button-prev  swiper-button-white"></div>
            </div>
        </div>
    </div>
    -->
    <!-- <div class="row mt20">
        <div class="col-main sponsor-box">
            <div class="tit">赞助商<span>SPONSOR</span></div>
            <dl>
                <dd>冠名赞助商</dd>
                <dt>
                    <a href="#"><img src="<?php echo STATICSPATH;?>images/logo01.jpg" alt=""></a>
                    <a href="#"><img src="<?php echo STATICSPATH;?>images/logo01.jpg" alt=""></a>
                    <a href="#"><img src="<?php echo STATICSPATH;?>images/logo01.jpg" alt=""></a>
                    <a href="#"><img src="<?php echo STATICSPATH;?>images/logo01.jpg" alt=""></a>
                </dt>
            </dl>
            <dl>
                <dd>官方战略合作伙伴</dd>
                <dt>
                    <a href="#"><img src="<?php echo STATICSPATH;?>images/logo01.jpg" alt=""></a>
                    <a href="#"><img src="<?php echo STATICSPATH;?>images/logo01.jpg" alt=""></a>
                    <a href="#"><img src="<?php echo STATICSPATH;?>images/logo01.jpg" alt=""></a>
                    <a href="#"><img src="<?php echo STATICSPATH;?>images/logo01.jpg" alt=""></a>
                </dt>
            </dl>
        </div>
    </div> -->

</div>




<script src="<?php echo STATICSPATH;?>scripts/lib/swiper.jquery.min.js" type="text/javascript"></script>
<script src="<?php echo STATICSPATH;?>scripts/lib/projekktor/projekktor-1.3.09.min.js" type="text/javascript"></script>

<script src="<?php echo STATICSPATH;?>scripts/app/home.js?v=01" type="text/javascript"></script>

<script type="text/javascript">
    $(function(){

        projekktor('#player_a', {
                    poster: "<?php echo STATICSCDN.$video_list[0]['pic_b_show'];?>",
                    imageScaling: 'fill',
                    title: "视频",
                    playerFlashMP4: '<?php echo STATICSPATH;?>scripts/lib/projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
                    playerFlashMP3: '<?php echo STATICSPATH;?>scripts/lib/projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
                    width: '100%',
                    height: '100%',
                    playlist: [
                        {
                            0: {src: "<?php echo STATICSCDN.$video_list[0]['filepath'];?>", type: "video/mp4"}
                        }
                    ]
                }, function(player) {} // on ready
        );

    });
</script>


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