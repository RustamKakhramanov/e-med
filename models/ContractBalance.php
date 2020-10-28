<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contract_balance".
 *
 * @property integer $id
 * @property integer $contract_id
 * @property integer $patient_id
 * @property string $date
 * @property string $sum
 *
 * @property Contract $contract
 * @property Patients $patient
 */
class ContractBalance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract_balance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract_id', 'patient_id'], 'integer'],
            [['date'], 'safe'],
            [['sum'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contract_id' => 'Contract ID',
            'patient_id' => 'Patient ID',
            'date' => 'Date',
            'sum' => 'Sum',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patients::className(), ['id' => 'patient_id']);
    }
}
