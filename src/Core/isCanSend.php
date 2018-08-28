<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/29
 * Time: 0:48
 */

namespace Kilingzhang\QQ\Core;


trait isCanSend
{
    use Blacklist, Whitelist;

    public function isCanSendPrivate(int $userId): bool
    {
        if ($this->isWhiteList() && !in_array($userId, $this->getPrivateWhiteList())) {
            return false;
        }
        if (!$this->isWhiteList() && $this->isBlackList() && in_array($userId, $this->getPrivateBlackList())) {
            return false;
        }
        return true;
    }

    public function isCanSendGroup(int $groupId): bool
    {
        if ($this->isWhiteList() && !in_array($groupId, $this->getGroupWhiteList())) {
            return false;
        }
        if (!$this->isWhiteList() && $this->isBlackList() && in_array($groupId, $this->getGroupBlackList())) {
            return false;
        }
        return true;
    }

    public function isCanSendDiscuss(int $discussId): bool
    {
        if ($this->isWhiteList() && !in_array($discussId, $this->getDiscussWhiteList())) {
            return false;
        }
        if (!$this->isWhiteList() && $this->isBlackList() && in_array($discussId, $this->getDiscussBlackList())) {
            return false;
        }
        return true;
    }
}