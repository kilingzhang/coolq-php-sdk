<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 19:07
 */

namespace Kilingzhang\QQ\Core;

use Kilingzhang\QQ\Core\Protocols\Protocol;

interface QQ
{
    public function __construct(Protocol $request);

    /**
     * 发送私聊消息 同步
     * @param int $userId QQ
     * @param string $message 消息
     * @param bool $autoEscape 是否转译CQ码等特殊符号
     * @return mixed
     */
    public function sendPrivateMsg(int $userId, string $message, bool $autoEscape = false): Response;

    /**
     * 发送私聊消息 异步
     * @param int $userId QQ
     * @param string $message 消息
     * @param bool $autoEscape 是否转译CQ码等特殊符号
     * @return mixed
     */
    public function sendPrivateMsgAsync(int $userId, string $message, bool $autoEscape = false): Response;

    /**
     * 发送群聊消息 同步
     * @param int $groupId 群号
     * @param string $message 消息
     * @param bool $autoEscape 是否转译CQ码等特殊符号
     * @return mixed
     */
    public function sendGroupMsg(int $groupId, string $message, bool $autoEscape = false): Response;

    /**
     * 发送群聊消息 异步
     * @param int $groupId 群号
     * @param string $message 消息
     * @param bool $autoEscape 是否转译CQ码等特殊符号
     * @return mixed
     */
    public function sendGroupMsgAsync(int $groupId, string $message, bool $autoEscape = false): Response;

    /**
     * 发送讨论组消息 同步
     * @param int $discussId 讨论组id
     * @param string $message 消息
     * @param bool $autoEscape 是否转译CQ码等特殊符号
     * @return mixed
     */
    public function sendDiscussMsg(int $discussId, string $message, bool $autoEscape = false): Response;

    /**
     * 发送讨论组消息 异步
     * @param int $discussId 讨论组id
     * @param string $message 消息
     * @param bool $autoEscape 是否转译CQ码等特殊符号
     * @return mixed
     */
    public function sendDiscussMsgAsync(int $discussId, string $message, bool $autoEscape = false): Response;

    /**
     * 发送消息 同步
     * @param string $messageType 消息类型
     * @param int $id 消息接受者id
     * @param string $message 消息
     * @param bool $autoEscape 是否转译CQ码等特殊符号
     * @return mixed
     */
    public function sendMsg(string $messageType, int $id, string $message, bool $autoEscape = false): Response;

    /**
     * 发送消息 异步
     * @param string $messageType 消息类型
     * @param int $id 消息接受者id
     * @param string $message 消息
     * @param bool $autoEscape 是否转译CQ码等特殊符号
     * @return mixed
     */
    public function sendMsgAsync(string $messageType, int $id, string $message, bool $autoEscape = false): Response;

    /**
     * 撤回消息
     * @param int $messageId 消息id
     * @return mixed
     */
    public function deleteMsg(int $messageId): Response;

    /**
     * 撤回群内其他成员消息 机器人必须为管理员
     * @param int $groupId 群号
     * @param int $messageId 消息id
     * @return mixed
     */
    public function deleteGroupMsg(int $groupId, int $messageId): Response;

    /**
     * 赞
     * @param int $userId
     * @param int $times
     * @return mixed
     */
    public function sendLike(int $userId, int $times = 1): Response;

    /**
     * 窗口抖动
     * @param int $userId
     * @return mixed
     */
    public function sendShake(int $userId): Response;

    /**
     * 修改个性签名
     * @param string $ignature
     * @return mixed
     */
    public function setQQSignature(string $ignature): Response;

    /**
     * 修改好友备注
     * @param int $userId QQ
     * @param string $friendName 备注名
     * @return mixed
     */
    public function setFriendName(int $userId, string $friendName): Response;

    /**
     * 删除好友
     * @param int $userId QQ
     * @return mixed
     */
    public function deleteFriend(int $userId): Response;

    /**
     * 加好友
     * @param int $groupId QQ
     * @param string $msg 附带信息
     * @return mixed
     */
    public function addFriend(int $userId, string $msg): Response;

    /**
     * 加群
     * @param int $groupId 群号
     * @param string $msg 附带信息
     * @return mixed
     */
    public function addGroup(int $groupId, string $msg): Response;

    /**
     * 移除群成员
     * @param int $groupId
     * @param int $userId
     * @param bool $rejectAddRequest 是否不再接收加群申请
     * @return mixed
     */
    public function setGroupKick(int $groupId, int $userId, bool $rejectAddRequest = false): Response;

    /**
     * 禁言
     * @param int $groupId 群号
     * @param int $userId QQ
     * @param int $duration 禁言时长 单位:秒 0为解除禁言 默认30分钟
     * @return mixed
     */
    public function setGroupBan(int $groupId, int $userId, int $duration = 30 * 60): Response;

    /**
     * 匿名禁言消息
     * @param int $groupId 群号
     * @param string $flag 用户id
     * @param int $duration 禁言时长 单位:秒 0为解除禁言 默认30分钟
     * @return mixed
     */
    public function setGroupAnonymousBan(int $groupId, string $flag, int $duration = 30 * 60): Response;

    /**
     * 设置全员禁言
     * @param int $groupId 群号
     * @param bool $enable 禁言、解除
     * @return mixed
     */
    public function setGroupWholeBan(int $groupId, bool $enable = true): Response;

    /**
     * 设置群管理
     * @param int $groupId 群号
     * @param int $userId QQ
     * @param bool $enable 设置、取消
     * @return mixed
     */
    public function setGroupAdmin(int $groupId, int $userId, bool $enable = true): Response;

    /**
     * 匿名设置
     * @param int $groupId 群号
     * @param bool $enable 开启、关闭
     * @return mixed
     */
    public function setGroupAnonymous(int $groupId, bool $enable = true): Response;

    /**
     * 修改群内成员的名片
     * @param int $groupId 群号
     * @param int $userId QQ
     * @param string|null $card 新名片
     * @return mixed
     */
    public function setGroupCard(int $groupId, int $userId, string $card = null): Response;

    /**
     * 退群
     * @param int $groupId 群号
     * @param bool $is_dismiss 是否解散，如果登录号是群主，则仅在此项为 true 时能够解散
     * @return mixed
     */
    public function setGroupLeave(int $groupId, bool $is_dismiss = false): Response;

    /**
     * 解散群
     * @param int $groupId 群号
     * @return mixed
     */
    public function setRemoveGroup(int $groupId): Response;

    /**
     * 设置群组专属头衔
     * @param int $groupId 群号
     * @param int $userId QQ
     * @param string|null $specialTitle 专属头衔，不填或空字符串表示删除专属头衔
     * @param int $duration 专属头衔有效期，单位秒，-1 表示永久，不过此项似乎没有效果，可能是只有某些特殊的时间长度有效，有待测试
     * @return mixed
     */
    public function setGroupSpecialTitle(int $groupId, int $userId, string $specialTitle = null, int $duration = -1): Response;

    /**
     * 修改讨论组名称
     * @param int $discussId
     * @param string $discussName
     * @return mixed
     */
    public function setDiscussName(int $discussId, string $discussName): Response;

    /**
     * 退讨论组
     * @param int $discussId
     * @return mixed
     */
    public function setDiscussLeave(int $discussId): Response;

    /**
     * 处理加好友请求 (不同驱动实现不同、参数不同。待兼容) CoolQ
     * @param string $flag 加好友请求的 flag（需从上报的数据中获得）
     * @param bool $approve 是否同意请求
     * @param string $remark 添加后的好友备注（仅在同意时有效）
     * @return mixed
     */
    public function setFriendAddRequest(string $flag, bool $approve = true, string $remark = ''): Response;

    /**
     *  处理加群请求／邀请 (不同驱动实现不同、参数不同。待兼容) CoolQ
     * @param string $flag 加好友请求的 flag（需从上报的数据中获得）
     * @param string $type add 或 invite，请求类型（需要和上报消息中的 sub_type 字段相符）
     * @param bool $approve 是否同意请求／邀请
     * @param string $reason 拒绝理由（仅在拒绝时有效）
     * @return mixed
     */
    public function setGroupAddRequest(string $flag, string $type, bool $approve = true, string $reason = ''): Response;

    /**
     * 获取登录号信息
     * @return mixed
     */
    public function getQQLoginInfo(): Response;

    /**
     * 获取陌生人信息
     * @param int $userId
     * @param bool $noCache
     * @return mixed
     */
    public function getStrangerInfo(int $userId, bool $noCache = false): Response;

    /**
     * 某QQ个人信息
     * @param int $userId
     * @param bool $noCache
     * @return mixed
     */
    public function getQQInfo(int $userId, bool $noCache = false): Response;

    /**
     * 获取群列表
     * @return mixed
     */
    public function getGroupList(): Response;

    /**
     * 群成员信息
     * @param int $groupId
     * @param int $userId
     * @param bool $noCache
     * @return mixed
     */
    public function getGroupMemberInfo(int $groupId, int $userId, bool $noCache = false): Response;

    /**
     * 群成员列表
     * @param int $groupId
     * @return mixed
     */
    public function getGroupMemberList(int $groupId): Response;

    /**
     * 邀请好友入群
     * @param int $groupId
     * @param $int $userId
     * @return mixed
     */
    public function inviteFriendIntoGroup(int $groupId, int $userId): Response;

    /**
     * 取Cookies
     * @return mixed
     */
    public function getCookies(): Response;

    /**
     * 取bkn
     * @return mixed
     */
    public function getBkn(): Response;

    /**
     * 取ClientKey
     * @return mixed
     */
    public function getClientKey(): Response;

    /**
     * 获取 QQ 相关接口凭证 (为了兼容不同平台返回的不同值)
     * @return mixed
     */
    public function getCredentials(): Response;

}