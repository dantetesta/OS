<?php

if (!function_exists('env')) {
    function env($key, $default = null) {
        static $env = null;
        if ($env === null) {
            $env = parse_ini_file(__DIR__ . '/../../.env');
        }
        return $env[$key] ?? $default;
    }
}
