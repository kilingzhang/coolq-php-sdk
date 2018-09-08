<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 21:52
 */

namespace Kilingzhang\QQ\Core\Protocols;


use GuzzleHttp\Exception\ClientException;
use Kilingzhang\QQ\Core\Exceptions\Exception;
use Kilingzhang\QQ\Core\Response;
use function Kilingzhang\QQ\http_put;
use function Kilingzhang\QQ\http_server;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class GuzzleProtocol implements Protocol
{
    private $url;
    private $host;
    private $port;
    private $client;
    private $response;
    private $accessToken;
    private $secret;
    private $isSignature = true;
    private $options = [];
    private $content = [];

    /**
     * GuzzleProtocol constructor.
     * @param string $url
     * @param string $access_token
     * @param string $secret
     * @throws Exception
     */
    public function __construct(string $url = '127.0.0.1:5700', string $access_token = '', string $secret = '')
    {
        $this->url = $url;
        $hosts = explode(':', $this->url);
        if (count($hosts) != 2) {
            throw new Exception('missing url or port');
        }
        $this->host = $hosts[0];
        $this->port = $hosts[1];
        $this->accessToken = $access_token;
        $this->secret = $secret;

        $this->options['headers'] = [
            'Authorization' => 'Token ' . $this->accessToken
        ];

        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->url,
            // You can set any number of default request options.
            'timeout' => 10.0,
        ]);

    }

    public function send($uri, $param = [], $method = 'POST'): Response
    {

        try {
            $this->response = $this->client->request($method, $uri, array_merge($this->options, [
                'query' => $param
            ]));
            $response = $this->response;
            if ($response->getStatusCode() == 200) {
                $response = $response->getBody();
                $response = json_decode($response, true);
                $code = $response['retcode'];
                $data = $response['data'];
                if ($code != 0) {
                    return Response::response(200, $code, []);
                }
                $data = empty($data) ? [] : $data;
                return Response::ok($data);
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
                    return Response::pluginServerError($this->client);
                    break;
                default:
                    return Response::error([
                        'message' => $e->getMessage()
                    ]);
                    break;
            }
        }

        return $response;

    }

    public function sendAsync($uri, $param = [], $method = 'POST'): Response
    {

        $promise = $this->client->requestAsync($method, $uri, array_merge($this->options, [
            'query' => $param
        ]));

        $promise->then(
            function (ResponseInterface $res) {
                echo $res->getBody() . "\n";
            },
            function (RequestException $e) {
                echo $e->getMessage() . "\n";
                echo $e->getRequest()->getMethod();
            })->wait();

//        return $promise;
    }

    public function isValidated(): bool
    {
        $signature = http_server('HTTP_X_SIGNATURE');
        $signature = $signature == '' ? '' : substr($signature, 5, strlen($signature));
        $putParams = http_put();
        if ($this->isSignature && !empty($signature) && (hash_hmac('sha1', \GuzzleHttp\json_encode($putParams, JSON_UNESCAPED_UNICODE), $this->secret) != $signature)) {
            //sha1验证失败
            return false;
        }
        return true;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getContent(): array
    {
        $content = http_put();
        if (empty($content)) {
            throw new Exception('put params not be empty');
        }
        switch ($content['post_type']) {
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
                break;
            //群、讨论组变动等非消息类事件
            case 'notice': //兼容4.0
            case 'event':
                $event = empty($content['event']) ? $content['notice_type'] : $content['event'];//兼容4.0
                switch ($event) {
                    //群管理员变动
                    case "group_admin":
                        $this->content = [
                            'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                            //"set"、"unset"	事件子类型，分别表示设置和取消管理员
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                        ];
                        break;
                    //群成员减少
                    case "group_decrease":
                        $this->content = [
                            'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
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
                break;
            default:
                $this->content = $content;
                break;
        }
        $this->content['post_type'] = $content['post_type'];
        return $this->content;
    }

    public function returnApi(Response $response)
    {
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function isCli(): bool
    {
        return false;
    }
}