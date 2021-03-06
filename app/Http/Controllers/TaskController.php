<?php

namespace App\Http\Controllers;

use App\Http\Service\MakeService;
use App\Http\Service\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private $taskService;

    private $makeService;

    private $taskExcelPath;

    public function __construct()
    {
        $this->taskService = new TaskService();
        $this->makeService = new MakeService();
        $this->taskExcelPath = public_path() . "/taskExcel/";
    }

    /**
     * 添加任务
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addTask(Request $request)
    {
        if ($request->isMethod("get")) {
            return response()->view("task");
        } elseif ($request->isMethod("post")) {
            $params = $request->all();
            if ($this->taskService->addTask($params)) {
                return $this->success();
            } else {
                return $this->error(-1, "添加失败，请稍后再试");
            }

        }
    }

    /**
     * 分页获取任务列表
     * @param Request $request
     * @return mixed
     */
    public function taskPage(Request $request)
    {
        $params = $request->all();
        $pageData = $this->taskService->taskPage($params);
        return view("taskPage", ["pageData" => $pageData, "params" => $params]);
    }


    /**
     * 任务分配
     * @param Request $request
     * @return view
     */
    public function assignTask(Request $request)
    {
        $taskId = $request->taskId;
        if (is_null($taskId)) {
            return $this->error(-1, "请至少选择一个任务");
        }

        $taskIdArray = explode(",", $taskId);
        return view("assignTask", ["taskId" => $taskIdArray, "needHands" => $this->taskService->getNeedHands($taskIdArray)]);
    }

    /**
     * 任务分配
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function taskGeneration(Request $request)
    {
        $params = $request->all();
        if (count($params["taskId"]) <= 0 || !isset($params["fileName"])) {
            return $this->error(-1, "参数错误，请检查文件");
        }

        return $this->taskService->taskGeneration($params);
    }

    /**
     * 生成任务文件
     */
    public function makeTaskFile()
    {
        return $this->makeService->makeTaskFile();
    }

    public function downTaskExcel(Request $request)
    {
        $filePath = $request->input("filePath");

        if (strlen($filePath) == 0) {
            return back()->withErrors("文件路径错误");
        }
        return response()->download($this->taskExcelPath . $filePath);
    }
}
