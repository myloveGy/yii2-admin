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
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use jinxing\admin\behaviors\UpdateBehavior;

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
            TimestampBehavior::className(),

            // created_id 和 updated_id 字段的处理
            UpdateBehavior::className(),
        ];

    }
}