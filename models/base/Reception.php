<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "reception".
 *
 * @property int $id
 * @property int $doctor_id
 * @property int $direction_id
 * @property string $created
 * @property string $updated
 * @property int $template_id
 * @property bool $deleted
 * @property string $html
 * @property bool $draft
 * @property int $branch_id
 *
 * @property Branch $branch
 * @property Doctor $doctor
 * @property Template $template
 * @property ReceptionDiagnosis[] $receptionDiagnoses
 * @property ReceptionVar[] $receptionVars
 */
class Reception extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reception';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_id', 'direction_id', 'template_id', 'branch_id'], 'default', 'value' => null],
            [['doctor_id', 'direction_id', 'template_id', 'branch_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['deleted', 'draft'], 'boolean'],
            [['html'], 'string'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
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
            'doctor_id' => 'Doctor ID',
            'direction_id' => 'Direction ID',
            'created' => 'Created',
            'updated' => 'Updated',
            'template_id' => 'Template ID',
            'deleted' => 'Deleted',
            'html' => 'Html',
            'draft' => 'Draft',
            'branch_id' => 'Branch ID',
        ];
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
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
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
    public function getReceptionDiagnoses()
    {
        return $this->hasMany(ReceptionDiagnosis::className(), ['reception_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptionVars()
    {
        return $this->hasMany(ReceptionVar::className(), ['reception_id' => 'id']);
    }
}
