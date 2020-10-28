<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<div class="pl20 pr20 pb20" style="min-width: 600px;">
    <h1><?= $widget->title; ?></h1>

    <?php
    $formId = uniqid();
    $form = ActiveForm::begin([
                'method' => 'post',
                'id' => $formId,
                'options' => [
                    'class' => 'pb80',
                ],
                'validateOnType' => true,
                'enableAjaxValidation' => true,
    ]);
    ?>

    <div class="schedule-widget">
        <div class="row">
            <div class="col-xs-6">
                <?=
                $form->field($widget, 'monday')->dropDownList(
                        ArrayHelper::map($widget->availableDoctors, 'id', 'initials'), [
                    'class' => 'selectpicker',
                    'multiple' => true,
                    'data-live-search' => 'true',
                    'title' => 'Не выбрано',
                    'data-selected-text-format' => 'count'
                        ]
                );
                ?>
            </div>
            <div class="col-xs-6">
                <?=
                $form->field($widget, 'tuesday')->dropDownList(
                        ArrayHelper::map($widget->availableDoctors, 'id', 'initials'), [
                    'class' => 'selectpicker',
                    'multiple' => true,
                    'data-live-search' => 'true',
                    'title' => 'Не выбрано',
                    'data-selected-text-format' => 'count'
                        ]
                );
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?=
                $form->field($widget, 'wednesday')->dropDownList(
                        ArrayHelper::map($widget->availableDoctors, 'id', 'initials'), [
                    'class' => 'selectpicker',
                    'multiple' => true,
                    'data-live-search' => 'true',
                    'title' => 'Не выбрано',
                    'data-selected-text-format' => 'count'
                        ]
                );
                ?>
            </div>
            <div class="col-xs-6">
                <?=
                $form->field($widget, 'thursday')->dropDownList(
                        ArrayHelper::map($widget->availableDoctors, 'id', 'initials'), [
                    'class' => 'selectpicker',
                    'multiple' => true,
                    'data-live-search' => 'true',
                    'title' => 'Не выбрано',
                    'data-selected-text-format' => 'count'
                        ]
                );
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?=
                $form->field($widget, 'friday')->dropDownList(
                        ArrayHelper::map($widget->availableDoctors, 'id', 'initials'), [
                    'class' => 'selectpicker',
                    'multiple' => true,
                    'data-live-search' => 'true',
                    'title' => 'Не выбрано',
                    'data-selected-text-format' => 'count'
                        ]
                );
                ?>
            </div>
            <div class="col-xs-6">
                <?=
                $form->field($widget, 'saturday')->dropDownList(
                        ArrayHelper::map($widget->availableDoctors, 'id', 'initials'), [
                    'class' => 'selectpicker',
                    'multiple' => true,
                    'data-live-search' => 'true',
                    'title' => 'Не выбрано',
                    'data-selected-text-format' => 'count'
                        ]
                );
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <?=
                $form->field($widget, 'sunday')->dropDownList(
                        ArrayHelper::map($widget->availableDoctors, 'id', 'initials'), [
                    'class' => 'selectpicker',
                    'multiple' => true,
                    'data-live-search' => 'true',
                    'title' => 'Не выбрано',
                    'data-selected-text-format' => 'count'
                        ]
                );
                ?>
            </div>
        </div>
    </div>

    <input type="hidden" name="_from_form" value="1"/>

    <div class="form-end mt30" style="left:0;">
        <div class="btn btn-lg btn-primary js-submit-handler">Сохранить</div>
        <span class="ml10 mr10">или</span>
        <a href="#" class="btn btn-sm btn-default js-cancel-handler">Отменить</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<style>
    .schedule-widget {
        position: relative;
    }
</style>

<script>
    $(document).ready(function () {
        $('#<?= $formId; ?> .selectpicker').selectpicker({
            countSelectedText: function (num, element) {
                return num + ' ' + declOfNum(num, ['врач', 'врача', 'врачей']);
            }
        });

        $('#<?= $formId; ?> .js-cancel-handler').on('click', function () {
            $(this).closest('.modal-wrap').trigger('close');
            return false;
        });

        $('#<?= $formId; ?> .js-submit-handler').on('click', function () {
            $(this).closest('form').submit();
            return false;
        });

        $('#<?= $formId; ?>').on('beforeSubmit', function () {
            var $f = $(this);
            $.ajax({
                url: $f.attr('action'),
                type: 'post',
                data: $f.serialize() + '&_sended=1',
                dataType: 'json',
                success: function (data) {
                    tvData.oncall = data;
                    $f.closest('.modal-wrap').trigger('close');
                }
            });
            return false;
        });
    });
</script>