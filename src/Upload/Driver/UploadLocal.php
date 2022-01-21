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
    protected UploadedFile $file;

    public function __construct(UploadedFile $file)
    {
        $this->name = (string)request('name', '');
        $this->field = (string)request('field', '');
        $this->file = $file;
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

    public function move(UploadData $uploadData)
    {
        if (!$this->before()) {
            return MsgContainer::msg('上传出现错误');
        };

        $size = $uploadData->getSize();
        $ext = $uploadData->getExtend();
        if ($this->file->getSize() > $size * 1024) {
            return MsgContainer::msg('文件超出限制大小');
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

        return [
            'filepath' => $save_path,
            'url' => AssetPath::getVisitPath($save_path)
        ];
    }
}
