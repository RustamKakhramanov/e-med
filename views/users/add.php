<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $model app\models\User */

$this->title = $model->id ? 'Редактировать пользователя' : 'Добавить пользователя';

$extraParams = json_decode($model->extra, true);
?>

<div class="row">
    <div class="col-md-12">

        <h1><?= $this->title; ?></h1>

        <?php
        $form = ActiveForm::begin([
            'method' => 'post',
            'options' => [
                'class' => 'users-add-form pb100',
                'novalidate' => '', //ng
            ],
            'validateOnType' => true,
            'enableAjaxValidation' => true,
        ]);
        ?>

        <div class="row">
            <div class="col-xs-3">
                <?=
                $form->field($model, 'role_id')->dropDownList(
                    [
                        $model::ROLE_ADMIN => $model::$arRoleLabels[$model::ROLE_ADMIN],
                        $model::ROLE_SPECIALIST => $model::$arRoleLabels[$model::ROLE_SPECIALIST],
                        $model::ROLE_OPERATOR => $model::$arRoleLabels[$model::ROLE_OPERATOR],
                        $model::ROLE_MANAGER => $model::$arRoleLabels[$model::ROLE_MANAGER],
                        $model::ROLE_CASHIER => $model::$arRoleLabels[$model::ROLE_CASHIER],
                    ], [
                        'class' => 'selectpicker',
                        'prompt' => ' '
                    ]
                );
                ?>
            </div>

            <div class="col-xs-3">
                <div class="fio-picker">
                    <?= $form->field($model, 'fio') ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3">
                <?= $form->field($model, 'username') ?>
            </div>
            <div class="col-xs-3">
                <?= $form->field($model, 'password_new') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <?=
                $form->field($model, 'branch_id')->dropDownList(
                    ArrayHelper::map($branches, 'id', 'name'), [
                        'class' => 'selectpicker',
                        //'prompt' => ' '
                    ]
                );
                ?>
            </div>
        </div>

        <div class="role-block operator-fields <?php if ($model->role_id != $model::ROLE_OPERATOR) echo 'hidden'; ?>">
            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label" for="extra-number">Внутренние номера (разделитель ;)</label>
                        <input type="text" id="extra-number" class="form-control" name="extra[number]"
                               value="<?= isset($extraParams['number']) ? $extraParams['number'] : ''; ?>">
                    </div>
                </div>

                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label" for="extra-number">Очередь</label>
                        <input type="text" id="extra-queue_number" class="form-control" name="extra[queue_number]"
                               value="<?= isset($extraParams['queue_number']) ? $extraParams['queue_number'] : ''; ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="role-block cashier-fields <?php if ($model->role_id != $model::ROLE_CASHIER) echo 'hidden'; ?>">
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <?php $uid = uniqid(); ?>
                        <?= Html::label('Касса'); ?>
                        <div class="relation-picker" data-id="<?= $uid; ?>">
                            <?=
                            Html::hiddenInput('extra[cashbox_id]', isset($extraParams['cashbox_id']) ? $extraParams['cashbox_id'] : '', [
                                'class' => 'target_value',
                                'data-text' => $model->getExtraParam('cashbox_id') ? $model->cashbox->name : '',
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
                                    Html::input('text', '', $model->getExtraParam('cashbox_id') ? $model->cashbox->name : '', [
                                        'class' => 'form-control search_input',
                                        'placeholder' => 'поиск'
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="role-block specialist-fields <?php if ($model->role_id != $model::ROLE_SPECIALIST) echo 'hidden'; ?>">
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <?php $uid = uniqid(); ?>
                        <?= Html::label('Специалист'); ?>
                        <div class="relation-picker" data-id="<?= $uid; ?>">
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
                                    Html::input('text', '', $model->getExtraParam('cashbox_id') ? $model->cashbox->name : '', [
                                        'class' => 'form-control search_input',
                                        'placeholder' => 'поиск'
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="/<?= Yii::$app->controller->id; ?>" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<script>
    var rolesIds = <?=json_encode($model::$arRoleNames);?>;
    $(document).ready(function () {
        $('#user-role_id').on('change', function () {
            $('.role-block').addClass('hidden');
            if ($(this).val()) {
                $('.role-block.' + rolesIds[$(this).val()] + '-fields').removeClass('hidden');
            }
        });

        $('.cashier-fields .relation-picker').relationPicker({
            url_picker: '<?= Url::to(['cashbox/picker']); ?>',
            url_ac: '<?= Url::to(['cashbox/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'cashboxPick'
        });

        $('.specialist-fields .relation-picker').relationPicker({
            url_picker: '<?= Url::to(['doctor/picker']); ?>',
            url_ac: '<?= Url::to(['doctor/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'doctorPick'
        });

        //TODO сделать валидацию спец полей
    });
</script>