<?php

namespace app\models\form;

use Yii;
use app\models\DirectionItem;

class DirectionCancel extends \yii\base\Model {

    public $cancel_reason;
    protected $_direction;

    public function rules() {
        return [
            ['cancel_reason', 'required', 'message' => 'Обязательно'],
            ['cancel_reason', 'safe']
        ];
    }

    public function attributeLabels() {
        return [
            'cancel_reason' => 'Введите причину отмены'
        ];
    }

    public function setDirection(DirectionItem $model) {
        $this->_direction = $model;
    }

    public function getDirection(){
        return $this->_direction;
    }

    //todo проверку прав

    public function save(){
        if ($this->_direction) {
            $this->_direction->setAttributes([
                'canceled' => true,
                'cancel_reason' => $this->cancel_reason,
                'cancel_user_id' => Yii::$app->user->identity->id
            ]);

            return $this->_direction->save(false);
        }
    }
}