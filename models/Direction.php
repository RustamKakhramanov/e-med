<?php

namespace app\models;

use Yii;
use app\helpers\Utils;

class Direction extends \app\models\base\Direction {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['created'], 'safe'],
            [['user_id', 'branch_id', 'patient_id'], 'required'],
            [['user_id', 'branch_id', 'patient_id'], 'default', 'value' => null],
            [['user_id', 'branch_id', 'patient_id'], 'integer'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patients::className(), 'targetAttribute' => ['patient_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
    public function getPatient() {
        return $this->hasOne(Patients::className(), ['id' => 'patient_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionItems() {
        return $this->hasMany(DirectionItem::className(), ['direction_id' => 'id']);
    }

    public function getNumberPrint() {
        return Utils::number_pad($this->id, 6);
    }

    /**
     * разрешено ли редактирование направления
     * @return bool
     */
    public function getCanEdit() {
        //проверка что неоплачено
        $paid = false;
        foreach ($this->directionItems as $item) {
            if ($item->paid) {
                $paid = true;
                break;
            }
        }
        if ($paid) return false;

        //проверка на проведение осмотров врача
        $receptionFind = false;
        foreach ($this->directionItems as $item) {
            $reception = Reception::findOne([
                'direction_id' => $item->id
            ]);
            if ($reception) {
                $receptionFind = true;
                break;
            }
        }
        if ($receptionFind) return false;

        return true;
    }
}
