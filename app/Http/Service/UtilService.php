<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_Drawing;

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

            $objPHPExcel->getActiveSheet()->setCellValue("A1", "总单数");
            $objPHPExcel->getActiveSheet()->setCellValue("A2", "总本金");
            $objPHPExcel->getActiveSheet()->setCellValue("A3", "总佣金");
            $objPHPExcel->getActiveSheet()->setCellValue("A4", "总金额");

            $allMoney = 0;
            $allCommission = 0;
            foreach ($datum as $v) {
                $allCommission += $v["commission"];
                $allMoney += $v["amount"];
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
            $objPHPExcel->getActiveSheet()->setCellValue("I6", "单价");
            $objPHPExcel->getActiveSheet()->getStyle('A6:I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A6:I6')->getFont()->setBold(true);

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

    /**
     * 生成统计excel
     * @param array $data
     * @param $filePath
     */
    public function makeCountExcel(array $data, $filePath, $recordId)
    {
        $handsExcelData = [];
        //拼装数据为excel左侧表头数据
        foreach ($data as $key => $datum) {
            $data = ["旺旺昵称" => $key, "任务编号" => $recordId];
            $countData = $this->initCountData($datum, $data);
            array_push($handsExcelData, $countData);
        }

        $excelHandData = $this->initCountExcelRowArray($recordId);
        $objPHPExcel = new \PHPExcel();
        $columnArray = range('A', 'Z');
        $objPHPExcel->setActiveSheetIndex(0);

        foreach ($excelHandData as $excelDataKey => $excelHandDatum) {
            $excelDataKey += 1;
            $objPHPExcel->getActiveSheet()->setCellValue("A" . $excelDataKey, $excelHandDatum);
            $objPHPExcel->getActiveSheet()->getStyle("A" . $excelDataKey)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle("A" . $excelDataKey)->getFont()->setBold(true);
            if ($excelHandDatum == "旺旺昵称" || $excelHandDatum == "任务编号") {
                $objPHPExcel->getActiveSheet()->getStyle("A" . $excelDataKey)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle("A" . $excelDataKey)->getFill()->getStartColor()->setARGB('FFFF00');
            }

            if ($excelHandDatum == "合计单数" || $excelHandDatum == "合计货款" || $excelHandDatum == "单笔佣金" || $excelHandDatum == "合计佣金" || $excelHandDatum == "合计打款金额") {
                $objPHPExcel->getActiveSheet()->getStyle("A" . $excelDataKey)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle("A" . $excelDataKey)->getFill()->getStartColor()->setARGB('00CCFF');
            }
        }

        foreach ($handsExcelData as $handsExcelKey => $handsExcelDatum) {
            foreach ($excelHandData as $key => $value) {
                $key += 1;
                $column = $columnArray[$handsExcelKey + 1];
                $objPHPExcel->getActiveSheet()->setCellValue($column . $key, @$handsExcelDatum[$value]);

                if ($value == "任务编号") {
                    $objPHPExcel->getActiveSheet()->getStyle($column . $key)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle($column . $key)->getFill()->getStartColor()->setARGB('339966');
                }

                if ($value == "合计单数" || $value == "合计货款" || $value == "单笔佣金" || $value == "合计佣金" || $value == "合计打款金额") {
                    $objPHPExcel->getActiveSheet()->getStyle($column . $key)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle($column . $key)->getFill()->getStartColor()->setARGB('00CCFF');
                }

                if ($value != "合计单数" && $value != "合计货款" && $value != "单笔佣金" && $value != "合计佣金" && $value != "合计打款金额" && $value != "旺旺昵称" && $value != "任务编号") {
                    $objPHPExcel->getActiveSheet()->getStyle($column . $key)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle($column . $key)->getFill()->getStartColor()->setARGB('CC99FF');
                }
            }
        }

        foreach ($excelHandData as $k => $item) {
            $k += 1;
            if ($k > 2 && $item != "单笔佣金") {
                $before = "B" . $k;
                $after = $columnArray[count($handsExcelData)] . $k;
                $objPHPExcel->getActiveSheet()->setCellValue($columnArray[count($handsExcelData) + 1] . $k, "=SUM($before:$after)");
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($filePath . $recordId . "小组统计.xlsx");
    }

    /**
     * 初始化表格统计数据
     * @param $data
     * @param $countData
     * @return mixed
     */
    private function initCountData($data, $countData)
    {
        $countData["合计货款"] = 0;
        $countData["合计佣金"] = 0;
        $countData["合计打款金额"] = 0;
        $countData["单笔佣金"] = 0;
        $countData["合计单数"] = 0;

        foreach ($data as $datum) {
            $countData["合计单数"] = count($data);
            $countData["合计货款"] += $datum["amount"];
            $countData["合计佣金"] += $datum["commission"];
            $countData["合计打款金额"] = $countData["合计货款"] + $countData["合计佣金"];
            $countData[$datum["businessName"]] = $datum["amount"];
            $countData["单笔佣金"] = $datum["commission"];
        }

        return $countData;
    }

    /**
     * 初始化表头填充数据
     * @param $recordId
     * @return string[]
     */
    private function initCountExcelRowArray($recordId)
    {
        $taskIdDataArray = DB::table("generation_record_task")->where("record_id", $recordId)->get();
        foreach ($taskIdDataArray as $item) {
            $taskIdArray[] = $item->task_id;
        }
        $taskArray = DB::table("task_base_info")->whereIn("task_id", $taskIdArray)->get();

        foreach ($taskArray as $task) {
            $businessName[] = $task->business_name;
        }

        $array = array("旺旺昵称", "任务编号");
        foreach ($businessName as $item) {
            array_push($array, $item);
        }

        array_push($array, "合计单数");
        array_push($array, "合计货款");
        array_push($array, "单笔佣金");
        array_push($array, "合计佣金");
        array_push($array, "合计打款金额");
        return $array;
    }

}
