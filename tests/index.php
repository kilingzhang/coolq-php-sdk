<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 15:53
 */
namespace CoolQSDK\Tests;
use CoolQSDK\CoolQ;

include __DIR__.'/../vendor/autoload.php';



$CoolQ = new  CoolQ('127.0.0.1',5700);

//unset($message);
//$message[0]['type'] = 'text';
//$message[0]['data'] = array(
//    'text'=>'1231145646'
//);
//
echo $CoolQ->getGroupList();
