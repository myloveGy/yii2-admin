<?php
/**
 * Created by PhpStorm.
 * Date: 2016/7/18
 * Time: 19:17
 */

use yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use jinxing\admin\helpers\Helper;
use jinxing\admin\web\AppAsset;

AppAsset::register($this);

$url         = Helper::getAssetUrl();
$renderPaths = Yii::$app->controller->module->loginOtherRenderPaths;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="description" content="overview &amp; stats"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <title><?= ArrayHelper::getValue(Yii::$app->params, 'projectTitle', 'Yii2 Admin') ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head(); ?>
    <!-- ace styles -->
    <link rel="stylesheet" href="<?= $url ?>/css/ace.min.css" id="main-ace-style"/>
    <!--[if lte IE 9]>
    <link rel="stylesheet" href="<?= $url ?>/css/ace-part2.min.css"/>
    <![endif]-->
    <!--[if lte IE 9]>
    <link rel="stylesheet" href="<?= $url ?>/css/ace-ie.min.css"/>
    <![endif]-->
    <!-- inline styles related to this page -->
    <!-- ace settings handler -->
    <script src="<?= $url ?>/js/ace-extra.min.js"></script>
    <script type="text/javascript">
        if (window.parent && window.parent.addIframe && window.parent.authHeight && typeof window.parent.addIframe === "function") {
            window.parent.location.reload()
        }
    </script>
    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->
    <!--[if lte IE 8]>
    <script src="<?= $url ?>/js/html5shiv.min.js"></script>
    <script src="<?= $url ?>/js/respond.min.js"></script>
    <![endif]-->

    <!-- 公共的JS文件 -->
    <!--[if !IE]> -->
    <script type="text/javascript">
        window.jQuery || document.write("<script src='<?=$url?>/js/jquery.min.js'>" + "<" + "/script>");
    </script>
    <!-- <![endif]-->
    <!--[if IE]>
    <script type="text/javascript">
        window.jQuery || document.write("<script src='<?= $url ?>/js/jquery1x.min.js'>" + "<" + "/script>");
    </script>
    <![endif]-->
    <script type="text/javascript">
        if ('ontouchstart' in document.documentElement) document.write("<script src='<?=$url?>/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
    </script>
    <script src="<?= $url ?>/js/bootstrap.min.js"></script>
    <!--[if lte IE 8]>
    <script src="<?= $url ?>/js/excanvas.min.js"></script>
    <![endif]-->

    <style>
        body {
            background: radial-gradient(200% 100% at bottom center, #f7f7b6, #e96f92, #75517d, #1b2947);
            background: radial-gradient(220% 105% at top center, #1b2947 10%, #75517d 40%, #e96f92 65%, #f7f7b6);
            background-attachment: fixed;
            overflow: hidden;
        }

        @keyframes rotate {
            0% {
                transform: perspective(400px) rotateZ(20deg) rotateX(-40deg) rotateY(0);
            }
            100% {
                transform: perspective(400px) rotateZ(20deg) rotateX(-40deg) rotateY(-360deg);
            }
        }

        .stars {
            transform: perspective(500px);
            transform-style: preserve-3d;
            position: absolute;
            bottom: 0;
            perspective-origin: 50% 100%;
            left: 50%;
            animation: rotate 90s infinite linear;
        }

        .star {
            width: 2px;
            height: 2px;
            background: #F7F7B6;
            position: absolute;
            top: 0;
            left: 0;
            transform-origin: 0 0 -300px;
            transform: translate3d(0, 0, -300px);
            backface-visibility: hidden;
        }

        .login-layout .widget-box {
            background: none;
        }
    </style>
</head>
<body class="login-layout light-login">
<div class="stars"></div>
<?php $this->beginBody() ?>
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="center">
                        <h1>
                            <span class="red" id="id-text2">
                                <?= ArrayHelper::getValue(Yii::$app->params, 'projectName', 'Yii2 Admin') ?>
                            </span>
                        </h1>
                        <h4 class="blue" id="id-company-text">
                            <?= ArrayHelper::getValue(Yii::$app->params, 'loginCompanyName', 'jinxing.liu@qq.com Yii2 Admin 项目') ?>
                        </h4>
                    </div>

                    <div class="space-6"></div>

                    <div class="position-relative">
                        <div id="login-box" class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header blue lighter bigger">
                                        <i class="ace-icon fa fa-coffee green"></i>
                                        请输入您的登录信息
                                    </h4>
                                    <div class="space-6"></div>
                                    <!--登录表单-->
                                    <?= $content ?>
                                </div>

                                <?php if (count($renderPaths) > 0) : ?>
                                    <div class="toolbar clearfix">
                                        <div>
                                            <a href="#" data-target="#forgot-box" class="forgot-password-link">
                                                <i class="ace-icon fa fa-arrow-left"></i>
                                                忘记密码
                                            </a>
                                        </div>

                                        <div>
                                            <a href="#" data-target="#signup-box" class="user-signup-link">
                                                注册
                                                <i class="ace-icon fa fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!--忘记密码-->
                        <?php if ($forgot = ArrayHelper::getValue($renderPaths, 'forgot')) : ?>
                            <?= $this->render($forgot) ?>
                        <?php endif; ?>

                        <!--注册管理员-->
                        <?php if ($register = ArrayHelper::getValue($renderPaths, 'register')) : ?>
                            <?= $this->render($register) ?>
                        <?php endif; ?>
                    </div><!-- /.position-relative -->

                    <div class="navbar-fixed-top align-right">
                        <br/>
                        &nbsp;
                        <a id="btn-login-dark" class="active" href="#">星空</a>
                        &nbsp;
                        <span class="blue">/</span>
                        &nbsp;
                        <a id="btn-login-blur" href="#">蓝色</a>
                        &nbsp;
                        <span class="blue">/</span>
                        &nbsp;
                        <a id="btn-login-light" href="#">明亮</a>
                        &nbsp; &nbsp; &nbsp;
                    </div>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.main-content -->
</div><!-- /.main-container -->

<script src="<?= $url ?>/js/jquery.cookie.js"></script>
<script type="text/javascript">
    jQuery(function ($) {

        $(document).on('click', '.toolbar a[data-target]', function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('.widget-box.visible').removeClass('visible');//hide others
            $(target).addClass('visible');//show target
        });

        $('#btn-login-dark').on('click', function (e) {
            $('body').attr('class', 'login-layout');
            $('#id-text2').attr('class', 'white');
            $('#id-company-text').attr('class', 'blue');
            $.cookie("login-theme", "login-dark", {expires: 365})
            $(".stars").show();
            e.preventDefault();
        });

        $('#btn-login-light').on('click', function (e) {
            $('body').attr('class', 'login-layout light-login');
            $('#id-text2').attr('class', 'red');
            $('#id-company-text').attr('class', 'blue');
            $.cookie("login-theme", "login-light", {expires: 365})
            $(".stars").hide();
            e.preventDefault();
        });

        $('#btn-login-blur').on('click', function (e) {
            $('body').attr('class', 'login-layout blur-login');
            $('#id-text2').attr('class', 'white');
            $('#id-company-text').attr('class', 'light-blue');
            $.cookie("login-theme", "login-blur", {expires: 365})
            $(".stars").show();
            e.preventDefault();
        });

        var theme = $.cookie("login-theme") || "login-light"
        $("#btn-" + theme).trigger("click")

        var stars = 900; /*星星的密集程度，数字越大越多*/
        var $stars = $(".stars");
        var r = 800; /*星星的看起来的距离,值越大越远,可自行调制到自己满意的样子*/
        for (var i = 0; i < stars; i++) {
            var $star = $("<div/>").addClass("star");
            $stars.append($star);
        }

        $(".star").each(function () {
            var cur = $(this);
            var s = 0.2 + (Math.random() * 1);
            var curR = r + (Math.random() * 300);
            cur.css({
                transformOrigin: "0 0 " + curR + "px",
                transform: " translate3d(0,0,-" + curR + "px) rotateY(" + (Math.random() * 360) + "deg) rotateX(" + (Math.random() * -50) + "deg) scale(" + s + "," + s + ")"
            })
        })
    });
</script>
<?php $this->endBody() ?>
<?= $this->blocks['javascript'] ?>
</body>
</html>
<?php $this->endPage() ?>
