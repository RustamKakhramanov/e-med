<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reception_var".
 *
 * @property integer $id
 * @property integer $reception_id
 * @property integer $var_id
 * @property string $value
 * @property string $uid
 *
 * @property Reception $reception
 * @property TemplateVar $var
 */
class ReceptionVar extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'reception_var';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['reception_id', 'var_id'], 'integer'],
            [['value'], 'string'],
            [['uid'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'reception_id' => 'Reception ID',
            'var_id' => 'Var ID',
            'value' => 'Value',
            'uid' => 'Uid'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReception() {
        return $this->hasOne(Reception::className(), ['id' => 'reception_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVar() {
        return $this->hasOne(TemplateVar::className(), ['id' => 'var_id']);
    }

}
