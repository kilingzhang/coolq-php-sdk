<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 15:53
 */

namespace CoolQSDK\Tests;


use swoole_http_client;

require_once __DIR__ . '/../vendor/autoload.php';

//http
$CoolQ = new  CoolQ('127.0.0.1:5700', 'kilingzhang', 'kilingzhang',false);
//websocket
//$CoolQ = new  CoolQ('127.0.0.1:6700', 'kilingzhang', 'kilingzhang',true);
//$CoolQ->setIsAsync(true);
//$CoolQ->setReturnFormat('array');
//194233857
//1353693508
echo $CoolQ->sendPrivateMsg(1353693508, 194233857, false, true);
