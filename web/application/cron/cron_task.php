<?php
/**
 * Created by PhpStorm.
 * User: su
 * Date: 5/7/15
 * Time: 2:54 PM
 *
 */
require_once dirname(__FILE__) . '/../configs/SystemConfig.php';

$config = HaloEnv::getConfig();
$serverRoot = $config['server']['root'];
$_SERVER['DOCUMENT_ROOT'] = $serverRoot;

class WeiboTask
{

    public  $aliasInfo;

    /**
     * @var CronModel
     * */
    public  $model;

    public $minDuration;

    public $maxDuration;

    public $minPraiseCount;

    public $maxPraiseCount;

    public  function init()
    {
        $this->model = new CronModel();
        $this->aliasInfo = $this->model->getAliasInfo();
        $this->minDuration = 30;
        $this->maxDuration = 180;
        $this->minPraiseCount = 2;
        $this->minPraiseCount = 11;
    }

    public function getMicroTime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return $sec*1000 + intval($usec*1000);
    }

    public function alias($type = 0)
    {

        $row = $this->model->getLastPriseId($type);
        $end_id = 0;
        if($row)
        {
            $end_id = $row['target_id'];
        }


        $countInfo = $this->model->getPraiseCount($type);

        if($countInfo)
        {
            foreach($countInfo as $value)
            {
                $filter  = array();
                if($value['praised'])
                {
                    foreach($value['praised'] as $praised)
                    {
                        $filter[] = $praised['source_id'];
                    }
                }

                $tmpId = $this->getRandomAliasUid($filter);
                $uid = $this->aliasInfo[$tmpId - 1]['uid'];

                if($type == 0)
                {

                    $praiseParam = array('feed_id'=>$value['target_id'],'uid'=>$uid,'like'=>1);
                    $result = HaloClient::singleton()->postData(HaloClient::ALIAS_PRAISE_FEED,$praiseParam);
                }
                else
                {
                    $praiseParam = array('secret_id'=>$value['target_id'],'uid'=>$uid,'like'=>1);
                    $result = HaloClient::singleton()->postData(HaloClient::ALIAS_PRAISE_SECRET,$praiseParam);
                }
                $this->model->praisedFeed($value['target_id'],$tmpId,$type);
                $this->model->setPraiseCount($value['id'],$value['count'] - 1,rand($this->minDuration,$this->maxDuration));
            }

        }

        $this->model->updateAliasDuration($type);


        $targetResult = $this->getNextPageData($type);


        $data = $targetResult['data'];
        if(is_array($data) || $data)
        {
            if(!$end_id)
            {
                $end_id = $targetResult['last_id'];
            }
            while(1)
            {
                foreach($data as $value)
                {
                    if($value['limit'] == 1)
                    {
                        continue;
                    }
                    $target_id = $value[$targetResult['key']];
                    if($target_id <= $end_id)
                    {
                        break 2;
                    }

                    $count = rand($this->minPraiseCount - 1,$this->maxPraiseCount - 1);

                    $id  = rand(1,count($this->aliasInfo));
//                    $uid = $this->aliasInfo[$id - 1]['uid'];

//                    if($type == 0)
//                    {
//                        $praiseParam = array('feed_id'=>$target_id,'uid'=>$uid,'like'=>1);
//                        $result = HaloClient::singleton()->postData(HaloClient::ALIAS_PRAISE_FEED,$praiseParam);
//                    }
//                    else
//                    {
//                        $praiseParam = array('secret_id'=>$target_id,'uid'=>$uid,'like'=>1);
//                        $result = HaloClient::singleton()->postData(HaloClient::ALIAS_PRAISE_SECRET,$praiseParam);
//                    }
                    //$this->model->praisedFeed($target_id,$id ,$type);
                    $this->model->setPraiseCount(0,$count,30,$type,$target_id);
                }

                $targetResult = $this->getNextPageData($type,$result);
                $data = $targetResult['data'];
                if(!is_array($data) || !$data )
                {
                    break;
                }
            }
        }
    }

    public function getRandomAliasUid($filter)
    {
        $id = 0;
        $count = count($this->aliasInfo);
        if($count <= count($filter))
        {
            return $id;
        }

        while (1)
        {
            $tmp = rand(1, $count);
            if (!in_array($tmp, $filter))
            {
                $id = $tmp;
                break;
            }
        }

        return $id;
    }

    public function getNextPageData($type,$result = null)
    {
        if(!$result)
        {
            $result = array();
        }
        if($type == 0)
        {
            $param = array('cursor_id'=>$this->getMicroTime(),'count'=>20,'offset'=>0);
            if($result['param'])
            {
                $param = $result['param'];
                $param['offset'] = $param['offset'] + 20;
                $result['param'] = $param;
            }
            $targetResult =  HaloClient::singleton()->postData(HaloClient::DYNAMIC_GET_LIST,$param);
            $result['data'] = $targetResult['list_info'];
            $result['last_id'] = $result['data'][count($result['data'])-1]['feed_id'];
            $result['key'] = 'feed_id';
            $result['param'] = $param;
        }
        elseif($type == 1)
        {
            $param = array('cursor_id'=>0,'max_count'=>20,'offset'=>0,'past'=>0);
            if($result['param'])
            {
                $param = $result['param'];
                $data = $result['data'];
                $param['cursor_id'] = $data[count($data)-1]['secret_id'];
                $param['past'] = 1;
                $result['param'] = $param;
            }
            $targetResult =  HaloClient::singleton()->postData(HaloClient::SECRET_GET_LIST,$param);
            $result['data'] = $targetResult;
            $result['last_id'] = $result['data'][count($result['data'])-1]['secret_id'];
            $result['key'] = 'secret_id';
            $result['param'] = $param;
        }
        return $result;
    }



}


$weiboTask = new WeiboTask();
$weiboTask->init();
$weiboTask->alias(0);
$weiboTask->alias(1);
