<?php

namespace jinxing\admin\controllers;

use Yii;
use jinxing\admin\models\Auth;
use jinxing\admin\traits\JsonTrait;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use jinxing\admin\helpers\Helper;
use jinxing\admin\models\Menu;
use jinxing\admin\models\Admin;
use jinxing\admin\models\forms\AdminForm;
use yii\helpers\ArrayHelper;

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
                'user'  => ArrayHelper::getValue($this->module, 'user'),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow'   => true,
                    ],
                    [
                        'actions' => [
                            'logout', 'index', 'system',
                            'update', 'create', 'switch-login'
                        ],
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
     *
     * 管理员登录欢迎页
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = false;
        // 获取用户导航栏信息
        $user  = ArrayHelper::getValue($this->module, 'admin.identity');
        $menus = Menu::getUserMenus($user->id);
        return $this->render('index', compact('user', 'menus'));
    }

    /**
     * 显示首页系统信息
     *
     * @return string
     */
    public function actionSystem()
    {
        // 用户信息
        return $this->render('system', [
            'yii'    => 'Yii ' . Yii::getVersion(),                         // Yii 版本
            'upload' => ini_get('upload_max_filesize'),             // 上传文件大小,
            'user'   => ArrayHelper::getValue($this->module, 'admin.identity')
        ]);
    }

    /**
     * 切换账号登录
     *
     * @return \yii\web\Response
     */
    public function actionSwitchLogin()
    {
        // 数据不存在
        if (!$array = Helper::getSwitchLoginInfo(Yii::$app->request->get('token'))) {
            return $this->redirect(Yii::$app->request->getReferrer());
        }

        // 查询用户是否
        if (!$afterUserInfo = Admin::findOne(ArrayHelper::getValue($array, 'after_user_id'))) {
            Yii::$app->session->setFlash('error', Yii::t('admin', '切换登录用户不存在'));
            return $this->redirect(Yii::$app->request->getReferrer());
        }

        /* @var $admin \yii\web\User */
        /* @var  $user Admin */
        $admin = ArrayHelper::getValue($this->module, 'admin');
        $user  = $admin->identity;
        // 验证用户权限
        if (
            $user->id == Admin::SUPER_ADMIN_ID || // 超级管理员随便切
            in_array($afterUserInfo->id, [Admin::SUPER_ADMIN_ID, $user->created_id]) ||
            $admin->can(Auth::SUPER_ADMIN_NAME) // 拥有超级管理员权限
        ) {
            // 退出当前用户
            Yii::$app->cache->delete(Menu::CACHE_KEY . $user->id);
            $admin->logout();

            // 记录之前登录的用户
            Yii::$app->session->set('before_user', $user->toArray(['id', 'username']));

            // 登录新增用户
            $admin->login($afterUserInfo, 0);
            Menu::setNavigation($afterUserInfo->id);
            return $this->goBack(); // 到首页去
        }

        Yii::$app->session->setFlash('error', Yii::t('admin', '抱歉，没有权限进行该操作'));
        return $this->redirect(Yii::$app->request->getReferrer());
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
        $user         = ArrayHelper::getValue($this->module, 'admin');

        // 不是游客直接跳转到首页
        if (!$user->isGuest) {
            return $this->goHome();
        }

        $model = new AdminForm();
        if ($model->load(Yii::$app->request->post()) && $model->login(ArrayHelper::getValue($this->module, 'user'))) {
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
     */
    public function actionLogout()
    {
        $user = ArrayHelper::getValue($this->module, 'admin');
        if ($admin = ArrayHelper::getValue($user, 'identity')) {
            $admin->last_time = time();
            $admin->last_ip   = Helper::getIpAddress();
            $admin->save();
        }

        Yii::$app->cache->delete(Menu::CACHE_KEY . $user->id);
        $user->logout();
        return $this->goHome();
    }
}
