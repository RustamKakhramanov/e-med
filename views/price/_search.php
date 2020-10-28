<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;
?>
<?php
$form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}",
            ],
            'options' => [
                'class' => 'price-search-form',
            ],
            'validateOnType' => false,
            'enableAjaxValidation' => false
        ]);
?>

<div class="row">
    <table class="form-table inner">
        <tr>
            <td style="width: 40%;">
                <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
            </td>

            <td style="width: 195px;">
                <div class="row">
                    <div class="col-xs-6">
                        <?=
                        $form->field($model, 'cost_min', ['options' => ['class' => '']])->widget(\yii\widgets\MaskedInput::className(), [
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
                        $form->field($model, 'cost_max', ['options' => ['class' => '']])->widget(\yii\widgets\MaskedInput::className(), [
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
                $form->field($model, 'type')->dropDownList(
                        $model::$types, [
                    'class' => 'selectpicker',
                    'prompt' => ' '
                        ]
                );
                ?>
            </td>

            <td class="col_submit">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-search">Найти</button>
            </td>
        </tr>
    </table>
</div>
<?= Html::activeInput('hidden', $model, 'group_id'); ?>
<?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
        $('.price-search-form').on('submit', function () {
            var $form = $(this);
            var data = $form.serialize();
            history.pushState('', '', '/<?= Yii::$app->controller->id; ?>?' + data);
            
            $.ajax({
                url: $form.attr('action'),
                method: 'get',
                data: data,
                success: function(resp) {
                    $('.price-items-ctr .items').html(resp);
                    
                }
            });
            return false;
        });
    });
</script>