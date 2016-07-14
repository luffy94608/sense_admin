/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        pagerObj: '',
        listContainer: $('.js_table_list'),
        uploadImageBtn: '.js_upload_image',
        uploadProgressModal: '#uploadAppProgressModal',

        imgPreview : $('.js_modal_pic_preview'),
        inputName : $('#js_modal_name'),
        inputDownload : $('#js_modal_download'),

        paramsAddBtn : $('.js_modal_params_add_btn'),
        paramsSection : $('.js_modal_params'),
        paramsCloneNode : $('.jmp_node .js_modal_param',$('.js_modal_params')),
        paramsSectionList : $('.jmp_list',$('.js_modal_params')),


        /**
         * 初始化 select 2
         */
        initSelectBtn : function () {
            init.inputDownload.select2({
                placeholder: "选择sdk下载文件",
                allowClear: true
            });
        },
        /**
         * 类型选择事件
         */
        initTypeChangeEvent:function (type) {
            var obj = $('#radio_type_'+type);
            var hrefId= obj.data('id');
            obj.prop('checked',true);
            $('.js_type_section').addClass('gone');
            $(hrefId).removeClass('gone')

        },

        /**
         * 初始化 基本参数事件
         */
        initModalParamsEvent : function () {
            init.paramsAddBtn.unbind().bind('click',function () {
                var node = init.paramsCloneNode.clone();
                init.paramsSectionList.append(node);
                init.initUploadEvent();
            });
            $(document).on('click','.jmp_delete',function () {
                $(this).parents('.js_modal_param').remove();
            });
        },
        getModalParamsData : function () {
            var objs = $('.js_modal_param',init.paramsSectionList);
            var len = objs.length;
            var res  = [];
            if(len)
            {
                objs.each(function (key) {
                    var tmpObj = $(this).find('input');
                    var tmpTextAreObj = $(this).find('textarea');
                    var params = {
                        id:  $.trim(tmpObj.eq(0).val()),
                        name:  $.trim(tmpObj.eq(1).val()),
                        content:  $.trim(tmpTextAreObj.val()),
                        sort_num:  key+1
                    };
                    if(params.name.length && params.content.length){
                        res.push(params);
                    }
                });
            }
            return res;
        },
        setModalParamsData : function (data) {
            if(data)
            {
                data.forEach(function (value) {
                    var node = init.paramsCloneNode.clone();
                    var tmpObj = $(node).find('input');
                    tmpObj.eq(0).val(value.id);
                    tmpObj.eq(1).val(value.name);
                    $(node).find('textarea').val(value.content);
                    init.paramsSectionList.append(node);
                });
            }
        },
        clearModalParamsData : function () {
            init.paramsSectionList.html('');
        },

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));
            init.inputName.val(data.name);
            init.initTypeChangeEvent(data.type)

            init.inputDownload.select2('val',data.download_ids.split(','));
            if(data.params){
                init.setModalParamsData(data.params);
            }
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));

            init.inputDownload.select2('val','');
            init.clearModalParamsData();
            init.initTypeChangeEvent(0)
        },

        /**
         * 上传文件
         */
        initUploadEvent:function () {
            $(init.uploadImageBtn).unbind().bind('click',function(){
                var $this = $(this);
                var modal = $(init.uploadProgressModal);
                $(this).uploadImage('/upload/upload-image',{request_type:'ajax'},function (data) {
                    var id = $this.data('id');
                    var parent = $this.data('parent');
                    if(data.res){
                        $(id,$(parent)).val(data.path);
                        $(id+'_preview',$(parent)).attr('src',data.url).removeClass('gone');
                    }
                    modal.modal('hide');
                    $('.progress-bar',modal).css('width',0);
                },function (percent) {
                    modal.modal({backdrop:'static',keyboard:false});
                    $('.progress-bar',modal).css('width',percent+'%');

                });

            });
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
                        download_ids:$.trim(init.inputDownload.select2('val')),
                        params:init.getModalParamsData(),
                        type:$('.js_radio_type:checked').val()
                    };
                    var checkMap = {
                        name:'请输入描述',
                    };
                    for (var key in checkMap){
                        if(!params[key] || params[key].length<1){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }
                    if(params.type == 1){
                        if(!params.download_ids || params.download_ids.length<1){
                            $.showToast('请选择下载文件',false);
                            return false;
                        }
                    }else{
                        if(!params.params || params.params.length<1){
                            $.showToast('请添加列表内容',false);
                            return false;
                        }
                    }
                    $.wpost('/page/update-page-ajax',params,function(res){
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
                    $.wpost('/page/delete-page-ajax',{id:id},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
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
                target = $(target);
                var bakTarget = target.clone();
                var bakCurrent =  $parent.clone();

                target.replaceWith(bakCurrent);
                $parent.replaceWith(bakTarget);
            });




        },
        run : function () {
            init.initSelectBtn();
            init.initUploadEvent();
            init.initModalParamsEvent();
            init.initBtnEvent();
        }
    };

    init.run();
});
