<?php

namespace jinxing\admin\web;

use yii\web\AssetBundle;

/**
 * Class TableAsset
 *
 * @package jinxing\admin\web
 */
class TableAsset extends AssetBundle
{
    /**
     * @var string 定义使用的目录路径
     */
    public $basePath = '@bower/jinxing-tables/dist/';

    /**
     * @var string 定义使用的目录路径
     */
    public $sourcePath = '@bower/jinxing-tables/dist/';

    /**
     * @var array 定义默认加载的js
     */
    public $js = [
        'meTables.min.js',
    ];

    /**
     * 重写注入资源函数
     *
     * @param \yii\web\View $view
     *
     * @return void|AssetBundle
     * @throws \yii\base\InvalidConfigException
     */
    public static function register($view)
    {
        $view->registerAssetBundle(ValidateAsset::className());
        $view->registerAssetBundle(DataTablesAsset::className());
        return parent::register($view);
    }
}