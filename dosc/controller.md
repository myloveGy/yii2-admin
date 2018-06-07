About controller description
----------------------------

Because jinxing\admin\controllers\Controller inherits from [yii\web\Controller](http://www.yiichina.com/doc/api/2.0/yii-web-controller), some configuration of parent class can be used

#### Public attributes
Attributes | Types of | Defaults | description
:----------|:---------|:---------|:-----------
$modelClass| string   | -------  | [The data model object used](http://www.yiichina.com/doc/api/2.0/yii-db-activerecord)  
$pk        | string   | id       | Data table primary key field
$sort      | string   | id       | Default sort field
$strategy  | string   | DataTables or JqGrid| Use data processing class, corresponding to front-end data table processing
$uploadFromClass|string| jinxing\admin\models\forms\UploadForm| Upload file processing model 
$strUploadPath|string | ./uploads/| Upload file save path