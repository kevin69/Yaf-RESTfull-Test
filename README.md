# Yaf-RESTfull-Test

##开发测试环境
* **OSX** 10.9.5
* **Nginx** 1.7.4
* **PHP** 5.3.29
* **Yaf** 2.2.9

##随便说说
据说以后的项目要用Yaf，但网上的教程和相关文档实在太少了，自己动手折腾着玩，就当是在熟悉吧，以前也没玩过RESTful，顺便尝试尝试。

##说明
这边建了一个假定为art.wywidgets.com的虚拟主机，nginx的rewrite配置为  
```
rewrite ^/(.*) /index.php?$1 last;
```

在控制器中，使用**indexGetAction**、 **indexPostAction**来代替原来的indexAction来编写相应的方法  
这个时候，使用GET访问/index/index的时候，页面得到Hello world  
而使用POST访问的时候，页面返回Hello world Post
而如果使用OPTIONS访问的时候，在Response Headers里可以得到allow: GET POST  

