<?php

class ICSGeneratorUtil
{

    /**
     * The event start date
     * @var DateTime
     */
    private $_start;

    /**
     * The event end date
     * @var DateTime
     */
    private $_end;

    /**
     * 
     * The name of the event
     * @var string
     */
    private $_name;

    private $_generated = false;

    public function __construct()
    {
        $this->_uid = uniqid();
        return $this;
    }

    public function getUID()
    {
        return $this->_uid;
    }

    /**
     * 
     * Set the event stat and end time.
     * 
     * @param DateTime $start
     * @param DateTime $end
     * @return \ICSGeneratorUtil 
     */
    public function setDate($start, $end)
    {
        $this->setStart($start);
        $this->setEnd($end);

        return $this;
    }

    public function setStart($start)
    {
        $this->_start = $start;
        return $this;
    }

    public function setEnd($end)
    {
        $this->_end = $end;
        return $this;
    }

    /**
     * Set the name of the event
     * @param string $name
     * @return \ICSGeneratorUtil 
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * 
     * Get the event name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * Just to do it.
     * @return \ICSGeneratorUtil 
     */
    public function __toString()
    {
        return $this;
    }

    /**
     * Get the start time set for the even
     * @return string
     */
    public function getStart($formatted = null)
    {
        if (null !== $formatted)
        {
            return date('Ymd\THis\Z', $this->_start);
        }

        return $this->_start;
    }

    /**
     * Get the end time set for the event
     * @return string
     */
    public function getEnd($formatted = null)
    {
        if (null !== $formatted)
        {
            return date('Ymd\THis\Z', $this->_end);
        }
        return $this->_end;
    }

    /**
     * 
     * Call this function to download the ICSGeneratorUtil. 
     */
    public function download()
    {
//        $_SESSION['calander_ICSGeneratorUtil_downloaded'] = self::DOWNLOADED;
        $generate = $this->_generate();
        @header("Pragma: public");
        @header("Expires: 0");
        @header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        @header("Cache-Control: public");
        @header("Content-Description: File Transfer");
        @header("Content-type: application/octet-stream");
        @header("Content-Disposition: attachment; filename=\"{$this->getName()}.ics\"");
        @header("Content-Transfer-Encoding: binary");
        @header("Content-Length: " . strlen($generate));
        print $generate;
    }

    public function getContent()
    {
        if (!$this->_generated)
        {
            if ($this->isValid())
            {
                if ($this->_generate())
                {
                    return $this->_generated;
                }
            }
            return false;
        }

        return $this->_generated;
    }
    
    public function generate()
    {
        $this->_generate();
        return $this;
    }
    private function _generate()
    {

        $content = "BEGIN:VCALENDAR\n";
        $content .= "CALSCALE:GREGORIAN\n";
        $content .= "VERSION:2.0\n";
        $content .= "METHOD:PUBLISH\n";

        $content .= "BEGIN:VEVENT\n";
        $content .= "UID:{$this->getUID()}\n";
        $birthday = date('Ymd', $this->_end);
        $content .= "DTEND:VALUE=DATE:{$birthday}\n";
        $content .= "RRULE:FREQ=YEARLY\n";
        $content .= "TRANSP:OPAQUE\n";
        $content .= "SUMMARY:{$this->getName()}\n";
        $content .= "DTSTART:{$birthday}\n";

        $start = date('Ymd\THis\Z', $this->_start);
        $content .= "DTSTAMP:{$start}\n";
        //fix
        $time = date("Ymd\THis\Z", time());
        $content .= "CREATED:{$time}\n";
        $content .= "SEQUENCE:0\n";
        $content .= "END:VEVENT\n";

        $content .= "END:VCALENDAR";

        $this->_generated = $content;

        return $this->_generated;
    }
}