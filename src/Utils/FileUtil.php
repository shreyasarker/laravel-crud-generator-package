<?php

namespace ShreyaSarker\LaraCrud\Utils;

class FileUtil
{
    public static function getFileName($name): string
    {
        return $name . '.php';
    }

    public static function cleanLastLineBreak($string): string
    {
        return rtrim($string, "\n");
    }
}