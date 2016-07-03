<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/9/2
 * Time: 10:36
 */

class CompanyModel extends Halo_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取认证企业列表
     * @param bool $type 是否获取相关企业权限 false 不获取 true获取
     * @return array
     * @throws Exception
     */
    public function getCompanyList($type=false)
    {
        $list=$this->db->getResultsByCondition('company');
        if($list)
        {
            $list=$this->ridResultSetPrefix($list);
            $cids=array();
            foreach($list as &$v)
            {
                $cids[]=$v['id'];
            }
            if(count($cids) && $type)
            {
                $result2=$this->db->getResultsByCondition('company_privilege_relation',sprintf('Fcid IN (\'%s\')',implode('\',\'',$cids)));
                $pids=array();
                $rpMap=array();
                if($result2)
                {
                    foreach($result2 as $v2)
                    {
                        $pids[]=$v2['Fpid'];
                        $rpMap[$v2['Fcid']][]=$v2['Fpid'];
                    }
                }
                $pids=array_unique($pids);
                if(count($pids))
                {
                    $result3=$this->db->getResultsByCondition('account_privilege',sprintf('Fid IN (%s)',implode(',',$pids)));
                }
                $pMap=array();
                if($result3)
                {
                    foreach($result3 as $v3)
                    {
                        $pMap[$v3['Fid']]=$this->ridFieldPrefix($v3);
                    }
                }
                foreach($rpMap as &$rpItem)
                {
                    if(!empty($rpItem) && is_array($rpItem))
                    {
                        foreach($rpItem as &$v4)
                        {
                            $v4=$pMap[$v4];
                        }
                    }
                }
                $tmpSuperCompanyList=$this->db->getResultsByCondition('super_admin',HaloPdo::condition('Ftype=0'));
                $superCompanyMap=array();
                if($tmpSuperCompanyList)
                {
                    foreach($tmpSuperCompanyList as $tsclv)
                    {
                        $superCompanyMap[$tsclv['Fid']]=$tsclv;
                    }
                }
                foreach($list as &$v5)
                {
                    $v5['privileges']=$rpMap[$v5['id']];
                    if(array_key_exists($v5['id'],$superCompanyMap))
                    {
                        $v5['is_super_admin']=1;

                    }
                    else
                    {
                        $v5['is_super_admin']=0;
                    }
                }
            }
        }
        return $list;
    }

    public function getCompanyAdmin()
    {
        $result=$this->db->getResultsByCondition('account_user',HaloPdo::condition('Fis_admin=1'));
        if($result)
        {
            $result=$this->ridResultSetPrefix($result);
        }
        return $result;
    }

    /**
     * 添加或者修改企业
     * @param $name
     * @param $domain
     * @param $privileges
     * @param string $id
     * @return bool
     * @throws Exception
     */
    public function updateCompany($name,$domain,$privileges,$id='')
    {
        $data=array(
            'Fname'=>$name,
            'Fdomain'=>$domain,
        );
        if(empty($id))
        {
            $data['Fcreated_at']=time();
            $data['Fupdated_at']=time();
            $result=$this->db->insertTable('company',$data);
            $id=$result;
        }
        else
        {
            $data['Fupdated_at']=time();
            $result=$this->db->updateTable('company',$data,HaloPdo::condition('Fid=?',$id));
        }
        if(!empty($privileges))
        {
            $rows=$this->db->getResultsByCondition('company_privilege_relation',HaloPdo::condition('Fcid=?',$id));
            $sPrivileges=array();
            if($rows)
            {
                foreach($rows as $v)
                {
                    $sPrivileges[]=$v['Fpid'];
                }
            }
            $addArr=array_diff($privileges,$sPrivileges);
            $delArr=array_diff($sPrivileges,$privileges);
            if(count($delArr))
            {
                $this->db->delRowByCondition2('company_privilege_relation',sprintf('Fcid=\'%s\' AND Fpid IN (%s)',$id,implode(',',$delArr)));
            }
            if(!empty($addArr))
            {
                $insertData=array();
                foreach($addArr as $aItem)
                {
                    $tmp['Fcid']=$id;
                    $tmp['Fpid']=$aItem;
                    array_push($insertData,$tmp);
                }
                $result2=$this->db->batchInsertData('company_privilege_relation',array_keys($insertData[0]),$insertData);
            }

        }
        return $result;
    }

    /**
     * 删除企业
     * @param $id
     * @return int
     */
    public function deleteCompany($id)
    {
        $result=$this->db->delRowByCondition2('company',HaloPdo::condition('Fid=?',$id));
        if($result)
        {
            $this->db->delRowByCondition2('super_admin',HaloPdo::condition('Fid=? AND Ftype=0',$id));
            $this->db->delRowByCondition2('company_privilege_relation',HaloPdo::condition('Fcid=?',$id));
        }
        return $result;
    }

    /**
     * 删除企业管理员
     * @param $id
     * @return int
     */
    public function deleteAdmin($id)
    {
        $result=$this->db->delRowByCondition2('account_user',HaloPdo::condition('Fid=? AND Fis_admin=1',$id));
        return $result;
    }

    /**
     * 获取指定企业的添加的角色
     */
    public function getCompanyPrivilege($map)
    {
        $res=array();
        $result=$this->db->getResultsByCondition('company_privilege_relation');
        if(empty($result))
        {
            return $res;
        }
        $resultMap=array();
        foreach($result as $v)
        {
            $resultMap[$v['Fcid']][]=$v['Fpid'];
        }

        $parentNode=array();
        $childNode=array();
        foreach($map as $mv)
        {
            if(is_array($mv['subArr']))
            {
                foreach($mv['subArr'] as $sbArr)
                {
                    $childNode[$sbArr['id']]=$sbArr;
                }
            }
            unset($mv['subArr']);
            $parentNode[$mv['id']]=$mv;
        }

        foreach($resultMap as $rk=>$rm)
        {
            $newArr=array();
            foreach($rm as $rmv)
            {
                $parent_id=$childNode[$rmv]['parent_id'];
                if(!isset($newArr[$childNode[$rmv]['parent_id']]))
                {
                    $newArr[$parent_id]=$parentNode[$parent_id];
                }
            }
            foreach($rm as $rmv)
            {
                $parent_id=$childNode[$rmv]['parent_id'];
                if(isset($newArr[$childNode[$rmv]['parent_id']]))
                {
                    $newArr[$parent_id]['subArr'][$rmv]=$childNode[$rmv];
                }

            }
            $resultMap[$rk]=$newArr;
        }
        $res=$resultMap;
        return $res;
    }

    /**
     * 添加超级企业
     * @param $cid
     * @param int $type 0企业 1用户
     * @return bool|int
     */
    public function setSuperCompany($cid,$type=0)
    {
        $data=array(
            'Fid'=>$cid,
            'Ftype'=>$type,
        );
        $result=$this->db->insertTable('super_admin',$data);
        return $result;
    }

    /**
     * 删除超级企业
     * @param $cid
     * @return bool|int
     */
    public function deleteSuperCompany($cid)
    {
        $row=$this->db->getRowByCondition('super_admin',HaloPdo::condition('Fid=? AND Ftype=0',$cid));
        $result=false;
        if($row)
        {
            $result=$this->db->delRowByCondition2('super_admin',HaloPdo::condition('Fid=? AND Ftype=0',$cid));
        }
        return $result;

    }

    /**
     * 获取所有超级管理员
     * @param int $type 0 企业 1 用户
     * @return array|bool|string
     */
    public function getSuperAdmin($type=0)
    {
        $result=$this->db->getResultsByCondition('super_admin',HaloPdo::condition('Ftype=?',$type));
        if($result)
        {
            $result=$this->ridResultSetPrefix($result);
        }
        return $result;
    }


} 