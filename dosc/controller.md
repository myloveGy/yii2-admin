About controller description
----------------------------

Because jinxing\admin\controllers\Controller inherits from [yii\web\Controller](http://www.yiichina.com/doc/api/2.0/yii-web-controller), some configuration of parent class can be used

### Public attributes
Attributes | Types of | Defaults | Description
:----------|:---------|:---------|:-----------
$modelClass| string   | -------  | [The data model object used](http://www.yiichina.com/doc/api/2.0/yii-db-activerecord)  
$pk        | string   | id       | Data table primary key field
$sort      | string   | id       | Default sort field
$strategy  | string   | DataTables or JqGrid| Use data processing class, corresponding to front-end data table processing
$uploadFromClass|string| jinxing\admin\models\forms\UploadForm| Upload file processing model 
$strUploadPath|string | ./uploads/| Upload file save path

### Action method
Method           | Description          | Related service methods
:----------------|:------------         |:----------------------------
actionIndex()    | Display view file    |
actionSearch()   | Search method to provide data for the front end| [where() Provide query conditions]()、[getQuery() Provide query object]()、[afterSearch() Data processing after query]()
actionCreate()   | Create data          |
actionUpdate()   | Change the data      |
actionDelete()   | Delete Data          |
actionDeleteAll()| Delete multiple data |
actionEditable() | Edit data inline     |
actionUpload()   | File Upload          | [ afterUpload() File upload processing]()
actionExport()   | Data output          | [where() Provide query conditions](#where)、[getQuery() Provide query object]()、[getExportHandleParams() Provide data export processing parameters ]()

### The public method

Method  | Description
getPk() | Get the primary key name

### where() public method 
return an array
Determine the query condition processing

<table>
    <tr>
        <td colspan="3">public function where() </td>
    </tr>
    <tr>
        <td>$params</td>
        <td> array </td>
        <td> The query condition information passed from the front end can be used without adding this parameter. </td>
    </tr>
</table>

Need to return an array, determine if the front-end query parameters, and finally spliced to db query array
E.g:
```
    public function where()
    {
        return [
            // Simple expression query
            'id'   => '=',
            'name' => 'like',
            
            /**
             * a complicated query
             * field    Query field
             * and      Query expression
             * func     Query value processing function
             */
            'username' => [
                'field' => 'name', 
                'and'   => 'like',
                'func'  => function ($value) {
                    return $value.'123';
                }
            ],
            
            // Use anonymous functions
            'email' => function ($value) {
                return ['like', 'email', $value];
            }
           
        ];
    }
```
When all queries are used, the above configuration will eventually generate the following query conditions:
```php
    $params = Yii::$app->request->post();
    $db->where([
        'and',
        ['=', 'id',  $params['id']],
        ['like', 'name', $params['name']],
        ['like', 'name', $params['username'].'123'],
        ['like', 'email', $params['email']]
    ]);
```

If it is a more complex query, such as linked table query, you need to override the [getQuery()](#getquery($where)-protected-method) method.

### getQuery($where) protected method
> return [yii\db\Query](http://www.yiichina.com/doc/api/2.0/yii-db-query) or [yii\db\ActiveRecord](http://www.yiichina.com/doc/api/2.0/yii-db-activerecord) 

<table>
    <tr>
        <td colspan="3">protected function getQuery() </td>
    </tr>
    <tr>
        <td>$where</td>
        <td> array </td>
        <td> Process the completed query array with front-end request parameters </td>
    </tr>
</table>

The default Query Query object returned by $modelClass, if it is a complex query, the method will be re-opened

```php 
    protected function getQuery($where)
    {
        return (new Query())->from('user')->leftJoin('actrive', 'user.id=active.user_id')->where($where);
    }

```

### afterSearch(&$array) protected method

通过引用传入查询 getQuery() 查询出来的数据比如日期时间的格式化：

```
    protected function afterSearch(&$array) 
    {
        foreach ($array as &$value) {
            $value['created_at']  = date('Y-m-d H:i:s', $value['created_at']);
            $value['status_name'] = $value['status'] == 10 ? '开启' : '关闭'; 
        }
        
        unset($value);
    }
```

### findOne($data = []) protected method

> return [yii\db\ActiveRecord](http://www.yiichina.com/doc/api/2.0/yii-db-activerecord)

通过请求参数查询对象，没有查询到会设置错误，返回false

### afterUpload($object, &$strFilePath, $strField) protected method

> return boolean

文件上传之后的处理，主要是对图片的裁剪和修改

### getExportHandleParams() protected method
> return an array

主要用来对导出数据做格式化的处理(需要处理的数据)，返回一个数组：

```
    protected function getExportHandleParams()
    {
        return [
            // 只能是需要格式化的数据，然后对应一个匿名函数处理
            'created_at' => function ($value) {
                return date('Y-m-d H:i:s', $value);
            },
            'status' => function ($value) {
                return $value == 10 ? '开启' : '关闭';
            }
        ];
    }
```




[←  Module configuration description](./config.md) | [Table configuration instructions →](./me-table.md)