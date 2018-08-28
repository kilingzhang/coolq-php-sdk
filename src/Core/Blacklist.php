<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 19:17
 */

namespace Kilingzhang\QQ\Core;


Trait  Blacklist
{

    /**
     * @var bool
     */
    private $isBlackList = true;

    /**
     * 私聊黑名单
     * @var array
     */
    private $privateBlackList = [];
    /**
     * 群组黑名单
     * @var array
     */
    private $groupBlackList = [];
    /**
     * 讨论组黑名单
     * @var array
     */
    private $discussBlackList = [];


    /**
     * @return bool
     */
    public function isBlackList(): bool
    {
        return $this->isBlackList;
    }

    /**
     * @param bool $isBlackList
     */
    public function setIsBlackList(bool $isBlackList)
    {
        $this->isBlackList = $isBlackList;
    }


    /**
     * @return array
     */
    public function getPrivateBlackList(): array
    {
        return $this->privateBlackList;
    }

    /**
     * @param array $privateBlackList
     */
    public function setPrivateBlackList(array $privateBlackList)
    {
        $this->privateBlackList = $privateBlackList;
    }

    /**
     * @return array
     */
    public function getGroupBlackList(): array
    {
        return $this->groupBlackList;
    }

    /**
     * @param array $groupBlackList
     */
    public function setGroupBlackList(array $groupBlackList)
    {
        $this->groupBlackList = $groupBlackList;
    }

    /**
     * @return array
     */
    public function getDiscussBlackList(): array
    {
        return $this->discussBlackList;
    }

    /**
     * @param array $discussBlackList
     */
    public function setDiscussBlackList(array $discussBlackList)
    {
        $this->discussBlackList = $discussBlackList;
    }

}