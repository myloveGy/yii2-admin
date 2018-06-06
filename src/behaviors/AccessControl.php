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
    public function getRules()
    {
        $actions = [
            'index', 'search',
            'create', 'update', 'delete', 'delete', 'delete-all',
            'edit', 'editable', 'upload'
        ];

        $action = Yii::$app->controller->action;
        if (in_array($action->id, $actions)) {
            return [
                [
                    'actions'     => $actions,
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
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
    }

    public function init()
    {
        $this->rules = $this->rules ?: $this->getRules();
        parent::init();
    }
}