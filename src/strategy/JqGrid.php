<?php
/**
 * Created by PhpStorm.
 * User: liujinxing
 * Date: 2017/3/28
 * Time: 13:39
 */

namespace jinxing\admin\strategy;

class JqGrid extends Strategy
{

    public function getRequest()
    {
        // 接收参数参数
        $request = \Yii::$app->request;
        $page    = (int)$request->post('page', 1) ?: 1; // 第几页
        $limit   = (int)$request->post('rows', 10);     // 每页多少条
        if ($field = $request->post('sidx')) {
            $orderBy = $field . ' ' . $request->post('sord', 'asc');
        } else {
            $orderBy = '';
        }

        // 返回查询字段信息
        $this->arrRequest = [
            'orderBy' => $orderBy,                   // 排序方式
            'offset'  => ($page - 1) * $limit,       // 查询开始位置
            'limit'   => $limit,                     // 查询数据条数
            'page'    => $page,                      // 第几页
            'filters' => $request->post('filters'),  // 查询参数
        ];

        return $this->arrRequest;
    }

    public function handleResponse($data, $total, $params = null)
    {
        $intTotalPage = $total > 0 ? ceil($total / $this->arrRequest['limit']) : 0;
        return [
            'errCode' => 0,
            'errMsg'  => 'success',
            'data'    => [
                'page'    => $this->arrRequest['page'], // 第几页
                'total'   => $intTotalPage,             // 总页数
                'records' => $total,                    // 总数据条数
                'rows'    => $data,                     // 数据
            ],
        ];
    }
}