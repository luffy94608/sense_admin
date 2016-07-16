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

        inputName : $('#js_modal_name'),
        inputType : $('#js_modal_type'),
        inputTarget : $('#js_modal_target'),
        inputParent : $('#js_modal_parent'),
        inputShowType : $('#js_modal_show_type'),
        inputBtnTypeNode : '.js_radio_btn_type',
        inputUrl : $('#js_modal_url'),
        inputPage : $('#js_modal_page'),

        typeParams : '',

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
                    var params = {
                        id:  $.trim($(this).find('.js_mp_id').val()),
                        name:  $.trim($(this).find('.js_mp_name').val()),
                        target:  $.trim($(this).find('.js_mp_target').val()),
                        btn_type:  $.trim($(this).find('.js_mp_btn_type').val()),
                        url:  $.trim($(this).find('.js_mp_url').val()),
                        module:$.trim($('.nav-tabs li.active').data('id')),
                        page_id:  $.trim($(this).find('.js_mp_page').val()),
                        sort_num:  key+1
                    };
                    if(params.btn_type == 1){
                        params.url = '';
                    }else{
                        params.page_id = '';
                    }

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
                    $(node).find('.js_mp_id').val(value.id);
                    $(node).find('.js_mp_name').val(value.name);
                    $(node).find('.js_mp_target').val(value.target);
                    $(node).find('.js_mp_btn_type').val(value.btn_type);
                    $(node).find('.js_mp_url').val(value.url);
                    $(node).find('.js_mp_page').val(value.page_id);
                    init.paramsSectionList.append(node);

                    var targetNodes = $(node).find('.js_mp_btn_type_target_sec');
                    targetNodes.addClass('gone');
                    targetNodes.eq(value.btn_type).removeClass('gone');

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
            init.inputType.val(data.type);
            init.initTypeShowEvent(data.type);
            init.inputTarget.val(data.target);
            init.inputParent.val(data.parent_id);
            init.inputShowType.val(data.show_type);
            $(init.inputBtnTypeNode).eq(data.btn_type).prop('checked',true);
            init.initBtnTypeShowEvent(data.btn_type);
            init.inputUrl.val(data.url);
            init.inputPage.val(data.page_id);

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

            init.inputName.val('');
            init.inputType.val(1);
            init.initTypeShowEvent(1);
            init.inputTarget.val('_self');
            init.inputParent.val(0);
            init.inputShowType.val(0);
            $(init.inputBtnTypeNode).eq(0).prop('checked',true);
            init.initBtnTypeShowEvent(0);
            init.inputUrl.val('');
            // init.inputPage.val('');
            var module =$.trim($('.nav-tabs li.active').data('id'));
            var typeSec = $('.js_modal_type_sec');
            var showTypeSec = $('.js_modal_show_type_sec');
            if(module == 1){
                typeSec.addClass('gone');
                showTypeSec.addClass('gone');
            }else{
                typeSec.removeClass('gone');
                showTypeSec.removeClass('gone');
            }

            init.clearModalParamsData();
        },

        initTypeShowEvent:function (val) {
            var obj = $('.js_modal_parent_section');
            var obj2 = $('.js_modal_show_type_sec');

            if(val ==2){
                obj.removeClass('gone');
                obj2.addClass('gone');
            }else{
                obj.addClass('gone');
                var module = $.trim($('.nav-tabs li.active').data('id'));
                if(module == 0){
                    obj2.removeClass('gone');
                }
            }
        },
        initBtnTypeShowEvent:function (val) {
            $('.js_radio_target_section').addClass('gone');
            if(val == 1){
                // $(init.inputBtnTypeNode).eq(1).prop('checked',true);
                $('.js_modal_page_section').removeClass('gone');
            }else{
                // $(init.inputBtnTypeNode).eq(0).prop('checked',true);
                $('.js_modal_url_section').removeClass('gone');
            }
        },


        btnSwitchEvent:function () {
            //btn type
            $(init.inputBtnTypeNode).unbind().bind('change',function () {
                var val = $(this).val();
                init.initBtnTypeShowEvent(val);
            });
            //type
            init.inputType.unbind().bind('change',function () {
                var val = $(this).val();
                init.initTypeShowEvent(val);
            });
            //params item btn type()
            $(document).on('change','.js_mp_btn_type',function () {
                var val = $(this).val();
                var parent = $(this).parents('.js_modal_param');
                $('.js_mp_btn_type_target_sec',parent).addClass('gone');
                if(val == 0){
                    parent.find('.js_mp_url_sec').removeClass('gone');
                }else{
                    parent.find('.js_mp_page_sec').removeClass('gone');
                }
            });
        },
        initTabEvent : function () {
            $('.nav-tabs li').unbind().bind('click',function () {
                init.typeParams = $(this).data('id');
                init.pagerObj.updateList(0);
            });
        },

        /**
         * 详情pager
         */
        initPager : function () {
            init.pagerObj = $('#wrapperPageList').Pager({
                protocol:'/manage/get-menu-list-ajax',
                listSize:20,
                onPageInitialized:function(){
                    if (init.pagerObj){
                        var top=$(window).scrollTop();
                        if(top>100){
                            $(window).scrollTop(0);
                        }
                    }
                },
                wrapUpdateData:function(idx,data){
                    var type = $('.nav-tabs li.active').data('id');
                    if(init.typeParams.toString().length){
                        type = init.typeParams;
                    }
                    var params = {
                        module:type,
                    };


                    if (params){
                        $.extend(data, params);
                    }
                    return data;
                }
            });

            init.pagerObj.updateList(0);
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
                        type:$.trim(init.inputType.val()),
                        target:$.trim(init.inputTarget.val()),
                        parent_id:$.trim(init.inputParent.val()),
                        show_type:$.trim(init.inputShowType.val()),
                        btn_type:$.trim($(init.inputBtnTypeNode+':checked').val()),
                        url:$.trim(init.inputUrl.val()),
                        page_id:$.trim(init.inputPage.val()),
                        module:$.trim($('.nav-tabs li.active').data('id')),
                        params:init.getModalParamsData()
                    };

                    var checkMap = {
                        name:'请输入菜单名称',
                    };
                    for (var key in checkMap){
                        if(!params[key] || params[key].length<1){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }
                    if(params.type==2) {
                        if(params.parent_id<1){
                            $.showToast('请选择父级菜单',false);
                            return false;
                        }
                    }else{
                        params.parent_id = '';
                    }

                    if(params.btn_type==1) {
                        params.url = '';
                    }else{
                        params.page_id = '';
                    }

                    $.wpost('/manage/update-menu-ajax',params,function(res){
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
                    $.wpost('/manage/delete-menu-ajax',{id:id},function(){
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
                if($this.hasClass('js_up')){
                    $(target).before($parent);
                }else{
                    $(target).after($parent);
                }
                // target = $(target);
                // var bakTarget = target.clone();
                // var bakCurrent =  $parent.clone();
                //
                // target.replaceWith(bakCurrent);
                // $parent.replaceWith(bakTarget);
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
                $.wpost('/manage/save-menu-sort-ajax',{params:params},function(){
                    $.showToast('保存成功',true);
                });
            });



        },
        run : function () {
            init.btnSwitchEvent();
            init.initTabEvent();
            init.initPager();
            init.initModalParamsEvent();
            init.initBtnEvent();
        }
    };

    init.run();
});
