<?php

namespace App\Http\Service;

use App\Model\TaskBaseInfo;
use App\Model\TaskExtendInfo;
use App\Model\TaskGenerationRecord;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MakeService
{
    private $taskExcelPath;

    public function __construct()
    {
        $this->taskExcelPath = public_path() . "/taskExcel/";
    }

    public function makeTaskFile()
    {
        $record = TaskGenerationRecord::where("status", 1)->orderBy("created_at", "desc")->first();
        if (null == $record) {
            return false;
        }

        $recordId = $record->record_id;
        Log::info("开始生成任务文件，记录id：$recordId");
//       $record->status = 2;
//       $record->save();


        $taskIds = $this->getTasks($recordId);
        $hands = $this->getHands($recordId);
        $handsData = $this->initHandsData($taskIds, $hands);

        $utilService = new UtilService();
                $dirData = $this->mkdirByTime($recordId);
        $utilService->makeHandsExcel($handsData, $dirData["worker"]);
        $utilService->makeCountExcel($handsData, $dirData["count"], $recordId);
        $this->zipFile($dirData["path"], $this->taskExcelPath, $dirData["zipName"]);
//        $record->down_path = $dirData["zipName"];
//        $record->status = 3;

        $record->save();
    }

    /**
     * 根据记录获取任务id
     * @param $recordId
     * @return array
     */
    public function getTasks($recordId)
    {
        $taskArray = DB::table("generation_record_task")->where("record_id", $recordId)->get();
        $taskIdArray = [];
        foreach ($taskArray as $value) {
            array_push($taskIdArray, $value->task_id);
        }

        return $taskIdArray;
    }

    /**
     * 根据记录获取刷手
     * @param $recordId
     * @return array
     */
    public function getHands($recordId)
    {
        $handsArray = DB::table("generation_record_hands")->where("record_id", $recordId)->get();
        $handsNameArray = [];
        foreach ($handsArray as $value) {
            array_push($handsNameArray, $value->hands_name);
        }

        return $handsNameArray;
    }

    /**
     * 构建刷手数据表信息
     * @param $taskIds
     * @param $hands
     * @return array
     */
    public function initHandsData($taskIds, $hands)
    {
        $taskInfoArray = TaskBaseInfo::find($taskIds);
        $countHandsNum = count($hands) - 1;
        $data = [];

        foreach ($taskInfoArray as $item) {
            $extendInfoArray = TaskExtendInfo::where("task_id", $item["task_id"])->get();
            $currentHandsNum = 0;
            foreach ($extendInfoArray as $value) {
                for ($i = 0; $i < $value->quantity; $i++) {
                    //取刷手
                    if (!isset($data[$hands[$currentHandsNum]])) {
                        $data[$hands[$currentHandsNum]] = [];
                    }

                    if (empty($data[$hands[$currentHandsNum]]) || $data[$hands[$currentHandsNum]][count($data[$hands[$currentHandsNum]]) - 1]["productType"] != $item->product_type && $data[$hands[$currentHandsNum]][count($data[$hands[$currentHandsNum]]) - 1]["businessName"] != $item->business_name) {
                        array_push($data[$hands[$currentHandsNum]], ["num" => count($data[$hands[$currentHandsNum]]) + 1, "key" => $value->key, "img" => $item->img, "taskRequire" => $item->task_require, "phonePrice" => $this->initPhonePriceString($item->phone_price), "businessName" => $item->business_name, "productType" => $item->product_type, "commission" => $item->commission, "amount" => $value["amount"]]);
                    }

                    //增加刷手键值索引
                    $currentHandsNum = $countHandsNum == $currentHandsNum ? 0 : ++$currentHandsNum;
                }
            }
        }

       return $data;
    }


    /**
     * 根据记录id时间创建文件夹
     * @param $recordId
     * @return array
     */
    public function mkdirByTime($recordId)
    {
        $str = $recordId . date("YmdHis");
        $dirName = $this->taskExcelPath . $str;
        File::makeDirectory($dirName . "/" . "刷手", 0755, true);
        File::makeDirectory($dirName . "/" . "商家", 0755, true);
        File::makeDirectory($dirName . "/" . "统计", 0755, true);

        return ["worker" => $dirName . "/" . "刷手" . "/", "business" => $dirName . "/" . "商家" . "/", "count" => $dirName . "/" . "统计" . "/", "path" => $dirName, "zipName" => $str];
    }

    /**
     * 初始化手机端价格
     * @param $phonePrice
     * @return string
     */
    private function initPhonePriceString($phonePrice)
    {
        $d = $phonePrice - 100;
        if ($d < 0) {
            $d = 0;
        }

        $a = $phonePrice + 100;
        return $d . "-" . $a;
    }


    /**
     * 压缩文件
     * @param $path
     * @param $toPath
     * @throws \Exception
     */
    private function zipFile($path, $toPath, $zipName) {
        $zipper = new Zipper();
        $arr = glob($path);
        $res = $zipper->make($toPath . $zipName . ".zip")->add($arr);
        $res->close();
    }
}
