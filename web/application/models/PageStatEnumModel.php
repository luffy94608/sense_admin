<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/5/12
 * Time: 16:35
 */
class PageStatEnumModel
{
    const PageHome = '0';//主页
    const Page     = 'web';//网站




    public static function getTitleMap()
    {
        $transformMap = array(

            self::Page                                  => "总站",
//            self::PageHome                              => "首页",
        );

        return $transformMap;
    }

    public static function getPageTitle($key)
    {
        $transformMap = self::getTitleMap();
        $model = new MenuModel();
        $menu = $model->getAllMenuListMapTitle();
        $transformMap = array_merge($transformMap,$menu);
        return $transformMap[$key];
    }

    public static function toAddStatDataEmptyData($list,$timeStr)
    {
        $res = empty($list) ? [] : $list;
        $existsKeys = [];
        if($list)
        {
            foreach ($list as $v)
            {
                $existsKeys[] = $v['url'];
            }
        }
        $transformMap = self::getTitleMap();
        $mapKeys = array_keys($transformMap);
        $diffArr = array_diff($mapKeys,$existsKeys);
        $diffDataArr = [];
        if(!empty($diffArr))
        {
            foreach ($diffArr as $item) {
                $tmpData = [
                    'url'=>$item,
                    'pc'=>0,
                    'uv'=>0,
                    'time'=>$timeStr,
                ];
                $diffDataArr[]=$tmpData;
            }
            $res = array_merge($res,$diffDataArr);
        }
        return $res;
    }

}