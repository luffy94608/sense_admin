<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */
class OperationModel extends Halo_Model
{


    public function __construct()
    {
        parent::__construct();
    }

    public function create($cid,$uid,$type,$desc,$params = '')
    {
        $params=array(
            'Fcid'=>$cid,
            'Fuid'=>$uid,
            'Ftype'=>$type,
            'Fdesc'=>$desc,
            'Fip'=>$this->getRemoteIp(),
            'Ftime'=>time(),
            'Fparams'=>$params,
        );

        $result = $this->db->insertTable('operations',$params);
        return $result;
    }

    /**
     * 获取客户端ip
     * @return mixed
     */
    public function getRemoteIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 获取日志列表
     * @param $cid
     * @param $timeStr
     * @return array|bool|string
     */
    public function getList($cid,$timeStr)
    {
        $startTime = strtotime($timeStr);
        $endTime = $startTime+60*60*24;

        $con = '';
        if(!HolloEnv::getAccessModelHandle($cid)){
            $con = sprintf('Fcid=%s AND ',$cid);
        }

        $result = $this->db->getResultsByCondition('operations',sprintf('%s Ftime>=%s AND Ftime<%s ORDER BY Ftime DESC',$con,$startTime,$endTime));
        if($result)
        {
            $uids = [];
            foreach ($result as $v){
                $uids[]=$v['Fuid'];
            }
            $userModel = new AccountModel();
            $userMap = $userModel->getUserMap($uids);
            $result = $this->ridResultSetPrefix($result);
            foreach ($result as &$v2)
            {
                if(array_key_exists($v2['uid'],$userMap))
                {
                    $v2['user'] = $userMap[$v2['uid']];
                }
            }

        }
        return $result;
    }
}













