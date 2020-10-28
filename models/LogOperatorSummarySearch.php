<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LogQueue;
use Carbon\Carbon;

class LogOperatorSummarySearch extends Model {

    const GROUP_TYPE_HOUR = 0;
    const GROUP_TYPE_DAY = 1;

    public static $group_types = [
        self::GROUP_TYPE_HOUR => 'По часам',
        self::GROUP_TYPE_DAY => 'По дням'
    ];
    public $user_id,
            $group_type,
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
        $this->date_from = date('01.m.Y');
        $this->date_to = date('d.m.Y');
    }

    public function attributeLabels() {
        return [
            'user_id' => 'Операторы',
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
     * собрать масив сессий пользователя за период, разбив по экстеншенам
     */
    protected function _prepareSess() {
        $slist = UserSession::find()
                ->where(['user_id' => $this->user_id])
                ->andWhere(['>=', 'created_at', $key . ' 00:00:00'])
                ->andWhere(['<=', 'created_at', $key . ' 23:59:59'])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();
        //наполнение данными по сессиям
        foreach ($periods as $key => $p) {
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
     * длительность сесси по человечьи
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
        $branch = Branch::find()->where(['id' => Yii::$app->user->identity->branch_id])->one();
        $url = $branch->getExtraParam('aster');
        if (!$url) {
            return false;
        }
        $client = new \WebSocket\Client($url);
        $client->setTimeout(100);

        $start = Carbon::createFromTimestamp(strtotime($this->date_from . ' 00:00:00'));
        $end = Carbon::createFromTimestamp(strtotime($this->date_to . ' 23:59:00'));

        $users = [];
        foreach ($this->user_id as $id) {
            $user = User::findOne(['id' => $id]);
            $users[] = [
                'id' => $id,
                'name' => $user->initials,
                'sessions' => [],
                'resp' => [],
                'duration' => 0,
                'calls' => 0,
                'abandoned' => 0,
                'completed' => 0,
                'outgoing_abandon' => 0,
                'outgoing_callme' => 0,
                'waiting' => 0,
                'longest' => 0
            ];
        }

        foreach ($users as $key => $user) {
            $slist = UserSession::find()
                    ->where(['user_id' => $user['id']])
                    ->andWhere(['>=', 'created_at', $start->format('Y-m-d H:i:s')])
                    ->andWhere(['<=', 'created_at', $end->format('Y-m-d H:i:s')])
                    ->orderBy(['created_at' => SORT_ASC])
                    ->all();
            foreach ($slist as $item) {
                $users[$key]['sessions'][] = [
                    'start' => $item->created_at,
                    'end' => $item->updated_at,
                    'exten' => $item->number,
                    'outgoing_abandon' => $item->getCountOutgoing(\app\models\LogOutgoing::TYPE_ABANDON),
                    'outgoing_callme' => $item->getCountOutgoing(\app\models\LogOutgoing::TYPE_CALLME)
                ];
            }
        }
        
        foreach ($users as $key => $user) {
            if ($user['sessions']) {
                $client->send(json_encode([
                    'action' => 'extenstatsummary',
                    'data' => [
                        'sessions' => json_encode($user['sessions'])
                    ]
                ]));
                $users[$key]['resp'] = json_decode($client->receive(), true);
            }
        }
        $client->close();
        
        //суммы по сессиям
        foreach ($users as $key => $user) {
            $monthDuration = 0;
            $totalWaiting = 0;
            $countWaiting = 0;
            $longest = 0;
            
            foreach ($user['resp'] as $sKey => $item) {
                //подсчет сброшенных
                if ($item['disposition'] == 'NO ANSWER' || $item['disposition'] == 'BUSY') {
                    $users[$key]['abandoned']++;
                }
                //подсчет отвеченных
                if ($item['disposition'] == 'ANSWERED') {
                    $users[$key]['completed']++;
                }
                //общее кол-во звонков
                $users[$key]['calls'] = $users[$key]['abandoned'] + $users[$key]['completed'];

                //подсчет времени ожидания
                if ($item['disposition'] == 'ANSWERED' || $item['disposition'] == 'NO ANSWER' || $item['disposition'] == 'BUSY') {
                    if ($item['billsec'] > $longest) {
                        $longest = $item['billsec'];
                    }
                    $totalWaiting += $item['billsec'];
                    $countWaiting++;
                }
                //убить айтемы
                unset($users[$key]['resp']);
            }
            $users[$key]['waiting'] = $countWaiting ? round($totalWaiting / $countWaiting) : 0;
            //самое большое время ожидания
            $users[$key]['longest'] = $longest;
            
            foreach ($user['sessions'] as $sess) {
                $users[$key]['outgoing_abandon'] += $sess['outgoing_abandon'];
                $users[$key]['outgoing_callme'] += $sess['outgoing_callme'];
            }
        }
        
        
        return $users;
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
