<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reception;

/**
 * ReceptionSearch represents the model behind the search form of `app\models\Reception`.
 */
class ReceptionSearch extends Reception {
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'doctor_id', 'direction_id', 'template_id', 'branch_id'], 'integer'],
            [['created', 'updated', 'html'], 'safe'],
            [['deleted', 'draft'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
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
    public function search($params, $pageSize = 10) {
        $query = Reception::find()
            ->joinWith('directionItem')
            ->joinWith('directionItem.direction')
            ->joinWith('directionItem.direction.patient');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize
            ]
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'created' => [
                    'label' => 'Дата'
                ],
                'direction_id' => [
                    'label' => 'Номер направления'
                ],
                Patients::tableName() . '.search_field' => [
                    'label' => 'Пациент'
                ]
            ],
            'defaultOrder' => [
                'created' => SORT_DESC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'doctor_id' => $this->doctor_id,
            'direction_id' => $this->direction_id,
            'created' => $this->created,
            'updated' => $this->updated,
            'template_id' => $this->template_id,
            'deleted' => $this->deleted,
            'draft' => $this->draft,
            'branch_id' => $this->branch_id,
        ]);

        $query->andFilterWhere(['ilike', 'html', $this->html]);

        return $dataProvider;
    }
}