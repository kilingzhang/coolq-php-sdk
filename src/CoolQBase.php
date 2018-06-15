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


abstract class CoolQBase
{
    private $host;
    private $token;
    private $secret = '';
    private $isSignature = true;
    private $isAsync = false;
    protected $content = array();
    protected $putParams = array();
    protected static $client;
    protected static $options;
    private static $returnFormat = 'string';


    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param array $putParams
     */
    public function setPutParams($putParams)
    {
        $this->putParams = $putParams;
    }

    /**
     * @return array
     */
    public function getPutParams()
    {
        return $this->putParams;
    }


    public function isHMAC()
    {
        $signature = self::server('HTTP_X_SIGNATURE');
        $signature = $signature['HTTP_X_SIGNATURE'] ? substr($signature['HTTP_X_SIGNATURE'], 5, strlen($signature['HTTP_X_SIGNATURE'])) : "";
        //TODO
        $this->putParams = self::put(true);
//        file_put_contents('./put.json', $content);
//        $content = json_decode(file_get_contents('./put.json'), true);
        if ($this->isSignature && !empty($signature) && (hash_hmac('sha1', \GuzzleHttp\json_encode($this->putParams, JSON_UNESCAPED_UNICODE), $this->secret) != $signature)) {
            //TODO sha1验证失败
            return false;
        }
        return true;
    }

    abstract function event();


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

    public static function put($isJson = false)
    {
        $content = file_get_contents('php://input');
        if ($isJson) {
            return $content;
        }
        return json_decode($content, true);
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

    abstract function CURL($uri = URL::get_version_info, $param = [], $method = 'GET');


}

