<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/login', "login");

Route::post("doLogin", "Controller@doLogin");

Route::middleware(["session"])->group(
    function () {
        Route::get('/', function () {
            return view('index');
        });

        Route::any("loginOut", "Controller@loginOut");

//添加任务
        Route::any("addTask", "TaskController@addTask");

//上传文件
        Route::any("fileUpload", "UploadController@fileUpload");

//任务列表页
        Route::any("taskPage", "TaskController@taskPage");

//生成任务分配记录
        Route::any("taskGeneration", "TaskController@taskGeneration");

//添加生成任务（选择任务与选择刷手文件）
        Route::any("assignTask", "TaskController@assignTask");

//生成任务文件
        Route::any("makeTaskFile", "TaskController@makeTaskFile");

//下载任务文件
        Route::any("downTaskExcel", "TaskController@downTaskExcel");

//任务记录页
        Route::any("taskRecord", "RecordController@taskRecord");
    }
);

