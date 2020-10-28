<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dest_template_price".
 *
 * @property integer $id
 * @property integer $dest_template_id
 * @property integer $price_id
 *
 * @property DestTemplate $destTemplate
 * @property Price $price
 */
class DestTemplatePrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dest_template_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dest_template_id', 'price_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dest_template_id' => 'Dest Template ID',
            'price_id' => 'Price ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestTemplate()
    {
        return $this->hasOne(DestTemplate::className(), ['id' => 'dest_template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(Price::className(), ['id' => 'price_id']);
    }
}
