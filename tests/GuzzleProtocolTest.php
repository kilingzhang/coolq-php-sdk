<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/27
 * Time: 21:41
 */

namespace Kilingzhang\Tests;


use Faker\Factory;
use Faker\Generator;
use Kilingzhang\QQ\CoolQ\Url;
use Kilingzhang\QQ\Core\Protocols\GuzzleProtocol;
use Kilingzhang\QQ\Core\Response;
use PHPUnit\Framework\TestCase;

class GuzzleProtocolTest extends TestCase
{

    /**
     * @var GuzzleProtocol
     */
    private $protocol;
    /**
     * @var Generator
     */
    private $faker;
    private $devQQ = '';
    private $url = '127.0.0.1:5700';
    private $accessToken = '';
    private $secret = '';

    public function setUp()
    {
        $this->protocol = new GuzzleProtocol($this->url, $this->accessToken, $this->secret);
        $this->faker = Factory::create();
    }

    public function testSend()
    {
        $message = $this->faker->text;
        $response = $this->protocol->send(Url::send_private_msg, [
            'user_id' => $this->devQQ,
            'message' => $message,
            'auto_escape' => false,
        ]);

        $this->assertInstanceOf(Response::class, $response);
    }

}
