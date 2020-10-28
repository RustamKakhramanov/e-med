<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patients;

class EventNewPatient extends Patients {

    //создание нового
    const SCENARIO_NEW = 'new';
    //поиск существующего
    const SCENARIO_FIND = 'find';

    public $phone;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['first_name', 'last_name', 'birthday', 'phone'], 'required', 'message' => 'Обязательно'],
            [['email'], 'safe'],
            [['sex'], 'integer'],
            [['first_name', 'last_name', 'middle_name', 'phone', 'email', 'birthday'], 'string', 'max' => 255],
            [['last_name'], 'uniqValidate']
        ];
    }

    public function setDefault() {
        parent::setDefault();
        $this->new = 1;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return array_merge(
                parent::attributeLabels(), [
            'phone' => 'Телефон',
            'email' => 'Эл. почта',
                ]
        );
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            
            $data_contact = [];
            $data_contact[] = [
                'type' => 'phone',
                'phone' => $this->phone
            ];
            
            if ($this->email) {
                $data_contact[] = [
                    'type' => 'email',
                    'email' => $this->email
                ];
            }
            
            $this->data_contact = json_encode($data_contact);

            return true;
        }
        return false;
    }
    
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->saveContacts();
    }

}
