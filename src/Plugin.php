<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 22:21
 */

namespace CoolQSDK;


use CoolQSDK\Plugin\BasePlugin;
use CoolQSDK\Plugin\TulingPlugin;

class Plugin
{




    public static function createFactory($name = 'Tuling'): BasePlugin
    {
        $plugin = new TulingPlugin();
        return $plugin;
    }


}