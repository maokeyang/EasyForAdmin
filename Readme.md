## 说明文档 ##

**此源码** 是为了快捷搭建后台管理系统设计的一套自动化生成逻辑和视图的工具。

### 环境准备 ###

将下载下来的压缩包，解压放到自己的www目录下。目录结构有admin curd_tool var，admin是管理系统项目后台实际源码地址，curd_tool 是用来生成代码文件的工具，var是存session cache等东西的位置，可以自行修改

### 效果图 ###

本系统是根据adminLte静态模板修改，生成自己的一套动态管理后台模板。效果如下图：

**生成工具的效果图**

> 初始

![生成工具效果图：](http://i.imgur.com/ITlQ9uG.png)

> 使用
 
![](http://i.imgur.com/PssVmea.png)

> 生成工具以及源码的文件

![](http://i.imgur.com/0yCzJfa.png)

**后台管理的效果图**

![](http://i.imgur.com/KEodbcJ.png)

![](http://i.imgur.com/CqcXCwg.png)

![](http://i.imgur.com/avaOBu7.png)


### 使用说明 ###
 
通过curd_tool生成的文件会在path/tools/crud_tool/crud_output里面，文件有php，html文件，只需将php文件复制到path/admin/app/模块名/下，html文件放置在path/admin/app/模块名/
_tp/下，修改path/admin/config/menu.php 添加新增的模块

数据库配置修改文件位于path/admin/config/db.php 

默认有一个admin用户，密码123456，可以在在页面修改，然后添加很多用户，为每个用户添加相应的菜单权限

### 待优化的地方 ###
 
 代码结构目录调整，正在修改，代码冗余可缩小，数据库主从或者接mysql中间件未开发,语言包还未完成
