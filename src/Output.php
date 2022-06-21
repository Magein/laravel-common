<?php

namespace Magein\Common;

class Output
{
    /**
     * @var string
     */
    protected string $message = '';

    /**
     * @var int
     */
    protected int $code = 0;

    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * @param mixed $data
     */
    public function __construct($data = '')
    {
        if (is_string($data)) {
            $this->code = ApiCode::ERROR;
            $this->message = $data;
        } else {
            $this->code = ApiCode::SUCCESS;
            $this->data = $data;
        }
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return intval($this->code);
    }

    /**
     * @param int
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param mixed $data
     * @param string $message
     * @return \Magein\Common\Output
     */
    public static function success($data = null, string $message = ''): Output
    {
        $instance = new self();
        $instance->setCode(ApiCode::SUCCESS);
        $instance->setData($data);
        $instance->setMessage($message);

        return $instance;
    }

    /**
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @return \Magein\Common\Output
     */
    public static function error(string $message, int $code = ApiCode::ERROR, $data = null): Output
    {
        $instance = new self();
        $instance->setCode($code);
        $instance->setData($data);
        $instance->setMessage($message);

        return $instance;
    }
}