<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/2 0002
 * Time: 下午 4:01
 */

namespace jinxing\admin\behaviors;

use Yii;

/**
 * Class AccessControl 权限验证
 *
 * @package jinxing\admin\behaviors
 */
class AccessControl extends \yii\filters\AccessControl
{
    /**
     * @var array 默认需要验证的action
     */
    public $defaultActions = [
        'index', 'search', 'create',
        'update', 'delete', 'delete-all',
        'edit', 'editable', 'upload'
    ];

    public function getRules()
    {
        $action = Yii::$app->controller->action;
        if (in_array($action->id, $this->defaultActions)) {
            return [
                [
                    'actions'     => $this->defaultActions,
                    'allow'       => true,
                    'permissions' => [
                        $action->getUniqueId(),
                    ],
                ]
            ];
        }

        return [
            [
                'actions' => [$action->id],
                'allow'   => true,
                'roles'   => ['@'],
            ],
        ];
    }

    public function init()
    {
        $this->rules = $this->rules ?: $this->getRules();
        parent::init();
    }
}