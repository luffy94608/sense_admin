<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class OrderController extends BaseController
{

    /**
     * @var OrderModel
     */
    protected $model = null;

    public function init()
    {
        parent::init();
        $this->model = new OrderModel();
    }

    /**
     * 订单列表
     */
    public function listAction()
    {
        $this->view->page='order-list-page';
        $this->view->typeOptions=OrderBuilder::toBuildTypeOptions();
        $model = new ServiceModel();
        $this->view->suppliers=$model->geSuppliersMap();
        Api::handleLog($this->cid,$this->uid,'查看订单列表','查看订单列表');
    }

    public function getListAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['offset']=$this->getLegalParam('offset','int',array(),0);
        $params['length']=$this->getLegalParam('length','int',array(),10);
        $params['key']=$this->getLegalParam('key','str',array(),'');
        $params['type']=$this->getLegalParam('type','int',array(),0);//0带分配 1已分配 2已完成 3已经关闭

        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $result = $this->model->getListWithStatus($params['type'],$params['key'],$params['offset'],$params['length']);
        $html = OrderBuilder::toBuildList($result['list']);

        $data = array(
            'total' =>$result['total'],
            'html' =>$html
        );
        $this->inputResult($data);
    }

    /**
     * 修改订单状态 分配服务商
     */
    public function updateOrderStatusAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        $type=$this->getLegalParam('type','str');
        if ($type == 0){//修改服务商
            $params['supplier_id']=$this->getLegalParam('supplier_id','str');
            $params['status'] =1;
            Api::handleLog($this->cid,$this->uid,'修改服务商','查看订单列表');
        } else if($type == 1) {//完成
            $params['status']=$this->getLegalParam('status','enum',[2]);
        } else {//关闭
            $params['status']=$this->getLegalParam('status','enum',[4]);
        }
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $result = $this->model->update($params['id'],$params);

        if($result>0)
        {
            $detail = $this->model->getOrderDetail($params['id']);
            $orderNo = $detail['order_no'];
            if ($type == 0){//修改服务商
                $model = new ServiceModel();
                $supplier = $model->getSupplierName($params['supplier_id']);
                $desc = sprintf('修改订单 %s 的服务商为 %s(id 为 %s)',$orderNo,$supplier,$params['supplier_id']);
                Api::handleLog($this->cid,$this->uid,'修改服务商',$desc);
            } else if($type == 1) {//完成
                $desc = sprintf('修改 %s 的订单为完成',$orderNo);
                Api::handleLog($this->cid,$this->uid,'完成订单',$desc);
            } else {//关闭
                $desc = sprintf('修改 %s 的订单为关闭',$orderNo);
                Api::handleLog($this->cid,$this->uid,'关闭订单',$desc);
            }
            $html = OrderBuilder::toBuildItem($detail);
            $this->inputResult($html);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }

    /**
     * 区域管理
     */
    public function areaAction()
    {
        $this->view->page='order-area-page';
        $list = $this->model->getAreaList();
        $html = OrderBuilder::toBuildAreaOrTypeListHtml($list);
        $this->view->html = $html;
        Api::handleLog($this->cid,$this->uid,'查看办公区域模块','查看办公区域模块');
    }

    /**
     * 服务内容管理
     */
    public function typeAction()
    {
        $this->view->page='order-type-page';
        $list = $this->model->getTypeList();
        $html = OrderBuilder::toBuildAreaOrTypeListHtml($list);
        $this->view->html = $html;
        Api::handleLog($this->cid,$this->uid,'查看需求服务模块','查看需求服务模块');
    }

    /**
     * 创建或者修改 服务或者区域
     */
    public function updateAreaOrTypeAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['name']=$this->getLegalParam('name','str');
        $params['type']=$this->getLegalParam('type','enum',[0,1],0);//0 区域 1服务
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');

        if (empty($id))
        {
            $result = $this->model->createOrderOrType($params['name'],$params['type']);
            $params['id'] = $result;
        }
        else
        {
            $result = $this->model->updateOrderOrType($id,$params,$params['type']);
            $params['id'] = $id;
        }

        if($result>0)
        {
            if($params['type']==0){
                $typeName = '办公区域';
            } else {
                $typeName = '需求服务';
            }
            if (empty($id))
            {
                $typeAction = '创建';

            }
            else
            {
                $typeAction = '更新';
            }
            $srcName= $this->model->getAreaOrTypeName($params['id'],$params['type']);
            $typeTitle = sprintf('%s %s',$typeAction,$typeName);
            $desc = sprintf('%s %s(id 为 %s) %s 为 %s',$typeAction,$typeName,$params['id'],$srcName,$params['name']);
            Api::handleLog($this->cid,$this->uid,$typeTitle,$desc);

            $html = OrderBuilder::toBuildAreaOrTypeItemHtml($params);
            $this->inputResult($html);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }

    /**
     * 删除 服务或者区域
     */
    public function deleteAreaOrTypeAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        $params['type']=$this->getLegalParam('type','enum',[0,1],0);//0 区域 1服务
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }

        $result = $this->model->deleteOrderOrType($params['id'],$params['type']);

        if($result>0)
        {
            $name = $this->model->getAreaOrTypeName($params['id'],$params['type']);
            if($params['type']==0){
                $type = '删除办公区域';
            } else {
                $type = '删除需求服务';
            }
            $desc = sprintf('删除 %s(id 为 %s)',$name,$params['id']);
            Api::handleLog($this->cid,$this->uid,$type,$desc);
            $this->inputResult($result);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }

    /**
     * 生成下载的文件
     */
    public function createExcelAjaxAction()
    {
        $result = $this->model->getListWithStatus(-1);
        $list = $result['list'];
        if(empty($list))
        {
            $this->inputErrorWithDesc('没有可导出的数据');
        }
        $keyIndex=array();
        foreach($list as $v)
        {
            $keyIndex[]=$v['status'];
        }
        array_multisort($keyIndex,SORT_ASC,$list);
        $model=new ExcelModel();
        $url=$model->createOrderExcel($list);
        if($url){
            $params = [
                'url'=>$url,
                'name'=>'订单信息',
            ];
            $paramsStr = http_build_query($params);
            $url = sprintf('/download/download-excel?%s',$paramsStr);
            Api::handleLog($this->cid,$this->uid,'下载订单excel','下载订单excel');
        }
        $this->inputResult($url);
    }

}