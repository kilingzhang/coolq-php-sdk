<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 21:52
 */

namespace Kilingzhang\QQ\Core;


use GuzzleHttp\Client;
use function Kilingzhang\QQ\http_put;
use function Kilingzhang\QQ\http_server;

class GuzzleProtocol implements Protocol
{

    private $host;
    private $port;
    private $url;
    private $client;
    private $response;
    private $accessToken;
    private $secret;
    private $isSignature = true;
    private $options = [];


    public function __construct(string $url = '127.0.0.1:5700', string $access_token = '', string $secret = '')
    {
        $this->url = $url;
        $hosts = explode(':', $this->url);
        $this->host = $hosts[0];
        $this->port = $hosts[1];
        $this->accessToken = $access_token;
        $this->secret = $secret;
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->host,
            // You can set any number of default request options.
            'timeout' => 10.0,
        ]);

    }

    public function send($uri, $param = [], $method = 'GET')
    {

        $this->response = $this->client->request($method, $uri, array_merge($this->options, [
            'query' => $param
        ]));

        if ($this->response->getStatusCode() == 200) {
            $response = $this->response->getBody();

        }

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