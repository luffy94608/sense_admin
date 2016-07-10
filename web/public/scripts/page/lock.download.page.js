/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        listContainer: $('.js_table_list'),

        progress: $('.js_progress_section'),
        progressBar: $('.progress-bar'),

        inputTitle : $('#js_modal_title'),
        inputContent : $('#js_modal_content'),
        inputType : $('#js_modal_type'),
        inputBtnName : $('#js_modal_btn_name'),
        inputUrl : $('#js_modal_url'),

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));

            init.inputTitle.val(data.title);
            init.inputBtnName.val(data.btn_name);
            init.inputContent.val(data.content);
            init.inputType.val(data.lock_type_id);
            init.inputUrl.val(data.url);
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.inputTitle.val('');
            init.inputBtnName.val('');
            init.inputContent.val('');
            init.inputType.val(0);
            init.inputUrl.val('');
        },


        /**
         * 初始化按钮时间
         */
        initBtnEvent : function () {

            /**
             * 新建或者编辑
             */
            $(document).on('click','.js_edit',function () {
                init.clearModalFormData();
                var submitBtn = init.modal.find('.js_submit');
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.data('id');
                var info=$parent.data('info');
                if(id && info){
                    init.setModalFormData(info);
                }

                init.modal.modal();
                submitBtn.unbind().bind('click',function () {
                    var params = {
                        id:id,
                        title:$.trim(init.inputTitle.val()),
                        btn_name:$.trim(init.inputBtnName.val()),
                        content:$.trim(init.inputContent.val()),
                        type_id:$.trim(init.inputType.val()),
                        url:$.trim(init.inputUrl.val()),
                    };
                    var checkMap = {
                        title:'请输入名称',
                        btn_name:'请输入下载按钮名称',
                        content:'请填写描述',
                        url:'请上传下载文件',
                    };

                    for (var key in checkMap){
                        if(!params[key]){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }
                    $.wpost('/lock/update-download-ajax',params,function(res){
                        if(!params.id){
                            init.listContainer.append(res)
                        }else{
                            $parent.replaceWith(res)
                        }
                        $.showToast('保存成功',true);
                        init.modal.modal('hide');
                    });
                });
            });

            /**
             * 删除
             */
            $(document).on('click','.js_delete',function () {
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.attr('data-id');
                $.confirm({content:'确认删除吗',success:function(){
                    $.wpost('/lock/delete-download-ajax',{id:id},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });


            /**
             * 上传文件
             */
            $('.js_upload_image').unbind().bind('click',function () {
                $(this).uploadImage('/upload/upload-image',{request_type:'ajax',type:1},function (data) {
                    if(data.res){
                        init.inputUrl.val(data.path);
                    }else{
                        $.showToast(data.desc,false);
                    }

                    init.progress.addClass('gone');
                },function (percent) {
                    if(percent>=0){
                        init.progress.removeClass('gone');
                    }
                    init.progressBar.css('width',percent+'%');
                });
            });
        },
        run : function () {
            init.initBtnEvent();
        }
    };

    init.run();
});
