<?php

class AccountModel extends Halo_Model
{

    /**
     * @param int $cursor_id
     * @param int $length
     * @param bool $type true 获取全部 false分页显示
     * @return array
     */
    public function getUserList($cursor_id=0,$length=20,$type=false)
    {

        $cursorSql='';
        if($cursor_id>0)
        {
            $cursorSql=sprintf(' AND Fid<%s ',$cursor_id);
        }

        $result=$this->db->getResultsByCondition('account_user',sprintf('Fdel=0 %s %s ORDER BY Fid DESC LIMIT %d',$cursorSql,$length+1));

        $hasMore=false;
        if($result && is_array($result) && count($result))
        {
            if(count($result)>=($length+1))
            {
                $hasMore=true;
                array_pop($result);
            }

            foreach($result as &$item)
            {
                $item['Fcursor_id']=$item['Fid'];
            }
            $result=$this->ridResultSetPrefix($result);
        }

        $data=array(
            'hasMore'=>$hasMore,
            'list'=>$result
        );
        if($cursor_id==0)
        {
            $total=$this->db->getCountByCondition('account_user',sprintf('Fdel=0'));
            $data['total']=intval($total);
        }

        return $data;
    }

    /**
     * 获取所有用户
     * @return array|bool|string
     */
    public function getAllUser($cid)
    {
        if(HolloEnv::getAccessModelHandle($cid))
        {
            $result=$this->db->getResultsByCondition('account_user u left join account_role r on u.Frid=r.Fid',HaloPdo::condition('u.Fdel=0 ORDER BY Fid DESC'),'u.*,r.Fname Frole_name');
        }
        else
        {
            $result=$this->db->getResultsByCondition('account_user u left join account_role r on u.Frid=r.Fid',HaloPdo::condition('u.Fdel=0 AND u.Fcid=? ORDER BY Fid DESC',$cid),'u.*,r.Fname Frole_name');
        }
        if($result)
        {
            foreach($result as &$v)
            {
                unset($v['Fpassword']);
            }
        }
        $result=$this->ridResultSetPrefix($result);
        return $result;
    }

    public function checkPassword($account,$psw)
    {
        $config=Yaf_Registry::get('config');
        $rootInfo=$config->root;
        if(!empty($rootInfo))
        {
            $id=$rootInfo->id;
            $cid=$rootInfo->cid;
            $rName=$rootInfo->name;
            $rPass=$rootInfo->pass;
            if($account==$rName && $psw==$rPass)
            {
                return array(
                    'id'=>$id,
                    'cid'=>$cid,
                    'name'=>$rName
                );
            }
        }

        $result=$this->db->getRowByCondition('account_user',HaloPdo::condition('BINARY Faccount=? AND Fpassword=?',$account,md5($psw)));
        if($result)
        {
            $result=$this->ridFieldPrefix($result);
        }
        return $result;
    }

    public function resetPassword($uid,$oldPass,$newPass)
    {
        $result=$this->db->getRowByCondition('account_user',HaloPdo::condition('Fid=? AND Fpassword=?',$uid,md5($oldPass)));
        if($result)
        {
            if($oldPass!=$newPass)
            {
                $result=$this->db->updateTable('account_user',array('Fpassword'=>md5($newPass)),HaloPdo::condition('Fid=?',$uid));
            }
            else
            {
                $result=true;
            }
        }
        else
        {
            $result=-1;
        }
        return $result;
    }


    /**
     * 添加修改用户
     * @param $account
     * @param $psw
     * @param $name
     * @param $mobile
     * @param $role_id
     * @param string $id
     * @param string $cid 所属企业
     * @param int $isAdmin 0 普通用户 1企业管理员
     * @return bool|int
     */
    public function updateUser($account,$psw,$name,$mobile,$role_id,$id='',$cid='',$isAdmin=0)
    {
        $data=array(
            'Faccount'=>$account,
            'Fname'=>$name,
            'Fphone'=>$mobile,
            'Frid'=>$role_id,
            'Fis_admin'=>$isAdmin,
        );
        if(!empty($cid))
        {
            $data['Fcid']=$cid;
        }
        if(!empty($psw))
        {
            $data['Fpassword']=md5($psw);
        }
        $time=time();

        if(empty($id))
        {
            $row=$this->db->getRowByCondition('account_user',HaloPdo::condition('Faccount=? AND Fdel=0',$account));
            if($row)
            {
                return -1;
            }
            $data['Ftime']=$time;
            $result=$this->db->insertTable('account_user',$data);
        }
        else
        {
            $data['Fupdate_time']=$time;
            $result=$this->db->updateTable('account_user',$data,HaloPdo::condition('Fid=?  AND Fdel=0',$id));
        }
        return $result;
    }

    public function deleteUser($id)
    {
        return $this->db->delRowByCondition2('account_user',HaloPdo::condition('Fid=?',$id));
        return $this->db->updateTable('account_user',array('Fdel'=>1),HaloPdo::condition('Fid=?',$id));
    }

    /**
     * 获取所有
     * @param $cid
     * @return array
     */
    public function getAllPrivilege($cid)
    {
        $con='';
        if(HolloEnv::getAccessModelHandle($cid))
        {
            $con=HaloPdo::condition('Fid ORDER BY Fid DESC');
        }
        else
        {
            $cidRes=$this->db->getResultsByCondition('company_privilege_relation',HaloPdo::condition('Fcid=?',$cid));
            $pids=array();
            if($cidRes)
            {
                foreach($cidRes as $crv)
                {
                    $pids[]=$crv['Fpid'];
                }
            }
            if(count($pids))
            {
                $con=sprintf('Fparent_id=0 OR Fid IN (%s)',implode(',',$pids));
            }
        }

        $result=$this->db->getResultsByCondition('account_privilege',$con);
        $res=array();
        if($result)
        {
            $result=$this->ridResultSetPrefix($result);
            $rootNode=array();
            $childNode=array();
            foreach($result as $v)
            {
                if($v['parent_id']==0)
                {
                    $rootNode[$v['id']]=$v;
                }
                else
                {
                    $childNode[$v['parent_id']][$v['id']]=$v;
                }
            }
            if(count($childNode))
            {
                foreach($childNode as $k=>$cv)
                {

                    if(array_key_exists($k,$rootNode))
                    {
                        $rootNode[$k]['subArr']=$cv;
                    }
                }
            }

            //去除没有子权限的数据
            if(count($rootNode))
            {
                foreach($rootNode as $k2=>$rv)
                {
                    if(empty($rv['subArr']))
                    {
                        unset($rootNode[$k2]);
                    }
                }
            }

            $res=$rootNode;
        }
        return $res;
    }

    public function updatePrivilege($subArr,$action,$name,$id='')
    {
        $res=array();
        $data=array(
            'Faction'=>$action,
            'Fname'=>$name,
        );
        $res=$this->ridFieldPrefix($data);
        if(empty($id))
        {
            $result=$this->db->insertTable('account_privilege',$data);
            $id=$result;
        }
        else
        {
            $row=$this->db->getRowByCondition('account_privilege',HaloPdo::condition('Fid=? AND Faction=? AND Fname=?',$id,$action,$name));
            if(!$row)
            {
                $result=$this->db->updateTable('account_privilege',$data,HaloPdo::condition('Fid=?',$id));
            }
            else
            {
                $result=true;
            }
        }
        $res['id']=$id;
        //操作成功
        if($id && !empty($subArr) && is_array($subArr))
        {
            $subResultMap=array();
            $insertData=array();
            $updateData=array();
            $existIds=array();
            $updateIds=array();
            $deleteIds=array();
            $subTmpResult=$this->db->getResultsByCondition('account_privilege',HaloPdo::condition('Fparent_id=?',$id));
            if($subTmpResult)
            {
                foreach($subTmpResult as $v)
                {
                    $existIds[]=$v['Fid'];
                    $subResultMap[$v['Fid']]=$v;
                }
            }
            foreach($subArr as $sav)
            {
                $tmpItem=array();
                $tmpItem['Faction']=$sav['action'];
                $tmpItem['Fname']=$sav['name'];
                $tmpItem['Fparent_id']=$id;
                if(!empty($sav['id']) && array_key_exists($sav['id'],$subResultMap))
                {
                    $updateIds[]=$sav['id'];
                    if(!($tmpItem['Faction']==$subResultMap[$sav['id']]['Faction'] && $tmpItem['Fname']==$subResultMap[$sav['id']]['Fname']))
                    {
                        $tmpItem['Fid']=$sav['id'];
                        $updateData[]=$tmpItem;
                    }
                }
                else
                {
                    $insertData[]=$tmpItem;
                }
            }
            $deleteIds=array_diff($existIds,$updateIds);
            if(count($updateData))
            {
                $updateResult=$this->db->batchUpdateData('account_privilege',array_keys($updateData[0]),$updateData,"Faction=VALUES(Faction),Fname=VALUES(Fname)");
            }
            if(count($insertData))
            {
                $insertResult=$this->db->batchInsertData('account_privilege',array_keys($insertData[0]),$insertData);
            }
            if(count($deleteIds))
            {
                $delResult=$this->db->delRowByCondition2('account_privilege',sprintf('Fid IN (%s)',implode(',',$deleteIds)));
            }

            $subResResult=$this->db->getResultsByCondition('account_privilege',HaloPdo::condition('Fparent_id=?',$id));
            if($subResResult)
            {
                $res['subArr']=$this->ridResultSetPrefix($subResResult);
            }
        }
        return $result?$res:false;
    }

    public function deletePrivilege($id)
    {
        $result=$this->db->delRowByCondition2('account_privilege',HaloPdo::condition('Fid=?',$id));
        if($result)
        {
            $this->db->delRowByCondition2('account_privilege',HaloPdo::condition('Fparent_id=?',$id));
            $this->db->delRowByCondition2('role_privilege_relation',HaloPdo::condition('Fpid=?',$id));
        }
        return $result;
    }

    public function deleteRole($id)
    {
        $result=$this->db->delRowByCondition2('account_role',HaloPdo::condition('Fid=?',$id));
        if($result)
        {
            $this->db->delRowByCondition2('role_privilege_relation',HaloPdo::condition('Frid=?',$id));
        }
        return $result;
    }

    /**
     * 获取角色
     * @param $cid
     * @param bool $type  true 获取权限列表 false 不获取权限
     * @return array|bool|string
     */
    public function getAllRole($cid,$type=true)
    {
        if(HolloEnv::getAccessModelHandle($cid))
        {
            $result=$this->db->getResultsByCondition('account_role');
        }
        else
        {
            $result=$this->db->getResultsByCondition('account_role',HaloPdo::condition('Fcid=?',$cid));
        }
        $rids=array();
        if($result && $type)
        {
            foreach($result as $v)
            {
                $rids[]=$v['Fid'];
            }
            if(count($rids))
            {
                $result2=$this->db->getResultsByCondition('role_privilege_relation',sprintf('Frid IN (%s)',implode(',',$rids)));
                $pids=array();
                $rpMap=array();
                if($result2)
                {
                    foreach($result2 as $v2)
                    {
                        $pids[]=$v2['Fpid'];
                        $rpMap[$v2['Frid']][]=$v2['Fpid'];
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
                foreach($result as &$v5)
                {
                    $v5['Fprivileges']=$rpMap[$v5['Fid']];
                }
            }
        }

        $result=$this->ridResultSetPrefix($result);
        return $result;
    }

    public function updateRole($name,$privileges,$cid,$id='')
    {
        $data=array(
            'Fname'=>$name,
        );
        if(empty($id))
        {
            $data['Fcid']=$cid;
            $result=$this->db->insertTable('account_role',$data);
            $id=$result;
        }
        else
        {
            $result=$this->db->updateTable('account_role',$data,HaloPdo::condition('Fid=? AND Fcid=?',$id,$cid));
        }
        if(!empty($privileges))
        {
            $rows=$this->db->getResultsByCondition('role_privilege_relation',HaloPdo::condition('Frid=?',$id));
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
                $this->db->delRowByCondition2('role_privilege_relation',sprintf('Frid=%s AND Fpid IN (%s)',$id,implode(',',$delArr)));
            }
            if(!empty($addArr))
            {
                $insertData=array();
                foreach($addArr as $aItem)
                {
                    $tmp['Frid']=$id;
                    $tmp['Fpid']=$aItem;
                    array_push($insertData,$tmp);
                }
                $result2=$this->db->batchInsertData('role_privilege_relation',array_keys($insertData[0]),$insertData);
            }

        }
        return $result;
    }

    /**
     * 获取用户的权限
     */
    public function  getUsePrivileges($uid)
    {
        $privilege=array();
        if(empty($uid))
        {
            return $privilege;
        }
        $info=$this->db->getRowByCondition('account_user',HaloPdo::condition('Fid=?',$uid));
        $rid=$info['Frid'];
        $cid=$info['Fcid'];
        $is_admin=$info['Fis_admin'];
        if(!empty($rid) || !empty($cid))
        {
            if($is_admin==1)
            {
                $result=$this->db->getResultsByCondition('company_privilege_relation',HaloPdo::condition('Fcid=?',$cid));
            }
            else
            {
                $result=$this->db->getResultsByCondition('role_privilege_relation',HaloPdo::condition('Frid=?',$rid));
            }
            if($result)
            {
                $pids=array();
                foreach($result as $v)
                {
                    $pids[]=$v['Fpid'];
                }
                $childData=$this->db->getResultsByCondition('account_privilege',sprintf('Fid IN (%s)',implode(',',$pids)));
                if($childData)
                {
                    $childData=$this->ridResultSetPrefix($childData);
                    $childList=array();
                    foreach($childData as $item)
                    {
                        $item['action']=strtolower(str_replace('-','',$item['action']));
                        $childList[$item['parent_id']][$item['action']]=$item;
                    }
                    $parentData=$this->db->getResultsByCondition('account_privilege',sprintf('Fid IN (%s)',implode(',',array_keys($childList))));
                    if($parentData)
                    {
                        $parentData=$this->ridResultSetPrefix($parentData);
                        foreach($parentData as &$pItem)
                        {
                            if(array_key_exists($pItem['id'],$childList))
                            {
                                $pItem['action']=strtolower(str_replace('-','',$pItem['action']));
                                $pItem['children']=$childList[$pItem['id']];
                                $privilege[$pItem['action']]=$pItem;
                            }
                        }
                    }
                }
            }
        }
        return $privilege;
    }


    /**
     * 判断是否有车辆监控模块
     * @param $uid
     * @return bool
     */
    public function hasMonitorModule($uid)
    {
        $status=false;;
        if(empty($uid))
        {
            return $status;
        }
        if(HolloEnv::getAccessModelHandle($uid))
        {
            $status=true;;
            return $status;
        }
        $info=$this->db->getRowByCondition('account_user',HaloPdo::condition('Fid=?',$uid));
        $rid=$info['Frid'];
        $cid=$info['Fcid'];
        $is_admin=$info['Fis_admin'];
        if(!empty($rid) || !empty($cid))
        {
            //获取监控模块的子模块id
            $moduleIds=array();
            $mResult=$this->db->getRowByCondition('account_privilege',HaloPdo::condition('Faction=\'monitor\' AND Fparent_id=0'));
            if($mResult)
            {
                $tmResult=$this->db->getResultsByCondition('account_privilege',HaloPdo::condition('Fparent_id=?',$mResult['Fid']));
                if($tmResult)
                {
                    foreach($tmResult as $t)
                    {
                        $moduleIds[]=$t['Fid'];
                    }
                }
            }
            if(count($moduleIds))
            {
                if($is_admin==1)
                {
                    $result=$this->db->getResultsByCondition('company_privilege_relation',sprintf('Fcid=\'%s\' AND Fpid IN (%s)',$cid,implode(',',$moduleIds)));
                }
                else
                {
                    $result=$this->db->getResultsByCondition('role_privilege_relation',sprintf('Frid=\'%s\'  AND Fpid IN (%s)',$rid,implode(',',$moduleIds)));
                }
                if($result)
                {
                    $status=true;
                }
            }

        }
        return $status;
    }

    /**
     * 获取用户
     * @param $uids
     * @return array
     */
    public function getUserMap($uids)
    {
        $res = [];
        if($uids)
        {
            $config=Yaf_Registry::get('config');
            $rootInfo=$config->root;
            if(!empty($rootInfo))
            {
                $id=$rootInfo->id;
                $cid=$rootInfo->cid;
                $rName=$rootInfo->name;
                $rootData= array(
                    'id'=>$id,
                    'cid'=>$cid,
                    'account'=>$rName,
                    'name'=>$rName
                );
                $res[$id] = $rootData;
            }
            $result=$this->db->getResultsByCondition('account_user',sprintf('Fid IN (%s)',implode(',',$uids)));
            if($result)
            {
                foreach ($result as $v)
                {
                    $res[$v['Fid']] = $this->ridFieldPrefix($v);
                }
            }

        }
        return $res;
    }
}
