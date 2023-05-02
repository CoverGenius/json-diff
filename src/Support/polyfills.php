<?php

declare(strict_types=1);

/*
 * This project needs to support PHP 7.3 so this
 * file is to polyfill functionality missing from PHP 7.3.
 */

/**
 * Introduced in PHP 8.1
 * @see https://www.php.net/manual/en/function.array-is-list.php
 */
if (!function_exists("array_is_list")) {
    function array_is_list(array $array): bool
    {
        $i = 0;
        foreach ($array as $k => $v) {
            if ($k !== $i++) {
                return false;
            }
        }
        return true;
    }
}