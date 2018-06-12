About controller description
----------------------------

Because jinxing\admin\controllers\Controller inherits from [yii\web\Controller](http://www.yiichina.com/doc/api/2.0/yii-web-controller), some configuration of parent class can be used

#### Public attributes
Attributes | Types of | Defaults | Description
:----------|:---------|:---------|:-----------
$modelClass| string   | -------  | [The data model object used](http://www.yiichina.com/doc/api/2.0/yii-db-activerecord)  
$pk        | string   | id       | Data table primary key field
$sort      | string   | id       | Default sort field
$strategy  | string   | DataTables or JqGrid| Use data processing class, corresponding to front-end data table processing
$uploadFromClass|string| jinxing\admin\models\forms\UploadForm| Upload file processing model 
$strUploadPath|string | ./uploads/| Upload file save path

#### Action method
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
actionExport()   | Data output          | [where() Provide query conditions]()、[getQuery() Provide query object]()、[getExportHandleParams() Provide data export processing parameters ]()

#### The public method

Method | Description



[←  Module configuration description](./config.md) | [Table configuration instructions →](./me-table.md)