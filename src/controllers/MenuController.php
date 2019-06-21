<?php

namespace jinxing\admin\controllers;

use jinxing\admin\models\Admin;
use jinxing\admin\models\Menu;
use jinxing\admin\helpers\Tree;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class MenuController 导航栏目信息控制器
 * @package backend\controllers
 */
class MenuController extends Controller
{
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'jinxing\admin\models\Menu';

    /**
     * 处理查询条件
     *
     * @return array
     */
    public function where()
    {
        return [
            [['id', 'pid', 'status'], '='],
            [['menu_name', 'url'], 'like']
        ];
    }

    /**
     * 首页显示
     * @return string
     */
    public function actionIndex()
    {
        // 查询父级分类信息
        $parents = Menu::find()->select(['id', 'menu_name', 'pid'])->where([
            'status' => Menu::STATUS_ACTIVE,
        ])->indexBy('id')->asArray()->all();

        // 处理显示select
        $strOptions = (new Tree(['array' => $parents, 'parentIdName' => 'pid']))
            ->getTree(0, '<option value="{id}" data-pid="{pid}"> {extend_space}{menu_name} </option>');

        return $this->render('index', [
            'admins'  => Admin::getAdmins(),
            'options' => $strOptions,
            'parents' => Json::encode(ArrayHelper::map($parents, 'id', 'menu_name'))
        ]);
    }
}
