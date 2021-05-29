 
jQuery.com = {

    alert: {
        _alert: null,
        _callback: null,
        init: function(sizeClass, customCss)
        {
            if(this._alert) return;

            var self = this;

            var strSize = '';
            if(sizeClass == undefined) {
                strSize = 'modal-sm';
            }else{
                strSize = sizeClass;
            }
            var strCustomCss = '';
            if(strCustomCss != undefined) strCustomCss = customCss;

            var str =
                '<div id="myAlert" class="modal" tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog '+strSize+' '+strCustomCss+'" role="document">' +
                        '<div class="modal-content">' +
                            '<div class="modal-header">' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '<h4 class="modal-title">提示</h4>' +
                            '</div>' +
                            '<div class="modal-body text-center"></div>' +
                            '<div class="modal-footer">' +
                                '<button type="button" class="btn btn-primary btn-ok" data-dismiss="modal" aria-label="Close">确定</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

            $('body').append(str);

            var $a = $("#myAlert");

            $(".btn-ok", $a).click(function(e){
                if(self._callback) self._callback();
            });

            $.com.modalMiddle($a);

            this._alert = $a;

            return true;
        },
        open: function(text, title, callback)
        {
            if(!text) return false;

            if(!title) title = '提示';

            if(!this._alert) this.init();

            $(".modal-title", this._alert).html(title);
            $(".modal-body", this._alert).html(text);

            this._alert.modal();

            if(callback) this._callback = callback;
            else this._callback = null;
        },
        close: function(callback)
        {
            if(!this._alert) this.init();

            this._alert.modal('hide');

            if(callback) callback();
        }
    },

    confirm: {
        _alert: null,
        _callback: null,
        init: function()
        {
            if(this._alert) return;

            var _this = this;

            var str =
                '<div id="myConfirm" class="modal" tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog modal-sm" role="document">' +
                        '<div class="modal-content">' +
                            '<div class="modal-header">' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '<h4 class="modal-title">确认</h4>' +
                            '</div>' +
                            '<div class="modal-body text-center"></div>' +
                            '<div class="modal-footer">' +
                                '<button type="button" class="btn btn-info btn-cancel" data-dismiss="modal" aria-label="Close">取消</button>' +
                                '<button type="button" class="btn btn-primary btn-ok">确定</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

            $('body').append(str);

            var $a = $("#myConfirm");

            $(".btn-ok", $a).click(function(e){
                _this._alert.modal('hide');
                if(_this._callback) _this._callback();
            });

            $.com.modalMiddle($a);

            this._alert = $a;

            return true;
        },
        open: function(text, title, callback)
        {
            var _this = this;

            if(!text) return false;

            if(!title) title = '确认';

            if(!this._alert) this.init();

            $(".modal-title", this._alert).html(title);
            $(".modal-body", this._alert).html(text);

            $(".btn-cancel", this._alert).focus();

            this._alert.modal();

            if(callback) this._callback = callback;
            else this._callback = null;
        },
        close: function(callback)
        {
            if(!this._alert) this.init();

            this._alert.modal('hide');

            if(callback) callback();
        }
    },

    getUserAgent: function()
    {
        var Sys = {};
        var ua = navigator.userAgent.toLowerCase();
        var s;
        (s = ua.match(/rv:([\d.]+)\) like gecko/)) ? Sys.ie = s[1] :
            (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
                (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
                    (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
                        (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
                            (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;

        return Sys;

        /*if (Sys.ie) $('span').text('IE: ' + Sys.ie);
         if (Sys.firefox) $('span').text('Firefox: ' + Sys.firefox);
         if (Sys.chrome) $('span').text('Chrome: ' + Sys.chrome);
         if (Sys.opera) $('span').text('Opera: ' + Sys.opera);
         if (Sys.safari) $('span').text('Safari: ' + Sys.safari);*/
    },

    initImgGray:function($img)
    {
        var userAgent = $.com.getUserAgent();
        if(userAgent.ie){
            var version = parseInt(userAgent.ie);
            if(version == 11 || version == 10){
                if($img.length){
                    grayscale($img);
                }
            }
        }
    },

    modalMiddle: function($container)
    {
        /*$container.on('show.bs.modal', function (e) {

            var $dialog = $('.modal-dialog', $container);
            $(this).css('display', 'block');
            $dialog.css({
                'margin-top': parseInt(($(window).height() - $dialog.height())/2)
            });

        });*/
        $container.on('shown.bs.modal', function (e) {

            var $dialog = $('.modal-dialog', $container);
            $dialog.css({
                'margin-top': parseInt(($(window).height() - $dialog.height())/2)
            });

        });
    },

    initTableCheckbox: function($container)
    {
        $container.on('click', 'tbody .cb [type="checkbox"]', function(e){

            $(this).closest('tr').toggleClass('warning');

        });

        $container.on('click', 'thead .cb [type="checkbox"]', function(e){

            var isChecked = $(this).is(':checked');

            $('tbody >tr', $container).each(function(index, element){

                $('>.cb [type="checkbox"]', element).prop('checked', isChecked);

                if(isChecked){
                    $(element).addClass('warning');
                }else{
                    $(element).removeClass('warning');
                }

            });

        });
    },

    initDivCheckbox: function($container)
    {
        $container.on('click', '.list .cb [type="checkbox"]', function(e){

            $(this).closest('.con').toggleClass('warning');

        });

        $container.on('click', '.title .cb [type="checkbox"]', function(e){

            var isChecked = $(this).is(':checked');

            $('.list .con', $container).each(function(index, element){

                $('.cb [type="checkbox"]', element).prop('checked', isChecked);

                if(isChecked){
                    $(element).addClass('warning');
                }else{
                    $(element).removeClass('warning');
                }

            });

        });
    },

    changeImgOnClick: function($container)
    {
        var $imgBig = $('.img-big', $container);
        var $list = $('.list', $container);

        $('a', $list).on('click', function(e){

            e.preventDefault();

            $imgBig.prop('src', $(this).attr('href'));

            return false;

        });
    },

    wow: function(){
        if (!(/msie [6|7|8|9]/i.test(navigator.userAgent))){
            new WOW().init({
                offset: 100
            });
        }
    },

    IsPC: function() {
        var userAgentInfo = navigator.userAgent;
        var Agents = ["Android", "iPhone",
            "SymbianOS", "Windows Phone",
            "iPad", "iPod"];
        var flag = true;
        for (var v = 0; v < Agents.length; v++) {
            if (userAgentInfo.indexOf(Agents[v]) > 0) {
                flag = false;
                break;
            }
        }
        return flag;
    },

    getCountDownData: function (time)
    {
        var now = new Date();
        //var endDate = new Date( Date.parse( time.replace(/-/g,"/") ) );
        var endDate = new Date(time);
        var leftTime = endDate.getTime() - now.getTime();

        if(leftTime <= 0){
            return {
                count: 0,
                day: '00',
                hour: '00',
                minute: '00',
                second: '00'
            };
        }
        var leftsecond = parseInt(leftTime / 1000);

        //var day1=parseInt(leftsecond/(24*60*60*6));
        var day_t = Math.floor(leftsecond / (60 * 60 * 24));
        var hour = Math.floor((leftsecond - day_t * 24 * 60 * 60) / 3600);
        var minute = Math.floor((leftsecond - day_t * 24 * 60 * 60 - hour * 3600) / 60);
        var second = Math.floor(leftsecond - day_t * 24 * 60 * 60 - hour * 3600 - minute * 60);

        day_t =  day_t >= 10 ? day_t : '0'+day_t;
        hour =  hour >= 10 ? hour : '0'+hour;
        minute =  minute >= 10 ? minute : '0'+minute;
        second =  second >= 10 ? second : '0'+second;

        return {
            count: 1,
            day: day_t,
            hour: hour,
            minute: minute,
            second: second
        };
    },

    initICheck: function($container)
    {
        if($.type($container) === "string"){
            $container = $($container);
        }

        $container.iCheck({
            labelHover : false,
            cursor : true,
            checkboxClass : 'icheckbox_minimal-yellow',
            radioClass : 'iradio_minimal-yellow',
            increaseArea : '20%'
        });
    }

};
