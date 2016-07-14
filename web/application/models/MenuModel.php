<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */

class MenuModel extends Halo_Model
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
        'icon'=>'icon',
        'icon_active'=>'icon_active',
        'sort_num'=>'sort_num',
    );

    public $keyLinkMap = array(
        'name'=>'name',
        'url'=>'url',
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
        if($result && empty($params))
        {
            $this->updatePageContents($result,$params);
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
            if($id && empty($params))
            {
                $this->updatePageContents($id,$params);
            }
            return false;
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
        $data = [
          'list'=>$result ? $result : [],
          'total'=>intval($total),
        ];
        return $data;
    }

    /**
     * 获取类别 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getPageDetail($id)
    {
        $result = $this->web_slave->getRowByCondition($this->tbl_page_name,HaloPdo::condition('id= ?',$id));
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
        if(!empty($params))
        {
            $maps  = $keyMap;
            $tbl_name = $tblName;

            $subResultMap=array();
            $insertData=array();
            $updateData=array();
            $existIds=array();
            $updateIds=array();

            $subTmpResult=$this->db->getResultsByCondition($tbl_name,HaloPdo::condition("{$relationKey}=?",$id));
            if($subTmpResult)
            {
                foreach($subTmpResult as $v)
                {
                    $existIds[]=$v['id'];
                    $subResultMap[$v['id']]=$v;
                }
            }
            $batchUpdateStrArr = [];
            foreach($params as $param)
            {
                $tmpItem = [];
                $tmpId= $param['id'];

                foreach($maps as $k=>$v)
                {
                    if(array_key_exists($v,$param) && $v!==false)
                    {
                        $tmpItem[$k]=$params[$v];
                        $batchUpdateStrArr[] = sprintf('%s=VALUES(%s)',$k,$k);
                    }
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
                }
            }
            if(count($updateData))
            {
                $batchUpdateStrArr = array_unique($batchUpdateStrArr);
                $this->db->batchUpdateData($tbl_name,array_keys($updateData[0]),$updateData,implode(',',$batchUpdateStrArr));
            }
            if(count($insertData))
            {
                $this->db->batchInsertData($tbl_name,array_keys($insertData[0]),$insertData);
            }
            $deleteIds=array_diff($existIds,$updateIds);
            if(count($deleteIds))
            {
                $this->db->delRowByCondition2($tbl_name,sprintf('id IN (%s)',implode(',',$deleteIds)));
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
    public function addLockExtraInfo(&$result)
    {
        $lockIds = [];
        $typeIds = [];
        foreach ($result as $v)
        {
            $lockIds[] = $v['id'];
            $typeIds[] = $v['lock_type_id'];
        }
        $paramsResult = $this->web_slave->getResultsByCondition('lock_params',sprintf('lock_id IN (%s) ORDER BY sort_num ASC',implode(',',$lockIds)));
        $paramsMap = [];
        if($paramsResult)
        {
            foreach ($paramsResult as $item)
            {
                $paramsMap[$item['lock_id']][] = $item;
            }
        }

        $typeIds = array_unique($typeIds);
        $types = $this->web_slave->getResultsByCondition('lock_types',sprintf('id IN (%s)',implode(',',$typeIds)),'id,name');
        $typeMap = [];
        if($types)
        {
            foreach ($types as $type)
            {
                $typeMap[$type['id']] = $type;
            }
        }

        foreach ($result as &$v)
        {
            $lockId = $v['id'];
            $tmpTypeId = $v['lock_type_id'];
            if(array_key_exists($tmpTypeId,$typeMap))
            {
                $v['type'] = $typeMap[$tmpTypeId];
            }
            if(array_key_exists($lockId,$paramsMap))
            {

                $v['params'] = $paramsMap[$lockId];
            }
        }
    }

}













