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

class CashierCancelForm extends Model {

    public
        $check_id,
        $payment_cash = 0,
        $payment_card = 0,
        $services = [];

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['check_id'], 'required', 'message' => 'Требуется заполнить'],
            [['payment_cash', 'payment_card'], 'integer'],
            [['services'], 'safe'],
            [['payment_cash', 'payment_card'], 'paymentValidate', 'skipOnEmpty' => false]
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

        $payment = (float)$this->payment_cash + (float)$this->payment_card;
        if ($payment != $total) {
            $this->addError('payment_cash', 'Значение оплаты не соотвествует сумме услуг');
        } else {
            if ($payment == 0) {
                $this->addError('payment_cash', 'Значение оплаты должно быть больше 0');
            }
        }
    }

    public function getCheck() {
        return Check::findOne($this->check_id);
    }

    public function process() {
        if (!$this->services) {
            return false;
        }

        $prev = Check::findOne(['id' => $this->check_id]);
        $model = new Check();
        $model->setAttributes([
            'user_id' => Yii::$app->user->identity->id,
            'patient_id' => $prev->patient_id,
            'shift_id' => Shift::getCurrent()->id,
            'branch_id' => $prev->branch_id,
            'back_id' => $prev->id,
            'sum' => ($this->payment_cash + $this->payment_card),
            'payment_cash' => $this->payment_cash,
            'payment_card' => $this->payment_card
        ]);
        $model->save();

        foreach ($this->services as $key => $value) {
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
        }
        $model->save();
        $model->sendWebkassa();
    }
}
