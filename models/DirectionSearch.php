<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Direction;
use app\models\Patients;
use app\models\Price;
use app\models\Doctor;

class DirectionSearch extends Model {

    public $patient_id,
        $doctor_id,
        $price_id,
        $date_from,
        $date_to,
        $paid,
        $canceled,
        $number;

    public function rules() {
        return [
            [['patient_id', 'doctor_id', 'price_id'], 'integer'],
            [['paid', 'canceled'], 'boolean'],
            [['date_from', 'date_to', 'number'], 'string']
        ];
    }

    public function attributeLabels() {
        return [
            'number' => 'Номер'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient() {
        if ($this->patient_id) {
            return Patients::findOne(['id' => $this->patient_id]);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor() {
        if ($this->doctor_id) {
            return Doctor::findOne(['id' => $this->doctor_id]);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice() {
        return Price::findOne(['id' => $this->price_id]);
    }

    public function search($params, $pageSize = 10) {
        $query = DirectionItem::find()
            ->joinWith('direction')
            ->joinWith('direction.patient')
            ->joinWith('price');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize
            ]
        ]);

        //sort
        $dataProvider->setSort([
            'attributes' => [
                DirectionItem::tableName() .'.id' => [
                    'label' => '#'
                ],
                Direction::tableName() . '.created',
                Price::tableName() . '.title',
                Direction::tableName() . '.id' => [
                    'label' => 'Номер'
                ],
                Patients::tableName() . '.search_field' => [
                    'label' => 'Пациент'
                ],
                DirectionItem::tableName() . '.paid' => [
                    'label' => 'Оплачено'
                ],
                Doctor::tableName() . '.fio' => [
                    'label' => 'Специалист',
                    'asc' => [
                        Doctor::tableName() . 'last_name' => SORT_ASC,
                        Doctor::tableName() . 'first_name' => SORT_ASC,
                        Doctor::tableName() . 'middle_name' => SORT_ASC,
                    ],
                    'desc' => [
                        Doctor::tableName() . 'last_name' => SORT_DESC,
                        Doctor::tableName() . 'first_name' => SORT_DESC,
                        Doctor::tableName() . 'middle_name' => SORT_DESC,
                    ],
                ]
            ],
            'defaultOrder' => [
                Direction::tableName() . '.created' => SORT_DESC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            Direction::tableName() . '.patient_id' => $this->patient_id,
            Direction::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id,
            DirectionItem::tableName() . '.doctor_id' => $this->doctor_id,
            DirectionItem::tableName() . '.paid' => $this->paid,
            DirectionItem::tableName() . '.canceled' => $this->canceled
        ]);

        if ($this->date_from) {
            $query->andWhere(['>=', Direction::tableName() . '.created', date('Y-m-d 00:00:00', strtotime($this->date_from))]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', Direction::tableName() . '.created', date('Y-m-d 00:00:00', strtotime($this->date_to))]);
        }

        if ($this->number) {
            $val = ltrim($this->number, '0');
            $query->andWhere(['=', Direction::tableName() . '.id', $val]);
        }

        return $dataProvider;
    }

    public function actionReception($id) {

    }
}
