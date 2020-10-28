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
            <td style="width: 40%;">
                <?= $form->field($searchModel, 'title')->textInput(['maxlength' => 255]) ?>
            </td>

            <td style="width: 195px;">
                <div class="row">
                    <div class="col-xs-6">
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
                    </div>
                    <div class="col-xs-6">
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
                    </div>
                </div>
            </td>

            <td class="col_type">
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