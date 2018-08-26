<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 23:13
 */

namespace Kilingzhang\Tests;

use Kilingzhang\QQ\CoolQ\CoolQ;
use Kilingzhang\QQ\Core\GuzzleProtocol;
use PHPUnit\Framework\TestCase;

class CoolQTest extends TestCase
{

    public $QQ;

    public function test__construct()
    {
        $request = new GuzzleProtocol('127.0.0.1:5700', 'kilingzhang', 'kilingzhang');

        $this->QQ = new CoolQ($request);

    }

    public function testSendPrivateMsg()
    {

    }

    public function testSendPrivateMsgAsync()
    {

    }
}
