<?php

namespace app\models\tv\widget;

use yii\base\Model;
use Yii;

class Oncall extends Model {
    public $monday,
            $tuesday,
            $wednesday,
            $thursday,
            $friday,
            $saturday,
            $sunday;
    
    protected $_name = 'oncall',
            $_title = 'Дежурные врачи';
    
    public function rules() {
        return [
            [['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], 'safe'],
            [['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], 'each', 'rule' => ['integer']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'monday' => 'Понедельник',
            'tuesday' => 'Вторник',
            'wednesday' => 'Среда',
            'thursday' => 'Четверг',
            'friday' => 'Пятница',
            'saturday' => 'Суббота',
            'sunday' => 'Воскресенье',
        ];
    }

    public function getName() {
        return $this->_name;
    }

    public function getTitle() {
        return $this->_title;
    }
    
    public function getAvailableDoctors() {
        return \app\models\Doctor::find()
                        ->where([
                            'deleted' => false,
                            'branch_id' => Yii::$app->user->identity->branch_id
                        ])
                        ->orderBy([
                            'last_name' => SORT_ASC,
                            'first_name' => SORT_ASC,
                            'middle_name' => SORT_ASC,
                        ])
                        ->all();
    }
    
    /**
     * список на сегодня
     */
    public function getTodayDoctors(){
        $dayName = strtolower(date('l'));
        $ids = [-1];
        if ($this->$dayName) {
            $ids = $this->$dayName;
        }
        
        return \app\models\Doctor::find()
                        ->where([
                            'id' => $ids
                        ])
                        ->orderBy([
                            'last_name' => SORT_ASC,
                            'first_name' => SORT_ASC,
                            'middle_name' => SORT_ASC,
                        ])
                        ->all();
    }
}
