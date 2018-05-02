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

use CoolQSDK\Plugin\BasePlugin;
use CoolQSDK\Plugin\TulingPlugin;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;


class CoolQ
{
    private $host;
    private $token;
    private $secret = '';
    private $isSignature = true;
    private $isAsync = false;
    private static $client;
    private static $options;
    private $block = false;

    private static $returnFormat = 'string';

    private $content = array();
    private $postType;
    private $plugins = array();




    public function getContent()
    {
        return $this->content;
    }

    public function attach(BasePlugin $plugin)
    {
        $key = array_search($plugin, $this->plugins);
        if ($key === false) {
            $this->plugins[] = $plugin;
        }
    }

    public function detach(BasePlugin $plugin)
    {
        $key = array_search($plugin, $this->plugins);
        if ($key !== false) {
            unset($this->plugins[$key]);
        }
    }

    public function notify()
    {
        // TODO: Implement notify() method.
        foreach ($this->plugins as $plugin) {
            // 把本类对象传给观察者，以便观察者获取当前类对象的信息
            if (!$this->block) {
                switch ($this->postType) {
                    //收到消息
                    case 'message':
                        $plugin->message($this);
                        break;
                    //群、讨论组变动等非消息类事件
                    case 'event':
                        $plugin->event($this);
                        break;
                    //加好友请求、加群请求／邀请
                    case 'request':
                        $plugin->request($this);
                        break;
                    default:
                        $plugin->other($this);
                        break;
                }
            }
        }
        $this->block = false;
    }


    public function event()
    {

        $signature = self::server('HTTP_X_SIGNATURE');
        $signature = $signature['HTTP_X_SIGNATURE'] ? substr($signature['HTTP_X_SIGNATURE'], 5, strlen($signature['HTTP_X_SIGNATURE'])) : "";
        $content = self::put();
        if ($this->isSignature && !empty($signature) && (hash_hmac('sha1', \GuzzleHttp\json_encode($content, JSON_UNESCAPED_UNICODE), $this->secret) != $signature)) {
            //TODO sha1验证失败
            echo '{"block": true,"reply":"signature=false"}';
            return;
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
                            'anonymous_flag' => $content['anonymous_flag'],
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
                        //todo
                        //以后再说吧
                        break;
                }
                break;
            //群、讨论组变动等非消息类事件
            case 'event':
                $event = $content['event'];
                switch ($event) {
                    //群管理员变动
                    case "group_admin":

                        $this->content = [
                            'event' => $content['event'],
                            //"set"、"unset"	事件子类型，分别表示设置和取消管理员
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                        ];

                        break;
                    //群成员减少
                    case "group_decrease":

                        $this->content = [
                            'event' => $content['event'],
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
                            'event' => $content['event'],
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
                            'event' => $content['event'],
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
                            'event' => $content['event'],
                            'user_id' => $content['user_id'],
                        ];

                        break;
                }
                break;
            //加好友请求、加群请求／邀请
            case 'request':
                $request_type = $content['request_type'];
                switch ($request_type) {
                    case "friend":

                        $this->content = [
                            'request_type' => $content['request_type'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
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
                            'message' => $content['message'],
                            'flag' => $content['flag'],
                        ];

                        //{"block": true,"approve":true,"reason":"就是拒绝你 不行啊"}
                        break;
                }
                break;
            default:

                $this->content = $content;

                break;
        }

        $this->notify();

    }

    private function initAttach()
    {
        $this->attach(new TulingPlugin());
    }

    public function __construct($host = '127.0.0.1:5700', $token = '', $secret = '')
    {
        $this->host = $host;
        //TODO  匹配是否合法URI

        $this->token = $token;
        $this->secret = $secret;
        self::$options['headers'] = [
            'Authorization' => 'Token ' . $this->token
        ];
        self::$client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->host,
            // You can set any number of default request options.
            'timeout' => 10.0,
        ]);

        $this->initAttach();

    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return bool
     */
    public function isSignature()
    {
        return $this->isSignature;
    }

    /**
     * @param bool $isSignature
     */
    public function setIsSignature($isSignature)
    {
        $this->isSignature = $isSignature;
    }

    /**
     * @return bool
     */
    public function isAsync()
    {
        return $this->isAsync;
    }

    /**
     * @param bool $isAsync
     */
    public function setIsAsync($isAsync)
    {
        $this->isAsync = $isAsync;
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
    public static function setReturnFormat($returnFormat)
    {
        $formats = [
            'string',
            'array'
        ];
        if (in_array($returnFormat, $formats)) {
            self::$returnFormat = $returnFormat;
        }
    }

    public function sendPrivateMsg($user_id, $message, $auto_escape = false, $async = null)
    {
        if ((is_null($async) && $this->isAsync) || $async === true) {
            return $this->sendPrivateMsgAsync($user_id, $message, $auto_escape);
        }

        $uri = URL::send_private_msg;
        $param = [
            'user_id' => $user_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendPrivateMsgAsync($user_id, $message, $auto_escape = false)
    {
        $uri = URL::send_private_msg_async;
        $param = [
            'user_id' => $user_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendGroupMsg($group_id, $message, $auto_escape = false, $async = null)
    {
        if ((is_null($async) && $this->isAsync) || $async === true) {
            return $this->sendGroupMsgAsync($group_id, $message, $auto_escape);
        }

        $uri = URL::send_group_msg;
        $param = [
            'group_id' => $group_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendGroupMsgAsync($group_id, $message, $auto_escape = false)
    {
        $uri = URL::send_group_msg_async;
        $param = [
            'group_id' => $group_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendDiscussMsg($discuss_id, $message, $auto_escape = false, $async = null)
    {
        if ((is_null($async) && $this->isAsync) || $async === true) {
            return $this->sendDiscussMsgAsync($discuss_id, $message, $auto_escape);
        }

        $uri = URL::send_discuss_msg;
        $param = [
            'discuss_id' => $discuss_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendDiscussMsgAsync($discuss_id, $message, $auto_escape = false)
    {
        $uri = URL::send_discuss_msg_async;
        $param = [
            'discuss_id' => $discuss_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendMsg($message_type, $id, $message, $auto_escape = false, $async = null)
    {
        if ((is_null($async) && $this->isAsync) || $async === true) {
            return $this->sendMsgAsync($message_type, $id, $message, $auto_escape);
        }

        $uri = URL::send_msg;
        $param = [
            'message_type' => $message_type,
            'user_id' => $id,
            'group_id' => $id,
            'discuss_id' => $id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendMsgAsync($message_type, $id, $message, $auto_escape = false)
    {
        $uri = URL::send_msg;
        $param = [
            'message_type' => $message_type,
            'user_id' => $id,
            'group_id' => $id,
            'discuss_id' => $id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function deleteMsg($message_id)
    {
        $uri = URL::delete_msg;
        $param = [
            'message_id' => $message_id,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendLike($user_id, $times = 1)
    {
        $uri = URL::send_like;
        $param = [
            'user_id' => $user_id,
            'times' => $times,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupKick($group_id, $user_id, $reject_add_request = false)
    {
        $uri = URL::set_group_kick;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'reject_add_request' => $reject_add_request,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupBan($group_id, $user_id, $duration = 30 * 60)
    {
        $uri = URL::set_group_ban;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'duration' => $duration,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupAnonymousBan($group_id, $flag, $duration = 30 * 60)
    {
        $uri = URL::set_group_anonymous_ban;
        $param = [
            'group_id' => $group_id,
            'flag' => $flag,
            'duration' => $duration,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupWholeBan($group_id, $enable = true)
    {
        $uri = URL::set_group_whole_ban;
        $param = [
            'group_id' => $group_id,
            'enable' => $enable,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupAdmin($group_id, $user_id, $enable = true)
    {
        $uri = URL::set_group_admin;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'enable' => $enable,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupAnonymous($group_id, $enable = true)
    {
        $uri = URL::set_group_anonymous;
        $param = [
            'group_id' => $group_id,
            'enable' => $enable,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupCard($group_id, $user_id, $card = null)
    {
        $uri = URL::set_group_card;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'card' => $card,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupLeave($group_id, $is_dismiss = false)
    {
        $uri = URL::set_group_leave;
        $param = [
            'group_id' => $group_id,
            'is_dismiss' => $is_dismiss,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupSpecialTitle($group_id, $user_id, $special_title = null, $duration = -1)
    {
        $uri = URL::set_group_special_title;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'special_title' => $special_title,
            'duration' => $duration,
        ];
        return $this->CURL($uri, $param);
    }

    public function setDiscussLeave($discuss_id)
    {
        $uri = URL::set_discuss_leave;
        $param = [
            'discuss_id' => $discuss_id,
        ];
        return $this->CURL($uri, $param);
    }

    public function setFriendAddRequest($flag, $approve = true, $remark = '')
    {
        $uri = URL::set_friend_add_request;
        $param = [
            'flag' => $flag,
            'approve' => $approve,
            'remark' => $remark,
        ];
        return $this->CURL($uri, $param);
    }

    public function setGroupAddRequest($flag, $type, $approve = true, $reason = '')
    {
        $uri = URL::set_group_add_request;
        $param = [
            'flag' => $flag,
            'type' => $type,
            'approve' => $approve,
            'reason' => $reason,
        ];
        return $this->CURL($uri, $param);
    }

    public function getLoginInfo()
    {
        $uri = URL::get_login_info;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public function getStrangerInfo($user_id, $no_cache = false)
    {
        $uri = URL::get_stranger_info;
        $param = [
            'user_id' => $user_id,
            'no_cache' => $no_cache,
        ];
        return $this->CURL($uri, $param);
    }

    public function getGroupList()
    {
        $uri = URL::get_group_list;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public function getGroupMemberInfo($group_id, $user_id, $no_cache = false)
    {
        $uri = URL::get_group_member_info;
        $param = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'no_cache' => $no_cache,
        ];
        return $this->CURL($uri, $param);
    }

    public function getGroupMemberList($group_id)
    {
        $uri = URL::get_group_member_list;
        $param = [
            'group_id' => $group_id,
        ];
        return $this->CURL($uri, $param);
    }

    public function getCookies()
    {
        $uri = URL::get_cookies;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public function getCsrfToken()
    {
        $uri = URL::get_csrf_token;
        $param = [];
        return $this->CURL($uri, $param);
    }

    /**
     * @param $file 收到的语音文件名，如 0B38145AA44505000B38145AA4450500.silk
     * @param $out_format 要转换到的格式，目前支持 mp3、amr、wma、m4a、spx、ogg、wav、flac
     * @return string
     */
    public function getRecord($file, $out_format)
    {
        $uri = URL::get_record;
        $param = [
            'file' => $file,
            'out_format' => $out_format,
        ];
        return $this->CURL($uri, $param);
    }

    public function getStatus()
    {
        $uri = URL::get_status;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public function getVersionInfo()
    {
        $uri = URL::get_version_info;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public function setRestart()
    {
        $uri = URL::set_restart;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public function setRestartPlugin()
    {
        $uri = URL::set_restart_plugin;
        $param = [];
        return $this->CURL($uri, $param);
    }

    /**
     * @param string $data_dir 收到清理的目录名，支持 image、record、show、bface
     * @return string
     */
    public function cleanDataDir($data_dir = '')
    {
        $uri = URL::clean_data_dir;
        $param = [
            'data_dir' => $data_dir
        ];
        return $this->CURL($uri, $param);
    }

    public function __getFriendList()
    {
        $uri = URL::_get_friend_list;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public static function put($param = null, $json = false)
    {
        $get = null;
        $content = file_get_contents('php://input');
        $get = $content;
        !$json && $get = json_decode($content, true);
        return $get;
    }

    public static function server($param = null, $default = '')
    {
        $get = null;
        $param == null && $get = $_SERVER;
        if (is_array($param) && count($param) > 0) {
            foreach ($param as $item) {
                $get[$item] = isset($_SERVER[$item]) ? $_SERVER[$item] : $default;
            }
        } else if ($param != null) {
            $get[$param] = isset($_SERVER[$param]) ? $_SERVER[$param] : $default;
        }
        return $get;
    }

    public function CURL($uri = URL::get_version_info, $param = [], $method = 'GET')
    {
        try {
            $response = self::$client->request($method, $uri, array_merge(self::$options, [
                'query' => $param
            ]));
            if ($response->getStatusCode() == 200) {
                return Response::Ok($response->getBody());
            }
        } catch (ClientException $e) {
            //如果 http_errors 请求参数设置成true，在400级别的错误的时候将会抛出
            switch ($e->getCode()) {
                case 400:
                    return Response::NotFoundResourceError();
                    break;
                case 401:
                    //401 配置文件中已填写access_token 初始化CoolQ对象时未传值
                    return Response::AccessTokenNoneError();
                    break;
                case 403:
                    //403 验证access_token错误
                    return Response::AccessTokenError();
                    break;
                case 404:
                    return Response::NotFoundResourceError();
                    break;
                case 406:
                    return Response::ContentTypeError();
                    break;
                default:
                    return Response::Error([
                        'message' => $e->getMessage()
                    ]);
                    break;
            }
        } catch (RequestException $e) {
            //在发送网络错误(连接超时、DNS错误等)时，将会抛出 GuzzleHttp\Exception\RequestException 异常。
            //一般为coolq-http-api插件未开启 接口地址无法访问
            switch ($e->getCode()) {
                case 0:
                    return Response::PluginServerError();
                    break;
                default:
                    return Response::Error([
                        'message' => $e->getMessage()
                    ]);
                    break;
            }
        }

    }


}

