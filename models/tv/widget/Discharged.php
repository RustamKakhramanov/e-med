<?php

namespace app\models\tv\widget;

use yii\base\Model;
use Yii;

class Discharged extends Model {
    public $strings;
    
    protected $_name = 'discharged',
            $_title = 'Сегодня выписываются';
    
    public function rules() {
        return [
            [['strings'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'strings' => 'Пациенты'
        ];
    }

    public function getName() {
        return $this->_name;
    }

    public function getTitle() {
        return $this->_title;
    }
    
}
