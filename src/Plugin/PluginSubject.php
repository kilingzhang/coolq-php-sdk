<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 23:20
 */

namespace CoolQSDK\Plugin;


use CoolQSDK\Plugin\BasePlugin;

interface PluginSubject
{
    // 添加/注册观察者
    public function attach(BasePlugin $observer);
    // 删除观察者
    public function detach(BasePlugin $observer);
    // 触发通知
    public function notify();
}