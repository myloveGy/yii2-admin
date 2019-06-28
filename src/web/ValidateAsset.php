<?php

namespace jinxing\admin\web;

/**
 * Class ValidateAsset jquery.validate 验证插件
 * 
 * @package jinxing\admin\web
 */
class ValidateAsset extends AppAsset
{
    /**
     * @var array 加载的公共css
     */
    public $css = [];

    /**
     * @var array 定义默认加载的js
     */
    public $js = [
        'js/jquery.validate.min.js',
        'js/common/validate.message.min.js',
    ];
}
