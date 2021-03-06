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
    public static function data(int $code = ApiCode::SUCCESS, string $msg = '', $data = null)
    {
        if ($data instanceof Collection && $data->isEmpty()) {
            $data = null;
        }

        if (empty($data) && $data !== 0 && $data !== '0') {
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
        return self::data(ApiCode::SUCCESS, $msg, $data);
    }

    /**
     * @param int $code
     * @param string $msg
     * @param mixed $data
     * @return Response|ResponseFactory
     */
    public static function error(string $msg = '', int $code = ApiCode::ERROR, $data = [])
    {
        return self::data($code, $msg, $data);
    }

    /**
     * @param $output
     * @return Response|ResponseFactory
     */
    public static function auto($output)
    {
        if ($output === false) {
            return self::error('');
        } elseif ($output instanceof Output) {
            return self::data($output->getCode(), $output->getMessage(), $output->getData());
        }
        return self::success($output, 'success');
    }
}
