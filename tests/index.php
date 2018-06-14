<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 15:53
 */

namespace CoolQSDK\Tests;

use CoolQSDK\CoolQ;
use CoolQSDK\CQ;
use CoolQSDK\Plugin;
use CoolQSDK\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$CoolQ = new  CoolQ('127.0.0.1:5700', 'kilingzhang', 'kilingzhang');
//$CoolQ->setReturnFormat('array');
//194233857
//1353693508
//echo $CoolQ->sendPrivateMsg(1353693508, 194233857, false, true);
echo "<pre>";
var_dump(

    $CoolQ->sendPrivateMsg(1353693508, 194233857, false)

);
