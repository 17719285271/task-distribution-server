<?php

namespace App\Http\Service;

use App\Model\TaskBaseInfo;
use App\Model\TaskExtendInfo;
use App\Model\TaskGenerationRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MakeService
{

    public function makeTaskDir()
    {
        $record = TaskGenerationRecord::where("status", 1)->orderBy("created_at", "desc")->first();
        $recordId = $record->record_id;
        Log::info("开始生成任务文件，记录id：$recordId");
//       $record->status = 2;
//       $record->save();

        $taskIds = $this->getTasks($recordId);
        $hands = $this->getHands($recordId);
        return $this->initHandsData($taskIds, $hands);
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

                    array_push($data[$hands[$currentHandsNum]], ["num" => count($data[$hands[$currentHandsNum]]) + 1, "key" => $value->key, "img" => $item->img, "taskRequire" => $item->task_require, "phonePrice" => $item->phone_price, "businessName" => $item->business_name]);

                    //增加刷手键值索引
                    $currentHandsNum = $countHandsNum == $currentHandsNum ? 0 : ++$currentHandsNum;
                }
            }
        }

       return $data;
    }
}
