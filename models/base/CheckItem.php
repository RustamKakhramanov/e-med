<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "check_item".
 *
 * @property int $id
 * @property int $check_id
 * @property int $direction_item_id
 *
 * @property Check $check
 * @property DirectionItem $directionItem
 */
class CheckItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'check_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['check_id', 'direction_item_id'], 'default', 'value' => null],
            [['check_id', 'direction_item_id'], 'integer'],
            [['check_id'], 'exist', 'skipOnError' => true, 'targetClass' => Check::className(), 'targetAttribute' => ['check_id' => 'id']],
            [['direction_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => DirectionItem::className(), 'targetAttribute' => ['direction_item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_id' => 'Check ID',
            'direction_item_id' => 'Direction Item ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheck()
    {
        return $this->hasOne(Check::className(), ['id' => 'check_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionItem()
    {
        return $this->hasOne(DirectionItem::className(), ['id' => 'direction_item_id']);
    }
}
