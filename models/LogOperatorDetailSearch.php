<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LogQueue;
use Carbon\Carbon;

class LogOperatorDetailSearch extends Model {

    const GROUP_TYPE_HOUR = 0;
    const GROUP_TYPE_DAY = 1;

    public static $group_types = [
        self::GROUP_TYPE_HOUR => 'По часам',
        self::GROUP_TYPE_DAY => 'По дням'
    ];
    public $user_id,
            $group_type,
            $month,
            $year,
            $date_from,
            $date_to

    ;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'date_from', 'date_to', 'group_type', 'year', 'month'], 'required', 'message' => 'Обязательно']
        ];
    }

    public function setDefault() {
        $this->group_type = self::GROUP_TYPE_HOUR;
        $this->month = date('m');
        $this->year = date('Y');
    }

    public function attributeLabels() {
        return [
            'user_id' => 'Оператор',
            'date_from' => 'Дата с',
            'date_to' => 'Дата по',
            'time_start' => 'Время с',
            'time_end' => 'Время по',
            'group_type' => 'Группировать',
            'month' => 'Месяц',
            'year' => 'Год',
        ];
    }

    /**
     * создать границы периода месяца
     */
    protected function _prepareDateRange() {
        $this->date_from = $this->year . '-' . $this->month . '-01';
        $this->date_to = date('Y-m-t', strtotime($this->date_from));
    }

    /**
     * собрать масив сессий пользователя за период, разбив по экстеншенам
     */
    protected function _prepareSess() {
        //месяц по дням
        $periods = [];
        $start = Carbon::createFromTimestamp(strtotime($this->date_from));
        $end = Carbon::createFromTimestamp(strtotime($this->date_to));
        $temp = $start->copy();

        while ($temp <= $end) {
            $periods[$temp->format('Y-m-d')] = [
                'sess' => []
            ];
            $temp->addDay();
        }

        //наполнение данными по сессиям
        foreach ($periods as $key => $p) {
            //TODO добавить возможность учитывать сессии продолжающиеся в полночь
            $slist = UserSession::find()
                    ->where(['user_id' => $this->user_id])
                    ->andWhere(['>=', 'created_at', $key . ' 00:00:00'])
                    ->andWhere(['<=', 'created_at', $key . ' 23:59:59'])
                    ->orderBy(['created_at' => SORT_ASC])
                    ->all();

            foreach ($slist as $item) {
                $periods[$key]['sess'][] = [
                    'start' => $item->created_at,
                    'end' => $item->updated_at,
                    'exten' => $item->number,
                    'outgoing_abandon' => $item->getCountOutgoing(\app\models\LogOutgoing::TYPE_ABANDON),
                    'outgoing_callme' => $item->getCountOutgoing(\app\models\LogOutgoing::TYPE_CALLME)
                ];
            }
        }

        return $periods;
    }

    /**
     * длительность сесси по-человечьи
     * @param type $end
     * @param type $start
     */
    protected function _durationRu($end, $start = false) {
        $output = '';
        if ($start !== false) {
            $diff = $end - $start;
        } else {
            $diff = $end;
        }
        if ($diff > 0) {
            if ($diff / 60 / 60 > 1) {
                $output .= human_plural_form(floor($diff / 60 / 60), ['час', 'часа', 'часов']);
                $output .= ' ';
                $diff -= floor($diff / 60 / 60) * 60 * 60;
            }

            $output .= human_plural_form(round($diff / 60), ['минута', 'минуты', 'минут']);
        }

        return $output;
    }

    public function search() {
        $durationStart = Carbon::createFromTimestamp(strtotime('2016-09-23 10:28:21'));
        $durationEnd = Carbon::createFromTimestamp(strtotime('2016-09-23 12:55:32'));

        $branch = Branch::find()->where(['id' => Yii::$app->user->identity->branch_id])->one();
        $url = $branch->getExtraParam('aster');
        if (!$url) {
            return false;
        }

        $this->_prepareDateRange();
        $sessions = $this->_prepareSess();        
        $client = new \WebSocket\Client($url);
        $client->setTimeout(50);
        $client->send(json_encode([
            'action' => 'extenstatdetail',
            'data' => [
                'sessions' => json_encode($sessions)
            ]
        ]));
        $sessions = json_decode($client->receive(), true);   
        $client->close();

        //суммы по сессиям
        foreach ($sessions as $dKey => $day) {
            foreach ($day['sess'] as $sKey => $sess) {
                $sessions[$dKey]['sess'][$sKey]['duration'] = $this->_durationRu(strtotime($sess['end']), strtotime($sess['start']));
                //подсчет сброшенных
                $abandoned = 0;
                foreach ($sess['resp'] as $item) {
                    if ($item['disposition'] == 'NO ANSWER' || $item['disposition'] == 'BUSY') {
                        $abandoned++;
                    }
                }
                $sessions[$dKey]['sess'][$sKey]['abandoned'] = $abandoned;
                //подсчет отвеченных
                $completed = 0;
                foreach ($sess['resp'] as $item) {
                    if ($item['disposition'] == 'ANSWERED') {
                        $completed++;
                    }
                }
                $sessions[$dKey]['sess'][$sKey]['completed'] = $completed;
                //общее кол-во звонков
                $sessions[$dKey]['sess'][$sKey]['calls'] = $abandoned + $completed;

                //подсчет времени ожидания
                $totalWaiting = 0;
                $countWaiting = 0;
                $longest = 0;
                foreach ($sess['resp'] as $item) {
                    if ($item['disposition'] == 'ANSWERED' || $item['disposition'] == 'NO ANSWER' || $item['disposition'] == 'BUSY') {
                        if ($item['billsec'] > $longest) {
                            $longest = $item['billsec'];
                        }
                        $totalWaiting += $item['billsec'];
                        $countWaiting++;
                    }
                }

                $sessions[$dKey]['sess'][$sKey]['waiting'] = $countWaiting ? round($totalWaiting / $countWaiting) : 0;
                //самое большое время ожидания
                $sessions[$dKey]['sess'][$sKey]['longest'] = $longest;

                //убить айтемы
                unset($sessions[$dKey]['sess'][$sKey]['resp']);
            }
        }

        $monthDuration = 0;

        //суммы по дням
        foreach ($sessions as $dKey => $day) {
            $abandoned = 0;
            foreach ($day['sess'] as $sess) {
                $abandoned += $sess['abandoned'];
            }
            $sessions[$dKey]['abandoned'] = $abandoned;

            $completed = 0;
            foreach ($day['sess'] as $sess) {
                $completed += $sess['completed'];
            }
            $sessions[$dKey]['completed'] = $completed;

            $sessions[$dKey]['calls'] = $abandoned + $completed;

            //подсчет времени ожидания
            $totalWaiting = 0;
            $countWaiting = 0;
            $longest = 0;
            foreach ($day['sess'] as $sess) {
                $totalWaiting += $sess['waiting'];
                $countWaiting++;
                if ($sess['longest'] > $longest) {
                    $longest = $sess['longest'];
                }
            }

            $sessions[$dKey]['waiting'] = $countWaiting ? round($totalWaiting / $countWaiting) : 0;
            //самое большое время ожидания
            $sessions[$dKey]['longest'] = $longest;

            //длительность сессий
            $dur = 0;
            foreach ($day['sess'] as $sess) {
                $dur += strtotime($sess['end']) - strtotime($sess['start']);
            }
            $minStart = null;
            $maxEnd = null;
            foreach ($day['sess'] as $sess) {
                if (strtotime($sess['start']) < $minStart || $minStart === null) {
                    $minStart = strtotime($sess['start']);
                }

                if (strtotime($sess['end']) > $maxEnd || $maxEnd === null) {
                    $maxEnd = strtotime($sess['end']);
                }
            }
            
            if ($dur) {
                $monthDuration += $dur;
                $sessions[$dKey]['duration'] = $this->_durationRu($dur);
            } else {
                $sessions[$dKey]['duration'] = 0;
            }
            
            //колво исходящих
            $sessions[$dKey]['outgoing_abandon'] = 0;
            $sessions[$dKey]['outgoing_callme'] = 0;
            foreach ($day['sess'] as $sess) {
                $sessions[$dKey]['outgoing_abandon'] += $sess['outgoing_abandon'];
                $sessions[$dKey]['outgoing_callme'] += $sess['outgoing_callme'];
            }
        }

        return [
            'periods' => $sessions,
            'monthDuration' => $this->_durationRu($monthDuration, false)
        ];
    }

    /**
     * периоды по часовой группировке
     * @return array
     */
    protected function _periodsHour() {
        $periods = [];
        $start = Carbon::createFromTimestamp(strtotime($this->date_from . ' ' . $this->time_start));
        $end = Carbon::createFromTimestamp(strtotime($this->date_to . ' ' . $this->time_end));

        $canMove = true;
        while ($canMove) {
            $delta = 60 - $start->minute;
            if ($start->diffInMinutes($end) < $delta) {
                $delta = $start->diffInMinutes($end);
            }

            $period = [
                'start' => $start->copy(),
                'end' => $start->copy()->addMinutes($delta),
                'time' => $start->format('Y-m-d H:i:s'),
                'data' => [
                    'abandoned' => 0,
                    'completed' => 0,
                    'calls' => 0,
                    'waiting' => 0,
                    'longest' => 0
                ],
                'items' => []
            ];

            $start->addMinutes($delta);
            if ($start >= $end) {
                $canMove = false;
            }
            $periods[] = $period;
        }

        return $periods;
    }

    protected function _periodsDay() {
        $periods = [];
        $start = Carbon::createFromTimestamp(strtotime($this->date_from . ' ' . $this->time_start));
        $end = Carbon::createFromTimestamp(strtotime($this->date_to . ' ' . $this->time_end));

        $canMove = true;
        while ($canMove) {
            $delta = 60 * 24 - (60 * $start->hour + $start->minute);
            if ($start->diffInMinutes($end) < $delta) {
                $delta = $start->diffInMinutes($end);
            }

            $period = [
                'start' => $start->copy(),
                'end' => $start->copy()->addMinutes($delta),
                'time' => $start->format('Y-m-d H:i:s'),
                'data' => [
                    'abandoned' => 0,
                    'completed' => 0,
                    'calls' => 0,
                    'waiting' => 0,
                    'longest' => 0
                ],
                'items' => []
            ];

            $start->addMinutes($delta);
            if ($start >= $end) {
                $canMove = false;
            }
            $periods[] = $period;
        }

        return $periods;
    }

}
