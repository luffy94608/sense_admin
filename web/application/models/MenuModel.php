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
    public $tbl_name = 'menus';

    public $keyMap = array(
        'name'=>'name',
        'url'=>'url',
        'page_id'=>'page_id',
        'type'=>'type',
        'module'=>'module',
        'btn_type'=>'btn_type',
        'show_type'=>'show_type',
        'status'=>'status',
        'parent_id'=>'parent_id',
        'target'=>'target',
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
    public function createMenu($params)
    {
        $data = [];
        $maps  = $this->keyMap;
        foreach($maps as $k=>$v)
        {
            if(array_key_exists($v,$params) && $v!==false)
            {
                $data[$k]=$params[$v];
            }
        }

        $result = $this->web->insertTable($this->tbl_name,$data);

        if($result && !empty($params['params']))
        {
            $this->updateSubMenus($result,$params['params']);
        }

        return $result;
    }

    /**
     * 更新页面
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function updateMenu($id,$params)
    {
        if(!empty($params))
        {
            $map=$this->keyMap;
            $data=array();
            foreach($params as $k=>$v)
            {
                if(array_key_exists($k,$map) && $v!==false)
                {
                    $data[$map[$k]]=$v;
                }
            }

            $this->web->updateTable($this->tbl_name,$data,HaloPdo::condition('id = ?',$id));
            if($id && !empty($params['params']))
            {
                $this->updateSubMenus($id,$params['params']);
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
    public function deleteMenu($id)
    {
        $result =  $this->web->delRowByCondition2($this->tbl_name,HaloPdo::condition('id = ?',$id));
        if($result){
            $this->web->delRowByCondition2($this->tbl_name,HaloPdo::condition('parent_id = ?',$id));
        }

        return $result;
    }

    /**
     * 获取二级菜单
     * @return array
     */
    public function getSubMenuList()
    {
        $res = [];
        $result = $this->web_slave->getResultsByCondition($this->tbl_name,sprintf('parent_id = 0 AND type=1 AND module = 0'));
        if($result)
        {
            $ids = [];
            foreach ($result as $v)
            {
                $ids[]= $v['id'];
            }
            $result2 = $this->web_slave->getResultsByCondition($this->tbl_name,sprintf('parent_id IN (%s)',implode(',',$ids)));
            $res = $result2;
        }
        return $res;
    }

    /**
     * 获取叶敏啊 列表
     * @return array
     */
    public function getMenuList($module)
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_name,sprintf('type IN (1,2) AND module = %d ORDER BY sort_num ASC',$module));
        if($result)
        {
            $this->addExtraInfo($result);
        }
        $data = [
            'list'=>$result ? $result : [],
            'total'=>0,
        ];
        return $data;
    }


    /**
     * 获取类别 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getMenuDetail($id)
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_name,HaloPdo::condition('id= ?',$id));
        if($result)
        {
            $this->addExtraInfo($result);
            $result = $result[0];
        }
        return $result;
    }

    public function saveSort($data)
    {
        return $this->web->batchUpdateData($this->tbl_name,array_keys($data[0]),$data,'sort_num= values(sort_num)');
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
                    if(array_key_exists($v,$param) && $param[$v]!==false)
                    {
                        $tmpItem[$k]=$param[$v];
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
     * 批量 menu
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function updateSubMenus($id,$params)
    {
        $relationKey = 'parent_id';
        $maps  = $this->keyMap;
        $tbl_name = $this->tbl_name;
        $this->updateCommonParams($tbl_name,$relationKey,$maps,$id,$params);

    }

    /**
     * 添加类别参数信息
     * @param $result
     */
    public function addExtraInfo(&$result)
    {
        $ids = [];
        $typeIds = [];
        foreach ($result as $v)
        {
            $ids[] = $v['id'];
            $typeIds[] = $v['lock_type_id'];
        }
        $paramsResult = $this->web_slave->getResultsByCondition($this->tbl_name,sprintf('parent_id IN (%s) ORDER BY sort_num ASC',implode(',',$ids)));
        $paramsMap = [];
        if($paramsResult)
        {
            foreach ($paramsResult as $item)
            {
                $paramsMap[$item['parent_id']][] = $item;
            }
        }
//
//        $typeIds = array_unique($typeIds);
//        $types = $this->web_slave->getResultsByCondition('lock_types',sprintf('id IN (%s)',implode(',',$typeIds)),'id,name');
//        $typeMap = [];
//        if($types)
//        {
//            foreach ($types as $type)
//            {
//                $typeMap[$type['id']] = $type;
//            }
//        }

        foreach ($result as &$v)
        {
            $parentId = $v['id'];
//            $tmpTypeId = $v['lock_type_id'];
//            if(array_key_exists($tmpTypeId,$typeMap))
//            {
//                $v['type'] = $typeMap[$tmpTypeId];
//            }
            if(array_key_exists($parentId,$paramsMap))
            {

                $v['params'] = $paramsMap[$parentId];
            }
        }
    }

}













