<?php

namespace jinxing\admin\controllers;

use yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use jinxing\admin\models\Admin;
use jinxing\admin\helpers\Helper;
use jinxing\admin\models\AuthAssignment;

/**
 * Class AuthAssignmentController 角色分配 执行操作控制器
 * @package backend\controllers
 */
class AuthAssignmentController extends Controller
{
    /**
     * @var string 定义默认排序使用的字段
     */
    public $sort = 'created_at';

    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'jinxing\admin\models\AuthAssignment';

    public function where()
    {
        $intUserId = ArrayHelper::getValue($this->module, 'userId');
        return [
            // 不是管理员默认查询条件
            'where' => $intUserId != Admin::SUPER_ADMIN_ID ? [['user_id' => $intUserId]] : [],

            [['user_id', 'item_name'], 'in'],
        ];
    }

    /**
     * 显示视图
     * @return string
     */
    public function actionIndex()
    {
        // 查询出全部角色
        $arrRoles = Admin::getArrayRole(ArrayHelper::getValue($this->module, 'userId'));
        $admins   = Admin::getAdmins();

        // 载入视图
        return $this->render('index', [
            'admins'   => $admins,
            'arrRoles' => $arrRoles,
            'roles'    => Json::encode($arrRoles),
        ]);
    }

    /**
     * 处理新增数据
     *
     * @return mixed|string
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        if (empty($data['user_id']) || empty($data['item_name']) || !is_array($data['item_name'])) {
            return $this->error(201);
        }

        foreach ($data['item_name'] as $name) {
            $model            = new AuthAssignment();
            $model->item_name = $name;
            $model->user_id   = $data['user_id'];
            if ($model->save()) {
                $this->arrJson['msg'] .= $model->item_name . ':' . Yii::t('admin', 'Successfully processed');
            } else {
                $this->arrJson['msg'] .= $model->item_name . ': ';
                $this->arrJson['msg'] .= Helper::arrayToString($model->getErrors());
            }
        }

        return $this->success($data, 0);
    }

    /**
     * 删除分配信息
     *
     * @return mixed|string
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        $data = Yii::$app->request->post();
        if (empty($data['item_name']) || empty($data['user_id'])) {
            return $this->error(201);
        }
        
        // 通过传递过来的唯一主键值查询数据
        if (!$model = AuthAssignment::findOne(['item_name' => $data['item_name'], 'user_id' => $data['user_id']])) {
            $this->error(222);
        }

        // 删除数据失败
        if (!$model->delete()) {
            return $this->error(1004, Helper::arrayToString($model->getErrors()));
        }

        return $this->success($model);
    }
}
