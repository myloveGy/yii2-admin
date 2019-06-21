<?php

namespace jinxing\admin\controllers;

/**
 * Class AuthRuleController 规则管理 执行操作控制器
 * @package backend\controllers
 */
class AuthRuleController extends Controller
{
    /**
     * @var string 定义使用默认排序字段
     */
    public $sort = 'name';

    /**
     * @var string 定义主键
     */
    public $pk = 'name';

    /**
     * 定义使用的model
     * @var string
     */
    public $modelClass = 'jinxing\admin\models\AuthRule';

    public function where()
    {
        return [
            [['name'], 'like']
        ];
    }

    /**
     * 搜索之后的数据处理
     *
     * @param mixed $array
     */
    public function afterSearch(&$array)
    {
        foreach ($array as &$value) {
            if ($value['data']) {
                $tmp = unserialize($value['data']);
                if (is_object($tmp)) {
                    $value['data'] = get_class($tmp);
                }
            }
        }
    }
}
