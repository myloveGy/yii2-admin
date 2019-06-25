<?php

namespace jinxing\admin\behaviors;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Class UpdateBehavior 给表字段补充 created_id 和 updated_id 字段信息
 *
 * @package jinxing\admin\behaviors
 */
class UpdateBehavior extends TimestampBehavior
{
    /**
     * @var string 指定字段 created 字段
     */
    public $createdAtAttribute = 'created_id';

    /**
     * @var string 指定 updated 字段
     */
    public $updatedAtAttribute = 'updated_id';

    /**
     * 获取值
     *
     * @param $event
     *
     * @return int|mixed
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return Yii::$app->controller->module->getUserId();
        }

        return parent::getValue($event);
    }
}