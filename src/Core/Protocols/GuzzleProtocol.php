<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 21:52
 */

namespace Kilingzhang\QQ\Core\Protocols;


use GuzzleHttp\Exception\ClientException;
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


    /**
     * GuzzleProtocol constructor.
     * @param string $url
     * @param string $access_token
     * @param string $secret
     */
    public function __construct(string $url = '127.0.0.1:5700', string $access_token = '', string $secret = '')
    {
        $this->url = $url;
        $hosts = explode(':', $this->url);
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
        $signature = $signature == '' ?? substr($signature, 5, strlen($signature));
        $putParams = http_put();
        if ($this->isSignature && !empty($signature) && (hash_hmac('sha1', \GuzzleHttp\json_encode($putParams, JSON_UNESCAPED_UNICODE), $this->secret) != $signature)) {
            //sha1验证失败
            return false;
        }
        return true;
    }

}