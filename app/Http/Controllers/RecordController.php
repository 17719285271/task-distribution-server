<?php
namespace App\Http\Controllers;

use App\Http\Service\RecordService;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    private $recordService;

    public function __construct()
    {
        $this->recordService = new RecordService();
    }

    /**
     * 任务记录
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function taskRecord(Request $request)
    {
        $params = $request->all();
        $pageData = $this->recordService->taskRecord($params);
        return view("recordPage", ["pageData" => $pageData, "params" => $params]);
    }
}
