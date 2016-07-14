<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */

class CloudModel extends Halo_Model
{

    private $web;
    private $web_slave;
    public $tbl_name = 'clouds';
    public $tbl_sub_name = 'cloud_params';
    public $timestamps = false;

    public $keyMap = array(
        'name'=>'name',
        'sort_num'=>'sort_num',
        'type'=>'type',
        'download_ids'=>'download_ids',
    );

    public $keySubMap = array(
        'name'=>'name',
        'content'=>'content',
        'cloud_id'=>'cloud_id',
        'sort_num'=>'sort_num',
    );

    public function __construct()
    {
        parent::__construct();
        $this->web = DataCenter::getDb('web');
        $this->web_slave = DataCenter::getDb('web_slave');
    }

    /**
     * 修改
     * @param $params
     * @return bool|int
     */
    public function create($params)
    {
        $timeStr = date('Y-m-d H:i:s');
        $data = [];
        $maps  = $this->keyMap;
        foreach($maps as $k=>$v)
        {
            if(array_key_exists($v,$params) && $v!==false)
            {
                $data[$k]=$params[$v];
            }
        }
        if($this->timestamps){
            $data['created_at'] = $timeStr;
            $data['updated_at'] = $timeStr;
        }

        $result = $this->web->insertTable($this->tbl_name,$data);
        if($result && !empty($params['params']))
        {
            $this->updateParams($result,$params['params']);
        }
        return $result;
    }

    /**
     * 更新
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function update($id,$params)
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
            if($this->timestamps){
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            $result =  $this->web->updateTable($this->tbl_name,$data,HaloPdo::condition('id = ?',$id));
            if($id && !empty($params['params']))
            {
                $this->updateParams($id,$params['params']);
            }
            return true;
        }
        return false;
    }


    /**
     * 更新参数
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function updateParams($id,$params)
    {
        if(!empty($params))
        {
            $subResultMap=array();
            $insertData=array();
            $updateData=array();
            $existIds=array();
            $updateIds=array();

            $subTmpResult=$this->db->getResultsByCondition($this->tbl_sub_name,HaloPdo::condition('cloud_id=?',$id));
            if($subTmpResult)
            {
                foreach($subTmpResult as $v)
                {
                    $existIds[]=$v['id'];
                    $subResultMap[$v['id']]=$v;
                }
            }
            foreach($params as $param)
            {
                $tmpItem=array();
                $tmpId= $param['id'];
                $name = $param['name'];
                $content = $param['content'];
                $sortNum = $param['sort_num'];
                $tmpItem['name']=$name;
                $tmpItem['content']=$content;
                $tmpItem['sort_num']=$sortNum;
                $tmpItem['cloud_id']=$id;

                if(!empty($param['id']) && array_key_exists($param['id'],$subResultMap))
                {
                    $updateIds[]=$tmpId;
                    if(!($name==$subResultMap[$tmpId]['name'] && $content==$subResultMap[$tmpId]['content']  && $sortNum==$subResultMap[$tmpId]['sort_num']))
                    {
                        $tmpItem['id']= $tmpId;
                        $updateData[]=$tmpItem;
                    }
                }
                else
                {
                    $insertData[]=$tmpItem;
                }
            }
            if(count($updateData))
            {
                $this->db->batchUpdateData($this->tbl_sub_name,array_keys($updateData[0]),$updateData,"name=VALUES(name),content=VALUES(content),sort_num=VALUES(sort_num)");
            }
            if(count($insertData))
            {
                $this->db->batchInsertData($this->tbl_sub_name,array_keys($insertData[0]),$insertData);
            }
            $deleteIds=array_diff($existIds,$updateIds);
            if(count($deleteIds))
            {
                $this->db->delRowByCondition2($this->tbl_sub_name,sprintf('id IN (%s)',implode(',',$deleteIds)));
            }

        }
        return false;
    }

    /**
     * 删除
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        $result = $this->web->delRowByCondition2($this->tbl_name,HaloPdo::condition('id = ?',$id));
        if($result)
        {
            $this->db->delRowByCondition2($this->tbl_sub_name,HaloPdo::condition('cloud_id',$id));
        }
        return $result;
    }

    /**
     * 获取 列表
     * @return array
     */
    public function getList()
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_name,sprintf('id>0  ORDER BY sort_num ASC'));
        if($result)
        {
            $this->addExtraInfo($result);
        }

        $total = $this->web_slave->getCountByCondition($this->tbl_name,HaloPdo::condition('id>0'));
        $data = [
            'list'=>$result ? $result : [],
            'total'=>intval($total),
        ];
        return $data;
    }

    /**
     * 添加类别参数信息
     * @param $result
     */
    public function addExtraInfo(&$result)
    {
        $lockIds = [];
        foreach ($result as $v)
        {
            $lockIds[] = $v['id'];
        }
        $paramsResult = $this->web_slave->getResultsByCondition($this->tbl_sub_name,sprintf('cloud_id IN (%s) ORDER BY sort_num ASC',implode(',',$lockIds)));
        $paramsMap = [];
        if($paramsResult)
        {
            foreach ($paramsResult as $item)
            {
                $paramsMap[$item['cloud_id']][] = $item;
            }
        }


        foreach ($result as &$v)
        {
            $lockId = $v['id'];

            if(array_key_exists($lockId,$paramsMap))
            {

                $v['params'] = $paramsMap[$lockId];
            }
        }
    }


    /**
     * 获取 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getDetail($id)
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

}













