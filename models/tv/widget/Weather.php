<?php

namespace app\models\tv\widget;

use yii\base\Model;
use Carbon\Carbon;

class Weather extends Model {

    public $lat = 43.25,
            $lng = 76.95,
            $days = 3;
    
    private $_name = 'weather',
            $_title = 'Погода';

    public function rules() {
        return [
            [['lat', 'lng', 'days'], 'required'],
            [['days'], 'integer']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'lat' => 'lat',
            'lng' => 'lng',
            'days' => 'Кол-во дней',
        ];
    }

    public function getName() {
        return $this->_name;
    }
    
    public function getTitle() {
        return $this->_title;
    }
    
    public function getForecast(){
        $source = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/forecast/daily?lat='.$this->lat.'&lon='.$this->lng.'&appid=9eafbd9fa4f132c3f246db3b2666e4ce&units=metric&cnt=' . $this->days), true);
        $days = ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];
        $today = date('Y-m-d');
        $data = [];
        foreach ($source['list'] as $item) {
            $time = Carbon::createFromTimestamp($item['dt']);
            $data[] = [
                'value' => ($item['temp']['day'] ? '+' : '') . round($item['temp']['day']),
                'icon' => '//openweathermap.org/img/w/' . $item['weather'][0]['icon'] . '.png',
                'day' => $time->format('Y-m-d') == $today ? 'Сегодня' : $days[$time->format('N') - 1]
            ];
        }
        
        return $data;
    }
}
