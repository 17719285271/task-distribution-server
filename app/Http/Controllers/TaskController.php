<?php
namespace App\Http\Controllers;

use App\Http\Service\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private $taskService;

    public function __construct()
    {
        $this->taskService = new TaskService();
    }

    /**
     * 添加任务
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addTask(Request $request) {
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
    public function taskPage(Request $request) {
        $pageData = $this->taskService->taskPage($request->all());
        return view("taskPage", $pageData);
    }
}
