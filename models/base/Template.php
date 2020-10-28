<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "template".
 *
 * @property int $id
 * @property string $name
 * @property string $created
 * @property string $updated
 * @property bool $deleted
 * @property int $user_id кто создал
 * @property bool $draft флаг черновика
 * @property string $html
 * @property int $branch_id
 * @property string $size Размер документа
 *
 * @property Reception[] $receptions
 * @property Branch $branch
 * @property TemplateDoc[] $templateDocs
 * @property TemplateSpec[] $templateSpecs
 * @property TemplateVar[] $templateVars
 * @property TemplateVarGroup[] $templateVarGroups
 */
class Template extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created', 'updated'], 'safe'],
            [['deleted', 'draft'], 'boolean'],
            [['user_id'], 'required'],
            [['user_id', 'branch_id'], 'default', 'value' => null],
            [['user_id', 'branch_id'], 'integer'],
            [['html'], 'string'],
            [['name', 'size'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'user_id' => 'User ID',
            'draft' => 'Draft',
            'html' => 'Html',
            'branch_id' => 'Branch ID',
            'size' => 'Size',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptions()
    {
        return $this->hasMany(Reception::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateDocs()
    {
        return $this->hasMany(TemplateDoc::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateSpecs()
    {
        return $this->hasMany(TemplateSpec::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateVars()
    {
        return $this->hasMany(TemplateVar::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateVarGroups()
    {
        return $this->hasMany(TemplateVarGroup::className(), ['template_id' => 'id']);
    }
}
