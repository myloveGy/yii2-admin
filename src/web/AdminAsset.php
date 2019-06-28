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
        'js/layer/layer.min.js',
    ];
}