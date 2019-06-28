<?php

namespace jinxing\admin\web;

/**
 * Class AdminAsset 后台资源加载类
 * @package backend\assets
 */
class AdminAsset extends AppAsset
{
    /**
     * @var array 定义默认加载的js
     */
    public $js = [
        'js/ace-elements.min.js',
        'js/ace.min.js',
        'js/common/tools.min.js',
        'js/layer/layer.js',
    ];

    /**
     * 注册 meTables 所需的js
     *
     * @param \yii\web\View $view 视图
     */
    public static function meTablesRegister($view)
    {
        // 加载资源
        $view->registerAssetBundle(ValidateAsset::className());
        $view->registerAssetBundle(DataTablesAsset::className());
        $view->registerAssetBundle(TableAsset::className());
    }
}