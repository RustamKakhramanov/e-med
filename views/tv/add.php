<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $model app\models\User */

$this->title = $model->id ? 'Редактировать шаблон' : 'Добавить шаблон';
?>

<div class="row">
    <div class="col-md-12">

        <h1><?= $this->title; ?></h1>

        <?php
        $formId = uniqid();
        $form = ActiveForm::begin([
                    'method' => 'post',
                    'id' => $formId,
                    'options' => [
                        'class' => 'pb100',
                    ],
                    'validateOnType' => true,
                    'enableAjaxValidation' => true,
        ]);
        ?>
        <div class="row">
            <div class="col-xs-3">
                <?= $form->field($model, 'name') ?>
            </div>
            <div class="col-xs-3">
                <?= $form->field($model, 'code') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3">
                <?=
                $form->field($model, 'template')->dropDownList(
                        $model->templates, [
                    'class' => 'selectpicker',
//                    'prompt' => ' '
                        ]
                );
                ?>
            </div>
        </div>

        <div class="template-widgets-ctr">
            <?php if ($model->id) {
                echo $this->render('template/' . strtolower($model->template));
            }?>
        </div>

        <?= $form->field($model, 'data')->hiddenInput()->label(false); ?>

        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="/<?= Yii::$app->controller->id; ?>" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<script>
    var tvData = <?= $model->data; ?>;
    var id = <?= 1 * $model->id;?>;

    $(document).ready(function () {
        $('#tvschedule-template').on('change', function () {
            var $c = $('.template-widgets-ctr');
            var v = $(this).val();
            if (!v) {
                $c.html('');
                return false;
            }
            $c.html('Загрузка..');
            $.ajax({
                url: '/tv/load-template/?name=' + v,
                type: 'get',
                success: function (resp) {
                    $c.html(resp);
                }
            });
        });
        
        if (!id) {
            $('#tvschedule-template').trigger('change');
        }
        
        $('#<?=$formId;?>').on('beforeSubmit', function(){
            $('#tvschedule-data').val(JSON.stringify(tvData));
        });
    });
</script>