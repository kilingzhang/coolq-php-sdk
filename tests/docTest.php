<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 15:53
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH.'/Autoloader.php';
$CoolQ = new  \CoolQCreator\CoolQ('127.0.0.1',5700,'token');
echo $CoolQ->getLoginInfo();
