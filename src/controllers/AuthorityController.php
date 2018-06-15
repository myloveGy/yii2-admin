<?php

namespace jinxing\admin\controllers;

use jinxing\admin\models\AuthRule;
use jinxing\admin\models\Auth;
use Yii;
use yii\helpers\Json;

/**
 * Class AuthorityController 权限管理类
 * @package backend\controllers
 */
class AuthorityController extends RoleController
{
    /**
     * 查询参数配置
     *
     * @return array
     */
    public function where()
    {
        return [
            'name'        => 'like',
            'description' => 'like',
            'rule_name'   => 'like',
            'where'       => [['=', 'type', Auth::TYPE_PERMISSION]],
        ];
    }

    /**
     * 权限页面显示操作
     * @return string
     */
    public function actionIndex()
    {
        // 查询出全部的规则
        $rules    = AuthRule::find()->asArray()->all();
        $arrRules = ['' => Yii::t('admin', 'please choose')];
        if ($rules) {
            foreach ($rules as $value) {
                if ($value['data']) {
                    $tmp = unserialize($value['data']);
                    if ($tmp) {
                        $value['data'] = get_class($tmp);
                    }
                }

                $arrRules[$value['name']] = $value['name'] . ' - ' . $value['data'];
            }
        }

        // 载入试图
        return $this->render('index', [
            'type'  => Auth::TYPE_PERMISSION, // 权限类型
            'rules' => Json::encode($arrRules) // 所有规则
        ]);
    }
}
