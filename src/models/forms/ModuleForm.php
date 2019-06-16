<?php


namespace jinxing\admin\models\forms;


use jinxing\admin\helpers\Helper;
use Yii;

class ModuleForm extends \yii\base\Model
{
    /**
     * @var string 表主键
     */
    public $primaryKey;

    /**
     * @var string 表名称
     */
    public $table;

    /**
     * @var array 提交信息
     */
    public $attr;

    /**
     * @var 视图文件
     */
    public $view;

    /**
     * @var string 控制文件
     */
    public $controller;

    /**
     * @var string 模型文件
     */
    public $model;

    /**
     * @var string 标题
     */
    public $title;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['table', 'attr', 'primaryKey', 'controller', 'model', 'view', 'title'], 'required'],
        ];
    }

    /**
     * @return mixed 获取名称
     */
    public function getName()
    {
        return str_replace(Yii::$app->db->tablePrefix, '', $this->table);
    }

    public function getTableClassName()
    {
        return Helper::strToUpperWords($this->getName());
    }

    public function getViewPath()
    {
        if (empty($this->view)) {
            return null;
        }

        return Yii::getAlias($this->view);
    }

    public function getControllerPath()
    {
        return Yii::getAlias($this->controller);
    }

    public function getControllerInfo()
    {
        return $this->handle($this->controller);
    }

    public function getModelPath()
    {
        return Yii::getAlias($this->model);
    }

    public function getModelInfo()
    {
        return $this->handle($this->model);
    }

    private function handle($aliasName)
    {
        $array     = explode('/', str_replace('@', '', $aliasName));
        $className = str_replace('.php', '', array_pop($array));
        $namespace = implode('\\', $array);
        return [$className, $namespace, $namespace . '\\' . $className];
    }
}