<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 13:55
 */

namespace CoolQSDK;


class MsgTool
{
    public static function deCodeHtml($message)
    {
        $message = preg_replace("/&amp;/", "&", $message);
        $message = preg_replace("/&#91;/", "[", $message);
        $message = preg_replace("/&#93;/", "]", $message);
        return $message;
    }
}