<?php

namespace app\modules\avalon\helpers;

class MyHelper
{
    public static function fPrint($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        exit;
    }
}
