<?php

if (!function_exists('isLocal')) {
    function isLocal()
    {
        if (app()->environment() == 'local') {
            return true;
        }
        return false;
    }
}

if (!function_exists('slog')) {
    function slog($message, $data = '')
    {
        if ($message && empty($data)) {
            $data = [$message];
            $message = 'debug';
        } elseif ($message && $data) {
            if (!is_array($data)) {
                $data = [$data];
            }
        } else {
            $message = 'debug';
            $data = [];
        }

        \Illuminate\Support\Facades\Log::debug($message, $data);

        return true;
    }
}
