<?php

namespace app\models;

use Yii;

class TemplateDoc extends \app\models\base\TemplateDoc {
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['doc_id', 'template_id'], 'required'],
            [['doc_id', 'template_id'], 'default', 'value' => null],
            [['doc_id', 'template_id'], 'integer'],
            [['doc_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doc_id' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoc() {
        return $this->hasOne(Doctor::className(), ['id' => 'doc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate() {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
}
