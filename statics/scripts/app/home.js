// JavaScript Document

$(function(){

    var _swiperPhoto;
    var _swiperPartner;
    var _slidesPerViewPhoto;
    var _slidesPerViewPartner;

    init();

    function init()
    {
        $(window).resize(function(e){
            resizeWin();
        });
        resizeWin();

        try{
            initPhotoList();
            initVideoSlider();
        }catch(e) {}
    }

    function resizeWin()
    {
        var w = $(window).width();
        if(w <= 991){
            _slidesPerViewPhoto = 3;
        }else{
            _slidesPerViewPhoto = 4;
        }

        if(w <= 767){
            _slidesPerViewPartner = 3;
        }else{
            _slidesPerViewPartner = 6;
        }

        if(_swiperPhoto){
            if(_swiperPhoto.params.slidesPerView != _slidesPerViewPhoto)
                _swiperPhoto.params.slidesPerView = _slidesPerViewPhoto;
        }
        if(_swiperPartner)
        {
            if(_swiperPartner.params.slidesPerView != _slidesPerViewPartner)
                _swiperPartner.params.slidesPerView = _slidesPerViewPartner;
        }
    }

    function initPhotoList()
    {
        var $container = $('#photoList');
        _swiperPhoto = new Swiper('#photoList .swiper-container',{
            loop: true,
            slidesPerView: _slidesPerViewPhoto,
            //slidesPerGroup: slidesPerView,
            autoplay : 8000
        });

        $('.btn-prev', $container).click(function(e){
            _swiperPhoto.slidePrev();
        });

        $('.btn-next', $container).click(function(e){
            _swiperPhoto.slideNext();
        });
    }

    function initVideoSlider()
    {
        //var $container = $('#videoSlider');
        //var len = $(".swiper-slide", $container).length;

        var swiper = new Swiper('#videoSlider .swiper-container',{
            loop: true,
            autoplay : 8000,
            preventClicks : false,
            pagination: '#videoSlider .swiper-pagination',
            paginationClickable: true,
            paginationBulletRender: function (index, className) {
                return '<span class="' + className + '">' + (index + 1) + '</span>';
            }
        });
    }

    function initPartnerList()
    {
        var $container = $('#partnerList');
        _swiperPartner = new Swiper('#partnerList .swiper-container',{
            loop: true,
            slidesPerView: _slidesPerViewPartner,
            //slidesPerGroup: slidesPerView,
            autoplay : 0
        });

        $('.btn-prev', $container).click(function(e){
            _swiperPartner.slidePrev();
        });

        $('.btn-next', $container).click(function(e){
            _swiperPartner.slideNext();
        });
    }

});

