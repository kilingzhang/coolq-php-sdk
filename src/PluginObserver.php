<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 23:21
 */

namespace CoolQSDK;


interface PluginObserver
{
    // 接收到通知的处理方法
    public function update(CoolQ $coolQ);

    public function message(CoolQ $coolQ);

    public function event(CoolQ $coolQ);

    public function request(CoolQ $coolQ);

    public function other(CoolQ $coolQ);

}