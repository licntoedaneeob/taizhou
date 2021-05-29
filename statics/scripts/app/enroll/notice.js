// JavaScript Document

$(function(){

    var _$btn_submit = $('#btn_submit');
    var _$djsTime = $('#djsTime');
    var _canSubmit = false;
    var _durationTime = parseInt($('>span', _$djsTime).text());//单位秒
    var _remainingTime = _durationTime;
    var _remainingTimer;

    init();

    function init()
    {
        $.com.initICheck('input');
        initSubmit();
        initDjs();
    }

    function initSubmit()
    {
        var $cb_agree = $('#cb_agree');

        _$btn_submit.on('click', function(e){

            if(!_canSubmit)
            {
                return false;
            }

            if(!$cb_agree.is(':checked'))
            {
                $.com.alert.open("请勾选并同意上述条款");
                return false;
            }

        });
    }

    function initDjs()
    {
        _remainingTimer = setInterval(showTime, 1000);
    }

    function showTime()
    {
        if(_remainingTime > 0)
        {
            _remainingTime --;
            $('>span', _$djsTime).text(_remainingTime);
        }
        else
        {
            clearInterval(_remainingTimer);
            _remainingTimer = null;

            _remainingTime = _durationTime;

            _$djsTime.fadeOut();

            _canSubmit = true;
            _$btn_submit.removeClass('disabled');
        }
    }

});

