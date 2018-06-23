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
        $where = [
            'action'     => 'like',
            'index'      => '=',
            'admin_name' => 'like',
            'where'      => [],
        ];

        $intUserId = ArrayHelper::getValue($this->module, 'userId');
        if ($intUserId != Admin::SUPER_ADMIN_ID) {
            $where['where'] = [['=', 'admin_id', $intUserId]];
        }

        return $where;
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
