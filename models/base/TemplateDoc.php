<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "template_doc".
 *
 * @property int $id
 * @property int $doc_id Доктор
 * @property int $template_id Шаблон
 *
 * @property Doctor $doc
 * @property Template $template
 */
class TemplateDoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template_doc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doc_id', 'template_id'], 'required'],
            [['doc_id', 'template_id'], 'default', 'value' => null],
            [['doc_id', 'template_id'], 'integer'],
            [['doc_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doc_id' => 'id']],
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
            'doc_id' => 'Доктор',
            'template_id' => 'Шаблон',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoc()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
}
