(function($){
    /**
     * 初始化框架js
     */
    // initiate layout and plugins
    Metronic.init(); // init metronic core componets
    Layout.init(); // init layout

    /**
     * 全局修改密码modal
     */

    $('.globalEditPassword').unbind().wclick(function(){
        var modal = $('#globalEditPasswordModal');
        modal.modal();
        var submitBtn=modal.find('#gepm-confirm');
        modal.find('#gepm-opass').val('');
        modal.find('#gepm-npass').val('');
        modal.find('#gepm-rpass').val('');
        submitBtn.unbind().wclick(function(){
            var data={
                oldPass: $.trim(modal.find('#gepm-opass').val()),
                newPass: $.trim(modal.find('#gepm-npass').val()),
                confirmPass: $.trim(modal.find('#gepm-rpass').val()),

            };
            for(var item in data) {
                var len=data[item].length;
                if(len<1 || len>50){
                    $.showToast('密码长度为1-50个字符',false);
                    return false;
                }
            }
            if(data.newPass!=data.confirmPass){
                $.showToast('新密码不一致',false);
                return false;
            }
            $.wpost('/index/reset-password-ajax',data,function(res){
                modal.modal('hide');
            });
        });
    });



})(jQuery);