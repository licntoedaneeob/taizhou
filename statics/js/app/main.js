
$(function(){

    init();

    function init()
    {
        initBackToTop();
    }

    function initBackToTop()
    {
        $('body').append('<div id="backToTop" class="btn-back-top"><i class="icon-arrow-up"></i>TOP</div>');

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

});

