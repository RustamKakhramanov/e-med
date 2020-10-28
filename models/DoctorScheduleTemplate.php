<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_schedule_template".
 *
 * @property integer $id
 * @property integer $doctor_id
 * @property string $type
 * @property string $data_days
 *
 * @property Doctor $doctor
 */
class DoctorScheduleTemplate extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'doctor_schedule_template';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['doctor_id'], 'integer'],
            [['type'], 'required'],
            [['data_days'], 'string'],
            [['type'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'doctor_id' => 'Doctor ID',
            'type' => 'Type',
            'data_days' => 'Data Days',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor() {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
    }

    public function getDataDaysData() {
        if (is_array($this->data_days)) {
            return $this->data_days;
        }

        if (is_string($this->data_days)) {
            return json_decode($this->data_days, true);
        }

        return [];
    }

}
