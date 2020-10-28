<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "price".
 *
 * @property integer $id
 * @property string $title
 * @property string $title_print
 * @property integer $type
 * @property string $group_id
 * @property string $cost
 * @property boolean $deleted
 * @property boolean $repeated
 * @property integer $branch_id
 * 
 * @property Branch $branch
 */
class Price extends \yii\db\ActiveRecord {
    
    public static $types = [
        0 => 'Прием специалиста',
        1 => 'Лаб. исследования',
        2 => 'Инст. исследования',
        3 => 'Манипуляции'
    ];
    
    public static $icons = [
        0 => 'ico-stethoscope',
        1 => 'ico-lab',
        2 => 'ico-pulse',
        3 => 'ico-scalpel'
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'price';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type', 'branch_id'], 'integer'],
            [['deleted', 'repeated'], 'boolean'],
            [['title', 'cost', 'group_id'], 'required', 'message' => 'Обязательно'],
            [['title', 'title_print'], 'string', 'max' => 255],
            [['cost'], 'costValidate']
        ];
    }
    
    public function costValidate($field, $attribute) {
        if ($this->$field !== null) {
            $this->$field = str_replace(',', '.', $this->$field);
            if (is_numeric($this->$field)) {
                if ($this->$field >= 0) {
                    if (!preg_match('/^[0-9]*(\.{0,1}[0-9]{1,2})?$/', $this->$field)) {
                        $this->addError($field, 'Допустимый формат 00.00');
                    }
                } else {
                    $this->addError($field, 'Только положительное');
                }
            } else {
                $this->addError($field, 'Требуется указать число');
            }
        } else {
            $this->addError($field, 'Обязательно');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'title_print' => 'Название для печати',
            'type' => 'Вид операции',
            'group_id' => 'Группа',
            'cost' => 'Базовая стоимость',
            'repeated' => 'Повторный прием'
        ];
    }

    public function setDefault() {
        $this->type = 0;
        $this->branch_id = Yii::$app->user->identity->branch_id;
        //$this->group = 0;
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
    public function getGroup() {
        return $this->hasOne(PriceGroup::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorPrices() {
        return $this->hasMany(DoctorPrice::className(), ['price_id' => 'id']);
    }
    
    public function getTypeText() {
        return self::$types[$this->type];
    }
    
    public function getIconClass() {
        return self::$icons[$this->type];
    }


    public function deleteSafe() {
        $this->deleted = 1;
        $this->save(false);
    }

}
