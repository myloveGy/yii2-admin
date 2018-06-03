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

### Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run
```
php composer.phar require jinxing/yii2-admin "~1.0"
```

Basic Configuration
-------------------

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'modules' => [
        'admin' => [
            'class' => 'jinxing\admin\Admin',
            
            // Make use of that kind of user
            'user' => 'admin'
            
            // Do not verify permissions
            ''
            ...
        ]
        ...
    ],
    ...
    'components' => [
        // Front desk user
        'user' => [
            'identityClass'   => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        
        // Background user
        'admin' => [
            'class' => '\yii\web\User',
            'identityClass' => 'jinxing\admin\models\Admin',
            'enableAutoLogin' => true,
            'loginUrl' => ['/admin/admin/default/login'],
            'idParam' => '_adminId',
            'identityCookie' => ['name' => '_admin','httpOnly' => true],
        ],
        
        // This step is not necessary, but you use it outside the module. The controller, view in the module must be added!
        'i18n' => [
            'translations' => [
                'admin' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en',
                    'basePath'       => '@jinxing/admin/messages'
                ],
            ],
        ],
                
    ]
];
```

There are also some param configuration, not mandatory, with default values

```
// Need to configure params.php
return [
    // Background prefix, used to import data, the prefix of the permission name; currently there is no good solution, all use this configuration item
    'admin_rule_prefix' => 'admin/admin', 
    
     // Login navigation menu cache time
    'cacheTime'         => 86400,    
    
    // Universal status                       
    'status'            => ['停用', '启用'],
               
    'projectName'       => 'Yii2 后台管理系统',              
    'projectTitle'      => 'Yii2 后台管理系统',
    'companyName'       => '<span class="blue bolder"> Liujinxing </span> Yii2 Admin 项目 &copy; 2016-2018',  
];
```
About the configuration of permissions
------------------------------------------

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