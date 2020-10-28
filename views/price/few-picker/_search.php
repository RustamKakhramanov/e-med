<?php

/** @var $searchModel app\models\PriceSearch */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>

<div class="row">
    <table class="form-table inner">
        <tr>
            <td>
                <?= $form->field($searchModel, 'title')->textInput(['maxlength' => 255]) ?>
            </td>
            <td style="width: 130px;">
                <?=
                $form->field($searchModel, 'cost_min', ['options' => ['class' => '']])->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '',
                    'clientOptions' => [
                        'clearIncomplete' => true,
                        'alias' => 'numeric',
                        'allowMinus' => false,
                        'allowPlus' => false
                    ],
                ])->textInput(['class' => 'form-control', 'maxlength' => 8])
                ?>
            </td>
            <td style="width: 130px;">
                <?=
                $form->field($searchModel, 'cost_max', ['options' => ['class' => '']])->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '',
                    'clientOptions' => [
                        'clearIncomplete' => true,
                        'alias' => 'numeric',
                        'allowMinus' => false,
                        'allowPlus' => false
                    ],
                ])->textInput(['class' => 'form-control', 'maxlength' => 8])
                ?>
            </td>
            <td>
                <?php
                $uid = uniqid();
                ?>
                <div class="relation-picker relation-picker__doctor" data-id="<?= $uid; ?>">
                    <label class="control-label" for="<?= $uid; ?>">Специалист</label>
                    <?=
                    Html::activeHiddenInput($searchModel, 'doctor_id', [
                        'class' => 'form-control target_value',
                        'data-text' => $searchModel->doctor_id ? $searchModel->doctor->fio : '',
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
                            Html::input('text', '', $searchModel->doctor_id ? $searchModel->doctor->fio : '', [
                                'class' => 'form-control search_input',
                                'placeholder' => 'поиск'
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <?=
                $form->field($searchModel, 'type')->dropDownList(
                    $searchModel::$types, [
                        'class' => 'selectpicker',
                        'prompt' => 'Все'
                    ]
                );
                ?>
            </td>
            <td style="width: 120px;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-search">Найти</button>
            </td>
        </tr>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#<?= $formUid; ?> .selectpicker').selectpicker();

        $('#<?=$formUid?> .relation-picker__doctor').relationPicker({
            url_picker: '<?= Url::to(['doctor/picker']); ?>',
            url_ac: '<?= Url::to(['doctor/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'doctorPick'
        });

        $('#<?= $formUid; ?>').on('beforeSubmit', function () {
            var $picker = $(this).closest('.b-picker');
            $picker.addClass('block__loading');
            var url = $(this).attr('action');
            var $modal = $(this).closest('.modal-wrap');
            $modal.trigger('updateData', {url: url});//.trigger('reload');
            $.ajax({
                url: url,
                type: 'post',
                data: $(this).serialize(),
                success: function (right_html) {
                    $('.b-picker__few-price-right').html(right_html);
                    $picker.removeClass('block__loading');
                }
            });

            return false;
        });
    });
</script>