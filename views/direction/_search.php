<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;

?>

<?php
$formId = uniqid();
$form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{hint}",
    ],
    'options' => [
        'id' => $formId,
        'class' => 'events-search-form',
    ],
]);
?>

<div class="row">
    <table class="form-table inner">
        <tr>
            <td style="width: 110px;">
                <?= $form->field($model, 'number') ?>
            </td>
            <td>
                <?php
                $uid = uniqid();
                ?>
                <div class="relation-picker relation-picker__price" data-id="<?= $uid; ?>">
                    <label class="control-label" for="<?= $uid; ?>">Услуга</label>
                    <?=
                    Html::activeHiddenInput($model, 'price_id', [
                        'class' => 'form-control target_value',
                        'data-text' => $model->price_id ? $model->price->title : '',
                        'id' => $uid
                    ]);
                    ?>
                    <div class="clearfix">
                        <div class="btn-ctr pull-right">
                            <span class="item item-open-picker" title="Расширенный поиск"></span>
                            <span class="item item-clear" title="Очистить значение"></span>
                        </div>
                        <div class="search_input-ctr">
                            <?=
                            Html::input('text', '', $model->price_id ? $model->price->title : '', [
                                'class' => 'form-control search_input',
                                'placeholder' => 'поиск'
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <?php
                $uid = uniqid();
                ?>
                <div class="relation-picker relation-picker__patient" data-id="<?= $uid; ?>">
                    <label class="control-label" for="<?= $uid; ?>">Пациент</label>
                    <?=
                    Html::activeHiddenInput($model, 'patient_id', [
                        'class' => 'form-control target_value',
                        'data-text' => $model->patient_id ? $model->patient->fio : '',
                        'id' => $uid
                    ]);
                    ?>
                    <div class="clearfix">
                        <div class="btn-ctr pull-right">
                            <span class="item item-open-picker" title="Расширенный поиск"></span>
                            <span class="item item-clear" title="Очистить значение"></span>
                        </div>
                        <div class="search_input-ctr">
                            <?=
                            Html::input('text', '', $model->patient_id ? $model->patient->fio : '', [
                                'class' => 'form-control search_input',
                                'placeholder' => 'поиск'
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <?php
                $uid = uniqid();
                ?>
                <div class="relation-picker relation-picker__doctor" data-id="<?= $uid; ?>">
                    <label class="control-label" for="<?= $uid; ?>">Специалист</label>
                    <?=
                    Html::activeHiddenInput($model, 'doctor_id', [
                        'class' => 'form-control target_value',
                        'data-text' => $model->doctor_id ? $model->doctor->fio : '',
                        'id' => $uid
                    ]);
                    ?>
                    <div class="clearfix">
                        <div class="btn-ctr pull-right">
                            <span class="item item-open-picker" title="Расширенный поиск"></span>
                            <span class="item item-clear" title="Очистить значение"></span>
                        </div>
                        <div class="search_input-ctr">
                            <?=
                            Html::input('text', '', $model->doctor_id ? $model->doctor->fio : '', [
                                'class' => 'form-control search_input',
                                'placeholder' => 'поиск'
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </td>
            <td class="form-group_submit" style="width: 120px;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-search">Найти</button>
            </td>
        </tr>
    </table>
</div>

<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        $('#<?=$formId?> .relation-picker__price').relationPicker({
            url_picker: '<?= Url::to(['price/picker']); ?>',
            url_ac: '<?= Url::to(['price/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'pricePick'
        });

        $('#<?=$formId?> .relation-picker__patient').relationPicker({
            url_picker: '<?= Url::to(['patient/picker']); ?>',
            url_ac: '<?= Url::to(['patient/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'patientPick'
        });

        $('#<?=$formId?> .relation-picker__doctor').relationPicker({
            url_picker: '<?= Url::to(['doctor/picker']); ?>',
            url_ac: '<?= Url::to(['doctor/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'doctorPick'
        });
    });
</script>