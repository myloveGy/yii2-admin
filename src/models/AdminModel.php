<?php

namespace jinxing\admin\models;

use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use jinxing\admin\behaviors\UpdateBehavior;

/**
 * Class AdminModel 后台处理有新增和修改字段的model
 * @package common\models
 */
class AdminModel extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
            UpdateBehavior::className(),
        ];
    }
}
