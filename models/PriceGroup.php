<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "price_group".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $deleted
 * @property integer $branch_id
 *
 * @property Branch $branch
 * @property Price[] $prices
 */
class PriceGroup extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'price_group';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['branch_id'], 'integer'],
            [['name'], 'string'],
            [['deleted'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems() {
        return $this->hasMany(Price::className(), ['group_id' => 'id']);
    }

    public function getActiveItems() {
        return $this->hasMany(Price::className(), ['group_id' => 'id'])->where(['deleted' => false]);
    }

    public function getPriceCount() {
        return $this->hasMany(Price::className(), ['group_id' => 'id'])->where(['deleted' => false])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function deleteSafe() {

        foreach ($this->items as $item) {
            $item->deleteSafe();
        }

        $this->deleted = 1;
        $this->save(false);
    }

}
