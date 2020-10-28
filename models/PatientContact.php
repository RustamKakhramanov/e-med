<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_contact".
 *
 * @property integer $id
 * @property string $type
 * @property string $city
 * @property string $corp
 * @property string $room
 * @property string $house
 * @property string $street
 * @property string $phone
 * @property string $email
 * @property integer $patient_id
 *
 * @property Patients $patient
 */
class PatientContact extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'patient_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type', 'patient_id'], 'required'],
            [['patient_id'], 'integer'],
            [['type', 'city', 'corp', 'room', 'house', 'street', 'phone', 'email'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'city' => 'City',
            'corp' => 'Corp',
            'room' => 'Room',
            'house' => 'House',
            'street' => 'Street',
            'phone' => 'Phone',
            'email' => 'Email',
            'patient_id' => 'Patient ID',
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            
            if($this->type == 'phone') {
                //$this->phone = preg_replace('/\D/', '', $this->phone);
                $this->phone = preg_replace('/[^0-9]/', '', $this->phone);
            }
            
            return true;
        }
        return false;
    }
    
    protected function _maskPhone() {
        //if ($this->phone) {
            return '+7 (' . substr($this->phone, 1, 3) . ') ' . substr($this->phone, 4, 3) . ' ' . substr($this->phone, 7);
        //}
        
        return $this->phone;
    }
    
    
    public function getPhoneDisplay() {
        return $this->_maskPhone();
    }

    public function toArray(array $fields = array(), array $expand = array(), $recursive = true) {
        $this->_maskPhone();
        return parent::toArray($fields, $expand, $recursive); 
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient() {
        return $this->hasOne(Patients::className(), ['id' => 'patient_id']);
    }

}
