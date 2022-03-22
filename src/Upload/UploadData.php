<?php

namespace Magein\Common\Upload;

class UploadData
{
    const IMAGE = ['jpg', 'png', 'gif', 'jpeg'];

    const AUDIO = ['mp3', 'wav'];

    const VIDEO = ['mp3', 'mp4', 'avi', 'wmv', 'mpg', 'mpeg', 'mov', 'rm'];

    const FILE = ['doc', 'docx', 'txt', 'xls', 'xlsx', 'pdf', 'ppt', 'md'];

    /**
     * 单位K
     * @var int
     */
    private int $size = 0;

    /**
     * 文件的扩展名称
     * @var array
     */
    private array $extend = [];

    /**
     * 保存路径
     * @var string
     */
    private string $save_path = '';

    private $call = null;

    /**
     * @param string $save_path
     * @param string|array $extend
     * @param int $size
     */
    public function __construct(string $save_path = '', $extend = ['png', 'jpg', 'gif'], int $size = 512)
    {
        if (empty($save_path)) {
            $save_path = date('Y/m/d');
        }
        $this->setSavePath($save_path);
        $this->setExtend($extend);
        $this->setSize($size);
    }

    public function setByMime($mime, $return_int = false)
    {
        $type = 0;
        if (preg_match('/image/', $mime)) {
            $type = 1;
            $filepath = 'image';
            $extend = self::IMAGE;
            $size = 512;
        } elseif (preg_match('/audio/', $mime)) {
            $type = 2;
            $filepath = 'audio';
            $extend = self::AUDIO;
            $size = 1024 * 6;
        } elseif (preg_match('/video/', $mime)) {
            $type = 3;
            $filepath = 'video';
            $extend = self::VIDEO;
            $size = 1024 * 6;
        } else {
            $filepath = 'file';
            $extend = self::FILE;
            $size = 1024 * 100;
        }

        $filepath = $filepath . '/' . date('Y/m/d');

        $this->setSavePath($filepath);
        $this->setExtend($extend);
        $this->setSize($size);

        return $return_int ? $type : $filepath;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size)
    {
        $this->size = $size;
    }

    /**
     * @return string[]
     */
    public function getExtend(): array
    {
        return $this->extend;
    }

    /**
     * @param array|string $extend
     */
    public function setExtend(array $extend)
    {
        if (is_string($extend) && preg_match('/,/', $extend)) {
            $extend = explode(',', $extend);
        }
        $this->extend = $extend;
    }

    /**
     * @return string
     */
    public function getSavePath(): string
    {
        return $this->save_path;
    }

    /**
     * @param string $save_path
     * @param bool $is_public
     */
    public function setSavePath(string $save_path, bool $is_public = true)
    {
        $save_path = trim($save_path, '/');

        if ($is_public) {
            $save_path = 'public/' . $save_path;
        }

        $this->save_path = $save_path;
    }

    public function setCall($call)
    {
        $this->call = $call;
    }

    public function complete($data)
    {
        if (is_callable($this->call)) {
            call_user_func($this->call, $data);
        }
    }
}
