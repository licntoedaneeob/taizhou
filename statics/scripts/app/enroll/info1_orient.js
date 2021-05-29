// JavaScript Document

$(function(){

    init();

    function init()
    {
        $.com.initICheck('input');
        initBirthday();
    }

    function initBirthday()
    {
        $('#birthday').datetimepicker({
            minView: "month",
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayBtn:  1,
            autoclose: 1
        });
        
        $('#birthday_1').datetimepicker({
            minView: "month",
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayBtn:  1,
            autoclose: 1
        });
        
        $('#birthday_2').datetimepicker({
            minView: "month",
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayBtn:  1,
            autoclose: 1
        });
        
        
        $('#birthday_3').datetimepicker({
            minView: "month",
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayBtn:  1,
            autoclose: 1
        });
        
    }

});

