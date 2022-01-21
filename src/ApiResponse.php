<?php

namespace Magein\Common;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * @param int $code
     * @param string $msg
     * @param mixed $data
     * @return Response|ResponseFactory
     */
    public static function data(int $code = ApiResponseCode::SUCCESS, string $msg = '', $data = null)
    {
        if ($data instanceof Collection && $data->isEmpty()) {
            $data = null;
        }

        if (empty($data) && $data != 0) {
            $data = null;
        }

        if ($data instanceof LengthAwarePaginator) {
            $data = ApiPaginate::toArray($data);
        }

        return response(
            [
                'code' => $code,
                'msg' => $msg,
                'data' => $data
            ]
        );
    }

    /**
     * @param int $msg
     * @param mixed $data
     * @return Response|ResponseFactory
     */
    public static function success($data = [], $msg = '')
    {
        return self::data(ResponseCode::SUCCESS, $msg, $data);
    }

    /**
     * @param int $code
     * @param string $msg
     * @param mixed $data
     * @return Response|ResponseFactory
     */
    public static function error(string $msg = '', int $code = ResponseCode::ERROR, $data = [])
    {
        return self::data($code, $msg, $data);
    }

    /**
     * @param $result
     * @return Response|ResponseFactory
     */
    public static function auto($result)
    {
        if ($result === false) {
            return self::error('');
        } elseif ($result instanceof MsgContainer) {
            return self::data($result->getCode(), $result->getMessage(), $result->getData());
        }
        return self::success($result, 'success');
    }
}