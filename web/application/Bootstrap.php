<?php

class Bootstrap extends Yaf_Bootstrap_Abstract
{

    protected function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();

//        $router->addRoute('callback', new Yaf_Route_Regex('#^/callback#', array('controller' => 'index', 'action' => 'callback'), array()));
////        $router->addRoute('wechat', new Yaf_Route_Regex('#^/wechat#', array('controller' => 'wechat', 'action' => 'index'), array()));
//        $router->addRoute('wechatcallback', new Yaf_Route_Regex('#^/wechatcallback#', array('controller' => 'wechat', 'action' => 'wechatcallback'), array()));
//        $router->addRoute('feedback', new Yaf_Route_Regex('#^/feedback#', array('controller' => 'account', 'action' => 'feedback'), array()));
//        $router->addRoute('logout', new Yaf_Route_Regex('#^/logout#', array('controller' => 'index', 'action' => 'logout'), array()));

    }

    protected function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new ActionPlugin());
        $dispatcher->registerPlugin(new AuthPlugin());
    }

    protected function _initView(Yaf_Dispatcher $dispatcher)
    {
        $view = new YafView(APPLICATION_VIEW_SCRIPTS_PATH);
        $view->enableLayout('layout/normal.phtml');
        $dispatcher->setView($view);
    }
}

