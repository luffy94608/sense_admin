<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */

class NewsModel extends Halo_Model
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
     * 修改名称
     * @param $params
     * @return bool|int
     */
    public function create($params)
    {
        $timeStr = date('Y-m-d H:i:s');
        $params=array(
            'title'=>$params['title'],
            'time'=>$params['time'],
            'content'=>$params['content'],
            'created_at'=>$timeStr,
            'updated_at'=>$timeStr,
        );
        $result = $this->web->insertTable('company_news',$params);
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
                'time'=>'time',
                'content'=>'content',
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
            return $this->web->updateTable('company_news',$data,HaloPdo::condition('id = ?',$id));
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
        return  $this->web->delRowByCondition2('company_news',HaloPdo::condition('id = ?',$id));
    }


    /**
     * 获取 列表
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function getNewsList($offset=0,$length =20)
    {
        $result = $this->web_slave->getResultsByCondition('company_news',sprintf('id>0  ORDER BY updated_at DESC LIMIT %d OFFSET %d ',$length,$offset));
        $total = $this->web_slave->getCountByCondition('company_news',HaloPdo::condition('id>0'));
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
    public function getNewsDetail($id)
    {
        $result = $this->web_slave->getRowByCondition('company_news',HaloPdo::condition('id= ?',$id));
        return $result;
    }

}













