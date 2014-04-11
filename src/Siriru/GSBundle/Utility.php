<?php

namespace Siriru\GSBundle;

class Utility
{
    public static function separateFloat($float)
    {
        $parts        = explode('.', (string)$float);
        $whole_number = $parts[0];
        $minute = (int)($whole_number/60) <= 10 ? '0'.(int)($whole_number/60) : (int)($whole_number/60);
        $seconde = $whole_number%60 <= 10 ? '0'.$whole_number%60 : $whole_number%60;

        $after_the_float = array_key_exists(1, $parts) ? $parts[1] : '00';
        return $minute.':'.$seconde.':'.$after_the_float;
    }
}
