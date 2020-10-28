<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device".
 *
 * @property integer $id
 * @property string $serial
 * @property string $name
 * @property boolean $deleted
 * @property string $pass
 * @property integer $branch_id
 * 
 * @property Branch $branch
 */
class Device extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'device';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['deleted'], 'boolean'],
            [['branch_id'], 'integer'],
            [['serial', 'name', 'pass'], 'string', 'max' => 255],
            [['serial', 'name', 'pass'], 'required', 'message' => 'Обязательно']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'serial' => 'Serial',
            'name' => 'Name',
            'deleted' => 'Deleted',
            'pass' => 'Pass'
        ];
    }

    public static function getCount() {
        return Device::find()->count();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function deleteSafe() {
        $this->deleted = 1;
        $this->save();
    }

}
