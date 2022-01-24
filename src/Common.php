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

if (!function_exists('log')) {
    function log($message, $data)
    {
        if ($message && empty($data)) {
            $message = 'debug';
            $data = [$message];
        } elseif ($message && $data && !is_array($data)) {
            $data = [$data];
        } else {
            $message = 'debug';
            $data = [];
        }

        \Illuminate\Support\Facades\Log::debug($message, $data);

        return true;
    }
}