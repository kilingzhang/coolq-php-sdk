<?php
/**
 *                   _oo8oo_
 *                  o8888888o
 *                  88" . "88
 *                  (| -_- |)
 *                  0\  =  /0
 *                ___/'==='\___
 *              .' \\|     |// '.
 *             / \\|||  :  |||// \
 *            / _||||| -:- |||||_ \
 *            |   | \\\  -  /// |   |
 *            | \_|  ''\---/''  |_/ |
 *           \  .-\__  '-'  __/-.  /
 *         ___'. .'  /--.--\  '. .'___
 *       ."" '<  '.___\_<|>_/___.'  >' "".
 *     | | :  `- \`.:`\ _ /`:.`/ -`  : | |
 *     \  \ `-.   \_ __\ /__ _/   .-` /  /
 *  =====`-.____`.___ \_____/ ___.`____.-`=====
 *                   `=---=`
 *            佛祖保佑         永无bug
 *            Created by PhpStorm.
 *               User: kilingzhang
 *               Date: 18-3-23
 *               Time: 下午4:00
 */

namespace CoolQSDK\Plugin;


use CoolQSDK\Support\Log;
use CoolQSDK\Support\Time;

abstract class BasePlugin implements PluginObserver
{
    public $Intercept;
    private $startTime;
    public $coolQ = null;

    abstract public function PluginName();

    public function __construct()
    {
        $this->startTime = Time::getMicrotime();

        $this->Intercept = false;

        if (isset($this->coolQ)) {
            $this->coolQ->block = $this->Intercept;
        }


    }


    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        Log::info($this->PluginName() . ' 共耗时：' . Time::ComMicritime($this->startTime, Time::getMicrotime()) . '秒' . '--------------END------------');
    }


    /**
     * @param bool $bool
     */
    public function setIntercept($bool = true)
    {
        $this->Intercept = $bool;

        if (isset($this->coolQ)) {
            $this->coolQ->block = $this->Intercept;
        }
    }

    /**
     * @return bool
     */
    public function isIntercept()
    {
        return $this->Intercept;
    }


}