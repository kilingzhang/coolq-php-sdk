<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 18-3-23
 * Time: 下午2:32
 */


use CoolQSDK\Tests\CoolQ;

include __DIR__ . '/../vendor/autoload.php';

$CoolQ = new  CoolQ('127.0.0.1:5700', 'kilingzhang', 'kilingzhang');

//$CoolQ->setReturnFormat('array');

echo $CoolQ->event();