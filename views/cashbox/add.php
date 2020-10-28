<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = $model->id ? 'Редактировать кассу' : 'Добавить кассу';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>
        <?php
        $form = ActiveForm::begin([
            'method' => 'post',
            'options' => [
                'class' => 'pb100',
            ],
            'validateOnType' => true,
            'enableAjaxValidation' => true,
        ]);
        ?>
        <?= $form->field($model, 'name') ?>

        <?= $form->field($model, 'org_name') ?>

        <div class="row">
            <div class="col-xs-3">
                <?= $form->field($model, 'webkassa_id') ?>
            </div>
            <div class="col-xs-3">
                <div class="checkbox" style="margin-top:12px;">
                    <?=
                    Html::activeCheckbox($model, 'use_nds', [
                        'label' => null,
                    ])
                    ?>
                    <?= Html::activeLabel($model, 'use_nds'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3">
                <?= $form->field($model, 'nds_serie') ?>
            </div>
            <div class="col-xs-3">
                <?= $form->field($model, 'nds_number') ?>
            </div>
        </div>

        <?= $form->field($model, 'operator_name') ?>

        <div class="row">
            <div class="col-xs-3">
                <?= $form->field($model, 'bin') ?>
            </div>
            <div class="col-xs-3">
                <?= $form->field($model, 'kkt') ?>
            </div>
            <div class="col-xs-3">
                <?= $form->field($model, 'rnk') ?>
            </div>
        </div>
        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="<?= Url::to(['index']); ?>" class="btn btn-sm btn-default">Отменить</a>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>