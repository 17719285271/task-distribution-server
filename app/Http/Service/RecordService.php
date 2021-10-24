<?php
namespace App\Http\Service;

use App\Model\TaskGenerationRecord;

class RecordService
{
    public function taskRecord(array $params)
    {
        $where = [];
        if (isset($params["startDate"]) && !is_null($params["startDate"])) {
            $where[] = ["created_at", ">=", $params["startDate"]];
        }

        if (isset($params["endDate"]) && !is_null($params["endDate"])) {
            $where[] = ["created_at", "<=", $params["endDate"]];
        }

        return TaskGenerationRecord::where($where)->orderBy("created_at", "desc")->paginate(15)->appends($params);
    }
}
