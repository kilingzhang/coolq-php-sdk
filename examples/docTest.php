<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 15:53
 */

require_once '../Autoloader.php';

use CoolQ\CoolQ;

$CoolQ = new  CoolQ('127.0.0.1',5700,'token');
echo $CoolQ->getLoginInfo();
