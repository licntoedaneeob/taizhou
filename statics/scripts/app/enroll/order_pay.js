// JavaScript Document

$(function(){

    init();

    function init()
    {
        initPayButton();
    }

    function initPayButton()
    {
        var $container = $('#payButtonMc');
        $container.on('click', 'a', function(e){

            e.preventDefault();
            $('a.active', $container).removeClass('active');
            $(this).addClass('active');

        });
    }

});

