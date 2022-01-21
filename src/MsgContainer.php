<?php

namespace Magein\Common;

class MsgContainer
{
    /**
     * @var string
     */
    private string $message;

    /**
     * @var int
     */
    private int $code;

    /**
     * @var mixed|null
     */
    private $data;

    /**
     * @param string $message
     * @param int $code
     * @param null $data
     */
    public function __construct(string $message = '', int $code = ApiResponseCode::SUCCESS, $data = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return [
            'message' => $this->message,
            'code' => $this->code,
            'data' => $this->data,
        ];
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
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed|null $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param string $message
     * @param int $code
     * @param null $data
     * @return MsgContainer
     */
    public static function msg(string $message, int $code = ApiResponseCode::ERROR, $data = null)
    {
        return new self($message, $code, $data);
    }
}
