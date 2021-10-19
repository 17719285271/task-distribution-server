<?php
namespace App\Http\Service;

use App\Model\TaskBaseInfo;
use App\Model\TaskExtendInfo;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function addTask($params) {
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

    private function saveTaskExtend($taskId, $extends) {
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
    private function initExtendParams($keys, $amounts, $quantitys) {
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
    public function taskPage($params) {
        $where = [];
        foreach ($params as $k => $param) {
            if (null != $param) {
                $w = [$k => $param];
                array_push($where, $w);
            }
        }

        TaskBaseInfo::where($where)->paginate(15);
    }
}
