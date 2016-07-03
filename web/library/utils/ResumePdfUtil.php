<?php

Yaf_Loader::import(sprintf('%s/tcpdf/tcpdf.php', LIB_PATH));
Yaf_Loader::import(sprintf('%s/tcpdf/config/lang/eng.php', LIB_PATH));

class ResumePdfUtil extends TCPDF
{
    const PDF_CREATOR = '微人脉';
    const PDF_AUTHOR  = '微人脉';
    
    var $resume;
    
    public function __construct( $resume )
    {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->resume = $resume;
        $this->init();
    }
    
    private function init ()
    {
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor(PDF_AUTHOR);
        // set default header data
        $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $this->pdfHeadTitle(), $this->pdfHeadString(),  array(0xc2,0x7c,0x00), array(255,255,255));
        $this->setFooterData($tc=array(0,64,0), $lc=array(255,255,255));
        
        // set header and footer fonts
        $this->setHeaderFont(Array(droidsansfallbackfull, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        //set margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        //set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        //set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        
        $this->SetFont('droidsansfallbackfull', '', 14, 'default', true);
        
        $this->setFontSubsetting(true);
        
        $this->AddPage();
        //$this->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        
    }
    
    public function pdfHeadTitle()
    {
        return null;
    }
    
    public function pdfHeadString()
    {
        return null;
    }
    
    public function SetFont($family, $style='', $size=null, $fontfile='', $subset='default', $out=true) 
    {
        if ($size === null) {
			$size = $this->FontSizePt;
		}
		if ($size < 0) {
			$size = 0;
		}
		// try to add font (if not already added)
 		$fontdata = $this->AddFont($family, $style, $fontfile, $subset);
		$this->FontFamily = $fontdata['family'];
		$this->FontStyle = $fontdata['style'];
		$this->CurrentFont = $this->getFontBuffer($fontdata['fontkey']);
		$this->SetFontSize($size, $out);
    }
    
    /**
     * 创建pdf 
     * @param $dest (string) Destination where to send the document. It can take one of the following values:<ul><li>I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.</li><li>D: send to the browser and force a file download with the name given by name.</li><li>F: save to a local server file with the name given by name.</li><li>S: return the document as a string (name is ignored).</li><li>FI: equivalent to F + I option</li><li>FD: equivalent to F + D option</li><li>E: return the document as base64 mime multi-part email attachment (RFC 2045)</li></ul>
     */
    public function create ( $dest='I' )
    {
        // set document information
        $this->SetTitle( $this->getTitle() );
        $this->SetSubject( $this->getSubject() );
        $html = $this->getBaseInfo().
                 $this->divln().$this->getContactInfo().
                 $this->divln().$this->getCareerInfo().
                 $this->divln().$this->getSkillsInfo().
                 $this->divln().$this->getProjectsInfo().
                 $this->divln().$this->getEducationInfo();

        $this->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
        $this->Output( $this->resume['user']['name'].'的简历.pdf', $dest );
    }
    
    
    private function getTitle ()
    {
        return $this->resume['user']['name'].'简历';
    }
    
    private function getSubject()
    {
        return $this->getTitle();
    }
    
    // base info
    private function baseInfoItem ( $k, $v , $fontsize = 10 )
    {
        if ( $this->empty_string( $v ) )
        {
            return '';
        }
        if ( $k == null )
        {
            return '<tr style="font-size:'.$fontsize.'"><td>'.$v.'</td></tr>';
        }
        else
        {
            return '<tr height="10" style="font-size:'.$fontsize.'"><td color="#aaa" align="left" width="32">'.$k.'</td><td color="#555" align="left">'.$v.'</td></tr>';
        }
    }
    
    private function getGender ()
    {
        return 'm'==$this->resume['user']['gender']?'男':(('f'==$this->resume['user']['gender'])?'女':'保密');
    }
    
    
    private function getIndustryNames ()
    {
        $names = array();
        foreach ( $this->resume['industry']  as $industry )
        {
            $names[] = $industry['name'];
        }
        return implode(',', $names);
    }
    
    private function marriage ()
    {
        if ( $this->resume['additional']['marriage'] == 4 )
        {
            return '';
        }
        $marriage = ProfileHelper::$MARRIAGE[$this->resume['additional']['marriage']];
        if ( $marriage == null )
        {
            $marriage = '';
        }
        else
        {
            $marriage = ', '.$marriage;
        }
        return $marriage;
    }
    
    private function educationDegree ( $education )
    {
        if ( isEmptyString( $education['degree']) )
        {
            return '';
        }
        $degree = ProfileHelper::$EDU_DEGREE[$education['degree']];
        if ( !isEmptyString($degree) )
        {
            $degree = '('.$degree.')';
        }
        else
        {
            $degree = '';
        }
        return $degree;
    }
    
    /**
     * 基本信息
     * @return string
     */
    private function getBaseInfo ()
    {
        $birthday = '';
        if ( !empty($this->resume['additional']['birthday']) )
        {
            $birthday = date(', Y年m月d日生',$this->resume['additional']['birthday']);
        }
        $location = '';
        if ( !isEmptyString ($this->resume['additional']['location']) )
        {
            $location = ', '.$this->resume['additional']['location'];
        } 
        
        $html = '<table width="100%" style="line-height:1.5;">
                    <tr>
                    <td style="width:1%;"></td>
                    <td style="color:#666;width:79%;">'.
                        '<table width="100%" >'.
                            $this->baseInfoItem(null, $this->resume['user']['name'],16).
                            $this->baseInfoItem(null, $this->resume['user']['org'].$this->resume['user']['job']).
                            //$this->baseInfoItem(null, $this->resume['user']['job']).
                            $this->baseInfoItem(null, $this->getGender().$this->marriage().$birthday.$location).
                            '<table width="100%"><tr><td style="height:10px;line-height:0.5;"></td></tr>'.
                                $this->baseInfoItem('行业',$this->getIndustryNames()).
                                $this->baseInfoItem('简介',$this->resume['user']['desc']).
                            '</table>'.
                    '</table>'.  
                '</td>'.
            '<td width="20%" style="text-align:right;"><img src="'.$this->getAvatarUrl().'" width="140" height="140" border="0" /></td></tr></table>';
        return $html;
    }
    
    private function delimerLine()
    {
    	
    	$html = '<tr style="line-height:0px;"><td></td></tr>
    			 <tr style="line-height:0px;"><td style="width:3%; float: left;"></td><td><div style="border-bottom:1px solid #eee; "></div></td></tr>
    			 <tr style="line-height:0px;"><td></td></tr>';
    	return $html;
    }
     
    /**
     * 联系信息
     * @return string
     */
    private function getContactInfo()
    {
        //;color:#
        $html ='';
        if( !empty($this->resume['contact']) || !empty($this->resume['addr']))
        {	
        	$contactInfo = '';
        	foreach ( $this->resume['contact'] as $contact )
        	{
        		$contactInfo = $contactInfo.$this->trhtml($contact['name'], $contact['value']);
        	}
        	
        	$addrs = array();
        	foreach ( $this->resume['addr'] as $addr )
        	{
        		$addrs[] = $addr['addr'].' '.($this->empty_string($addr['postcode']) ? '':('邮编:'.$addr['postcode']));
        	}
        	
        	$addrsInfo = $this->trhtml('工作地址', implode(',', $addrs));
        	
        	$html = $this->sectionHtml('联系方式','',$contactInfo.$addrsInfo);
        }
        return $html;
    }
    
    /**
     * 头像地址
     * @return string
     */
    private function getAvatarUrl ()
    {
        return $this->resume['user']['picture'];
    }

    /**
     * 技能信息
     * @return string
     */
    private function getSkillsInfo ()
    {
        $html ='';
        if(!empty($this->resume['skill']))
        {
            $skillsInfo = '';
            foreach (  $this->resume['skill'] as $key=>$skill )
            {
                $skillsInfo = $skillsInfo.$this->skillDiv('',$skill['text'],'','font-size:12;');
            }
            $skillsInfo = '<tr style="font-size:10;line-height:1.6;"><td width="3%">&nbsp;</td><td style="word-wrap:break-word;" width="97%" align="left">'.$skillsInfo.'</td></tr>';
            $html = $this->sectionHtml('技能','',$skillsInfo );
        }
        return $html;
    }




    private function skillDiv ( $name, $value, $value_gray = null , $style = null , $escape = true )
    {
        if ( $this->empty_string( $value ) )
        {
            return '';
        }
        $fontStyle = '';
        if ( !empty($style) )
        {
            $fontStyle = $style;

        }
        if ( $escape )
        {
            $value = SecurityUtils::escape( $value );
        }
        $nameHtml = '';
        if($name != null){
            $nameHtml = '<span style="color:#aaa;">'.$name.'</span>&nbsp;&nbsp;';
        }
        return '<span style="display:inline-block;">'.$value.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

    }



    /**
     * 职业生涯
     * @return string
     */
    private function getCareerInfo ()
    {
    	$html = '';
    	if (!empty($this->resume['occupation']) )
    	{
    		$careerInfo = '';
    		foreach ( $this->resume['occupation'] as $key=>$occupation )
    		{
    			$timeinfo = ProfileHelper::DiffOccuDate($occupation);
                $careerInfo = $careerInfo.
                            $this->trhtml_new('', $occupation['org'],$this->occupationLocation($occupation), 'font-size:12;color:#C27C00;').
                            $this->trhtml_new('', $occupation['job'],$timeinfo).
                            str_replace("\n", '<br/>',$this->trhtml_new('简介', $occupation['desc']));
    			if(count($this->resume['occupation']) != $key +1)
    			{
    				$careerInfo.=$this->delimerLine();
    			}
    			 
    		}
    		$html = $this->sectionHtml( '职业经历','',$careerInfo );
    	}
        return $html;
    }
    
    private function occupationLocation ($occupation)
    {
        if ( isEmptyString($occupation['location']))
        {
            return '';
        }
        else
        {
           return ' ('.$occupation['location'].')';  
        }
    }
    
    /**
     * 项目信息
     * @return string
     */
    private function getProjectsInfo ()
    {
    	$html ='';
    	if( !empty($this->resume['project']) )
    	{
    		$projectInfo = '';
    		foreach ( $this->resume['project'] as $key=>$project )
    		{
    		    $url = $project['url'];
    		    if ( !isEmptyString($url) )
    		    {
    		        $url = '<a style="text-decoration:none;" href="'.$url.'">'.$url.'</a>';
    		    }
    			$projectInfo = $projectInfo.$this->trhtml_new('', $project['title'],null,'font-size:12; color:#C27C00').
    			$this->trhtml_new('', $project['desc']).
    			$this->trhtml_new('职能', $project['obj_member']['function']).
    			$this->trhtml_new('网址', $url, null,null, false).
    			$this->trhtml_new('所属', $this->empty_string($project['company'])?'个人':$project['company']);
    			 
    			if(count($this->resume['project']) != $key +1)
    			{
    				$projectInfo.=$this->delimerLine();
    			}
    		
    		}
    		$html = $this->sectionHtml('项目','',$projectInfo );
    	}	
        return $html;
    }
    
    /**
     * 教育信息
     * @return string
     */
    private function getEducationInfo ()
    {
        $html ='';
        if( !empty($this->resume['education']) )
        {
        	$educationInfo = '';
        	foreach ( $this->resume['education'] as $key=>$education )
        	{
        		$time = ProfileHelper::DescDate($education['start_time'], $education['end_time'], $education['not_ended'], true);
        		$educationInfo = $educationInfo.$this->trhtml_new('', $education['school']['name'], null , 'font-size:12;').
        		$this->trhtml_new('', $education['department'],$education['major'].$this->educationDegree($education)).
        		$this->trhtml_new('', $time ).
        		$this->trhtml_new('简介', $education['desc']);
        	
        		if(count($this->resume['education']) != $key +1)
        		{
        			$educationInfo.=$this->delimerLine();
        		}
        	}
        	$html = $this->sectionHtml('教育经历','',$educationInfo);
        }	
        return $html;
    }
    
    // 功能函数
    private function startTableWithTitle ( $name,$icon )
    {
        $iconImage = "";
        if (!isEmptyString($icon))
            $iconImage = '<img src="'.BaseBuilder::getImageUrl($icon).'" style="vertical-align:baseline;">';

        return '<table style="color:#666" width="100%" cellspacing="5">
                <tr><td style="line-height:1; " valign="bottom"><span>'.$iconImage.'</span>&nbsp;<font style="font-size:12;">'.$name.'</font></td></tr><tr><td style="line-height:0;height:5px;border-bottom:1px solid #d8edb6;width:100%;"></td></tr></table><table style="color:#666; margin-top:0;" width="100%" cellspacing="5" >';

    }
    private function endtable ()
    {
        return '</table>';
    }
    
    private function &sectionHtml ( $sectionTitle , $icon , $sectionInnerHtml )
    {
        if ( $this->empty_string($sectionInnerHtml) )
        {
            $sectionInnerHtml = '<tr><td></td></tr>';
        }
        $html = $this->startTableWithTitle($sectionTitle,$icon).$sectionInnerHtml.$this->endtable();
        return $html;
    }
    
    private function trhtml ( $name, $value , $color = null , $escape = true )
    {
        if ( $this->empty_string( $value ) )
        {
            return '';
        }
        $colorStyle = '';
        if ( $color != null )
        {
            $colorStyle = 'color:'.$color;
        }
        if ( $escape )
        {
            $value = SecurityUtils::escape( $value );
        }
        return '<tr style="font-size:10;line-height:1.2;"><td width="3%"></td><td width="13%" color="gray">'.$name.'</td><td style="word-wrap:break-word;'.$colorStyle.'" width="84%" align="left">'.$value.'</td></tr>';
    }
    private function trhtml_new ( $name, $value, $value_gray = null , $style = null , $escape = true )
    {
        if ( $this->empty_string( $value ) )
        {
            return '';
        }
        $fontStyle = '';
        if ( !empty($style) )
        {
            $fontStyle = $style;

        }
        if ( $escape )
        {
            $value = SecurityUtils::escape( $value );
        }
        $nameHtml = '';
        if($name != null){
            $nameHtml = '<span style="color:#aaa;">'.$name.'</span>&nbsp;&nbsp;';
        }
        return '<tr style="font-size:10;line-height:1.2;"><td width="3%"></td><td style="word-wrap:break-word;" width="97%" align="left">'.$nameHtml.'<span style="'.$fontStyle.'" >'.$value.'</span>&nbsp;&nbsp;<span style="color:#aaa;">'.$value_gray.'</span></td></tr>';
    }
    
    private function trSihtml ( $value , $color = '#666' )
    {
        if ( $this->empty_string( $value ) )
        {
            return '';
        }
        else 
        {
            $value = SecurityUtils::escape( $value );
            return '<tr style="font-size:10;color:'.$color.'"><td  align="left">'.$value.'</td></tr>';
        }
    }
    
    private function trRatehtml ( $name , $value , $total=5 )
    {
        if ( empty( $value ) )
        {
            return '';
        }
        $value = SecurityUtils::escape( $value );
        $html = '<tr style="font-size:10"><td width="3%"></td><td width="97%" align="left"><table style="width:70px;" cellpadding="1"><tr>';
        $evaluate_icon = BaseBuilder::getImageUrl('evaluate_icon.png');
        $evaluate_icon_a = BaseBuilder::getImageUrl('evaluate_icon_a.png');
        for ( $index = 0; $index < $total; $index++ )
        {
            $html = $html.'<td><img src="'.($index>=$value?$evaluate_icon:$evaluate_icon_a).'" width="18px" class="pointer"/></td>';
        }
        $html = $html.'</tr></table></td></tr>';
        return $html;
    }
    
    private function trlnhtml()
    {
        return '<tr style="line-height:0px;"><td></td></tr>';
    }
    
    public function divln()
    {
        return '<div style="height:10"></div>';
    }
    
}