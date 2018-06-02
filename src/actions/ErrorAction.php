<?php

namespace jinxing\admin\actions;

use jinxing\admin\traits\JsonTrait;
use Yii;

class ErrorAction extends \yii\web\ErrorAction
{
    use JsonTrait;

    /**
     * Builds string that represents the exception.
     * Normally used to generate a response to AJAX request.
     * @return string
     * @since 2.0.11
     */
    protected function renderAjaxResponse()
    {
        Yii::$app->getResponse()->setStatusCode(200);
        return $this->error($this->getExceptionCode(), $this->getExceptionMessage());
    }
}