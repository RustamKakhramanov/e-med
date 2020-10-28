<?php

namespace app\models;

use app\models\webkassa\Client;
use Yii;
use app\helpers\Utils;

class Check extends \app\models\base\Check {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['created', 'updated'], 'safe'],
            [['user_id', 'patient_id', 'sum'], 'required'],
            [['user_id', 'patient_id', 'back_id', 'shift_id', 'branch_id'], 'default', 'value' => null],
            [['user_id', 'patient_id', 'back_id', 'shift_id', 'branch_id'], 'integer'],
            [['sum', 'payment_cash', 'payment_card'], 'number'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patients::className(), 'targetAttribute' => ['patient_id' => 'id']],
            [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shift::className(), 'targetAttribute' => ['shift_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            $this->updated = date('Y-m-d H:i:s');

            if (!$this->id) {
                $this->created = date('Y-m-d H:i:s');
            }

            return true;
        }
        return false;
    }

    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['created'] = 'Дата';
        $labels['sum'] = 'Сумма';

        return $labels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
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
    public function getShift() {
        return $this->hasOne(Shift::className(), ['id' => 'shift_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckItems() {
        return $this->hasMany(CheckItem::className(), ['check_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractCalcs() {
        return $this->hasMany(ContractCalc::className(), ['check_id' => 'id']);
    }

    public function getNumber() {
        return Utils::number_pad($this->id, 6);
    }

    /**
     * список услуг доступных к возврату
     * @return array
     */
    public function getAvailableToCancelItems() {
        $cancelChecks = self::find()
            ->where([
                'back_id' => $this->id
            ])
            ->all();

        $cancelIds = [];
        foreach ($cancelChecks as $check) {
            foreach ($check->checkItems as $item) {
                $cancelIds[] = $item->direction_item_id;
            }
        }

        $result = [];
        foreach ($this->checkItems as $item) {
            if (!in_array($item->direction_item_id, $cancelIds)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * создать чек возврата
     * @param $id исходный чек
     * @param $services услуги
     */
    public static function createBack($id, $services = []) {
        if (!$services) {
            return false;
        }
        $prev = self::findOne(['id' => $id]);
        $model = new self();
        $model->setAttributes([
            'user_id' => Yii::$app->user->identity->id,
            'patient_id' => $prev->patient_id,
            'shift_id' => Shift::getCurrent()->id,
            'branch_id' => $prev->branch_id,
            'back_id' => $prev->id,
            'sum' => 0
        ]);
        $model->save();
        $sum = 0;

        foreach ($services as $key => $value) {
            $directionItem = DirectionItem::findOne(['id' => $key]);
            $directionItem->canceled = true;
            $directionItem->paid = false;
            $directionItem->save();

            $checkItem = new CheckItem();
            $checkItem->setAttributes([
                'check_id' => $model->id,
                'direction_item_id' => $directionItem->id
            ]);
            $checkItem->save();
            $sum += $value;
        }

        $model->sum = $sum;
        $model->save();

        if ($prev->webkassa_id) {
            $data = [
                'CashboxUniqueNumber' => $prev->shift->cashbox->webkassa_id,
                'OperationType' => Client::OPERATION_TYPE_CANCEL,
                'Positions' => [],
                'Payments' => []
            ];
            $totalTax = 0;
            $totalSum = 0;
            /** @var $item CheckItem */
            foreach ($services as $key => $value) {
                $directionItem = DirectionItem::findOne(['id' => $key]);
                $tax = 0;
                if ($prev->shift->cashbox->use_nds) {
                    $sum = $directionItem->count * $directionItem->cost;
                    $tax = round($sum - $sum / 1.12, 2);
                    $totalTax += $tax;
                    $totalSum += $sum;
                }
                $data['Positions'][] = [
                    'Count' => $directionItem->count,
                    'Price' => $directionItem->cost,
                    'Tax' => $tax,
                    'TaxType' => $prev->shift->cashbox->use_nds ? Client::TAX_TYPE_NDS : Client::TAX_TYPE_NO,
                    'PositionName' => $directionItem->price->title
                ];
            }

            $data['Payments'][] = [
                'Sum' => $totalSum,
                'PaymentType' => Client::PAYMENT_TYPE_CARD
            ];

            $resp = Client::check($data);
            $model->webkassa_id = $resp['CheckNumber'];
            $model->webkassa_data = json_encode($resp);
            $model->save();
        }
    }

    public function getWebkassaData() {
        return json_decode($this->webkassa_data, true);
    }

    /**
     * отправить в вебкассу
     */
    public function sendWebkassa() {
        if ($this->webkassa_id) {
            return false;
        }
        $data = [
            'CashboxUniqueNumber' => $this->shift->cashbox->webkassa_id,
            'OperationType' => $this->back_id ? Client::OPERATION_TYPE_CANCEL : Client::OPERATION_TYPE_BUY,
            'Positions' => [],
            'Payments' => []
        ];

        $totalTax = 0;
        /** @var $item CheckItem */
        foreach ($this->checkItems as $item) {
            $tax = 0;
            if ($this->shift->cashbox->use_nds) {
                $sum = $item->directionItem->count * $item->directionItem->cost;
                $tax = round($sum - $sum / 1.12, 2);
                $totalTax += $tax;
            }
            $data['Positions'][] = [
                'Count' => $item->directionItem->count,
                'Price' => $item->directionItem->cost,
                'Tax' => $tax,
                'TaxType' => $this->shift->cashbox->use_nds ? Client::TAX_TYPE_NDS : Client::TAX_TYPE_NO,
                'PositionName' => $item->directionItem->price->title
            ];
        }

        if ($this->payment_cash) {
            $data['Payments'][] = [
                'Sum' => $this->payment_cash,
                'PaymentType' => Client::PAYMENT_TYPE_CASH
            ];
        }

        if ($this->payment_card) {
            $data['Payments'][] = [
                'Sum' => $this->payment_card,
                'PaymentType' => Client::PAYMENT_TYPE_CARD
            ];
        }
        try {
            $resp = Client::check($data);
            $this->webkassa_id = $resp['CheckNumber'];
            $this->webkassa_data = json_encode($resp);
            $this->nds = $totalTax;
            $this->save();
        } catch (\Exception $e) {
            $log = new CheckLog();
            $log->setAttributes([
                'check_id' => $this->id,
                'user_id' => Yii::$app->user->identity->id,
                'date' => date('Y-m-d H:i:s'),
                'params' => json_encode($data),
                'message' => $e->getMessage()
            ]);
            $log->save();
            Yii::$app->session->setFlash(uniqid(), ['error', 'Ошибка передачи: ' . $e->getMessage()]);
        }
    }
}
