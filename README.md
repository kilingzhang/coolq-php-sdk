# coolq-sdk-php
## 简介
通过对 [CoolQ HTTP API 插件](https://cqhttp.cc/docs/) 插件的封装，方便phper直接调用 [CoolQ HTTP API 插件](https://cqhttp.cc/docs/) 插件的各种api。并且已集成对上报事件的封装。未来版本将加入基于 [CoolQ HTTP API 插件](https://cqhttp.cc/docs/) 开发酷Q插件的基础上报事件封装基础类。尽量做到PHP开发QQ机器人一步到位。

## 快速开始

### 安装
#### composer快速安装（推荐）     
```
composer require kilingzhang/coolq-php-sdk
```
#### 或在 ```composer.json``` ```require``` 字段下添加    
```

"require:": {
        ... ,
        "kilingzhang/coolq-php-sdk": "^1.0"
    },

```

### 基本使用

假设我们创建文件为 api.php, 且api.php和vendor目录为同一级目录
```

->api.php
->vendor/

```

#### ```api.php```

```

use CoolQSDK\CoolQ;
use CoolQSDK\Response;

require_once __DIR__ . '/vendor/autoload.php';

$CoolQ = new  CoolQ('127.0.0.1:5700', 'your-access_token', 'your-secret');

//$CoolQ->setReturnFormat('array');

echo $CoolQ->getLoginInfo();


```




## 文档

### API 调用
~~暂未更新~~

### 事件处理
~~暂未更新~~

## 版本升级(针对0.5升级至1.x)
1. 命名空间更改由```CoolQSDK\CoolQSDK```更换为```CoolQSDK\CoolQ```
2. ```new CoolQSDK('127.0.0.1',5700,'token')```  更改为  ```new CoolQ('127.0.0.1:5700', 'your-access_token', 'your-secret')``` 新版本已做 ```token``` 与　```access_token```兼容
3. 删除``` $is_post ```参数　(5.0以下版本用于选择请求接口方法为```GET```还是```POST```，默认```GET```)　现在默认统一为```GET```
4. sendXXX函数添加```$is_aysnc```字段，字段默认为false
5. ```$auto_escape```字段已做向下兼容
6. 0.5.x版本用户可继续通过```composer require slight-sky/coolq-sdk-php```安装


## 框架支持 (未来计划)

- ~~ [Laravel](https://github.com/kilingzhang/coolq-laravel-sdk) ~~
- ~~ [Lumen](https://github.com/kilingzhang/coolq-lumen-sdk) ~~

## 更新记录

- 添加获取群列表
- 添加POST提交事件方式
- 升级SDK 同步cqhttp3.x版本插件
- 兼容cqhttp2.x版本

## API参数描述 
[API参数描述](https://cqhttp.cc/docs/)
