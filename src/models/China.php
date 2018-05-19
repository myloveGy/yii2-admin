<?php

namespace jinxing\admin\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%china}}".
 *
 * @property integer $Id
 * @property string $Name
 * @property integer $Pid
 *
 * @property China $p
 * @property China[] $chinas
 */
class China extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%china}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'pid'], 'integer'],
            [['name'], 'string', 'min' => 2, 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'name' => '名称',
            'pid'  => '父类ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(China::className(), ['id' => 'pid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChinas()
    {
        return $this->hasMany(China::className(), ['pid' => 'id']);
    }

}
