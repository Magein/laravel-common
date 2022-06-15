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
    protected $content = null;

    /**
     * @param string $message
     * @param int $code
     * @param mixed $content
     */
    public function __construct(string $message = '', int $code = ApiCode::SUCCESS, $content = null)
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

    /**
     * @param mixed $content
     * @param string $message
     * @return \Magein\Common\Output
     */
    public static function success($content = null, string $message = ''): Output
    {
        $instance = new self();
        $instance->setCode(ApiCode::SUCCESS);
        $instance->setContent($content);
        $instance->setMessage($message);

        return $instance;
    }

    /**
     * @param string $message
     * @param mixed $content
     * @return \Magein\Common\Output
     */
    public static function error(string $message, $content = null): Output
    {
        $instance = new self();
        $instance->setCode(ApiCode::ERROR);
        $instance->setContent($content);
        $instance->setMessage($message);

        return $instance;
    }
}