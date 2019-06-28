<?php

namespace jinxing\admin\web;

/**
 * Main backend application asset bundle.
 */
class DataTablesAsset extends AppAsset
{
    /**
     * @var array 加载的公共css
     */
    public $css = [];

    /**
     * @var array 定义默认加载的js
     */
    public $js = [
        'js/jquery.dataTables.min.js',
        'js/jquery.dataTables.bootstrap.js',
    ];
}
