Yii2 Ace Admin 后台扩展
=======================

为yii2开发的扩展，后台模板使用的ace admin。对于一般的后台开发，比较方便; 对于数据表的CURL操作都有封装，且所有操作都有权限控制。

#### 特点
* 使用RBAC权限管理，所有操作基于权限控制
* 视图使用JS控制，数据显示使用的jquery.DataTables
* 基于数据表的增、删、改、查都有封装，添加新的数据表操作方便
### 安装要求
* PHP >= 5.4
* MySQL

### 安装

1. 执行 composer 安装项目
    
    ```
    php composer require jinxing/yii2-admin
    ```
2. 配置好数据库配置后,导入数据表结构

需要顺序执行
* 导入rbac migration 权限控制数据表
    ```
    php yii migrate --migrationPath=@yii/rbac/migrations
    ``` 
* 导入admin migration 后台基础数据
    ```
    php yii migrate --migrationPath=@jinxing/yii2-admin/migrations
    ```

### 使用说明

基本操作的权限(以管理员操作为例)：

* admin/index       (显示管理员页面 + 左侧导航显示)
* admin/search      (管理员数据显示表格数据显示)
* admin/create      (添加管理员信息)
* admin/update      (修改管理员信息)
* admin/delete      (删除管理员信息)
* admin/delete-all  (批量删除管理员数据)
* admin/upload      (上传管理员头像)
* admin/export      (管理员数据信息导出)

每一个请求对应一个权限，请求路径就是权限名称，权限验证在Controller beforeAction 方法中验证

### 预览
1. 登录页
![登录页](./dosc/images/desc1.png)
2. 数据显示
![数据显示](./dosc/images/desc2.png)
3. 权限分配
![权限分配](./dosc/images/desc3.png)
4. 模块生成
![模块生成](./dosc/images/desc4.png)