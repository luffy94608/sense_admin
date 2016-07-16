<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */
class BannerModel extends Halo_Model
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
        $result = $this->web_slave->getResultsByCondition('banners',HaloPdo::condition('id>0 ORDER BY sort_num ASC'));
        if ($result) {

        }
        return $result ? $result : [];
    }

    /**
     * 详情
     * @param $id
     * @return string
     */
    public function getBannerDetail($id)
    {
        $result = $this->web_slave->getRowByCondition('banners',HaloPdo::condition('id=?',$id));
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
            'title'=>$params['title'],
            'sub_title'=>$params['sub_title'],
            'url'=>$params['url'],
            'img'=>$params['img'],
            'btn_name'=>$params['btn_name'],
            'btn_url'=>$params['btn_url'],
            'created_at'=>$timeStr,
            'updated_at'=>$timeStr,
        );
        $result = $this->web->insertTable('banners',$params);
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
                'title'=>'title',
                'sub_title'=>'sub_title',
                'url'=>'url',
                'img'=>'img',
                'btn_name'=>'btn_name',
                'btn_url'=>'btn_url',
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
            return $this->web->updateTable('banners',$data,HaloPdo::condition('id = ?',$id));
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
        return  $this->web->delRowByCondition2('banners',HaloPdo::condition('id = ?',$id));
    }

    /**
     * @param $data
     * @return int
     */
    public function saveSort($data)
    {
        return $this->web->batchUpdateData('banners',array_keys($data[0]),$data,'sort_num= values(sort_num)');
    }

}













