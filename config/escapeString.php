<?php
// escapeString.php

if (!function_exists('escapeString')) {
    function escapeString($text)
    {
        global $connect;
        if (!isset($connect) || !is_object($connect)) {
            throw new Exception('Database connection not available');
        }
        return $connect->real_escape_string($text);
    }
}
