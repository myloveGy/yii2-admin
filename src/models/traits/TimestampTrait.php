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

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * Trait TimestampTrait 定义处理时间戳
 *
 * @package jinxing\admin\models\traits
 */
trait TimestampTrait
{
    /**
     * 定义行为处理时间
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}