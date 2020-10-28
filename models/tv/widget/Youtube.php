<?php

namespace app\models\tv\widget;

use yii\base\Model;

class Youtube extends Model {

    public $url;
    
    private $_name = 'youtube',
            $_title = 'Youtube видео';

    public function rules() {
        return [
            [['url'], 'required']
        ];
    }

    public function getName() {
        return $this->_name;
    }
    
    public function getTitle() {
        return $this->_title;
    }
}
