<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/29
 * Time: 0:04
 */

namespace Kilingzhang\Tests;

use Faker\Factory;
use Faker\Generator;
use Kilingzhang\QQ\CoolQ\QQ;
use Kilingzhang\QQ\Core\Protocols\GuzzleProtocol;
use Kilingzhang\QQ\Core\Response;
use PHPUnit\Framework\TestCase;

class CoolQQTest extends TestCase
{

    /**
     * @var \Kilingzhang\QQ\Core\QQ
     */
    private $QQ;
    /**
     * @var Generator
     */
    private $faker;
    private $devQQ = '';
    private $devGroupId = '';
    private $devDiscussId = '';
    private $url = '';
    private $accessToken = '';
    private $secret = '';
    private $messageId;
    private $flagId;
    private $messageGroupId;
    private $messageDiscussId;

    public function setUp()
    {
        $this->QQ = new QQ(new  GuzzleProtocol($this->url, $this->accessToken, $this->secret));
        $this->faker = Factory::create();
    }

    public function testSendPrivateMsg()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendPrivateMsg($this->devQQ, $text, false);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSendPrivateMsgAsync()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendPrivateMsgAsync($this->devQQ, $text, false);
        $this->assertInstanceOf(Response::class, $response);
    }


    public function testSendGroupMsg()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendGroupMsg($this->devGroupId, $text, false);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSendGroupMsgAsync()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendGroupMsgAsync($this->devGroupId, $text, false);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSendDiscussMsg()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendDiscussMsg($this->devDiscussId, $text, false);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSendDiscussMsgAsync()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendDiscussMsgAsync($this->devDiscussId, $text, false);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSendMsg()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendMsg('private', $this->devQQ, $text, false);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSendMsgAsync()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendMsgAsync('private', $this->devQQ, $text, false);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDeleteMsg()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendPrivateMsg($this->devQQ, $text, false);
        $data = $response->getData();
        $this->messageId = $data['message_id'];
        $response = $this->QQ->deleteMsg($this->messageId);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDeleteGroupMsg()
    {
        $text = $this->faker->text;
        $response = $this->QQ->sendGroupMsg($this->devGroupId, $text, false);
        $data = $response->getData();
        $this->messageGroupId = $data['message_id'];
        $response = $this->QQ->deleteGroupMsg($this->devGroupId, $this->messageGroupId);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSendLike()
    {
        $response = $this->QQ->sendLike($this->devQQ, 1);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSendShake()
    {
        $response = $this->QQ->sendShake($this->devQQ);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSetQQSignature()
    {
        $text = $this->faker->text;
        $response = $this->QQ->setQQSignature($text);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSetFriendName()
    {
        $name = $this->faker->name;
        $response = $this->QQ->setFriendName($this->devQQ, $name);
        $this->assertInstanceOf(Response::class, $response);
    }

//    public function testDeleteFriend()
//    {
//        $response = $this->QQ->deleteFriend($this->devQQ);
//        $this->assertInstanceOf(Response::class, $response);
//    }

    public function testAddFriend()
    {
        $name = $this->faker->name;
        $response = $this->QQ->addFriend($this->devQQ, $name);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testAddGroup()
    {
        $name = $this->faker->name;
        $response = $this->QQ->addGroup($this->devGroupId, $name);
        $this->assertInstanceOf(Response::class, $response);
    }

//    public function testSetGroupKick()
//    {
//        $name = $this->faker->name;
//        $response = $this->QQ->setGroupKick($this->devGroupId, $this->devQQ, $name);
//        $this->assertInstanceOf(Response::class, $response);
//    }

    public function testSetGroupBan()
    {
        $response = $this->QQ->setGroupBan($this->devGroupId, $this->devQQ, 1);
        $this->assertInstanceOf(Response::class, $response);
    }

//    public function testSetGroupAnonymousBan()
//    {
//        $response = $this->QQ->setGroupAnonymousBan($this->devGroupId, $this->flagId);
//        $this->assertInstanceOf(Response::class, $response);
//    }

    public function testSetGroupWholeBan()
    {
        $response = $this->QQ->setGroupWholeBan($this->devGroupId);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSetGroupWholeOpen()
    {
        $response = $this->QQ->setGroupWholeBan($this->devGroupId, false);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSetGroupAdmin()
    {
        $response = $this->QQ->setGroupAdmin($this->devGroupId, $this->devQQ);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSetGroupAnonymous()
    {
        $response = $this->QQ->setGroupAnonymous($this->devGroupId);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSetGroupCard()
    {
        $name = $this->faker->name;
        $response = $this->QQ->setGroupCard($this->devGroupId, $this->devQQ, $name);
        $this->assertInstanceOf(Response::class, $response);
    }

//    public function testSetGroupLeave()
//    {
//        $response = $this->QQ->setGroupLeave($this->devGroupId, false);
//        $this->assertInstanceOf(Response::class, $response);
//    }

//    public function testSetRemoveGroup()
//    {
//        $response = $this->QQ->setRemoveGroup($this->devGroupId);
//        $this->assertInstanceOf(Response::class, $response);
//    }

    public function testSetGroupSpecialTitle()
    {
        $name = $this->faker->name;
        $response = $this->QQ->setGroupSpecialTitle($this->devGroupId, $this->devQQ, $name);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSetDiscussName()
    {
        $name = $this->faker->name;
        $response = $this->QQ->setDiscussName($this->devDiscussId, $name);
        $this->assertInstanceOf(Response::class, $response);
    }

//    public function testSetDiscussLeave()
//    {
//        $response = $this->QQ->setDiscussLeave($this->devDiscussId);
//        $this->assertInstanceOf(Response::class, $response);
//    }

//    public function testSetFriendAddRequest()
//    {
//        $remark = $this->faker->title;
//        $response = $this->QQ->setFriendAddRequest($this->flagId, true, $remark);
//        $this->assertInstanceOf(Response::class, $response);
//    }
//
//    public function testSetGroupAddRequest()
//    {
//        $reason = $this->faker->title;
//        $response = $this->QQ->setGroupAddRequest($this->flagId, true, $reason);
//        $this->assertInstanceOf(Response::class, $response);
//    }

    public function testGetQQLoginInfo()
    {
        $response = $this->QQ->getQQLoginInfo();
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetStrangerInfo()
    {
        $response = $this->QQ->getStrangerInfo($this->devQQ);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetQQInfo()
    {
        $response = $this->QQ->getQQInfo($this->devQQ);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetGroupList()
    {
        $response = $this->QQ->getGroupList();
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetGroupMemberInfo()
    {
        $response = $this->QQ->getGroupMemberInfo($this->devGroupId, $this->devQQ);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetGroupMemberList()
    {
        $response = $this->QQ->getGroupMemberList($this->devGroupId);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testInviteFriendIntoGroup()
    {
        $response = $this->QQ->inviteFriendIntoGroup($this->devGroupId, $this->devQQ);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetCookies()
    {
        $response = $this->QQ->getCookies();
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetBkn()
    {
        $response = $this->QQ->getBkn();
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetClientKey()
    {
        $response = $this->QQ->getClientKey();
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetCredentials()
    {
        $response = $this->QQ->getCredentials();
        $this->assertInstanceOf(Response::class, $response);
    }
}
