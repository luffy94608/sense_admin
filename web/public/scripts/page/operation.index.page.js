/**
 * xiufei.lu
 */

$(document).ready(function($){
    /**
     * 时间单位选择  逻辑控制
     */
    var datePicket=$('.date-picker');
    datePicket.datepicker({
        todayBtn: 'linked',
        todayHighlight: true,
        autoclose: true,
        disableTouchKeyboard: true,
    });
    var tableObj = $('#operation_table');
    var opts = {
        "order": [//定义列表的初始排序设定,为一个2维数组,
            [5, 'desc']
        ]
    };
    var operationTable = tableObj.initDataTableWithAdvance(opts);
    var initData =function () {
        var time=$('#search_time').val();
        $.wpost('/operation/get-operation-list-ajax',{time:time},function(data){
            operationTable.fnDestroy();
            $('#operation_table_list').html(data.html);
            operationTable = tableObj.initDataTableWithAdvance(opts);
        });
    };

    /**
     * 搜索操作
     */

    $(document).on('click','#search_submit',function(){
        initData();
    });
    initData();



});
