<?php

namespace Magein\Common;

class BaseService
{
    /**
     * @var BaseService
     */
    protected static BaseService $instance;

    /**
     * @return static
     */
    public static function instance(): ?BaseService
    {
        self::$instance = new static();

        return self::$instance;
    }

    /**
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @return MsgContainer
     */
    public function error(string $message, int $code = 1, $data = null): MsgContainer
    {
        return new MsgContainer($message, $code, $data);
    }
}
