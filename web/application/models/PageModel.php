<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */

class PageModel extends Halo_Model
{

    private $web;
    private $web_slave;
    public $tbl_page_name = 'pages';
    public $tbl_type_name = 'page_types';
    public $tbl_link_name = 'page_links';
    public $tbl_content_name = 'page_contents';

//    public $keyTypeMap = array(
//        'name'=>'name',
//        'title'=>'title',
//        'img'=>'img',
//        'content'=>'content',
//    );

    public $keyPageMap = array(
        'name'=>'name',
        'title'=>'title',
        'keywords'=>'keywords',
        'description'=>'description',
        'banner'=>'banner',
        'extra'=>'extra',
        'page_type_id'=>'page_type_id',
    );

    public $keyContentMap = array(
        'page_id'=>'page_id',
        'title'=>'title',
        'sub_title'=>'sub_title',
        'content'=>'content',
        'pic'=>'pic',
        'position'=>'position',
        'icon'=>'icon',
        'icon_active'=>'icon_active',
        'sort_num'=>'sort_num',
    );

    public $keyLinkMap = array(
        'name'=>'name',
        'url'=>'url',
        'target'=>'target',
        'page_content_id'=>'page_content_id',
        'sort_num'=>'sort_num',
    );



    public function __construct()
    {
        parent::__construct();
        $this->web = DataCenter::getDb('web');
        $this->web_slave = DataCenter::getDb('web_slave');
    }

    /**
     * 创建页面
     * @param $params
     * @return bool|int
     */
    public function createPage($params)
    {
        $timeStr = date('Y-m-d H:i:s');
        $data = [];
        $maps  = $this->keyPageMap;
        foreach($maps as $k=>$v)
        {
            if(array_key_exists($v,$params) && $v!==false)
            {
                $data[$k]=$params[$v];
            }
        }
        $data['created_at'] = $timeStr;
        $data['updated_at'] = $timeStr;

        $result = $this->web->insertTable($this->tbl_page_name,$data);
        if($result && !empty($params['contents']))
        {
            $this->updatePageContents($result,$params['contents']);
        }

        return $result;
    }

    /**
     * 更新页面
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function updatePage($id,$params)
    {
        if(!empty($params))
        {
            $map=$this->keyPageMap;
            $data=array();
            foreach($params as $k=>$v)
            {
                if(array_key_exists($k,$map) && $v!==false)
                {
                    $data[$map[$k]]=$v;
                }
            }

            $data['updated_at'] = date('Y-m-d H:i:s');

            $this->web->updateTable($this->tbl_page_name,$data,HaloPdo::condition('id = ?',$id));
            if($id && !empty($params['contents']))
            {
                $this->updatePageContents($id,$params['contents']);
            }
            return true;
        }
        return false;
    }
    /**
     * 删除页面
     * @param $id
     * @return int
     */
    public function deletePage($id)
    {
        $result =  $this->web->delRowByCondition2($this->tbl_page_name,HaloPdo::condition('id = ?',$id));
        if($result)
        {
            $contents  = $this->web_slave->getResultsByCondition($this->tbl_content_name,HaloPdo::condition('page_id',$id));
            if($contents)
            {
                $delIds= [];
                foreach ($contents as $content)
                {
                    $delIds[]= $content['id'];
                }

                $this->db->delRowByCondition2($this->tbl_content_name,HaloPdo::condition('page_id',$id));
                $this->db->delRowByCondition2($this->tbl_link_name,sprintf('page_content_id IN (%s)',implode(',',$delIds)));

            }
        }
        return $result;
    }

    /**
     * 获取叶敏啊 列表
     * @return array
     */
    public function getPageList()
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_page_name,sprintf('id>0  ORDER BY updated_at DESC'));
        $total = $this->web_slave->getCountByCondition($this->tbl_page_name,HaloPdo::condition('id>0'));

        if($result)
        {
            $this->addExtraInfo($result);
        }

        $data = [
          'list'=>$result ? $result : [],
          'total'=>intval($total),
        ];
        return $data;
    }

    /**
     * 获取 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getPageDetail($id)
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_page_name,HaloPdo::condition('id= ?',$id));
        if($result)
        {
            $this->addExtraInfo($result);
            $result = $result[0];
        }

        return $result;
    }

    /**
     * @param $tblName
     * @param $relationKey //外键key
     * @param $id  //外键值
     * @param $keyMap //table key 映射map
     * @param $params //更新数据
     * @return bool
     */
    public function updateCommonParams($tblName,$relationKey,$keyMap,$id,$params)
    {
//        if(($params))
        {
            $maps  = $keyMap;
            $tbl_name = $tblName;

            $subResultMap=array();
            $insertData=array();
            $insertBakData=array();
            $updateData=array();
            $existIds=array();
            $updateIds=array();

            $subTmpResult=$this->web->getResultsByCondition($tbl_name,HaloPdo::condition("{$relationKey}=?",$id));
            if($subTmpResult)
            {
                foreach($subTmpResult as $v)
                {
                    $existIds[]=$v['id'];
                    $subResultMap[$v['id']]=$v;
                }
            }
            $batchUpdateStrArr = [];
            $paramsMap = [];
            foreach($params as $param)
            {
                $tmpItem = [];
                $tmpLinkItem = [];
                $tmpId= isset($param['id'])?$param['id']:'';

                if($tmpId && !empty($param['links']))
                {
                    $paramsMap[$tmpId] = $param['links'];
                }

                foreach($maps as $k=>$v)
                {

                    if(array_key_exists($v,$param) && $param[$v]!==false)
                    {
                        $tmpItem[$k]=$param[$v];
                        $tmpLinkItem[$k]=$param[$v];
                        $batchUpdateStrArr[] = sprintf('%s=VALUES(%s)',$k,$k);
                    }

                }
                if($tbl_name == 'page_contents' && !empty($param['links']))
                {
                    $tmpLinkItem['links'] = $param['links'];
                }
                if(!empty($tmpId) && array_key_exists($tmpId,$subResultMap))
                {
                    $updateIds[]=$tmpId;
                    $tmpResultItem = $subResultMap[$tmpId];

                    $updateStatusArr =  [];
                    //判断是否需要更新
                    foreach($maps as $k=>$v)
                    {
                        if(array_key_exists($k,$param) && array_key_exists($k,$tmpResultItem))
                        {
                            if($param[$k] == $tmpResultItem[$k]){
                                $updateStatusArr[] =  true;
                            }else{
                                $updateStatusArr[] =  false;
                            }
                        }
                    }
                    //更新link
                    if($tbl_name == 'page_contents')
                    {
                        if(array_key_exists($tmpId,$paramsMap))
                        {
                            $this->updatePageLinks($tmpId,$paramsMap[$tmpId]);
                        }
                    }
                    if(in_array(false,$updateStatusArr,true))
                    {
                        $tmpItem['id']= $tmpId;
                        $updateData[]=$tmpItem;
                    }

                }
                else
                {
                    $tmpItem[$relationKey]=$id;
                    $insertData[]=$tmpItem;
                    $insertBakData[]=$tmpLinkItem;
                }
            }
            if(count($updateData))
            {
                $batchUpdateStrArr = array_unique($batchUpdateStrArr);
                $this->web->batchUpdateData($tbl_name,array_keys($updateData[0]),$updateData,implode(',',$batchUpdateStrArr));
            }
            if(count($insertData))
            {
//                $this->web->batchInsertData($tbl_name,array_keys($insertData[0]),$insertData);
                foreach ($insertData as $key => $insertITem)
                {
                    $result = $this->web->insertTable($tbl_name,$insertITem);
                    if($result && $tbl_name == 'page_contents')
                    {
                        if($insertBakData[$key]['links'])
                        {
                            $this->updatePageLinks($result,$insertBakData[$key]['links']);
                        }
                    }
                }


            }
            $deleteIds=array_diff($existIds,$updateIds);
            if(count($deleteIds))
            {
                $this->web->delRowByCondition2($tbl_name,sprintf('id IN (%s)',implode(',',$deleteIds)));
                if($tbl_name == 'page_contents')
                {
                    $this->web->delRowByCondition2($this->tbl_link_name,sprintf('page_content_id IN (%s)',implode(',',$deleteIds)));
                }

            }

        }
        return false;
    }

    /**
     * 批量更新页面内容 type
     * @param $pageId
     * @param $params
     * @return bool|int
     */
    public function updatePageContents($pageId,$params)
    {
        $relationKey = 'page_id';
        $maps  = $this->keyContentMap;
        $tbl_name = $this->tbl_content_name;
        $this->updateCommonParams($tbl_name,$relationKey,$maps,$pageId,$params);

    }

    /**
     * 批量更新页面内容链接 type
     * @param $pageId
     * @param $params
     * @return bool|int
     */
    public function updatePageLinks($pageId,$params)
    {
        $relationKey = 'page_content_id';
        $maps  = $this->keyLinkMap;
        $tbl_name = $this->tbl_link_name;
        $this->updateCommonParams($tbl_name,$relationKey,$maps,$pageId,$params);

    }


    /**
     * 添加类别参数信息
     * @param $result
     */
    public function addExtraInfo(&$result)
    {
        $pageIds = [];
        $typeIds = [];
        foreach ($result as $v)
        {
            $pageIds[] = $v['id'];
            $typeIds[] = $v['page_type_id'];
        }
        //content
        $paramsResult = $this->web_slave->getResultsByCondition($this->tbl_content_name,sprintf('page_id IN (%s) ORDER BY sort_num ASC',implode(',',$pageIds)));
        $paramsMap = [];
        $contentIds = [];
        if($paramsResult)
        {
            foreach ($paramsResult as $item)
            {
                $paramsMap[$item['page_id']][] = $item;
                $contentIds[] = $item['id'];
            }
        }
        //link
        $contentToLinksMap = [];
        if($contentIds)
        {
            $links = $this->web_slave->getResultsByCondition($this->tbl_link_name,sprintf('page_content_id IN (%s) ORDER BY sort_num ASC',implode(',',$contentIds)));
            if($links)
            {
                foreach ($links as $link)
                {
                    $contentToLinksMap[$link['page_content_id']][] = $link;
                }
            }
        }

        foreach ($paramsMap as &$item)
        {
            if(is_array($item))
            {
                foreach ($item as &$itemValue)
                {
                    $cId = $itemValue['id'];
                    if(array_key_exists($cId,$contentToLinksMap))
                    {
                        $itemValue['links'] = $contentToLinksMap[$cId];
                    }
                }
            }
        }

        //type
        $typeIds = array_unique($typeIds);
        $typeMap = [];
        $typeResult = $this->web_slave->getResultsByCondition($this->tbl_type_name,sprintf('id IN (%s)',implode(',',$typeIds)));
        if($typeResult)
        {
            foreach ($typeResult as $typeItem)
            {
                $typeMap[$typeItem['id']]=$typeItem;
            }
        }

        foreach ($result as &$v)
        {
            $id = $v['id'];
            if(array_key_exists($id,$paramsMap))
            {
                $v['contents'] = $paramsMap[$id];
            }

            if(array_key_exists($v['page_type_id'],$typeMap))
            {
                $v['type'] = $typeMap[$v['page_type_id']];
            }

        }
    }

    /**
     * 获取所有页面类型
     * @return array|bool|string
     */
    public function getPageTypes()
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_type_name);
        return $result;
    }



    /**
     * 创建页面
     * @param $params
     * @return bool|int
     */
    public function createPageContentInfo($params)
    {
        $data = [];
        $maps  = $this->keyContentMap;
        foreach($maps as $k=>$v)
        {
            if(array_key_exists($v,$params) && $v!==false)
            {
                $data[$k]=$params[$v];
            }
        }

        $result = $this->web->insertTable($this->tbl_content_name,$data);
        if($result && !empty($params['links']))
        {
            $this->updatePageLinks($result,$params['links']);
        }

        return $result;
    }

    /**
     * 更新页面
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function updatePageContentInfo($id,$params)
    {
        if(!empty($params))
        {
            $map=$this->keyContentMap;
            $data=array();
            foreach($params as $k=>$v)
            {
                if(array_key_exists($k,$map) && $v!==false)
                {
                    $data[$map[$k]]=$v;
                }
            }


            $this->web->updateTable($this->tbl_content_name,$data,HaloPdo::condition('id = ?',$id));
            if($id && isset($params['links']))
            {
                $this->updatePageLinks($id,$params['links']);
            }
            return true;
        }
        return false;
    }
    /**
     * 删除页面
     * @param $id
     * @return int
     */
    public function deletePageContent($id)
    {
        $result =  $this->web->delRowByCondition2($this->tbl_content_name,HaloPdo::condition('id = ?',$id));
        if($result)
        {
            $this->db->delRowByCondition2($this->tbl_link_name,sprintf('page_content_id =%s',$id));

        }
        return $result;
    }

    /**
     * 获取叶敏啊 列表
     * @return array
     */
    public function getPageContentList()
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_content_name,sprintf('page_id=0  ORDER BY sort_num ASC'));
        $total = $this->web_slave->getCountByCondition($this->tbl_content_name,HaloPdo::condition('id>0'));

        if($result)
        {
            $this->addExtraLinksInfo($result);
        }

        $data = [
            'list'=>$result ? $result : [],
            'total'=>intval($total),
        ];
        return $data;
    }

    /**
     * 获取 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getPageContentDetail($id)
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_content_name,HaloPdo::condition('id= ?',$id));
        if($result)
        {
            $this->addExtraLinksInfo($result);
            $result = $result[0];
        }

        return $result;
    }

    /**
     * 添加链接信息
     * @param $result
     */
    public function addExtraLinksInfo(&$result)
    {
        $pageIds = [];
        foreach ($result as $v)
        {
            $pageIds[] = $v['id'];
        }
        $paramsResult = $this->web_slave->getResultsByCondition($this->tbl_link_name,sprintf('page_content_id IN (%s) ORDER BY sort_num ASC',implode(',',$pageIds)));
        $paramsMap = [];
        if($paramsResult)
        {
            foreach ($paramsResult as $item)
            {
                $paramsMap[$item['page_content_id']][] = $item;
            }
        }

        foreach ($result as &$v)
        {
            $id = $v['id'];
            if(array_key_exists($id,$paramsMap))
            {
                $v['links'] = $paramsMap[$id];
            }

        }
    }

    /**
     * @param $data
     * @return int
     */
    public function saveContentSort($data)
    {
        return $this->web->batchUpdateData($this->tbl_content_name,array_keys($data[0]),$data,'sort_num= values(sort_num)');
    }
}













