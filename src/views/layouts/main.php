<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use jinxing\admin\web\AdminAsset;
use jinxing\admin\helpers\Helper;

AdminAsset::register($this);
$url = Helper::getAssetUrl();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <title><?= Yii::$app->name . Html::encode($this->title) ?></title>
    <meta name="description" content="3 styles with inline editable feature"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head(); ?>
    <style>
        .dataTables_filter > form > label, .dataTables_length > label {
            margin: 3px 0;
        }

        .dataTables_filter > form > button {
            margin-top: -3px;
        }

        .dataTables_filter > form > button:last-child {
            margin-left: 8px !important;
        }

        form div.table-search-col {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        ::-webkit-scrollbar-track {
            background-color: #F5F5F5;
        }

        ::-webkit-scrollbar {
            width: 6px;
            background-color: #F5F5F5;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #bbd4e5;
        }

        label.required:before {
            display: inline-block;
            margin-right: 4px;
            color: #f5222d;
            font-size: 14px;
            font-family: SimSun, sans-serif;
            line-height: 1;
            content: "*";
        }
    </style>
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
    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->
    <!--[if lte IE 8]>
    <script src="<?= $url ?>/js/html5shiv.min.js"></script>
    <script src="<?= $url ?>/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="no-skin">
<?php $this->beginBody() ?>
<!-- /section:basics/navbar.layout -->
<div class="main-container" id="main-container">
    <!--主要内容信息-->
    <div class="main-content">
        <div class="page-content">
            <!--主要内容信息-->
            <div class="page-content-area">
                <div class="page-header">
                    <h1><?= $this->title; ?></h1>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--尾部信息-->
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
                <span class="bigger-120">
                    <?= ArrayHelper::getValue(Yii::$app->params, 'companyName', '<span class="bolder"> jinxing.liu@qq.com </span> <a href="https://github.com/myloveGy/yii2-ace-admin" target="_blank">Yii2 Admin</a> 项目 &copy; 2016-2020') ?>
                </span>
            </div>
        </div>
    </div>

    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
    </a>
</div>
<!-- 公共的JS文件 -->
<!-- basic scripts -->
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
    try {
        window.parent.removeOverlay();
    } catch (e) {
    }
    if ('ontouchstart' in document.documentElement) document.write("<script src='<?=$url?>/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>
<script src="<?= $url ?>/js/bootstrap.min.js"></script>
<!-- page specific plugin scripts -->
<!--[if lte IE 8]>
<script src="<?= $url ?>/js/excanvas.min.js"></script>
<![endif]-->
<?php $this->endBody() ?>
<?= isset($this->blocks['javascript']) ? $this->blocks['javascript'] : '' ?>
</body>
</html>
<?php $this->endPage() ?>

