Yii2-admin extension Change Log
===============================

1.2.5 2019-06-25
----------------
-- refactor: meTables 编辑添加 autocompo


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