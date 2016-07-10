/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        listContainer: $('.js_table_list'),

        progress: $('.js_progress_section'),
        progressBar: $('.progress-bar'),
        imgPreview: $('.js_img_preview'),

        inputName : $('#js_modal_name'),
        inputTitle : $('#js_modal_title'),
        inputContent : $('#js_modal_content'),
        inputImg : $('#js_modal_img'),



        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));
            var img = '';
            if(data.img){
                img = document.global_config_data.img_host + data.img;
            }
            init.imgPreview.attr('src',img).removeClass('gone');

            init.inputName.val(data.name);
            init.inputTitle.val(data.title);
            init.inputContent.val(data.content);
            init.inputImg.val(data.img);
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.imgPreview.addClass('gone').attr('src','');
            init.inputName.val('');
            init.inputTitle.val('');
            init.inputContent.val('');
            init.inputImg.val('');
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
                        name:$.trim(init.inputName.val()),
                        title:$.trim(init.inputTitle.val()),
                        content:$.trim(init.inputContent.val()),
                        img:$.trim(init.inputImg.val()),
                    };
                    var checkMap = {
                        name:'请输入类别名称',
                        title:'请输入标题',
                        content:'请填写描述',
                        img:'上传产品图',
                    };

                    for (var key in checkMap){
                        if(!params[key]){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }
                    $.wpost('/lock/update-type-ajax',params,function(res){
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
                    $.wpost('/lock/delete-type-ajax',{id:id},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });


            /**
             * 上传文件
             */
            $('.js_upload_image').unbind().bind('click',function () {
                $(this).uploadImage('/upload/upload-image',{request_type:'ajax'},function (data) {
                    if(data.path){
                        init.inputImg.val(data.path);
                        init.imgPreview.attr('src',data.url).removeClass('gone');
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
