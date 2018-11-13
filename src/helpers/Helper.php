<?php

namespace jinxing\admin\helpers;

use yii\base\Module;
use yii\helpers\ArrayHelper;
use Closure;
use yii\web\Application;

/**
 * Class Helper
 * 辅助处理类，一般用来定义公共方法
 * @author  liujx
 * @package common\helpers
 */
class Helper
{
    /**
     * map() 使用ArrayHelper 处理数组, 并添加其他信息
     *
     * @param  mixed  $array  需要处理的数据
     * @param  string $id     键名
     * @param  string $name   键值
     * @param  array  $params 其他数据
     *
     * @return array
     */
    public static function map($array, $id, $name, $params = ['请选择'])
    {
        $array = ArrayHelper::map($array, $id, $name);
        if ($params) {
            foreach ($params as $key => $value) $array[$key] = $value;
        }

        ksort($array);
        return $array;
    }

    /**
     * getIpAddress() 获取IP地址
     * @return string 返回字符串
     */
    public static function getIpAddress()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $strIpAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $strIpAddress = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $strIpAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $strIpAddress = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $strIpAddress = getenv('HTTP_CLIENT_IP');
            } else {
                $strIpAddress = getenv('REMOTE_ADDR') ? getenv('REMOTE_ADDR') : '';
            }
        }

        return $strIpAddress;
    }

    /**
     * 处理通过请求参数对应yii2 where 查询条件
     *
     * @param array  $params 请求参数数组
     * @param array  $where  定义查询处理方式数组
     * @param string $join   默认查询方式是and
     *
     * @return array
     */
    public static function handleWhere($params, $where, $join = 'and')
    {
        // 处理默认查询条件
        if ($arrReturn = ArrayHelper::getValue($where, 'where')) {
            unset($where['where']);
        }

        // 请求参数和查询参数必须存在
        if ($where && $params) {
            foreach ($params as $key => $value) {
                // 判断不能查询请求的数据不能为空，且定义了查询参数对应查询处理方式
                if ($value === '' || !isset($where[$key])) {
                    continue;
                }

                // 匿名函数处理
                $handle = $where[$key];
                if ($handle instanceof Closure) {
                    $arrReturn[] = $handle($value);
                    continue;
                }

                // 数组
                if (is_array($handle)) {
                    // 处理函数
                    if (isset($handle['func']) && (function_exists($handle['func']) || $handle['func'] instanceof Closure)) {
                        $value = $handle['func']($value);
                    }

                    $handle['field'] = empty($handle['field']) ? $key : $handle['field'];   // 对应字段
                    $handle['and']   = empty($handle['and']) ? '=' : $handle['and'];        // 查询连接类型
                    $arrReturn[]     = [$where[$key]['and'], $where[$key]['field'], $value];
                    continue;
                }

                $arrReturn[] = [(string)$handle, $key, $value];
            }
        }

        // 存在查询条件，数组前面添加 连接类型
        if ($arrReturn) {
            array_unshift($arrReturn, $join);
        }

        return $arrReturn;
    }

    /**
     * 将一个多维数组连接为一个字符串
     *
     * @param  array $array 数组
     *
     * @return string
     */
    public static function arrayToString($array)
    {
        $str = '';
        if (!empty($array)) {
            foreach ($array as $value) {
                $str .= is_array($value) ? implode('', $value) : $value;
            }
        }

        return $str;
    }

    /**
     * 通过指定字符串拆分数组，然后各个元素首字母，最后拼接
     *
     * @example $strName = 'yii_user_log',$and = '_', return YiiUserLog
     *
     * @param string $strName 字符串
     * @param string $and     拆分的字符串(默认'_')
     *
     * @return string
     */
    public static function strToUpperWords($strName, $and = '_')
    {
        // 通过指定字符串拆分为数组
        $value = explode($and, $strName);
        if ($value) {
            // 首字母大写，然后拼接
            $strReturn = '';
            foreach ($value as $val) {
                $strReturn .= ucfirst($val);
            }
        } else {
            $strReturn = ucfirst($strName);
        }

        return $strReturn;
    }

    /**
     * model 导出excel
     *
     * @param string             $title        excel 标题
     * @param array              $columns      列对应的字段名称 ['id' => 'ID']
     *                                         ['id' => 'id', 'title' => '标题', 'content' => '内容']
     *                                         导出查询数据
     *                                         ['id' => 1, 'title' => 123, 'content' => 'test']
     *                                         其中 key 对应的是查询出来数据的 key,数据导填充的值通过这个key获取
     *                                         其中 value 对应的是导出第一列对应的标题内容
     * @param                    $query        \yii\db\Query 查询对象
     *                                         注意对象查询结果一定要转数组 asArray()
     * @param array              $handleParams 处理参数
     * @param null|object|string $function     处理函数
     *
     * @return mixed
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \yii\base\ExitException
     */
    public static function excel($title, $columns, $query, $handleParams = [], $function = null)
    {
        $intCount = $query->count();
        // 判断数据是否存在
        if ($intCount <= 0) {
            return;
        }

        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $objPHPExcel = new \PHPExcel();
        if ($intCount > 3000) {
            $cacheMethod   = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '8MB');
            \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        }

        $objPHPExcel->getProperties()->setCreator("yii2.com")
            ->setLastModifiedBy("yii2.com")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->setActiveSheetIndex(0);

        // 确定第一行信息
        $letter  = 'A';
        $letters = [];
        foreach ($columns as $attribute => $value) {
            $letters[$letter] = $attribute;
            $objPHPExcel->getActiveSheet()->setCellValue($letter . '1', $value);
            $letter++;
        }

        unset($letter);

        // 写入数据信息
        $intNum = 2;
        foreach ($query->batch(1000) as $array) {
            // 函数处理
            if ($function && $function instanceof Closure) {
                $function($array);
            }

            // 处理每一行的数据
            foreach ($array as $value) {
                // 写入信息数据
                foreach ($letters as $letter => $attribute) {
                    $tmpValue = isset($value[$attribute]) ? $value[$attribute] : null;
                    // 匿名函数处理
                    if (isset($handleParams[$attribute]) && $handleParams[$attribute] instanceof Closure) {
                        $tmpValue = $handleParams[$attribute]($tmpValue);
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue($letter . $intNum, $tmpValue);
                }

                $intNum++;
            }
        }

        // 设置sheet 标题信息
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $objPHPExcel->setActiveSheetIndex(0);

        // 设置头信息
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');           // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');  // always modified
        header('Cache-Control: cache, must-revalidate');            // HTTP/1.1
        header('Pragma: public');                                   // HTTP/1.0

        // 直接输出文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        \Yii::$app->end();
        return;
    }

    /**
     * 获取模块ID
     *
     * @param      $module
     *
     * @param bool $get_application
     *
     * @return array
     */
    public static function getModuleIds($module, $get_application = false)
    {
        $array = [];
        if ($module instanceof Application) {
            if ($get_application) {
                $array[] = $module->id;
            }
        } else if ($module instanceof Module) {
            $array[] = $module->id;
            if ($ids = static::getModuleIds($module->module)) {
                foreach ($ids as $id) {
                    array_unshift($array, $id);
                }
            }
        }

        return $array;
    }

    /**
     * 获取登录地址
     *
     * @param  integer $beforeUserId
     * @param  integer $afterUseId
     * @param array    $params
     * @param string   $url
     *
     * @return string
     */
    public static function getSwitchLoginUrl(
        $beforeUserId,
        $afterUseId,
        $params = [],
        $url = '/admin/admin/default/switch-login'
    )
    {
        $params = array_merge([
            'before_user_id' => $beforeUserId,
            'after_user_id'  => $afterUseId,
            'time'           => time(),
        ], $params);

        $params['sign'] = static::getSign($params);
        return $url . '?token=' . base64_encode(json_encode($params));
    }

    /**
     * 获取切换登录信息
     *
     * @param string $token
     * @param array  $notEmpty
     *
     * @return bool|mixed
     */
    public static function getSwitchLoginInfo($token = '', $notEmpty = [])
    {
        // 验证数据
        if (!$token || (!$array = json_decode(base64_decode($token), true))) {
            return false;
        }

        // 验证必要参数
        array_push($notEmpty, 'time', 'sign', 'before_user_id', 'after_user_id');
        if (!static::validateNotEmpty($array, $notEmpty)) {
            return false;
        }

        // 验证密钥
        $sign = $array['sign'];
        unset($array['sign']);
        if ($sign !== static::getSign($array)) {
            return false;
        }

        // 验证时间
        if ((time() - $array['time']) > 3600) {
            return false;
        }

        return $array;
    }

    /**
     * 获取密钥
     *
     * @param array  $params
     * @param string $salt
     *
     * @return string
     */
    public static function getSign($params = [], $salt = 'NtuGEpZiKjfS91Fy')
    {
        ksort($params);
        $str = [];
        foreach ($params as $key => $val) {
            $str[] = "{$key}={$val}";
        }

        return md5(implode('&', $str) . $salt);
    }

    /**
     * 验证数据中不允许为空的字段信息
     *
     * @param array $params       需要验证的数组
     * @param array $needValidate 不能为空的字段
     *
     * @return bool
     */
    public static function validateNotEmpty($params = [], $needValidate = [])
    {
        if (empty($needValidate)) {
            return true;
        }

        foreach ($needValidate as $key) {
            if (empty($params[$key])) {
                return false;
            }
        }

        return true;
    }
}