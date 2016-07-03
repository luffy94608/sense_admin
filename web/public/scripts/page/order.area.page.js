/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        listContainer: $('.js_table_list'),
        progress: $('.js_progress_section'),
        inputName : $('#js_modal_name'),
        type :0,

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));

            init.inputName.val(data.name);
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));

            init.inputName.val('');
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
                        name:$.trim(init.inputName.val()),
                        type:init.type,
                    };
                    if(!params.name){
                        $.showToast('请输入 区域名称',false);
                        return false;
                    }
                    $.wpost('/order/update-area-or-type-ajax',params,function(res){
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
                    $.wpost('/order/delete-area-or-type-ajax',{id:id,type:init.type},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });

        }
    };

    init.initBtnEvent();
});
