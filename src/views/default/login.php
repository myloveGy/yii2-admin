<?php

use yii\helpers\Html;
use yii\captcha\Captcha;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model jinxing\admin\models\forms\AdminForm */

$this->title                   = Yii::t('admin', 'Login');
$this->params['breadcrumbs'][] = $this->title;
$this->registerCss('div.field-adminform-verifycode {
            width: 50%;
            float: left;
            margin-bottom: 0;
        }

        .image-code {
            float: right;
            display: block;
        }');
?>
<?php $form = ActiveForm::begin(); ?>
<fieldset>
    <label class="block clearfix">
        <span class="block input-icon input-icon-right">
            <?= $form->field($model, 'username')
                ->textInput(['placeholder' => Yii::t('admin', 'loginUsernamePlaceholder')])
                ->label(false) ?>
            <i class="ace-icon fa fa-user"></i>
        </span>
    </label>

    <label class="block clearfix">
        <span class="block input-icon input-icon-right">
            <?= $form->field($model, 'password')
                ->passwordInput(['placeholder' => Yii::t('admin', 'loginPasswordPlaceholder')])
                ->label(false) ?>
            <i class="ace-icon fa fa-lock"></i>
        </span>
    </label>
    <?php if (Yii::$app->session->get('validateCode')) : ?>
        <label class="block clearfix">
        <span class="block input-icon input-icon-right">
            <?= $form->field($model, 'verifyCode')
                ->textInput([
                    'placeholder' => Yii::t('admin', '验证码'),
                    'class'       => 'input-code',
                ])
                ->label(false) ?>
            <?= Captcha::widget([
                'name'          => 'verify-code',
                'captchaAction' => 'default/captcha',
                'imageOptions'  => [
                    'id'    => 'verify-code',
                    'title' => '换一个',
                    'alt'   => '换一个',
                    'class' => 'image-code',
                    'style' => 'cursor:pointer;',
                ],
                'template'      => '{image}',
            ]) ?>
        </span>
        </label>
    <?php endif; ?>
    <div class="space"></div>
    <div class="clearfix">
        <label class="inline">
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
        </label>
        <?= Html::submitButton('登录', ['class' => 'btn bg-olive btn-block width-35 pull-right btn btn-sm btn-primary']) ?>
    </div>
</fieldset>
<?php ActiveForm::end(); ?>
<?php $this->beginBlock('javascript'); ?>
<script>
    $(function () {
        $("#verify-code").trigger('click')
    })
</script>
<?php $this->endBlock(); ?>
