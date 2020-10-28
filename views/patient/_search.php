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

<div class="row">
    <div class="col-xs-6">
        <table class="form-table">
            <tr>
                <td style="width:40%;">
                    <?= $form->field($model, 'last_name')->textInput(['maxlength' => 50]) ?>
                </td>
                <td>
                    <?= $form->field($model, 'first_name')->textInput(['maxlength' => 50]) ?>
                </td>
                <td>
                    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 50]) ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-xs-6">
        <table class="form-table">
            <tr>
                <td class="form-group_date">
                    <?= $form->field($model, 'birthday')->widget(Calendar::className(), ['form' => $form]); ?>
                </td>
                <td>
                    <?= $form->field($model, 'phone'); ?>
                </td>
                <td class="form-group_submit">
                    <label>&nbsp;</label>
                    <?= Html::submitButton('Найти', ['class' => 'btn btn-block btn-search']) ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="extra collapse <?= (Yii::$app->request->get('PatientsSearch')) ? 'in' : '' ?>" id="extra">
    <div class="row">
        <div class="col-xs-6">
            <table class="form-table">
                <tr>
                    <td class="hasTable" style="width:70%;">
                        <table class="form-table auto">
                            <tr>
                                <td class="form-group_age">
                                    <label>Возраст</label>
                                    <div class="clearfix form-group">
                                        <?=
                                        $form->field($model, 'age_from', ['options' => ['class' => ''], 'template' => '{input}'])->widget(\yii\widgets\MaskedInput::className(), [
                                            'mask' => '', //'9{1,2}'
                                            'clientOptions' => [
                                                'clearIncomplete' => true,
                                                'alias' => 'numeric',
                                            ],
                                        ])->textInput(['class' => 'form-control pull-left', 'maxlength' => 2])
                                        ?>

                                        <span class="pull-left">&ndash;</span>

                                        <?=
                                        $form->field($model, 'age_to', ['options' => ['class' => ''], 'template' => '{input}'])->widget(\yii\widgets\MaskedInput::className(), [
                                            'mask' => '', //'9{1,3}'
                                            'clientOptions' => [
                                                'clearIncomplete' => true,
                                                'alias' => 'numeric',
                                            ],
                                        ])->textInput(['class' => 'form-control pull-left', 'maxlength' => 3])
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <?= $form->field($model, 'iin'); ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <?= $form->field($model, 'area'); ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-2 form-group">
            <table class="form-table">
                <tr>
                    <td>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="clearfix">
    <div data-target="#extra" class="btn btn-sm btn-default pull-left extra-handler <?= (Yii::$app->request->get('PatientsSearch')) ? '' : 'collapsed' ?>" data-toggle="collapse">Расширенный поиск <span class="caret ml5"></span></div>
    <a href="/patient"><div class="btn btn-sm btn-default pull-left ml10">Очистить</div></a>
</div>

<?php ActiveForm::end(); ?>

<script>
    $(function () {
//        $('#patientssearch-phone').inputmask({
//            mask: '+7 (999) 999 9999',
//            autoUnmask: true,
//            clearIncomplete: true
//        });
    });
</script>