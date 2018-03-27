<?php
/**
 * @author Kilingzhang
 * @collaborator: kj415j45
 */

namespace CoolQSDK;


class CQ
{
    public static function At($QQ)
    {
        return "[CQ:at,qq=$QQ]";
    }

    public static function Face($id)
    {
        return "[CQ:face,id=$id] ";
    }

    public static function Emoji($id)
    {
        return "[CQ:emoji,id=$id] ";
    }

    public static function Bface($id)
    {
        return "[CQ:bface,id=$id] ";
    }

    public static function Sface($id)
    {
        return "[CQ:sface,id=$id] ";
    }

    public static function Image($url)
    {
        return "[CQ:image,file=$url] ";
    }

    public static function Record($url)
    {
        return "[CQ:record,file=$url] ";
    }

    public static function Rps($type)
    {
        return "[CQ:rps,type=$type] ";
    }

    public static function Dice($type)
    {
        return "[CQ:dice,type=$type] ";
    }

    public static function Shake()
    {
        return "[CQ:shake] ";
    }

    public static function Anonymouse($ignore)
    {
        if ($ignore) {
            return "[CQ:anonymous,ignore=true] ";
        } else {
            return "[CQ:anonymous] ";
        }
    }

    public static function Music($type, $id)
    {
        return "[CQ:music,type=$type,id=$id]";
    }

    public static function DiyMusic($type = custom, $url, $audio, $title, $content, $image)
    {
        return "[CQ:music,type=custom,url=$url,audio=$audio,title=$title,content=$content,image=$image] ";
    }

    public static function Share($url, $title, $content, $image)
    {
        return "[CQ:share,url=$url,title=$title,content=$content,image=$image] ";
    }

    public static function FilterCQAt($string)
    {
        return preg_replace('/\[CQ:at,qq=\d+\]/','',$string);
    }

    public static function DecodeHtml($message)
    {
        $message = preg_replace("/&amp;/", "&", $message);
        $message = preg_replace("/&#91;/", "[", $message);
        $message = preg_replace("/&#93;/", "]", $message);
        return $message;
    }

}
