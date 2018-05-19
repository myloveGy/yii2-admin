<?php

namespace jinxing\admin\models;

use jinxing\admin\behaviors\UpdateBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Class AdminModel 后台处理有新增和修改字段的model
 * @package common\models
 */
class AdminModel extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value'              => new Expression('UNIX_TIMESTAMP()'),
            ],
            UpdateBehavior::className(),
        ];
    }
}
