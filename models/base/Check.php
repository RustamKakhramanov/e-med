<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "check".
 *
 * @property int $id
 * @property string $created
 * @property string $updated
 * @property int $user_id
 * @property int $patient_id
 * @property string $sum
 * @property int $back_id ид чека для возврата
 * @property int $shift_id смена
 * @property int $branch_id
 * @property double $payment_cash Оплата наличными
 * @property double $payment_card Оплата платежной картой
 * @property string $webkassa_id Фискальный признак вебкассы
 * @property string $webkassa_data Данные вебкассы
 * @property double $nds НДС
 *
 * @property Branch $branch
 * @property Patients $patient
 * @property Shift $shift
 * @property User $user
 * @property CheckItem[] $checkItems
 * @property ContractCalc[] $contractCalcs
 */
class Check extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created', 'updated'], 'safe'],
            [['user_id', 'patient_id', 'sum'], 'required'],
            [['user_id', 'patient_id', 'back_id', 'shift_id', 'branch_id'], 'default', 'value' => null],
            [['user_id', 'patient_id', 'back_id', 'shift_id', 'branch_id'], 'integer'],
            [['sum', 'payment_cash', 'payment_card', 'nds'], 'number'],
            [['webkassa_data'], 'string'],
            [['webkassa_id'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patients::className(), 'targetAttribute' => ['patient_id' => 'id']],
            [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shift::className(), 'targetAttribute' => ['shift_id' => 'id']],
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
            'created' => 'Created',
            'updated' => 'Updated',
            'user_id' => 'User ID',
            'patient_id' => 'Patient ID',
            'sum' => 'Sum',
            'back_id' => 'ид чека для возврата',
            'shift_id' => 'смена',
            'branch_id' => 'Branch ID',
            'payment_cash' => 'Оплата наличными',
            'payment_card' => 'Оплата платежной картой',
            'webkassa_id' => 'Фискальный признак вебкассы',
            'webkassa_data' => 'Данные вебкассы',
            'nds' => 'НДС',
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
    public function getShift()
    {
        return $this->hasOne(Shift::className(), ['id' => 'shift_id']);
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
    public function getCheckItems()
    {
        return $this->hasMany(CheckItem::className(), ['check_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractCalcs()
    {
        return $this->hasMany(ContractCalc::className(), ['check_id' => 'id']);
    }
}
