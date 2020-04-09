<?php

namespace jinxing\admin\rules;

use yii;
use yii\rbac\Rule;
use jinxing\admin\models\Auth;
use jinxing\admin\models\Admin;

/**
 * Class AuthAssignmentRule 删除角色的规则(不能删除超级管理员的角色)
 * @package backend\rules
 */
class AuthAssignmentRule extends Rule
{
    /**
     * @var string 定义名称
     */
    public $name = 'auth-assignment';

    /**
     * 执行验证
     *
     * @param int|string     $user
     * @param \yii\rbac\Item $item
     * @param array          $params
     *
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        $strItemName = empty($params['item_name']) ? Yii::$app->request->post('item_name') : $params['item_name'];
        // 不管是谁，都不能删除超级管理员
        if ($strItemName === Auth::SUPER_ADMIN_NAME) {
            return false;
        }

        // 不是超级管理员，只能删除自己的
        if ($user !== Admin::SUPER_ADMIN_ID && Yii::$app->request->post('user_id', $user) != $user) {
            return false;
        }

        return true;
    }
}