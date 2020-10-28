<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "template_var".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property boolean $deleted
 * @property string $extra
 * @property integer $template_id
 * @property integer $group_id
 *
 * @property ReceptionVar[] $receptionVars
 * @property Template $template
 * @property TemplateVarGroup $group
 */
class TemplateVar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template_var';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deleted'], 'boolean'],
            [['extra'], 'string'],
            [['template_id', 'group_id'], 'integer'],
            [['name', 'type'], 'string', 'max' => 255],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemplateVarGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
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
            'type' => 'Type',
            'deleted' => 'Deleted',
            'extra' => 'json доп опции',
            'template_id' => 'Template ID',
            'group_id' => 'Группа',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptionVars()
    {
        return $this->hasMany(ReceptionVar::className(), ['var_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(TemplateVarGroup::className(), ['id' => 'group_id']);
    }
}
