<?php

namespace Magein\Common;

trait BaseService
{
    /**
     * @var null
     */
    protected static $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function error($message, $code = ApiCode::ERROR, $data = null)
    {
        abort(ApiResponse::data($code, $message, $data));

        return false;
    }
}
