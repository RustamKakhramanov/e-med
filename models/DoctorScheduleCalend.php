<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_schedule_calend".
 *
 * @property integer $id
 * @property integer $doctor_id
 * @property string $date
 * @property string $data_items
 * @property boolean $enabled
 * @property integer $num
 *
 * @property Doctor $doctor
 */
class DoctorScheduleCalend extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor_schedule_calend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doctor_id'], 'required'],
            [['doctor_id', 'num'], 'integer'],
            [['date'], 'safe'],
            [['data_items'], 'string'],
            [['enabled'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doctor_id' => 'Doctor ID',
            'date' => 'Date',
            'data_items' => 'Data Items',
            'enabled' => 'Enabled',
            'num' => 'Num',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
    }

    public function getData_items() {
        if (is_array($this->data_items)) {
            return $this->data_items;
        }

        if (is_string($this->data_items)) {
            return json_decode($this->data_items, true);
        }

        return [];
    }
}
