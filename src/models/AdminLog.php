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
     * 类型
     */
    const TYPE_CREATE = 1; // 创建
    const TYPE_UPDATE = 2; // 修改
    const TYPE_DELETE = 3; // 删除
    const TYPE_OTHER  = 4;  // 其他
    const TYPE_UPLOAD = 5;  // 上传

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
     * 获取类型说明
     *
     * @param null $type
     *
     * @return array|mixed|null
     */
    public static function getTypeDescription($type = null)
    {
        $mixReturn = [
            self::TYPE_CREATE => '创建',
            self::TYPE_CREATE => '创建',
            self::TYPE_UPDATE => '修改',
            self::TYPE_DELETE => '删除',
            self::TYPE_OTHER  => '其他',
            self::TYPE_UPLOAD => '上传',
        ];

        if ($type !== null) {
            $mixReturn = isset($mixReturn[$type]) ? $mixReturn[$type] : null;
        }

        return $mixReturn;
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
