<?php

namespace Magein\Common;

class BaseService
{
    /**
     * @var BaseService|null
     */
    protected static ?BaseService $instance = null;

    /**
     * @return \Magein\Common\BaseService
     */
    public static function instance(): BaseService
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
