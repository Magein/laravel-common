<?php

namespace Magein\Common;

class AssetPath
{
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