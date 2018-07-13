<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/16
 * Time: 0:50
 */

namespace CoolQSDK\Tests;


use CoolQSDK\Response;

class CoolQ extends \CoolQSDK\CoolQ
{

    public function beforeCurl($uri = '', $param = [])
    {

    }

    public function afterCurl($uri = '', $param = [], $response, $errorException)
    {

    }


    public function onSignature($isHMAC)
    {
        if (!$isHMAC) {
            $this->returnJsonApi(Response::signatureError());
        }
    }

    public function onMessage($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function onEvent($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function onNotice($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function onRequest($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function onOther($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }
}