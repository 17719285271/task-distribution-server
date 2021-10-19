<?php

namespace App\Util;


class CodeConstant
{

    const SUCCESS_CODE = 0;

    const ERROR_CODE = -1;

    const NOT_FOUND_ERROR_CODE = -2;

    const REQUEST_METHOD_ERROR_CODE = -3;

    const MODEL_NOT_FOUND_ERROR_CODE = -4;

    const LOGIN_ERROR_CODE = -6;

    const AUTH_FAILED_ERROR_CODE = -7;

    const AUTH_TOKEN_EXPIRED_ERROR_CODE = -8;

    const AUTH_TOKEN_INVALID_ERROR_CODE = -9;

    const AUTH_TOKEN_NULL_ERROR_CODE = -10;

    const PARAM_FAIL_ERROR_CODE = -11;

    const TIMES_VIEWED_TODAY_OVER = -12;

    const WECHAT_AUTH_ERROR_CODE = -13;

    const TOO_MANY_ERROR_CODE = -14;

    const SING_LOGIN_ERROR_CODE = -15;

    const MESSAGE = [
        self::SUCCESS_CODE                          => '请求成功',
        self::ERROR_CODE                            => '请求失败',
        self::NOT_FOUND_ERROR_CODE                  => '当前请求uri不存在',
        self::REQUEST_METHOD_ERROR_CODE             => '请求方式错误',
        self::MODEL_NOT_FOUND_ERROR_CODE            => '当前数据不存在',
//        self::LOGIN_AUTH_ERROR_CODE                 => '登录失败',
        self::LOGIN_ERROR_CODE                      => '登录失败',
        self::AUTH_FAILED_ERROR_CODE                => '请登录后再试',
        self::AUTH_TOKEN_EXPIRED_ERROR_CODE         => 'token已过期',
        self::AUTH_TOKEN_INVALID_ERROR_CODE         => '无效的token',
        self::AUTH_TOKEN_NULL_ERROR_CODE            => 'token不存在',
        self::PARAM_FAIL_ERROR_CODE                 => '请求参数错误',
        self::TIMES_VIEWED_TODAY_OVER               => '今天免费次数已用完',
        self::WECHAT_AUTH_ERROR_CODE                => '微信授权失败',
        self::TOO_MANY_ERROR_CODE                   => '当前请求次数过多，请稍后再试',
        self::SING_LOGIN_ERROR_CODE                 => '您的账户已在其它端登录，是否重新登录',
    ];

    /**
     * 获取状态码对应的消息
     * @param $code
     * @return string
     */
    public static function getMessage($code)
    {
        return self::MESSAGE[$code];
    }
}
