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

        typeSection : ".js_show_type_section",
        typeDownloadSection : "#js_show_type_download",
        typeLockSection : "#js_show_type_lock",
        typeSolutionSection : "#js_show_type_solution",
        typeListSection : "#js_show_type_list",
        typeHeadSection : "#js_show_type_head",

        bannerPreview : $('#js_modal_banner_preview'),
        inputBanner : $('#js_modal_banner'),
        inputName : $('#js_modal_name'),
        inputType : $('#js_modal_type'),
        inputDownload : $('#js_modal_download'),
        inputLock : $('#js_modal_lock'),
        inputSolution : $('#js_modal_solution'),
        inputHead : $('#js_modal_head'),
        inputTitle : $('#js_modal_title'),
        inputKeywords : $('#js_modal_keywords'),
        inputDesc : $('#js_modal_description'),

        paramsAddBtn : $('.js_modal_params_add_btn'),
        paramsSection : $('.js_modal_params'),
        paramsCloneNode : $('.jmp_node .js_modal_param',$('.js_modal_params')),
        paramsSectionList : $('.jmp_list',$('.js_modal_params')),

        paramsSubAddBtn : '.js_modal_params_sub_add_btn',
        paramsSubSection : $('.js_modal_sub_params'),
        paramsSubCloneNode : $('.jmp_sub_node .js_modal_sub_param',$('.js_modal_sub_params')),
        paramsSubSectionList : $('.jmp_sub_list',$('.js_modal_sub_params')),

        paramsSubSectionNode : '.js_modal_sub_params',
        paramsSubSectionListNode : '.jmp_sub_list',

        /**
         * 初始化 select event
         */
        initSelectBtn : function () {
            //下载sdk
            init.inputDownload.select2({
                placeholder: "选择sdk下载文件",
                allowClear: true
            });

            //页面类型切换
            init.inputType.unbind().bind('change',function () {
                var val = parseInt($(this).val());
                init.initSwitchTypeShowEvent(val);

            });
        },

        initSwitchTypeShowEvent : function (type) {
            $(init.typeSection).addClass('gone');
            $('.js_modal_item_img_section').removeClass('gone');
            switch (type)
            {
                case 2:
                    $(init.typeLockSection).removeClass('gone');
                    break;
                case 4:
                    $(init.typeSolutionSection).removeClass('gone');
                    break;
                case 11:
                case 12:
                    $(init.typeHeadSection).removeClass('gone');
                    break;
                case 14:
                    $(init.typeDownloadSection).removeClass('gone');
                    break;
                case 15:
                    $('.js_modal_item_img_section').addClass('gone');
                    $(init.typeListSection).removeClass('gone');
                    break;
                case 16:
                    $(init.typeListSection).removeClass('gone');
                    break;
                case 17:
                    $(init.typeListSection).removeClass('gone');
                    break;
            }
        },

        /**
         * 初始化 基本参数事件
         */
        //sub
        getModalSubParamsData : function (section) {
            var objs = $('.js_modal_sub_param',$(init.paramsSubSectionListNode,section));
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
        //sub
        setModalSubParamsData : function (section,data) {
            if(data)
            {
                data.forEach(function (value) {
                    var node = init.paramsSubCloneNode.clone();
                    var tmpObj = $(node).find('input');
                    var tmpSelectObj = $(node).find('select');
                    tmpObj.eq(0).val(value.id);
                    tmpObj.eq(1).val(value.name);
                    tmpObj.eq(2).val(value.url);
                    tmpSelectObj.val(value.target);
                    $(init.paramsSubSectionListNode,section).append(node);
                });
            }
        },
        //index
        initModalParamsEvent : function () {
            init.paramsAddBtn.unbind().bind('click',function () {
                var node = init.paramsCloneNode.clone();
                init.paramsSectionList.append(node);
            });
            $(document).on('click','.jmp_delete',function () {
                $(this).parents('.js_modal_param').remove();
            });

            $(document).on('click',init.paramsSubAddBtn,function () {
                var node = init.paramsSubCloneNode.clone();
                $(this).parents('.js_modal_params_sub_section').find('.jmp_sub_list').append(node);
            });
            $(document).on('click','.jmp_sub_delete',function () {
                $(this).parents('.js_modal_sub_param').remove();
            });
        },
        //index
        getModalParamsData : function () {
            var objs = $('.js_modal_param',init.paramsSectionList);
            var len = objs.length;
            var res  = [];
            if(len)
            {
                objs.each(function (key) {
                    var tmpObj = $(this).find('input');
                    var tmpSelectObj = $(this).find('select');
                    var tmpTextAreObj = $(this).find('textarea');
                    var params = {
                        id:  $.trim(tmpObj.eq(0).val()),
                        title:  $.trim(tmpObj.eq(1).val()),
                        sub_title:  $.trim(tmpObj.eq(2).val()),
                        pic:  $.trim(tmpObj.eq(3).val()),
                        position:  $.trim(tmpSelectObj.val()),
                        content:  $.trim(tmpTextAreObj.val()),
                        links:  init.getModalSubParamsData($(this)),
                        sort_num:  key+1
                    };
                    if(params.content.length){
                        res.push(params);
                    }
                });
            }
            return res;
        },
        //index
        setModalParamsData : function (data) {
            if(data)
            {
                data.forEach(function (value) {
                    var node = init.paramsCloneNode.clone();
                    var tmpObj = $(node).find('input');
                    var tmpSelectObj = $(node).find('select');
                    var tmpTextAreObj = $(node).find('textarea');

                    tmpObj.eq(0).val(value.id);
                    tmpObj.eq(1).val(value.title);
                    tmpObj.eq(2).val(value.sub_title);
                    tmpObj.eq(3).val(value.pic);
                    tmpSelectObj.val(value.position);
                    tmpTextAreObj.val(value.content);
                    if(value.links){
                        init.setModalSubParamsData($(node),value.links);
                    }

                    init.paramsSectionList.append(node);
                });
            }
        },
        clearModalParamsData : function () {
            init.paramsSectionList.html('');
            init.paramsSubSectionList.html('');
        },

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));
            var host = document.global_config_data.img_host;

            init.inputName.val(data.name);
            init.inputBanner.val(data.banner);
            init.bannerPreview.attr('src',host+data.banner).removeClass('gone');
            init.inputType.val(data.page_type_id).prop('disabled',true);

            init.inputTitle.val(data.title);
            init.inputKeywords.val(data.keywords);
            init.inputDesc.val(data.description);


            var typeId= parseInt(data.page_type_id);
            switch (typeId)
            {
                case 2://加密锁详情
                    init.inputLock.val(data.extra);
                    break;
                case 4://解决方案
                    init.inputSolution.val(data.extra);
                    break;
                case 11://只是产权
                case 12://公司性质
                    init.inputHead.val(data.extra);
                    break;
                case 14://文件列表
                    init.inputDownload.select2('val',data.extra.split(','));
                    break;
                case 15://文本列表
                    $('.js_modal_item_img_section').addClass('gone');
                case 16://图文分离
                case 17://图文混合
                    if(data.contents){
                        init.setModalParamsData(data.contents);
                    }
                    break;
            }
            init.initSwitchTypeShowEvent(typeId);
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));

            init.inputTitle.val('');
            init.inputKeywords.val('');
            init.inputDesc.val('');

            init.inputBanner.val('');
            init.bannerPreview.attr('src','').addClass('gone');
            init.inputName.val('');
            init.inputHead.val('');
            init.inputType.val('').prop('disabled',false);
            init.inputDownload.select2('val','');
            $(init.typeSection).addClass('gone');
            $('.js_modal_item_img_section').removeClass('gone');
            init.clearModalParamsData();
        },

        /**
         * 上传文件
         */
        initUploadEvent:function () {
            $(document).on('click',init.uploadImageBtn,function(){
                var $this = $(this);
                var modal = $(init.uploadProgressModal);
                $(this).uploadImage('/upload/upload-image',{request_type:'ajax'},function (data) {
                    var id = $this.data('id');
                    var parent = $this.data('parent');
                    if(data.res){
                        var parentObj = '';
                        if(parent.length){
                            parentObj = $this.parents(parent);
                        }
                        $(id,parentObj).val(data.path);
                        $(id+'_preview',parentObj).attr('src',data.url).removeClass('gone');
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
                        title:$.trim(init.inputTitle.val()),
                        keywords:$.trim(init.inputKeywords.val()),
                        description:$.trim(init.inputDesc.val()),
                        banner:$.trim(init.inputBanner.val()),
                        page_type_id:parseInt($.trim(init.inputType.val())),
                        url:$('#js_modal_type').find('option:checked').data('url'),
                        download_ids:$.trim(init.inputDownload.select2('val')),
                        lock_id:$.trim(init.inputLock.val()),
                        solution_id:$.trim(init.inputSolution.val()),
                        head:$.trim(init.inputHead.val()),
                        contents:init.getModalParamsData(),
                    };

                    var checkMap = {
                        banner:'请上传banner 图',
                        name:'请输入单页名称',
                        page_type_id:'请选择单页类型',
                    };

                    for (var key in checkMap){
                        if(!params[key] || params[key].length<1){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }
                    switch (params.page_type_id)
                    {
                        case 2://加密锁详情
                            params.extra = params.lock_id;
                            break;
                        case 4://解决方案
                            params.extra = params.solution_id;
                            break;
                        case 11://只是产权
                        case 12://公司性质
                            params.extra = params.head;
                            break;
                        case 14://文件列表
                            if(!params.download_ids || params.download_ids.length<1){
                                $.showToast('请选择下载文件',false);
                                return false;
                            }
                            params.extra = params.download_ids;
                            break;
                        case 15://文本列表
                            if(!params.contents || params.contents.length<1){
                                $.showToast('请添加列表内容',false);
                                return false;
                            }
                            break;
                        case 16://图文分离
                            if(!params.contents || params.contents.length<1){
                                $.showToast('请添加列表内容',false);
                                return false;
                            }
                            break;
                        case 17://图文混合
                            if(!params.contents || params.contents.length<1){
                                $.showToast('请添加列表内容',false);
                                return false;
                            }
                            break;
                    }

                    $.wpost('/page/update-page-ajax',params,function(res){
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
                    $.wpost('/page/delete-page-ajax',{id:id},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });
            /**
             * 上升 下降
             */
            var sortBtnEvent = function ($this,$parent) {
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
            };
            
            $(document).on('click','.js_up,.js_down',function () {
                var $this=$(this);
                var $parent=$($this.parents('.js_modal_param')[0]);
                sortBtnEvent($this,$parent);

            });
            $(document).on('click','.js_sub_up,.js_sub_down',function () {
                var $this=$(this);
                var $parent=$($this.parents('.js_modal_sub_param')[0]);
                sortBtnEvent($this,$parent);
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
