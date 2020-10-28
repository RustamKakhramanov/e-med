<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reception_diagnosis".
 *
 * @property integer $id
 * @property integer $reception_id
 * @property integer $mkb_id
 * @property boolean $main
 *
 * @property Mkb $mkb
 * @property Reception $reception
 */
class ReceptionDiagnosis extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reception_diagnosis';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reception_id', 'mkb_id'], 'integer'],
            [['main'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reception_id' => 'Reception ID',
            'mkb_id' => 'Mkb ID',
            'main' => 'Main',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMkb()
    {
        return $this->hasOne(Mkb::className(), ['id' => 'mkb_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReception()
    {
        return $this->hasOne(Reception::className(), ['id' => 'reception_id']);
    }
}
