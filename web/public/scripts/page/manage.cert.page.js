/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        listContainer: $('.js_table_list'),

        inputName : $('#js_modal_name'),

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
                    var params = {
                      id:  $.trim(tmpObj.eq(0).val()),
                      name:  $.trim(tmpObj.eq(1).val()),
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
                    tmpObj.eq(1).val(value.name);
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
                        name:$.trim(init.inputName.val()),
                        params:init.getModalParamsData()
                    };
                    var checkMap = {
                        name:'请输入事件名称',
                        params:'请添加事件内容',
                    };

                    for (var key in checkMap){
                        if(!params[key] || params[key].length<1){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }
                    $.wpost('/manage/update-route-ajax',params,function(res){
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
                    $.wpost('/manage/delete-route-ajax',{id:id},function(){
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
                $.wpost('/manage/save-route-sort-ajax',{params:params},function(){
                    $.showToast('保存成功',true);
                });
            });

        },
        run : function () {
            init.initModalParamsEvent();
            init.initBtnEvent();
        }
    };

    init.run();
});
