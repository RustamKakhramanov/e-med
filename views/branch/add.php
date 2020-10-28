<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = $model->id ? 'Редактировать филиал' : 'Добавить филиал';
$extraParams = json_decode($model->extra, true);
?>

<div class="row">
    <div class="col-md-12">

        <h1><?= $this->title; ?></h1>

        <?php
        $form = ActiveForm::begin([
            'method' => 'post',
            'options' => [
                'class' => 'patients-add-form pb100',
                'novalidate' => '', //ng
            ],
            'validateOnType' => true,
            'enableAjaxValidation' => true,
        ]);
        ?>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'name') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label" for="extra-aster">Aster ws url</label>
                    <input type="text" id="extra-aster" class="form-control" name="extra[aster]" value="<?= isset($extraParams['aster']) ? $extraParams['aster'] : ''; ?>">
                </div>
            </div>
        </div>

        <div class="checkbox" style="margin-top: 11px;">
            <?= Html::activeCheckbox($model, 'kassa', [
                'label' => null
            ])
            ?>
            <?= Html::activeLabel($model, 'kassa'); ?>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="kassa-settings <?php if (!$model->kassa) echo 'hidden'; ?>">
                    <?= $form->field($model, 'kassa_url'); ?>
                    <?= $form->field($model, 'kassa_login'); ?>
                    <?= $form->field($model, 'kassa_password'); ?>

                    <?=
                    $form->field($model, 'kassa_mode')->dropDownList(
                        $model->kassaModeLabels, [
                            'prompt' => 'Не выбрано',
                            'class' => 'selectpicker form-control'
                        ]
                    );
                    ?>
                </div>
            </div>
        </div>

        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="/branch" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>


<style>
    .kassa-settings {
        border-left: 1px #eceff3 solid;
        padding-left: 20px;
        margin-left: 6px;
    }
</style>

<script>
    $(document).ready(function () {
        $('#branch-kassa').on('change', function () {
            if ($(this).is(':checked')) {
                $('.kassa-settings').removeClass('hidden');
            } else {
                $('.kassa-settings').addClass('hidden');
            }
        });
    });
</script>