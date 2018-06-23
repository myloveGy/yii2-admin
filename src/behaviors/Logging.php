<?php
/**
 *
 * LoggingBehavior.php
 *
 * Author: jinxing.liu@verystar.cn
 * Create: 2018/6/6 09:38
 * Editor: created by PhpStorm
 */

namespace jinxing\admin\behaviors;

use yii\web\User;
use yii\di\Instance;
use yii\base\ActionFilter;
use jinxing\admin\models\AdminLog;

/**
 * Class LoggingBehavior 日志记录
 *
 * @package jinxing\admin\behaviors
 */
class Logging extends ActionFilter
{
    /**
     * @var User|array|string|false the user object representing the authentication status or the ID of the user application component.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     * Starting from version 2.0.12, you can set it to `false` to explicitly switch this component support off for the filter.
     */
    public $user = 'user';

    /**
     * @var array 需要记录日志的action
     */
    public $needLogActions = ['create', 'update', 'delete', 'delete-all', 'editable', 'upload'];

    /**
     * 初始化赋值
     * 
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->user !== false) {
            $this->user = Instance::ensure($this->user, User::className());
        }
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed            $result
     *
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        if (in_array($action->id, $this->needLogActions)) {
            AdminLog::create($action, $this->user, $result);
        }

        return parent::afterAction($action, $result);
    }
}