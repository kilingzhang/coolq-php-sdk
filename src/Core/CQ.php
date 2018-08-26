<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 20:57
 */

namespace Kilingzhang\QQ\Core;


interface CQ
{
    public static function at(int $userId);

    public static function atAll();

    public static function face(int $id);

    public static function emoji(int $id);

    public static function bFace(int $id);

    public static function sFace(int $id);

    public static function magic(string $magic);

    public static function image(string $url);

    public static function flash(string $url);

    public static function record(string $url, bool $isMagic = false);

    /**
     * 发送猜拳魔法表情
     * @param string $type
     * @return mixed
     */
    public static function rps(string $type);

    /**
     * 发送掷骰子魔法表情
     * @param string $type
     * @return mixed
     */
    public static function dice(string $type);

    public static function shake();

    /**
     * 匿名发消息
     * 本CQ码需加在消息的开头。
     * 当{1}为true时，代表不强制使用匿名，如果匿名失败将转为普通消息发送。
     * 当{1}为false或ignore参数被忽略时，代表强制使用匿名，如果匿名失败将取消该消息的发送。
     * @param bool $ignore
     * @return mixed
     */
    public static function anonymouse(bool $ignore);

    public static function music(string $type, int $id);

    public static function diyMusic(string $url, string $audio, string $title, string $content, string $image);

    public static function share(string $url, string $title, string $content, string $image);

    public static function rich(string $url, string $text);

    public static function filterCQAt(string $string);

    public static function decodeHtml(string $message);

    public static function isAtMe(string $message, $userId);

}