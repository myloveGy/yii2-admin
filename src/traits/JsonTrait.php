<?php

namespace jinxing\admin\traits;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Trait Json
 * @author  liujx
 * @package common\traits
 */
trait JsonTrait
{
    /**
     * 定义返回json的数据
     *
     * @var array
     */
    protected $arrJson = [
        'code' => 201,
        'msg'  => '',
        'data' => [],
    ];

    /**
     * 响应ajax 返回
     *
     * @param string $array 其他返回参数(默认null)
     *
     * @return mixed|string
     */
    protected function returnJson($array = null)
    {
        // 判断是否覆盖之前的值
        if ($array && is_array($array)) {
            $this->arrJson = array_merge($this->arrJson, $array);
        }

        // 没有错误信息使用code 确定错误信息
        if (empty($this->arrJson['msg']) && isset(Yii::$app->i18n->translations['admin'])) {
            $this->arrJson['msg'] = ArrayHelper::getValue(
                (array)Yii::t('admin', 'error_code'),
                $this->arrJson['code']
            );
        }

        // 设置JSON返回
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->arrJson;
    }

    /**
     * handleJson() 处理返回数据
     *
     * @param mixed   $data 返回数据
     * @param integer $code 返回状态码
     * @param string  $msg  提示信息
     */
    protected function handleJson($data, $code = 0, $msg = '')
    {
        list($this->arrJson['data'], $this->arrJson['code'], $this->arrJson['msg']) = [$data, $code, $msg];
    }

    /**
     * 处理成功返回
     *
     * @param mixed  $data 返回结果信息
     * @param string $msg  正确信息
     *
     * @return mixed|string
     */
    protected function success($data = [], $message = '')
    {
        $code = 0;
        return $this->returnJson(compact('code', 'msg', 'data'));
    }

    /**
     * 处理错误返回
     *
     * @param integer $code 错误码
     * @param string  $msg  错误信息
     *
     * @return mixed|string
     */
    protected function error($code = 201, $msg = '')
    {
        return $this->returnJson(compact('code', 'msg'));
    }

    /**
     * 设置错误码
     *
     * @param int $code 设置错误码
     */
    public function setCode($code = 201)
    {
        $this->arrJson['code'] = $code;
    }

    /**
     * 设置错误信息
     *
     * @param string $message
     */
    public function setMessage($message = '')
    {
        $this->arrJson['msg'] = $message;
    }
}