<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Doctor;

/**
 * DoctorSearch represents the model behind the search form about `app\models\Doctor`.
 */
class ScheduleSearch extends Doctor {
    
    

    public $spec;
    public $type = 'day';
    public $date_day;
    public $date_week;
    public $week_doctor_id = 0;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'subdivision_id', 'spec', 'week_doctor_id'], 'integer'],
            [['first_name', 'last_name', 'middle_name', 'birthday', 'type', 'date_day', 'date_week'], 'safe'],
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
            'spec' => 'Специализация',
            'week_doctor_id' => 'Специалист'
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
        $query = Doctor::find()->joinWith('specialities')->joinWith('scheduleTemplate');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'doctor.last_name'
            ],
            'defaultOrder' => [
                'doctor.last_name' => SORT_ASC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            Doctor::tableName() . '.id' => $this->week_doctor_id,
            'sex' => $this->sex,
            'birthday' => $this->birthday,
            'fired' => 0,
            Doctor::tableName() . '.deleted' => false,
            Doctor::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id
        ]);

        if ($this->type == 'day') {
            $query->andFilterWhere([
                'subdivision_id' => $this->subdivision_id,
                'speciality.id' => $this->spec
            ]);
        } else {
//            $query->andFilterWhere([
//                
//            ]);
        }

        if (!$this->date_day) {
            //$this->date_day = date('Y-m-d', strtotime('+1 days')); //toremove
            $this->date_day = date('Y-m-d');
        }

        if (!$this->date_week) {
            $d = date('d.m.Y');
            if (date('N', strtotime($d)) == 1) {
                $startWeek = $d;
            } else {
                $startWeek = date('d.m.Y', strtotime('last monday'));
            }
            $this->date_week = $startWeek;
        }
        
        return $this->_jsonSchedule($dataProvider);
    }

    /**
     * собрать json для сетки расп
     * @param type $dataProvider
     * @return arrau
     */
    protected function _jsonSchedule($dataProvider) {
        $jsonData = [
            'week' => [
                'interval' => 30,
                'days' => []
            ],
            'day' => []
        ];

        $currentDate = time();        
        
        if ($this->type == 'day') {
            foreach ($dataProvider->getModels() as $doc) {
                $schedule = current($doc->getScheduleGrid($this->date_day, $this->date_day));
                
                if ($schedule['periods']) {
                    $jsonData['day'][] = $schedule;
                }
            }
        } else {
            $doc = current($dataProvider->getModels());
            if ($doc) {
                $jsonData['week']['days'] = $doc->getScheduleGrid($this->date_week, date('d.m.Y', strtotime('+6 day', strtotime($this->date_week))));
            }
        }
        
        return $jsonData;
    }
    
}
