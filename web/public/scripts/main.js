(function () {
    //全局可以host

    var configData = document.global_config_data;
    var version = configData.version;

    requirejs.config({

        baseUrl: configData.resource_root + '/scripts/',
        urlArgs: 'v=' + version,
        waitSeconds: 0,
        paths: {
            //core js
            'jquery': '../assets/global/plugins/jquery.min',
            'jquery-easypiechart': '../assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min',
            'jquery-migrate': '../assets/global/plugins/jquery-migrate.min',
            'jquery-ui': '../assets/global/plugins/jquery-ui/jquery-ui.min',
            'bootstrap': '../assets/global/plugins/bootstrap/js/bootstrap.min',
            'bootstrap-hover-dropdown': '../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min',
            'bootstrap-modalmanager': '../assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager',
            'bootstrap-modal': '../assets/global/plugins/bootstrap-modal/js/bootstrap-modal',
            'slimscroll': '../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min',
            'blockui': '../assets/global/plugins/jquery.blockui.min',
            'cokie': '../assets/global/plugins/jquery.cokie.min',
            'uniform': '../assets/global/plugins/uniform/jquery.uniform.min',
            'bootstrap-switch': '../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min',
            'jquery-dataTables': '../assets/global/plugins/datatables/media/js/jquery.dataTables',
            'dataTables-bootstrap': '../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap',
            'pulsate': '../assets/global/plugins/jquery.pulsate.min',

            'dataTable': '../assets/global/scripts/datatable',
            'select2': '../assets/global/plugins/select2/select2.min',
            'select2_zh-CN': '../assets/global/plugins/select2/select2_locale_zh-CN',
            'dataTables-tableTools': "../assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min",
            //amchart
            'amcharts': '../assets/global/plugins/amcharts/amcharts/amcharts',
            'serial': '../assets/global/plugins/amcharts/amcharts/serial',
            'pie': '../assets/global/plugins/amcharts/amcharts/pie',
            'themes-light': '../assets/global/plugins/amcharts/amcharts/themes/light',

            'date-picker': '../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min',
            'time-picker': '../../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker',
            'metronic': '../assets/global/scripts/metronic',
            'layout': '../assets/admin/layout/scripts/layout',
            'widget': 'common/common.widget',
            'base': 'common/base',

            //page js
            'index-page': 'page/index.page',
            'login-page': 'page/login.page',
            'account-user-page': 'page/account.user.page',
            'account-role-page': 'page/account.role.page',
            'account-module-page': 'page/account.module.page',
            'company-index-page': 'page/company.index.page',
            'company-user-page': 'page/company.user.page',

            'statistics-index-page': 'page/statistics.index.page',
            'manage-recruit-page': 'page/manage.recruit.page',
            'manage-news-page': 'page/manage.news.page',
            'manage-map-page': 'page/manage.map.page',
            'manage-route-page': 'page/manage.route.page',
            'manage-solution-page': 'page/manage.solution.page',
            'lock-type-page': 'page/lock.type.page',
            'lock-download-page': 'page/lock.download.page',
            'lock-list-page': 'page/lock.list.page',
            'home-banner-page': 'page/home.banner.page',
            'home-partner-page': 'page/home.partner.page',

        },
        // Use shim for plugins that does not support ADM
        shim: {
            'jquery-migrate': ['jquery'],
            'jquery-ui': ['jquery'],
            'jquery-easypiechart': ['base'],
            'bootstrap': ['jquery'],
            'bootstrap-hover-dropdown': ['jquery'],
            'slimscroll': ['jquery'],
            'blockui': ['jquery'],
            'cokie': ['jquery'],
            'uniform': ['jquery'],
            'bootstrap-switch': ['jquery'],
            'date-picker': ['bootstrap', 'jquery-ui'],
            'time-picker':['bootstrap', 'jquery-ui'],
            'jquery-dataTables': ['jquery'],

            'dataTables-bootstrap': ['jquery-dataTables'],
            'dataTable': ['jquery-dataTables'],
            'bootstrap-modalmanager': ['bootstrap'],
            'bootstrap-modal': ['bootstrap'],
            'pulsate': ['jquery'],

            'select2': ['jquery'],
            'dataTables-tableTools':['dataTable'],
            //amchart
            'themes-light': ['amcharts'],
            'themes-chalk': ['amcharts'],
            'themes-patterns': ['amcharts'],
            'serial': ['themes-light'],
            'pie': ['themes-light'],
            'radar': ['themes-light'],

            'metronic': ['jquery-ui', 'bootstrap', 'blockui', 'dataTables-bootstrap', 'select2', 'bootstrap-modal', 'bootstrap-modalmanager'],
            'layout': ['metronic'],

            'widget': ['layout'],
            'base': ['widget'],

            //page shim
            'index-page': ['jquery-easypiechart'],
            'login-page': ['base'],
            'account-user-page': ['base', 'dataTables-bootstrap'],
            'account-role-page': ['base'],
            'account-module-page': ['base'],
            'company-index-page':['base'],
            'company-user-page':['base'],

            'statistics-index-page':['base', 'serial','date-picker'],
            'manage-recruit-page':['base'],
            'manage-map-page':['base'],
            'manage-route-page':['base'],
            'manage-solution-page':['base'],
            'manage-news-page':['base','date-picker'],
            'lock-type-page':['base'],
            'lock-download-page':['base'],
            'lock-list-page':['base'],
            'home-banner-page':['base'],
            'home-partner-page':['base'],

        }

    });

    var page = configData.page;

    var modules = [];
    if (page) {
        modules.push(page);
    }

    if (modules.length) {
        require(modules, function () {
        });
    }

})();
