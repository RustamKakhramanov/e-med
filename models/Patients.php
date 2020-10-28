<?php

namespace app\models;

use app\helpers\Utils;
use Yii;

/**
 * This is the model class for table "patients".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property boolean $sex
 * @property string $birthday
 * @property integer $health_group
 * @property integer $social_status
 * @property integer $group
 * @property string $nationality
 * @property string $area
 * @property string $data_contact
 * @property string $data_document
 * @property string $data_work
 * @property string $data_family
 * @property string $iin
 * @property boolean $deleted
 * @property boolean $new
 * @property integer $branch_id
 *
 * @property Branch $branch
 * @property Event[] $events
 */
class Patients extends \yii\db\ActiveRecord {

    //для контактных данных
    public $data_contact;
    //для договоров
    public $data_contract;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'patients';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['first_name', 'last_name', 'birthday'], 'required', 'message' => 'Обязательно'],
            [['birthday'], 'safe'],
            [['sex', 'new'], 'boolean'],
            [['health_group', 'social_status', 'group', 'branch_id'], 'integer'],
            [['data_document', 'data_work', 'data_family', 'data_contact', 'data_contract'], 'string'],
            [['first_name', 'last_name', 'middle_name', 'nationality', 'area'], 'string', 'max' => 255],
            //[['first_name', 'last_name', 'middle_name', 'nationality', 'area'], 'filter', 'filter'=>'trim'],
            ['iin', 'iinValidate'],
            [['last_name'], 'uniqValidate'],
            [['last_name', 'first_name', 'middle_name'], 'nospaceValidate', 'skipOnEmpty' => false]
        ];
    }

    /**
     * валидация иина, 12 цифр, начало гг-мм-дд
     * @param $model
     * @param $attribute
     */
    public function iinValidate($model, $attribute) {
        $iin = $this->$model;
        if (strlen($iin) != 12) {
            $this->addError($model, 'требуется 12 символов');
        } else {
            if (!preg_match('/^\d+$/', $iin)) {
                $this->addError($model, 'требуются цифры');
            } else {
                $croped = substr($iin, 0, 6);
                $date = \DateTime::createFromFormat('y.m.d', substr($croped, 0, 2) . '.' . substr($croped, 2, 2) . '.' . substr($croped, 4, 2));
                $res = \DateTime::getLastErrors();
                if ($res['warning_count'] || $res['error_count']) {
                    $this->addError($model, 'неверный формат');
                }
            }
        }
    }

    /**
     * проверка на уникальность пациента
     * @param type $field
     * @param type $attribute
     */
    public function uniqValidate($field, $attribute) {
        if ($this->birthday) {
            $count = Patients::find()
                ->andWhere(['deleted' => 0])
                ->andWhere(['ilike', 'first_name', $this->first_name])
                ->andWhere(['ilike', 'last_name', $this->last_name])
                ->andWhere(['=', 'birthday', $this->birthday])
                ->andWhere(['!=', 'id', (int)$this->id])
                ->count();

            if ($count) {
                $this->addError($field, 'Такой пациент уже существует');
            };
        }
    }

    public function nospaceValidate($field, $attr) {
        if (preg_match('/\s/', $this->$field)) {
            $this->addError($field, 'Недопустимый формат');
        }
    }

    public function setDefault() {
        $this->data_document = '[]';
        $this->data_work = '[]';
        $this->data_family = '[]';
        $this->data_contact = '[]';
        $this->data_contract = '[]';
        $this->sex = 1;
        $this->new = 0;

        //test
//        $this->first_name = 'Константин';
//        $this->last_name = 'Пецкалев';
//        $this->middle_name = 'Константинович';
//        $this->sex = 1;
//        $this->birthday = '18.10.1991';
//        $this->iin = '911018000000';

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'sex' => 'Пол',
            'birthday' => 'Дата рождения',
            'health_group' => 'Группа здоровья',
            'social_status' => 'Социальный статус',
            'iin' => 'Иин',
            'group' => 'Группа пациентов',
            'nationality' => 'Гражданство',
            'area' => 'Участок',
            'data_contact' => 'Data Contact',
            'data_document' => 'Data Document',
            'data_work' => 'Data Work',
            'data_family' => 'Data Family',
        ];
    }

    public function beforeSave($insert) {
        $this->search_field = $this->fio;

        return parent::beforeSave($insert);
    }

    public function getFio() {
        return implode(' ', [
            $this->last_name,
            $this->first_name,
            $this->middle_name
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function getInitials() {
        $str = [
            $this->last_name,
            mb_strtoupper(mb_substr($this->first_name, 0, 1)) . '.'
        ];

        if (trim($this->middle_name)) {
            $str[] = mb_strtoupper(mb_substr($this->middle_name, 0, 1)) . '.';
        }

        return implode(' ', $str);
    }

    public function deleteSafe() {
        $this->deleted = 1;
        $this->save(false);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents() {
        return $this->hasMany(Event::className(), ['patient_id' => 'id']);
    }

    /**
     * получить телефон пациента
     * @return string
     */
    public function getPhone() {
        $phone = null;

        foreach ($this->contacts as $item) {
            if ($item['type'] = 'phone' && isset($item['phone'])) {
                $phone = $item->phoneDisplay;
                break;
            }
        }

        return $phone;
    }

    public function getSexPrint() {
        return $this->sex ? 'Мужской' : 'Женский';
    }

    public function getBirthdayPrint() {
        return date('d.m.Y', strtotime($this->birthday));
    }

    public function getAge() {
        return (date('Y') - date('Y', strtotime($this->birthday)));
    }

    public function getContacts() {
        return $this->hasMany(PatientContact::className(), ['patient_id' => 'id']);
    }

    public function saveContacts() {
        if ($this->data_contact) {
            //удаление старых
            foreach ($this->contacts as $item) {
                $item->delete();
            }

            //добавление
            $items = json_decode($this->data_contact, true);

            foreach ($items as $item) {
                $Contact = new PatientContact($item);
                $Contact->patient_id = $this->id;
                $Contact->save();
            }
        }

        return $this;
    }

    public function saveContracts() {

        $newItems = json_decode($this->data_contract, true);

        //обновление удаление существующих
        foreach ($this->contracts as $contract) {
            $found = false;
            foreach ($newItems as $item) {
                if (isset($item['id']) && !$item['typical'] && $item['id'] == $contract->id) {
                    $found = $item;
                }
            }

            if ($found) {
                unset($found['id']);
                $contract->setAttributes($found);
                $contract->save();
            } else {
                $contract->deleteSafe();
            }
        }

        //новые
        foreach ($newItems as $item) {
            //пропускаем типовые и уже созданные
            if (!isset($item['id']) && !$item['typical']) {
                $contract = new Contract($item);
                $contract->patient_id = $this->id;
                $contract->save();
            }
        }

        return $this;
    }

    //незавершенные направления
    public function hasDirections() {
        return Direction::find()->where([
            'patient_id' => $this->id,
            'closed' => false,
        ])->count();
    }

    //события без направлений
    public function hasEventWithoutDirections() {
        $query = new \yii\db\Query;
        $query->select('*')
            ->from(EventPrice::tableName())
            ->where(['=', EventPrice::tableName() . '.canceled', 0])
            ->andWhere(['=', EventPrice::tableName() . '.dir', false])
            ->leftJoin(Event::tableName(), Event::tableName() . '.id = ' . EventPrice::tableName() . '.event_id')
            ->andWhere(['=', Event::tableName() . '.patient_id', $this->id]);

        return $query->count();
    }

    //получить основной типовой
    public function getDefaultContract() {
        return Contract::find()
            ->with('contractor')
            ->andFilterWhere(['=', 'typical', 1])
            ->andFilterWhere(['=', 'main', 1])
            ->one();
    }

    public function getContracts() {
        return $this->hasMany(Contract::className(), ['patient_id' => 'id']);
    }

    //получить список активных договоров (с дефолтным)
    public function getCurrentContracts() {

        $items = Contract::find()
            ->andFilterWhere(['=', 'deleted', false])
            ->andFilterWhere(['=', 'typical', false])
            ->andFilterWhere(['=', 'patient_id', $this->id])
            ->andFilterWhere(['<=', 'start', date('Y-m-d')])
            ->andFilterWhere(['>=', 'end', date('Y-m-d')])
            ->orderBy('id')
            ->all();

        $default[] = $this->defaultContract;

        return array_merge($default, $items);
    }

    /**
     * получить дату последнего активного направления
     */
    public function getCashierDate() {
        $direction = Direction::find()
            ->leftJoin(DirectionItem::tableName(), DirectionItem::tableName() . '.direction_id = ' . Direction::tableName() . '.id')
            ->where([
                Direction::tableName() . '.patient_id' => $this->id,
                DirectionItem::tableName() . '.paid' => false,
                DirectionItem::tableName() . '.canceled' => false
            ])
            ->orderBy([
                Direction::tableName() . '.created' => SORT_ASC
            ])
            ->one();

        if ($direction) {
            return $direction->created;
        }
    }

    /**
     * получить стоимость неоплаченных активных направлений
     */
    public function getCashierCost() {
        $items = DirectionItem::find()
            ->leftJoin(Direction::tableName(), Direction::tableName() . '.id = ' . DirectionItem::tableName() . '.direction_id')
            ->where([
                Direction::tableName() . '.patient_id' => $this->id,
                DirectionItem::tableName() . '.paid' => false,
                DirectionItem::tableName() . '.canceled' => false
            ])
            ->all();

        $summ = 0;
        foreach ($items as $item) {
            $summ += $item->cost * $item->count;
        }

        return $summ;
    }

    public function getActiveDirections() {
        $items = DirectionItem::find()
            ->leftJoin(Direction::tableName(), Direction::tableName() . '.id = ' . DirectionItem::tableName() . '.direction_id')
            ->where([
                Direction::tableName() . '.patient_id' => $this->id,
                DirectionItem::tableName() . '.paid' => false,
                DirectionItem::tableName() . '.canceled' => false
            ])
            ->orderBy([Direction::tableName() . '.created' => SORT_DESC])
            ->all();

        return $items;
    }
}
