
$(function(){

    var _$btn_submit = $('#btn_submit');
    var _$cb_agree = $('#cb_agree');
    var _$djsTime = $('#djsTime');
    var _canSubmit = false;
    var _durationTime = parseInt($('>span', _$djsTime).text());//单位秒
    var _remainingTime = _durationTime;
    var _remainingTimer;

    init();

    function init()
    {
        initSubmit();
        initDjs();
    }

    function initSubmit()
    {
        _$btn_submit.on('click', function(e){

            if(!_canSubmit)
            {
                return false;
            }

            if(!_$cb_agree.is(':checked'))
            {
                return false;
            }

        });

        _$cb_agree.on('change', function (e) {

            if(_canSubmit)
            {
                if(_$cb_agree.is(':checked'))
                {
                    _$btn_submit.removeClass('disabled');
                }
                else
                {
                    _$btn_submit.addClass('disabled');
                }
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
            
            if(_$cb_agree.is(':checked'))
            {
                _$btn_submit.removeClass('disabled');
            }
        }
    }

});

