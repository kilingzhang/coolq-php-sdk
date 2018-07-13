<?php
/**
 * @author Kilingzhang
 * @collaborator: kj415j45
 */

namespace CoolQSDK;


class CQ
{


    public static function at(string $QQ): string
    {
        return "[CQ:at,qq=$QQ]";
    }

    public static function face($id)
    {
        return "[CQ:face,id=$id] ";
    }

    public static function emoji($id)
    {
        return "[CQ:emoji,id=$id] ";
    }

    public static function bFace($id)
    {
        return "[CQ:bface,id=$id] ";
    }

    public static function sFace($id)
    {
        return "[CQ:sface,id=$id] ";
    }

    public static function image($url)
    {
        return "[CQ:image,file=$url] ";
    }

    public static function record($url)
    {
        return "[CQ:record,file=$url] ";
    }

    public static function rps($type)
    {
        return "[CQ:rps,type=$type] ";
    }

    public static function dice($type)
    {
        return "[CQ:dice,type=$type] ";
    }

    public static function shake()
    {
        return "[CQ:shake] ";
    }

    public static function anonymouse($ignore)
    {
        if ($ignore) {
            return "[CQ:anonymous,ignore=true] ";
        } else {
            return "[CQ:anonymous] ";
        }
    }

    public static function music($type, $id)
    {
        return "[CQ:music,type=$type,id=$id]";
    }

    public static function diyMusic($type = custom, $url, $audio, $title, $content, $image)
    {
        return "[CQ:music,type=custom,url=$url,audio=$audio,title=$title,content=$content,image=$image] ";
    }

    public static function share($url, $title, $content, $image)
    {
        return "[CQ:share,url=$url,title=$title,content=$content,image=$image] ";
    }

    public static function rich($url, $text)
    {
        return "[CQ:rich,url=$url,text=$text] ";
    }

    public static function filterCQAt($string)
    {
        return preg_replace('/\[CQ:at,qq=\d+\]/', '', $string);
    }

    public static function decodeHtml($message)
    {
        $message = preg_replace("/&amp;/", "&", $message);
        $message = preg_replace("/&#91;/", "[", $message);
        $message = preg_replace("/&#93;/", "]", $message);
        return $message;
    }


    public static function isAtMe($message, $QQ)
    {
        $cq = self::at($QQ);
        $pos = strpos($message, $cq);
        return $pos !== false ? true : false;
    }

}
