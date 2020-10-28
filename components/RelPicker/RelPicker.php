<?php

namespace app\components\RelPicker;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * инпут для relationPicker
 */
class RelPicker extends Widget {
    public
        $form,
        $model,
        $field,
        $uid,
        $groupClass,
        $inputClass,
        $text;

    public function init() {
        parent::init();

        if ($this->uid == null) {
            $this->uid = uniqid();
        }
    }

    public function run() {
        return $this->render('index', [
            'form' => $this->form,
            'model' => $this->model,
            'field' => $this->field,
            'text' => $this->text,
            'uid' => $this->uid,
            'groupClass' => $this->groupClass,
            'inputClass' => $this->inputClass
        ]);
    }
}
