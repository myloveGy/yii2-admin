<?php

namespace jinxing\admin\models;

use yii\helpers\Json;
use \yii\db\ActiveRecord;
use jinxing\admin\models\traits\TimestampTrait;

/**
 * This is the model class for table "{{%uploads}}".
 *
 * @property integer $id
 * @property string  $title
 * @property string  $url
 * @property integer $created_at
 * @property integer $updated_at
 */
class Uploads extends ActiveRecord
{
    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%uploads}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'url'], 'required'],
            [['title'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'Id',
            'title'      => '标题',
            'url'        => '文件访问地址',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 修改之前的处理
     *
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (is_array($this->url)) {
            $this->url = Json::encode($this->url);
        }

        return parent::beforeSave($insert);
    }
}
