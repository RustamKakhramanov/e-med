<?php

namespace app\models;

use Yii;

class CheckItem extends \app\models\base\CheckItem {
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['check_id', 'direction_item_id'], 'default', 'value' => null],
            [['check_id', 'direction_item_id'], 'integer'],
            [['check_id'], 'exist', 'skipOnError' => true, 'targetClass' => Check::className(), 'targetAttribute' => ['check_id' => 'id']],
            [['direction_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => DirectionItem::className(), 'targetAttribute' => ['direction_item_id' => 'id']],
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
    public function getDirectionItem() {
        return $this->hasOne(DirectionItem::className(), ['id' => 'direction_item_id']);
    }
}
