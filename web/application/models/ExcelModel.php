<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */
class ExcelModel
{
    public $objExcel;// 创建一个处理对象实例
    public $objWriter;// 创建文件格式写入对象实例, uncomment
    public $objActSheet;

    public function __construct()
    {
        $objExcel=new PHPExcel();
        $this->objExcel=$objExcel;
        $this->objWriter=new PHPExcel_Writer_Excel5($objExcel);
    }

    public function createOrderExcel($data)
    {
        $objExcel =$this->objExcel;
        $objWriter =$this->objWriter;

        //设置文档基本属性
        $objProps = $objExcel->getProperties();
//        $objProps->setCreator("hollo");
//        $objProps->setLastModifiedBy("hollo");
        $objProps->setTitle("订单信息");
//        $objProps->setSubject("订单信息");
//        $objProps->setDescription("订单信息");
//        $objProps->setKeywords("订单信息");
//        $objProps->setCategory("订单信息");

        //*************************************
        //设置当前的sheet索引，用于后续的内容操作。
        //一般只有在使用多个sheet的时候才需要显示调用。
        //缺省情况下，PHPExcel会自动创建第一个sheet被设置SheetIndex=0
        $objExcel->setActiveSheetIndex(0);
        $objActSheet = $objExcel->getActiveSheet();
        $this->objActSheet=$objActSheet;

        //设置当前活动sheet的名称
        $objActSheet->setTitle('订单信息');

        //设置宽度，这个值和EXCEL里的不同，不知道是什么单位，略小于EXCEL中的宽度
        $objActSheet->getColumnDimension('A')->setWidth(30);
        $objActSheet->getColumnDimension('B')->setWidth(30);
        $objActSheet->getColumnDimension('C')->setWidth(30);
        $objActSheet->getColumnDimension('D')->setWidth(30);
        $objActSheet->getColumnDimension('E')->setWidth(30);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(30);
        $objActSheet->getColumnDimension('H')->setWidth(30);
        $objActSheet->getColumnDimension('I')->setWidth(30);
        $objActSheet->getColumnDimension('J')->setWidth(60);
        $objActSheet->getColumnDimension('K')->setWidth(30);
        //设置单元格的值
        $objActSheet->setCellValue('A1', '订单信息');
        //合并单元格
        $objActSheet->mergeCells('A1:D1');
        //设置样式
        $objStyleA1 = $objActSheet->getStyle('A1');
        $objStyleA1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objFontA1 = $objStyleA1->getFont();
        $objFontA1->setName('宋体');
        $objFontA1->setSize(18);
        $objFontA1->setBold(true);
        //设置居中对齐
        $objActSheet->setCellValue('A2', '订单编号');
        $objActSheet->setCellValue('B2', '员工工号');
        $objActSheet->setCellValue('C2', '手机号');
        $objActSheet->setCellValue('D2', '办公区域');
        $objActSheet->setCellValue('E2', '需求服务');
        $objActSheet->setCellValue('F2', '需求描述');
        $objActSheet->setCellValue('G2', '服务商');
        $objActSheet->setCellValue('H2', '提交时间');
        $objActSheet->setCellValue('I2', '评分（5分)');
        $objActSheet->setCellValue('J2', '评价内容');
        $objActSheet->setCellValue('K2', '状态');

        $this->setCellStyle('A2');
        $this->setCellStyle('B2');
        $this->setCellStyle('C2');
        $this->setCellStyle('D2');
        $this->setCellStyle('E2');
        $this->setCellStyle('F2');
        $this->setCellStyle('G2');
        $this->setCellStyle('H2');
        $this->setCellStyle('I2');
        $this->setCellStyle('J2');
        $this->setCellStyle('K2');

        $scoreMap = [
            1=>'很差',
            2=>'差',
            3=>'一般',
            4=>'好',
            5=>'很好',
        ];

        $n=3;
        foreach($data as $v)
        {
            $objActSheet->getRowDimension($n)->setRowHeight(16);
            $this->setCellStyle('A'.$n);
            $this->setCellStyle('B'.$n);
            $this->setCellStyle('C'.$n);
            $this->setCellStyle('D'.$n);
            $this->setCellStyle('E'.$n);
            $this->setCellStyle('F'.$n);
            $this->setCellStyle('G'.$n);
            $this->setCellStyle('H'.$n);
            $this->setCellStyle('I'.$n);
            $this->setCellStyle('J'.$n);
            $this->setCellStyle('K'.$n);

            $orderNo = $v['order_no'];
            $job_id = $v['job_id'];
            $mobile = $v['mobile'];
            $area = $v['area']['name'];
            $type = $v['type']['name'];
            $desc = $v['desc'];
            $supplier = $v['supplier']['name'];
            $supplier = $supplier ? $supplier : '未分配';
            $time = $v['created_at'];
            $score = $v['score'];
            $scoreTitle = $scoreMap[$score];
            $remark = $v['remark'];
            $status = OrderBuilder::toBuildTypeOptions($v['status']);

            $objActSheet->setCellValue('A'.$n, $orderNo);
            $objActSheet->setCellValue('B'.$n, $job_id);
            $objActSheet->setCellValue('C'.$n, $mobile);
            $objActSheet->setCellValue('D'.$n, $area);
            $objActSheet->setCellValue('E'.$n, $type);
            $objActSheet->setCellValue('F'.$n, $desc);
            $objActSheet->setCellValue('G'.$n, $supplier);
            $objActSheet->setCellValue('H'.$n, $time);
            $objActSheet->setCellValue('I'.$n, "{$score} ($scoreTitle)");
            $objActSheet->setCellValue('J'.$n, $remark);
            $objActSheet->setCellValue('K'.$n, $status);
            ++$n;
        }

        //输出内容
        $path=realpath(APPLICATION_PATH . '/../file/');
        $outputFileName = $path."/path_data_".date("YmdHis",time()).'_'.rand(100000,999999).".xls";
//        $outputFileName = $path."/path_data.xls";
        //到文件
        $objWriter->save($outputFileName);
        return $outputFileName;
    }

    /**
     * 设置单元格居中和边框样式
     * @param $key
     * @param string $style
     */
    public function setCellStyle($key,$style = PHPExcel_Style_Border::BORDER_THIN )
    {
        $this->objActSheet->getStyle($key)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->objActSheet->getStyle($key)->getBorders()->getTop()->setBorderStyle();
        $this->objActSheet->getStyle($key)->getBorders()->getLeft()->setBorderStyle($style);
        $this->objActSheet->getStyle($key)->getBorders()->getRight()->setBorderStyle($style);
        $this->objActSheet->getStyle($key)->getBorders()->getBottom()->setBorderStyle($style);
    }

}













