<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patients;

class CashierSearch extends Patients {

    public
        $patient_id,
        $date_start,
        $date_end;

    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['date_start'] = 'Дата с';
        $labels['date_end'] = 'Дата по';
        $labels['patient_id'] = 'Пациент';

        return $labels;
    }

    public function rules() {
        $rules = parent::rules();
        $rules[] = ['patient_id', 'integer'];
        $rules[] = [['date_start', 'date_end'], 'safe'];

        return $rules;
    }

    public function getPatient() {
        return self::findOne(['id' => $this->patient_id]);
    }

    public function search($params) {
        $query = self::find();
        $query->leftJoin(Direction::tableName(), Direction::tableName() . '.patient_id = ' . self::tableName() . '.id');
        $query->leftJoin(DirectionItem::tableName(), DirectionItem::tableName() . '.direction_id = ' . Direction::tableName() . '.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'direction_created' => [
                    'label' => 'Дата последнего направления',
                    'asc' => [
                        Direction::tableName() . '.created' => SORT_ASC
                    ],
                    'desc' => [
                        Direction::tableName() . '.created' => SORT_DESC
                    ]
                ],
                'patients_search_field' => [
                    'label' => 'Фио пациента',
                    'asc' => [
                        self::tableName() . '.search_field' => SORT_ASC
                    ],
                    'desc' => [
                        self::tableName() . '.search_field' => SORT_DESC
                    ]
                ],
                'sum' => [
                    'label' => 'Стоимость'
                ]
            ],
            'defaultOrder' => [
                'direction_created' => SORT_DESC
            ]
        ]);

        $this->load($params);

        if ($this->patient_id) {
            $query->andWhere([self::tableName() . '.id' => $this->patient_id]);
        }

        if ($this->date_start) {
            $query->andWhere(['>=', Direction::tableName() .'.created', date('Y-m-d 00:00:00', strtotime($this->date_start))]);
        }

        if ($this->date_end) {
            $query->andWhere(['<=', Direction::tableName() .'.created', date('Y-m-d 23:59:59', strtotime($this->date_end))]);
        }

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            Direction::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id,
            DirectionItem::tableName() . '.paid' => false,
            DirectionItem::tableName() . '.canceled' => false,
        ]);

        //$query->groupBy([self::tableName(). '.id']);


        return $dataProvider;
    }
}