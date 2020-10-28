<?php

use yii\helpers\Url;
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
                'class' => 'events-search-form',
            ],
        ]);
?>

<div class="row">
    <table class="form-table inner">
        <tr>
            <td class="form-group_date">
                <?=
                $form->field($model, 'date_from')->label('Период с')->widget(Calendar::className(), [
                    'form' => $form,
                    'options' => [
                        'showArrows' => false,
                        'readonly' => true,
                        'dropClass' => ''
                    ]
                ]);
                ?>
            </td>
            <td class="form-group_date">
                <?=
                $form->field($model, 'date_to')->label('по')->widget(Calendar::className(), [
                    'form' => $form,
                    'options' => [
                        'showArrows' => false,
                        'readonly' => true
                    ]
                ]);
                ?>
            </td>
            <td>
                <?= $form->field($model, 'patient_fio')->textInput(['maxlength' => 50]) ?>
            </td>
            <td>
                <?= $form->field($model, 'doctor_fio')->textInput(['maxlength' => 50]) ?>
            </td>
            <td class="form-group_submit">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-search">Найти</button>
            </td>
        </tr>
    </table>
</div>

<div class="extra form-group collapse <?= (Yii::$app->request->get('EventSearch')) ? 'in' : '' ?>" aria-expanded="true" id="extra">
    <div class="clearfix">
        <div class="pull-left">
            <label>Вид операции</label>
            <ul class="tabs tabs-small tabs-form tabs-checkbox-handler clearfix">
                <?php foreach ($model::$types as $key => $type) { ?>
                    <li class="<?php if ($model->type == $key) echo 'active'; ?>"><a href="#" data-toggle="tab" data-value="<?= $key; ?>"><?= $type; ?></a></li>
                <?php } ?>
                <!--                <li class="active"><a href="#" data-toggle="tab">Все</a></li>
                                <li class=""><a href="#" data-toggle="tab">Звонок</a></li>
                                <li class=""><a href="#" data-toggle="tab">Он-лайн запись</a></li>
                                <li class=""><a href="#" data-toggle="tab">Веб ассистент</a></li>
                                <li class=""><a href="#" data-toggle="tab">Эл. почта</a></li>-->

                <?= Html::activeInput('hidden', $model, 'type'); ?>
            </ul>
        </div>
<!--        <div class="pull-left ml20">
            <label>Статус</label>
            <ul class="tabs tabs-small tabs-form  clearfix">
                <li class="active"><a href="#" data-toggle="tab">Все</a></li>
                <li class=""><a href="#" data-toggle="tab"><i class="fa fa-circle dot-icon text-success"></i>Активный</a></li>
                <li class=""><a href="#" data-toggle="tab"><i class="fa fa-circle dot-icon text-warning"></i>В работе</a></li>
                <li class=""><a href="#" data-toggle="tab"><i class="fa fa-circle dot-icon text-danger"></i>Отказ</a></li>
            </ul>
        </div>-->
    </div>
</div>

<div class="clearfix">
    <div data-target="#extra" class="btn btn-sm btn-default pull-left extra-handler <?= (Yii::$app->request->get('EventSearch')) ? '' : 'collapsed' ?>" data-toggle="collapse">Расширенный поиск <span class="caret ml5"></span></div>
    <a href="<?= Url::toRoute('/' . Yii::$app->controller->id); ?>" class="btn btn-sm btn-default pull-left ml10">Очистить</a>
</div>

<?php ActiveForm::end(); ?>

<script>

    $(document).ready(function () {
        //чекбоксы в виде табов
        $('.tabs-checkbox-handler a').on('click', function () {
            $('input[type="hidden"]', $(this).closest('.tabs-checkbox-handler')).val($(this).attr('data-value'));
        });

    });
</script>