<?php

namespace Magein\Common;

trait Error
{
    /**
     * 错误信息
     * @var string
     */
    protected string $_error;

    /**
     * 获取错误信息
     * @return string
     */
    public function getError(): string
    {
        return $this->_error;
    }

    /**
     * @param string $error
     * @return false
     */
    public function setError(string $error): bool
    {
        $this->_error = $error;

        return false;
    }
}
