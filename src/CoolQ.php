<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 12:57
 */

namespace CoolQSDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;

class CoolQ
{
    private $host;
    private $token;
    private $secret;
    private static $client;
    private static $options;

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

    public function sendPrivateMsg($user_id, $message, $auto_escape = false)
    {
        $uri = URL::send_private_msg;
        $param = [
            'user_id' => $user_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendGroupMsg($group_id, $message, $auto_escape = false)
    {
        $uri = URL::send_group_msg;
        $param = [
            'group_id' => $group_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendDiscussMsg($discuss_id, $message, $auto_escape = false)
    {
        $uri = URL::send_discuss_msg;
        $param = [
            'discuss_id' => $discuss_id,
            'message' => $message,
            'auto_escape' => $auto_escape,
            'is_raw' => $auto_escape,
        ];
        return $this->CURL($uri, $param);
    }

    public function sendMsg($message_type, $id, $message, $auto_escape = false)
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

    public function get_cookies()
    {
        $uri = URL::get_cookies;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public function get_csrf_token()
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
    public
    function getRecord($file, $out_format)
    {
        $uri = URL::get_record;
        $param = [
            'file' => $file,
            'out_format' => $out_format,
        ];
        return $this->CURL($uri, $param);
    }

    public
    function getStatus()
    {
        $uri = URL::get_status;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public
    function getVersionInfo()
    {
        $uri = URL::get_version_info;
        $param = [];
        return $this->CURL($uri, $param);
    }


    public
    function setRestart()
    {
        $uri = URL::set_restart;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public
    function setRestartPlugin()
    {
        $uri = URL::set_restart_plugin;
        $param = [];
        return $this->CURL($uri, $param);
    }

    /**
     * @param string $data_dir 收到清理的目录名，支持 image、record、show、bface
     * @return string
     */
    public
    function cleanDataDir($data_dir = '')
    {
        $uri = URL::clean_data_dir;
        $param = [
            'data_dir' => $data_dir
        ];
        return $this->CURL($uri, $param);
    }

    public
    function __getFriendList()
    {
        $uri = URL::_get_friend_list;
        $param = [];
        return $this->CURL($uri, $param);
    }

    public
    function CURL($uri = URL::get_version_info, $param = [], $method = 'GET')
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
                    return Response::PulginServerError();
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

