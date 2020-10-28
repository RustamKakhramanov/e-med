<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Subdivision;

/**
 * SubdivisionSearch represents the model behind the search form about `app\models\Subdivision`.
 */
class SubdivisionSearch extends Subdivision {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['name'], 'safe'],
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
        $query = Subdivision::find();

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
            'deleted' => 0,
            'branch_id' => Yii::$app->user->identity->branch_id
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
