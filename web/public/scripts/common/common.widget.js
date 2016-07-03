(function($){

    $.log = function(v){
        console.log(v);
    };
	$.fn.wclick = function(callback){ 
		$(this).click(function(e){ 
			if(false === callback.call(this, e))
				e.preventDefault();
		});
	};

    $.confirm=function(opt){
        var options={
            'title':'提示',
            'content':'内容',
            'cancelTitle':'取消',
            'confirmTitle':'确定',
            'success':function(){},
            'cancel':function(){}
        };
        $.extend(options,opt);
        var modal=$('#global_confirm_modal');
        modal.find('.modal-title').html(options.title);
        modal.find('.modal-body').html(options.content);
        modal.find('.confirm_button').unbind().wclick(function(){
            if($.isFunction(options.success)){
                options.success();
            }
            modal.modal('hide');
        });
        modal.find('.cancel_button').unbind().wclick(function(){
            if($.isFunction(options.cancel)){
                options.cancel();
            }
            modal.modal('hide');
        });
        modal.modal();
    };

    $.showToast = function(str,YesOrNo,callback){
        /**
         * 参数1传字符串
         * 参数2传布尔值 true是对勾样式，false是叉子样式。其他和不传无样式
         * 默认不允许传html
         */
        var st = {
            toastID:'toast-new',
            dur:2000
        }
        if( typeof str !== 'string' ){
            throw 'Invalid param of $.showToast';
            return;
        }

        if( $('#'+st.toastID).length == 0 ){
            var div = '<section id="'+st.toastID+'" class="gone">\
                <div class="toast-box">\
                </div>\
            </section>';
            $('body').append(div);
        }

        //如果允许传html就把这句删了
//        str  = str.getLegalStr();
        var $toast = $('#'+st.toastID);
        var $box = $toast.find('.toast-box');
        var init = function(){
            $box.empty().append('<p>');
        }
        var setPosition = function(){
            var h = $(window).height();
            var top = h/2 - 30;
            $toast.css('top',top+'px');
        }
        var toastFlash = function(time){
            $toast.fadeIn(300);
            setTimeout(function(){
                $toast.fadeOut(300);
                if($.isFunction(callback)){
                    setTimeout(callback,300);
                }
            },time)
        }
        var checkSpecialStatus = function(){
            if( typeof YesOrNo == 'boolean' ){
                if( YesOrNo ){
                    $box.find('p').addClass('yea');
                }else{
                    $box.find('p').addClass('nay');
                }
            }
        }

        init();
        checkSpecialStatus();
        $box.find('p').append(str);
        setPosition();
        toastFlash(st.dur);
    };
    /**
     * 全局重新登录modal
     */
    $.initLoginDialog=function(){
        var modal = $('#global_login_modal');
        modal.find('#glm_name').val('');
        modal.find('#glm_password').val('');
        modal.modal();
        var submitBtn=modal.find('.confirm_button');
        submitBtn.unbind().wclick(function(){
            var data={
                account: $.trim(modal.find('#glm_name').val()),
                password: $.trim(modal.find('#glm_password').val())
            };
            if(!data.account){
                $.showToast('请输入用户名',false);
                return false;
            }
            if(!data.password){
                $.showToast('请输入密码',false);
                return false;
            }
            $.wpost('/index/check-password',data,function(res){
                modal.modal('hide');
            });
        });
    };

    $.wpost = function(url, data, callback,failback,withoutLoading){
        var loadingBody=$('body');
        if(data == null)
            data = {};
        data.request_type = 'ajax';

        if (!withoutLoading){
            $('body').modalmanager('loading');
            //Metronic.blockUI({
            //    target: loadingBody,
            //    animate: true,
            //    overlayColor: '#666'
            //});
        }

        $.log(">>>>>>>>>>>>>"+url+">>>>>>>>>>>>>>>");

        jQuery.ajax({type: 'post',url: url,data: data,
            success: function(res){
                $.log('===========');
                $.log(res);
                if (!withoutLoading){
                    //Metronic.stopPageLoading();
                    $('body').modalmanager('removeLoading');
                    //Metronic.unblockUI(loadingBody);
                }
                if(res.code == 0){
                    if($.isFunction(callback))
                        callback(res.data);
                }else{
                    if (parseInt(res.code) == -100){
                        $.showToast( res.desc,false);
                        return;
                    }
                    if (parseInt(res.code) == -302){
                        $('#global_login_modal').modal();
                        $.initLoginDialog();
                        //window.location.href='/index/login';
                        return;
                    }
                    if($.isFunction(failback))
                        failback(res);
                    else
                        $.showToast( res.desc,false);
                }
            },
            error:function(request, textStatus, err){
                if (textStatus){
                    var extra = textStatus + '<br>status:'+ request.status + ' ' + request.statusText + '<br>';
                    $.showToast('服务器错误',false);
                } else {
                    $.showToast(err.description,false);
                }
                if (!withoutLoading){
                    $('body').modalmanager('removeLoading');
                    //Metronic.unblockUI(loadingBody);
                }
            },
            dataType: 'json'});
    };

    $.fn.uploadImage = function(action,data,func,progressFunc){
        $(this).next('input[type="file"]').remove();
        var input = $(' <input style="display:none;" type="file" name="file"/>');
        var _this = $(this);
        input.change(function(){
            if(/image/i.test(this.files[0].type)){
                var xhr = new XMLHttpRequest();
                _this.data("_sid",'_sid_'+Math.random());
                xhr.upload.onprogress = function(e) {
                    var ratio = e.loaded / e.total;
                    var percent = ratio*100;
                    if($.isFunction(progressFunc)){
                        progressFunc(percent);
                    }
                    $("#" + _this.data("_sid")).html(percent + "%");
                };
                xhr.onload = function(){
                    if(this.status  == 200){
                        if(func){
                            func($.parseJSON(this.responseText).data);
                        }
                    }
                    $("#" + _this.data("_sid")).remove();
                };
                xhr.open("POST", action, true);
                var fd = new FormData();
                fd.append('image',this.files[0]);

                if (data){
                    for(var i in data){
                        fd.append(i, data[i]);
                    }
                }

                xhr.send(fd);
            }else{
                alert('请选择图片');
                $(this).click();
            }
        });
        $(this).after(input);
        input.click();
    };

    Date.prototype.Format = function(fmt){ //author: meizz
        var o = {
            "M+" : this.getMonth()+1,                 //月份
            "d+" : this.getDate(),                    //日
            "h+" : this.getHours(),                   //小时
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        return fmt;
    };

    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };

    $.fn.Pager = function(opts) {
        var st = {
            url: null,
            currentPage : 0,
            protocol: null,
            listSize: 20,
            onPageInitialized:null,
            onCountChanged:null,
            wrapUpdateData:null
        };

        var $this = $(this);
        $.extend(st, opts);

        $this.currentIdx = function(){
            return st.currentPage;
        };

        $this.initTabPage = function(){
            var count = $this.find("#list_count").html();
            var pagerCount = parseInt((parseInt(count) + st.listSize-1) / st.listSize);
//	        if(count > st.listSize){
            var pi = $this.find("#pager_indicator").PagerIndicator({
                url: st.url?st.url:null,
                currentPage: st.currentPage,
                totalCount:count,
                totalPage: pagerCount,
                pageSize: 5,
                onPageChange: function(index){
                    st.currentPage = index;
//	        			$this.find("#list").html(res.html);
                    $this.updateList(index);
                    pi.invalidate();
                }
            });
//	        };
            if($.isFunction(st.onPageInitialized))
                st.onPageInitialized($this);
        };

        $this.setEmpty = function(html){
            $this.find("#list").empty().html(html);
        };
        $this.updateList = function(pageIndex){
            var data = {offset:pageIndex*st.listSize,length:st.listSize};
            if($.isFunction(st.wrapUpdateData))
                data = st.wrapUpdateData(pageIndex,data);
            $.wpost(st.protocol, data ,function(res){
                $this.find("#list_count").html(res.total);
                $this.find("#list").html(res.html);

                if($.isFunction(st.onCountChanged))
                    st.onCountChanged(res.total);
                $this.initTabPage(pageIndex);
            });
        };

        $this.initTabPage();

        return $this;
    };

    $.fn.PagerIndicator = function(opts) {
        var st = {
            url: null,
            totalCount: 0,
            totalPage : 1,
            currentPage : 0,
            pageSize : 3,
            updateWhenInit: false,
            showLast: true,
            onPageChange : null
        };

        var $this = $(this);
        var spanClass = '';
        var numberClass = '';
        var currentClass = "active";
        var BTN = $("<li class='paginate_button'><a href='javascript:void(0);'></a></li>");
        var SPAN = $("<li class='paginate_button' style='padding:0px 5px 0px 0px;'></li>");
        var firstString = '首页', lastString = '末页';
        var prevString = '上一页', nextString = '下一页';

        if (opts != st)
            $.extend(st, opts);

        st.totalPage = Math.max(1,st.totalPage);
        st.currentPage = Math
            .min(Math.max(st.currentPage, 0), st.totalPage - 1);
        if (st.url){
            if (st.url.indexOf('?') > 0){
                st.url = st.url + '&';
            } else {
                st.url = st.url + '?';
            }
        }

        var even = (st.pageSize & 1) ? 0 : 1;
        var half = (st.pageSize - 1) >> 1;

        var lower = Math.max(0, Math.min(st.currentPage + half,
            st.totalPage - 1)
        - st.pageSize + 1);
        var upper = Math.min(st.totalPage - 1, Math.max(st.currentPage - half,
            0)
        + st.pageSize - 1);

        var first = st.currentPage == 0, last = st.currentPage == st.totalPage - 1;
        var preIdx = Math.max(st.currentPage - 1, 0), nextIdx = Math.min(
            st.currentPage + 1, st.totalPage - 1);

        var ITEMS = [ {
            html : firstString,
            title : firstString,
            disabled : first,
            desIdx : 0,
            style : first ? spanClass: numberClass
        } ];
        ITEMS.push({
            html : prevString,
            title : prevString,
            disabled : first,
            desIdx : preIdx,
            style : first ? spanClass: numberClass
        });

        for ( var i = lower; i <= upper; i++) {
            var focused = i == st.currentPage;
            var s = focused ? currentClass : numberClass;
            ITEMS.push({
                html : i + 1,
                title : i + 1,
                style : s,
                disabled : focused,
                desIdx : i,
                isNum : true
            });
        }

        ITEMS.push({
            html : nextString,
            title : nextString,
            disabled : last,
            desIdx : nextIdx,
            style : last ? spanClass: numberClass
        });
        if(st.showLast)
            ITEMS.push({
                html : lastString,
                title : lastString,
                disabled : last,
                desIdx : st.totalPage - 1,
                style : last ? spanClass: numberClass
            });

        $this.empty();

        if (st.totalCount > 0){
            var pageInfo = $('<li class="paginate_button"><div class="page_info"></div></li>').find('.page_info').html('总数<span class="number">'+st.totalCount+'</span>,共<span class="number">'+st.totalPage+'</span>页');
            if (st.totalPage > 1){
                pageInfo.append('<input type="text" placeholder="GO" class="input_go">');
                pageInfo.find('input').keypress(function(e){
                    if (e.keyCode == 13){
                        var page = parseInt($(this).val(), 10);
                        if (st.url){
                            location.href = st.url + "page=" + page;
                        } else {
                            if (0 < page && page <= st.totalPage){
                                if ($.isFunction(st.onPageChange)) {
                                    st.onPageChange(page-1);
                                }
                            }
                        }
                    }
                }) ;
            }
            pageInfo.parent().appendTo($this);
        }
        if (st.totalPage > 1){
            $.each(ITEMS, function(i, data) {
                if (data.disabled && !data.isNum && false) {
                    SPAN.clone().appendTo($this).html(data.html).attr("title",
                        data.title).addClass(data.style);
                } else {
                    if (st.currentPage == data.desIdx){
                        var tmp=BTN.clone().appendTo($this);
                        tmp.addClass(data.style);
                        tmp.find('a').html(data.html).addClass(data.style);
                    } else {
                        if(st.url==null){
                            BTN.clone().appendTo($this).find('a').html(data.html).attr("title",
                                data.title).addClass(data.style).wclick(function() {
//									if (st.currentPage != data.desIdx) {
                                    st.currentPage = data.desIdx;
                                    if ($.isFunction(st.onPageChange)) {
                                        st.onPageChange(data.desIdx);
//                                        alert(data.desIdx);
                                    }
//									}
                                    return false;
                                });
                        }else{
                            BTN.clone().appendTo($this).find('a').html(data.html).attr("title",
                                data.title).addClass(data.style).attr("href", st.url + "page=" + (data.desIdx+1) ).attr("target", "_top");

                        }
                    }
                }
            });
        }

        $this.invalidate = function(){
            $this.PagerIndicator(st);
        };
        if (st.updateWhenInit && $.isFunction(st.onPageChange)) {
            st.onPageChange(st.currentPage);
            // alert(data.desIdx);
        }

        $this.currentPage = function(){
            return st.currentPage;
        };


        return $this;
    };


    $.getByteLength = function(val){
        if (!val) return 0;
        var len = 0;

        for (var i=0; i < val.length; i++) {
            if (val.substr(i,1).match(/[^\x00-\xff]/ig) != null)
                len += 2;
            else
                len += 1;
        }

        return len;
    };
    // input verify
    $.hintFormatter = {
        empty:'{0}为必填项',
        tooShort:'{0}至少{1}个字',
        tooLong:'{0}最多{1}个字',
        bad:'{0}格式不对'
    };
    $.defaultVerifyConfig = {
        name:{
            min:4,
            max:16,
            banSpecial:true,
            title:'真实姓名',
            hasSpecial:function(val){
                var regular = /^[\u4E00-\u9FA5a-zA-Z\s]+$/;
                if (regular.test(val)){
                    var regularS = /^（）[！？。，《》{}【】“”·、：；‘’……]+$/;
                    return regularS.test(val);
                }

                return true;
            },
            test:function(val){
                return true;
            }
        },
        account:{
            min:-1,
            max:1000,
            title:'账号',
            test:function(val){
                return true;
            }
        },
        email:{
            min:-1,
            max:-1,
            title:'邮箱',
            test:function(val){
                var regEmail = /^[a-z0-9]+([._-]*[a-z0-9]+)*@([a-z0-9\-_]+([.\_\-][a-z0-9]+))+$/i;
//				var regEmail = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;
//				var regEmail = /^([\w-_]+(?:\.[\w-_]+)*)@((?:[a-z0-9]+(?:-[a-zA-Z0-9]+)*)+\.[a-z]{2,6})$/i;
                return regEmail.test(val);
            }
        },
        url:{
            min:-1,
            max:-1,
            title:'URL',
            test:function(val){
                var regUrl = /^(https?:\/\/)?(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
                return regUrl.test(val);
            }
        },
        qq:{
            min:5,
            max:15,
            title:'QQ',
            test:function(val){
                var regQQ = /^[1-9][0-9]{4,}$/i;
                return regQQ.test(val);
            }
        },
        mobile:{
            min:-1,
            max:-1,
            title:'手机号',
            test:function(val){
                var regMobile = /^(\+|00)?[0-9\s\-]{3,20}$/;
                return regMobile.test(val);
            }
        },
        phone:{
            min:-1,
            max:-1,
            title:'电话',
            test:function(val){
                var regPhone = /^(\+|00)?[0-9\(\)\-\,\s]{3,20}$/;
                return regPhone.test(val);
            }
        },
        number:{
            min:-1,
            max:-1,
            title:'号码',
            test:function(val){
                var regNumber = /^[0-9]+$/i;
                return regNumber.test(val);
            }
        }
    };

    // state 1:ok 0:empty -1:short -2:bad
    $.checkInputVal = function(opts){
        var st = {
            val:null,
            onChecked:function(value,state,hint){
            },
            type:null,
            showHint:false,
            required:true
        };
        st = $.extend(st,opts);
        st.onChecked = $.isFunction(st.onChecked) ? st.onChecked : function(value,state,hint){};
//		st.type = st.type || $(this).attr('checkType');

        var getErrorHint = function(error,config){
            if (error == 0){
                return $.hintFormatter.empty.format(config.title);
            } else if (error == -1){
                return $.hintFormatter.tooShort.format(config.title,parseInt((config.min + 1) / 2));
            } else if (error == -2){
                return $.hintFormatter.tooLong.format(config.title,parseInt(config.max / 2));
            } else if (error == -3){
                return $.hintFormatter.bad.format(config.title);
            }

            return '';
        };

        var val = st.val;
        var val = jQuery.trim(val);

        var config = $.defaultVerifyConfig[st.type];
        if (!val || val.length == 0){
            st.onChecked(val, 0, getErrorHint(0,config));
            return 0;
        }

        var length = $.getByteLength(val);
        if (config.min > 0 && length < config.min){
            st.onChecked(val, -1, getErrorHint(-1,config));
            return -1;
        }

        if (config.max > 0 && length > config.max){
            st.onChecked(val, -2, getErrorHint(-2,config));
            return -2;
        }

        if (config.banSpecial){
            if ($.isFunction(config.hasSpecial)){
                if (config.hasSpecial(val)){
                    st.onChecked(val, -4, '不能包含特殊字符');
                    return -4;
                }
            } else {
//				var regular= /['.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/i;
                var regular = /^[\u4E00-\u9FA5a-zA-Z0-9\s\.\,\(\)\+#\-]+$/;
                if (!regular.test(val)){
                    st.onChecked(val, -4, '不能包含特殊字符');
                    return -4;
                } else {
                    var regularS = /^[（）！？。，《》{}【】“”·、：；‘’……]+$/;
                    if (regularS.test(val)){
                        st.onChecked(val, -4, '不能包含特殊字符');
                        return -4;
                    }
                }
            }
        }

//		var regular = /^([^\`\+\~\!\#\$\%\^\&\*\(\)\|\}\{\=\"\'\！\￥\……\（\）\——]*[\+\~\!\#\$\%\^\&\*\(\)\|\}\{\=\"\'\`\！\?\:\<\>\尠“\”\；\‘\‘\〈\ 〉\￥\……\（\）\——\｛\｝\【\】\\\/\;\：\？\《\》\。\，\、\[\]\,]+.*)$/;
        if (!config.test(val)){
            st.onChecked(val, -3, getErrorHint(-3,config));
            return -3;
        }


        st.onChecked(val, 1, '');
        return 1;
    };

    /**
     * 初始化 dataTables advanced 公共 插件
     * @param opts
     */
    $.fn.initDataTableWithAdvance = function (opts) {
        var table = $(this);
        var id=table.attr('id');
        //自定义配置文件
        var self_opt={
            'isShowTrDetail':false
        };
        if($.fn.DataTable.TableTools){
            $.extend(true, $.fn.DataTable.TableTools.classes, {
                "container": "btn-group tabletools-btn-group pull-right",
                "buttons": {
                    "normal": "btn btn-sm default",
                    "disabled": "btn btn-sm default disabled"
                }
            });
        }

        //默认配置选项
        var options={
            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": 以升序排列此列",
                    "sortDescending": ": 以降序排列此列"
                },
                "emptyTable": "表中数据为空",
                "info": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "infoEmpty": "没有数据",
                "infoFiltered": "(由 _MAX_ 条数据中检索)",
                "lengthMenu": "显示 _MENU_ 数据",
                "search": "搜索:",
                "zeroRecords": "没有匹配结果",
                "paging": {
                    "first": "首页",
                    "previous": "上一页",
                    "next": "下一页",
                    "last": "末页"
                }
            },
            "ordering": true,//排序总开关
            "columnDefs": [{//哪一列禁止排序
                "orderable": false,
                "targets": []
            }],
            "order": [//定义列表的初始排序设定,为一个2维数组,
                [1, 'asc']
            ],
            "lengthMenu": [
                [5, 10,15, 20, -1],
                [5, 10,15, 20, "全部"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,
            "bStateSave": false // save datatable state(pagination, sort, etc) in cookie.

        };
        $.extend(options,self_opt);
        $.extend(options,opts);
        var oTable = table.dataTable(options);

        /**
         *初始化列表详情
         */
        /* Formatting function for row expanded details */
        function fnFormatDetails(oTable, nTr) {
            var aData = oTable.fnGetData(nTr);
            var sOut = '<table>';
            sOut += '<tr><td>Platform(s):</td><td>' + aData[2] + '</td></tr>';
            sOut += '<tr><td>Engine version:</td><td>' + aData[3] + '</td></tr>';
            sOut += '<tr><td>CSS grade:</td><td>' + aData[4] + '</td></tr>';
            sOut += '<tr><td>Others:</td><td>Could provide a link here</td></tr>';
            sOut += '</table>';

            return sOut;
        }
        /*
         * Insert a 'details' column to the table
         */
        if(options.isShowTrDetail){
            var nCloneTh = document.createElement('th');
            nCloneTh.className = "table-checkbox";

            var nCloneTd = document.createElement('td');
            nCloneTd.innerHTML = '<span class="row-details row-details-close"></span>';

            table.find('thead tr').each(function () {
                this.insertBefore(nCloneTh, this.childNodes[0]);
            });

            table.find('tbody tr').each(function () {
                this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
            });
            /* Add event listener for opening and closing details
             * Note that the indicator for showing which row is open is not controlled by DataTables,
             * rather it is done here
             */
            table.on('click', ' tbody td .row-details', function () {
                var nTr = $(this).parents('tr')[0];
                if (oTable.fnIsOpen(nTr)) {
                    /* This row is already open - close it */
                    $(this).addClass("row-details-close").removeClass("row-details-open");
                    oTable.fnClose(nTr);
                } else {
                    /* Open this row */
                    $(this).addClass("row-details-open").removeClass("row-details-close");
                    oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
                }
            });
        }

        /**
         * 初始化 显示或隐藏某些列
         * @type {*|jQuery|HTMLElement}
         */
        var tableWrapper = $('#'+id+'_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper
        var tableColumnToggler = $('#'+id+'_column_toggler');
        /* modify datatable control inputs */
        //tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown

        /* handle show/hide columns*/
        $('input[type="checkbox"]', tableColumnToggler).change(function () {
            /* Get the DataTables object again - this is not a recreation, just a get of the object */
            var iCol = parseInt($(this).attr("data-column"));
            var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
            oTable.fnSetColumnVis(iCol, (bVis ? false : true));
        });
        return oTable;
    };

})(jQuery);