<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_price".
 *
 * @property int $id
 * @property int $doctor_id
 * @property int $price_id
 *
 * @property Doctor $doctor
 * @property Price $price
 */
class DoctorPrice extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'doctor_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['doctor_id', 'price_id'], 'required'],
            [['doctor_id', 'price_id'], 'default', 'value' => null],
            [['doctor_id', 'price_id'], 'integer'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Price::className(), 'targetAttribute' => ['price_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'doctor_id' => 'Doctor ID',
            'price_id' => 'Price ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor() {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice() {
        return $this->hasOne(Price::className(), ['id' => 'price_id']);
    }
}
