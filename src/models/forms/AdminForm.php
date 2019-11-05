<?php

namespace jinxing\admin\models\forms;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use jinxing\admin\models\Admin;

/**
 * Login form
 */
class AdminForm extends \yii\base\Model
{
    /**
     * @var 验证码字段
     */
    public $verifyCode;

    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // 验证码
        $codeWhen = function () {
            return Yii::$app->session->get('validateCode');
        };

        return [
            // username and password are both required
            [['username', 'password'], 'required'],

            // 验证码验证
            [['verifyCode'], 'required', 'when' => $codeWhen],
            [['verifyCode'], 'captcha',
                'when'          => $codeWhen,
                'captchaAction' => Url::toRoute('default/captcha'),
                // 前端js 验证什么时候生效
                'whenClient'    => "function(attribute, value) {
                    return false;
                }",
            ],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'   => '管理员账号',
            'password'   => '管理员密码',
            'rememberMe' => '记住登录',
            'verifyCode' => '验证码',
        ]; 
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params    the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('admin', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @param string $user
     *
     * @return boolean whether the user is logged in successfully
     * @throws \yii\base\InvalidConfigException
     */
    public function login($user = 'admin')
    {
        if ($this->validate()) {
            return Yii::$app->get($user)->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * @return bool|Admin|null
     */
    public function getUser()
    {
        // 获取设置的model
        $modelClass = 'jinxing\admin\models\Admin';
        if ($user = Yii::$app->get(ArrayHelper::getValue(Yii::$app, 'controller.module.user'))) {
            $modelClass = $user->identityClass;
        }

        if ($this->_user === false) {
            $this->_user = $modelClass::findByUsername($this->username);
        }

        return $this->_user;
    }
}
