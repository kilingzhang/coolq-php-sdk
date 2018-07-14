<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 18-3-23
 * Time: 下午2:32
 */

namespace CoolQSDK\Tests;


include __DIR__ . '/../vendor/autoload.php';

//http
//$CoolQ = new  CoolQ('127.0.0.1:5700', 'kilingzhang', 'kilingzhang',false);
//websocket
$CoolQ = new  CoolQ('127.0.0.1:6700', 'kilingzhang', 'kilingzhang',true);
//$CoolQ->setIsAsync(true);
//$CoolQ->setReturnFormat('array');
$CoolQ->event();