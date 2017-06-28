# coolq-sdk-php

CoolQ机器人 基于[http插件](https://richardchien.github.io/coolq-http-api/#/) 


```
  
  CoolQ.php     sdk
  
  CQ.php        CQ码封装
  
  MsgTool.php   对特殊消息的转译等处理操作封装
  
```
```

    composer require slight-sky/coolq-sdk-php
    
    {
        "require": {
    		"slight-sky/coolq-sdk-php": "^0.1.0"
        }
    }

    
    

```

```
    require_once '../Autoloader.php';
    
    use CoolQ\CoolQ;
    
    $CoolQ = new  CoolQ('127.0.0.1',5700,'token');
    
    echo $CoolQ->getLoginInfo();
    
```


[API参数描述](https://richardchien.github.io/coolq-http-api/#/API)