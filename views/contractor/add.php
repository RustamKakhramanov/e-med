<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = $model->id ? 'Редактировать контрагента' : 'Добавить контрагента';
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

        <div class="row">
            <div class="col-xs-9">
                <?= $form->field($model, 'name') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3">
                <?= $form->field($model, 'bin') ?>
            </div>

        </div>
        <div class="row">
            <div class="col-xs-3">
                <?= $form->field($model, 'phone') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-9">
                <?= $form->field($model, 'address') ?>
            </div>
        </div>

        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="/<?= Yii::$app->controller->id; ?>" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>