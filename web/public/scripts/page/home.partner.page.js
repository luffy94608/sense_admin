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

        inputUrl : $('#js_modal_url'),
        inputImg : $('#js_modal_img'),

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));
            var img = '';
            if(data.logo){
                img = document.global_config_data.img_host + data.logo;
            }
            init.imgPreview.attr('src',img).removeClass('gone');
            init.inputUrl.val(data.url);
            init.inputImg.val(data.logo);
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.imgPreview.addClass('gone').attr('src','');
            init.inputUrl.val('');
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
                var submitBtn = init.modal.find('.submit');
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.attr('data-id');
                var info=$parent.attr('data-info');
                if(id && info){
                    info= JSON.parse(info);
                    init.setModalFormData(info);
                }

                init.modal.modal();
                submitBtn.unbind().bind('click',function () {
                    var params = {
                        id:id,
                        logo:$.trim(init.inputImg.val()),
                        url:$.trim(init.inputUrl.val()),
                    };

                    if(!params.logo){
                        $.showToast('请上传图片',false);
                        return false;
                    }
                    $.wpost('/home/update-partner-ajax',params,function(res){
                        if(!params.id){
                            init.listContainer.prepend(res)
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
                    $.wpost('/home/delete-partner-ajax',{id:id},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });


            /**
             * 上传图片
             */
            $('.js_upload_image').unbind().bind('click',function () {
                $(this).uploadImage('/upload/upload-image',{request_type:'ajax'},function (data) {
                    if(data.path){
                        init.inputImg.val(data.path);
                        init.imgPreview.attr('src',data.img).removeClass('gone');
                    }
                    init.progress.addClass('gone');
                },function (percent) {
                    if(percent>=0){
                        init.progress.removeClass('gone');
                    }
                    init.progressBar.css('width',percent+'%');
                });
            });

            /**
             * 上升 下降
             */
            $(document).on('click','.js_up,.js_down',function () {
                var $this=$(this);
                var $parent=$($this.parents('.js_modal_param')[0]);
                if($parent.length == 0){
                    $parent=$($this.parents('tr'));
                }
                var target;

                if($this.hasClass('js_up')){
                    target = $parent.prev()[0];
                }else{
                    target = $parent.next()[0];
                }
                if(!target){
                    return false;
                }
                if($this.hasClass('js_up')){
                    $(target).before($parent);
                }else{
                    $(target).after($parent);
                }
            });

            /**
             * 保存排序
             */
            $(document).on('click','.js_sort_save',function () {
                var trNodes = $('.js_table_list tr');

                var params = [];
                for (var i=0;i<trNodes.length;i++){
                    var tmpNode = $(trNodes[i]);
                    var tmpId = tmpNode.data('id');
                    if(tmpId)
                    {
                        var tmpData = {
                            id:tmpId,
                            sort_num:i+1,
                        };
                        params.push(tmpData);
                    }

                }
                if(params.length<1){
                    $.showToast('无可排序的数据',false);
                    return false;
                }
                $.wpost('/home/save-partner-sort-ajax',{params:params},function(){
                    $.showToast('保存成功',true);
                });
            });
        },
        run : function () {
            init.initBtnEvent();
        }
    };

    init.run();
});
