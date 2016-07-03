<?php
class BaseBuilder 
{

    public static function createPageIndicator()
    {
        return "<div style='text-align:center;margin:10px;'><ul class='pagination' id='pager_indicator'></ul></div>";
    }


    public static function toRequireJsHtml()
    {
        $config = HaloEnv::get('config');
        $root = $config['resource']['web']['root'];
        return "<script src='{$root}/requirejs/require.js' data-main='{$root}/scripts/main.js' type='text/javascript'></script>";
    }

    public static function createJsonDataTag($id, $data, $tag = 'script')
    {
        $json = json_encode($data);
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
        {
            $tag = 'xmp';
        }
        if($tag == 'script')
            return "<{$tag} id='{$id}' type='text/data'>{$json}</{$tag}>";
        else
            return "<{$tag} id='{$id}' style='display:none;'>{$json}</{$tag}>";
    }

    public static function getImageUrl($url)
    {
        $config = HaloEnv::get('config');
        $resourceRoot = $config->resource->web->root.'/images/';
        return $resourceRoot.$url;
    }

    public static function getPlaceHolderImage()
    {
        return self::getImageUrl('grey.gif');
    }


}