// JavaScript Document

$(function(){

    var _$nav = $('#navbar');
    var _$topSlider = $('#topSlider');
    var _topSlider;
    var _topSlider_animation = 'slide';

    init();

    function init()
    {
        $(window).resize(function(e){
            resizeWin();
        });
        resizeWin();

        try{
            initTopSlider();
        }catch (e){}

        initSearch();
        initBackToTop();
        initCountDown();
        //initMenuMobile();
        initNavPath();
        initQrcode();
    }

    function resizeWin()
    {
        if($(window).width() <= 767)
        {
            initMenuMobile();

            $(".m-pic", _$topSlider).each(function(index, element) {
                $(element).prop('src', $(this).data('src_s'));
            });

            //_topSlider_animation = 'slide';
            //if(_topSlider) _topSlider.params.effect = 'slide';
        }
        else
        {
            initMenu();

            $(".m-pic", _$topSlider).each(function(index, element) {
                $(element).prop('src', $(this).data('src'));
            });

            //_topSlider_animation = 'fade';
            //if(_topSlider) _topSlider.params.effect = 'fade';
        }
    }

    function initMenu()
    {
        _$nav.off();
        _$nav.on('mouseenter', '.nav > li', function(e){

            $(this).addClass("hover");
            $('.sub', this).stop().slideDown('fast');

        });

        _$nav.on('mouseleave', '.nav > li', function(e){

            $(this).removeClass("hover");
            $('.sub', this).stop().slideUp('fast');

        });
    }

    function initMenuMobile()
    {
        _$nav.off();
        _$nav.on('click', '.nav>li>a', function(e){

            var $item = $(this).parent();
            var $sub = $(this).next();
            if($sub.length){
                if(!$item.hasClass('show-sub'))
                {
                    $('.nav>.show-sub', _$nav).removeClass('show-sub').find('.sub').stop().slideUp('fast');
                    $item.toggleClass('show-sub');
                    $sub.stop().slideToggle('fast');
                }
                else
                {
                    $item.removeClass('show-sub').find('.sub').stop().slideUp('fast');
                }
            }

        });
    }

    function initTopSlider()
    {
        var $container = $('#topSlider');
        var len = $('.swiper-slide', $container).length;
        var loop = len > 1 ? true : false;
        var autoplay = len > 1 ? 8000 : 0;

        if(len <= 1){
            $('.pagination-mc', $container).css('visibility', 'hidden');
        }

        _topSlider = new Swiper('#topSlider .swiper-container',{
            effect: _topSlider_animation,
            loop: loop,
            autoplay : autoplay,
            pagination : '#topSlider .swiper-pagination',
            paginationClickable :true
        });
    }

    function initSearch()
    {
        var $container = $('#searchMain');
        var $form = $('#searchMainForm');
        var $key = $('.key', $container);
        var $btn = $('.btn', $container);
        var $list = $('.notice-list', $container);

        var key;
        $btn.on('click', function(e){

            key = $.trim($key.val());
            if(key == '')
            {
                $.com.alert.open('请输入关键字');
                return false;
            }

            $list.slideDown('fast');

            closeHandler();

        });

        $container.on('click', function(e){

            e.stopPropagation();

        });

        function closeHandler()
        {
            $('body').one('click', function(e){

                $list.slideUp('fast');

            });
        }

        $list.on('click', 'a', function(e){

            e.preventDefault();

            var href = $(this).attr('href');

            var serialize = $form.serialize();

            window.location.href = href + '?' + serialize;

            return false;

        });
    }

    function initBackToTop()
    {
        var $btn = $('#backToTop');
        var isVis = false;

        $btn.click(function(e){

            e.preventDefault();

            $('body,html').scrollTop(0);

        });

        $(window).scroll(function() {

            if( $(window).scrollTop() > 100 )
            {
                if(!isVis)
                {
                    $btn.addClass('active');
                    isVis = true;
                }
            }
            else
            {
                if(isVis)
                {
                    $btn.removeClass('active');
                    isVis = false;
                }
            }

        });
    }

    function initCountDown()
    {
        var endTime = '2017-09-23 08:00';
        var $container = $('#countDown');
        var $day = $('.day', $container);
        var $hour = $('.hour', $container);
        var $minute = $('.minute', $container);
        var $second = $('.second', $container);

        endTime = Date.parse(endTime.replace(/-/g,"/"));

        var data;
        data = $.com.getCountDownData(endTime);

        function show()
        {
            if(data.count == 0)
            {
                clearInterval(timer);
                timer = null;
            }
            else
            {
                data = $.com.getCountDownData(endTime);
            }

            $day.text(data.day);
            $hour.text(data.hour);
            $minute.text(data.minute);
            $second.text(data.second);
        }

        if(data.count == 1)
        {
            var timer = setInterval(show, 1000);
        }
        show();

        //var timestamp = Date.parse(new Date(time));
    }

    function initNavPath()
    {
        var $loadCurPos = $('#loadCurPos');
        if($loadCurPos.length)
        {
            $('body').scrollTop($loadCurPos.offset().top - 30);
        }
    }

    function initQrcode()
    {
        $('#qrcode').on('click', '.btn-close', function(e){

            e.preventDefault();

            $(this).closest('div').remove();

        });
    }

});

