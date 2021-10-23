<?php

namespace App\Http\Controllers;

use App\Util\ResponseTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResponseTrait;

    public function doLogin(Request $request)
    {
        $userName = $request->input("userName");
        $passWord = $request->input("passWord");
        if (strlen($userName) < 1 || strlen($passWord) < 1) {
            return $this->error(-1, "用户名或密码为空");
        }
        $where["user_name"] = $userName;
        $where["pass_word"] = $passWord;
        $user = DB::table("users")->where($where)->first();
        if (empty($user)) {
            return $this->error(-1, "用户名或密码错误");
        }

        session(["userId" => $user->user_id, "userName" => $user->user_name]);
        return $this->success();
    }

    /**
     * 退出登录
     * @param Request $request
     */
    public function loginOut(Request $request)
    {
        session()->forget($request->session()->get("userName"));
        session()->forget($request->session()->get("userId"));
        redirect("login");
    }
}
