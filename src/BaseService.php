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
}
