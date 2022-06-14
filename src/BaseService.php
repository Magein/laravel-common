<?php

namespace Magein\Common;

class BaseService
{
    /**
     * @var BaseService
     */
    protected static BaseService $instance;

    protected ?Message $message = null;

    /**
     * @return static
     */
    public static function instance(): BaseService
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function response($data = null)
    {
        if ($data) {
            return ApiResponse::success($data);
        }

        $message = $this->message;
        if (empty($data) || $message) {
            return ApiResponse::data($message->getCode(), $message->getMessage(), $message->getData());
        }

        return ApiResponse::success();
    }

    /**
     * @param string $error
     * @param string|int $code
     * @param $data
     * @return bool
     */
    public function error(string $error, $code = ApiCode::ERROR, $data = null): bool
    {
        $this->message = new Message($error, $code, $data);

        return false;
    }
}
