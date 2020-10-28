<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contractor".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $deleted
 * @property string $address
 * @property string $bin
 * @property string $phone
 * @property integer $branch_id
 *
 * @property ContractContractor[] $contractContractors
 * @property Branch $branch
 */
class Contractor extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'contractor';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['deleted'], 'boolean'],
            [['name', 'address', 'bin', 'phone'], 'string', 'max' => 255],
            [['name', 'bin'], 'required', 'message' => 'Обязательно'],
            [['branch_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'deleted' => 'Deleted',
            'address' => 'Адрес',
            'bin' => 'БИН',
            'phone' => 'Телефон',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractContractors() {
        return $this->hasMany(ContractContractor::className(), ['contractor_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

}
