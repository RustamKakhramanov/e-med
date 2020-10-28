<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Doctor;

/**
 * DoctorSearch represents the model behind the search form about `app\models\Doctor`.
 */
class DoctorSearch extends Doctor {

    public $speciality_main;
    public $name_fio;
    public $spec;
    public $is_fired = 0;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'subdivision_id', 'spec', 'speciality_main'], 'integer'],
            [['first_name', 'last_name', 'middle_name', 'birthday', 'name_fio', 'is_fired', 'search_field'], 'safe'],
            [['sex', 'fired', 'deleted'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return array_merge(
                parent::attributeLabels(), [
            'fired' => 'Показывать уволенных',
            'spec' => 'Специализация',
            'name_fio' => 'Фио',
            'is_fired' => 'Показывать уволенных'
                ]
        );
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Doctor::find()
                ->joinWith('specialities')
                ->joinWith('scheduleTemplate')
                ->groupBy('doctor.id');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        //sort
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'name_fio' => [
                    'label' => 'Специалист',
                    'asc' => [
                        'last_name' => SORT_ASC,
                        'first_name' => SORT_ASC,
                        'middle_name' => SORT_ASC,
                    ],
                    'desc' => [
                        'last_name' => SORT_DESC,
                        'first_name' => SORT_DESC,
                        'middle_name' => SORT_DESC,
                    ],
                ],
                'subdivision_id',
                'specialities.name' => [
                    'label' => 'Основная специализация',
                    'asc' => ['speciality.name' => SORT_ASC],
                    'desc' => ['speciality.name' => SORT_DESC],
                ],
                'sex',
                'birthday'
            ],
            'defaultOrder' => [
                'name_fio' => SORT_ASC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sex' => $this->sex,
            'birthday' => $this->birthday,
            'subdivision_id' => $this->subdivision_id,
            //'fired' => $this->fired,
            'fired' => $this->is_fired ? null : 0,
            Doctor::tableName() . '.deleted' => false,
            Doctor::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id,
            'speciality.id' => $this->spec
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
                ->andFilterWhere(['like', 'last_name', $this->last_name])
                ->andFilterWhere(['like', 'middle_name', $this->middle_name]);
        //->andFilterWhere(['ilike', 'CONCAT(' . Doctor::tableName() . '.last_name, \' \', ' . Doctor::tableName() . '.first_name' . ')', $this->name_fio]);
        //поиск фио специалиста
        if ($this->name_fio) {
            $query->andWhere(['ilike', 'search_field', $this->name_fio]);

//            $fio = explode(' ', $this->name_fio);
//            $groups = ['or'];
//            $params = [];
//            $fields = ['last_name', 'first_name', 'middle_name'];
//
//            foreach ($fio as $key => $word) {
//                $params[':fio' . $key] = $word . '%';
//            }
//
//            foreach ($fields as $field) {
//                $group = ['or'];
//                foreach ($params as $key => $param) {
//                    $group[] = Doctor::tableName() . '.' . $field . ' ilike ' . $key;
//                }
//                $groups[] = $group;
//            }
//
//            $query->andWhere($groups, $params);
        }

        return $dataProvider;
    }

}
