<?php


namespace App\Util;



trait ResponseTrait
{

    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($message = '', $code = 0)
    {
        return response()->json([
            'code' => $code,
            'message' => $message ?: CodeConstant::getMessage($code),
        ]);
    }

    /**
     * @param int $code
     * @param $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($code = -1, $message = '')
    {
        return response()->json([
            'code' => $code,
            'message' => $message ?: CodeConstant::getMessage($code),
        ]);
    }

    protected function data($data = [], $message = '', $code = 0)
    {

        return $this->response(
            $code, $message ?: CodeConstant::getMessage(CodeConstant::SUCCESS_CODE), $data
        );
    }

    protected function response($code, $message, $data)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
