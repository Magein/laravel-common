<?php

namespace Magein\Common;

/**
 * @method static success($content = null, $message = '', $code = 0)
 * @method static error($message = '', $code = 1, $content = null)
 */
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
    protected $content = null;

    /**
     * @param string $message
     * @param int $code
     * @param mixed $content
     */
    public function __construct(string $message = '', int $code = ApiCode::ERROR, $content = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->content = $content;
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }
}
