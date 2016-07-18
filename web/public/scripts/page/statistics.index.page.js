/**
 * xiufei.lu
 */
$(document).ready(function(){

    /**
     * 每条线路的表格
     */
    var buildPieceItemHtml=function(id,title){
        return '           <div id="'+id+'" class="chart" style="height: 500px;"></div> ';
        var html='' +
            '<div class="row">' +
            '<div class = "col-md-12"> ' +
            '   <div class = "portlet light bordered"> ' +
            '       <div class = "portlet-title"> ' +
            '           <div class = "caption"> ' +
            '               <i class = "icon-bar-chart font-green-haze"></i> ' +
            '               <span class="caption-subject bold uppercase font-green-haze"> '+title+'</span> ' +
            '               <span class="caption-helper"></span> ' +
            '           </div > ' +
            '           <div class="tools"> ' +
            '               <a href = "javascript:;" class="collapse"></a> ' +
            '               <a href="javascript:;" class="fullscreen"></a> ' +
            '               <a href = "javascript:;" class="remove"></a> ' +
            '           </div > ' +
            '       </div > ' +
            '       <div class="portlet-body"> ' +
            '           <div id="'+id+'" class="chart" style="height: 500px;"></div> ' +
            '       </div > ' +
            '   </div > ' +
            '</div > ' +
            '</div > ';
        return html;
    };

    var initPieceChart = function(data) {
        var id='js_id_piece_chart_'+data.id;
        var title=data.title;
        //生成html
        var html=buildPieceItemHtml(id,title);
        $('.js_piece_charts_section').append(html);
        var dataList = data.data;
        var graphs = data.graph;
        //初始化表格
        var chart = AmCharts.makeChart(id, {
            "type": "serial",
            "theme": "light",
            "fontFamily": 'Open Sans',
            "color":    '#888888',
            "legend": {
                "equalWidths": false,
                "useGraphSettings": true,
                "valueAlign": "left",
                "valueWidth": 120
            },
            "dataProvider": dataList,
            "graphs": graphs,
            "chartCursor": {
                "categoryBalloonDateFormat": "DD",
                "cursorAlpha": 0.1,
                "cursorColor": "#000000",
                "fullWidth": true,
                "valueBalloonsEnabled": false,
                "zoomable": false
            },
            "dataDateFormat": "YYYY-MM-DD",
            "categoryField": "date",
            "categoryAxis": {
                "dateFormats": [{
                    "period": "DD",
                    "format": "DD"
                }, {
                    "period": "WW",
                    "format": "MMM DD"
                }, {
                    "period": "MM",
                    "format": "MMM"
                }, {
                    "period": "YYYY",
                    "format": "YYYY"
                }],
                "parseDates": false,
                "autoGridCount": false,
                "axisColor": "#555555",
                "gridAlpha": 0.1,
                "gridColor": "#FFFFFF",
                "gridCount": 50
            },
            "exportConfig": {
                "menuBottom": "20px",
                "menuRight": "22px",
                "menuItems": [{
                    "icon": Metronic.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
                    "format": 'png'
                }]
            }

        });

        $('#'+id).closest('.portlet').find('.fullscreen').click(function() {
            chart.invalidateSize();
        });
    };
    //通用的图表
    var initChartSection = function(data) {
        var chart = AmCharts.makeChart("chart_bar_user", {
            "type": "serial",
            "theme": "light",
            "fontFamily": 'Open Sans',
            "color":    '#888888',
            "legend": {
                "equalWidths": false,
                "useGraphSettings": true,
                "valueAlign": "left",
                "valueWidth": 120
            },
            "dataProvider": data.data,
            "graphs": data.graphs,
            "chartCursor": {
                "categoryBalloonDateFormat": "DD",
                "cursorAlpha": 0.1,
                "cursorColor": "#000000",
                "fullWidth": true,
                "valueBalloonsEnabled": false,
                "zoomable": false
            },
            "dataDateFormat": "YYYY-MM-DD",
            "categoryField": "date",
            "categoryAxis": {
                "dateFormats": [{
                    "period": "DD",
                    "format": "DD"
                }, {
                    "period": "WW",
                    "format": "MMM DD"
                }, {
                    "period": "MM",
                    "format": "MMM"
                }, {
                    "period": "YYYY",
                    "format": "YYYY"
                }],
                "parseDates": false,
                "autoGridCount": false,
                "axisColor": "#555555",
                "gridAlpha": 0.1,
                "gridColor": "#FFFFFF",
                "gridCount": 50
            },
            "exportConfig": {
                "menuBottom": "20px",
                "menuRight": "22px",
                "menuItems": [{
                    "icon": Metronic.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
                    "format": 'png'
                }]
            }
        });

        $('#chart_bar_user').closest('.portlet').find('.fullscreen').click(function() {
            chart.invalidateSize();
        });
    };



    var initSearchEvent=function(){
        var startTime=$('#start_search_time').val();
        var endTime=$('#end_search_time').val();
        if(endTime<startTime){
            $.showToast('结束时间不能小于开始时间',false);
            return false;
        }
        $.wpost('/statistics/search-stat-data-ajax',{start_time:startTime,end_time:endTime},function(data){
            // initChartSection(data);
            ////初始化各个图表
            $('.js_piece_charts_section').html('');
            if(data.pieces && data.pieces.length){
                for(var i=0;i<data.pieces.length;i++ ){
                    initPieceChart(data.pieces[i]);
                }
            }
        });
    };

    initSearchEvent();
    $('#search_submit').unbind().wclick(function(){
        initSearchEvent();
    });

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


});
