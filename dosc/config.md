About the configuration of the module
-------------------------------------

Because jinxing\admin\module inherits from [yii\base\Module](http://www.yiichina.com/doc/api/2.0/yii-base-module), some configuration of parent class can be used

##### The following only describes the custom configuration

Configuration item |Variable type | Defaults     | Configuration instructions
:------------------|:-------------| :------------| :--------------------------
user               | string       | admin        | [Users used by the module](http://www.yiichina.com/doc/api/2.0/yii-web-user) 
allowControllers   | array        | ['default']  | Controllers that do not need to verify permissions
frameNumberSize    | integer      | 8            | Allow up to a few iFrame
verifyAuthority    | bool         | true         | Do you need permission verification?
leftTopButtons     | array        | [...]        | Left top button group
userLinks          | array        | [...]        | User related link group

##### Use these configurations

```php
return [
    'components' => [
        'modules' => [
            'admin' => [
                'class' => 'jinxing\admin\Admin',
                
                // Make use of that kind of user
                'user' => 'admin'
                
                // Modify the default configuration
                'Configuration name' => 'The configured value',
                // E.g
                'verifyAuthority' => false
                ...
            ]
            ...
        ],
        ...
    ],
]
```

[Controller description →](./controller.md)