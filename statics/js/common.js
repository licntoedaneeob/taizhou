

$(function(){
    $( ".btn-search" ).click(function() {
        $( "#dropdown-search" ).slideToggle( "slow");
    });
       

    //hover展开收起
     var menu_ul = $('.faq-grid .bd li p'),
         menu_a  = $('.faq-grid .bd li h4');
        
        menu_ul.hide();
    
        menu_a.click(function(e) {
            e.preventDefault();
            if(!$(this).hasClass('active')) {
                menu_a.removeClass('active');
                menu_ul.filter(':visible').slideUp('normal');
                $(this).addClass('active').next().stop(true,true).slideDown('normal');
            } else {
                $(this).removeClass('active');
                $(this).next().stop(true,true).slideUp('normal');
            }
        });
    


})