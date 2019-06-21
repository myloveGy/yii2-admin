<?php

namespace jinxing\admin\behaviors;

use Yii;
use yii\behaviors\TimestampBehavior;

class UpdateBehavior extends TimestampBehavior
{
    public $createdAtAttribute = 'created_id';

    public $updatedAtAttribute = 'updated_id';

    protected function getValue($event)
    {
        if ($this->value === null) {
            return Yii::$app->controller->module->getUserId();
        }

        return parent::getValue($event);
    }
}