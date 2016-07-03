<?php

class ProfileHelper
{
    public static $GENDER = array(
            'n' => '性别:',
            'm' => '男',
            'f' => '女'
    );

    public static $BIRTHDAY_START = 1910; // '1910-1-1';
    public static $DATE_TIME_FMT = 'Y-n-j H:i:s';

    public static $DATE_FMT = 'Y年n月j日';

    public static $DATE_FMT2 = 'Y年n月';
    
    public static $DATE_FMT3 = 'Y年n月j日 H:i';
    public static $DATE_FMT31 = '去年n月j日';
    public static $DATE_FMT4 = 'n月j日 H:i';
    public static $DATE_FMT41 = '昨天 H:i';
    public static $DATE_FMT42 = '前天 H:i';
    public static $DATE_FMT5 = 'H:i';
    
    public static $DAY_FMT3 = 'Y年n月j日';
    public static $DAY_FMT31 = '去年n月j日';
    public static $DAY_FMT4 = 'n月j日';

    public static $DELIMITER = '、';

    public static $MARRIAGE = array(
            1 => '单身',
            2 => '恋爱中',
            3 => '已婚',
            4 => '不公开'
    );
    
    public static $PRIVACY_CONTACT = array(
    		0=>'仅自己可见',
    		1=>'仅人脉可见',
    		2=>'所有人可见'
    		);

    public static $PRIVACY = array(
            'occupation' => '职业生涯',
            'education' => '教育背景',
            'marriage' => '婚姻状况',
            'email_im' => '电子邮件和IM',
            'birthday' => '生日'
    );

    public static $EDU_DEGREE = array(
            1 => '大专',
            2 => '本科',
            3 => '硕士',
            4 => '博士'
            );

    public static $SCHOOL_TYPE = array(
            1 => '大学',
            2 => '高中',
            3 => '中专技校',
            4 => '初中',
            5 => '小学'
    );

    public static $EDUCATION = array(
            '未知',
            '初中',
            '高中',
            '大专',
            '大学',
            '硕士',
            '博士',
            '其他'
    );

    public static $TIME_UNIT = array(
            'h' => '小时',
            'i' => '分钟',
            's' => '秒'
    );

    public static $CONTACT_PATTERN = array(
            "mobile" => "phone",
            "phone" => "phone",
            "fax" => "phone",
            "qq" => "number",
            "email" => "email",
            "msn" => "email",
            "weixin" => "",
            "gtalk" => "email",
            "skype" => "email",
            "yahoo" => "email",
            "sina" => "email",
            "facebook" => "email",
            "twitter" => "email",
            "blog" => "url",
            "home" => "url",
            "work" => "url"
    );

    public static $CONTACT_MOD = array(
            1 => "new",
            2 => "modify",
            3 => "delete"
    );

    public static function GetEducation ($type)
    {
        if (isset(self::$EDUCATION[$type])) {
            return self::$EDUCATION[$type];
        }
        return self::$EDUCATION[7];
    }

    public static function FormatDate ($date)
    {
        return date(self::$DATE_FMT, $date);
    }

    public static function FormatDateYM ($date)
    {
        return date(self::$DATE_FMT2, $date);
    }

    public static function DiffEduDate ($to, $from)
    {
    	$diffYM = self::diffYM($to, $from);
    	
    	$y = $diffYM['y'];
    	$m = $diffYM['m'];
    
    	$res = '';
    	if ($y) {
    		$res .= $y . '年';
    	}
    
    	if ($m >= 3 && $m <= 6){
    		$res .= '半';
            if(!$y)
                $res .= '年';
    	} else if ($m >=7 ){
    		$res = ($y+1).'年';
    	} 
    
    	return $res;
    }
    
    public static function DiffDate ($to, $from)
    {
    	$diffYM = self::diffYM($to, $from);
    	
    	$y = $diffYM['y'];
    	$m = $diffYM['m'];
        
        $res = '';
        if ($y) 
            $res .= $y . '年';
        
        if ($m) 
            $res .= $m . '个月';
        
        return $res;
    }
    
    public static function DiffOccuDate($occu)
    {
        if (intval($occu['start_year']) == 0 && intval($occu['start_month']) == 0 &&
        intval($occu['end_year']) == 0 && intval($occu['end_month']) == 0 &&
        intval($occu['not_ended']) == 0)
            return '';
    	$start_year = intval($occu['start_year']);
    	$start_month = intval($occu['start_month']);
    	
    	$end_year = intval($occu['end_year']);
    	$end_month = intval($occu['end_month']);
    	
    	$end_year_calc = $end_year ? $end_year : intval(date('Y'));
    	$end_month_calc = $end_month ? $end_month : intval(date('m'));
    	if (!$start_month)
    		$end_month_calc = 0;
    	
    	if ($occu['not_ended'] || !$occu['end_year'])
    	{
    		$end_year_calc = intval(date('Y'));
    		$end_month_calc = intval(date('m'));
    	}
    	
    	$diffM = (12 * $end_year_calc + $end_month_calc) - (12 * $start_year + $start_month) + ($start_month ? 1 : 0);
    	$y = intval($diffM / 12);
    	$m = $diffM % 12;
    	
    	$length = '';
    	if ($y)
    		$length .= $y . '年';
    	if ($m)
    		$length .= $m . '个月';
    	
    	if ($occu['not_ended'] || !$occu['end_year'])
    	{
    		
    		if ($start_month && $start_year)
    			return sprintf('%d年%d月至今(%s)', $start_year, $start_month, $length);
    		if ($start_year)
    			return sprintf('%d年至今(%s)', $start_year, $length);
    		
    		return '至今';
    	}
    	
    	if ($start_year)
    	{
    		if ($start_month)
    			return sprintf('%d年%d月-%d年%d月(%s)', $start_year, $start_month, $end_year, $end_month, $length);
    		
    		$length = ($end_year - $start_year + 1).'年';
    		return sprintf('%d年-%d年(%s)', $start_year, $end_year, $length);
    	}
    	
    	return sprintf('至%d年',$end_year);
    }

    public static function DescDate ($from, $to, $to_now = false, $edu = false)
    {
        if ($from == 0 && $to == 0 && $to_now == false)
            return '';
        $str_from = self::FormatDateYM($from);
        if ($to_now && $edu)
            return $str_from.' - 现在';
        
        $str_to = $to_now ?  '现在' : self::FormatDateYM($to);
        $str_diff = $edu ? self::DiffEduDate($to, $from) : self::DiffDate($to_now ? time() : $to, $from);
        
        $res = $str_from . ' - ' . $str_to;
        if ($str_diff)
            $res .= ' (' . $str_diff . ')';
        return $res;
    }

    public static function TimePassed ($time)
    {
    	$now = time();
    	if(date('Y', $now) - date('Y', $time) > 0)
    		return date(date('Y', $now) - date('Y', $time) > 1 ? self::$DATE_FMT3:self::$DATE_FMT31, $time); 
    	if(date('m', $now) - date('m', $time) > 0 || date('d', $now) - date('d', $time) > 0)
    	{
    		if(date('m', $now) - date('m', $time) == 0 && date('d', $now) - date('d', $time) ==1)
    			return date(self::$DATE_FMT41, $time); 
    		if(date('m', $now) - date('m', $time) == 0 && date('d', $now) - date('d', $time) ==2)
    			return date(self::$DATE_FMT42, $time); 
    		return date(self::$DATE_FMT4, $time); 
    	}
    	if(date('H', $now) - date('H', $time) > 0)
    		return date(self::$DATE_FMT5, $time); 
    	if(date('i', $now) - date('i', $time) > 0)
    		return (date('i', $now) - date('i', $time)).'分钟前';
    	return '1分钟前';
    }
    public static function DayPassed ($time)
    {
    	$now = time();
    	if(date('Y', $now) - date('Y', $time) > 0)
    		return date(date('Y', $now) - date('Y', $time) > 1 ? self::$DAY_FMT3:self::$DAY_FMT31, $time); 
    	return date(self::$DAY_FMT4, $time); 
    }
    public static function InDayPassed ($time)
    {
    	$now = time();
    	if(date('Y', $now) - date('Y', $time) == 0 && date('m', $now) - date('m', $time) == 0 && date('d', $now) - date('d', $time) <= 2)
    		return date("n月j日", $time);
    	return '';
    }
    public static function TimePassed1($time)
    {
        $temp = new DateTime(date(self::$DATE_TIME_FMT, $time));
        $now = new DateTime();
        $intv = $now->diff($temp);
        
        if ($intv->y > 0)
        	return $intv->y."年前";
        if ($intv->m > 0)
        	return $intv->m."个月前";
        if ($intv->d > 0)
        	return $intv->d."天前";
        if ($intv->h > 0)
        	return $intv->h.'小时前';
        if ($intv->i > 0)
        	return $intv->i.'分钟前';
        return '1分钟前';
    }
    
    public static function NotificationTime($time)
    {
    	$today = strtotime(date('Y-m-d', time()));
    	$yestoday = $today - 24 * 3600;
    	$before_yesterday = $yestoday - 24 * 3600;
    	
    	if ($time >= $today) return '今天';
    	if ($time >= $yestoday) return '昨天';
    	if ($time >= $before_yesterday) return '前天';
    	
    	return self::DayPassed($time);
    }
    
    public static function DescJob($user)
    {
        $info = array();
        if($user['org']) 
        {
            $info[] = $user['org'];
        }
        if($user['job']) 
        {
            $info[] = $user['job'];
        }	
        return implode(', ', $info);
    }

    public static function DescUser ($gender, $marriage, $birthday)
    {
        $info = array();
        if ($gender) {
            if(isset(ProfileHelper::$GENDER[$gender]))
                $info[] = ProfileHelper::$GENDER[$gender];
            else
                $info[] = '未知';
        }
        if ($marriage) {
            $info[] = self::$MARRIAGE[$marriage];
        }
        if ($birthday) {
            $info[] = date(ProfileHelper::$DATE_FMT, $birthday);
        }
        
        if (count($info)) {
            $info = implode(ProfileHelper::$DELIMITER, $info);
        }
        return $info;
    }
    
    public static function GetDegree($degree)
    {
        if(self::$EDU_DEGREE[$degree])
            return self::$EDU_DEGREE[$degree];
        return "";
    }

    public static function GetBirthYearRang ()
    {
        $now = intval(date('Y'));
        return array_reverse(range(self::$BIRTHDAY_START, $now));
    }

    public static function ShowContactMenu ($types)
    {
        $res = array();
        foreach ($types as $prop => $group) {
            $res[] = "<hr />";
            $check = BaseBuilder::getImageUrl('check.png');
            foreach ($group as $type => $name) {
                $res[] = "<div> <img width='16' height='16' src='{$check}' />";
                $res[] = "<a href='{$prop}_{$type}'>$name</a></div>";
            }
        }
        array_shift($res);
        return implode("\n", $res);
    }

    public static function GetContactName ($groups, $prop, $type)
    {
        if ($prop == "custom") {
            return $type;
        }
        
        if (isset($groups[$prop])) {
            $group = &$groups[$prop];
            if (isset($group[$type]))
                return $group[$type];
        }
        
        return $type;
    }
    
    public static function GetContactHtmlList($contactTypes,$contacts,$addr,$user,$nameStyle='',$valueStyle='',$short=false)
    {
    	$nameMap = array('Google Talk'=>'Gtalk', '个人邮箱'=>'邮箱', '公司邮箱'=>'邮箱','公司网址'=>'网址','个人主页'=>'主页');
    	$html = '';
    	foreach ($contacts as $contact)
    	{
    		$property = $contact['property'];
    		$type = $contact['type'];
    		
    		$name = self::GetContactName($contactTypes, $contact['property'], $contact['type']);
    		if ($short && $nameMap[$name])
    		{
    			$name = $nameMap[$name];
    		}

    		$html .= "<p><span $nameStyle>$name</span>";
     		$canVerify = $property == 'email';
    		
    		$hasVerifyIcon = $canVerify && ($user['relation']['friend'] == 2 || $contact['verify']);
    		
    		/*$cutClass = $short ?  'p_cut_ellipsis' : '';*/
/*    		$widthStyle = $short ? ($hasVerifyIcon ? "style='max-width:204px;'" : "style='max-width:224px;'") : '';
			    		
    		$html .= "<div class='fl $cutClass' $widthStyle>";*/
    		
    		if ($property=='email')
    		{
                $content = "{$user['name']}, 您好: \r\n";
                if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
                {
                    $body = rawurlencode(iconv('ISO-8859-1','GB2312//IGNORE', $content."Test"));
                }
                else
                {
                    $body = rawurlencode($content);
                }
//    			$body = str_replace('+', '%20', $body);
//                $body = rawurlencode($body);
    			$html .= "<a id='content_val' href='mailto:{$contact['value']}?body=$body'>{$contact['value']}</a>";
    		}
    		else if ($property=='url')
    		{
    			$url = $contact['value'];
    			if (strpos($url, 'http') !== 0)
    				$url = 'http://'.$url;
    			
    			$html .= "<a id='content_val' href='$url' target='blank'>{$contact['value']}</a>";
    		}
    		else 
    			$html .= "<span id='content_val'>{$contact['value']}</span>";

    		
    		//$adjust = $short ? '&nbsp;' : '';
    		
    		if ($canVerify)
    		{
    			$toVerifyIcon = $user['relation']['friend'] == 2 ? 
    			"<a href='javascript:void(0);' verify_contact_id='{$contact['id']}'
    			verify_contact_type='{$contact['type']}' title='未验证' ><span class='ml05'>未验证</span></a>" : '';
    			$verify = $contact['verify'];
    			$html .= $verify ? "<span class='ml05'>已验证</span>" : $toVerifyIcon;
    		}
    		$html .= "</p>";
    	}

        $addrName = $short ? '地址' : '工作地址';
        if(!!$addr){
            $html .="<div class='clearfix'>";
            $html .= "<span $nameStyle style='float: left;'>$addrName</span><div class='ml65'> ";
        }
        foreach ($addr as $item)
        {
            $html .= "<p>".$item['addr'];
            if ($item['postcode'])
            {
                $html .=  "({$item['postcode']})"."</p>";
            }
        }
        if(!!$addr){
            $html .="</div>";
        }
        $html .="</div>";
    	
    	return $html;
    }
    
    public static function sortExperince($exps)
    {
    	usort($exps, function($a,$b){
    		$aNotEnd = $a['not_ended'];$bNotEnd = $b['not_ended'];
    		$aStart = $a['start_time'];$aEnd = $a['end_time'];
    		$bStart = $b['start_time'];$bEnd = $b['end_time'];
    		if (!$aStart && !$aEnd) return -1;
    		if (!$bStart && !$bEnd) return 1;
    		if ($aNotEnd == $bNotEnd){
    			return ($aStart > $bStart) ? -1 : ($aStart < $bStart ? 1 : ($aEnd > $bEnd ? -1 : 1));
    		}
    		if ($aNotEnd) return -1;
    		
    		return 1;
    	});
    	
    	return $exps;
    }
    
    public static function sortOccupations($occus)
    {
    	usort($occus, function($a,$b){
    		$aNotEnd = $a['not_ended'] || !$a['end_year'];$bNotEnd = $b['not_ended'] || !$b['end_year'];
    		$aStart = $a['start_year'] * 100 + $a['start_month'];$aEnd = $a['end_year'] * 100 + $a['end_month'];
    		$bStart = $b['start_year'] * 100 + $b['start_month'];$bEnd = $b['end_year'] * 100 + $b['end_month'];
    		if (!$aStart && !$aEnd) return -1;
    		if (!$bStart && !$bEnd) return 1;
    		if ($aNotEnd == $bNotEnd){
    			return ($aStart > $bStart) ? -1 : ($aStart < $bStart ? 1 : ($aEnd > $bEnd ? -1 : 1));
    		}
    		if ($aNotEnd) return -1;
    	
    		return 1;
    	});
    		 
    	return $occus;
    }
    
    public static function MergeExperience($educations,$occupations,$birthday,$length=7)
    {
    	$exps = array();
    	foreach ((array)$occupations as $occu)
    	{
    		$time = ProfileHelper::FormatDateYM($occu['start_time']);
    		$start = $occu['start_time'];
    		if ($occu['not_ended']){
    			$time='现在';
    			$start = time();
    		}
    		$exps[] = array('time'=>$time, 'start'=>$start,'title1'=>$occu['org'], 'orgId'=>$occu['company_id'],
    				'title2'=>$occu['job'], 'end'=>$occu['end_time'], 'to_now'=>$occu['not_ended']);
    	}
    		
    	
    	foreach ((array)$educations as $edu)
    	{
    		$time = ProfileHelper::FormatDateYM($edu['start_time']);
    		$start = $edu['start_time'];
    		if ($edu['not_ended']){
    			$time='现在';
    			$start = time();
    		}
    		
    		$degree = $edu["degree"]?$edu["degree"]:2;
    		$degree = self::$EDU_DEGREE[$degree];
    		
    		$exps[] = array('time'=>$time, 'start'=>$start,'title1'=>$edu['school']['name'], 
    				'title2'=>$edu['major']."($degree)");
    	}
    	
    	usort($exps, function($a,$b){
    		return $a['start'] == $b['start'] ? 0 : ($a['start'] > $b['start'] ? -1 : 1);
    	});
    	
    	if (count($exps) > 0 && $birthday){
    		$exps[] = array('time'=>ProfileHelper::FormatDate($birthday), 'title1'=>'呱呱坠地');
    	}
    	
    	$exps = array_slice($exps, 0,$length);
    	
    	return $exps;
    }
    
    public static function contentEscape($content)
    {
    	return nl2br(preg_replace("/[\n]{2,}/","\n\n",trim($content)));
    }
    
    public static function showSettingItem($setting,$str)
    {
    	if ($setting > 0)
    		return "<div><span class='icon_ok fl'></span>
    				<div class='fl'>$str</div><div class='clear'></div></div>";
    	else 
    		return "<div class='f_gray'><div class='fl'>&nbsp;&nbsp;&nbsp;
    				</div><div class='fl'>$str</div><div class='clear'></div></div>";
    }
    
    public static function altSettingItem($setting,$yes,$no)
    {
    	$str = $setting ? $yes : $no;
    	
    	return "<div ><div class='fl'>&nbsp;&nbsp;&nbsp;
    				</div><div class='fl'>$str</div><div class='clear'></div></div>";
    }
    
    public static function checkBoxItem($checked, $id, $str)
    {
    	$state = $checked ? 'checked="checked"' : '';
    	return "<div> <input id='$id' style='margin-right:5px;' type='checkbox' $state><label for='$id' style='cursor:pointer;'>$str</lable></div>";
    }
    
    public static function radioItem($checked, $settingVal,$id, $str)
    {
    	$state = $checked ? 'checked="checked"' : '';
    	return "<div> <input id='$id.$settingVal'  name='$id' type='radio' $state  settingVal='$settingVal' style='margin-right:5px;'>
    	<label for='$id.$settingVal' style='cursor:pointer;'>$str</lable></div>";
    }
    
    public static function showCheckItem($checked)
    {
    	return $checked ? "<div class='icon_ok'></div>" : "<div class='icon_delete2'></div>";
    }
    
    public static function MakeSettingItem($valueId,$value,$valueDesc,$type='check',$currentVal=0)
    {
    	if ($type == 'check')
    		$checkIcon = $value > 0 ? '<span class="icon_ok"></span>' : '&nbsp;&nbsp;&nbsp;';
    	else if ($type == 'radio')
    		$checkIcon = $value == $currentVal ? '<span class="icon_ok"></span>' : '&nbsp;&nbsp;';
    	
    	return "<div class='setting_value_item'><div class='{$type}_switch fl'>$checkIcon</div>
    	<div class='gone' id='$valueId'>$value</div>
    	<div class='gone'>$currentVal</div>
		<div class='fl'>$valueDesc</div><div class='clear'></div></div>";
    }
    
    public static function BriefAdditionalInfo($user, $additionals)
    {
    	$desc ='';
    	if ($user['gender'] != 'n')
    		$desc .= ProfileHelper::$GENDER[$user['gender']];
    	
    	if ($additionals['marriage'] && $additionals['marriage'] != 4)
    	{
    		if ($desc) $desc .= ' | ';
    		$desc .= ProfileHelper::$MARRIAGE[$additionals['marriage']];
    	}
    	 
    	if ($additionals['birthday'])
    	{
    		if ($desc) $desc .= ' | ';
    		$desc .= ProfileHelper::FormatDate($additionals['birthday']).'生';
    	}
    	 
    	if ($additionals['location'])
    	{
    		if ($desc) $desc .= ' | ';
    		$desc .= $additionals['location'];
    	}
    	
    	return $desc;
    }
    
    public static function BriefDesc($additionals,$user)
    {
    	$desc ='';
    	if ($user['gender'] != 'n')
    		$desc .= ProfileHelper::$GENDER[$user['gender']];
    	
    	if ($additionals['birthday'])
    	{
    		if ($desc) $desc .= ', ';
    		$desc .= ProfileHelper::FormatDate($additionals['birthday']).'生';
    	}
    	
    	if ($additionals['marriage'] && $additionals['marriage'] != 4)
    	{
    		if ($desc) $desc .= ', ';
    		$desc .= ProfileHelper::$MARRIAGE[$additionals['marriage']];
    	}
    	
    	$desc1 = '';
//     	if ($additionals['poi']['name'])
//     	{
//     		$desc1 .= '办公学习地点:'.$additionals['poi']['name'];
//     	}
    	if ($additionals['location'])
    	{
    		$desc1 .= $additionals['location'];
    	}
    	
    	$desc2='';
    	if ($user['desc'])
    	{
    		$desc2 .= $user['desc'];
    	} else 
    	{
    		$desc2 .= $desc == '' ? '' : '未填写';
    	}
    	
    	return array('desc'=>$desc, 'desc1'=>$desc1, 'desc2'=>$desc2);
    }
    
    private static function diffYM($to,$from)
    {
    	$yFrom = intval(date('Y', $from));
    	$mFrom = intval(date('m', $from));
    	 
    	$yTo = intval(date('Y', $to));
    	$mTo = intval(date('m', $to));
    	 
    	 
    	$diffM = (12 * $yTo + $mTo) - (12 * $yFrom + $mFrom) + 1;
    	 
    	$y = intval($diffM / 12);
    	$m = $diffM % 12;
    	
    	return array('y'=>$y, 'm'=>$m);
    }
}

?>