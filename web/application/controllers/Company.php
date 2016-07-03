<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/9/2
 * Time: 10:18
 */

class CompanyController extends BaseController
{
    /**
     * @var CompanyModel
     */
    public $model;
    public function init()
    {
        parent::init();
        $this->model=new CompanyModel();
    }

    /**
     * 企业管理
     */
    public function indexAction()
    {
        $this->view->page='company-index-page';
        $list=$this->model->getCompanyList(true);
        $accountModel=new AccountModel();
        $privilegeList=$accountModel->getAllPrivilege($this->uid);
        $this->view->list=$list;
        $this->view->privilegeList=$privilegeList;
    }

    /**
     * 修改或者添加企业
     */
    public function updateCompanyAjaxAction()
    {
        $params['name']=$this->getLegalParam('name','str');
        $params['domain']=$this->getLegalParam('domain','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $params['privilege']=$this->getLegalParam('privilege','raw');

        $params['id']=$this->getLegalParam('id','str');
        $result=$this->model->updateCompany($params['name'],$params['domain'],$params['privilege'],$params['id']);

        if($result || $result ===0)
        {
            $this->inputResult($result);
        }
        $this->inputErrorWithDesc('操作失败');
    }

    /**
     * 删除企业
     */
    public function deleteCompanyAjaxAction()
    {
        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $result=$this->model->deleteCompany($params['id']);

        if($result)
        {
            $this->inputResult($result);
        }
        $this->inputErrorWithDesc('操作失败');
    }

    /**
     * 管理员设置
     */
    public function userAction()
    {
        $this->view->page='company-user-page';
        $companyList=$this->model->getCompanyList();
        $adminList=$this->model->getCompanyAdmin();
        if(!empty($companyList) && !empty($adminList))
        {
            $map=array();
            foreach($companyList as $v)
            {
                $map[$v['id']]=$v;
            }
            foreach($adminList as &$v2)
            {
                if(array_key_exists($v2['cid'],$map))
                {
                    $v2['company_name']=$map[$v2['cid']]['name'];
                }
            }
        }

        $this->view->companyList=$companyList;
        $this->view->adminList=$adminList;
    }

    /**
     * 添加或者修改企业管理员
     */
    public function updateAdminAjaxAction()
    {
        $params['account']=trim($this->getLegalParam('account','str'));
        $params['cid']=trim($this->getLegalParam('cid','str'));
        $password=trim($this->getLegalParam('password','str'));

        $id=$this->getLegalParam('id','str');
        if(empty($id))
        {
            $params['password']=$password;
        }
        if(in_array(false,$params,true))
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

        $params['id']=$id;
        $accountModel=new AccountModel();
        $result=$accountModel->updateUser($params['account'],$params['password'],$params['name'],$params['phone'], 0,$params['id'],$params['cid'],1);
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

    /**
     * 删除企业管理员
     */
    public function deleteAdminAjaxAction()
    {
        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $result=$this->model->deleteAdmin($params['id']);

        if($result)
        {
            $this->inputResult($result);
        }
        $this->inputErrorWithDesc('操作失败');
    }

    /**
     * 设置超级企业
     */
    public function updateSuperCompanyAjaxAction()
    {
        $params['id']=$this->getLegalParam('id','str');
        $params['type']=$this->getLegalParam('type','enum',array(0,1));
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }

        switch($params['type'])
        {
            case 0:
                $result=$this->model->setSuperCompany($params['id']);
                break;
            case 1:
                $result=$this->model->deleteSuperCompany($params['id']);
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