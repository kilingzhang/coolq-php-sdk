<?php
/**
 * @author Kilingzhang
 * @collaborator: kj415j45
 */

namespace Kilingzhang\QQ\CoolQ;


class CQ implements \Kilingzhang\QQ\Core\CQ
{
    public static function CQ($cq, $data)
    {
        $res[] = "CQ:{$cq}";
        foreach ($data as $key => $value) {
            $res[] = $key . '=' . $value;
        }
        $res = implode(',', $res);
        return "[{$res}]";
    }

    public static function at(int $userId)
    {
        return self::CQ('at', [
            'qq' => $userId
        ]);
    }

    public static function atAll()
    {
        return self::CQ('at', [
            'qq' => 'all'
        ]);
    }

    public static function face(int $id)
    {
        return self::CQ('face', [
            'id' => $id
        ]);
    }

    public static function emoji(int $id)
    {
        return self::CQ('emoji', [
            'id' => $id
        ]);
    }

    public static function bFace(int $id)
    {
        return self::CQ('bface', [
            'id' => $id
        ]);
    }

    public static function sFace(int $id)
    {
        return self::CQ('sface', [
            'id' => $id
        ]);
    }

    public static function magic(string $magic)
    {
        return '[]';
    }

    public static function image(string $url)
    {
        return self::CQ('image', [
            'file' => $url
        ]);
    }

    public static function flash(string $url)
    {
        return '[]';
    }

    public static function record(string $url, bool $isMagic = false)
    {
        return self::CQ('record', [
            'file' => $url,
            'magic' => $isMagic,
        ]);
    }

    public static function rps(string $type)
    {
        return self::CQ('rps', [
            'type' => $type
        ]);
    }

    public static function dice(string $type)
    {
        return self::CQ('dice', [
            'type' => $type
        ]);
    }

    public static function shake()
    {
        return self::CQ('shake', []);
    }

    public static function anonymouse(bool $ignore)
    {
        return self::CQ('anonymouse', $ignore ? [
            'ignore' => $ignore
        ] : []);
    }

    public static function music(string $type, int $id)
    {
        return self::CQ('music', [
            'type' => $type,
            'id' => $id,
        ]);
    }

    public static function diyMusic(string $url, string $audio, string $title, string $content, string $image)
    {
        return self::CQ('music', [
            'type' => 'custom',
            'audio' => $audio,
            'title' => $title,
            'content' => $content,
            'image' => $image,
        ]);
    }

    public static function share(string $url, string $title, string $content, string $image)
    {
        return self::CQ('share', [
            'url' => $url,
            'title' => $title,
            'content' => $content,
            'image' => $image,
        ]);
    }

    public static function rich(string $url, string $text)
    {
        return self::CQ('rich', [
            'url' => $url,
            'text' => $text
        ]);
    }


    public static function filterCQAt(string $string)
    {
        return preg_replace('/\[CQ:at,qq=\d+\]/', '', $string);
    }

    public static function decodeHtml(string $message)
    {
        $message = preg_replace("/&amp;/", "&", $message);
        $message = preg_replace("/&#91;/", "[", $message);
        $message = preg_replace("/&#93;/", "]", $message);
        return $message;
    }

    public static function isAtMe(string $message, $userId)
    {
        $cq = self::at($userId);
        $pos = strpos($message, $cq);
        return $pos !== false ? true : false;
    }
}
