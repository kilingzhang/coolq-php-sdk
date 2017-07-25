# coolq-sdk-php

- CoolQ机器人 

    基于[http插件](https://richardchien.github.io/coolq-http-api/#/)
 
 
- [CoolQ机器人后台管理](https://github.com/slight-sky/CoolQ/tree/CoolQ) 

    基于[coolq-sdk-php](https://github.com/slight-sky/coolq-sdk-php) 
    
    基于[http插件](https://richardchien.github.io/coolq-http-api/#/)

```
  
  CoolQ.php     sdk
  
  CQ.php        CQ码封装
  
 
  
```
```

    composer require slight-sky/coolq-sdk-php
    
    {
        "require": {
    		"slight-sky/coolq-sdk-php": "^0.5.0"
        }
    }

    
    

```

```
    require_once '../Autoloader.php';
    
    use CoolQSDK\CoolQSDK;
    
    $CoolQ = new  CoolQSDK('127.0.0.1',5700,'token');
    
    echo $CoolQ->getLoginInfo();
    
```

#log

- 添加获取群列表
- 添加POST提交事件方式

[API参数描述](https://richardchien.github.io/coolq-http-api/#/API)