<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 19:18
 */

namespace Kilingzhang\QQ\Core;


trait Whitelist
{

    /**
     * @var bool
     */
    private $isWhiteList = false;

    /**
     * 白名单优先级高于黑名单 当开启白名单时，黑名单将失效
     * 私聊白名单
     * @var array
     */
    private $privateWhiteList = [];

    /**
     * 群组白名单
     * @var array
     */
    private $groupWhiteList = [];

    /**
     * 讨论组白名单
     * @var array
     */
    private $discussWhiteList = [];

    /**
     * @return bool
     */
    public function isWhiteList(): bool
    {
        return $this->isWhiteList;
    }

    /**
     * @param bool $isWhiteList
     */
    public function setIsWhiteList(bool $isWhiteList)
    {
        $this->isWhiteList = $isWhiteList;
    }

    /**
     * @return array
     */
    public function getPrivateWhiteList(): array
    {
        return $this->privateWhiteList;
    }

    /**
     * @param array $privateWhiteList
     */
    public function setPrivateWhiteList(array $privateWhiteList)
    {
        $this->privateWhiteList = $privateWhiteList;
    }

    /**
     * @return array
     */
    public function getGroupWhiteList(): array
    {
        return $this->groupWhiteList;
    }

    /**
     * @param array $groupWhiteList
     */
    public function setGroupWhiteList(array $groupWhiteList)
    {
        $this->groupWhiteList = $groupWhiteList;
    }

    /**
     * @return array
     */
    public function getDiscussWhiteList(): array
    {
        return $this->discussWhiteList;
    }

    /**
     * @param array $discussWhiteList
     */
    public function setDiscussWhiteList(array $discussWhiteList)
    {
        $this->discussWhiteList = $discussWhiteList;
    }

}