<?php

namespace Betteryourweb\Helpers;

class Utils
{
    public static function base_dir($path)
    {
        if (!$path) return BASE_DIR;

        $path = trim($path, '/');
        return BASE_DIR . "/" . $path;
    }

    public static function get_var_name($var)
    {
        foreach ($GLOBALS as $var_name => $value) {
            if ($value === $var) {
                return $var_name;
            }
        }

        return false;

    }
}