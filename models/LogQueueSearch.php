<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LogQueue;
use Carbon\Carbon;

class LogQueueSearch extends Model {

    const GROUP_TYPE_HOUR = 0;
    const GROUP_TYPE_DAY = 1;

    public static $group_types = [
        self::GROUP_TYPE_HOUR => 'По часам',
        self::GROUP_TYPE_DAY => 'По дням'
    ];
    public $queue,
            $group_type,
            $date_from,
            $date_to,
            $time_start,
            $time_end

    ;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['queue', 'date_from', 'date_to', 'time_start', 'time_end', 'group_type'], 'required', 'message' => 'Обязательно'],
            [['date_from', 'date_to'], 'timeValidate']
        ];
    }

    public function timeValidate($field, $attribute) {
        if ($this->time_start && $this->time_end && $this->date_from && $this->date_to) {
            $start = strtotime($this->date_from . ' ' . $this->time_start);
            $end = strtotime($this->date_to . ' ' . $this->time_end);

            if ($end <= $start) {
                $this->addError($field, 'Неверный период');
            }
        }
    }
    
    public function setDefault(){
        $this->date_from = date('Y-m-d');
        $this->date_to = date('Y-m-d');
        $this->time_start = '08:00';
        $this->time_end = '18:00';
        $this->group_type = self::GROUP_TYPE_HOUR;
    }

    public function attributeLabels() {
        return [
            'queue' => 'Очередь',
            'date_from' => 'Дата с',
            'date_to' => 'Дата по',
            'time_start' => 'Время с',
            'time_end' => 'Время по',
            'group_type' => 'Группировать'
        ];
    }

    public function search() {
        $periods = $this->group_type == self::GROUP_TYPE_HOUR ? $this->_periodsHour() : $this->_periodsDay();

        $qlist = LogQueue::find()
                ->where(['branch_id' => Yii::$app->user->identity->branch_id])
                ->andWhere(['=', 'queue', $this->queue])
                ->andWhere(['>=', 'date', $this->date_from . ' ' . $this->time_start])
                ->andWhere(['<', 'date', $this->date_to . ' ' . $this->time_end])
                ->orderBy(['date' => SORT_ASC])
                ->all();

        foreach ($periods as $key => $period) {
            //разброс данных по периодам
            foreach ($qlist as $row) {
                $rowDate = Carbon::createFromFormat('Y-m-d H:i:s', $row->date);
                if ($rowDate >= $period['start'] && $rowDate < $period['end']) {
                    $periods[$key]['items'][] = $row;
                }
            }
            $period = $periods[$key];

            //подсчет сброшенных
            $abandoned = 0;
            $abandoned_last = null;
            foreach ($period['items'] as $item) {
                if ($abandoned_last !== null) {
                    if ($item->abandoned >= $abandoned_last) {
                        $abandoned += $item->abandoned - $abandoned_last;
                    }
                }
                $abandoned_last = $item->abandoned;
            }
            $periods[$key]['data']['abandoned'] = $abandoned;
            
            //подсчет отвеченных
            $completed = 0;
            $completed_last = null;
            foreach ($period['items'] as $item) {
                if ($completed_last !== null) {
                    if ($item->completed >= $completed_last) {
                        $completed += $item->completed - $completed_last;
                    }
                }
                $completed_last = $item->completed;
            }
            $periods[$key]['data']['completed'] = $completed;
            
            //общее кол-во звонков
            $periods[$key]['data']['calls'] = $periods[$key]['data']['abandoned'] + $periods[$key]['data']['completed'];
            
            //подсчет времени ожидания
            $elist = \app\models\LogQueueEntry::find()
                    ->where(['queue' => $this->queue])
                    ->andWhere(['>=', 'closed', $period['start']->format('Y-m-d H:i:s')])
                    ->andWhere(['<', 'closed', $period['end']->format('Y-m-d H:i:s')])
                    ->andWhere(['branch_id' => Yii::$app->user->identity->branch_id])
                    ->all();
            $totalWait = 0;
            $longest = 0;
            if ($elist) {
                foreach ($elist as $row) {
                    $totalWait += $row->duration;
                    if ($row->duration > $longest) {
                        $longest = $row->duration;
                    }
                }
                $periods[$key]['data']['waiting'] = round($totalWait / count($elist));
            } else {
                $periods[$key]['data']['waiting'] = 0;
            }
            
            //самое большое время ожидания
            $periods[$key]['data']['longest'] = $longest;
            
            //убить айтемы
            unset($periods[$key]['items']);
        }

        return $periods;
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
