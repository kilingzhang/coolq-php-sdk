# coolq-sdk-php
```
  
  CoolQ.php     sdk
  
  CQ.php        CQ码封装
  
  MsgTool.php   对特殊消息的转译等处理操作封装
  
```


```
    include 'CoolQ.php';
    $CoolQ = new CoolQ('127.0.0.1',5700,'token');
    $CoolQ->getLoginInfo();
```


[API参数描述](https://richardchien.github.io/coolq-http-api/#/API)