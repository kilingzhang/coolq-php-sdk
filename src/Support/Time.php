<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/12
 * Time: 23:56
 */

namespace CoolQSDK\Support;


class Time
{
    public static function getMicrotime()
    {
        $microtime = preg_replace('/\./', '', microtime(true));
        $len = strlen($microtime);
        for ($i = $len; $i < 14; $i++) {
            $microtime .= '0';
        }
        return $microtime;
    }

    public static function ComMicritime($start, $end, $len = 4)
    {
        return (substr($end, $len) - substr($start, $len)) / 10000;
    }
}