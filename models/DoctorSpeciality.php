<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_speciality".
 *
 * @property integer $doctor_id
 * @property integer $speciality_id
 * @property integer $duration
 * @property boolean $main
 * @property boolean $show_schedule
 * @property integer $id
 *
 * @property Doctor $doctor
 * @property Speciality $speciality
 */
class DoctorSpeciality extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor_speciality';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doctor_id', 'speciality_id', 'duration'], 'integer'],
            [['main', 'show_schedule'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'doctor_id' => 'Doctor ID',
            'speciality_id' => 'Speciality ID',
            'duration' => 'Duration',
            'main' => 'Main',
            'show_schedule' => 'Show Schedule',
            'id' => 'ID',
        ];
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
    public function getSpeciality()
    {
        return $this->hasOne(Speciality::className(), ['id' => 'speciality_id']);
    }
}
