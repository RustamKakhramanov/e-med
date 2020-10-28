<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
?>

<?php
$form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}",
            ],
            'options' => [
                'class' => 'patients-search-form',
            ],
        ]);
?>
<table class="form-table inner">
    <tr>
        <td style="width: 180px;">
            <label>&nbsp;</label>
            <ul class="tabs tabs-form clearfix">
                <li class="<?php if ($model->active_tab == 'paid') echo 'active';?>"><a href="#paid" data-toggle="tab">Оплаченные</a></li>
                <li class="<?php if ($model->active_tab == 'nopaid') echo 'active';?>"><a href="#nopaid" data-toggle="tab">К оплате</a></li>
            </ul>
        </td>
        <td>
            <?= $form->field($model, 'patient_id'); ?>
        </td>
        <td>
            <?=
            $form->field($model, 'period')->dropDownList(
                    $model::$periods, [
                'class' => 'selectpicker'
                    ]
            );
            ?>
        </td>
        <td style="width: 90px;">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-block btn-search">Найти</button>
        </td>
    </tr>
</table>

<?php ActiveForm::end(); ?>