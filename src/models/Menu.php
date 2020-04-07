<?php

namespace jinxing\admin\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use jinxing\admin\helpers\Tree;
use jinxing\admin\models\traits\AdminModelTrait;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property integer $id
 * @property string  $pid
 * @property string  $menu_name
 * @property string  $icons
 * @property string  $url
 * @property integer $status
 * @property integer $sort
 * @property integer $created_at
 * @property integer $created_id
 * @property integer $updated_at
 * @property integer $updated_id
 */
class Menu extends ActiveRecord
{
    use AdminModelTrait;

    /**
     * 状态
     */
    const STATUS_ACTIVE = 1; // 启用
    const STATUS_DELETE = 0; // 关闭

    /**
     * @var array 新增时候填写的权限
     */
    public $auth = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'status', 'sort'], 'integer'],
            [['menu_name', 'status'], 'required'],
            ['url', 'filter', 'filter' => function ($value) {
                return trim($value, '/');
            }, 'when'                  => function ($model) {
                return $model->url;
            }],
            [['menu_name', 'icons', 'url'], 'string', 'max' => 50],
            ['auth', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'pid'        => '上级分类',
            'menu_name'  => '栏目名称',
            'icons'      => '图标',
            'url'        => '访问地址',
            'status'     => '状态',
            'sort'       => '排序字段',
            'created_at' => '创建时间',
            'created_id' => '创建用户',
            'updated_at' => '修改时间',
            'updated_id' => '修改用户',
        ];
    }

    /**
     * 修改之后的处理
     *
     * @param bool  $insert
     * @param array $changedAttributes
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert && $this->url && $this->auth) {
            $url = explode('/', $this->url);
            array_pop($url);
            if ($url) {
                (new Auth())->batchInsert($this->auth, implode('/', $url) . '/', $this->menu_name);
            }
        }

        return true;
    }

    /**
     *  获取用户导航栏信息
     *
     * @param int $intUserId 用户ID
     *
     * @return mixed
     */
    public static function getUserMenus($intUserId)
    {
        // 管理员登录
        if ($intUserId == Admin::SUPER_ADMIN_ID) {
            $menus = self::find()
                ->where(['status' => self::STATUS_ACTIVE])
                ->orderBy(['sort' => SORT_ASC])
                ->asArray()
                ->all();
        } else {
            // 其他用户登录成功获取权限
            if ($permissions = Yii::$app->getAuthManager()->getPermissionsByUser($intUserId)) {
                $menus = self::getMenusByPermissions($permissions);
            }
        }

        // 没有菜单返回空
        if (!isset($menus) || empty($menus)) {
            return [];
        }

        // 生成导航信息
        return (new Tree([
            'array'        => $menus,
            'childrenName' => 'child',
            'parentIdName' => 'pid',
        ]))->getTreeArray(0);
    }

    /**
     * 通过权限获取导航栏目
     *
     * @param array $permissions 权限信息
     *
     * @return array
     */
    public static function getMenusByPermissions($permissions)
    {
        // 查询导航栏目
        if ($menus = static::findMenus(['url' => array_keys($permissions), 'status' => static::STATUS_ACTIVE])) {
            $sort = ArrayHelper::getColumn($menus, 'sort');
            array_multisort($sort, SORT_ASC, $menus);
        }

        return $menus;
    }

    /**
     *
     *
     * @param integer|array $where 查询条件
     *
     * @return array
     */
    public static function findMenus($where)
    {
        if ($parents = static::find()->where($where)->asArray()->indexBy('id')->all()) {
            $arrParentIds = [];
            foreach ($parents as $value) {
                if ($value['pid'] != 0 && !isset($parents[$value['pid']])) {
                    $arrParentIds[] = $value['pid'];
                }
            }

            if ($arrParentIds) {
                if ($arrParents = static::findMenus(['id' => $arrParentIds])) {
                    $parents += $arrParents;
                }
            }
        }

        return $parents;
    }

    /**
     * 获取jstree 需要的数据
     *
     * @param array $array    数据信息
     * @param array $arrHaves 需要选中的数据
     *
     * @return array
     */
    public static function getJsMenus($array, $arrHaves)
    {
        if (empty($array) || !is_array($array)) {
            return [];
        }

        $arrReturn = [];
        foreach ($array as $value) {
            $array = [
                'text'  => $value['menu_name'],
                'id'    => $value['id'],
                'data'  => $value['url'],
                'state' => [],
            ];

            $array['state']['selected'] = in_array($value['url'], $arrHaves);
            $array['icon']              = $value['pid'] == 0 || !empty($value['children']) ? 'menu-icon fa fa-list orange' : false;
            if (!empty($value['children'])) {
                $array['children'] = self::getJsMenus($value['children'], $arrHaves);
            }

            $arrReturn[] = $array;
        }

        return $arrReturn;
    }
}
