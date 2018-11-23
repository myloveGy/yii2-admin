<?php

namespace jinxing\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use jinxing\admin\models\Menu;
use jinxing\admin\models\Auth;
use jinxing\admin\helpers\Helper;
use yii\web\Application;

/**
 * Class ModuleController 模块生成测试文件
 * @package backend\controllers
 */
class ModuleController extends Controller
{
    /**
     * 首页显示
     *
     * @return string
     * @throws \yii\base\NotSupportedException
     */
    public function actionIndex()
    {
        // 查询到库里面全部的表
        $tables = Yii::$app->db->getSchema()->getTableSchemas();
        $tables = ArrayHelper::map($tables, 'name', 'name');
        return $this->render('index', [
            'tables'         => $tables,
            'is_application' => $this->module->module instanceof Application
        ]);
    }

    /**
     * 第一步接收标题和数据表数据生成表单配置信息
     *
     * @return mixed|string
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        // 1、接收参数并验证
        $request  = Yii::$app->request;
        $strTitle = $request->post('title'); // 标题
        $strTable = $request->post('table'); // 数据库表
        if (empty($strTable) || empty($strTitle)) {
            return $this->error(201);
        }

        // 获取表信息
        $db     = Yii::$app->db;
        $tables = Yii::$app->db->getSchema()->getTableSchemas();
        $tables = ArrayHelper::getColumn($tables, 'name');
        if (empty($tables) || !in_array($strTable, $tables)) {
            return $this->error(217);
        }

        // 查询表结构信息
        $arrTables = $db->createCommand('SHOW FULL COLUMNS FROM `' . $strTable . '`')->queryAll();
        if (empty($arrTables)) {
            return $this->error(218);
        }

        return $this->success($this->createForm($arrTables));
    }

    /**
     * 第二步生成预览HTML文件
     * @return mixed|string
     * @throws \yii\base\Exception
     */
    public function actionUpdate()
    {
        // 1、获取验证参数
        $request     = Yii::$app->request;
        $attr        = $request->post('attr');
        $table       = $request->post('table');
        $primary_key = $request->post('pk');
        if (empty($table) || empty($attr)) {
            return $this->error(201);
        }

        $name = str_replace(Yii::$app->db->tablePrefix, '', $table);
        if (empty($name)) {
            return $this->error(217);
        }

        // 拼接字符串
        $strCName = Helper::strToUpperWords($name) . 'Controller.php';
        $name     = str_replace('_', '-', $name);
        $basePath = '@' . str_replace(['\\', '/controllers'], ['/', ''], $this->module->module->controllerNamespace);
        $strCName = $basePath . '/controllers/' . $strCName;
        $strVName = $basePath . '/views/' . $name . '/index.php';
        if (!($this->module->module instanceof Application)) {
            $name = $this->module->module->id . '/' . $name;
        }

        return $this->success([
            'html'        => highlight_string($this->createView($attr, $request->post('title'), '', $primary_key), true),
            'file'        => [$strVName, file_exists(Yii::getAlias($strCName))],
            'controller'  => [$strCName, file_exists(Yii::getAlias($strCName))],
            'primary_key' => $primary_key,
            'auth_name'   => $name,
            'menu_name'   => $name
        ]);
    }

    /**
     * 第三步开始生成文件
     * @return mixed|string
     * @throws \yii\base\Exception
     */
    public function actionProduce()
    {
        // 接收参数
        $request     = Yii::$app->request;
        $attr        = $request->post('attr');       // 表单信息
        $table       = $request->post('table');      // 操作表
        $title       = $request->post('title');      // 标题信息
        $html        = $request->post('html');       // HTML 文件名
        $php         = $request->post('controller'); // PHP  文件名
        $auth        = (int)$request->post('auth');  // 生成权限
        $menu        = (int)$request->post('menu');  // 生成导航
        $allow       = (int)$request->post('allow'); // 允许文件覆盖
        $primary_key = $request->post('primary_key'); // 主键
        $auth_name   = $request->post('auth_prefix');
        $menu_name   = $request->post('menu_prefix');

        // 第一步验证参数：
        if (empty($attr) || empty($table) || empty($html) || empty($php)) {
            return $this->error(201);
        }

        // 表名字不能为空
        $name = str_replace(Yii::$app->db->tablePrefix, '', $table);
        if (empty($name)) {
            return $this->error(217);
        }

        // 获取文件目录
        $view_path       = Yii::getAlias($html);
        $controller_path = Yii::getAlias($php);
        $str_name        = str_replace('_', '-', $name);

        // 验证文件不存在
        if ($allow !== 1 && (file_exists($view_path) || file_exists($controller_path))) {
            return $this->error(219);
        }

        // 生成权限
        if ($auth == 1 && !$this->createAuth($str_name, $title, $auth_name)) {
            return $this->error(223);
        }

        // 生成导航栏目
        if ($menu == 1 && !$this->createMenu($str_name, $title, $menu_name)) {
            return $this->error(224);
        }

        // 生成视图文件
        $this->createView($attr, $title, $view_path, $primary_key);

        // 生成控制器
        $this->createController($name, $title, $controller_path, $primary_key);

        // 返回数据
        return $this->success(Url::toRoute(['/' . $str_name . '/index']));
    }

    /**
     * 生成权限操作
     * @access private
     *
     * @param  string  $prefix    前缀名称
     * @param  string  $title     标题
     * @param   string $auth_name 权限名称
     *
     * @return bool
     *
     * @throws \yii\base\Exception
     */
    private function createAuth($prefix, $title, $auth_name = '')
    {
        $name = $auth_name ?: $prefix;
        $name = trim($name, '/') . '/';
        $auth = new Auth();
        return $auth->batchInsert(array_keys($auth->array_default_auth), $name, $title);
    }

    /**
     * 生成导航栏信息
     *
     * @access private
     *
     * @param  string $name      权限名称
     * @param  string $title     导航栏目标题
     * @param  string $menu_name 栏目名称
     *
     * @return bool
     */
    private function createMenu($name, $title, $menu_name = '')
    {
        if (Menu::findOne(['menu_name' => $title])) {
            return true;
        }

        $url              = $menu_name ?: $name;
        $model            = new Menu();
        $model->menu_name = $title;
        $model->pid       = 0;
        $model->icons     = 'menu-icon fa fa-globe';
        $model->url       = trim($url, '/') . '/index';
        $model->status    = 1;
        return $model->save(false);
    }

    /**
     * 生成视图文件信息
     *
     * @param $array
     *
     * @return string
     */
    private function createForm($array)
    {
        $primary_key = '';
        foreach ($array as $value) {
            if (ArrayHelper::getValue($value, 'Key') == 'PRI') {
                $primary_key = ArrayHelper::getValue($value, 'Field');
                break;
            }
        }

        $strHtml = '<div class="alert alert-info">
    <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>填写配置表格信息!</strong>
</div>';
        $table   = '';
        foreach ($array as $value) {
            $key     = $value['Field'];
            $title   = ArrayHelper::getValue($value, 'Comment') ?: $value['Field'];
            $options = [];
            if (ArrayHelper::getValue($value, 'Null') == 'NO') {
                $options[] = 'required: true';
            }

            if (stripos($value['Type'], 'int(') !== false) {
                $options[] = 'number: true';
            }

            if (stripos($value['Type'], 'varchar(') !== false) {
                $sLen      = trim(str_replace('varchar(', '', $value['Type']), ')');
                $options[] = 'rangeLength: "[2, ' . $sLen . ']"';
            }

            // 主键修改隐藏
            if ($key == $primary_key) {
                $options = [];
                $select  = '<option value="hidden" selected="selected">hidden</option>';
            } else {
                $select = '<option value="text" selected="selected">text</option>';
            }

            $other   = stripos($value['Field'], '_at') !== false ? 'meTables.dateTimeString' : '';
            $options = implode(', ', $options);
            $table   .= <<<HTML
<tr>
    <td>{$key}</td>
    <td><input type="text" name="attr[{$key}][title]" value="{$title}" required="required" /></td>
    <td>
        <select class="is-hide" name="attr[{$key}][edit]">
            <option value="1" selected="selected">开启</option>
            <option value="0" >关闭</option>
        </select>
        <select name="attr[{$key}][type]">
            {$select}
            <option value="text">text</option>
            <option value="hidden" >hidden</option>
            <option value="select">select</option>
            <option value="radio">radio</option>
            <option value="password">password</option>
            <option value="textarea">textarea</option>
        </select>
        <input type="text" name="attr[{$key}][options]" value='{$options}'/>
    </td>
    <td class="text-center">
        <select name="attr[{$key}][search]">
            <option value="1">开启</option>
            <option value="0" selected="selected">关闭</option>
        </select>
    </td>
    <td class="text-center">
        <select name="attr[{$key}][bSortable]">
            <option value="1" >开启</option>
            <option value="0" selected="selected">关闭</option>
        </select>
    </td>
    <td class="text-center">
        <input type="text" name="attr[{$key}][createdCell]" value="{$other}" />
    </td>
    <td class="text-center">
        <button class="btn btn-danger btn-xs" type="button" onclick="$(this).parent().parent().remove()">删除</button>
    </td>
</tr>
HTML;
        }

        return '<table class="table table-striped table-bordered table-hover">
     <thead>
     <tr>
        <th class="text-center">字段</th>
        <th class="text-center">标题</th>
        <th class="text-center">编辑</th>
        <th class="text-center">搜索</th>
        <th class="text-center">排序</th>
        <th class="text-center">回调</th>
        <th class="text-center">操作</th>
    </tr>
    <tbody>
    ' . $table . '
</tbody>
</thead>       
</table>' . $strHtml . '<input type="hidden" name="pk" value="' . $primary_key . '">';
    }

    /**
     * 生成预览HTML文件
     * @access private
     *
     * @param  array  $array       接收表单配置文件
     * @param  string $title       标题信息
     * @param  string $path        文件地址
     *
     * @param string  $primary_key 主键名称
     *
     * @return string 返回 字符串
     * @throws \yii\base\Exception
     */
    private function createView($array, $title, $path = '', $primary_key = 'id')
    {
        $strHtml = '';
        if ($array) {
            foreach ($array as $key => $value) {
                $arrayOptions = [
                    "title: \"{$value['title']}\"",
                    "data: \"{$key}\"",
                ];

                // 编辑
                if ($value['edit'] == 1) {
                    $edit = ['type: "' . $value['type'] . '"'];
                    if ($options = trim($value['options'], ',')) {
                        $edit[] = $options;
                    }

                    $arrayOptions[] = 'edit: {' . implode(', ', $edit) . '}';
                }

                // 搜索
                if ($value['search'] == 1) {
                    $arrayOptions[] = 'search: {type: "text"}';
                }

                // 排序
                if ($value['bSortable'] == 0) {
                    $arrayOptions[] = 'sortable: false';
                }

                // 回调
                if (!empty($value['createdCell'])) {
                    $arrayOptions[] = "createdCell: {$value['createdCell']}";
                }

                $strHtml .= "\n\t\t\t\t\t{\n\t\t\t\t\t\t" . trim(implode(",\n\t\t\t\t\t\t", $arrayOptions), ', ') . "\n\t\t\t\t\t},";
            }
        }

        $strHtml            = trim($strHtml, ',');
        $primary_key_config = $primary_key && $primary_key != 'id' ? 'pk: "' . $primary_key . '",' : '';
        $sHtml              = <<<html
<?php

use jinxing\admin\widgets\MeTable;
// 定义标题和面包屑信息
\$this->title = '{$title}';
?>
<?=MeTable::widget()?>
<?php \$this->beginBlock('javascript') ?>
<script type="text/javascript">
    var m = meTables({
        title: "{$title}",
        {$primary_key_config}
        table: {
            aoColumns: [
                {$strHtml}
            ]       
        }
    });
    
    /**
    meTables.fn.extend({
        // 显示的前置和后置操作
        beforeShow: function(data, child) {
            return true;
        },
        afterShow: function(data, child) {
            return true;
        },
        
        // 编辑的前置和后置操作
        beforeSave: function(data, child) {
            return true;
        },
        afterSave: function(data, child) {
            return true;
        }
    });
    */

     \$(function(){
         m.init();
     });
</script>
<?php \$this->endBlock(); ?>
html;
        // 生成文件
        if (!empty($path)) {
            FileHelper::createDirectory(dirname($path));
            file_put_contents($path, $sHtml);
            return $strWhere;
        }

        return $sHtml;
    }

    /**
     * 生成控制器文件
     * @access private
     *
     * @param  string $name        控制器名
     * @param  string $title       标题
     * @param  string $path        文件名
     * @param string  $primary_key 主键名称
     *
     * @return void
     */
    private function createController($name, $title, $path, $primary_key = 'id')
    {
        $strFile  = trim(strrchr($path, '/'), '/');
        $strName  = trim($strFile, '.class.php');
        $strModel = str_replace('controllers', 'models', $this->module->module->controllerNamespace) . '\\' . Helper::strToUpperWords($name);
        $pk       = $primary_key && $primary_key != 'id' ? 'protected $pk = \'' . $primary_key . '\';' : '';

        // 上层模块是 Application,那么只要基础module 下的基础控制器就好了
        if ($this->module->module instanceof Application) {
            $use = '';
        } else {
            $use = 'use jinxing\admin\controllers\Controller;';
        }

        // 模板
        $strControllers = <<<Html
<?php

namespace {$this->module->module->controllerNamespace};

{$use}

/**
 * Class {$strName} {$title} 执行操作控制器
 * @package {$this->module->module->controllerNamespace}
 */
class {$strName} extends Controller
{
    {$pk}
    
    /**
     * @var string 定义使用的model
     */
    public \$modelClass = '{$strModel}';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        // 需要自己配置查询字段对应的处理表达式 ['username' => 'like']
        return [];
    }
}

Html;

        file_put_contents($path, $strControllers);
    }
}