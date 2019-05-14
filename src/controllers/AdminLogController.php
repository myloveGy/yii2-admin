<?php

namespace jinxing\admin\controllers;

use jinxing\admin\models\Admin;
use yii\helpers\ArrayHelper;

/**
 * Class AdminLogController 操作日志 执行操作控制器
 *
 * @package backend\controllers
 */
class AdminLogController extends Controller
{
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'jinxing\admin\models\AdminLog';

    /**
     * 查询处理
     *
     * @return array 返回数组
     */
    public function where()
    {
        $intUserId = ArrayHelper::getValue($this->module, 'userId');
        return [
            // 不是管理员默认查询条件
            'where' => $intUserId != Admin::SUPER_ADMIN_ID ? [['admin_id' => $intUserId]] : [],

            // 其他字段查询
            [['admin_name', 'action'], 'like'],
            ['index', '='],
        ];
    }

    public function actionIndex()
    {
        $admins = Admin::getAdmins();
        return $this->render('index', compact('admins'));
    }

    /**
     * 导出创建时间显示处理
     * @return array
     */
    public function getExportHandleParams()
    {
        return [
            'created_at' => function ($value) {
                return date('Y-m-d H:i:s', $value);
            }
        ];
    }
}
