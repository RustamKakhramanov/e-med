<?php

namespace app\models;

use Yii;

class TemplateSpec extends \app\models\base\TemplateSpec {
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['spec_id', 'template_id'], 'required'],
            [['spec_id', 'template_id'], 'default', 'value' => null],
            [['spec_id', 'template_id'], 'integer'],
            [['spec_id'], 'exist', 'skipOnError' => true, 'targetClass' => Speciality::className(), 'targetAttribute' => ['spec_id' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpec() {
        return $this->hasOne(Speciality::className(), ['id' => 'spec_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate() {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
}
