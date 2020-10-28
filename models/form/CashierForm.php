<?php

namespace app\models\form;

use app\models\Check;
use app\models\CheckItem;
use app\models\CheckItems;
use app\models\DirectionItem;
use app\models\Reception;
use app\models\ReceptionVar;
use app\models\Shift;
use app\models\webkassa\Client;
use Yii;
use yii\base\Model;
use app\models\Template;

class CashierForm extends Model {

    public
        $patient_id,
        $payment_cash = 0,
        $payment_card = 0,
        $services = [];

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['patient_id'], 'required', 'message' => 'Требуется заполнить'],
            [['payment_cash', 'payment_card', 'patient_id'], 'integer'],
            [['services'], 'safe'],
            [['payment_cash'], 'paymentValidate', 'skipOnEmpty' => false]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'payment_cash' => 'Наличными',
            'payment_card' => 'Платежной картой'
        ];
    }

    public function paymentValidate($attr, $field) {
        $total = 0;
        if ($this->services) {
            foreach ($this->services as $key => $value) {
                $total += $value;
            }
        }

        $payment = (float) $this->payment_cash + (float) $this->payment_card;
        if ($payment != $total) {
            $this->addError('payment_cash', 'Значение оплаты не соотвествует сумме услуг');
        } else {
            if ($payment == 0) {
                $this->addError('payment_cash', 'Значение оплаты должно быть больше 0');
            }
        }
    }

    public function process() {
        $check = new Check();
        $check->setAttributes([
            'user_id' => Yii::$app->user->identity->id,
            'branch_id' => Yii::$app->user->identity->branch_id,
            'patient_id' => $this->patient_id,
            'payment_cash' => $this->payment_cash,
            'payment_card' => $this->payment_card,
            'sum' => ($this->payment_cash + $this->payment_card),
            'shift_id' => Shift::getCurrent()->id
        ]);
        $check->save();

        foreach ($this->services as $key => $value) {
            $directionItem = DirectionItem::findOne(['id' => $key]);
            $directionItem->paid = true;
            $directionItem->save();

            $checkItem = new CheckItem();
            $checkItem->setAttributes([
                'check_id' => $check->id,
                'direction_item_id' => $directionItem->id
            ]);
            $checkItem->save();
        }

        $check->sendWebkassa();
    }
}
