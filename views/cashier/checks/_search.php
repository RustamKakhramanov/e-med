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
                'class' => 'patients-search-form',
            ],
        ]);
?>

<div class="row">
    <table class="form-table inner">
        <tr>
            <td style="width:30%;">
                <?= $form->field($model, 'name_fio')->textInput(['maxlength' => 50]) ?>
            </td>

            <td>
                <?=
                $form->field($model, 'spec')->dropDownList(
                        ArrayHelper::map($specialities, 'id', 'name'), [
                    'class' => 'selectpicker',
                    'data-live-search' => 'true',
                    'prompt' => 'Все'
                        ]
                );
                ?>
            </td>

            <td>
                <?=
                $form->field($model, 'subdivision_id')->dropDownList(
                        ArrayHelper::map($subdivisions, 'id', 'name'), [
                    'class' => 'selectpicker',
                    'data-live-search' => 'true',
                    'prompt' => 'Все'
                        ]
                );
                ?>
            </td>
            <td class="form-group_dismissed">
                <div class="checkbox mt10">
                    <?=
                    Html::activeCheckbox($model, 'is_fired', [
                        'label' => null,
                    ])
                    ?>
                    <label for="doctorsearch-is_fired"><?= $model->getAttributeLabel('is_fired') ?></label>
                </div>
            </td>
            <td class="form-group_submit">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-search">Найти</button>
            </td>
        </tr>
    </table>
</div>

<a href="/<?= Yii::$app->controller->id; ?>" class="btn btn-sm btn-default mt15">Очистить</a>

<?php ActiveForm::end(); ?>
