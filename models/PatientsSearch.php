<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patients;

/**
 * PatientsSearch represents the model behind the search form about `app\models\Patients`.
 */
class PatientsSearch extends Patients {

    public $age_from,
        $age_to,
        $name_fio,
        $phone,
        $email,
        $name_query;

    //quick search
    public $quick_field,
        $quick_value;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'health_group', 'social_status', 'group'], 'integer'],
            [['quick_field', 'quick_value', 'name_query'], 'string'],
            [['first_name', 'last_name', 'middle_name', 'birthday', 'age_from', 'age_to', 'nationality', 'area', 'data_contact', 'data_document', 'data_work', 'data_family', 'phone', 'iin', 'name_fio'], 'safe'],
            [['sex'], 'boolean'],
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
                'phone' => 'Телефон',
                'email' => 'Эл. почта',
                'quick_field' => 'Поле',
                'quick_value' => 'Значение',
                'name_query' => 'Фио'
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
    public function search($params, $pageSize = 10, $limit = false) {
        $query = Patients::find()->joinWith('contacts');

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        if ($pageSize != false) {
            $dataProvider->setPagination([
                'pageSize' => $pageSize
            ]);
        } else {
            $dataProvider->pagination = false;
        }

        if ($limit) {
            $query->limit($limit);
        }

        //sort
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'has_dir' => [
                    'label' => false
                ],
                'name_fio' => [
                    'label' => 'Пациент',
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
                'last_visit' => [
                    'label' => 'Последний прием'
                ],
                'sex',
                'birthday',
                'age' => [
                    'label' => 'Возраст',
                    'asc' => ['birthday' => SORT_ASC],
                    'desc' => ['birthday' => SORT_DESC],
                ],
                'iin'
            ],
            'defaultOrder' => [
                'id' => SORT_DESC
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
            self::tableName() . '.sex' => $this->sex,
            self::tableName() . '.birthday' => $this->birthday,
            self::tableName() . '.health_group' => $this->health_group,
            self::tableName() . '.social_status' => $this->social_status,
            self::tableName() . '.group' => $this->group,
            self::tableName() . '.deleted' => false
        ]);

        $query->andFilterWhere(['ilike', 'first_name', $this->first_name])
            ->andFilterWhere(['ilike', 'last_name', $this->last_name])
            ->andFilterWhere(['ilike', 'middle_name', $this->middle_name])
            ->andFilterWhere(['>=', function ($birthday) {
                return "(date_part('year', now()) - date_part('year', [[$birthday]]))";
            }, $this->age_from])
            ->andFilterWhere(['<=', function ($birthday) {
                return "(date_part('year', now()) - date_part('year', [[$birthday]]))";
            }, $this->age_to])
            ->andFilterWhere(['ilike', 'nationality', $this->nationality])
            ->andFilterWhere(['ilike', 'area', $this->area])
            ->andFilterWhere(['like', 'iin', $this->iin]);

        if ($this->phone) {
            $query->andWhere([
                'and',
                'patient_contact.type = :type',
                'patient_contact.phone like :phone'
            ], [
                ':type' => 'phone',
                ':phone' => '%' . $this->phone . '%'
            ]);
        }

        //поиск по фио
        if ($this->name_fio) {
            $query->andWhere(['ilike', self::tableName() . '.search_field', $this->name_fio]);
        }

        if ($this->name_query) {
            $query->andWhere(['ilike', self::tableName() . '.search_field', $this->name_query]);
        }

//        dd($query->createCommand()->rawSql);
//        exit;

        return $dataProvider;
    }

}
