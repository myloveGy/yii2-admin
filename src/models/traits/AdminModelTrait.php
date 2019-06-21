<?php
/**
 *
 * Timestamp.php
 *
 * Author: jinxing.liu
 * Create: 2019-06-21 15:16
 * Editor: created by PhpStorm
 */

namespace jinxing\admin\models\traits;

use Yii;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * Trait AdminModelTrait 定义处理时间戳
 *
 * @package jinxing\admin\models\traits
 */
trait AdminModelTrait
{
    /**
     * 定义行为处理时间
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            // 时间处理
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],

            // created_id 和 update_id 修改
            'admin'     => [
                'class'              => TimestampBehavior::className(),
                'value'              => Yii::$app->controller->module->getUserId(),
                'createdAtAttribute' => 'created_id',
                'updatedAtAttribute' => 'updated_id',
            ],
        ];
    }
}