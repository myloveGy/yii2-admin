<?php

namespace jinxing\admin\behaviors;

use yii\web\User;
use yii\di\Instance;
use yii\base\Behavior;
use yii\base\Controller;
use jinxing\admin\models\AdminLog;

/**
 * Class LoggingBehavior 日志记录
 *
 * @package jinxing\admin\behaviors
 */
class Logging extends Behavior
{
    /**
     * @var string 使用的用户组件
     */
    public $user = 'user';

    /**
     * @var array 需要记录日志的action
     */
    public $needLogActions = ['create', 'update', 'delete', 'delete-all', 'editable', 'upload'];

    public function events()
    {
        return [
            Controller::EVENT_AFTER_ACTION => 'beforeAction',
        ];
    }

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
    }
}