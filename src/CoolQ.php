<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/16
 * Time: 0:50
 */

namespace CoolQSDK;


use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class CoolQ extends CoolQBase
{

    function CURL($uri = URL::get_version_info, $param = [], $method = 'GET')
    {
        try {

            $response = self::$client->request($method, $uri, array_merge(self::$options, [
                'query' => $param
            ]));
            if ($response->getStatusCode() == 200) {
                $response = $response->getBody();
                return Response::Ok($response);
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
        } catch (GuzzleException $e) {
        }

    }

    function event()
    {

        if (!$this->isHMAC()) {
            echo '{"block": true,"reply":"signature=false"}';
            return false;
        }

        $content = $this->getPutParams();
        if (empty($content)) {
            echo '{"block": true,"reply":"Params Wrong!}';
            return false;
        }
        $postType = $content['post_type'];
        switch ($postType) {
            //收到消息
            case 'message':
                //TODO 消息处理
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
                //TODO 事件处理
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
                //TODO 申请处理
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
    }
}