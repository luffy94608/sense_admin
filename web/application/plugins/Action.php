<?php

/**
 * Created by PhpStorm.
 * User: yexuan.guo@yolu-inc.com
 * Date: 14-11-18
 * Time: 下午1:55
 */
class ActionPlugin extends Yaf_Plugin_Abstract
{
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $aliasName = $request->getActionName();
        $name = str_replace('-','',$aliasName);
        $request->setActionName($name);

        $aliasController = $request->getControllerName();
        $list = explode('-', $aliasController);
        if ($list && count($list))
        {
            foreach ($list as &$item)
            {
                $item = ucfirst($item);
            }
            $request->setControllerName(implode('', $list));
        }

        $request->setParam('action_alias_name', $aliasName);
    }
} 