<?php

namespace jinxing\admin\controllers;

use jinxing\admin\traits\JsonTrait;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use jinxing\admin\helpers\Helper;
use jinxing\admin\models\Menu;
use jinxing\admin\models\Admin;
use jinxing\admin\models\forms\AdminForm;

/**
 * Class DefaultController 后台首页处理
 * @package backend\controllers
 */
class DefaultController extends \yii\web\Controller
{
    use JsonTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user'  => $this->module->user,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow'   => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'system', 'grid', 'get-data', 'update', 'create', 'test'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'jinxing\admin\actions\ErrorAction',
            ],
        ];
    }

    /**
     * 管理员登录欢迎页
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $this->layout = false;
        // 获取用户导航栏信息
        $user                            = Yii::$app->get($this->module->user);
        $menus                           = Menu::getUserMenus($user->id);
        Yii::$app->view->params['user']  = $user->identity;
        Yii::$app->view->params['menus'] = $menus ? $menus : [];
        return $this->render('@jinxing/admin/views/default/index');
    }

    /**
     * 显示首页系统信息
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSystem()
    {
        // 用户信息
        Yii::$app->view->params['user'] = Yii::$app->get($this->module->user)->identity;
        return $this->render('@jinxing/admin/views/default/system', [
            'yii'    => 'Yii ' . Yii::getVersion(),                      // Yii 版本
            'upload' => ini_get('upload_max_filesize'),      // 上传文件大小
        ]);
    }

    /**
     * 后台管理员登录
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        $this->layout = 'login.php';
        $user         = Yii::$app->get($this->module->user);

        // 不是游客直接跳转到首页
        if (!$user->isGuest) {
            return $this->goHome();
        }

        $model = new AdminForm();
        if ($model->load(Yii::$app->request->post()) && $model->login($this->module->user)) {
            // 生成缓存导航栏文件
            Menu::setNavigation($user->id);
            return $this->goBack(); // 到首页去
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 后台管理员退出
     *
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogout()
    {
        $user     = Yii::$app->get($this->module->user);
        $admin_id = $user->id;
        // 用户退出修改登录时间
        $admin = Admin::findOne($admin_id);
        if ($admin) {
            $admin->last_time = time();
            $admin->last_ip   = Helper::getIpAddress();
            $admin->save();
        }

        Yii::$app->cache->delete(Menu::CACHE_KEY . $admin_id);
        $user->logout();
        return $this->goHome();
    }
}
