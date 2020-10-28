<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subdivision".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $deleted
 * @property integer $branch_id
 * 
 * @property Branch $branch
 */
class Subdivision extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'subdivision';
    }

    /**
     * @inheritdoc
     */
    public function rules() {

        return [
            [['name'], 'required', 'message' => 'Обязательно'],
            [['deleted'], 'boolean'],
            [['branch_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['name', 'nameValidate']
        ];
    }

    public function nameValidate($model, $attribute) {
        $name = $this->$model;
        $count = Subdivision::find()
                ->andWhere(['deleted' => 0])
                ->andWhere(['ilike', 'name', $name])
                ->andWhere(['!=', 'id', (int) $this->id])
                ->andWhere(['branch_id' => Yii::$app->user->identity->branch_id])
                ->count();
        if ($count) {
            $this->addError($model, 'Такая запись уже существует');
        };
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'deleted' => 'Deleted',
        ];
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
