<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 15:53
 */

require_once '../Autoloader.php';

use CoolQSDK\CoolQSDK;

$CoolQ = new  CoolQSDK('127.0.0.1',5700,'slight');

//unset($message);
//$message[0]['type'] = 'text';
//$message[0]['data'] = array(
//    'text'=>'1231145646'
//);
//
//echo $CoolQ->sendGroupMsg(194233857,$message,false,true);
