<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;
?>
<?php
$formUid = uniqid();
$form = ActiveForm::begin([
            'action' => ['picker'],
            'id' => $formUid,
            'method' => 'get',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}",
            ],
            'validateOnType' => false,
            'enableAjaxValidation' => false
        ]);
?>

<div class="row">
    <table class="form-table inner">
        <tr>

            <td style="width:30%;">
                <?= $form->field($searchModel, 'name_fio')->textInput(['maxlength' => 50]) ?>
            </td>

            <td>
                <?=
                $form->field($searchModel, 'spec')->dropDownList(
                    $searchModel->specialitiesSelect, [
                        'class' => 'selectpicker',
                        'prompt' => 'Все'
                    ]
                );
                ?>
            </td>

            <td>
                <?=
                $form->field($searchModel, 'subdivision_id')->dropDownList(
                    $searchModel->subdivisionsSelect, [
                        'class' => 'selectpicker',
                        'prompt' => 'Все'
                    ]
                );
                ?>
            </td>

            <td class="col_submit" style="width: 120px;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-search">Найти</button>
            </td>
        </tr>
    </table>
</div>
<?= Html::hiddenInput('target', Yii::$app->request->get('target'));?>
<?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
        $('#<?= $formUid; ?> .selectpicker').selectpicker();

        $('#<?= $formUid; ?>').on('submit', function () {
            var url = $(this).attr('action') + '?' + $(this).serialize();
            var $modal = $(this).closest('.modal-wrap');
            $modal.trigger('updateData', {url: url}).trigger('reload');
            return false;
        });
    });
</script>