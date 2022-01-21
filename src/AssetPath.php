<?php

namespace Magein\Common;

class AssetPath
{
    /**
     * 去掉带domain的地址
     * @param $path
     * @return string
     */
    public static function toSavePath($path): string
    {
        if ($path) {
            if (is_array($path)) {
                $path = current($path);
            }
            $path = trim(parse_url($path)['path'] ?? '', '/');
        }

        return $path;
    }

    /**
     * 将路径中的public替换成static
     * @param $save_path
     * @return string
     */
    public static function replaceStorageFilePath($save_path): string
    {
        if (empty($save_path)) {
            return $save_path;
        }

        return preg_replace('/^public/', 'static', $save_path);
    }

    /**
     * 获取预览的地址，包含路径的
     * @param $save_path
     * @return string
     */
    public static function getVisitPath($save_path): string
    {
        if (empty($save_path)) {
            return $save_path;
        }

        $save_path = self::replaceStorageFilePath($save_path);
        $save_path = trim($save_path, '/');

        return config('app.url') . '/' . $save_path;
    }
}