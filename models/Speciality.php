<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "speciality".
 *
 * @property integer $id
 * @property string $name
 * @property integer $branch_id
 *
 * @property Branch $branch
 */
class Speciality extends \yii\db\ActiveRecord {

    public $selected_price = '[]';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'speciality';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required', 'message' => 'Обязательно'],
            [['name'], 'string', 'max' => 255],
            [['branch_id'], 'integer'],
            ['name', 'nameValidate'],
            ['selected_price', 'priceValidate']
        ];
    }

    public function nameValidate($model, $attribute) {

        $name = $this->$model;
        $count = Speciality::find()
            ->andWhere(['deleted' => 0])
            ->andWhere(['=', 'name', $name])
            ->andWhere(['!=', 'id', (int)$this->id])
            ->andWhere(['branch_id' => Yii::$app->user->identity->branch_id])
            ->count();

        if ($count) {
            $this->addError($model, 'Такая запись уже существует');
        };
    }

    public function priceValidate($field, $attribute) {
        if ($this->$field) {
            $priceItems = json_decode($this->$field, true);
            if (count($priceItems)) {
                $ids = [];
                foreach ($priceItems as $item) {

                    if (!isset($item['id'])) {
                        $this->addError($field, 'Не указана услуга');
                        break;
                    }

                    if (!in_array($item['id'], $ids)) {
                        $ids[] = $item['id'];
                    } else {
                        $this->addError($field, 'Не допускается дублирование услуг');
                        break;
                    }
                }
            }
        } else {
            //$this->addError($field, 'Требуется выбрать услуги из прайса');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function getDoctors() {
        return $this->hasMany(Doctor::className(), ['id' => 'doctor_id'])
            ->viaTable('doctor_speciality', ['speciality_id' => 'id']);
    }

    public function getDoctorsCount() {
        return $this->hasMany(Doctor::className(), ['id' => 'doctor_id'])
            ->viaTable('doctor_speciality', ['speciality_id' => 'id'])->count();
    }

    public function getSpecialities() {
        return $this->hasMany(Speciality::className(), ['id' => 'speciality_id'])->via('doctorSpecialities');
    }

    public function getPrices() {
        return $this->hasMany(Price::className(), ['id' => 'price_id'])
            ->viaTable('speciality_price', ['speciality_id' => 'id'])
            ->orderBy(['title' => SORT_ASC]);
    }

    /**
     * сохранить связи специализаций
     */
    public function savePrice($data) {
        foreach ($this->prices as $item) {
            $this->unlink('prices', $item, true);
        }

        foreach ($data as $id) {
            $price = Price::findOne(['id' => $id]);
            if ($price) {
                $this->link('prices', $price);
            }
        }

    }

    public function deleteSafe() {
        $this->deleted = 1;
        $this->save();
    }
}
