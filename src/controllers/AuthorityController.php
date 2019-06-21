<?php

namespace jinxing\admin\controllers;

use Yii;
use yii\helpers\Json;
use jinxing\admin\models\Auth;
use jinxing\admin\models\AuthRule;

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
            'where' => [['type' => Auth::TYPE_PERMISSION]],
            [['name', 'description', 'rule_name'], 'like'],
        ];
    }

    /**
     * 权限页面显示操作
     * @return string
     */
    public function actionIndex()
    {
        // 查询出全部的规则
        $arrRules = ['' => Yii::t('admin', 'please choose')];
        if ($rules = AuthRule::find()->asArray()->all()) {
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
