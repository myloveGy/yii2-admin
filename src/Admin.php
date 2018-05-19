<?php

namespace jinxing\admin;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii\helpers\Url;
use yii\web\UnauthorizedHttpException;
use \jinxing\admin\traits\JsonTrait;

/**
 * admin module definition class
 */
class Admin extends \yii\base\Module
{
    use JsonTrait;

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'jinxing\admin\controllers';

    /**
     * @var string 定义使用布局
     */
    public $layout = '@jinxing/admin/views/layouts/main';

    /**
     * @var string 指定用户
     */
    public $user = 'admin';

    /**
     * @var array 不验证的控制器名称
     */
    public $allowControllers = ['default'];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        Yii::$app->assetManager->bundles     = [
            // 去掉自己的bootstrap 资源
            'yii\bootstrap\BootstrapAsset' => [
                'css' => []
            ],
            // 去掉自己加载的Jquery
            'yii\web\JqueryAsset'          => [
                'sourcePath' => null,
                'js'         => [],
            ],
        ];
        Yii::$app->errorHandler->errorAction = ArrayHelper::getValue(Yii::$app->params, 'admin_rule_prefix') . '/default/error';
    }

    /**
     * @param \yii\base\Action $action
     *
     * @return bool|\yii\console\Response|\yii\web\Response
     * @throws UnauthorizedHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeAction($action)
    {
        // 不验证权限和用户登录
        if (in_array($action->controller->id, $this->allowControllers)) {
            return parent::beforeAction($action);
        }

        // 验证用户登录
        if (Yii::$app->get($this->user)->isGuest) {
            return Yii::$app->response->redirect(Url::toRoute('site/login'));
        }

        // 验证权限
        $url = ArrayHelper::getValue(Yii::$app->params, 'admin_rule_prefix') . '/' . $action->controller->id . '/' . $action->id;
        if (Yii::$app->get($this->user)->can($url) && Yii::$app->getErrorHandler()->exception === null) {
            // 没有权限AJAX返回
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->content = Json::encode($this->error(216));
                return false;
            }

            throw new UnauthorizedHttpException('对不起，您现在还没获得该操作的权限!');
        }

        return true;
    }
}
