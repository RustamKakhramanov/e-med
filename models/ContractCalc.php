<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contract_calc".
 *
 * @property integer $id
 * @property integer $contract_id
 * @property integer $patient_id
 * @property integer $check_id
 * @property integer $reception_id
 * @property string $sum
 *
 * @property Check $check
 * @property Contract $contract
 * @property Patients $patient
 */
class ContractCalc extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'contract_calc';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['contract_id', 'patient_id'], 'required'],
            [['contract_id', 'patient_id', 'check_id', 'reception_id'], 'integer'],
            [['sum'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'contract_id' => 'Contract ID',
            'patient_id' => 'Patient ID',
            'check_id' => 'Check ID',
            'reception_id' => 'Reception ID',
            'sum' => 'Sum',
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->updated = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheck() {
        return $this->hasOne(Check::className(), ['id' => 'check_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract() {
        return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient() {
        return $this->hasOne(Patients::className(), ['id' => 'patient_id']);
    }
    
    /**
     * создать движение по чеку
     * @param type $Check
     */
    public static function createFromCheck($Check) {

        $contracts = [];
        //сортировка по договорам
        foreach ($Check->items as $item) {
            $dir = $item->direction;
            if (!isset($contracts[$dir->contract_id])) {
                $contracts[$dir->contract_id] = 0;
            }
            $contracts[$dir->contract_id] += ($dir->cost * $dir->count);
        }

        foreach ($contracts as $contractId => $sum) {
            $ContactCalc = new ContractCalc();
            $ContactCalc->patient_id = $Check->patient_id;
            $ContactCalc->contract_id = $contractId;
            $ContactCalc->sum = $sum;
            $ContactCalc->check_id = $Check->id;
            $ContactCalc->save();
        }
    }

}
