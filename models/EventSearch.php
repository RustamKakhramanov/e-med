<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Event;

/**
 * EventSearch represents the model behind the search form about `app\models\Event`.
 */
class EventSearch extends Event {

    public $date_from;
    public $date_to;
    public $patient_fio;
    public $doctor_fio;
    
    public static $types = [
        -1 => 'Все',
        0 => 'Звонок',
        1 => 'Он-лайн запись',
        2 => 'Веб ассистент',
        3 => 'Эл. почта'
    ];
    
    public static $stateLabels = [
        -1 => 'Все',
        0 => 'Запланировано',
        1 => 'В работе',
        2 => 'Завершено',
        3 => 'Отменено'
    ];

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'type', 'marketing', 'patient_id', 'doctor_id', 'user_id'], 'integer'],
            [['incoming', 'first_time', 'deleted', 'creation', 'canceled'], 'boolean'],
            [['comment', 'date', 'updated', 'created', 'date_from', 'date_to', 'patient_fio', 'doctor_fio'], 'safe'],
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
            'type' => 'Операция',
            'date' => 'Дата',
            'patient_fio' => 'Пациент (фио)',
            'doctor_fio' => 'Специалист (фио)'
                ]
        );
    }

    public function setDefault() {
        $this->date_from = date('d.m.Y');
        $this->date_to = date('d.m.Y', strtotime('+1 month'));
        $this->type = -1;
        $this->creation = 0;
        $this->canceled = false;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 10) {
        $query = Event::find()->joinWith('doctor')->joinWith('patient');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize
            ]
        ]);

        //sort
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'type' => [
                    'label' => 'Операция'
                ],
                'date' => [
                    'label' => 'Дата'
                ],
                'patient.name' => [
                    'label' => 'Пациент',
                    'asc' => [
                        'patients.last_name' => SORT_ASC,
                        'patients.first_name' => SORT_ASC,
                        'patients.middle_name' => SORT_ASC,
                    ],
                    'desc' => [
                        'patients.last_name' => SORT_DESC,
                        'patients.first_name' => SORT_DESC,
                        'patients.middle_name' => SORT_DESC,
                    ],
                ],
                'patients.birthday' => [
                    'label' => 'Возраст',
                    'asc' => ['patients.birthday' => SORT_ASC],
                    'desc' => ['patients.birthday' => SORT_DESC],
                ],
                'doctor.name' => [
                    'label' => 'Специалист',
                    'asc' => [
                        'doctor.last_name' => SORT_ASC,
                        'doctor.first_name' => SORT_ASC,
                        'doctor.middle_name' => SORT_ASC,
                    ],
                    'desc' => [
                        'doctor.last_name' => SORT_DESC,
                        'doctor.first_name' => SORT_DESC,
                        'doctor.middle_name' => SORT_DESC,
                    ],
                ],
                'doctor_spec' => [
                    'label' => 'Специализация',
//                    'asc' => ['doctor_speciality.name' => SORT_ASC],
//                    'desc' => ['doctor_speciality.name' => SORT_DESC],
                ],
                'patient.phone' => [
                    'label' => 'Телефон'
                ]
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
            'incoming' => $this->incoming,
            'type' => $this->type >= 0 ? $this->type : null,
            'marketing' => $this->marketing,
            'first_time' => $this->first_time,
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            //'date' => $this->date,
            'updated' => $this->updated,
            'user_id' => $this->user_id,
            'event.deleted' => false,
            'creation' => $this->creation,
            'created' => $this->created,
            'canceled' => $this->canceled,
            'event.branch_id' => Yii::$app->user->identity->branch_id
        ]);

        $query->andFilterWhere(['>=', 'date', $this->date_from])
                ->andFilterWhere(['<=', 'date', $this->date_to]);

        //поиск фио пациента
        if ($this->patient_fio) {

            $fio = explode(' ', $this->patient_fio);
            $groups = ['or'];
            $params = [];
            $fields = ['last_name', 'first_name', 'middle_name'];

            foreach ($fio as $key => $word) {
                $params[':fio' . $key] = $word . '%';
            }

            foreach ($fields as $field) {
                $group = ['or'];
                foreach ($params as $key => $param) {
                    $group[] = Patients::tableName() . '.' . $field . ' ilike ' . $key;
                }
                $groups[] = $group;
            }

            $query->andWhere($groups, $params);
        }
        
        //поиск фио специалиста
        if ($this->doctor_fio) {

            $fio = explode(' ', $this->doctor_fio);
            $groups = ['or'];
            $params = [];
            $fields = ['last_name', 'first_name', 'middle_name'];

            foreach ($fio as $key => $word) {
                $params[':fio' . $key] = $word . '%';
            }

            foreach ($fields as $field) {
                $group = ['or'];
                foreach ($params as $key => $param) {
                    $group[] = Doctor::tableName() . '.' . $field . ' ilike ' . $key;
                }
                $groups[] = $group;
            }

            $query->andWhere($groups, $params);
        }

        return $dataProvider;
    }

}
