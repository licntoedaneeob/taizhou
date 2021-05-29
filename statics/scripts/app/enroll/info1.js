// JavaScript Document



    function initBirthday()
    {
        $('#birthday').datetimepicker({
            minView: "month",
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayBtn:  1,
            autoclose: 1
        });
    }
    
	initBirthday();