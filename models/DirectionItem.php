<?php

namespace app\models;

use Yii;
use app\helpers\Utils;
use yii\helpers\ArrayHelper;

class DirectionItem extends \app\models\base\DirectionItem {

    const STATUS_PLANNED = 0,
        STATUS_INPROGRESS = 1,
        STATUS_COMPLETED = 2;

    protected $_statusLabels = [
        self::STATUS_PLANNED => 'Запланировано',
        self::STATUS_INPROGRESS => 'Выполняется',
        self::STATUS_COMPLETED => 'Завершено'
    ];

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['direction_id', 'doctor_id', 'price_id', 'count', 'cancel_user_id', 'status'], 'default', 'value' => null],
            [['direction_id', 'doctor_id', 'price_id', 'count', 'cancel_user_id', 'status'], 'integer'],
            [['price_id', 'count'], 'required'],
            [['cost'], 'number'],
            [['paid', 'canceled'], 'boolean'],
            [['cancel_reason'], 'string'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Price::className(), 'targetAttribute' => ['price_id' => 'id']],
            [['cancel_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cancel_user_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckItems() {
        return $this->hasMany(CheckItem::className(), ['direction_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirection() {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCancelUser() {
        return $this->hasOne(User::className(), ['id' => 'cancel_user_id']);
    }

    public function getSumm() {
        return $this->cost * $this->count;
    }

    public function getServiceName() {
        return $this->price->title;
    }

    public function getNumberPrint() {
        return Utils::number_pad($this->id, 6);
    }

    public function getServiceCost() {
        return Utils::ncost($this->cost);
    }

    public function getStatusPrint() {
        return $this->_statusLabels[$this->status];
    }

    /**
     * селект врачей которые могут оказывать услугу прайса
     * @return array
     */
    public function getDoctorsCanUse() {
        if ($this->price_id) {
            $items = Doctor::find()
                ->joinWith('doctorPrices')
                ->where([
                    DoctorPrice::tableName() . '.price_id' => $this->price_id
                ])
                ->orderBy([
                    Doctor::tableName() . '.last_name' => SORT_ASC,
                    Doctor::tableName() . '.first_name' => SORT_ASC,
                    Doctor::tableName() . '.middle_name' => SORT_ASC
                ])
                ->all();

            return ArrayHelper::map($items, 'id', 'fio');
        }

        return [];
    }
}
