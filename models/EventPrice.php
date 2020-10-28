<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "event_price".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $price_id
 * @property integer $count
 * @property integer $dir
 * @property integer $canceled
 *
 * @property Price $price
 * @property Speciality $speciality
 */
class EventPrice extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'event_price';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['event_id', 'price_id', 'count'], 'required'],
            [['event_id', 'price_id', 'count'], 'integer'],
            [['dir', 'canceled'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'price_id' => 'Price ID',
            'count' => 'Count',
        ];
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
    public function getSpeciality() {
        return $this->hasOne(Speciality::className(), ['id' => 'speciality_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent() {
        return $this->hasOne(Event::className(), ['id' => 'event_id']);
    }

}
