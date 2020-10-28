<?php

namespace app\models;

use Yii;

class TemplateVarGroup extends \app\models\base\TemplateVarGroup {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['template_id'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateVars() {
        return $this->hasMany(TemplateVar::className(), ['group_id' => 'id'])->orderBy([
            TemplateVar::tableName() . '.name' => SORT_ASC
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate() {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    public function delete() {
        foreach ($this->templateVars as $templateVar) {
            $templateVar->group_id = null;
            $templateVar->save(false);
        }

        return parent::delete();
    }

    public static function getCommonRels() {
        return self::find()
            ->where([
                'template_id' => null
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();
    }
}
