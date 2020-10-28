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
            'options' => [
                'class' => 'patients-search-form',
            ],
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
            <td>
                <?= $form->field($searchModel, 'name_query')->textInput(['maxlength' => 50]) ?>
            </td>
            <td class="form-group_date">
                <?= $form->field($searchModel, 'birthday')->widget(Calendar::className(), ['form' => $form]); ?>
            </td>
            <td class="col_phone">
                <?= $form->field($searchModel, 'phone'); ?>
            </td>
            <td class="col_submit">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-search">Найти</button>
            </td>
        </tr>
    </table>
</div>
<?= Html::hiddenInput('target', Yii::$app->request->get('target'));?>
<?php ActiveForm::end(); ?>

<style>
    .patients-search-form .col_phone {
        width: 150px;
    }
    .patients-search-form .col_submit {
        width: 90px;
    }
</style>

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