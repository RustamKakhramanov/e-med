<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = $model->id ? 'Редактировать устройство' : 'Добавить устройство';
?>

<div class="row">
    <div class="col-md-12">

        <h1><?= $this->title; ?></h1>

        <?php
        $form = ActiveForm::begin([
                    'method' => 'post',
                    'options' => [
                        'class' => 'device-add-form pb100',
                        'novalidate' => '', //ng
                    ],
                    'validateOnType' => true,
                    'enableAjaxValidation' => true,
        ]);
        ?>
        <div class="row">
            <div class="col-xs-4">
                <?= $form->field($model, 'name') ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-4">
                <?= $form->field($model, 'serial') ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-4">
                <?= $form->field($model, 'pass') ?>
            </div>
        </div>
        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="<?= Url::toRoute('/' . Yii::$app->controller->id); ?>" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>