<?php

namespace app\models\form;

use app\models\Direction;
use app\models\DirectionItem;
use app\models\Patients;
use Yii;
use yii\base\Model;

class DirectionMaster extends Model {

    public $patient_id;
    public $direction_id;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['patient_id'], 'required'],
            [['patient_id', 'direction_id'], 'integer'],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patients::className(), 'targetAttribute' => ['patient_id' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'patient_id' => 'Пациент'
        ];
    }

    public function getPatient() {
        if ($this->patient_id) {
            $model = Patients::findOne(['id' => $this->patient_id]);
            if ($model) {
                return $model;
            }
        }
    }

    public function commonValidate() {
        $errors = [];
        foreach (Yii::$app->request->post('items', []) as $key => $row) {
            if ($row['id']) {
                $dir = DirectionItem::findOne(['id' => $row['id']]);
            } else {
                $dir = new DirectionItem();
            }
            $dir->setAttributes([
                'price_id' => $row['price_id'],
                'cost' => $row['cost'],
                'count' => $row['count'],
                'doctor_id' => $row['doctor_id'],
            ]);
            $dir->validate();
            foreach ($dir->errors as $eKey => $error) {
                $errors['items-' . $key . '-' . $eKey] = $error;
            }
        }

        return $errors;
    }

    public function process() {
        if (!$this->direction_id) {
            $model = new Direction();
            $model->setAttributes([
                'created' => date('Y-m-d H:i:s'),
                'user_id' => Yii::$app->user->identity->id,
                'branch_id' => Yii::$app->user->identity->branch_id,
                'patient_id' => $this->patient_id
            ]);
            $model->save();
        } else {
            $model = Direction::findOne(['id' => $this->direction_id]);
        }

        //удаление существующих
        if ($this->direction_id) {
            foreach ($model->directionItems as $directionItem) {
                $founded = false;
                foreach (Yii::$app->request->post('items', []) as $row) {
                    if ($row['id'] == $directionItem->id) {
                        $founded = true;
                        break;
                    }
                }
                if (!$founded) {
                    $directionItem->delete();
                }
            }
        }

        foreach (Yii::$app->request->post('items', []) as $row) {
            if ($row['id']) {
                $dir = DirectionItem::findOne(['id' => $row['id']]);
            } else {
                $dir = new DirectionItem();
            }
            $dir->setAttributes([
                'price_id' => $row['price_id'],
                'cost' => $row['cost'],
                'count' => $row['count'],
                'doctor_id' => $row['doctor_id']
            ]);
            $dir->direction_id = $model->id;
            $dir->save();
        }
    }
}
