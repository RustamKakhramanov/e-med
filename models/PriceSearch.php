<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Price;

/**
 * PriceSearch represents the model behind the search form about `app\models\Price`.
 */
class PriceSearch extends Price {

    public $cost_min;
    public $cost_max;
    public $doctor_id;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'type', 'cost_min', 'cost_max', 'group_id', 'doctor_id'], 'integer'],
            [['title', 'title_print'], 'safe'],
            [['cost'], 'number'],
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
     * @inheritdoc
     */
    public function attributeLabels() {
        return array_merge(
            parent::attributeLabels(), [
                'cost_min' => 'Стоимость от',
                'cost_max' => 'до'
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
        $query = Price::find()
            ->joinWith('group')
            ->joinWith('doctorPrices')
        ;

        //debug($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        //sort
        $dataProvider->setSort([
            'attributes' => [
                'title' => [
                    'asc' => [
                        'title' => SORT_ASC
                    ],
                    'desc' => [
                        'title' => SORT_DESC
                    ],
                ],
                'group_name' => [
                    'label' => 'Группа',
                    'asc' => [
                        PriceGroup::tableName() . '.name' => SORT_ASC,
                    ],
                    'desc' => [
                        PriceGroup::tableName() . '.name' => SORT_DESC
                    ],
                ],
                'cost'
            ],
            'defaultOrder' => [
                'title' => SORT_ASC
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
            self::tableName() . '.type' => $this->type,
            self::tableName() . '.cost' => $this->cost,
            self::tableName() . '.group_id' => $this->group_id,
            self::tableName() . '.deleted' => false,
            self::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id
        ]);

        $query->andFilterWhere(['ilike', self::tableName() . '.title', $this->title])
            ->andFilterWhere(['ilike', self::tableName() . '.title_print', $this->title_print])
            ->andFilterWhere(['>=', self::tableName() . '.cost', $this->cost_min])
            ->andFilterWhere(['<=', self::tableName() . '.cost', $this->cost_max]);

        if ($this->doctor_id) {
            $query->andWhere([
                DoctorPrice::tableName() . '.doctor_id' => $this->doctor_id
            ]);
        }

        return $dataProvider;
    }

}
