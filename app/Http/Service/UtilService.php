<?php

namespace App\Http\Service;

use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

class UtilService
{
    private $handsFilePath;

    private $imgPath;

    private $taskExcelPath;

    public function __construct()
    {
        $this->handsFilePath = public_path() . "/handsExcel/";
        $this->imgPath = public_path() . "/shopImg/";
        $this->taskExcelPath = public_path() . "/taskExcel/";
    }

    /**
     * 校验刷手人数并获取刷手数据
     * @param $fileName
     * @param $num
     * @return array|false
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function verifyHandsNumAndGetData($fileName, $num)
    {
        $handsData = $this->getHandsExcelData($fileName);
        if (count($handsData) <= 0 || count($handsData) < $num) {
            return false;
        }

        return $handsData;
    }

    /**
     * 获取刷手excel刷手数据
     * @param $fileName
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function getHandsExcelData($fileName)
    {
        $data = [];
        $reader = \PHPExcel_IOFactory::createReader("Excel2007");
        $PHPExcel = $reader->load($this->handsFilePath . $fileName);
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn();// 取得总列数
        /** 循环读取每个单元格的数据 */
        for ($row = 1; $row <= $highestRow; $row++) {//行数是以第1行开始
            for ($column = 'B'; $column <= $highestColumm; $column++) {//列数是以A列开始
                array_push($data, $sheet->getCell($column . $row)->getValue());
            }
        }

        foreach ($data as $key => &$value) {
            if (is_null($value)) {
                unset($data[$key]);
            }
        }

        $array = array_values($data);
        return array_splice($array, 1);
    }

    /**
     * 生成刷手excel
     * @param array $handsExcelData
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function makeHandsExcel(array $handsExcelData, $recordId)
    {
        foreach ($handsExcelData as $kkk => $datum) {
            $objPHPExcel = new \PHPExcel();
            $columnArray = range('A', 'Z');
            $objPHPExcel->setActiveSheetIndex(0);

            $rowNum = 2;
            $objPHPExcel->getActiveSheet()->setCellValue("A1", "编号");
            $objPHPExcel->getActiveSheet()->setCellValue("B1", "关键词");
            $objPHPExcel->getActiveSheet()->setCellValue("C1", "图片");
            $objPHPExcel->getActiveSheet()->setCellValue("D1", "任务要求");
            $objPHPExcel->getActiveSheet()->setCellValue("E1", "手机端价格");
            $objPHPExcel->getActiveSheet()->setCellValue("F1", "商家名称");
            $objPHPExcel->getActiveSheet()->setCellValue("G1", "产品类目");
            $objPHPExcel->getActiveSheet()->setCellValue("H1", "该笔佣金");
            $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);

            for ($t = 0; $t < 9; $t++) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnArray[$t])->setWidth(20);
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);

            foreach ($datum as $item) {
                $i = 0;
                foreach ($item as $value) {
                    $objPHPExcel->getActiveSheet()->setCellValue($columnArray[$i] . $rowNum, $value);
                    ++$i;
                }
                ++$rowNum;

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $filePath = $this->taskExcelPath . $this->mkdirByTime($recordId) . "/";
                $objWriter->save($filePath . $kkk . ".xlsx");
            }
        }
    }

    public function mkdirByTime($recordId)
    {
        $dirName = $recordId . date("Y-m-dH:i:s");
        $dir = iconv("UTF-8", "GBK", $dirName);
        $dir = $this->taskExcelPath . $dir;

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dirName;
    }

}
