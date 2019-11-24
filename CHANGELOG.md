Yii2 Ace Admin 后台扩展更新记录
=============================

1.0.17 2019-11-24
-----------------

- fix: 修复`modules`中`beforeAction`返回错误问题

1.0.16 2019-05-21
-----------------

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

- change: 控制器 where 方法定义可以和 model 定义验证规则方式一致
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

- bug: 修复数据库迁移模块前缀处理错误问题    
- style: 标签关闭按钮样式修改
