<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 15:53
 */

namespace CoolQSDK\Tests;

use CoolQSDK\CoolQ;
use CoolQSDK\Response;

include __DIR__ . '/../vendor/autoload.php';


$CoolQ = new  CoolQ('127.0.0.1:5700', '', '');
//194233857
//1353693508
echo $CoolQ->get_cookies(194233857, 1353693508);
