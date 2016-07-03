<?php

class ImageModel extends Halo_Model
{
	public $root ;//= '../../image/fix/';
	public $host ;//= 'http://img.fix.weirenmai.dragon-stone.cn';
	
	public function __construct()
	{
		parent::__construct();
		
		$config = Yaf_Registry::get('config');
		$this->root = self::getImageRoot();
		$this->host = self::getImageHost();

		$this->errorCode = array(
				array('key'=>'img_not_exist','text'=>'图片不存在'),
				array('key'=>'img_format_error','text'=>'图片格式错误'),
				array('key'=>'img_storage_error','text'=>'图片存储错误'),
                array('key'=>'img_not_access','text'=>'获取图片失败'),
                array('key'=>'path_invalid','text'=>'文件路径无效'),
        );
	}
	public static function getImageRoot()
	{
		$config = Yaf_Registry::get('config');
		return $config->img->root;
	}
	public static function getImageHost()
	{
		$config = Yaf_Registry::get('config');
		return $config->img->host;
	}
	public static function getZoomUrl($url)
	{
		$pos = strrpos($url,'.');
		$result = substr($url,0,$pos);
		$result .= '_480';
		$result .= substr($url,$pos);
		return $result;
	}
	public static function getRelatetivePath($url)
	{
		$imageHost = ImageModel::getImageHost();
		if(!StringUtil::hasPrefix($url, $imageHost))
			return $url;
		
		return substr($url, strlen($imageHost)+1);
	}
	public function newImage($type,$uid,$mimeType,$ext,$tmpName)
	{
// 		$root = '../../image/fix/';
		$sourceImg = $this->getImageData($mimeType,$tmpName);
		if (! $sourceImg)
			return $this->getErrorCode ( 'img_format_error' );
		
		$md5 = md5 ( file_get_contents ( $tmpName ) );
		$condition = sprintf ( 'Fmd5=\'%s\'', $md5 );
		// echo $condition;
		$row = $this->operation_db->getRowByCondition ( 'data_image', $condition );
		if ($row) 
		{
			imagedestroy ( $sourceImg );
			$zoomUrlRalativePath = $this->getZoomUrl ( $row ['Furl'] );
			$url = sprintf ( '%s/%s', self::getImageHost (), $zoomUrlRalativePath );
			return array (
					'id' => $row ['Fid'],
					'url' => $url 
			);
		}
		
		$path = sprintf ( '%s/%s/', $type, date ( 'Y/m/d' ) );
		$dirs = explode ( '/', $path );
		$this->ensurePathExists ( $this->root, $dirs );
		$sourceName = sprintf ( '%s_%s', $uid, date ( 'h-i-s' ) );
		$fileName = $this->root . $path . $sourceName . $ext;
		
		$sizes = array (
				2000,
				480,
				100 
		);
		// $storage = new SaeStorage();
		$zoomUrlRalativePath = '';
		// $url = $storage->upload( 'image' , $path.$sourceName.$ext ,
		// $tmpName );
		if (move_uploaded_file ( $tmpName, $fileName )) {
			
			list ( $srcWidth, $srcHeight ) = getimagesize ( $fileName );
			foreach ( $sizes as $v ) {
				$zoomName = sprintf ( '%s_%d', $sourceName, $v );
				$newSize = $this->getZoomSize ( $srcWidth, $srcHeight, $v );
				if (is_array ( $newSize )) {
					$destImg = imagecreatetruecolor ( $newSize [0], $newSize [1] );
					imagecopyresampled ( $destImg, $sourceImg, 0, 0, 0, 0, $newSize [0], $newSize [1], $srcWidth, $srcHeight );
					
					ob_start ();
					imagejpeg ( $destImg );
					$data = ob_get_clean ();
					
					if ($v == 480) {
						$zoomUrlRalativePath = $path . $zoomName . $ext;
						// $zoomUrl = $storage->write( 'image' ,
						// $path.$zoomName.$ext , $data );
					}
					// else
					// $storage->write( 'image' , $path.$zoomName.$ext ,
					// $data );
					
					file_put_contents ( $this->root . $path . $zoomName . $ext, $data );
					
					imagedestroy ( $destImg );
				} else {
					copy ( $fileName, $this->root . $path . $zoomName . $ext );
					// $storage->upload( 'image' , $path.$zoomName.$ext ,
					// $tmpName );
				}
			}
			imagedestroy ( $sourceImg );
			
			$relativePath = $path . $sourceName . $ext;
			$id = $this->operation_db->insertTable ( 'data_image', array (
					'Fmd5' => $md5,
					'Furl' => $relativePath,
					'Fwidth' => $srcWidth,
					'Fheight' => $srcHeight,
					'Ftime' => time () 
			) );
			
			$returnPath = empty ( $zoomUrlRalativePath ) ? $relativePath : $zoomUrlRalativePath;
			return array (
					'id' => $id,
					'url' => sprintf ( '%s/%s', $this->host, $returnPath ) 
			);
		} else {
			imagedestroy ( $sourceImg );
			return $this->getErrorCode ( 'img_storage_error' );
		}
	}
	public function newCompanyLogo($companyId,$mimeType,$ext,$tmpName)
	{
		$sourceImg = $this->getImageData($mimeType,$tmpName);
		if(!$sourceImg)
			return $this->getErrorCode('img_storage_error');
		
		$dateStr = date('Ymd');
		$imageRalativeDir = sprintf('logo/%s/', $dateStr);
		$imageDir = $this->root.$imageRalativeDir;
		
		$dirs = explode('/', $imageRalativeDir);
		$this->ensurePathExists($this->root, $dirs);
		
		$imageName = sprintf ( '%s_%s', $companyId, date ( 'h-i-s' ));
		$imagePath = $imageDir.$imageName.$ext;
		
		list ( $srcWidth, $srcHeight ) = getimagesize ( $tmpName );
		$sizes = array (array (180,180 ));
		foreach ( $sizes as $v ) 
		{
			list ( $destWidth, $destHeight ) = $this->getZoomValue ( $v [0], $v [1], $srcWidth, $srcHeight );
			
			$destImg = imagecreatetruecolor ( $destWidth, $destHeight );
			imagecopyresampled ( $destImg, $sourceImg, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight );
			
			$zoomName = sprintf ( '%s_%d', $imageName, $v [0] );
			imagejpeg ( $destImg, $imageDir.$zoomName.$ext);
			imagedestroy ( $destImg );
		}
		
		if (! move_uploaded_file ( $tmpName, $imagePath ))
		{
			imagedestroy ( $sourceImg );
			return $this->getErrorCode ( 'img_storage_error' );
		}
		
		imagedestroy ( $sourceImg );
		
		$url= sprintf('%s/%s%s_180%s', $this->host, $imageRalativeDir, $imageName, $ext);
		return $url;
	}

    public function getMicroTimeName()
    {
        $micTimeArray=explode(' ',microtime());
        $date=date ( 'h-i-s' ,$micTimeArray[1]);
        $micTime=substr($micTimeArray[0],2);
        $result=$micTime."_".$date;
        return $result;
    }

    public function uploadImage($mimeType,$ext,$tmpName)
    {
        $sourceImg = $this->getImageData($mimeType,$tmpName);
        if(!$sourceImg)
            return $this->getErrorCode('img_storage_error');

        $dateStr = date('Ymd');
        $imageRalativeDir = sprintf('secret/%s/', $dateStr);
        $imageDir = $this->root.$imageRalativeDir;

        $dirs = explode('/', $imageRalativeDir);
        $this->ensurePathExists($this->root, $dirs);
        $imageName = $this->getMicroTimeName();
        $imagePath = $imageDir.$imageName.$ext;

        list ( $srcWidth, $srcHeight ) = getimagesize ( $tmpName );
        $sizes = array (array (180,180 ));
        foreach ( $sizes as $v )
        {
            list ( $destWidth, $destHeight ) = $this->getZoomValue ( $v [0], $v [1], $srcWidth, $srcHeight );

            $destImg = imagecreatetruecolor ( $destWidth, $destHeight );
            imagecopyresampled ( $destImg, $sourceImg, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight );

            $zoomName = sprintf ( '%s_%d', $imageName, $v [0] );
            imagejpeg ( $destImg, $imageDir.$zoomName.$ext);
            imagedestroy ( $destImg );
        }

        if (! move_uploaded_file ( $tmpName, $imagePath ))
        {
            imagedestroy ( $sourceImg );
            return $this->getErrorCode ( 'img_storage_error' );
        }

        imagedestroy ( $sourceImg );

        $url= sprintf('%s/%s%s%s', $this->host, $imageRalativeDir, $imageName, $ext);
        return $url;
    }

	public function newSvg($uid,$tmpName)
	{
		
	}
	public function ensurePathExists($root,$dirs)
	{
		if(!is_dir($root))
		{
			if(!@mkdir($root))
			{
				return false;
			}
		}
		
		$path = '';
		foreach($dirs as $v)
		{
			$root .= $v.'/';
			$path .= $v.'/';
		
			if(!file_exists($root) || !is_dir($root))
			{
				if(@mkdir($root))
				{
					continue;
				}
				else
				{
					return false;
					break;
				}
			}
		}
		return $path;
	}
	public function checkImage($md5)
	{
		$condition = sprintf('Fmd5=\'%s\'',$md5);
		$row = $this->operation_db->getRowByCondition('data_image', $condition);
		if($row)
		{
			return array('id'=>$row['Fid'],'url'=>$row['Furl']);
		}
		else
			return $this->getErrorCode('img_not_exist');
	}
	public function getImageData($mime,$filename)
	{
		switch($mime)
		{
			case 'image/jpeg':
			case 'image/pjpeg':
				return imagecreatefromjpeg($filename);
				break;
			case 'image/png':
				return imagecreatefrompng($filename);
				break;
			case 'image/gif':
				return imagecreatefromgif($filename);
				break;
		}
	}
	public function getExt($mimeType)
	{
		$imageExtMaps = array(
				'image/gif'=>'.gif',
				'image/jpeg'=>'.jpg',
				'image/pjpeg'=>'.jpeg',
				'image/png'=>'.png',
				);
		
		if(isset($imageExtMaps[$mimeType]))
			return $imageExtMaps[$mimeType];
		else
			return false;
	}
	function getZoomSize($srcWidth,$srcHeight,$limitWidth)
	{
		if($srcWidth<=$limitWidth)
		{
			return false;
		}
		else
		{
			$scale = floor(($limitWidth/$srcWidth)*100)/100;
			$limitHeight = $srcHeight*$scale;
			return array($limitWidth,$limitHeight);
		}
	}
	function getZoomValue($limit_width,$limit_height,$source_width,$source_height)
	{
		$wh=array();
		if($source_width<=$limit_width && $source_height<=$limit_height)
		{
			$wh[]=$source_width;
			$wh[]=$source_height;
		}
		else
		{
			$w=$source_width/$limit_width;
			$h=$source_height/$limit_height;
			if($w>$h)
			{
				$wh[]=$limit_width;
				$wh[]=($w>=1?($source_height/$w):($source_height*$w));
			}
			elseif($w<$h)
			{
				$wh[]=($h>=1?($source_width/$h):($source_width*$h));
				$wh[]=$limit_height;
			}
			else
			{
				$wh[]=$limit_width;
				$wh[]=$limit_height;
			}
		}
		return $wh;
	}

    function downloadImage($url, $saveDir, $fileName)
    {
        if (is_string($saveDir) && is_string($fileName) && $saveDir != '' && $fileName != '')
        {
            if (substr($saveDir, -1) != '/')
            {
                $saveDir .= '/';
            }
            if(!file_exists($saveDir) && !mkdir($saveDir, 0755, true))
            {
                return $this->getErrorCode('img_storage_error');
            }
            if (file_exists($saveDir.$fileName))
            {
                return true;
            }
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close ($ch);
            if ($code >= 400 || file_put_contents($saveDir.$fileName, $res) === false)
            {
                echo 'url:'.$url.' code:'.$code."\n";
                return $this->getErrorCode('img_not_access');
            }
            return true;
        }
        return $this->getErrorCode('path_invalid');
    }

    public function resizeImage($url,$beginX,$beginY,$endX,$endY)
    {
        $imgPath=$config = SystemConfig::get('publish.img.path');

        $basedir = $config->get('img.basedir');

        if(file_exists($url))
        {
            Header( "Content-type: image/jpeg");
            $im = imagecreatefromjpeg($url);
            imagejpeg($im,null,100);
            imagedestroy($im);
            die();
        }


//        if(isset($data) && strlen($data['cover']) > 0)
//        {
//            $coverUrl = $data['cover'];
//            $coverUrl = $this->getLocalPath($coverUrl);
//            $ext = $this->getImageType($coverUrl);
//
//            switch($ext)
//            {
//                case 'png':
//                    $cover = imagecreatefrompng($coverUrl);
//                    break;
//                case 'gif':
//                    $cover = imagecreatefromgif($coverUrl);
//                    break;
//                case 'jpeg':
//                case 'jpg':
//                    $cover = imagecreatefromjpeg($coverUrl);
//                    break;
//                default:
//                    echo $coverUrl;
//                    die();
//            }
//            Header( "Content-type: image/jpeg");
//            $top = imagecreatefrompng($config->get('rank.img.top'));
//            $bg = imagecreatefrompng($config->get('rank.img.bg'));
//
//            $topHeight = imagesy($top);
//            $wholeHeight = imagesy($bg);
//
//            //add user bg
//            $white = ImageColorAllocate($bg, 255,255,255);
//
//            $margin = 5;
//            $gap = 10;
//            $fontSize = 13;
//            $fontSmallSize = 10;
//            $bgWidth = $wholeWidth -2*$gap;
//
//
//            $srcWidth = imagesx($cover);
//            $srcHeight = imagesy($cover);
//
//            if($srcWidth > $bgWidth - $gap*4)
//            {
//                $coverWidth = $bgWidth - $gap*4;
//            }
//            else
//            {
//                $coverWidth = $srcWidth;
//            }
//
//            $bgWidth = $coverWidth + $gap*2;
//            $coverHeight = intval($coverWidth / $srcWidth * $srcHeight);
//            $bgHeight = $gap + $coverHeight + $gap + $fontSize + $gap/2 + $fontSmallSize + $gap + $gap*1.5;
//
////        $info['data']['org'] = '友录';//$chinese->big5_gb2312($info['data']['org']);
////        $info['data']['job'] = '美工';//$chinese->big5_gb2312($info['data']['job']);
//            if(strlen($data['org']) > 0)
//            {
//                $orgAndJob[] = $data['org'];
//            }
//            if(strlen($data['job']) > 0)
//            {
//                $orgAndJob[] = $data['job'];
//            }
//
//            if(count($orgAndJob) == 0)
//            {
//                $bgHeight -= $fontSmallSize + 8;
//            }
//
//
//
//            $whiteBg = imagecreatetruecolor($bgWidth,$bgHeight);
////        $border = imagecolorallocate($whiteBg,206,193,199);
//            imagefill($whiteBg,0,0,$white);
////        imagefilledrectangle($whiteBg,1,1,$bgWidth-2,$bgHeight-2,$white);
//
//
//            imagecopyresized($whiteBg, $cover, ($bgWidth - $coverWidth)/2, $gap, 0, 0, $coverWidth, $coverHeight, imagesx($cover), imagesy($cover));
//            imagedestroy($cover);
//
//
//            $chinese = new utf8_chinese;
//            $name = $chinese->big5_gb2312($data['name']);
//
//            $name = $this->tailText($name,$fontSize,$font,$coverWidth);
//            $x = $gap;
//            $nameColor = imagecolorallocate($whiteBg,150,86,102);
//            $y = $coverHeight + $gap*2.5 + $fontSize;
//            ImageTTFText($whiteBg, $fontSize, 0, $x, $y, $nameColor, $font, $name);
//
//            if(count($orgAndJob) > 0)
//            {
//                $org = implode(', ',$orgAndJob);
//                $org = $chinese->big5_gb2312($org);
//                $org = $this->tailText($org,$fontSmallSize,$font,$coverWidth);
//                $orgColor = imagecolorallocate($whiteBg,85,85,85);
//                $y += $fontSize + 8;
//                ImageTTFText($whiteBg, $fontSmallSize, 0, $x, $y, $orgColor, $font, $org);
//            }
//
//            $height = $bgHeight + 2*$gap + $topHeight + $margin*2 + $gap;
//            $im = imagecreatetruecolor($wholeWidth,$height);
//            $bgColor = imagecolorallocate($im,249,189,217);
//            imagefill($im,0,0,$bgColor);
//
//            // add top & bg
//            imagecopy($im, $top, 0, 0, 0, 0, $wholeWidth, $topHeight);
//            imagedestroy($top);
//
//            imagecopy($im, $bg, 0, $topHeight, 0, 0, $wholeWidth, $wholeHeight);
//            imagedestroy($bg);
//
//            $topHeight += $margin;
//            imagecopy($im, $whiteBg, ($wholeWidth - $bgWidth)/2 , $gap+ $topHeight, 0, 0, $bgWidth, $bgHeight);
//            imagedestroy($whiteBg);
//
//
//            //add bottom
//            $bottom = imagecreatefrompng($config->get('rank.img.bottom'));
//            $bottomHeight = imagesy($bottom);
//            $height = imagesy($im);
//            $finalImg = imagecreatetruecolor($wholeWidth, $height + $bottomHeight);
//            imagecopy($finalImg, $im, 0, 0, 0, 0, $wholeWidth, $height);
//            imagedestroy($im);
//            imagecopy($finalImg, $bottom, 0, $height, 0, 0, $wholeWidth, $bottomHeight);
//
//
//
//
//            imagejpeg($finalImg,$imgName,100);
//            imagejpeg($finalImg,null,100);
//            imagedestroy($im);
//            imagedestroy($finalImg);
//        }
//        else
//        {
//            die();
//        }
    }


    function downloadCmsImage($url)
    {
        $config = SystemConfig::get('config');
        $time = time();
        $saveDir = $config->image->base_dir;
        $subfix='cms/' . sprintf('%s/', date('Y', $time)) . sprintf('%s/', date('m', $time)) . sprintf('%s/', date('d', $time));
        $saveDir .= $subfix;
        $fileName = md5($url);
        echo 'url:'.$url.' file name'.$fileName."\n";
        if ($this->downloadImage($url, $saveDir, $fileName))
        {
            return $subfix . $fileName;
        }
        return false;
    }
}