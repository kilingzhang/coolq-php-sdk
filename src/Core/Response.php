<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/28
 * Time: 0:06
 */

namespace Kilingzhang\QQ\Core;


class Response
{

    public $code = 0;
    public $message = 'success';
    public $errorCode = 0;
    public $errorMsg = 0;
    public $data = [];

    public function __construct(int $code, string $message, int $errorCode, string $errorMsg, array $data)
    {
        $this->code = $code;
        $this->message = $message;
        $this->errorCode = $errorCode;
        $this->errorMsg = $errorMsg;
        $this->data = $data;
    }

    /**
     * @return int
     */
    public
    function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }


    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }


    public function getErrorMsg(): string
    {
        return $this->errorMsg;
    }


    public function toArray(): array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'errorCode' => $this->getErrorCode(),
            'errorMsg' => $this->getErrorMsg(),
            'data' => $this->getData(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public static function response($code, $errorCode, $data)
    {


        $message = [
            0 => '接口请求成功',
            100 => '请参数参数错误',
            200 => 'HTTP请求成功',
            400 => '资源请求被拒绝',
            500 => '服务器故障',
            65535 => '未获取到上报事件发送的上报数据',
        ];

        if (!array_key_exists($code, $message)) {
            $message = '未知状态码';
        }

        $message = $message[$code];

        $msg = [
            -1 => '请求发送失败',
            -2 => '未收到服务器回复，可能未发送成功',
            -3 => '消息过长或为空',
            -4 => '消息解析过程异常',
            -5 => '日志功能未启用',
            -6 => '日志优先级错误',
            -7 => '数据入库失败',
            -8 => '不支持对系统帐号操作',
            -9 => '帐号不在该群内，消息无法发送',
            -10 => '该用户不存在/不在群内',
            -11 => '数据错误，无法请求发送',
            -12 => '不支持对匿名成员解除禁言',
            -13 => '无法解析要禁言的匿名成员数据',
            -14 => '由于未知原因，操作失败',
            -15 => '群未开启匿名发言功能，或匿名帐号被禁言',
            -16 => '帐号不在群内或网络错误，无法退出/解散该群',
            -17 => '帐号为群主，无法退出该群',
            -18 => '帐号非群主，无法解散该群',
            -19 => '临时消息已失效或未建立',
            -20 => '参数错误',
            -21 => '临时消息已失效或未建立',
            -22 => '获取QQ信息失败',
            -23 => '找不到与目标QQ的关系，消息无法发送',
            -99 => 'Air 不支持此操作',
            -101 => '应用过大',
            -102 => '不是合法的应用',
            -103 => '不是合法的应用',
            -104 => '应用不存在公开的Information函数',
            -105 => '无法载入应用信息',
            -106 => '文件名与应用ID不同',
            -107 => '返回信息解析错误',
            -108 => 'AppInfo返回的Api版本不支持直接加载，仅支持Api版本为9(及以上)的应用直接加载',
            -109 => 'AppInfo返回的AppID错误',
            -110 => '缺失AppInfo返回的AppID对应的[Appid].json文件',
            -111 => '[Appid].json文件内的AppID与其文件名不同',
            -120 => '无Api授权接收函数(Initialize)',
            -121 => 'Api授权接收函数(Initialize)返回值非0',
            -122 => '尝试恶意修改酷Q配置文件，将取消加载并关闭酷Q',
            -150 => '无法载入应用信息',
            -151 => '应用信息Json串解析失败，请检查Json串是否正确',
            -152 => 'Api版本过旧或过新',
            -153 => '应用信息错误或存在缺失',
            -154 => 'Appid不合法',
            -160 => '事件类型(Type)错误或缺失',
            -161 => '事件函数(Function)错误或缺失',
            -162 => '应用优先级不为10000、20000、30000、40000中的一个',
            -163 => '事件类型(Api)不支持应用Api版本',
            -164 => '应用Api版本大于8，但使用了新版本已停用的事件类型(Type)：1(好友消息)、3(临时消息)',
            -165 => '事件类型为2(群消息)、4(讨论组消息)、21(私聊消息)，但缺少正则表达式(regex)的表达式部分(expression)',
            -166 => '存在为空的正则表达式(regex)的key',
            -167 => '存在为空的正则表达式(regex)的表达式部分(expression)',
            -168 => '应用事件(event)id参数不存在或为0',
            -169 => '应用事件(event)id参数有重复=>',
            -180 => '应用状态(status)id参数不存在或为0',
            -181 => '应用状态(status)period参数不存在或设置错误',
            -182 => '应用状态(status)id参数有重复=>',
            -201 => '无法载入应用，可能是应用文件已损坏',
            -202 => 'Api版本过旧或过新',
            -997 => '应用未启用',
            -998 => '应用调用在Auth声明之外的 酷Q A。',
            -2333 => 'CQHTTP插件未开启，或插件服务器启动失败。访问被拒绝。请检测Docker端口是否开启，网络是否通畅。',
            -1000 => '无法找到swoole扩展，请先安装.',
            -1001 => 'must be used in PHP CLI mode.',
            -1002 => '无法找到Pcntl扩展，请先安装.',
            0 => '同时 status 为 ok，表示操作成功',
            1 => '同时 status 为 async，表示操作已进入异步执行，具体结果未知',
            100 => '参数缺失或参数无效，通常是因为没有传入必要参数，某些接口中也可能因为参数明显无效（比如传入的 QQ 号小于等于 0，此时无需调用酷 Q 函数即可确定失败），此项和以下的 status 均为 failed',
            102 => '酷 Q 函数返回的数据无效，一般是因为传入参数有效但没有权限，比如试图获取没有加入的群组的成员列表,或调取语音相关接口未安装语音组件',
            103 => '操作失败，一般是因为用户权限不足，或文件系统异常、不符合预期',
            201 => '工作线程池未正确初始化（无法执行异步任务）',
            400 => ' POST 请求的正文格式不正确',
            401 => 'access token 未提供',
            403 => 'access token 或 HTTP_X_SIGNATURE 不符合',
            404 => 'API 不存在',
            405 => '该账号id已被禁止，无法对其进行操作',
            406 => 'POST 请求的 Content-Type 不支持',
            500 => '未知错误',
            65535 => '未获取到上报事件发送的上报数据',
        ];

        if (!array_key_exists($errorCode, $msg)) {
            $msg = '未知状态码';
        }

        $msg = $msg[$errorCode];

        $response = new Response($code, $message, $errorCode, $msg, $data);
        return $response;
    }

    public static function ok(array $data = [])
    {
        $response = new Response(0, 'success', 200, 'success', $data);
        return $response;
    }

    public static function notFoundResourceError()
    {
        return Response::response(400, 404, []);
    }

    public static function accessTokenError()
    {
        return Response::response(400, 403, []);
    }

    public static function signatureError()
    {
        return Response::response(400, 403, []);
    }

    public static function accessTokenNoneError()
    {
        return Response::response(400, 401, []);
    }

    public static function banAccountError($data = [])
    {
        return Response::response(400, 405, $data);
    }

    public static function contentTypeError()
    {
        return Response::response(400, 406, []);
    }

    public static function pluginServerError($data = [])
    {
        return Response::response(500, -2333, $data);
    }

    public static function eventMissParamsError($data = [])
    {
        return Response::response(100, 65535, $data);
    }


    public static function NotExitsSwoolError($data = [])
    {
        return Response::response(500, -1000, $data);
    }

    public static function NotBeCliError($data = [])
    {
        return Response::response(500, -1001, $data);
    }

    public static function NotExitsPcntlError($data = [])
    {
        return Response::response(500, -1002, $data);
    }

    public static function error($data = [])
    {
        return Response::response(500, 500, $data);
    }

}