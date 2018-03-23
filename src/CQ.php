<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 13:52
 */

namespace CoolQSDK;


class CQ
{
    public static function enAtCode($QQ)
    {
        return "[CQ:at,qq=$QQ]";
    }

    public static function enFaceCode($id)
    {
        return "[CQ:face,id=$id] ";
    }

    public static function enEmojiCode($id)
    {
        return "[CQ:emoji,id=$id] ";
    }

    public static function enBfaceCode($id)
    {
        return "[CQ:bface,id=$id] ";
    }

    public static function enSfaceCode($id)
    {
        return "[CQ:sface,id=$id] ";
    }

    public static function enImageCode($url)
    {
        return "[CQ:image,file=$url] ";
    }

    public static function enRecordCode($url)
    {
        return "[CQ:record,file=$url] ";
    }

    public static function enRpsCode($type)
    {
        return "[CQ:rps,type=$type] ";
    }

    public static function enDiceCode($type)
    {
        return "[CQ:dice,type=$type] ";
    }

    public static function enShakeCode()
    {
        return "[CQ:shake] ";
    }

    public static function enAnonymouseCode($ignore)
    {
        if ($ignore) {
            return "[CQ:anonymous,ignore=true] ";
        } else {
            return "[CQ:anonymous] ";
        }
    }

    public static function enMusicCode($type, $id)
    {
        return "[CQ:music,type=$type,id=$id]";
    }

    public static function enDiyMusicCode($type = custom, $url, $audio, $title, $content, $image)
    {
        return "[CQ:music,type=custom,url=$url,audio=$audio,title=$title,content=$content,image=$image] ";
    }

    public static function enShareCode($url, $title, $content, $image)
    {
        return "[CQ:share,url=$url,title=$title,content=$content,image=$image] ";
    }

    public static function filterCQAt($string)
    {
        return preg_replace('/\[CQ:at,qq=\d+\]/','',$string);
    }

    public static function deCodeHtml($message)
    {
        $message = preg_replace("/&amp;/", "&", $message);
        $message = preg_replace("/&#91;/", "[", $message);
        $message = preg_replace("/&#93;/", "]", $message);
        return $message;
    }

}