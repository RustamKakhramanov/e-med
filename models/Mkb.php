<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mkb".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $parent_id
 * @property string $parent_code
 * @property integer $node_count
 * @property string $additional_info
 *
 * @property Mkb $parent
 * @property Mkb[] $mkbs
 */
class Mkb extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mkb';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'additional_info'], 'string'],
            [['parent_id', 'node_count'], 'integer'],
            [['code', 'parent_code'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'parent_id' => 'Parent ID',
            'parent_code' => 'Parent Code',
            'node_count' => 'Node Count',
            'additional_info' => 'Additional Info',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Mkb::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMkbs()
    {
        return $this->hasMany(Mkb::className(), ['parent_id' => 'id']);
    }
}
