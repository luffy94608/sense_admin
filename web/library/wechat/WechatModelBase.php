<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 14-6-24
 * Time: 上午11:40
 */

class WechatModelBase extends Halo_Model
{

    /**
     * @var HaloDataSource
     */
    public $wechatDb;

    public function __construct()
    {
        parent::__construct();
        $this->wechatDb = DataCenter::getDb('wechat');
    }

    public function unsubscribe($fakeId)
    {
        $this->wechatDb->updateTable('user_wechat_info', array('Fstatus' => 0, 'Ftime' => time()), HaloPdo::condition('Ffake_id=? AND Fcid=?', $fakeId,WeChatEnv::getCorpDbId()));
    }

    /***
     * 添加部门
     * @param $wId
     * @param $departments
     */
    private function insertDepartment($wId,$departments)
    {
        $con = sprintf('Fcid=\'%s\' AND Fdepartment_id IN (%s)',WeChatEnv::getCorpDbId(),implode(',',$departments));
        $resut = $this->wechatDb->getResultsByCondition('department_info',$con);
        if(count($resut) != count($departments))
        {
            $this->getDepartmentlist();
            $resut = $this->wechatDb->getResultsByCondition('department_info',$con);
        }


        if(!empty($resut))
        {

            $insertArray = array();
            foreach($departments as $departId)
            {
                $insertArray[] = array($wId,$departId,WeChatEnv::getCorpDbId());
            }
            $this->wechatDb->batchInsertData('user_department','Fuid,Fdepartment_id,Fcid',$insertArray);
        }
    }

    /**
     * 获取当前公司所有的部门
     */
    public function getDepartmentlist()
    {
        $wechat = new WeChatUtil();
        $data = $wechat->getDepartmentList();
        $array = array();
        if($data != false)
        {
            $departList = $data['department'];
            foreach($departList as $v)
            {
                $array[] = array($v['id'],$v['name'],$v['parentid'],WeChatEnv::getCorpDbId());
            }
        }
        $this->wechatDb->batchInsertData('department_info','Fdepartment_id,Fname,Fparent_id,Fcid',$array);
    }

    /***
     * 创建user
     * @param $fakeId
     * @return array|bool|null|string
     */
    public function createUser($fakeId)
    {
        $uid = $this->getUidByFakeId($fakeId);
        if(!$uid)
        {
            $wechatUtil = new WeChatUtil();
            $userInfo = $wechatUtil->getUser($fakeId);

            $config = HaloEnv::get("config");
            $host = $config['url']['host'];
            $imgUrl = $host . "/images/avatar.png" ;

//            $wechatModel = new WechatModel();
            $this->createUserInfoWithOauth($userInfo,$imgUrl);
        }
        return $uid;
    }

    /***
     * 获取公司信息
     * @param $cropKey
     * @return array|bool|mixed|null|string
     */
    public function getCorpInfoByKey($cropKey)
    {
        $row = $this->wechatDb->getRowByCondition('corp_info',HaloDataSource::condition('Fcorp_key = ?',$cropKey));
        if(!empty($row))
        {
            return $row;
        }
        return false;
    }

    /***
     * 获取应用id
     * @param $cropDbId
     * @param $moudleName
     * @return bool
     */
    public function getAgentIdByMoudle($cropDbId,$moudleName)
    {
        $con = sprintf('Fcid = %d AND Fmoudle_name = \'%s\'',$cropDbId,$moudleName);
        $row = $this->wechatDb->getRowByCondition('agent_info',$con);
        if(!empty($row))
        {
            $cid = $row['Fagent_id'];
        }
        return $cid == null ? false: $cid;
    }

    /***
     * 获取微信的corp id
     * @param $cid
     * @return bool
     */
    public function getCropKeyWithDbId($cid)
    {
        $con = sprintf('Fid = %d',$cid);
        $row = $this->wechatDb->getRowByCondition('corp_info',$con);
        if(!empty($row))
        {
            $key = $row['Fcorp_key'];
        }
        return $key == null ? false: $key;
    }

    /***
     * 创建user
     * @param $userInfo
     * @param $avatarPath
     * @return bool|int
     */
    public function createUserInfoWithOauth($userInfo,$nothing='')
    {
        #warnning
        $fakeId = $userInfo['userid'];
        Logger::DEBUG('user info is '.json_encode($userInfo));
        $row = $this->wechatDb->getRowByCondition('user_wechat_info', HaloPdo::condition('Ffake_id=? AND Fcid=?', $fakeId,WeChatEnv::getCorpDbId()));
        $wechatUserId = 0;
        $departmentArray = $userInfo['department'];

        $map = array(
            'name'=>'Fname',
            'gender'=>'Fsex',
            'mobile'=>'Fmobile',
            'tel'=>'Ftel',
            'position'=>'Fposition',
            'email'=>'Femail','weixinid'=>'Fwechat','qq'=>'Fqq','status'=>'Fstatus','avatar'=>'Fpicture');

        if(empty($row))
        {
            $data = array(
                'Ffake_id' => $fakeId,
                'Fcid' => WeChatEnv::getCorpDbId(),
                'Freg_time' => time(),
                'Fdepartment_ids' => implode(',',$departmentArray),
                'Freg_time' => time(),
            );

            foreach($userInfo as $k=>$v)
            {
                if(isset($map[$k]))
                {
                    $data[$map[$k]] = $v;
                }

            }
            $wechatUserId = $this->wechatDb->insertTable('user_wechat_info', $data);
        }
        else
        {
            $wechatUserId = $row['Fid'];

            $data = array(
                'Fcid' => WeChatEnv::getCorpDbId(),
                'Fdepartment_ids' => implode(',',$departmentArray),
                );
            foreach($userInfo as $k=>$v)
            {
                if(isset($map[$k]))
                {
                    $data[$map[$k]] = $v;
                }

            }
            $this->wechatDb->updateTable('user_wechat_info', $data, HaloPdo::condition('Ffake_id=? AND Fcid=?', $fakeId,WeChatEnv::getCorpDbId()));
        }


        Logger::DEBUG('createUserInfoWithOauth :: departmentArray :'.json_encode($departmentArray));

        $this->insertDepartment($wechatUserId,$departmentArray);

        if(!$fakeId)
        {
            YafDebug::log($userInfo);
        }
        return $wechatUserId;
    }


    /***
     * 通过fakeId 获取uid
     * @param $fakeId
     * @param bool $autoAdd
     * @return array|bool|null|string
     */
    public function getUidByFakeId($fakeId,$autoAdd = false)
    {
        $uid = $this->wechatDb->getVarByCondition("user_wechat_info",HaloPdo::condition('Ffake_id=? AND Fcid=?',$fakeId ,WeChatEnv::getCorpDbId()),"Fid");
        return $uid == null ? false : $uid;
    }

    /***
     * 通过uid获取fakeId
     * @param $uid
     * @return array|bool|null|string
     */
    public function getFakeIdByUid($uid)
    {
        $fakeId = $this->wechatDb->getVarByCondition('user_wechat_info',HaloPdo::condition('Fcid=? AND Fid=? ', WeChatEnv::getCorpDbId(),$uid),'Ffake_id');
        return $fakeId;
    }

    /***
     * 通过fakeID获取名字
     * @param $fakeId
     * @return array|bool|null|string
     */
    public function getNameByFakeId($fakeId)
    {
        $name = $this->wechatDb->getVarByCondition('user_wechat_info',  HaloPdo::condition('Ffake_id=? AND Fcid=? AND Fstatus = 1', $fakeId,WeChatEnv::getCorpDbId()), 'Fname');
        return $name;
    }

    /***
     * 获取用户的基本信息
     * @param $uid
     * @return array|bool|mixed|null|string
     */
    public function getUserBaseInfo($uid)
    {
        $row = $this->wechatDb->getRowByCondition('user_wechat_info', HaloPdo::condition('Fcid=? AND Fid=?', WeChatEnv::getCorpDbId(),$uid));
        return $row;
    }

    /***
     * 获取用户的部门信息
     * @param $uid
     * @return array|bool|mixed|null|string
     */
    public function getUserDepartmentNames($uid)
    {
        $row = $this->wechatDb->getRowByCondition('user_wechat_info', HaloPdo::condition('Fcid=? AND Fid=?', WeChatEnv::getCropDbId(),$uid),'Fdeparment_ids');
        return $row;
    }

    /***
     * 获取用户的部门ids
     * @param $uid
     * @return array|bool
     */
    public function getUserDepartments($uid,$cid)
    {
        $rows = $this->wechatDb->getResultsByCondition('user_department',HaloPdo::condition('Fuid = ? AND Fcid = ?',$uid, $cid));
        $res = array();
        if(!empty($rows))
        {
            foreach($rows as $v)
            {
                $res[$v['Fdepartment_id']] = array('id'=>$v['Fdepartment_id']);
            }
        }
        $departInfos = $this->wechatDb->getResultsByCondition('department_info',sprintf('Fcid = %d AND Fdepartment_id IN (%s)',$cid,implode(',',array_keys($res))));
        if(!empty($departInfos))
        {
            foreach($departInfos as $r)
            {
                if(isset($res[$r['Fdepartment_id']]))
                {
                    $res[$r['Fdepartment_id']]['name'] = $r['Fname'];
                }
            }
        }
        return $res;
    }

} 
