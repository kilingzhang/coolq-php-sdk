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

    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array $putParams
     */
    public function setPutParams(array $putParams)
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

    public function isHMAC(): bool
    {
        $signature = self::server(['HTTP_X_SIGNATURE']);
        $signature = $signature['HTTP_X_SIGNATURE'] ? substr($signature['HTTP_X_SIGNATURE'], 5, strlen($signature['HTTP_X_SIGNATURE'])) : "";
        //TODO
        $this->putParams = self::put();
//        file_put_contents('./put.json', $content);
//        $content = json_decode(file_get_contents('./put.json'), true);
        if ($this->isSignature && !empty($signature) && (hash_hmac('sha1', \GuzzleHttp\json_encode($this->putParams, JSON_UNESCAPED_UNICODE), $this->secret) != $signature)) {
            //TODO sha1验证失败
            return false;
        }
        return true;
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
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret)
    {
        $this->secret = $secret;
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
            'string',
            'array'
        ];
        if (in_array($returnFormat, $formats)) {
            self::$returnFormat = $returnFormat;
        }
    }

    public function sendPrivateMsg(int $user_id, string $message, bool $auto_escape = false, bool $async = null)
    {
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

    public static function put(bool $isJson = false)
    {
        $content = file_get_contents('php://input');
        if ($isJson) {
            return $content;
        }
        return json_decode($content, true);
    }

    public static function server(array $param = array(), string $default = ''): array
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

    public function curl($uri = Url::get_version_info, $param = [], $method = 'GET')
    {
        try {

            $response = self::$client->request($method, $uri, array_merge(self::$options, [
                'query' => $param
            ]));
            if ($response->getStatusCode() == 200) {
                $response = $response->getBody();
                return Response::ok($response);
            }
        } catch (ClientException $e) {
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
            //在发送网络错误(连接超时、DNS错误等)时，将会抛出 GuzzleHttp\Exception\RequestException 异常。
            //一般为coolq-http-api插件未开启 接口地址无法访问
            switch ($e->getCode()) {
                case 0:
                    return Response::pluginServerError();
                    break;
                default:
                    return Response::error([
                        'message' => $e->getMessage()
                    ]);
                    break;
            }
        } catch (GuzzleException $e) {
        }

    }


}

