<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = $model->id ? 'Редактировать позицию прайса' : 'Добавить позицию прайса';
?>

<div class="row">
    <div class="col-md-12">

        <h1><?= $this->title; ?></h1>

        <?php
        $form = ActiveForm::begin([
                    'method' => 'post',
                    'options' => [
                        'class' => 'pricelist-edit-form pb100',
                        'novalidate' => '', //ng
                    ],
                    'validateOnType' => true,
                    'enableAjaxValidation' => true,
        ]);
        ?>
        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'title'); ?>
            </div>

        </div>

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'title_print'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-4">
                <?=
                $form->field($model, 'group_id')->dropDownList(
                        ArrayHelper::map($groups, 'id', 'name'), [
                    'class' => 'selectpicker select-price-group',
                    'data-live-search' => 'true'
                        ]
                );
                ?>
            </div>
            <div class="col-xs-2">
                <?=
                $form->field($model, 'type')->dropDownList(
                        $model::$types, [
                    'class' => 'selectpicker',
                        //'prompt' => ' '
                        ]
                );
                ?>
            </div>
            <div class="col-xs-2">
                <?=
                $form->field($model, 'cost')->textInput([
                    'class' => 'form-control text-right'
                ]);
                ?>
            </div>
        </div>

        <div class="repeated-ctr" style="display: <?= $model->type == 0 ? 'block' : 'none';?>">
            <label>Тип приема</label>
            <div class="btn-group radiopicker" data-toggle="buttons">
                <label class="btn btn-lg btn-select <?php if (!$model->repeated) { ?>active<?php } ?>">
                    <?=
                    Html::radio('Price[repeated]', !$model->repeated, [
                        'value' => 0,
                        'label' => null,
                        'uncheck' => null,
                        'checked' => true,
                        //'class' => 'j-' . $classNameLower . '-' . $attribute,
                        'id' => 'repeated-false'
                    ]);
                    ?>
                    Первичный
                </label>

                <label class="btn btn-lg btn-select <?php if ($model->repeated) { ?>active<?php } ?>">
                    <?=
                    Html::radio('Price[repeated]', $model->repeated, [
                        'value' => 1,
                        'label' => null,
                        'uncheck' => null,
                        //'class' => 'j-'.$classNameLower.'-'.$attribute,
                        'id' => 'repeated-true'
                    ]);
                    ?>
                    Повторный
                </label>
            </div>
        </div>

        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="<?= Yii::$app->request->referrer; ?>" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<style>
    .select-price-group .dropdown-menu {
        right: 0px;
    }

    .select-price-group .dropdown-menu.inner {
        overflow-x: hidden;
    }
</style>

<script>
    $(document).ready(function () {
        
        $('#price-type').on('change', function(){
            if ($(this).val() == 0) {
                $('.repeated-ctr').show();
            } else {
                $('.repeated-ctr').hide();
            }
        });
        
    });
</script>