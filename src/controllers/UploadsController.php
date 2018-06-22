<?php

namespace jinxing\admin\controllers;

/**
 * Class UploadsController 上传文件 执行操作控制器
 *
 * @package backend\controllers
 */
class UploadsController extends Controller
{
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'jinxing\admin\models\Uploads';

    /**
     * 文件导出数据格式化
     *
     * @return array|mixed
     */
    public function getExportHandleParams()
    {
        $array['created_at'] = $array['updated_at'] = function ($value) {
            return date('Y-m-d H:i:s', $value);
        };

        return $array;
    }
}
