<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Writer_Excel2007;

class UtilService
{
    private $handsFilePath;

    private $imgPath;

    public function __construct()
    {
        $this->handsFilePath = public_path() . "/handsExcel/";
        $this->imgPath = public_path() . "/shopImg/";
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
    public function makeHandsExcel(array $handsExcelData, $filePath)
    {
        foreach ($handsExcelData as $kkk => $datum) {
            $objPHPExcel = new \PHPExcel();
            $columnArray = range('A', 'Z');
            $objPHPExcel->setActiveSheetIndex(0);

            $array = [0 => ["店铺" => 1, "价格" => 2]];
            $objPHPExcel->getActiveSheet()->setCellValue("A1", "总单数");
            $objPHPExcel->getActiveSheet()->setCellValue("A2", "总本金");
            $objPHPExcel->getActiveSheet()->setCellValue("A3", "总佣金");
            $objPHPExcel->getActiveSheet()->setCellValue("A4", "总金额");

            $allMoney = 0;
            $allCommission = 0;
            foreach ($datum as $v) {
                $allMoney += $v["phonePrice"];
                foreach ($v as $extendValue) {
                    $allCommission += $extendValue["amount"];
                }
            }

            $objPHPExcel->getActiveSheet()->setCellValue("B1", count($datum));
            $objPHPExcel->getActiveSheet()->setCellValue("B2", $allMoney);
            $objPHPExcel->getActiveSheet()->setCellValue("B3", $allCommission);
            $objPHPExcel->getActiveSheet()->setCellValue("B4", $allCommission + $allMoney);
            $objPHPExcel->getActiveSheet()->getStyle('A1:B4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('A1:B4')->getFill()->getStartColor()->setARGB('faff72');

            $rowNum = 7;
            $objPHPExcel->getActiveSheet()->setCellValue("A6", "编号");
            $objPHPExcel->getActiveSheet()->setCellValue("B6", "关键词");
            $objPHPExcel->getActiveSheet()->setCellValue("C6", "图片");
            $objPHPExcel->getActiveSheet()->setCellValue("D6", "任务要求");
            $objPHPExcel->getActiveSheet()->setCellValue("E6", "手机端价格");
            $objPHPExcel->getActiveSheet()->setCellValue("F6", "商家名称");
            $objPHPExcel->getActiveSheet()->setCellValue("G6", "产品类目");
            $objPHPExcel->getActiveSheet()->setCellValue("H6", "该笔佣金");
            $objPHPExcel->getActiveSheet()->setCellValue("H7", "单价");
            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getFont()->setBold(true);

            for ($t = 0; $t < 9; $t++) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnArray[$t])->setWidth(20);
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);

            foreach ($datum as $item) {
                $i = 0;
                foreach ($item as $key => $value) {
                    if ($key == "img") {
                        try {
                            $objDrawing = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath($this->imgPath . $value);
                            $objDrawing->setCoordinates($columnArray[$i] . $rowNum);
                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        } catch (\Exception $exception) {
                            $message = $exception->getMessage();
                            Log::error("写入图片出错：$message");
                        }
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue($columnArray[$i] . $rowNum, $value);
                    }

                    ++$i;
                }
                ++$rowNum;

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save($filePath . $kkk . ".xlsx");
            }
        }

    }

}
