/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        pager : '',
        listContainer: $('.js_table_list'),
        inputSupplerId : $('#supplier_id'),

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));
            if(data.supplier_id>0){
                init.inputSupplerId.val(data.supplier_id);
            }
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.inputSupplerId.val(0);
        },

        /**
         * 初始化page
         */
        initPageData : function () {
            init.pager = $('#wrapperPageList').Pager({
                protocol:'/order/get-list-ajax',
                listSize:20,
                onPageInitialized:function(){
                    if (init.pager){
                        var top=$(window).scrollTop();
                        if(top>100){
                            $(window).scrollTop(0)
                        }
                    }
                },
                wrapUpdateData:function(idx,data){
                    var param={};
                    var type= $.trim($('.js_nav_tab li.active').data('type'));
                    var key= $.trim($('#search_key').val());

                    param.type=type;
                    param.key=key;
                    if (param){
                        $.extend(data, param);
                    }
                    return data;
                }
            });
            init.pager.updateList(0);
        },

        /**
         * 初始化按钮时间
         */
        initBtnEvent : function () {
            /**
             * 分配
             */
            $(document).on('click','.js_distribution',function () {
                init.clearModalFormData();
                var submitBtn = init.modal.find('.submit');
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
                        supplier_id:$.trim(init.inputSupplerId.val()),
                        type:0,
                    };
                    $.wpost('/order/update-order-status-ajax',params,function(res){
                        $parent.replaceWith(res);
                        $.showToast('保存成功',true);
                        init.modal.modal('hide');
                    });
                });
            });

            /**
             * 完成
             */
            $(document).on('click','.js_accomplish',function () {
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.attr('data-id');
                $.confirm({content:'确定将该订单标记完成吗?',success:function(){
                    $.wpost('/order/update-order-status-ajax',{id:id,type:1,status:2},function(){
                        $.showToast('操作成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });

            /**
             * 关闭
             */
            $(document).on('click','.js_close',function () {
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.attr('data-id');
                $.confirm({content:'确定要关闭此订单吗?',success:function(){
                    $.wpost('/order/update-order-status-ajax',{id:id,type:2,status:4},function(){
                        $.showToast('操作成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });

            /**
             * type 切换
             */
            $(document).on('click','.js_nav_tab li',function () {
                $('.js_nav_tab li').removeClass('active');
                $(this).addClass('active');
                init.pager.updateList(0);
            });

            /**
             * 搜索search_user
             */
            $(document).on('click','#search_user',function () {
                init.pager.updateList(0);
            });

            /**
             * 一键导出
             */
            $(document).on('click','#js_down_excel_file',function () {
                $.wpost('/order/create-excel-ajax',{},function(res){
                    if (res) {
                        window.location.href=res;
                    }
                });
            });

        },
        run : function () {
            init.initPageData();
            init.initBtnEvent();
        }
    };

    init.run();
});
