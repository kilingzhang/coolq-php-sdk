# coolq-sdk-php
## 简介
通过对 [CoolQ HTTP API 插件](https://cqhttp.cc/docs/) 插件的封装，方便phper直接调用 [CoolQ HTTP API 插件](https://cqhttp.cc/docs/) 插件的各种api。并且已集成对上报事件的封装。一个纯PHP 的 cqhttp-api 的 最基础的封装。 将更大的发挥空间留给开发者。 接下来会发布一个基于本sdk的机器人框架。让开发者开发机器人插件更加方便高效。尽量保持各个插件代码和sdk代码的低耦合。让插件开发变得更加简单。

## 快速开始
### 环境
```
PHP >= 7.0
```

### 安装
#### composer快速安装（推荐）     
```
composer require kilingzhang/coolq-php-sdk
```
> 注：如果安装过慢，可以在全局添加国内的composer镜像  [详情](https://pkg.phpcomposer.com/#how-to-use-packagist-mirror)
```
composer config -g repo.packagist composer https://packagist.phpcomposer.com
```

### SDK目录代码介绍

![coolq-sdk-php-code-tree](http://markdown-1252847423.file.myqcloud.com/cool-sdk-php-code-tree.png)

1. `CoolQBase.php` *核心文件*
> coolq-sdk-php 的所有核心方法均在此文件中。为开发者的基础文件，所有开发扩展需要继承此类，此文件包含了对于coolq-http-api 插件的 上报及主动Api调用的封装。方便开发者在安装本sdk后直接可上手开发。其中各种方法所对应 [CoolQ HTTP API 插件 文档](https://cqhttp.cc/docs/4.0/#/API) 中 Api的url。 如  [CoolQ HTTP API 插件 中 发送私聊消息 文档](https://cqhttp.cc/docs/4.0/#/API) 中 url 为 ![send_private_msg](http://markdown-1252847423.file.myqcloud.com/%5DL8RI%28OHL1~UWLAQ%60YPH8PS.png)，则此sdk所对应的方法为 `public function sendPrivateMsg(int $user_id, string $message, bool $auto_escape = false, bool $async = null)`。 其中 $async  参数是为了兼容是否开启异步发送消息。其余函数详情请见[文档说明]()

2. `CoolQ.php` *核心文件*
每一个开发者的入口文件。本sdk默认封装了很多基础的方法，但是由于每个人的对于上报事件的处理都不相同，且部分同学不想在本框架中引入http请求的第三方包，或者想对此过程进行监控。所以本sdk提供了对CoolQBase类进行继承，在子类中重写和实现event抽象类的方法来提供代码的解耦。CoolQ类为sdk默认提供的基于CoolQBase父类的实体类。里面的网络请求的crul方法通过第三方包`guzzlehttp/guzzle`实现。event事件未做响应事件的相应处理，仅提供了对应流程的方法。方便大家套用。如果想自己实现event上报事件的开发，需要自己实现继承CoolQBase父类的实体。并实现event方法。
3. `CQ.php` 
> 对于cq码字符串拼接的封装，方便开发者调用已有cq码。如 `@1353693508` cq码为`[CQ:at,qq=1353693508]`,此时我们只需要调用 `CQ:at(1353693508)` 该方法会帮我们返回已拼接的对应cq码字符串。
4. `Url.php`
> `coolq-http-api` 的接口调用时所需访问的url集合。如：` const send_private_msg = '/send_private_msg';` 等。
5. `Response.php`
> 对于sdk返回响应的数据结构及方法的封装。




### 基本使用
[DEMO](https://github.com/kilingzhang/coolq-sdk-php-test)

假设我们创建文件为 api.php, 且api.php和vendor目录为同一级目录
```
->src/CoolQ.php
->api.php
->vendor/

```

#### ```api.php```

```php

<?php


use Kilingzhang\Tests\CoolQ;

include 'vendor/autoload.php';

$CoolQ = new  CoolQ('127.0.0.1:5700', 'token', 'secret', false);
//$CoolQ = new  CoolQ('127.0.0.1:6700', 'token', 'secret', true);

//$CoolQ->setReturnFormat('array');

$CoolQ->event();


```


#### ```src/CoolQ.php```

```php

namespace Kilingzhang\Tests;


use CoolQSDK\Response;

class CoolQ extends \CoolQSDK\CoolQ
{

    public function beforeCurl($uri = '', $param = [])
    {

    }

    public function afterCurl($uri = '', $param = [], $response, $errorException)
    {

    }


    public function onSignature($isHMAC)
    {
        if (!$isHMAC) {
            $this->returnJsonApi(Response::signatureError());
        }
    }

    public function onMessage($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function onEvent($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function onNotice($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function onRequest($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function onOther($content)
    {
        $response = $this->sendPrivateMsg(1353693508, json_encode($content, JSON_UNESCAPED_UNICODE), false, true);
        $this->returnJsonApi($response);
    }

    public function beforEvent()
    {
    }

    public function afterEvent()
    {
    }
}

```
记得在composer中添加命名空间  否则无法自动加载
```json
 {
     ...
     "autoload-dev": {
         "psr-4": {
             "Kilingzhang\\Tests\\": "src/"
         }
     }
 }

```


在对应的 api.php 目录下，输入命令。

```
php -S 0.0.0.0:8000 -t ./
```

这时候我们写的上报事件就跑起来了。





## 文档

### API 调用
[API文档]()

### 事件处理
[事件处理文档]()


## 版本升级(针对0.5升级至1.x)
1. 命名空间更改由```CoolQSDK\CoolQSDK```更换为```CoolQSDK\CoolQ```
2. ```new CoolQSDK('127.0.0.1',5700,'token')```  更改为  ```new CoolQ('127.0.0.1:5700', 'your-access_token', 'your-secret')``` 新版本已做 ```token``` 与　```access_token```兼容
3. 删除``` $is_post ```参数　(0.5以下版本用于选择请求接口方法为```GET```还是```POST```，默认```GET```)　现在默认统一为```GET```
4. sendXXX函数添加```$is_aysnc```字段，字段默认为false
5. ```$auto_escape```字段已做向下兼容
6. 0.5.x版本用户可继续通过```composer require slight-sky/coolq-sdk-php```安装


## 版本升级(针对1.x升级至2.x)
sdk进行了大量不兼容修改。 如命名空间修改，代码风格规范化等。如： CQ码中 静态方法全部小写字母开头等。

sdk对coolq-http-api插件的升级进行兼容，已从 3.x升级至4.x 其中上报事件的字段变化详见 [coolq-http-api 升级指南](https://cqhttp.cc/docs/4.0/#/UpgradeGuide)
## 框架支持 (未来计划)

- ~~[swoole](https://github.com/kilingzhang/coolq-swoole-sdk)~~


## 基于sdk的项目


## 更新记录

- 添加获取群列表
- 添加POST提交事件方式
- 升级SDK 同步cqhttp4.x版本插件
- 兼容cqhttp3.x,2.x版本

## API参数描述 
[API参数描述](https://cqhttp.cc/docs/)
