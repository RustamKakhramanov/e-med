<?php

namespace app\models\tv\widget;

use yii\base\Model;
use Yii;

class Schedule extends Model {
    public $ids = '[]',
            $columns = 1,
            $days = 7,
            $rows = 6,
            $timer = 15;
    protected $_name = 'schedule',
            $_title = 'График работы';
    
    protected static $_days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];

    public function rules() {
        return [
            [['ids', 'columns', 'rows', 'timer', 'days'], 'required'],
            [['ids'], 'idsValidate'],
            [['days'], 'daysValidate']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'rows' => 'Кол-во врачей на страницу',
            'timer' => 'Время смены слайдов (сек)',
            'days' => 'Кол-во дней'
        ];
    }

    public function daysValidate($field, $attribute) {
        if ($this->$field) {
            $val = 1 * $this->$field;
            if ($val > 7 || $val < 1) {
                $this->addError($field, 'Допустимо от 1 до 7');
            }
        } else {
            $this->addError($field, 'Требуется заполнить');
        }
    }

    public function idsValidate($field, $attribute) {
        if ($this->$field) {
            $ids = json_decode($this->$field, true);
            if (!$ids) {
                $this->addError($field, 'Требуется выбрать специалистов');
            }
        } else {
            $this->addError($field, 'Требуется выбрать специалистов');
        }
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

    public function getDoctors() {
        return \app\models\Doctor::find()
                        ->where([
                            'id' => json_decode($this->ids, true)
                        ])
                        ->orderBy([
                            'last_name' => SORT_ASC,
                            'first_name' => SORT_ASC,
                            'middle_name' => SORT_ASC,
                        ])
                        ->all();
    }

    public static function prettyPeriod($periods) {
        $partsStart = explode(':', $periods[0]['start']);
        $partsEnd = explode(':', $periods[count($periods) - 1]['end']);
        $result = '<span class="start">' . $partsStart[0] * 1;
        $result .= '<sup>' . $partsStart[1] . '</sup></span>';
        $result .= '<span class="dash"> – </span><span class="end">' . $partsEnd[0] * 1;
        $result .= '<sup>' . $partsEnd[1] . '</sup></span>';

        return $result;
    }

    public static function dayName($number) {
        return self::$_days[$number - 1];
    }

}
