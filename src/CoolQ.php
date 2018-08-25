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
 *
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 12:57
 */

namespace CoolQSDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;


abstract class CoolQ
{
    /**
     * HTTP 服务器监听的 IP HTTP 服务器监听的端口
     * 127.0.0.1:5700
     * @var string
     */
    private static $host;
    /**
     * API 访问 token，如果不为空，则会在接收到请求时验证 Authorization 请求头是否为 Token
     * @var string
     */
    private static $token;
    /**
     * 上报数据签名密钥，如果不为空，则会在 HTTP 上报时对 HTTP 正文进行 HMAC SHA1 哈希，使用 secret 的值作为密钥，计算出的哈希值放在上报的 X-Signature 请求头，例如 X-Signature:
     * @var string
     */
    private static $secret = '';
    /**
     * 是否开启验证X-Signature 请求头 (强烈建议开启)
     * @var bool
     */
    /**
     * 是否使用 websocket
     * @var bool
     */
    private static $useWs = false;
    private $isSignature = true;
    /**
     * 是否全局开始异步事件(当开启此设置时，全局无论是否使用异步函数均为异步处理)
     * @var bool
     */
    private $isAsync = false;
    /**
     * 上报事件上报的参数
     * @var array
     */
    private $putParams = array();
    /**
     * websocket api post 发送参数
     * @var array
     */
    private $pushParams = '';
    /**
     * @var null
     */
    private $response = null;
    /**
     *
     * @var \GuzzleHttp\Client 或 \swoole_http_client
     */
    private static $clientInstance;
    /**
     * @var \swoole_http_client
     */
    private static $eventClientInstance;
    /**
     * @var bool
     */
    private static $isContentEvent = false;
    /**
     * @var
     */
    private static $eventPutParams;
    /**
     * http 请求头参数
     * @var
     */
    private static $options;
    /**
     * 全局返回值类型(默认为json字符串 也可以设置数组类型 array)
     * @var string
     */
    private static $returnFormat = 'json';
    /**
     * @var bool
     */
    private $isWhiteList = false;
    /**
     * @var bool
     */
    private $isBlackList = true;
    /**
     * 白名单优先级高于黑名单 当开启白名单时，黑名单将失效
     * 私聊白名单
     * @var array
     */
    private $privateWhiteList = [];
    /**
     * 私聊黑名单
     * @var array
     */
    private $privateBlackList = [];
    /**
     * 群组白名单
     * @var array
     */
    private $groupWhiteList = [];
    /**
     * 群组黑名单
     * @var array
     */
    private $groupBlackList = [];
    /**
     * 讨论组白名单
     * @var array
     */
    private $discussWhiteList = [];
    /**
     * 讨论组黑名单
     * @var array
     */
    private $discussBlackList = [];


    private $postType = '';
    private $content = array();


    public function __construct($host = '127.0.0.1:5700', $token = '', $secret = '', $useWs = false)
    {

        if (substr(PHP_VERSION, 0, 1) != "7") die("PHP >=7 required.");
        if ($useWs && !extension_loaded("swoole")) {
            $this->returnJsonApi(Response::NotExitsSwoolError());
            die();
        }

        self::$host = $host;
        self::$useWs = $useWs;
        self::$token = $token;
        self::$secret = $secret;
        self::$clientInstance = self::getClient();

        if ($this->isUseWs()) {

            if ($useWs && !preg_match("/^cli$/i", php_sapi_name())) {
                $this->returnJsonApi(Response::NotBeCliError());
                die();
            }

            self::$eventClientInstance = self::getEventClientInstance();

            self::$clientInstance->on('message', function ($_cli, $frame) {
                $pushParams = $this->getPushParams();
                $this->afterCurl('/' . $pushParams['action'], $pushParams['params'], $_cli, null);
            });
            self::$clientInstance->upgrade('/api/', function ($cli) {
                self::$clientInstance = $cli;
            });

            self::$eventClientInstance->on('message', function ($cli, $frame) {
                self::$eventPutParams = $frame->data;
                $this->event();
            });
        }
    }

    public abstract function beforeCurl($uri = '', $param = []);

    public abstract function afterCurl($uri = '', $param = [], $response, $errorException);

    public abstract function beforEvent();

    public abstract function afterEvent();


    /**
     * @return string
     */
    public function getHost(): string
    {
        return self::$host;
    }

    /**
     * @return bool
     */
    public function isUseWs(): bool
    {
        return self::$useWs;
    }

    /**
     * @return Client
     */
    public static function getClient()
    {
        if (self::$clientInstance == null) {
            if (self::$useWs == false) {
                self::$options['headers'] = [
                    'Authorization' => 'Token ' . self::$token
                ];
                self::$clientInstance = new Client([
                    // Base URI is used with relative requests
                    'base_uri' => self::$host,
                    // You can set any number of default request options.
                    'timeout' => 10.0,
                ]);
            } else {
                $host = explode(':', self::$host);
                self::$clientInstance = new \swoole_http_client($host[0], $host[1]);
                self::$clientInstance->setHeaders([
                    'Authorization' => 'Token ' . self::$token
                ]);
                self::$clientInstance->set(['websocket_mask' => true]);

            }
        }
        return self::$clientInstance;
    }

    public static function getEventClientInstance()
    {
        if (self::$eventClientInstance == null) {
            $host = explode(':', self::$host);
            self::$eventClientInstance = new \swoole_http_client($host[0], $host[1]);

            self::$eventClientInstance->setHeaders([
                'Authorization' => 'Token ' . self::$token
            ]);
            self::$eventClientInstance->set(['websocket_mask' => true]);

        }
        return self::$eventClientInstance;
    }

    /**
     * @return mixed
     */
    public static function getOptions(): array
    {
        return self::$options;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return self::$token;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return self::$secret;
    }

    /**
     * @return bool
     */
    public function isSignature(): bool
    {
        return $this->isSignature;
    }

    /**
     * @param bool $isSignature
     */
    public function setIsSignature(bool $isSignature)
    {
        $this->isSignature = $isSignature;
    }

    /**
     * @return bool
     */
    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    /**
     * @param bool $isAsync
     */
    public function setIsAsync(bool $isAsync)
    {
        $this->isAsync = $isAsync;
    }

    public function setPutParams($putParams)
    {
        $this->putParams = $putParams;
    }

    /**
     * @return array
     */
    public function getPutParams(): array
    {

        if ($this->isUseWs()) {
            $this->putParams = json_decode(self::$eventPutParams, true);
        } else {
            $this->putParams = $this->put();
        }

        if (!$this->isUseWs() && !empty($this->putParams)) {
            file_put_contents('./send_private_msg.json', json_encode($this->putParams, JSON_UNESCAPED_UNICODE));
        }

        if (!$this->isUseWs() && file_exists('./send_private_msg.json')) {
            $this->putParams = json_decode(file_get_contents('./send_private_msg.json'), true);
        }

        if (empty($this->putParams)) {
            $this->returnJsonApi(Response::eventMissParamsError());
        }
        return $this->putParams;
    }

    /**
     * @return array
     */
    public function getPushParams(): array
    {
        return json_decode(empty($this->pushParams) ? '[]' : $this->pushParams, true);
    }

    /**
     * @return string
     */
    public static function getReturnFormat(): string
    {
        return self::$returnFormat;
    }

    /**
     * @param string $returnFormat
     */
    public static function setReturnFormat(string $returnFormat)
    {
        $formats = [
            'json',
            'array'
        ];
        if (in_array($returnFormat, $formats)) {
            self::$returnFormat = $returnFormat;
        }
    }

    /**
     * @return string
     */
    public function getPostType(): string
    {
        return $this->postType;
    }

    /**
     * @param string $postType
     */
    public function setPostType(string $postType)
    {
        $this->postType = $postType;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array $content
     */
    public function setContent(array $content)
    {
        $this->content = $content;
    }

    /**
     * @return bool
     */
    public function isWhiteList(): bool
    {
        return $this->isWhiteList;
    }

    /**
     * @param bool $isWhiteList
     */
    public function setIsWhiteList(bool $isWhiteList)
    {
        $this->isWhiteList = $isWhiteList;
    }

    /**
     * @return bool
     */
    public function isBlackList(): bool
    {
        return $this->isBlackList;
    }

    /**
     * @param bool $isBlackList
     */
    public function setIsBlackList(bool $isBlackList)
    {
        $this->isBlackList = $isBlackList;
    }

    /**
     * @return array
     */
    public function getPrivateWhiteList(): array
    {
        return $this->privateWhiteList;
    }

    /**
     * @param array $privateWhiteList
     */
    public function setPrivateWhiteList(array $privateWhiteList)
    {
        $this->privateWhiteList = $privateWhiteList;
    }

    /**
     * @return array
     */
    public function getPrivateBlackList(): array
    {
        return $this->privateBlackList;
    }

    /**
     * @param array $privateBlackList
     */
    public function setPrivateBlackList(array $privateBlackList)
    {
        $this->privateBlackList = $privateBlackList;
    }

    /**
     * @return array
     */
    public function getGroupWhiteList(): array
    {
        return $this->groupWhiteList;
    }

    /**
     * @param array $groupWhiteList
     */
    public function setGroupWhiteList(array $groupWhiteList)
    {
        $this->groupWhiteList = $groupWhiteList;
    }

    /**
     * @return array
     */
    public function getGroupBlackList(): array
    {
        return $this->groupBlackList;
    }

    /**
     * @param array $groupBlackList
     */
    public function setGroupBlackList(array $groupBlackList)
    {
        $this->groupBlackList = $groupBlackList;
    }

    /**
     * @return array
     */
    public function getDiscussWhiteList(): array
    {
        return $this->discussWhiteList;
    }

    /**
     * @param array $discussWhiteList
     */
    public function setDiscussWhiteList(array $discussWhiteList)
    {
        $this->discussWhiteList = $discussWhiteList;
    }

    /**
     * @return array
     */
    public function getDiscussBlackList(): array
    {
        return $this->discussBlackList;
    }

    /**
     * @param array $discussBlackList
     */
    public function setDiscussBlackList(array $discussBlackList)
    {
        $this->discussBlackList = $discussBlackList;
    }

    /**
     *
     * @param $response
     */
    public function returnJsonApi($response)
    {
        switch (CoolQ::getReturnFormat()) {
            case "json":
                break;
            case "array":
                $response = json_encode($response, JSON_UNESCAPED_UNICODE);
                break;
            default:
                $response = 'Not Found Format';
                break;
        }
        echo $response;
        //websocket 禁止退出
        if (!$this->isUseWs()) {
            exit();
        }
    }

    private function put(bool $isJson = false)
    {
        $content = file_get_contents('php://input');
        if ($isJson) {
            return $content;
        }
        $content = json_decode($content, true);
        return empty($content) ? [] : $content;
    }

    private function server(array $param = array(), string $default = ''): array
    {
        $get = null;
        empty($param) && $get = $_SERVER;
        if (is_array($param) && count($param) > 0) {
            foreach ($param as $item) {
                $get[$item] = isset($_SERVER[$item]) ? $_SERVER[$item] : $default;
            }
        } else if ($param != null) {
            $get[$param] = isset($_SERVER[$param]) ? $_SERVER[$param] : $default;
        }
        return $get;
    }

    public function isHMAC(): bool
    {
        $signature = $this->server(['HTTP_X_SIGNATURE']);
        $signature = $signature['HTTP_X_SIGNATURE'] ? substr($signature['HTTP_X_SIGNATURE'], 5, strlen($signature['HTTP_X_SIGNATURE'])) : "";
        $putParams = $this->getPutParams();
        if ($this->isSignature && !empty($signature) && (hash_hmac('sha1', \GuzzleHttp\json_encode($putParams, JSON_UNESCAPED_UNICODE), self::$secret) != $signature)) {
            //sha1验证失败
            return false;
        }
        return true;
    }

    public function event()
    {

        if (!self::$isContentEvent && $this->isUseWs()) {
            self::$eventClientInstance->upgrade('/event/', function ($cli) {
                self::$isContentEvent = true;
            });
            return;
        }

        $this->beforEvent();

        $isHMAC = $this->isHMAC();

        $this->onSignature($isHMAC);

        $content = $this->getPutParams();

        if (empty($content)) {
            $this->returnJsonApi(Response::eventMissParamsError());
        }

        $this->postType = $content['post_type'];
        switch ($this->postType) {
            //收到消息
            case 'message':
                $message_type = $content['message_type'];
                switch ($message_type) {
                    //私聊消息
                    case "private":

                        $this->content = [
                            'message_type' => $content['message_type'],
                            'message_id' => $content['message_id'],
                            'font' => $content['font'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
                            //消息子类型，如果是好友则是 "friend"，
                            //如果从群或讨论组来的临时会话则分别是 "group"、"discuss"
                            //"friend"、"group"、"discuss"、"other"
                            'sub_type' => $content['sub_type'],
                        ];

                        break;
                    //群消息
                    case "group":

                        $this->content = [
                            'message_type' => $content['message_type'],
                            'message_id' => $content['message_id'],
                            'font' => $content['font'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
                            'group_id' => $content['group_id'],
                            //匿名用户显示名
                            'anonymous' => $content['anonymous'],
                            //匿名用户 flag，在调用禁言 API 时需要传入
                            'anonymous_flag' => empty($content['anonymous']['flag']) ? '' : $content['anonymous']['flag'],
                        ];

                        // {"reply":"message","block": true,"at_sender":true,"kick":false,"ban":false}


                        break;
                    //讨论组消息
                    case "discuss":

                        $this->content = [
                            'message_type' => $content['message_type'],
                            'message_id' => $content['message_id'],
                            'font' => $content['font'],
                            'discuss_id' => $content['discuss_id'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
                        ];

                        // {"reply":"message","block": true,"at_sender":true}
                        break;
                }
                $this->onMessage($this->content);
                break;
            //群、讨论组变动等非消息类事件
            case 'notice': //兼容4.0
            case 'event':
                $event = empty($content['event']) ? $content['notice_type'] : $content['event'];//兼容4.0
                switch ($event) {
                    //群管理员变动
                    case "group_admin":

                        $this->content = [
                            'event' =>  empty($content['event']) ? $content['notice_type'] : $content['event'],
                            //"set"、"unset"	事件子类型，分别表示设置和取消管理员
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                        ];

                        break;
                    //群成员减少
                    case "group_decrease":

                        $this->content = [
                            'event' =>  empty($content['event']) ? $content['notice_type'] : $content['event'],
                            //"leave"、"kick"、"kick_me"	事件子类型，分别表示主动退群、成员被踢、登录号被踢
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                            'operator_id' => $content['operator_id'],
                        ];

                        break;
                    //群成员增加
                    case "group_increase":

                        $this->content = [
                            'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                            //"approve"、"invite"	事件子类型，分别表示管理员已同意入群、管理员邀请入群
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                            'operator_id' => $content['operator_id'],
                        ];


                        break;
                    //群文件上传
                    case "group_upload":


                        $this->content = [
                            'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                            #字段名	数据类型	说明
                            #id	string	文件 ID
                            #name	string	文件名
                            #size	number	文件大小（字节数）
                            #busid	number	busid（目前不清楚有什么作用）
                            'file' => $content['file'],
                        ];

                        break;
                    //好友添加
                    case "friend_added":

                        $this->content = [
                            'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                            'user_id' => $content['user_id'],
                        ];

                        break;
                }
                $this->onEvent($this->content);
                break;
            //加好友请求、加群请求／邀请
            case 'request':
                $request_type = $content['request_type'];
                switch ($request_type) {
                    case "friend":

                        $this->content = [
                            'request_type' => $content['request_type'],
                            'user_id' => $content['user_id'],
                            'message' => empty($content['message']) ? $content['comment'] : $content['message'],//兼容4.0
                            'flag' => $content['flag'],
                        ];

                        //{"block": true,"approve":true,"reason":"就是拒绝你 不行啊"}
                        break;
                    case "group":

                        $this->content = [
                            'request_type' => $content['request_type'],
                            //"add"、"invite"	请求子类型，分别表示加群请求、邀请登录号入群
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                            'message' => empty($content['message']) ? $content['comment'] : $content['message'],//兼容4.0
                            'flag' => $content['flag'],
                        ];

                        //{"block": true,"approve":true,"reason":"就是拒绝你 不行啊"}
                        break;
                }
                $this->onRequest($this->content);
                break;
            default:
                $this->content = $content;
                $this->onOther($this->content);
                break;
        }

        $this->afterEvent();

    }

    public abstract function onSignature($isHMAC);

    public abstract function onMessage($content);

    public abstract function onEvent($content);

    public abstract function onRequest($content);

    public abstract function onOther($content);


    public function sendPrivateMsg(int $user_id, string $message, bool $auto_escape = false, bool $async = null)
    {
        if ($this->isWhiteList() && !in_array($user_id, $this->getPrivateWhiteList())) {
            return Response::banAccountError();
        }

        if (!$this->isWhiteList() && $this->isBlackList() && in_array($user_id, $this->getPrivateBlackList())) {
            return Response::banAccountError();
        }

        if ((is_null($async) && $this->isAsync) || $async === true) {
            return $this->sendPrivateMsgAsync($user_id, $message, $auto_escape);
        }

        $uri = Url::send_private_msg;
        $param = [
            'user_id' => $user_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->curl($uri, $param);
    }

    public function sendPrivateMsgAsync(int $user_id, string $message, bool $auto_escape = false)
    {
        if ($this->isWhiteList() && !in_array($user_id, $this->getPrivateWhiteList())) {
            return Response::banAccountError();
        }

        if (!$this->isWhiteList() && $this->isBlackList() && in_array($user_id, $this->getPrivateBlackList())) {
            return Response::banAccountError();
        }

        $uri = Url::send_private_msg_async;
        $param = [
            'user_id' => $user_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->curl($uri, $param);
    }

    public function sendGroupMsg(int $group_id, string $message, bool $auto_escape = false, bool $async = null)
    {
        if ($this->isWhiteList() && !in_array($group_id, $this->getGroupWhiteList())) {
            return Response::banAccountError();
        }

        if (!$this->isWhiteList() && $this->isBlackList() && in_array($group_id, $this->getGroupBlackList())) {
            return Response::banAccountError();
        }

        if ((is_null($async) && $this->isAsync) || $async === true) {
            return $this->sendGroupMsgAsync($group_id, $message, $auto_escape);
        }

        $uri = Url::send_group_msg;
        $param = [
            'group_id' => $group_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->curl($uri, $param);
    }

    public function sendGroupMsgAsync(int $group_id, string $message, bool $auto_escape = false)
    {
        if ($this->isWhiteList() && !in_array($group_id, $this->getGroupWhiteList())) {
            return Response::banAccountError();
        }

        if (!$this->isWhiteList() && $this->isBlackList() && in_array($group_id, $this->getGroupBlackList())) {
            return Response::banAccountError();
        }

        $uri = Url::send_group_msg_async;
        $param = [
            'group_id' => $group_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->curl($uri, $param);
    }

    public function sendDiscussMsg(int $discuss_id, string $message, bool $auto_escape = false, bool $async = null)
    {
        if ($this->isWhiteList() && !in_array($discuss_id, $this->getDiscussWhiteList())) {
            return Response::banAccountError();
        }

        if (!$this->isWhiteList() && $this->isBlackList() && in_array($discuss_id, $this->getDiscussBlackList())) {
            return Response::banAccountError();
        }

        if ((is_null($async) && $this->isAsync) || $async === true) {
            return $this->sendDiscussMsgAsync($discuss_id, $message, $auto_escape);
        }

        $uri = Url::send_discuss_msg;
        $param = [
            'discuss_id' => $discuss_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->curl($uri, $param);
    }

    public function sendDiscussMsgAsync(int $discuss_id, string $message, bool $auto_escape = false)
    {
        if ($this->isWhiteList() && !in_array($discuss_id, $this->getDiscussWhiteList())) {
            return Response::banAccountError();
        }

        if ($this->isBlackList() && in_array($discuss_id, $this->getDiscussBlackList())) {
            return Response::banAccountError();
        }

        $uri = Url::send_discuss_msg_async;
        $param = [
            'discuss_id' => $discuss_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->curl($uri, $param);
    }

    public function sendMsg(string $message_type, int $id, string $message, bool $auto_escape = false, bool $async = null)
    {
        switch ($message_type) {
            case 'private':
                if ($this->isWhiteList() && !in_array($id, $this->getPrivateWhiteList())) {
                    return Response::banAccountError();
                }

                if ($this->isBlackList() && in_array($id, $this->getPrivateBlackList())) {
                    return Response::banAccountError();
                }
                break;
            case 'group':
                if ($this->isWhiteList() && !in_array($id, $this->getGroupWhiteList())) {
                    return Response::banAccountError();
                }

                if ($this->isBlackList() && in_array($id, $this->getGroupBlackList())) {
                    return Response::banAccountError();
                }
                break;
            case 'discuss':
                if ($this->isWhiteList() && !in_array($id, $this->getDiscussWhiteList())) {
                    return Response::banAccountError();
                }

                if ($this->isBlackList() && in_array($id, $this->getDiscussBlackList())) {
                    return Response::banAccountError();
                }
                break;
            default:
                break;
        }

        if ((is_null($async) && $this->isAsync) || $async === true) {
            return $this->sendMsgAsync($message_type, $id, $message, $auto_escape);
        }

        $uri = Url::send_msg;
        $param = [
            'message_type' => $message_type,
            'user_id' => $id,
            'group_id' => $id,
            'discuss_id' => $id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->curl($uri, $param);
    }

    public function sendMsgAsync(string $message_type, int $id, string $message, bool $auto_escape = false)
    {
        switch ($message_type) {
            case 'private':
                if ($this->isWhiteList() && !in_array($id, $this->getPrivateWhiteList())) {
                    return Response::banAccountError();
                }

                if ($this->isBlackList() && in_array($id, $this->getPrivateBlackList())) {
                    return Response::banAccountError();
                }
                break;
            case 'group':
                if ($this->isWhiteList() && !in_array($id, $this->getGroupWhiteList())) {
                    return Response::banAccountError();
                }

                if ($this->isBlackList() && in_array($id, $this->getGroupBlackList())) {
                    return Response::banAccountError();
                }
                break;
            case 'discuss':
                if ($this->isWhiteList() && !in_array($id, $this->getDiscussWhiteList())) {
                    return Response::banAccountError();
                }

                if ($this->isBlackList() && in_array($id, $this->getDiscussBlackList())) {
                    return Response::banAccountError();
                }
                break;
            default:
                break;
        }

        $uri = Url::send_msg;
        $param = [
            'message_type' => $message_type,
            'user_id' => $id,
            'group_id' => $id,
            'discuss_id' => $id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->curl($uri, $param);
    }

    public function deleteMsg(int $message_id)
    {
        $uri = Url::delete_msg;
        $param = [
            'message_id' => $message_id,
        ];
        return $this->curl($uri, $param);
    }

    public function sendLike(int $user_id, int $times = 1)
    {
        $uri = Url::send_like;
        $param = [
            'user_id' => $user_id,
            'times' => $times,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupKick(int $group_id, int $user_id, bool $reject_add_request = false)
    {
        $uri = Url::set_group_kick;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'reject_add_request' => $reject_add_request,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupBan(int $group_id, int $user_id, int $duration = 30 * 60)
    {
        $uri = Url::set_group_ban;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'duration' => $duration,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupAnonymousBan(int $group_id, string $flag, int $duration = 30 * 60)
    {
        $uri = Url::set_group_anonymous_ban;
        $param = [
            'group_id' => $group_id,
            'flag' => $flag,
            'duration' => $duration,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupWholeBan(int $group_id, bool $enable = true)
    {
        $uri = Url::set_group_whole_ban;
        $param = [
            'group_id' => $group_id,
            'enable' => $enable,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupAdmin(int $group_id, int $user_id, bool $enable = true)
    {
        $uri = Url::set_group_admin;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'enable' => $enable,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupAnonymous(int $group_id, bool $enable = true)
    {
        $uri = Url::set_group_anonymous;
        $param = [
            'group_id' => $group_id,
            'enable' => $enable,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupCard(int $group_id, int $user_id, string $card = null)
    {
        $uri = Url::set_group_card;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'card' => $card,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupLeave(int $group_id, bool $is_dismiss = false)
    {
        $uri = Url::set_group_leave;
        $param = [
            'group_id' => $group_id,
            'is_dismiss' => $is_dismiss,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupSpecialTitle(int $group_id, int $user_id, string $special_title = null, int $duration = -1)
    {
        $uri = Url::set_group_special_title;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'special_title' => $special_title,
            'duration' => $duration,
        ];
        return $this->curl($uri, $param);
    }

    public function setDiscussLeave(int $discuss_id)
    {
        $uri = Url::set_discuss_leave;
        $param = [
            'discuss_id' => $discuss_id,
        ];
        return $this->curl($uri, $param);
    }

    public function setFriendAddRequest(string $flag, bool $approve = true, string $remark = '')
    {
        $uri = Url::set_friend_add_request;
        $param = [
            'flag' => $flag,
            'approve' => $approve,
            'remark' => $remark,
        ];
        return $this->curl($uri, $param);
    }

    public function setGroupAddRequest(string $flag, string $type, bool $approve = true, string $reason = '')
    {
        $uri = Url::set_group_add_request;
        $param = [
            'flag' => $flag,
            'type' => $type,
            'approve' => $approve,
            'reason' => $reason,
        ];
        return $this->curl($uri, $param);
    }

    public function getLoginInfo()
    {
        $uri = Url::get_login_info;
        $param = [];
        return $this->curl($uri, $param);
    }

    public function getStrangerInfo(int $user_id, bool $no_cache = false)
    {
        $uri = Url::get_stranger_info;
        $param = [
            'user_id' => $user_id,
            'no_cache' => $no_cache,
        ];
        return $this->curl($uri, $param);
    }

    public function getGroupList()
    {
        $uri = Url::get_group_list;
        $param = [];
        return $this->curl($uri, $param);
    }

    public function getGroupMemberInfo(int $group_id, int $user_id, bool $no_cache = false)
    {
        $uri = Url::get_group_member_info;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'no_cache' => $no_cache,
        ];
        return $this->curl($uri, $param);
    }

    public function getGroupMemberList(int $group_id)
    {
        $uri = Url::get_group_member_list;
        $param = [
            'group_id' => $group_id,
        ];
        return $this->curl($uri, $param);
    }

    public function getCookies()
    {
        $uri = Url::get_cookies;
        $param = [];
        return $this->curl($uri, $param);
    }

    public function getCsrfToken()
    {
        $uri = Url::get_csrf_token;
        $param = [];
        return $this->curl($uri, $param);
    }

    /**
     * @param $file 收到的语音文件名，如 0B38145AA44505000B38145AA4450500.silk
     * @param $out_format 要转换到的格式，目前支持 mp3、amr、wma、m4a、spx、ogg、wav、flac
     * @return string
     */
    public function getRecord(string $file, string $out_format)
    {
        $uri = Url::get_record;
        $param = [
            'file' => $file,
            'out_format' => $out_format,
        ];
        return $this->curl($uri, $param);
    }

    public function getStatus()
    {
        $uri = Url::get_status;
        $param = [];
        return $this->curl($uri, $param);
    }

    public function getVersionInfo()
    {
        $uri = Url::get_version_info;
        $param = [];
        return $this->curl($uri, $param);
    }

    public function setRestart()
    {
        $uri = Url::set_restart;
        $param = [];
        return $this->curl($uri, $param);
    }

    public function setRestartPlugin()
    {
        $uri = Url::set_restart_plugin;
        $param = [];
        return $this->curl($uri, $param);
    }

    /**
     * @param string $data_dir 收到清理的目录名，支持 image、record、show、bface
     * @return string
     */
    public function cleanDataDir(string $data_dir = '')
    {
        $uri = Url::clean_data_dir;
        $param = [
            'data_dir' => $data_dir
        ];
        return $this->curl($uri, $param);
    }

    public function __getFriendList()
    {
        $uri = Url::_get_friend_list;
        $param = [];
        return $this->curl($uri, $param);
    }


    private function curlWs($uri = Url::get_version_info, $param = [], $method = 'GET')
    {

        //$uri: /send_private_msg -> action: send_private_msg
        $pushParams['action'] = substr($uri, 1);
        $pushParams['params'] = $param;
        $pushParams = json_encode($pushParams, JSON_UNESCAPED_UNICODE);
        $this->pushParams = $pushParams;

        $cli = self::getClient();
        $cli->push($this->pushParams);
        return;
    }

    private function curl($uri = Url::get_version_info, $param = [], $method = 'GET')
    {
        //curl before d/o
        $this->beforeCurl($uri, $param);

        if ($this->isUseWs() == true) {
            return $this->curlWs($uri, $param, $method);
        }

        try {

            $this->response = self::getClient()->request($method, $uri, array_merge(self::$options, [
                'query' => $param
            ]));

            $response = $this->response;

            //curl after do
            $this->afterCurl($uri, $param, $response, null);

            if ($response->getStatusCode() == 200) {
                $response = $response->getBody();
                return Response::ok($response);
            }

        } catch (ClientException $e) {
            $this->afterCurl($uri, $param, $this->response, $e);
            //如果 http_errors 请求参数设置成true，在400级别的错误的时候将会抛出
            switch ($e->getCode()) {
                case 400:
                    return Response::notFoundResourceError();
                    break;
                case 401:
                    //401 配置文件中已填写access_token 初始化CoolQ对象时未传值
                    return Response::accessTokenNoneError();
                    break;
                case 403:
                    //403 验证access_token错误
                    return Response::accessTokenError();
                    break;
                case 404:
                    return Response::notFoundResourceError();
                    break;
                case 406:
                    return Response::contentTypeError();
                    break;
                default:
                    return Response::error([
                        'message' => $e->getMessage()
                    ]);
                    break;
            }
        } catch (RequestException $e) {
            $this->afterCurl($uri, $param, $this->response, $e);
            //在发送网络错误(连接超时、DNS错误等)时，将会抛出 GuzzleHttp\Exception\RequestException 异常。
            //一般为coolq-http-api插件未开启 接口地址无法访问
            switch ($e->getCode()) {
                case 0:
                    return Response::pluginServerError(self::getClient());
                    break;
                default:
                    return Response::error([
                        'message' => $e->getMessage()
                    ]);
                    break;
            }
        } catch (GuzzleException $e) {
            $this->afterCurl($uri, $param, $this->response, $e);
        }

    }


}

