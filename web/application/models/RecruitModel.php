<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */
class RecruitModel extends Halo_Model
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
            'location'=>$params['location'],
            'num'=>$params['num'],
            'experience'=>$params['experience'],
            'degree'=>$params['degree'],
            'nature'=>$params['nature'],
            'salary'=>$params['salary'],
            'duty'=>$params['duty'],
            'requirement'=>$params['requirement'],
            'created_at'=>$timeStr,
            'updated_at'=>$timeStr,
        );
        $result = $this->web->insertTable('recruits',$params);
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
                'location'=>'location',
                'num'=>'num',
                'experience'=>'experience',
                'degree'=>'degree',
                'nature'=>'nature',
                'salary'=>'salary',
                'duty'=>'duty',
                'requirement'=>'requirement',
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
            return $this->web->updateTable('recruits',$data,HaloPdo::condition('id = ?',$id));
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
        return  $this->web->delRowByCondition2('recruits',HaloPdo::condition('id = ?',$id));
    }


    /**
     * 获取 列表
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function getRecruitList($offset=0,$length =20)
    {
        $result = $this->web_slave->getResultsByCondition('recruits',sprintf('id>0  ORDER BY sort_num ASC'));
        $data = [
          'list'=>$result ? $result : [],
          'total'=>0,
        ];
        return $data;
    }

    /**
     * 获取 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getRecruitDetail($id)
    {
        $result = $this->web_slave->getRowByCondition('recruits',HaloPdo::condition('id= ?',$id));
        return $result;
    }

    public function saveSort($data)
    {
        return $this->web->batchUpdateData('recruits',array_keys($data[0]),$data,'sort_num= values(sort_num)');
    }


}













