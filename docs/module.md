模块配置说明
==========

## 模块 `jinxing\admin\Module`类的可配置项

[可以直接参考类文件](https://github.com/myloveGy/yii2-admin/blob/master/src/Module.php)

`Yii2`的配置都是通过数组 `class`字段指定使用类，其他`key`指定类属性的方式去配置的；
所以可以直接参考类有哪些属性，就可以在配置文件中指定

例如: 
```php
$config = [
    'modules'             => [
        'admin' => [
             // 指定模块使用 后台模块类
            'class' => 'jinxing\admin\Module',
            
            // 指定模块类 $user 属性值， 确定模块使用的 登录用户组件名称
            'user'  => 'user',
        ],
    ],
];
```

### 可配置属性列表

该列表只是列出的 `jinxing\admin\Module` 自定义的属性，其父类的属性 `yii\base\Module` [参考](https://www.yiichina.com/doc/api/2.0/yii-base-module)

| 名称 | 类型 | 默认值 | 说明 |
|:--------|:-----|:----|:------|
|`$user`|`string`|`admin`|使用的登录用户组件名称|
|`$allowControllers`|`array`|`['default']`|不需要验证权限的控制器|
|`$frameNumberSize`|`int`|8|后台界面允许开启iframe的个数,超过会隐藏|
|`$verifyAuthority`|`boolean`|`true`|是否需要验证权限|
|`$defaultAction`|`string`|`default/system`|登录成功欢迎页面的路由|
|`$leftTopButtons`|`array`| `[...]`|后台界面左侧按钮配置 |
|`$userLinks`|`array`|`[...]`|后台界面登录用户右侧按钮配置|


## `Yii2`高级版后台配置模板

## `Yii2`基础班后台配置模板 

## 其他配置说明

### `$user` 的说明
