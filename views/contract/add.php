<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;

$this->title = $model->id ? 'Редактировать договор' : 'Добавить договор';
?>

<div class="row">
    <div class="col-md-12">

        <h1><?= $this->title; ?></h1>

        <?php
        $form = ActiveForm::begin([
                    'method' => 'post',
                    'options' => [
                        'class' => 'patients-add-form pb100',
                        'novalidate' => '', //ng
                    ],
                    'validateOnType' => true,
                    'enableAjaxValidation' => true,
        ]);
        ?>

        <?= $form->field($model, 'name') ?>
        
        <?= $form->field($model, 'start')->widget(Calendar::className(), ['form' => $form]); ?>
        
        <?= $form->field($model, 'end')->widget(Calendar::className(), ['form' => $form]); ?>
        
        <div class="checkbox">
            <?=
            Html::activeCheckbox($model, 'typical', [
                'label' => null,
            ])
            ?>
            <label for="contract-typical"><?= $model->getAttributeLabel('typical') ?></label>
        </div>
        

        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="/<?= Yii::$app->controller->id; ?>" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>