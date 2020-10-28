<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "template_spec".
 *
 * @property int $id
 * @property int $spec_id Специализация
 * @property int $template_id Шаблон
 *
 * @property Speciality $spec
 * @property Template $template
 */
class TemplateSpec extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template_spec';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['spec_id', 'template_id'], 'required'],
            [['spec_id', 'template_id'], 'default', 'value' => null],
            [['spec_id', 'template_id'], 'integer'],
            [['spec_id'], 'exist', 'skipOnError' => true, 'targetClass' => Speciality::className(), 'targetAttribute' => ['spec_id' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spec_id' => 'Специализация',
            'template_id' => 'Шаблон',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpec()
    {
        return $this->hasOne(Speciality::className(), ['id' => 'spec_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
}
