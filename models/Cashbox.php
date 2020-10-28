<?php

namespace app\models;

use Yii;

class Cashbox extends \app\models\base\Cashbox {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['branch_id', 'name', 'org_name', 'webkassa_id', 'bin', 'nds_serie', 'nds_number', 'operator_name', 'kkt', 'rnk'], 'required', 'message' => 'Обязательно'],
            [['branch_id'], 'default', 'value' => null],
            [['branch_id'], 'integer'],
            [['deleted', 'use_nds'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShifts() {
        return $this->hasMany(Shift::className(), ['cashbox_id' => 'id']);
    }
}
