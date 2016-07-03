/**
 * xiufei.lu
 */
$(document).ready(function(){

    //通用的图表
    // var initChartSection = function(data) {
    //     var chart = AmCharts.makeChart("chart_bar_user", {
    //         "type": "serial",
    //         "theme": "light",
    //         "fontFamily": 'Open Sans',
    //         "color":    '#888888',
    //         "legend": {
    //             "equalWidths": false,
    //             "useGraphSettings": true,
    //             "valueAlign": "left",
    //             "valueWidth": 120
    //         },
    //         "dataProvider": data.data,
    //         "graphs": data.graphs,
    //         "chartCursor": {
    //             "categoryBalloonDateFormat": "DD",
    //             "cursorAlpha": 0.1,
    //             "cursorColor": "#000000",
    //             "fullWidth": true,
    //             "valueBalloonsEnabled": false,
    //             "zoomable": false
    //         },
    //         "dataDateFormat": "YYYY-MM-DD",
    //         "categoryField": "date",
    //         "categoryAxis": {
    //             "dateFormats": [{
    //                 "period": "DD",
    //                 "format": "DD"
    //             }, {
    //                 "period": "WW",
    //                 "format": "MMM DD"
    //             }, {
    //                 "period": "MM",
    //                 "format": "MMM"
    //             }, {
    //                 "period": "YYYY",
    //                 "format": "YYYY"
    //             }],
    //             "parseDates": false,
    //             "autoGridCount": false,
    //             "axisColor": "#555555",
    //             "gridAlpha": 0.1,
    //             "gridColor": "#FFFFFF",
    //             "gridCount": 50
    //         },
    //         "exportConfig": {
    //             "menuBottom": "20px",
    //             "menuRight": "22px",
    //             "menuItems": [{
    //                 "icon": Metronic.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
    //                 "format": 'png'
    //             }]
    //         }
    //     });
    //
    //     $('#chart_bar_user').closest('.portlet').find('.fullscreen').click(function() {
    //         chart.invalidateSize();
    //     });
    // };
    //
    // var initSearchEvent=function(){
    //     var startTime=$('#start_search_time').val();
    //     var endTime=$('#end_search_time').val();
    //     if(endTime<startTime){
    //         $.showToast('结束时间不能小于开始时间',false);
    //         return false;
    //     }
    //     $.wpost('/statistics/search-stat-data-ajax',{start_time:startTime,end_time:endTime},function(data){
    //         initChartSection(data);
    //     });
    // };
    //
    // initSearchEvent();
    // $('#search_submit').unbind().wclick(function(){
    //     initSearchEvent();
    // });
    //
    // /**
    //  * 时间单位选择  逻辑控制
    //  */
    // var datePicket=$('.date-picker');
    // datePicket.datepicker({
    //     todayBtn: 'linked',
    //     todayHighlight: true,
    //     autoclose: true,
    //     disableTouchKeyboard: true,
    // });


});
