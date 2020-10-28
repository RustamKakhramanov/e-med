<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "event".
 *
 * @property integer $id
 * @property boolean $incoming
 * @property integer $type
 * @property string $comment
 * @property integer $marketing
 * @property boolean $first_time
 * @property integer $patient_id
 * @property integer $doctor_id
 * @property string $date
 * @property string $created
 * @property string $updated
 * @property integer $user_id
 * @property boolean $deleted
 * @property boolean $creation
 * @property boolean $canceled
 * @property integer $branch_id
 *
 * @property Branch $branch
 * @property Doctor $doctor
 * @property Patients $patient
 * @property User $user
 */
class Event extends \yii\db\ActiveRecord {

    //создание нового
    const SCENARIO_NEW = 'new';
    //поиск существующего
    const SCENARIO_FIND = 'find';
    //редактирование
    const SCENARIO_EDIT = 'edit';
    const STATE_PLANED = 0;
    const STATE_INWORK = 1;
    const STATE_DONE = 2;
    const STATE_CANCELED = 3;

    public $typeLabels = [
        'Звонок',
        'Он-лайн запись',
        'Веб ассистент',
        'Эл. почта',
    ];
    public static $incomingTypes = [
        1 => 'Входящее',
        0 => 'Исходящее'
    ];
    public static $types = [
        0 => 'Звонок',
        1 => 'Он-лайн запись',
        2 => 'Веб ассистент',
        3 => 'Эл. почта'
    ];
    public static $stateLabels = [
        'Запланировано',
        'В работе',
        'Завершено',
        'Отменено'
    ];
    public $selected_prices = '[]';
    protected $ruMonths = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['incoming', 'first_time', 'deleted', 'creation', 'canceled'], 'boolean'],
            [['type', 'marketing', 'patient_id', 'doctor_id', 'user_id', 'branch_id'], 'integer'],
            [['doctor_id', 'user_id'], 'required', 'message' => 'Обязательно'],
            [['date'], 'required', 'message' => 'Укажите время приема'],
            //[['patient_id'], 'required', 'on' => self::SCENARIO_NEW, 'message' => 'Нужно выбрать пациента'],
            [['patient_id'], 'required', 'message' => 'Нужно выбрать пациента'],
            [['date', 'created', 'updated', 'comment'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['selected_prices'], 'pricesValidate']
        ];
    }

    public function pricesValidate($field, $attribute) {
        if ($this->$field) {
            $items = json_decode($this->$field, true);        
            if (count($items)) {
                foreach ($items as $item) {
                    if (!isset($item['id'])) {
                        $this->addError($field, 'Выберите услугу');
                        break;
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'incoming' => 'Incoming',
            'type' => 'Тип',
            'comment' => 'Комментарий',
            'marketing' => 'Marketing',
            'first_time' => 'Первичное обращение',
            'patient_id' => 'Пациент ID',
            'doctor_id' => 'Доктор ID',
            'date' => 'Дата',
            'created' => 'Created',
            'updated' => 'Updated',
            'user_id' => 'Пользователь ID',
            'deleted' => 'Deleted',
            'creation' => 'Creation',
            'canceled' => 'Отменено'
        ];
    }

    public function setDefault() {
        $this->incoming = 1;
        $this->type = 0;
        $this->branch_id = @Yii::$app->user->identity->branch_id;
        $this->user_id = @Yii::$app->user->identity->id;
    }

    public function beforeSave($insert) {
        if ($this->id) {
            $dirty = $this->getDirtyAttributes();
            $source = $this->getOldAttributes();
            $changed = [];
            foreach ($source as $key => $val) {
                if (isset($dirty[$key]) && $val != $dirty[$key]) {
                    $changed[$key] = [
                        'before_value' => $val,
                        'after_value' => $dirty[$key]
                    ];
                }
            }
            EventLog::createLog($this->id, $changed);
        }
                
        if (parent::beforeSave($insert)) {
            $this->updated = date('Y-m-d H:i:s');
            if (!$this->id) {
                $this->created = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor() {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->joinWith('specialities');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient() {
        return $this->hasOne(Patients::className(), ['id' => 'patient_id']);
    }

    public function getEventPrices() {
        return $this->hasMany(EventPrice::className(), ['event_id' => 'id']);
    }

    public function getPrices() {
        return $this->hasMany(Price::className(), ['id' => 'price_id'])->via('eventPrices');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function saveRel() {
        if ($this->selected_prices) {
            //удаление прошлых
            foreach ($this->prices as $price) {
                $this->unlink('prices', $price, true);
            }
            //добавление
            $prices = json_decode($this->selected_prices, true);
            foreach ($prices as $item) {
                $price = Price::findOne(['id' => $item['id']]);
                $this->link('prices', $price, [
                    'count' => $item['count']
                ]);
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function deleteSafe() {
        $this->deleted = 1;
        $this->save(false);
    }

    /**
     * получить состояние события
     * @return int
     */
    public function getState() {
        $state = self::STATE_PLANED;

        if ($this->canceled) {
            $state = self::STATE_CANCELED;
        }

        //todo поиск приема

        return $state;
    }

    public function getPrintType() {
        return $this->typeLabels[$this->type];
    }

    public function getHumanDate() {
        return date('d', strtotime($this->date)) . ' ' . $this->ruMonths[date('n', strtotime($this->date)) - 1] . ' в ' . date('H:i', strtotime($this->date));
    }
    
    public function getLogs(){
        return $this->hasMany(EventLog::className(), ['event_id' => 'id'])->orderBy(['date' => SORT_ASC]);
    }

}
