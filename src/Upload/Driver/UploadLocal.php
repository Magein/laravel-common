<?php

namespace Magein\Common\Upload\Driver;

use Illuminate\Http\UploadedFile;
use Magein\Common\AssetPath;
use Magein\Common\MsgContainer;
use Magein\Common\Upload\UploadData;

class UploadLocal
{
    /**
     * 上传的标记
     * @var string
     */
    protected string $name = '';

    /**
     * 对应的字段信息
     * @var string
     */
    protected string $field = '';

    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var UploadData
     */
    protected $uploadData;

    /**
     * @param UploadedFile|null $file
     * @param UploadData|null $uploadData
     */
    public function __construct(UploadedFile $file = null, UploadData $uploadData = null)
    {
        $this->name = (string)request('name', '');
        $this->field = (string)request('field', '');
        $file && $this->file = $file;
        $uploadData && $this->uploadData = $uploadData;
    }

    /**
     * 移动之前的回调函数
     * @return mixed
     */
    protected function before()
    {
        return true;
    }

    /**
     * 移动之后的回调函数
     * @return mixed
     */
    protected function after()
    {
        return true;
    }

    public function move()
    {
        if (!$this->before()) {
            return MsgContainer::msg('上传出现错误');
        };

        $uploadData = $this->uploadData;
        $config = config('filesystems.upload');
        if ($config) {
            try {
                $config = new $config($this->name, $this->field, $this->file);
                if (method_exists($config, 'uploadData')) {
                    /**
                     * @var UploadData
                     */
                    $uploadData = $config->uploadData();
                }
            } catch (\Exception $exception) {
                $uploadData = null;
            }
        }

        if (empty($uploadData)) {
            $uploadData = new UploadData();
            $uploadData->setByMime($this->file->getMimeType());
        }

        $size = $uploadData->getSize();
        $ext = $uploadData->getExtend();
        $allow_size = $size * 1024;
        if ($this->file->getSize() > $allow_size) {
            return MsgContainer::msg('文件超出限制大小:允许的最大值为' . $allow_size . 'K');
        }

        $origin_ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        if ($ext && !in_array($origin_ext, $ext)) {
            return MsgContainer::msg('不允许的文件类型');
        }
        $filepath = $uploadData->getSavePath();
        $save_path = AssetPath::replaceStorageFilePath($this->file->store($filepath));

        if (!$this->after()) {
            return MsgContainer::msg('上传出现错误');
        };

        $data = [
            'filepath' => $save_path,
            'url' => AssetPath::getVisitPath($save_path)
        ];

        $uploadData->complete($data);

        return $data;
    }
}
