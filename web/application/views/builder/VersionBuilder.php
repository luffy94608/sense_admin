<?php
class VersionBuilder 
{
	public static function wrapFileNameWithVersion($fileName,$ext='js')
	{
	    $releaseVersion = null;
		require '../release/config.php';
		$version = $releaseVersion;

		return sprintf('%s?v=%s.%s',$fileName,$version,$ext);
	}
	
	public static function cssFile($css)
	{
        $config = HaloEnv::get('config');
        $resourceRoot = $config->resource->web->root;
		return sprintf('<link href="%s%s" rel="stylesheet" type="text/css" />', $resourceRoot, VersionBuilder::wrapFileNameWithVersion($css, 'css'));
	}
	
	public static function jsFile($js)
	{
        $config = HaloEnv::get('config');
        $resourceRoot = $config->resource->web->root;
		return sprintf('<script src="%s%s" type="text/javascript"></script>', $resourceRoot, VersionBuilder::wrapFileNameWithVersion($js));
	}
}
