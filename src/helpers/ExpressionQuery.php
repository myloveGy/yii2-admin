<?php
/**
 *
 * ExpressionQuery.php
 *
 * Author: jinxing.liu@verystar.cn
 * Create: 2018/11/13 15:16
 * Editor: created by PhpStorm
 */

namespace jinxing\admin\helpers;

use yii\helpers\ArrayHelper;

/**
 * Class ExpressionQuery 表达式查询支持
 *
 * @package jinxing\admin\helpers
 *
 * ```php
 * $where = (new ExpressionQuery())->getFilterCondition([
 *      'username:like' => 'username',
 *      'name:not_like' => 'name',
 *      'status:in'     => '1,2',
 *      'type:in'       => [1, 2],
 *      'age:not_null'  => null,
 *      'sex'           => 1,
 *      'created_at:gt' => 12
 * ]);
 *
 * $where = [
 *      'and',
 *      ['LIKE', 'username', 'username'],
 *      ['NOT LIKE', 'name', 'name'],
 *      ['in', 'status', [1, 2]],
 *      ['in', 'type', [1, 2]],
 *      ['IS NOT NULL', 'age'],
 *      ['=', 'sex', 1],
 *      ['>', 'created_at', 12]
 * ];
 * ```
 */
class ExpressionQuery
{
    /**
     * @var array 支持查询的表达式
     */
    protected $expression = [
        'eq'          => '=',
        'neq'         => '!=',
        'ne'          => '!=',
        'gt'          => '>',
        'egt'         => '>=',
        'gte'         => '>=',
        'ge'          => '>=',
        'lt'          => '<',
        'le'          => '<=',
        'lte'         => '<=',
        'elt'         => '<=',
        'in'          => 'IN',
        'not_in'      => 'NOT IN',
        'not in'      => 'NOT IN',
        'between'     => 'BETWEEN',
        'not_between' => 'NOT BETWEEN',
        'not between' => 'NOT BETWEEN',
        'like'        => 'LIKE',
        'not_like'    => 'NOT LIKE',
        'not like'    => 'NOT LIKE',
        'null'        => 'IS NULL',
        'not_null'    => 'IS NOT NULL'
    ];

    /**
     * 获取表达式
     *
     * @param string $strExpression 表达式
     *
     * @return mixed|string
     */
    public function getExpression($strExpression)
    {
        if (empty($strExpression)) {
            return '=';
        }

        // 第一步：获取简写的表达式
        if ($expression = ArrayHelper::getValue($this->expression, strtolower($strExpression))) {
            return $expression;
        }

        // 第二步：是否存在表达式里面
        if (in_array($strExpression, $this->expression)) {
            return $strExpression;
        }

        // 最后使用默认的 =
        return '=';
    }

    /**
     * 验证是否为空
     *
     * @param $value
     *
     * @return bool
     */
    public function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || is_string($value) && trim($value) === '';
    }

    /**
     * 获取过滤后的查询条件
     *
     * @param array $condition 查询条件
     *
     * @return array
     */
    public function getFilterCondition($condition)
    {
        if (empty($condition)) {
            return [];
        }

        $where = [];
        foreach ($condition as $column => $value) {
            // 过滤为空的数据
            if ($this->isEmpty($value)) {
                continue;
            }

            $filters = explode(':', $column);
            // 例如： username:like, username:not_like, username:gt
            $expression = $this->getExpression(ArrayHelper::getValue($filters, 1));
            // 如果需要传入数组的传入一个字符串，使用,分割
            if (in_array($expression, ['IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN']) && is_string($value)) {
                $value = explode(',', $value);
            }

            $column  = trim(array_shift($filters));
            $where[] = [$expression, $column, $value];
        }

        if ($where) {
            array_unshift($where, 'and');
        }

        return $where;
    }

}