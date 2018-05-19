<?php

namespace jinxing\admin\behaviors;

use yii;
use yii\base\InvalidCallException;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * Class UpdateBehavior
 * @author liujx<821901008@qq.com>
 * @package common\behaviors
 */
class UpdateBehavior extends AttributeBehavior
{
    /**
     * 定义创建用户字段名
     * @var string
     */
    public $createdIdAttribute = 'created_id';

    /**
     * 定义修改用户字段名
     * @var string
     */
    public $updatedIdAttribute = 'updated_id';

    /**
     * 定义只
     * @var integer
     */
    public $value;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdIdAttribute, $this->updatedIdAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedIdAttribute,
            ];
        }
    }

    /**
     * 获取值
     * @param yii\base\Event $event
     * @return int|mixed|string
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return Yii::$app->controller->module->getUserId();
        }

        return parent::getValue($event);
    }

    /**
     * 修改值
     * @param $attribute
     */
    public function touch($attribute)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Updating the created_id is not possible on a new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }
}