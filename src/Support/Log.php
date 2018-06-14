<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/13
 * Time: 0:08
 */

namespace CoolQSDK\Support;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    private static $loggerInstance = null;
    private static $loggerPath = null;
    private static $loggerName = null;
    private static $loggerId = null;

    private function __construct()
    {
        $logger = new Logger(getenv('APP_NAME','COOLQ'));
        try {
            $logger->pushHandler(new StreamHandler(getenv('LOG_PATH','./logs/coolq.log'), Logger::INFO));
        } catch (\Exception $e) {
        }
        self::$loggerInstance = $logger;
    }

    public static function getInstance($name)
    {
        if (self::$loggerInstance === null) {
            if (self::$loggerName === null) {
                throw  new \Exception('logger name must be not empty');
            }
            if (self::$loggerPath === null) {
                throw  new \Exception('logger path must be not empty');
            }
            new Log(self::$loggerName);
        }

        if (self::$loggerId == null) {
            self::$loggerId = Time::getMicrotime();
        }

        return self::$loggerInstance;
    }


    public static function info($message, $content = array())
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->info($msg . $message, $content);
        } catch (\Exception $e) {
        }
    }

    public static function error($message, $content = array())
    {
        $msg = self::$loggerId . ' | ';
        try {
            return self::getInstance(self::$loggerName)->error($msg . $message, $content);
        } catch (\Exception $e) {
        }

    }

    /**
     * @return null
     */
    public static function getLoggerPath()
    {
        return self::$loggerPath;
    }

    /**
     * @return null
     */
    public static function getLoggerName()
    {
        return self::$loggerName;
    }

    /**
     * @param null $loggerPath
     */
    public static function setLoggerPath($loggerPath)
    {
        if (self::$loggerId == null) {
            self::$loggerId = Time::getMicrotime();
        }
        self::$loggerPath = $loggerPath;
    }

    /**
     * @param null $loggerName
     */
    public static function setLoggerName($loggerName)
    {
        if (self::$loggerId == null) {
            self::$loggerId = Time::getMicrotime();
        }
        self::$loggerName = $loggerName;
    }


}