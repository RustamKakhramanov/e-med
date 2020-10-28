<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dest_template_spec".
 *
 * @property integer $id
 * @property integer $dest_template_id
 * @property integer $spec_id
 *
 * @property DestTemplate $destTemplate
 * @property Speciality $spec
 */
class DestTemplateSpec extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dest_template_spec';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dest_template_id', 'spec_id'], 'integer']
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
            'spec_id' => 'Spec ID',
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
    public function getSpec()
    {
        return $this->hasOne(Speciality::className(), ['id' => 'spec_id']);
    }
}
