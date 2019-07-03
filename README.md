Yii2 Ace Admin 后台扩展模块
==========================

![Progress](http://progressed.io/bar/100?title=completed) 
[![Latest Stable Version](https://poser.pugx.org/jinxing/yii2-admin/v/stable)](https://packagist.org/packages/jinxing/yii2-admin)
[![Total Downloads](https://poser.pugx.org/jinxing/yii2-admin/downloads)](https://packagist.org/packages/jinxing/yii2-admin)
[![Latest Unstable Version](https://poser.pugx.org/jinxing/yii2-admin/v/unstable)](https://packagist.org/packages/jinxing/yii2-admin)
[![GitHub issues](https://img.shields.io/github/issues/myloveGy/yii2-admin.svg)](https://github.com/myloveGy/yii2-admin/issues)
[![GitHub forks](https://img.shields.io/github/forks/myloveGy/yii2-admin.svg)](https://github.com/myloveGy/yii2-admin/network)
[![GitHub stars](https://img.shields.io/github/stars/myloveGy/yii2-admin.svg)](https://github.com/myloveGy/yii2-admin/stargazers)
[![GitHub license](https://img.shields.io/github/license/myloveGy/yii2-admin.svg)](https://github.com/myloveGy/yii2-admin/blob/master/LICENSE.md)

[change to English](/README.en-US.md)

## 作者博客

[https://mylovegy.github.io/blog/](https://mylovegy.github.io/blog/)

## 简介

使用的 [ace admin](http://ace.jeka.by/) 前端框架, 为`yii2`开发的一个后台模块; 
对于二次开发比较方便，包含了基本的后台功能

## 功能特性

* 包含基本的后台功能
    - 管理员管理: 登录、登出、修改密码等
    - 菜单管理: 可视化动态菜单、根据权限显示菜单
    - 权限管理: 角色、权限、用户的管理  
    
* 使用`yii2`自带的`RBAC`权限管理
* 对于二次开发比较方便
    - 定义基本控制器(封装了基本的`CURD`操作), 后续开发基于基础控制器继承修改
    - 拥有模块生成功能(类似于`gii`), 可视化生成代码模板, 简单操作即可生成 控制器`controller`、模型`model`, 视图`views`
        文件，提高开发效率

## 安装

### 安装要求

* PHP >= 5.4
* MySQL

### 全新项目安装

全新项目安装可以直接使用[liujx/yii2-app-advanced](https://packagist.org/packages/liujx/yii2-app-advanced)

### 在已有项目中安装

使用 `composer` 下载包文件 

```
composer require jinxing/yii2-admin
```

### 配置模块信息

在你的 `main.php` 配置文件中添加下面配置

```php
return [
    'modules' => [
        'admin' => [
            'class' => 'jinxing\admin\Module',
        ]
    ],
    'components' => [
        
        // 后台登录用户组件信息
        'admin' => [
            'class'           => 'yii\web\User',
            'identityClass'   => 'jinxing\admin\models\Admin',
            'enableAutoLogin' => true,
            'loginUrl'        => ['/admin/admin/default/login'],
            'idParam'         => '_adminId',
            'identityCookie'  => ['name' => '_admin', 'httpOnly' => true],
        ],
        
        // 后台使用的语言配置信息
        'i18n' => [
            'translations' => [
                'admin' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en',
                    'basePath'       => '@jinxing/admin/messages'
                ],
            ],
        ],
        
        // 配置权限使用数据库
        'authManager'  => [
            'class' => 'yii\rbac\DbManager',
        ],
                
    ]
];
```

在你的 `params.php` 配置文件添加如下配置信息

```php
return [
    // 这个配置是为了导入权限信息需要配置的，就是配置后台模块的路径 
    'admin_rule_prefix' => 'admin',                        

    // 登录成功首页是否需要显示其他信息
    'projectOpenOther' => true,
    
    // 项目信息
    'projectName'      => 'Yii2 后台管理系统',              
    'projectTitle'     => 'Yii2 后台管理系统',
    'companyName'      => '<span class="blue bolder"> Liujinxing </span> Yii2 Admin 项目 &copy; 2016-2018',  
];
```

#### [高级版本配置参考](./docs/module.md#yii2高级版后台配置模板, '高级版本配置参考')
#### [基础版本配置参考](./docs/module.md#yii2基础版后台配置模板, '基础版本配置参考')

### 使用数据库迁移、导入后台所需的数据库信息、需要顺序执行下面命令

#### 需要配置 `console`

在 `console` 配置中的 `components` 组件中加入权限配置信息

```php
return [
    'components' => [
        // 权限配置
        'authManager'  => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
];
``` 

- 高级版本的配置文件在 `console/config/main.php`中
- 基础版本的配置文件在 `config/console.php`中

#### 导入权限表信息
```
php yii migrate --migrationPath=@yii/rbac/migrations
```

#### 导入后台表信息和默认权限、菜单信息
```
php yii migrate --migrationPath=@jinxing/admin/migrations
```

### 你可以愉快的使用了

访问地址

```
// 登录地址、域名需要根据你的域名修改
http://localhost/path/to?index.php?r=admin/default/login
```

#### 默认的账号和密码

1. 超级管理员
    - username: super  
    - password: admin123

2. 普通管理员
    - username: admin
    - password: admin888
    
## 在自己模块中使用

### `Yii2` 高级版本中使用

定义一个基础控制，其他控制器都继承基础控制器

1. 控制器继承`jinxing\admin\controllers\Controller` 
    - 定义控制器使用的布局文件为 `@jinxing/admin/views/layouts/main`
    - 定义上传文件表单类使用自己的 ，例如：`backend\models\forms\UploadForm`
2. 如果要记录操作日志和权限验证，定义行
    - 记录日志行为类： `jinxing\admin\behaviors\Logging` 
    
        默认只会记录： create, update, delete, delete-all, editable, upload 操作的日志，
        需要添加或者修改，定义`needLogActions` 属性
    
    - 权限验证行为类： `yii\filters\AccessControl` [类的属性和配置参考](https://www.yiichina.com/doc/api/2.0/yii-filters-accesscontrol)

例子：
```php
namespace backend\controllers;

use jinxing\admin\behaviors\Logging;
use jinxing\admin\controllers\Controller as BaseController;
use yii\filters\AccessControl;

/**
 * Class Controller 后台的基础控制器
 * @package backend\controllers
 */
class Controller extends BaseController
{
    /**
     * @var string 使用 yii2-admin 的布局
     */
    public $layout = '@jinxing/admin/views/layouts/main';
    
    /**
     * @var string 使用自己定义的上传文件处理表单
     */
    public $uploadFromClass = 'backend\models\forms\UploadForm';
    
    /**
     * 定义使用的行为
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'       => true,
                        'permissions' => [$this->action->getUniqueId()],
                    ],
                ],
            ],
            
            'logging' => [
                'class' => Logging::className(),
            ],
        ];
    }
}
```

### `Yii2` 基础版本中使用

`yii2` 基础版本需要为后台定义一个模块，这个模块可以直接继承`jinxing\admin\Module`

例子：
```php
namespace app\modules\admin;

use Yii;
use jinxing\admin\Module;

/**
 * admin module definition class
 */
class Admin extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        Yii::$app->errorHandler->errorAction = $this->getUniqueId() . '/admin/default/error';
    }
}
```

>如果不使用模块继承方式，配置参考高级版本

## 使用文档

### [模块配置说明](./docs/module.md)
### [控制器配置说明](./docs/controller.md)
### [前端`MeTables`配置说明](https://github.com/myloveGy/jinxing-tables?_blank)

## 后台预览

1. 登录页面
![登录页](./docs/images/docs-1.png)
2. 数据显示
![数据显示](./docs/images/docs-2-1.png)
![数据显示](./docs/images/docs-2-2.png)
3. 权限分配
![权限分配](./docs/images/docs-3.png)
4. 模块生成(代码生成)
![模块生成](./docs/images/docs-4.png)