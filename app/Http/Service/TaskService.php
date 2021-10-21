<?php

namespace App\Http\Service;

use App\Model\TaskBaseInfo;
use App\Model\TaskExtendInfo;
use App\Model\TaskGenerationRecord;
use App\Util\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService
{
    use ResponseTrait;

    public function addTask($params)
    {
        $extends = $this->initExtendParams($params["key"], $params["amount"], $params["quantity"]);
        DB::beginTransaction();
        try {
            $taskBaseInfo = new TaskBaseInfo;
            $taskBaseInfo->business_name = $params["shopName"];
            $taskBaseInfo->task_name = "ceshji";
            $taskBaseInfo->product_url = $params["productUrl"];
            $taskBaseInfo->phone_price = $params["phonePrice"];
            $taskBaseInfo->task_require = $params["taskRequire"];
            $taskBaseInfo->commission = $params["commission"];
            $taskBaseInfo->create_user = 1;
            $taskBaseInfo->product_type = $params["productType"];
            $taskBaseInfo->img = $params["fileName"];
            $taskBaseInfo->save($params);
            $this->saveTaskExtend($taskBaseInfo->task_id, $extends);
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    private function saveTaskExtend($taskId, $extends)
    {
        if (null == $taskId || null == $extends) {
            throw new \HttpInvalidParamException("参数错误");
        }

        $data = [];
        foreach ($extends as $extend) {
            $extendInfo = ["task_id" => $taskId, "key" => $extend["key"], "amount" => $extend["amount"], "quantity" => $extend["quantity"], "created_at" => date("Y-m-d H:i:s"), "updated_at" => date("Y-m-d H:i:s")];
            array_push($data, $extendInfo);
        }

        TaskExtendInfo::insert($data);
    }

    /**
     * 初始化扩展参数
     * @param $keys
     * @param $amounts
     * @param $quantitys
     * @return array
     */
    private function initExtendParams($keys, $amounts, $quantitys)
    {
        $extends = [];
        for ($i = 0; $i < count($keys); $i++) {
            $extend = ["key" => $keys[$i], "amount" => $amounts[$i], "quantity" => $quantitys[$i]];
            array_push($extends, $extend);
        }

        return $extends;
    }

    /**
     * 分页获取任务列表
     * @param $params
     */
    public function taskPage($params)
    {
        $where = [];
        if (isset($params["startDate"]) && !is_null($params["startDate"])) {
            $where[] = ["created_at", ">=", $params["startDate"]];
        }

        if (isset($params["endDate"]) && !is_null($params["endDate"])) {
            $where[] = ["created_at", "<=", $params["endDate"]];
        }

        if (isset($params["businessName"]) && !is_null($params["businessName"])) {
            $where[] = ["business_name", "like", "%" . $params["businessName"] . "%"];
        }

        return TaskBaseInfo::where($where)->orderBy("created_at", "desc")->paginate(15)->appends($params);
    }


    /**
     * 根据任务id获取所需刷手数量
     * @param array $taskId
     * @return mixed
     */
    public function getNeedHands(array $taskId)
    {
        $needHand = 0;
        $taskData = TaskBaseInfo::find($taskId);
        foreach ($taskData as $key => $task) {
            $taskData[$key]["extendInfo"] = TaskExtendInfo::where("task_id", $task->task_id)->get();
        }

        //计算店铺与类目数量
        $countArray = [];
        foreach ($taskData as $datum) {
            $countArray["business"][$datum->business_name] = [$datum->task_id];
            $countArray["type"][$datum->product_type] = [$datum->task_id];
        }

        $businessCount = count($countArray["business"]);
        $typeCount = count($countArray["type"]);
        //如果有店铺或类目数量为1 则有多少单就需要多少刷手
        if ($businessCount == 1 || $typeCount == 1) {
            $minDataTaskIds = $taskId;
        } else {
            //否则 取最小的一个有多少单 那就需要多少个刷手
            $minDataTaskIds = $businessCount <= $typeCount ? $countArray["business"] : $countArray["type"];
        }

        foreach ($taskData as $taskDatum) {
            if (in_array($taskDatum->task_id, $minDataTaskIds)) {
                foreach ($taskDatum["extendInfo"] as $extend) {
                    $needHand += $extend->quantity;
                }
            }
        }

        return $needHand;
    }

    /**
     * 生成任务
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Exception
     */
    public function taskGeneration($params)
    {
        $utilService = new UtilService();
        $handsData = $utilService->verifyHandsNumAndGetData($params["fileName"], $this->getNeedHands($params["taskId"]));
        if (!$handsData) {
            return $this->error(-1, "刷手人数不足，请确认文件内容");
        }

        DB::beginTransaction();
        try {
            $record = new TaskGenerationRecord;
            $record->create_user = 1;
            $record->hands_file = $params["fileName"];
            $record->save();

            $recordTaskArray = [];
            foreach ($params["taskId"] as $id) {
                array_push($recordTaskArray, ["record_id" => $record->record_id, "task_id" => $id]);
            }

            DB::table("generation_record_task")->insert($recordTaskArray);
            $recordHandsArray = [];
            foreach ($handsData as $handsDatum) {
                array_push($recordHandsArray, ["record_id" => $record->record_id, "hands_name" => $handsDatum]);
            }

            DB::table("generation_record_hands")->insert($recordHandsArray);
            DB::table("task_base_info")->whereIn("task_id", $params["taskId"])->update(["is_generation" => 1]);
        } catch (\Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            Log::error("[service] taskGeneration 入库错误：$message");
            return $this->error(-1, "构建任务错误，请稍后重试");
        }

        DB::commit();
        return $this->success("任务分配成功，请稍后移步至下载中心查看");
    }
}
