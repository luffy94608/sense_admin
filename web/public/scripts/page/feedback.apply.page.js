/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        inputContent : $('#js_modal_content'),

        datePicker : $('.date-picker'),
        pagerObj: '',
        tableContainer: '.js_table_list',
        dataTableObj: '',
        dataTableOptions : {
            "bLengthChange":true, //关闭每页显示多少条数据
            "bPaginate": true, //翻页功能
            "bFilter":true, //过滤功能
            "bSort": true, //排序功能
            "bJQueryUI": false,
            "bInfo": true,//页脚信息
            "bAutoWidth":true,//自动宽度
            "bProcessing":true,//正在处理提示
            "order": [//定义列表的初始排序设定,为一个2维数组,
                [6, 'desc']
            ],
            "columnDefs": [{//哪一列禁止排序
                "orderable": false,
                "targets": [0]
            }]
        },

        /**
         * 时间单位选择  逻辑控制
         */
        initDataPickerEvent : function () {
            init.datePicker.datepicker({
                // todayBtn: 'linked',
                todayHighlight: true,
                clearBtn: true,
                autoclose: true
            });
        },


        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));
            init.inputContent.val(data.desc);
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.inputContent.val('');
        },
        /**
         * 详情pager
         */
        initPager : function () {
            init.pagerObj = $('#wrapperPageList').Pager({
                protocol:'/feedback/get-apply-list-ajax',
                listSize:20,
                onPageInitialized:function(){
                    if (init.pagerObj){
                        var top=$(window).scrollTop();
                        if(top>100){
                            $(window).scrollTop(0);
                        }
                    }
                    init.dataTableObj = $(init.tableContainer).initDataTableWithAdvance(init.dataTableOptions);
                },
                wrapUpdateData:function(idx,data){
                    var params = {
                        start_time:$('#start_search_time').val(),
                        end_time:$('#end_search_time').val(),
                    };
                    if (params){
                        $.extend(data, params);
                    }
                    if(init.dataTableObj){
                        init.dataTableObj.fnDestroy();
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
             * 详情
             */
            $(document).on('click','.js_detail',function () {
                init.clearModalFormData();
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.data('id');
                var info=$parent.data('info');
                if(id && info){
                    init.setModalFormData(info);
                }
                init.modal.modal();
            });

            /**
             * 搜索按钮日期
             */
            $(document).on('click','#search_submit',function () {
                init.pagerObj.updateList(0);
            });

        },
        run : function () {
            init.initDataPickerEvent();
            init.initPager();
            init.initBtnEvent();
        }
    };

    init.run();
});
