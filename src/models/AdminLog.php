<?php

namespace jinxing\admin\models;

use jinxing\admin\helpers\Helper;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%admin_operate_logs}}".
 *
 * @property integer $id
 * @property string  $action
 * @property string  $index
 * @property string  $request
 * @property string  $response
 * @property string  $ip
 * @property integer $admin_id
 * @property string  $admin_name
 * @property integer $created_at
 */
class AdminLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_operate_logs}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id'], 'integer'],
            [['request', 'response'], 'string'],
            [['action'], 'string', 'max' => 64],
            [['index'], 'string', 'max' => 100],
            [['ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => '日志ID',
            'admin_id'   => '后台用户ID',
            'admin_name' => '后台用户名称',
            'action'     => '操作方法',
            'index'      => '数据唯一标识',
            'request'    => '请求参数',
            'response'   => '放回的数据',
            'ip'         => '请求IP',
            'created_at' => '创建时间',
        ];
    }
    
    /**
     * 添加日志
     *
     * @param \yii\base\Action $action
     * @param \yii\web\User    $user
     * @param array            $result
     *
     * @return bool
     */
    public static function create($action, $user, $result = [])
    {
        $key             = ArrayHelper::getValue($action, 'controller.pk', 'id');
        $log             = new AdminLog();
        $log->index      = Yii::$app->request->post($key, '');
        $log->request    = Json::encode(Yii::$app->request->post());
        $log->response   = Json::encode($result);
        $log->action     = $action->getUniqueId();
        $log->admin_id   = ArrayHelper::getValue($user, 'id');
        $log->admin_name = ArrayHelper::getValue($user, 'identity.username', '');
        $log->ip         = Helper::getIpAddress();
        $log->created_at = new Expression('UNIX_TIMESTAMP()');
        return $log->save();
    }
}
