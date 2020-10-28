<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Speciality;

/**
 * SpecialitySearch represents the model behind the search form about `app\models\Speciality`.
 */
class SpecialitySearch extends Speciality {

    public $doctors;
    public $doctorsCount;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'doctors'], 'integer'],
            [['name', 'doctorsCount'], 'safe'],
            [['deleted'], 'boolean'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Speciality::find();

//        $subQuery = Speciality::find()->select('count(*) as doctors_count');
//        $query->leftJoin([
//            'doctors_count' => $subQuery
//                ], 'doctors_count.doctors_count = id');
        
        $query->joinWith('doctors')->groupBy('speciality.id');

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
                'name',
                'doctorsCount' => [
                    'asc' => ['count(doctor.id)' => SORT_ASC],
                    'desc' => ['count(doctor.id)' => SORT_DESC],
                    'label' => 'Кол-во врачей'
                ]
            ],
            'defaultOrder' => [
                'name' => SORT_ASC
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
            'speciality.deleted' => 0,
            'speciality.branch_id' => Yii::$app->user->identity->branch_id
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name]);

        return $dataProvider;
    }

}
