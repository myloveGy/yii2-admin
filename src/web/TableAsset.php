<?php

namespace jinxing\admin\web;

use Yii;
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
    public $basePath = '@bower/jinxing-tables/';

    /**
     * @var string 定义使用的目录路径
     */
    public $sourcePath = '@bower/jinxing-tables/';

    /**
     * @var array 定义默认加载的js
     */
    public $js = [
        'meTables.min.js',
    ];
}