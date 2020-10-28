<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Check;

/**
 * CheckSearch represents the model behind the search form about `app\models\Check`.
 */
class CheckSearch extends Check {

    public $active_tab = 'paid';
    public $period = -1;
    public static $periods = [
        -1 => 'Все',
        0 => 'Сегодня',
        1 => 'Вчера',
        2 => 'Эта неделя',
        3 => 'Этот месяц'
    ];

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'user_id', 'patient_id', 'back_id', 'period'], 'integer'],
            [['created', 'updated', 'active_tab'], 'safe'],
            [['sum'], 'number'],
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
                'active_tab' => 'Вид',
                'period' => 'Дата',
                'patient_id' => 'Пациент'
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
        $query = Check::find()
            ->joinWith('patient')
            ->joinWith('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id' => [
                    'label' => '#',
                    'asc' => [
                        self::tableName() . '.id' => SORT_ASC,
                    ],
                    'desc' => [
                        self::tableName() . '.id' => SORT_DESC
                    ]
                ],
                'created',
                'sum',
                'patients.name' => [
                    'label' => 'Пациент',
                    'asc' => [
                        Patients::tableName() . '.last_name' => SORT_ASC,
                        Patients::tableName() . '.first_name' => SORT_ASC,
                        Patients::tableName() . '.middle_name' => SORT_ASC
                    ],
                    'desc' => [
                        Patients::tableName() . '.last_name' => SORT_DESC,
                        Patients::tableName() . '.first_name' => SORT_DESC,
                        Patients::tableName() . '.middle_name' => SORT_DESC,
                    ],
                ],
                'user.username' => [
                    'label' => 'Пользователь',
                    'asc' => [User::tableName() . '.username' => SORT_ASC],
                    'desc' => [User::tableName() . '.username' => SORT_DESC]
                ],
                'webkassa_id'
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

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.created' => $this->created,
            self::tableName() . '.updated' => $this->updated,
            self::tableName() . '.user_id' => $this->user_id,
            self::tableName() . '.patient_id' => $this->patient_id,
            self::tableName() . '.sum' => $this->sum,
            self::tableName() . '.back_id' => $this->back_id,
            self::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id
        ]);

        return $dataProvider;
    }

}
