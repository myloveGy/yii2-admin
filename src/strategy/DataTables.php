<?php

namespace jinxing\admin\strategy;

use Yii;

class DataTables extends Strategy
{
    public function getRequest()
    {
        // 接收参数
        $request          = Yii::$app->request;
        $this->arrRequest = [
            'draw'    => (int)$request->get('draw'),         // 请求次数
            'orderBy' => trim($request->get('orderBy', '')), // 排序条件
            'offset'  => intval($request->get('offset', 0)), // 查询开始位置
            'limit'   => intval($request->get('limit', 10)), // 查询数据条数
            'filters' => $request->get('filters'),           // 查询过滤条件
        ];

        return $this->arrRequest;
    }

    public function handleResponse($data, $total, $params = null)
    {
        return [
            'draw'            => $this->arrRequest['draw'], // 请求次数
            'recordsTotal'    => $total,                    // 数据总条数
            'recordsFiltered' => $total,                    // 数据总条数
            'data'            => $data,                     // 数据信息
        ];
    }
}