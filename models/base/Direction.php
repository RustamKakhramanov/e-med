<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "direction".
 *
 * @property int $id
 * @property string $created Дата создания
 * @property int $user_id Пользователь
 * @property int $branch_id Филиал
 * @property int $patient_id Пациент
 *
 * @property Branch $branch
 * @property Patients $patient
 * @property User $user
 * @property DirectionItem[] $directionItems
 */
class Direction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created'], 'safe'],
            [['user_id', 'branch_id', 'patient_id'], 'required'],
            [['user_id', 'branch_id', 'patient_id'], 'default', 'value' => null],
            [['user_id', 'branch_id', 'patient_id'], 'integer'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patients::className(), 'targetAttribute' => ['patient_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Дата создания',
            'user_id' => 'Пользователь',
            'branch_id' => 'Филиал',
            'patient_id' => 'Пациент',
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
    public function getPatient()
    {
        return $this->hasOne(Patients::className(), ['id' => 'patient_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionItems()
    {
        return $this->hasMany(DirectionItem::className(), ['direction_id' => 'id']);
    }
}
