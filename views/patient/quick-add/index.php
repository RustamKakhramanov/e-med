<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;
?>
<?php
$formUid = uniqid();
$form = ActiveForm::begin([
            'id' => $formUid,
            //'action' => ['index'],
            'action' => Url::to([Yii::$app->controller->id . '/' . Yii::$app->controller->action->id]),
            'method' => 'post',
            'options' => [
                'class' => '',
                'novalidate' => '',
            ],
            'validateOnType' => false,
            'validateOnBlur' => true,
            'validateOnChange' => false,
            'enableAjaxValidation' => true,
        ]);
?>

<div class="pl20 pr20 pb10" style="width: 820px;">
    <h1>Создание пациента</h1>

    <div class="clearfix mt5">
        <div class="btn btn-primary pull-left form-submit-handler">Сохранить</div>
        <div class="btn btn-default pull-left ml20 form-cancel-handler">Отмена</div>
    </div>

    <div class="row mt20">
        <div class="col-xs-4">
            <?= $form->field($model, 'last_name') ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'first_name') ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'middle_name') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <?= $form->field($model, 'phone') ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'email') ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'birthday')->widget(Calendar::className(), ['form' => $form]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <?= $form->field($model, 'sex')->widget(SexPicker::className(), ['form' => $form]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    .input-datepicker {
        width: 100%;
    }
</style>

<script>
    $(document).ready(function () {
        $('#<?= $formUid; ?> .form-cancel-handler').on('click', function () {
            $(this).closest('.modal-wrap').trigger('close');
        });

//        $('#<?= $formUid; ?> .form-submit-handler').on('click', function () {
//            $(this).closest('form').submit();
//        });

        $('#eventnewpatient-phone').inputmask({
            mask: '+7 (999) 999 9999',
            autoUnmask: false,
            clearIncomplete: true
        });

        $('#<?= $formUid; ?> .input-datepicker-ui').datepicker({
            dateFormat: 'dd.mm.yy',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:2020',
            onSelect: function (date) {
                var $parent = $(this).closest('.input-datepicker');
                $('input', $parent).val(date);
                $('.dropdown-handler', $parent).dropdown('toggle');
            }
        });
        $('#<?= $formUid; ?> .input-datepicker .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });

        $('#<?= $formUid; ?>').on('submit', function () {
            var $form = $(this);
            var $modal = $form.closest('.modal-wrap');
            $.ajax({
                url: $form.attr('action'),
                type: 'post',
                data: {
                    EventNewPatient: $form.serializeObject().EventNewPatient,
                    _sended: true
                },
                dataType: 'json',
                success: function (resp) {
                    if (resp && resp.hasOwnProperty('id')) {
                        $form.yiiActiveForm('resetForm');
                        setTimeout(function () {
                            $('.patients-search-form', $('.modal-wrap[data-uid="' + $modal.attr('data-parent') + '"]')).submit();
                            $modal.trigger('close');
                        }, 10);
                    }
                }
            });
            return false;
        });
    });
</script>