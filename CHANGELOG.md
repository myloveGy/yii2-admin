Yii2-admin extension Change Log
===============================
1.2.19 2020-05-25
-----------------
- 🌟 登录页面配色修改 星空、蓝色、明亮
- 💄 添加`loginOtherRenderPaths` 配置登录页面，渲染注册管理员、忘记密码页面地址
```php
    /**
     * @var string[] 登录视图中需要引入其他页面的路径配置
     */
    $loginOtherRenderPaths = [
        // 注册管理员
        'register' => '/default/register',

        // 忘记密码
        'forgot'   => '/default/forgot',
    ];
```

如果不需要注册、忘记密码
```php
return [
    'modules'             => [
        'admin' => [
            'class'                 => 'jinxing\admin\Module',
            'user'                  => 'user',
            'loginOtherRenderPaths' => [],
        ],
    ],
];
```

1.2.18 2020-04-09
-----------------

- 🛠 修复角色分配，管理员能够删除超级管理员的情况

1.2.17 2020-02-14
-----------------

- 🌟 使用代码生成的model的label默认使用db的comments

1.2.16 2019-12-09
-----------------

- 🛠 修复查询菜单为空报的Notice的错误bug


1.2.15 2019-12-05
-----------------

- 🛠 修复当iframe页面session过期后，主页面没有刷新问题

1.2.14 2019-12-01
-----------------

- refactor: `Nav`小部件的`url`使用绝对路径，并且添加 `Yii::$app->getRequest()->getBaseUrl()`配置的前缀
>目的是为了项目使用二级目录配置的时候，不需要关注菜单路径、和权限；权限和菜单添加的时候，不需要添加二级目录的前缀，和单域名部署添加方式保持一致；如下：

1. 单独域名部署访问地址: `http://localhost/admin/menu/index`
2. 二级目录部署(/admin)访问地址: `http://localhost/admin/admin/menu/index`

二级目录部署需要配置

项目配置(只是request部分)：
```php
$config = [
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl'   => '/admin',
        ],
    ]
];
```

nginx配置(这里给出的只是路由重写部分配置):
```
location /admin {
    try_files $uri $uri/ /backend/web/index.php$is_args$args;
}

location ~ ^/admin/(.+\.(html|js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar|woff2|woff|ttf))$ {
    access_log  off;
    expires  360d;

    rewrite  ^/admin/(.+)$  /backend/web/$1 break;
    rewrite  ^/admin/(.+)/(.+)$  /backend/web/$1/$2 break;
    try_files  $uri =404;
}
```

上面两种配置对应的权限和名称都为：

1. 菜单地址：`admin/menu/index`
2. 权限名称：`admin/menu/index`

1.2.13 2019-11-28
-----------------

- refactor: 添加`captchaAction`配置选项，配置验证码验证`action`地址

1.2.12 2019-11-24
-----------------

- fix: 修复`modules`中`beforeAction`返回错误问题

1.2.11 2019-11-12
-----------------

- feat: 添加退出页面可以在模块中配置 `logoutUrl`

1.2.10 2019-11-04
-----------------

- refactor: 优化角色分配权限信息页面
- refactor: 管理员登录、密码错误一次、后面需要输入验证码


1.2.9 2019-07-06
----------------

- refactor: `controller` 代码优化
    - `findOne` 优化 查询使用数组, 允许设置`$pk` 为 `model` 的唯一索引字段
    
    ```php
    $model = \yii\db\ActiveRecord::findOne([$this->pk => $data[$this->pk]]);
    
    // 之前 $model = \yii\db\ActiveRecord::findOne($data[$this->pk]);
    ```
    - `actionUpload` 优化，`UploadForm` 存在指定字段验证场景，才设置验证场景

1.2.8 2019-07-05
----------------

- fix: 视图里面不能直接引入使用 `use Yii` 

1.2.7 2019-07-04
----------------

- refactor: 删除`docs`目录
    - [说明文档](https://mylovegy.github.io/yii2-admin/)

1.2.6 2019-06-27
----------------
- refactor: 资源管理优化；项目里面不再包含前端资源，前端资源使用`bower` 管理
- refactor: 菜单信息不在缓存到本地
- refactor: `meTables` 优化
    - 添加可以配置搜索表单按钮选项, 默认选项，如果不用按钮 设置 render 为 `false`
    
    ```json
    {
        "search": {
          "render": true
        }
    }
    ```
    ![搜索添加按钮](https://mylovegy.github.io/yii2-admin/docs/images/metable-search.png)
    - 搜索表单回车事件优化

1.2.5 2019-06-25
----------------

- refactor: `meTables` 优化
    - 编辑添加 `autocomplete` 处理表单自动填充问题
    - 编辑和添加表单初始化的时候，重置验证信息
- refactor: `Logging`行为优化，直接继承`yii\base\Behavior`

1.2.4 2019-06-22
----------------

- fix: 配置自己的登录`user`问题修复
- add: 支持管理员通过邮箱登录后台

1.2.3 2019-06-21
----------------

- fix: 管理员切换跳转地址问题修复
- fix: 修复 `meTables` 如是编辑 `data` 中存在对象报错问题
- refactor: 部分代码重构
    - 基础控制器`Controller`提供导出`created_at`、`updated_at` 字段数据格式
    - 助手类`Helper` 数据导出使用`ArrayHelper::getValue`方法获取单列中指定字段数据，支持`key`为`user.name`的语法
    - add: 添加文件
            - `jinxing\admin\models\traits\AdminModelTrait` 用来替代 `AdminModel`
            - `jinxing\admin\models\traits\CreatedAtTrait` 用来处理 `created_at` 时间字段自动填充
            - `jinxing\admin\models\traits\TimestampTrait` 用来处理 `created_at`,`updated_at` 时间字段自动填充
    - delete: 删除文件
        - `jinxing\admin\models\AdminModel`基础后台`model`删除
    - update: 修改地方
        - `jinxing\admin\traits\JsonTrait`修改`json`返回格式如下
            
            ```json
            {
               "code": 0,
               "msg": "success",
               "data": []
            }
            ```
    
1.2.2 2019-06-18
----------------

- add: 模块生成支持跟多功能
    - 支持生成`model`
    - 支持生成`where`条件

1.2.1 2019-05-21
----------------

- bug: meTables.js 文件搜索表单 input 回车事件判断错误问题
- style: 打开标签关闭按钮样式调整  

1.2.0 2019-05-19
----------------

- change: 不在支持表达式查询的方式
    - remove: 删除控制器的 getDefaultWhere 方法
    - change: 视图中查询字段去掉表达式
    - change: 控制器上传文件之后处理方法改动
        ```php
        /**
         * @params string              $strFilePath   上传好的文件保存路径
         * @params string              $strFiled      上传文件字段名称
         * @params \yii\web\UploadFile $strObject     上传文件处理类
         * @return string 需要返回文件保存路径
         */
        public function afterUpload($strFilePath, $strField, $strObject)
        {
              return $strFilePath;
        }
    
        ```
    - add: 控制器添加 where 方法处理前端查询字段和查询方式
        
        ```php
              
            /**
             * 定义查询处理
             * @params array $params 请求的参数，可以不用接收
             * @return array 必须要返回一个数组
             */
            public function where()
            {
                return [
                    // where 这个是定义默认的查询条件,注意，需要是一个二维数组
                    'where' => [['=', 'type', 2]],
                    
                    // 之前的方式，还是支持的
                    'id' => '=',
                    'status' => '=',
                    
                    // 新的数组方式定义，
                    // 第一个元素表示：对应的字段，
                    // 第二个元素表示：处理的方式，同样支持字符串、数组、匿名函数处理方式
                    [['id', 'status'], '='],
                ];
            }
          
            ```
    
- change: meTable 的查询 input 添加回车查询数据

1.1.5   2019-05-10
------------------

- bug: 修复导出数据，查询条件无效问题   