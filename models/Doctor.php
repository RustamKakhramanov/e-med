<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "doctor".
 *
 * @property int $id
 * @property bool $sex
 * @property int $subdivision_id
 * @property bool $fired уволен
 * @property bool $deleted
 * @property int $branch_id
 * @property string $photo Фотография
 *
 * @property Direction[] $directions
 * @property Branch $branch
 * @property Subdivision $subdivision
 * @property DoctorPrice[] $doctorPrices
 * @property DoctorScheduleCalend[] $doctorScheduleCalends
 * @property DoctorScheduleTemplate[] $doctorScheduleTemplates
 * @property DoctorSpeciality[] $doctorSpecialities
 * @property Event[] $events
 * @property Reception[] $receptions
 * @property Template[] $templates
 * @property User[] $users
 */
class Doctor extends \yii\db\ActiveRecord {

    //типы блока сетки
    //свободно, доступно для записи
    const TYPE_FREE = 0;
    //прошло время
    const TYPE_PAST = 1;
    //занято
    const TYPE_BUSY = 2;
    //заблокировано (кто-то уже создает событие)
    const TYPE_LOCKED = 3;

    const PHOTO_PATH = '/uploads/docs/';
    const PHOTO_MAN = '/img/default_man.png';
    const PHOTO_WOMAN = '/img/default_woman.png';

    public $selected_specs = '[]';
    public $schedule = '[]';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'doctor';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['first_name', 'last_name', 'birthday', 'subdivision_id'], 'required', 'message' => 'Обязательно'],
            [['sex', 'fired', 'deleted'], 'boolean'],
            [['birthday'], 'safe'],
            [['subdivision_id', 'branch_id'], 'integer'],
            [['first_name', 'last_name', 'middle_name', 'photo', 'search_field', 'search_field'], 'string', 'max' => 255],
            [['selected_specs'], 'specValidate'],
            [['schedule'], 'scheduleValidate']
        ];
    }

    /**
     * валидация специализации, не пусто и одна основная
     * @param $field
     * @param $attribute
     */
    public function specValidate($field, $attribute) {

        if ($this->$field) {

            $specs = json_decode($this->$field, true);

            if (!count($specs)) {
                $this->addError($field, 'Требуется добавить специализацию');
            } else {
                //кол-во основных
                $main = 0;
                //ид на дубли
                $ids = [];
                $idsError = false;
                foreach ($specs as $key => $spec) {
                    if (isset($spec['main']) && $spec['main']) {
                        $main++;
                    }

                    if (!in_array($spec['speciality_id'], $ids)) {
                        $ids[] = $spec['speciality_id'];
                    } else {
                        $idsError = true;
                    }
                }

                if ($idsError) {
                    $this->addError($field, 'Не допускается дублирование специализаций');
                }

                if ($main == 0) {
                    $this->addError($field, 'Не указана основная специализация');
                }

                if ($main > 1) {
                    $this->addError($field, 'Допускается только одна основная');
                }
            }
        } else {
            $this->addError($field, 'Требуется добавить специализацию');
        }
    }

    function scheduleValidate($field, $attribute) {
        $data = json_decode($this->$field, true);
        $errors = [];

        if ($data) {
            //шаблон
            foreach ($data['temp']['items'] as $item) {
                if ($item['enabled'] && !count($item['times'])) {
                    $errors['temp'][] = [
                        'num' => $item['num'],
                        'text' => 'Укажите периоды работы или установите день выходным'
                    ];
                }
            }

            //календарный
            foreach ($data['calend']['items'] as $item) {
                if ($item['enabled'] && !count($item['times'])) {
                    $errors['calend'][] = [
                        'num' => $item['num'],
                        'text' => 'Укажите периоды работы или установите день выходным'
                    ];
                }
            }
        }

        if (count($errors)) {
            $this->addError($field, $errors);
        }
    }

    public function setDefault() {
        $this->sex = 1;
        $this->branch_id = Yii::$app->user->identity->branch_id;
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
            'subdivision_id' => 'Подразделение',
            'fired' => 'Сотрудник уволен',
            'deleted' => 'Deleted',
        ];
    }

    public function deleteSafe() {
        $this->deleted = 1;
        $this->save(false);
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
    public function getSubdivision() {
        return $this->hasOne(Subdivision::className(), ['id' => 'subdivision_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

//    public function getSpecialities() {
//        return $this->hasMany(Speciality::className(), ['id' => 'speciality_id'])
//                        ->viaTable('doctor_speciality', ['doctor_id' => 'id'], null, ['duration', 'main', 'show_schedule'])
//
//        ;
//    }
//    public function getSpecialities() {
//        return $this->hasMany(Speciality::className(), ['id' => 'speciality_id'])->viaTable('doctor_speciality', ['doctor_id' => 'id']);
//    }

    public function getDoctorSpecialities() {
        return $this->hasMany(DoctorSpeciality::className(), ['doctor_id' => 'id']);
    }

    public function getSpecialities() {
        return $this->hasMany(Speciality::className(), ['id' => 'speciality_id'])->via('doctorSpecialities');
    }

    public function getSpeciality_main() {
        foreach ($this->doctorSpecialities as $spec) {
            if ($spec->main) {
                return $spec->speciality;
            }
        }

        throw new \yii\base\Exception('Отсутсвует основная специализация у врача id ' . $this->id);
    }

    public function getScheduleTemplate() {
        return $this->hasOne(DoctorScheduleTemplate::className(), ['doctor_id' => 'id']);
    }

    public function getScheduleCalend() {
        return $this->hasMany(DoctorScheduleCalend::className(), ['doctor_id' => 'id']);
    }

    public function getDoctorPrices() {
        return $this->hasMany(DoctorPrice::className(), ['doctor_id' => 'id']);
    }

    /**
     * длительность приема основной специализ
     * @return int
     */
    public function getDuration() {
        foreach ($this->doctorSpecialities as $spec) {
            if ($spec->main) {
                return $spec->duration;
            }
        }
    }

    /**
     * длительность приема указанной специализ
     * @return int
     */
    public function getDurationSpec($spec_id) {
        foreach ($this->doctorSpecialities as $spec) {
            if ($spec->speciality_id == $spec_id) {
                return $spec->duration;
            }
        }
    }

    /**
     * сохранить связи специализаций
     */
    public function saveRel() {
        if ($this->selected_specs) {

            foreach ($this->specialities as $spec) {
                $this->unlink('specialities', $spec, true);
            }

            //добавление
            $specs = json_decode($this->selected_specs, true);
            foreach ($specs as $item) {
                $spec = Speciality::findOne(['id' => $item['speciality_id']]);
                $this->link('specialities', $spec, [
                    'main' => isset($item['main']) && $item['main'],
                    'duration' => $item['duration'],
                    'show_schedule' => isset($item['show_schedule']) && $item['show_schedule']
                ]);
            }
        }
    }

    public function saveSchedule() {
        if ($this->schedule) {

            $template = $this->scheduleTemplate;
            if ($template) {
                $template->delete();
            }

            $schedule = json_decode($this->schedule, true);
            $scheduleTemp = $schedule['temp'];
            $obj = new DoctorScheduleTemplate();
            $obj->doctor_id = $this->id;
            $obj->type = $scheduleTemp['type'];
            $obj->data_days = json_encode($scheduleTemp['items']);
            $obj->save();

            if (isset($schedule['calend'])) {
                $scheduleCalend = $schedule['calend']; //post
                //границы периода
                $min = strtotime('2050-01-01');
                $max = strtotime('1970-01-01');
                foreach ($scheduleCalend['items'] as $item) {
                    $d = strtotime($item['date']);
                    if ($d < $min) {
                        $min = $d;
                    }
                    if ($d > $max) {
                        $max = $d;
                    }
                }

                $templates = $this->scheduleCalend; //db
                if ($templates && $scheduleCalend) { //todo
                    foreach ($templates as $temp) {
                        $d = strtotime($temp->date);
                        if ($d <= $max && $d > $min) {
                            $temp->delete();
                        }
                    }
                }

                foreach ($scheduleCalend['items'] as $day) {
                    $obj = new DoctorScheduleCalend();
                    $obj->num = $day['num'];
                    $obj->doctor_id = $this->id;
                    $obj->date = $day['date'];
                    $obj->enabled = $day['enabled'];
                    $obj->data_items = json_encode($day['times']);
                    $obj->save();
                }
            }
        }
    }

    public function savePrice($items = []){
        foreach ($this->doctorPrices as $prev) {
            $prev->delete();
        }

        foreach ($items as $id) {
            $model = new DoctorPrice();
            $model->setAttributes([
                'doctor_id' => $this->id,
                'price_id' => $id
            ]);
            $model->save();
        }
    }

    /**
     * single
     * @param type $id
     * @return type
     */
    public static function getById($id) {
        return self::find()->andWhere(['doctor.id' => $id])->joinWith('specialities')->joinWith('scheduleTemplate')->one();
    }

    /**
     * сеть расписания врача
     * @param str $from дата с
     * @param str $to дата по
     * @param int $duration длительность приема
     * @return array
     */
    public function getScheduleGrid($from, $to, $duration = false) {
        $data = [];
        $currentDate = time();
        $start = strtotime($from);
        $end = strtotime($to);

        $template = $this->scheduleTemplate;

        if (!$duration) {
            $duration = $this->duration;
        }

        //начало заполнения по шаблону
        $counter = 0;
        if ($template['type'] == 'week') {
            $counter = date('N', $start) - 1;
        } else {
            $counter = date('j', $start) - 1;
        }

        while ($start <= $end) {
            $item = [
                //инфа о специалисте
                'doctor' => [
                    'id' => $this->id,
                    'last_name' => $this->last_name,
                    'name' => implode(' ', [$this->first_name, $this->middle_name]),
                    'spec' => $this->speciality_main->name
                ],
                'num' => $counter,
                //флаг рабочего дня
                'enabled' => false,
                //дата
                'date' => date('Y-m-d', $start),
                //интервал мин
                'interval' => $duration,
                //контенер ячеек расп
                'times' => [],
                //периоды рабочего времени
                'periods' => []
            ];

            $founded = false;
            //поиск порядкового дня в шаблоне
            foreach ($template->dataDaysData as $day) {
                if ($day['num'] == $item['num']) {
                    $founded = $day;
                    break;
                }
            }

            if ($founded) {
                $item['enabled'] = $day['enabled'];
                $item['periods'] = $day['times'];
            }

            if (!$item['enabled']) {
                $item['periods'] = [];
            }

            $data[] = $item;

            $counter++;
            $start = strtotime('+1 day', $start);

            //для недели обнулить
            if ($template['type'] == 'week' && $counter == 7) {
                $counter = 0;
            }

            //для месяца проверить не достигли ли его конца
            if ($template['type'] == 'month') {
                if (date('t', $start) == $counter) {
                    $counter = 0;
                }
            }
        }
        //конец заполнения по шаблону
        //начало заполнение по календарному
        $calendDays = $this->scheduleCalend;
        foreach ($calendDays as $calendDay) {
            foreach ($data as $key => $item) {
                if ($item['date'] == $calendDay['date']) {
                    if (!$calendDay['enabled']) {
                        $data[$key]['enabled'] = false;
                        $data[$key]['periods'] = [];
                    } else {
                        $data[$key]['periods'] = $calendDay['data_items'];
                        $data[$key]['enabled'] = $calendDay['enabled'];
                    }
                    break;
                }
            }
        }

        //конец заполнение по календарному
        //создание ячеек для каждого дня
        foreach ($data as $key => $day) {
            //поиск всех записей и заблокированных ячеек на день
            $events = \app\models\Event::find()
                ->where([
                    'and',
                    'deleted = false',
                    'canceled = false',
                    //'branch_id = :branch_id',
                    'doctor_id = :doctor_id',
                    'to_char(event.date, \'YYYY-MM-DD\') = :date',
                    [
                        'or',
                        'creation = false',
                        [
                            'and',
                            'creation = true',
                            'updated > :updated'
                        ]
                    ]
                ], [
                        ':doctor_id' => $this->id,
                        ':date' => $day['date'],
                        ':updated' => date('Y-m-d H:i:s', strtotime('-5 minutes', $currentDate)),
                        //':branch_id' => Yii::$app->user->identity ? Yii::$app->user->identity->branch_id : null
                    ]
                )
                ->all();

            foreach ($day['periods'] as $period) {
                $start = strtotime($day['date'] . ' ' . $period['start'] . ':00');
                $end = strtotime($day['date'] . ' ' . $period['end'] . ':00');

                $canMove = true;
                while ($canMove) {
                    $endCell = strtotime('+' . $duration . ' minutes', $start);

                    $time = [
                        'start' => date('H:i', $start),
                        'end' => date('H:i', $endCell),
                        'type' => self::TYPE_FREE,
                        'entry' => false
                    ];

                    //прошлое
                    if ($start < $currentDate) {
                        $time['type'] = self::TYPE_PAST;
                    }

                    //поиск события на ячейку
                    $foundedEvent = false;
                    foreach ($events as $event) {
                        //todo проверить круглосуточную 00:00
                        $eventTime = strtotime($event->date);
                        if ($eventTime >= $start && $eventTime < $endCell) {
                            $foundedEvent = $event;
                            break;
                        }
                    }

                    //если найдено событие
                    if ($foundedEvent) {
                        if ($time['type'] == self::TYPE_FREE) {
                            $time['type'] = self::TYPE_BUSY;
                        }
                        //инфа по событию
                        $entry = [
                            'id' => $foundedEvent->id,
                            'state' => $foundedEvent->state,
                            'created' => $foundedEvent->created,
                            'updated' => $foundedEvent->updated,
                            'user' => [
                                'id' => $foundedEvent->user_id,
                                'name' => $foundedEvent->user->username,
                            ],
                            'patient' => [
                                'name' => $foundedEvent->patient_id ? $foundedEvent->patient->initials : false,
                                'phone' => $foundedEvent->patient_id ? $foundedEvent->patient->phone : null
                            ],
                            'cost' => 0,
                            'creation' => $foundedEvent->creation
                        ];

                        $time['entry'] = $entry;
                    }

                    $data[$key]['times'][] = $time;
                    $start = strtotime('+' . $duration . ' minutes', $start);
                    if ($start > $end) {
                        $canMove = false;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * инициалы
     * @return string
     */
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

    /**
     * мини сеть (для выбора времени в событии)
     * @return array
     */
    public function getMiniGrid() {
        $date = date('Y-m-d');
        $grid = $this->getScheduleGrid($date, date('Y-m-d', strtotime('+4 day', strtotime($date))));

        $data = [];
        foreach ($grid as $day) {
            $temp = [
                'date' => $day['date'],
                'times' => []
            ];

            foreach ($day['times'] as $time) {
                if ($time['type'] == self::TYPE_FREE) {
                    $temp['times'][] = $time['start'];
                }
            }

            if (count($temp['times'])) {
                $data[] = $temp;
            }
        }

        return $data;
    }

    public function getScheduleToday() {
        $date = date('Y-m-d');
        $grid = $this->getScheduleGrid($date, $date);

        return $grid;
    }

    /**
     * список доступных шаблонов
     * @return array
     */
    public function getAvailableTemplates() {

        $resp = Template::find()
            ->where([
                'and',
                'deleted = false',
                'draft = false',
                'branch_id = :branch_id',
                'spec_id in (' . implode(',', [1, 2, 3, 4, 5]) . ')',
                [
                    'or',
                    'doctor_id = :doctor_id',
                    'doctor_id is null',
                ]
            ], [
                ':doctor_id' => $this->id,
                ':branch_id' => Yii::$app->user->identity->branch_id
            ])
            ->all();

        return $resp;
    }

    /**
     * адрес картинки
     * @return type
     */
    public function getPhotoUrl() {
        return $this->photo ? self::PHOTO_PATH . $this->photo : ($this->sex ? self::PHOTO_MAN : self::PHOTO_WOMAN);
    }

    public function getSpecialitiesSelect() {
        $list = Speciality::find()
            ->andWhere([
                'deleted' => 0,
                'branch_id' => Yii::$app->user->identity->branch_id
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return ArrayHelper::map($list, 'id', 'name');
    }

    public function getSubdivisionsSelect() {
        $list = Subdivision::find()
            ->andWhere([
                'deleted' => 0,
                'branch_id' => Yii::$app->user->identity->branch_id
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return ArrayHelper::map($list, 'id', 'name');
    }

    public function getPriceList(){
        return Price::find()
            ->where(['branch_id' => Yii::$app->user->identity->branch_id])
            ->orderBy(['title' => SORT_ASC])
            ->all();
    }

    public function getMainSpecPrint() {
        return $this->getSpeciality_main()->name;
    }
}
