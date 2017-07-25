<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 2017/6/28
 * Time: 12:57
 */

namespace CoolQSDK;


class CoolQSDK
{
    private $host;
    private $post;
    private $token;
    private $path;

    /**
     * CoolQSDK constructor.
     * @param string $HOST CoolQ插件开启的服务器地址
     * @param int $POST 端口
     * @param string $TOKEN token
     */
    function __construct($HOST = '127.0.0.1', $POST = 5700, $TOKEN = '')
    {
        $this->token = $TOKEN;
        $this->host = $HOST;
        $this->post = $POST;
        $this->path = $HOST . ':' . $this->post . '/';
    }

    /**
     * /get_login_info 获取登录号信息
     *   参数
     *   无
     *   响应数据
     *   字段名    数据类型    说明
     *   user_id    number    QQ 号
     *   nickname    string    QQ 昵称
     * @return string json
     * {
     *       "status": "ok",
     *       "retcode": 0,
     *       "data": {
     *       "user_id": 1246002938,
     *       "nickname": "机器人不知道多少代了"
     *       }
     *    }
     */
    public function getLoginInfo()
    {
        $url = $this->path . "get_login_info";
        $res = self::curl_request($url);
        return $res;
    }


    /**
     * /send_private_msg 发送私聊消息
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $user_id  number    -    对方 QQ 号
     * @param $message  string/array    -    要发送的内容
     * @param string $is_raw bool    false    消息内容是否作为纯文本发送（即不解析 CQ 码），message 数据类型为 array 时无效
     * @return mixed|string
     */
    public function sendPrivateMsg($user_id, $message, $is_raw = false, $is_post = false)
    {
        if($is_post == false){
            $message = urlencode($message);
            $url = $this->path . "send_private_msg?user_id=$user_id&message=$message&is_raw=$is_raw";
            $res = self::curl_request($url);
        }else{
            $url = $this->path . "send_private_msg";
            $data['user_id'] = $user_id;
            $data['message'] = $message;
            $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $res = self::curl_post($url,$data);
        }
        return $res;
    }

    /**
     * /send_group_msg 发送群消息
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param $message  string/array    -    要发送的内容
     * @param string $is_raw bool    false    消息内容是否作为纯文本发送（即不解析 CQ 码），message 数据类型为 array 时无效
     * @return mixed|string
     */
    public function sendGroupMsg($group_id, $message, $is_raw = false, $is_post = false)
    {
        if($is_post == false){
            $message = urlencode($message);
            $url = $this->path . "send_group_msg?group_id=$group_id&message=$message&is_raw=$is_raw";
            $res = self::curl_request($url);
        }else{
            $url = $this->path . "send_group_msg";
            $data['group_id'] = $group_id;
            $data['message'] = $message;
            $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $res = self::curl_post($url,$data);
        }
        return $res;
    }

    /**
     * /send_discuss_msg 发送讨论组消息
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $discuss_id   number    -    讨论组 ID（正常情况下看不到，需要从讨论组消息上报的数据中获得）
     * @param $message  string/array    -    要发送的内容
     * @param string $is_raw bool    false    消息内容是否作为纯文本发送（即不解析 CQ 码），message 数据类型为 array 时无效
     * @return mixed|string
     */
    public function sendDiscussMsg($discuss_id, $message, $is_raw = false, $is_post = false)
    {
        if($is_post == false){
            $message = urlencode($message);
            $url = $this->path . "send_discuss_msg?discuss_id=$discuss_id&message=$message&is_raw=$is_raw";
            $res = self::curl_request($url);
        }else{
            $url = $this->path . "send_discuss_msg";
            $data['discuss_id'] = $discuss_id;
            $data['message'] = $message;
            $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $res = self::curl_post($url,$data);
        }
        return $res;
    }

    /**
     * /send_like 发送好友赞
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $user_id  number    -    对方 QQ 号
     * @param int $times number    1    赞的次数，每个好友每天最多 10 次
     * @return mixed|string
     */
    public function sendLike($user_id, $times = 1)
    {
        $url = $this->path . "send_like?user_id=$user_id&times=$times";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_kick 群组踢人
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param $user_id number    -    要踢的 QQ 号
     * @param string $reject_add_request bool    false    拒绝此人的加群请求
     * @return mixed|string
     */
    public function setGroupKick($group_id, $user_id, $reject_add_request = false)
    {
        $url = $this->path . "set_group_kick?group_id=$group_id&user_id=$user_id&reject_add_request=$reject_add_request";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_ban 群组单人禁言
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param $user_id  number    -    要禁言的 QQ 号
     * @param int $duration number    30 * 60    禁言时长，单位秒，0 表示取消禁言
     * @return mixed|string
     */
    public function setGroupBan($group_id, $user_id, $duration = 30)
    {
        $duration = $duration * 60;
        $url = $this->path . "set_group_ban?group_id=$group_id&user_id=$user_id&duration=$duration";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_whole_ban 群组全员禁言
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param string $enable bool    true    是否禁言
     * @return mixed|string
     */
    public function setGroupWholeBan($group_id, $enable = true)
    {
        $url = $this->path . "set_group_whole_ban?group_id=$group_id&enable=$enable";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_anonymous_ban 群组匿名用户禁言
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param $flag string    -    要禁言的匿名用户的 flag（需从群消息上报的数据中获得）
     * @param int $duration number    30 * 60    禁言时长，单位秒，无法取消匿名用户禁言
     * @return mixed|string
     */
    public function setGroupAnonymousBan($group_id, $flag, $duration = 30)
    {
        $url = $this->path . "set_group_anonymous_ban?group_id=$group_id&flag=$flag&duration=$duration";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_admin 群组设置管理员
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param $user_id  number    -    要设置管理员的 QQ 号
     * @param string $enable bool    true    true 为设置，false 为取消
     * @return mixed|string
     */
    public function setGroupAdmin($group_id, $user_id, $enable = true)
    {
        $url = $this->path . "set_group_admin?group_id=$group_id&user_id=$user_id&enable=$enable";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_anonymous 群组匿名
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param string $enable bool    true    是否允许匿名聊天
     * @return mixed|string
     */
    public function setGroupAnonymous($group_id, $enable = true)
    {
        $url = $this->path . "set_group_anonymous?group_id=$group_id&enable=$enable";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_special_title 设置群组专属头衔
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param $user_id  number    -    要设置的 QQ 号
     * @param string $special_title string    空    专属头衔，不填或空字符串表示删除专属头衔
     * @param int $duration number    -1    专属头衔有效期，单位秒，-1 表示永久，不过此项似乎没有效果，可能是只有某些特殊的时间长度有效，有待测试
     * @return mixed|string
     */
    public function setGroupSpecialTitle($group_id, $user_id, $special_title = "", $duration = -1)
    {
        $url = $this->path . "set_group_special_title?group_id=$group_id&user_id=$user_id&special_title=$special_title&duration=$duration";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_card 设置群名片（群备注）
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param $user_id  number    -    要设置的 QQ 号
     * @param $card string    空    群名片内容，不填或空字符串表示删除群名片
     * @return mixed|string
     */
    public function setGroupCard($group_id, $user_id, $card)
    {
        $url = $this->path . "set_group_card?group_id=$group_id&user_id=$user_id&card=$card";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_leave 退出群组
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param string $is_dismiss bool    false    是否解散，如果登录号是群主，则仅在此项为 true 时能够解散
     * @return mixed|string
     */
    public function setGroupLeave($group_id, $is_dismiss = false)
    {
        $url = $this->path . "set_group_leave?group_id=$group_id&is_dismiss=$is_dismiss";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_discuss_leave 退出讨论组衔
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $discuss_id   number    -    讨论组 ID（正常情况下看不到，需要从讨论组消息上报的数据中获得）
     * @return mixed|string
     */
    public function setDiscussLeave($discuss_id)
    {
        $url = $this->path . "set_discuss_leave?discuss_id=$discuss_id";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_friend_add_request 处理加好友请求
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $flag  string    -    加好友请求的 flag（需从上报的数据中获得）
     * @param string $approve bool    true    是否同意请求
     * @param string $remark string    空    添加后的好友备注（仅在同意时有效）
     * @return mixed|string
     */
    public function setFriendAddRequest($flag, $approve = true, $remark = "")
    {
        $url = $this->path . "set_friend_add_request?flag=$flag&approve=$approve&remark=$remark";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /set_group_add_request 处理加群请求／邀请
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $flag string    -    加好友请求的 flag（需从上报的数据中获得）
     * @param $type string    -    add 或 invite，请求类型（需要和上报消息中的 sub_type 字段相符）
     * @param string $approve bool    true    是否同意请求／邀请
     * @param string $reason string    空    拒绝理由（仅在拒绝时有效）
     * @return mixed|string
     */
    public function setGroupAddRequest($flag, $type, $approve = true, $reason = "")
    {
        $url = $this->path . "set_group_add_request?flag=$flag&type=$type&approve=$approve&remark=$reason";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /get_group_list  获取群列表
     *   参数
     *        字段名    数据类型    默认值    说明
     * @return mixed|string
     */
    public function getGroupList()
    {
        $url = $this->path . "get_group_list";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /get_group_member_list 获取群成员列表
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @return mixed|string
     */
    public function getGroupMemberList($group_id)
    {
        $url = $this->path . "get_group_member_list?group_id=$group_id";
        $res = self::curl_request($url);
        return $res;
    }


    /**
     * /get_group_member_info 获取群成员信息
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $group_id number    -    群号
     * @param $user_id  number    -    QQ 号（不可以是登录号）
     * @param string $no_cache bool    false    是否不使用缓存（使用缓存可能更新不及时，但响应更快）
     * @return mixed|string
     */
    public function getGroupMemberInfo($group_id, $user_id, $no_cache = false)
    {
        $url = $this->path . "get_group_member_info?group_id=$group_id&user_id=$user_id&no_cache=$no_cache";
        $res = self::curl_request($url);
        return $res;
    }


    /**
     * /get_stranger_info 获取陌生人信息
     *   参数
     *        字段名    数据类型    默认值    说明
     * @param $user_id  number    -    QQ 号（不可以是登录号）
     * @param string $no_cache bool    false    是否不使用缓存（使用缓存可能更新不及时，但响应更快）
     * @return mixed|string
     */
    public function getStrangerInfo($user_id, $no_cache = false)
    {
        $url = $this->path . "get_stranger_info?user_id=$user_id&no_cache=$no_cache";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /get_cookies 获取 Cookies
     * @return mixed|string
     */
    public function getCookies()
    {
        $url = $this->path . "get_cookies";
        $res = self::curl_request($url);
        return $res;
    }

    /**
     * /get_csrf_token 获取 CSRF Token
     * @return mixed|string
     */
    public function getCsrfToken()
    {
        $url = $this->path . "get_csrf_token";
        $res = self::curl_request($url);
        return $res;
    }

    public function curl_request($url)
    {
        $header[] = "Authorization:token $this->token";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        return $data;
    }

    public function curl_post($url,$data)
    {
        $header[] = "Authorization:token $this->token";
        $header[] = "Content-Type: application/json";
        $header[] = "Content-Length: ". strlen($data);
        $ch = curl_init($url); //请求的URL地址
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//$data JSON类型字符串
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        return $data;
    }

    public static function Test()
    {
        return 'success';
    }

}

