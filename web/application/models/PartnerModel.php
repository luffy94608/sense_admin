<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */
class PartnerModel extends Halo_Model
{

    private $web;
    private $web_slave;

    public function __construct()
    {
        parent::__construct();
        $this->web = DataCenter::getDb('web');
        $this->web_slave = DataCenter::getDb('web_slave');
    }

    /**
     * 列表
     * @return array|bool|string
     */
    public function getList()
    {
        $result = $this->web_slave->getResultsByCondition('partners',HaloPdo::condition('id>0 ORDER BY updated_at DESC'));
        if ($result) {

        }
        return $result ? $result : [];
    }

    /**
     * 详情
     * @param $id
     * @return string
     */
    public function getDetail($id)
    {
        $result = $this->web_slave->getRowByCondition('partners',HaloPdo::condition('id=?',$id));
        return $result;
    }

    /**
     * 修改名称
     * @param $params
     * @return bool|int
     */
    public function create($params)
    {
        $timeStr = date('Y-m-d H:i:s');
        $params=array(
            'logo'=>$params['logo'],
            'url'=>$params['url'],
            'created_at'=>$timeStr,
            'updated_at'=>$timeStr,
        );
        $result = $this->web->insertTable('partners',$params);
        return $result;
    }

    /**
     * 更新
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function update($id,$params )
    {
        if(!empty($params))
        {
            $map=array(
                'logo'=>'logo',
                'url'=>'url'
            );
            $data=array();
            foreach($params as $k=>$v)
            {
                if(array_key_exists($k,$map) && $v!==false)
                {
                    $data[$map[$k]]=$v;
                }
            }
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->web->updateTable('partners',$data,HaloPdo::condition('id = ?',$id));
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
        return  $this->web->delRowByCondition2('partners',HaloPdo::condition('id = ?',$id));
    }


}













