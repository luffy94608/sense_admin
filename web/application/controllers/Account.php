<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/7/14
 * Time: 20:21
 */

class AccountController extends BaseController
{
    /**
     * @var AccountModel
     */
    protected $model = null;
    public function init()
    {
        parent::init();
        $this->model=new AccountModel();
    }

    public function userAction()
    {
        $this->view->page='account-user-page';
        $params['cid']=$this->getLegalParam('cid','str');
        $cid=$this->cid;
        if(!empty($params['cid']))
        {
            $cid=$params['cid'];
        }

        $companyModel=new CompanyModel();
        $companyList=$companyModel->getCompanyList(false);
        $list=$this->model->getAllUser($cid);
        $roleList=$this->model->getAllRole($cid,false);
        $this->view->accessStatus=HolloEnv::getAccessModelHandle($this->cid);

        //显示公司名字
        if(!empty($companyList) && !empty($list))
        {
            $map=array();
            foreach($companyList as $v)
            {
                $map[$v['id']]=$v;
            }
           if(!empty($roleList))
           {
               foreach($roleList as $l)
               {
                    if(array_key_exists($l['cid'],$map))
                    {
                        $map[$l['cid']]['role_list'][]=$l;
                    }
               }
           }
            foreach($list as &$v2)
            {
                if(array_key_exists($v2['cid'],$map))
                {
                    $v2['company_name']=$map[$v2['cid']]['name'];
                    $v2['role_list']=$map[$v2['cid']]['role_list'];
                }
            }
        }

        $this->view->list=$list;
        $this->view->companyList=$companyList;
        $this->view->roleList=$roleList;
    }

    public function roleAction()
    {
        $this->view->page='account-role-page';

        $params['cid']=$this->getLegalParam('cid','str');
        $cid=$this->cid;
        if(!empty($params['cid']))
        {
            $cid=$params['cid'];
        }
        $companyModel=new CompanyModel();
        $companyList=$companyModel->getCompanyList(false);

        $list=$this->model->getAllRole($cid);
        $privilegeList=$this->model->getAllPrivilege($cid);

        if(!empty($companyList) && !empty($list))
        {
            $map=array();
            $privilegesMap=$companyModel->getCompanyPrivilege($privilegeList);

            foreach($companyList as $v)
            {
                if(array_key_exists($v['id'],$privilegesMap))
                {
                    if(!empty($privilegesMap[$v['id']]) && is_array($privilegesMap[$v['id']]))
                    {
                        foreach($privilegesMap[$v['id']] as &$pmv)
                        {
                            if(is_array($pmv['subArr']))
                            {
                                $pmv['subArr']=array_values($pmv['subArr']);
                            }
                        }
                    }
                    $v['company_privilege']=array_values($privilegesMap[$v['id']]);
                }
                $map[$v['id']]=$v;

            }
            foreach($list as &$v2)
            {
                if(array_key_exists($v2['cid'],$map))
                {
                    $v2['company_name']=$map[$v2['cid']]['name'];
                    $v2['company_privilege']=$map[$v2['cid']]['company_privilege'];
                }
            }
        }

        $this->view->list=$list;
        $this->view->companyList=$companyList;
        $this->view->privilegeList=$privilegeList;
        $this->view->accessStatus=HolloEnv::getAccessModelHandle($this->cid);
    }

    public function moduleAction()
    {
        $this->view->page='account-module-page';
        $list=$this->model->getAllPrivilege($this->cid);
        if(!empty($list) && is_array($list))
        {
            foreach($list as &$v)
            {
                if(is_array($v['subArr']))
                {
                    $v['subArr']=array_values($v['subArr']);
                }
            }
        }
        $this->view->list=$list;
    }

    public function updateUserAjaxAction()
    {
        $params['account']=trim($this->getLegalParam('account','str'));
        $params['cid']=$this->cid;
        $password=trim($this->getLegalParam('password','str'));

        $id=$this->getLegalParam('id','str');
        if(empty($id))
        {
            $params['password']=$password;
        }
        if(in_array(false,$params))
        {
            $this->inputParamErrorResult();
        }

        if($params['account']=='root')
        {
            $this->inputErrorWithDesc('不能添加root账户');
        }
        if(!empty($id))
        {
            $params['password']=$password;
        }

        $params['name']=$this->getLegalParam('name','str');
        $params['phone']=$this->getLegalParam('phone','str');
        $params['rid']=$this->getLegalParam('rid','str',array(),0);

        $params['id']=$id;
        $result=$this->model->updateUser($params['account'],$params['password'],$params['name'],$params['phone'], $params['rid'],$params['id'],$params['cid']);
        if($result>0)
        {
            $this->inputResult($result);
        }
        $desc='操作失败';
        if($result=-1)
        {
            $desc='账户已存在';
        }
        $this->inputErrorWithDesc($desc);
    }


    public function updateRoleAjaxAction()
    {
        $params['name']=$this->getLegalParam('name','str');
        $params['cid']=$this->cid;
        if(in_array(false,$params))
        {
            $this->inputParamErrorResult();
        }
        $params['privilege']=$this->getLegalParam('privilege','raw');

        $params['id']=$this->getLegalParam('id','str');
        $result=$this->model->updateRole($params['name'],$params['privilege'],$params['cid'],$params['id']);
        if($result || $result ===0)
        {
            $this->inputResult($result);
        }
        $this->inputErrorWithDesc('操作失败');
    }

    public function updatePrivilegeAjaxAction()
    {
        $params['action']=$this->getLegalParam('action','str');
        $params['name']=$this->getLegalParam('name','str');
        $params['subArr']=$this->getLegalParam('subArr','raw');
        if(in_array(false,$params))
        {
            $this->inputParamErrorResult();
        }
        $params['id']=$this->getLegalParam('id','str');
        $result=$this->model->updatePrivilege($params['subArr'],$params['action'],$params['name'],$params['id']);
        if($result)
        {
            $this->inputResult($result);
        }
        $this->inputErrorWithDesc('操作失败');
    }

    public function deleteAjaxAction()
    {
        $params['id']=$this->getLegalParam('id','str');
        $params['type']=$this->getLegalParam('type','enum',array(1,2,3),false);//1 用户 2角色 3 权限
        if(in_array(false,$params))
        {
            $this->inputParamErrorResult();
        }

        switch($params['type'])
        {
            case 1:
                $result=$this->model->deleteUser($params['id']);
                break;
            case 2:
                $result=$this->model->deleteRole($params['id']);
                break;
            case 3:
                $result=$this->model->deletePrivilege($params['id']);
                break;
            default:
                $result=false;
                break;
        }
        if($result)
        {
            $this->inputResult($result);
        }
        $this->inputErrorWithDesc('操作失败');
    }



} 