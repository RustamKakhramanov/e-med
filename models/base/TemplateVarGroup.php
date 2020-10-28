<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "template_var_group".
 *
 * @property integer $id
 * @property integer $template_id
 * @property string $name
 *
 * @property TemplateVar[] $templateVars
 * @property Template $template
 */
class TemplateVarGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template_var_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => 'Шаблон',
            'name' => 'Название',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateVars()
    {
        return $this->hasMany(TemplateVar::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
}
