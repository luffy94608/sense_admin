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

        inputTitle : $('.js_modal_title'),
        inputImgPosition : $('.js_modal_position'),
        inputPic : $('.js_modal_img'),
        inputIcon : $('.js_modal_icon'),
        inputIconActive : $('.js_modal_icon_active'),
        inputSubTitle : $('.js_modal_sub_title'),
        inputContent : $('.js_modal_content'),

        paramsAddBtn : $('.js_modal_params_add_btn'),
        paramsSection : $('.js_modal_params'),
        paramsCloneNode : $('.jmp_node .js_modal_param',$('.js_modal_params')),
        paramsSectionList : $('.jmp_list',$('.js_modal_params')),



        /**
         * 初始化 基本参数事件
         */
        initModalParamsEvent : function () {
            init.paramsAddBtn.unbind().bind('click',function () {
                var node = init.paramsCloneNode.clone();
                init.paramsSectionList.append(node);
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
                    var tmpSelectObj = $(this).find('select');
                    var params = {
                        id:  $.trim(tmpObj.eq(0).val()),
                        name:  $.trim(tmpObj.eq(1).val()),
                        url:  $.trim(tmpObj.eq(2).val()),
                        target:  $.trim(tmpSelectObj.val()),
                        sort_num:  key+1
                    };
                    if(params.name.length){
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
                    var tmpSelectObj = $(node).find('select');
                    tmpObj.eq(0).val(value.id);
                    tmpObj.eq(1).val(value.name);
                    tmpObj.eq(2).val(value.url);
                    tmpSelectObj.val(value.target);
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
            init.inputTitle.val(data.title);
            init.inputSubTitle.val(data.sub_title);
            init.inputImgPosition.val(data.position);
            init.inputPic.val(data.pic);
            init.inputIcon.val(data.icon);
            init.inputIconActive.val(data.icon_active);
            init.inputContent.val(data.content);

            var host = document.global_config_data.img_host;
            $(init.inputPic.data('preview')).attr('src',host+data.pic).removeClass('gone');
            $(init.inputIcon.data('preview')).attr('src',host+data.icon).removeClass('gone');
            $(init.inputIconActive.data('preview')).attr('src',host+data.icon_active).removeClass('gone');

            if(data.links){
                init.setModalParamsData(data.links);
            }
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));

            init.inputTitle.val('');
            init.inputSubTitle.val('');
            init.inputImgPosition.val(0);
            init.inputPic.val('');
            init.inputIcon.val('');
            init.inputIconActive.val('');
            init.inputContent.val('');

            $(init.inputPic.data('preview')).attr('src','').addClass('gone');
            $(init.inputIcon.data('preview')).attr('src','').addClass('gone');
            $(init.inputIconActive.data('preview')).attr('src','').addClass('gone');
            init.clearModalParamsData();
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
                    if(data.res){
                        $(id).val(data.path);
                        $(id+'_preview').attr('src',data.url).removeClass('gone');
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
                        title:$.trim(init.inputTitle.val()),
                        sub_title:$.trim(init.inputSubTitle.val()),
                        position:$.trim(init.inputImgPosition.val()),
                        pic:$.trim(init.inputPic.val()),
                        icon:$.trim(init.inputIcon.val()),
                        icon_active:$.trim(init.inputIconActive.val()),
                        content:$.trim(init.inputContent.val()),
                        links:init.getModalParamsData()
                    };

                    var checkMap = {
                        title:'请输入标题',
                        pic:'请上传图片',
                        icon:'请上传icon',
                        icon_active:'请上传icon选中图标',
                        sub_title:'请输入描述',
                        content:'请输入内容',
                    };
                    for (var key in checkMap){
                        if(!params[key] || params[key].length<1){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }

                    $.wpost('/home/update-list-ajax',params,function(res){
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
                    $.wpost('/home/delete-list-ajax',{id:id},function(){
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
                $.wpost('/home/save-list-sort-ajax',{params:params},function(){
                    $.showToast('保存成功',true);
                });
            });



        },
        run : function () {
            init.initUploadEvent();
            init.initModalParamsEvent();
            init.initBtnEvent();
        }
    };

    init.run();
});
