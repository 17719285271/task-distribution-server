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

Route::get('/', function () {
    return view('index');
});

Route::view('/login', "login");

Route::any("addTask", "TaskController@addTask");

Route::any("shopImgUpload", "UploadController@shopImgUpload");

Route::any("taskPage", "TaskController@taskPage");

Route::post("taskGeneration", "TaskController@taskGeneration");
