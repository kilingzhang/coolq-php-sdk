<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 22:19
 */

namespace CoolQSDK\Plugin;


use CoolQSDK\CoolQ;
use CoolQSDK\CoolQSubject;

class TulingPlugin extends BasePlugin
{


    public function update(CoolQ $coolQ)
    {

    }

    public function message(CoolQ $coolQ)
    {
        // TODO: Implement message() method.

        $content = $coolQ->getContent();

        $coolQ->sendPrivateMsg($content['user_id'], $content['message']);

        $this->setIntercept();
    }

    public function event(CoolQ $coolQ)
    {
        // TODO: Implement event() method.
    }

    public function request(CoolQ $coolQ)
    {
        // TODO: Implement request() method.
    }

    public function other(CoolQ $coolQ)
    {
        // TODO: Implement other() method.
    }
}