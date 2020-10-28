<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dest_template".
 *
 * @property integer $id
 * @property string $name
 * @property integer $branch_id
 *
 * @property DestTemplatePrice[] $destTemplatePrices
 * @property Branch $branch
 */
class DestTemplate extends \yii\db\ActiveRecord {

    public $allowed_spec;
    public $allowed_diagnosis;
    public $selected_price;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'dest_template';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'string', 'max' => 255],
            [['branch_id'], 'integer'],
            [['name'], 'required', 'message' => 'Укажите название'],
            [['selected_price', 'allowed_diagnosis', 'allowed_spec'], 'string'],
            [['selected_price'], 'required', 'message' => 'Выберите услуги'],
            [['selected_price'], 'priceValidate'],
            [['allowed_spec'], 'required', 'message' => 'Выберите специализации'],
            //[['allowed_spec'], 'each', 'rule' => ['integer']]
        ];
    }

    public function priceValidate($field, $attribute) {
        if ($this->$field) {
            $items = json_decode($this->$field, true);
            if (!count($items)) {
                $this->addError($field, 'Выберите услуги');
            } else {
                foreach ($items as $item) {
                    if (!isset($item['id'])) {
                        $this->addError($field, 'Выберите услуги');
                        break;
                    }
                }
            }
        } else {
            $this->addError($field, 'Выберите услуги');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'allowed_spec' => 'Разрешенные специализации'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestTemplatePrices() {
        return $this->hasMany(DestTemplatePrice::className(), ['dest_template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestTemplateDiagnoses() {
        return $this->hasMany(DestTemplateDiagnosis::className(), ['dest_template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestTemplateSpecs() {
        return $this->hasMany(DestTemplateSpec::className(), ['dest_template_id' => 'id']);
    }
    
    public function getPrices() {
        return $this->hasMany(Price::className(), ['id' => 'price_id'])
                        ->viaTable('dest_template_price', ['dest_template_id' => 'id']);
    }
    
    public function getSpecs() {
        return $this->hasMany(Speciality::className(), ['id' => 'spec_id'])
                        ->viaTable('dest_template_spec', ['dest_template_id' => 'id']);
    }
    
    public function getDiagnoses(){
        return $this->hasMany(Mkb::className(), ['id' => 'mkb_id'])
                        ->viaTable('dest_template_diagnosis', ['dest_template_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function saveRel() {

        //удаление прошлых специализаций
        foreach ($this->destTemplateSpecs as $item) {
            $item->delete();
        }
        //добавление
        $specs = json_decode($this->allowed_spec, true);
        foreach ($specs as $spec) {
            $item = new DestTemplateSpec();
            $item->setAttributes([
                'dest_template_id' => $this->id,
                'spec_id' => $spec['id']
            ]);
            $item->save();
        }


        if ($this->allowed_diagnosis) {
            //удаление прошлых диагнозов
            foreach ($this->destTemplateDiagnoses as $item) {
                $item->delete();
            }
            //добавление
            $items = json_decode($this->allowed_diagnosis, true);
            foreach ($items as $item) {
                $diag = new DestTemplateDiagnosis();
                $diag->setAttributes([
                    'dest_template_id' => $this->id,
                    'mkb_id' => $item['id']
                ]);
                $diag->save();
            }
        }
        
        if ($this->selected_price) {
            //удаление прошлых
            foreach ($this->destTemplatePrices as $item) {
                $item->delete();
            }
            //добавление
            $prices = json_decode($this->selected_price, true);
            foreach ($prices as $item) {
                $rel = new DestTemplatePrice();
                $rel->setAttributes([
                    'dest_template_id' => $this->id,
                    'price_id' => $item['id']
                ]);
                $rel->save();
            }
        }
    }
    
    public function delete() {
        foreach ($this->destTemplateDiagnoses as $item) {
            $item->delete();
        }
        foreach ($this->destTemplateSpecs as $item) {
            $item->delete();
        }
        foreach ($this->destTemplatePrices as $item) {
            $item->delete();
        }
        parent::delete();
    }

}
