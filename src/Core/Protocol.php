<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 21:54
 */

namespace Kilingzhang\QQ\Core;


interface Protocol
{
    public function __construct(string $url = '127.0.0.1:5700', string $access_token = '', string $secret = '');

    public function send($uri, $param, $method);

}