<?php

namespace jinxing\admin;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\UnauthorizedHttpException;
use jinxing\admin\traits\JsonTrait;

/**
 * admin module definition class
 */
class Module extends yii\base\Module
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
     * @var int 允许开启iFrame 个数
     */
    public $frameNumberSize = 8;

    /**
     * @var bool 权限验证
     */
    public $verifyAuthority = true;

    /**
     * @var string 默认首页action
     */
    public $defaultAction = 'default/system';

    /**
     * @var bool 左边头部按钮
     */
    public $leftTopButtons = [
        [
            'id'        => 'my-arrange',
            'url'       => 'arrange/calendar',
            'title'     => '我的日程',
            'icon'      => 'fa fa-calendar',
            'btn-class' => 'btn-success'
        ],
        [
            'id'        => '',
            'url'       => '',
            'title'     => '',
            'icon'      => 'fa fa-pencil',
            'btn-class' => 'btn-info'
        ],
        [
            'id'        => 'my-info',
            'url'       => 'admin/view',
            'title'     => '个人信息',
            'icon'      => 'glyphicon glyphicon-user',
            'btn-class' => 'btn-warning'
        ],
        [
            'id'        => 'index',
            'url'       => 'default/system',
            'title'     => '登录信息',
            'icon'      => 'fa fa-cogs',
            'btn-class' => 'btn-danger'
        ]
    ];

    /**
     * @var array 用户点击相关按钮
     */
    public $userLinks = [
        ['title' => '登录信息', 'id' => 'index', 'url' => 'default/system', 'icon' => 'fa fa-desktop'],
        ['title' => '个人信息', 'id' => 'my-info', 'url' => 'admin/view', 'icon' => 'fa fa-user'],
        ['title' => '我的日程', 'id' => 'my-arrange', 'url' => 'arrange/calendar', 'icon' => 'fa fa-calendar']
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // 资源处理
        Yii::$app->assetManager->bundles = [
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

        // 设置错误处理页面
        Yii::$app->errorHandler->errorAction = $this->getUniqueId() . '/default/error';
        if (!isset(Yii::$app->i18n->translations['admin'])) {
            Yii::$app->i18n->translations['admin'] = [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath'       => '@jinxing/admin/messages'
            ];
        }
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
            return Yii::$app->response->redirect(Url::toRoute('default/login'));
        }

        // 验证权
        if ($this->verifyAuthority && !Yii::$app->get($this->user)->can($action->getUniqueId())) {
            // 没有权限AJAX返回
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->content = Json::encode($this->error(216));
                return false;
            }

            throw new UnauthorizedHttpException('对不起，您现在还没获得该操作的权限!');
        }

        return true;
    }

    /**
     * 获取登录用户
     *
     * @return null|object
     * @throws \yii\base\InvalidConfigException
     */
    public function getUser()
    {
        return Yii::$app->get($this->user);
    }

    /**
     * 获取登录用户ID
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getUserId()
    {
        return $this->getUser()->id;
    }

    /**
     * @return null|object
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function getAdmin()
    {
        return $this->getUser();
    }
}
