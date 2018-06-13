<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/5/2
 * Time: 22:19
 */

namespace CoolQSDK\Plugin;


use CoolQSDK\CoolQ;
use CoolQSDK\CQ;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class Tuling2Plugin extends BasePlugin
{

    private $apiKey = 'acd777bd268347978677e9c48a637c9c';

    private $client = null;

    private static $selfQq = 2093208406;

    public function __construct()
    {
        //MUST  必须复用父类析构函数
        parent::__construct();
        $this->client = new Client();
    }

    public function __destruct()
    {
        //MUST  如果需要复写 必须调用父类
        parent::__destruct();
    }

    public function update(CoolQ $coolQ)
    {

    }

    public function message(CoolQ $coolQ)
    {
        // TODO: Implement message() method.
        $content = $coolQ->getContent();


        switch ($content['message_type']) {
            //私聊消息
            case "private":

                if (empty($this->apiKey)) {
                    $coolQ->sendPrivateMsg($content['user_id'], 'No ApiKey');
                    return;
                }

                $data = $this->privateTuling($content['user_id'], $content['message']);
                $data = json_decode($data, true);
                $coolQ->sendPrivateMsg($content['user_id'], $data['results'][0]['values']['text']);

                break;
            //群消息
            case "group":

                if (self::$selfQq == null) {
                    $loginInfo = json_decode($coolQ->getLoginInfo(), true);
                    if ($loginInfo['retcode'] !== 0) {
                        $coolQ->sendGroupMsg($content['group_id'], '机器人信息获取失败');
                        return;
                    }
                    self::$selfQq = $loginInfo['data']['user_id'];
                }

                if (empty($this->apiKey)) {
                    $coolQ->sendGroupMsg($content['group_id'], 'No ApiKey');
                    return;
                }

                $message = $content['message'];

                if (CQ::isAtMe($message, self::$selfQq) || $content['group_id'] == '647895869') {
                    $data = $this->groupTuling($content['user_id'], $content['group_id'], $content['group_id'], $content['message']);
                    $data = json_decode($data, true);
                    $coolQ->sendGroupMsg($content['group_id'], CQ::At($content['user_id']) . "\n" . $data['results'][0]['values']['text']);

                }


                // {"reply":"message","block": true,"at_sender":true,"kick":false,"ban":false}


                break;
            //讨论组消息
            case "discuss":


                if (empty($this->apiKey)) {
                    $coolQ->sendDiscussMsg($content['discuss_id'], 'No ApiKey');
                    return;
                }

                $data = $this->groupTuling($content['user_id'], $content['discuss_id'], $content['discuss_id'], $content['message']);
                $data = json_decode($data, true);
                $coolQ->sendDiscussMsg($content['discuss_id'], $data['results'][0]['values']['text']);

                // {"reply":"message","block": true,"at_sender":true}
                //todo
                //以后再说吧
                break;

        }


        $this->setIntercept();
    }

    public function event(CoolQ $coolQ)
    {
        // TODO: Implement event() method.
    }

    public function request(CoolQ $coolQ)
    {
        // TODO: Implement request() method.
    }

    public function other(CoolQ $coolQ)
    {
        // TODO: Implement other() method.
    }

    public function privateTuling($userId, $inputText, $inputImage = null, $inputMedia = null, $selfInfo = null)
    {
        $url = 'http://openapi.tuling123.com/openapi/api/v2';

        $params = [
            'reqType' => 0,
            'userInfo' => [
                'apiKey' => $this->apiKey,
                'userId' => $userId,
            ],
            'perception' => [
                'inputText' => [
                    'text' => $inputText
                ],
                'inputImage' => [
                    'url' => $inputImage,
                ],
                'inputMedia' => [
                    'url' => $inputMedia,
                ],
                'selfInfo' => [
                    'location' => $selfInfo
                ],
            ],
        ];

        try {
            $response = $this->client->request('POST', $url, [
                RequestOptions::JSON => $params,
            ]);

            if ($response->getStatusCode() == 200) {
                return $response->getBody();
            }

        } catch (GuzzleException $e) {
        }


    }

    public function groupTuling($userId, $groupId, $userIdName, $inputText, $inputImage = null, $inputMedia = null, $selfInfo = null)
    {
        $url = 'http://openapi.tuling123.com/openapi/api/v2';
        $params = [
            'reqType' => 0,
            'userInfo' => [
                'apiKey' => $this->apiKey,
                'userId' => $userId,
                'groupId' => $groupId,
                'userIdName' => $userIdName,
            ],
            'perception' => [
                'inputText' => [
                    'text' => $inputText
                ],
                'inputImage' => [
                    'url' => $inputImage,
                ],
                'inputMedia' => [
                    'url' => $inputMedia,
                ],
                'selfInfo' => [
                    'location' => $selfInfo
                ],
            ],
        ];

        try {
            $response = $this->client->request('POST', $url, [
                RequestOptions::JSON => $params,
            ]);

            if ($response->getStatusCode() == 200) {
                return $response->getBody();
            }

        } catch (GuzzleException $e) {
        }

    }

    public function PluginName()
    {
        return 'Tuling2Plugin';
    }
}