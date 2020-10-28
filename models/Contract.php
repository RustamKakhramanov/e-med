<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contract".
 *
 * @property integer $id
 * @property string $name
 * @property string $start
 * @property string $end
 * @property boolean $deleted
 * @property boolean $typical
 * @property integer $contractor_id
 * @property Patients $patient
 * @property boolean $main
 * @property integer $branch_id
 *
 * @property Contractor $contractor
 * @property Branch $branch
 */
class Contract extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'contract';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['start', 'end'], 'safe'],
            [['deleted', 'typical', 'main'], 'boolean'],
            [['contractor_id', 'patient_id', 'branch_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'start' => 'Дата начала',
            'end' => 'Дата завершения',
            'deleted' => 'Deleted',
            'typical' => 'Типовой',
            'contractor_id' => 'Контрагент',
            'patient_id' => 'Patient ID',
            'main' => 'Основной',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractor() {
        return $this->hasOne(Contractor::className(), ['id' => 'contractor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient() {
        return $this->hasOne(Patients::className(), ['id' => 'patient_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    //установить основным
    public function setMainTypical() {
        $contracts = \app\models\Contract::find()
                ->andFilterWhere(['!=', 'id', $this->id])
                ->andFilterWhere(['=', 'main', 1])
                ->andFilterWhere(['=', 'deleted', 0])
                ->all();

        foreach ($contracts as $c) {
            $c->main = 0;
            $c->save();
        }

        $this->main = 1;
        $this->save();
    }

    public function deleteSafe() {
        //todo сделать проверку - если нет операций то удалять полностью
        $this->deleted = 1;
        $this->save();
    }

    //текуший баланс
    public function getBalance($patient_id) {

        $balance = 0;
        //получить цифру с прошлого месяца
        $prevMonth = ContractBalance::find()->where([
                    'patient_id' => $patient_id,
                    'contract_id' => $this->id,
                    'date' => date('Y-m-t', strtotime('-1 month'))
                ])->one();

        if ($prevMonth) {
            $balance = $prevMonth->sum;
        }

        //расчет текущего месяца
        $calcs = ContractCalc::find()->where([
                    'patient_id' => $patient_id,
                    'contract_id' => $this->id
                ])->andWhere(['>=', 'updated', date('Y-m-01')])
                ->all();

        foreach ($calcs as $calc) {
            if ($calc->check->back_id) {
                $balance -= $calc->sum;
            } else {
                $balance += $calc->sum;
            }
        }

        return $balance;
    }

}
