<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 15:53
 */

namespace CoolQSDK\Tests;


require_once __DIR__ . '/../vendor/autoload.php';
// 报告所有错误
error_reporting(E_ALL);

$CoolQ = new  CoolQ('127.0.0.1:5700', 'kilingzhang', 'kilingzhang',false);

//$CoolQ->setReturnFormat('array');
//194233857
//1353693508
//echo $CoolQ->sendPrivateMsg(1353693508, 194233857, false, true);


echo $CoolQ->sendPrivateMsg(1353693508, 'Ni Mei De! SB', false, true);
