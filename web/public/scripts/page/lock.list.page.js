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

        inputVersion : $('#js_modal_version'),
        inputType : $('#js_modal_type'),
        inputShopUrl : $('#js_modal_shop_url'),
        inputStatus : $('#js_modal_status'),
        inputDownload : $('#js_modal_download'),
        inputImg : $('#js_modal_img'),
        inputDesc : $('#js_modal_desc'),
        inputFeature : $('#js_modal_feature'),

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
                    var params = {
                      id:  $.trim(tmpObj.eq(0).val()),
                      key:  $.trim(tmpObj.eq(1).val()),
                      value:  $.trim(tmpObj.eq(2).val()),
                      desc:  $.trim(tmpObj.eq(3).val()),
                      sort_num:  key+1
                    };
                    res.push(params);
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
                    tmpObj.eq(1).val(value.param_1);
                    tmpObj.eq(2).val(value.param_2);
                    tmpObj.eq(3).val(value.param_3);
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
            var pic = '';
            if(data.pic){
                pic = document.global_config_data.img_host + data.pic;
            }
            init.imgPreview.attr('src',pic).removeClass('gone');

            init.inputVersion.val(data.version);
            init.inputShopUrl.val(data.shop_url);
            init.inputType.val(data.lock_type_id);
            if(data.try_status==1){
                init.inputStatus.prop('checked',true);
            }
            init.inputDownload.select2('val',data.download_ids.split(','));
            init.inputImg.val(data.pic);
            init.inputDesc.val(data.description);
            init.inputFeature.val(data.feature);
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
            init.imgPreview.addClass('gone').attr('src','');
            init.inputVersion.val('');
            init.inputType.val('');
            init.inputShopUrl.val('');
            init.inputStatus.prop('checked',false);
            init.inputDownload.select2('val','');
            init.inputImg.val('');
            init.inputDesc.val('');
            init.inputFeature.val('');
            init.clearModalParamsData();
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
                        version:$.trim(init.inputVersion.val()),
                        type_id:$.trim(init.inputType.val()),
                        shop_url:$.trim(init.inputShopUrl.val()),
                        status:init.inputStatus.prop('checked') ? 1 : 0,
                        download_ids:$.trim(init.inputDownload.select2('val')),
                        pic:$.trim(init.inputImg.val()),
                        desc:$.trim(init.inputDesc.val()),
                        feature:$.trim(init.inputFeature.val()),
                        params:init.getModalParamsData()
                    };
                    var checkMap = {
                        version:'请输入版本名称',
                        type_id:'请选择产品类别',
                        download_ids:'请选择sdk下载文件',
                        pic:'请上传产品图',
                        desc:'请输入产品信息',
                        feature:'请输入产品特点',
                        params:'请添加基本参数',
                    };

                    for (var key in checkMap){
                        if(!params[key] || params[key].length<1){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }
                    $.wpost('/lock/update-lock-ajax',params,function(res){
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
                    $.wpost('/lock/delete-lock-ajax',{id:id},function(){
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

            /**
             * 上升 下降
             */
            $(document).on('click','.js_up,.js_down',function () {
                var $this=$(this);
                var $parent=$($this.parents('.js_modal_param')[0]);
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

        },
        run : function () {
            init.initSelectBtn();
            init.initModalParamsEvent();
            init.initBtnEvent();
        }
    };

    init.run();
});
