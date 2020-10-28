<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dest_template_diagnosis".
 *
 * @property integer $id
 * @property integer $dest_template_id
 * @property integer $mkb_id
 *
 * @property DestTemplate $destTemplate
 * @property Mkb $mkb
 */
class DestTemplateDiagnosis extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dest_template_diagnosis';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dest_template_id', 'mkb_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dest_template_id' => 'Dest Template ID',
            'mkb_id' => 'Mkb ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestTemplate()
    {
        return $this->hasOne(DestTemplate::className(), ['id' => 'dest_template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMkb()
    {
        return $this->hasOne(Mkb::className(), ['id' => 'mkb_id']);
    }
}
