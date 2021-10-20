<?php

namespace App\Http\Service;

class UtilService
{
    private $handsFilePath;

    public function __construct()
    {
        $this->handsFilePath = public_path() . "/handsExcel/";
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
        if (count($handsData) <= 0 || count($handsData) < $num ) {
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
        return array_splice ($array, 1);
    }
}
