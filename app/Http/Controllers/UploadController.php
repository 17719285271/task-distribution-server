<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class UploadController
{
    /**
     * 上传商品图片
     *
     * @param Request $request
     * @return false|string
     */
    public function fileUpload(Request $request) {
        $dirName = "";
        if ($request->type == 1) {
            $dirName = "shopImg";
        } else if($request->type == 2) {
            $dirName = "handsExcel";
        }

        $file = $request->file("file");
        $fileName = $this->initFileName($file->getClientOriginalExtension());
        $file->move($dirName, $fileName);
        return json_encode(["code" => 0, "data" => ["fileName" => $fileName], "message" => "成功"]);
    }


    private function initFileName($fileNameExtend) {
        $keys = array_merge(range(0, 9), range('a', 'z'));

        $key = "";
        for ($i = 0; $i < 10; $i++) {
            $key .= $keys[array_rand($keys)];

        }

        $key .= time() . "." . $fileNameExtend;
        return $key;
    }
}
