// JavaScript Document

$(function(){

    var _swiper;

    init();

    function init()
    {
        try{
            initSlider();
        }catch(e) {}
    }

    function initSlider()
    {
        var $container = $('#hotSlider');
        var $desc = $('.desc', $container);
        var len = $(".swiper-slide", $container).length;
        var index;

        _swiper = new Swiper('#hotSlider .swiper-container',{
            loop: true,
            autoplay : 8000,
            onSlideChangeStart: function(swiper)
            {
                var n = swiper.activeIndex;
                index = (n > len || n == 0) ? Math.abs(n - len) : n;
                index = index - 1;

                $(".item.active", $desc).addClass('out');
            },
            onSlideChangeEnd: function(swiper)
            {
                $(".item", $desc).hide().removeClass('active out');
                $(".item:eq("+index+")", $desc).show().addClass('active');
            }
        });

        $('.btn-prev', $container).click(function(e){
            _swiper.slidePrev();
        });

        $('.btn-next', $container).click(function(e){
            _swiper.slideNext();
        });
    }

});

