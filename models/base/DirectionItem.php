<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "direction_item".
 *
 * @property int $id
 * @property int $direction_id Заказ
 * @property int $doctor_id Специалист
 * @property int $price_id Услуга
 * @property double $cost Стоимость
 * @property int $count Кол-во
 * @property bool $paid Оплачено
 * @property bool $canceled Отменено
 * @property string $cancel_reason Причина отмены
 * @property int $cancel_user_id Кто отменил
 * @property int $status Статус
 *
 * @property CheckItem[] $checkItems
 * @property Direction $direction
 * @property Doctor $doctor
 * @property Price $price
 * @property User $cancelUser
 */
class DirectionItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'direction_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'direction_id' => 'Заказ',
            'doctor_id' => 'Специалист',
            'price_id' => 'Услуга',
            'cost' => 'Стоимость',
            'count' => 'Кол-во',
            'paid' => 'Оплачено',
            'canceled' => 'Отменено',
            'cancel_reason' => 'Причина отмены',
            'cancel_user_id' => 'Кто отменил',
            'status' => 'Статус',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckItems()
    {
        return $this->hasMany(CheckItem::className(), ['direction_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(Price::className(), ['id' => 'price_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCancelUser()
    {
        return $this->hasOne(User::className(), ['id' => 'cancel_user_id']);
    }
}
