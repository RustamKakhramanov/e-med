<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "check_items".
 *
 * @property integer $id
 * @property integer $check_id
 * @property integer $direction_id
 *
 * @property Check $check
 * @property Direction $direction
 */
class CheckItems extends \yii\db\ActiveRecord {
    
    //состояние наличия возвратного чека с такой услугой
    public $canceled = false;


    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'check_items';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['check_id', 'direction_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'check_id' => 'Check ID',
            'direction_id' => 'Direction ID',
        ];
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
    public function getDirection() {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

}
