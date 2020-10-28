<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var $model app\models\TemplateVar */
?>

<div class="temp-vars-inner">
    <h3 class="mt10">
        <?= ($model->id) ? 'Редактировать' : 'Добавить'; ?> показатель
    </h3>

    <?php
    $formId = uniqid();
    $form = ActiveForm::begin([
        //'action' => ['template/add-var', 'id' => $model->template_id],
        'method' => 'post',
        'options' => [
            'id' => $formId,
            'class' => 'mt10'
        ],
        'validateOnType' => true,
        'enableAjaxValidation' => true,
    ]);
    ?>

    <?= $form->field($model, 'name'); ?>

    <?=
    $form->field($model, 'group_id')->dropDownList(
        ArrayHelper::map($model->template->templateVarGroups, 'id', 'name'), [
            'prompt' => 'Без группы',
            'class' => 'selectpicker form-control'
        ]
    );
    ?>

    <?=
    $form->field($model, 'type')->dropDownList(
        $model->selectTypesAvailable, [
            //'prompt' => 'Не выбрано',
            'class' => 'selectpicker form-control'
        ]
    );
    ?>

    <div class="temp-vars-type mb20 <?php if ($model->type != $model::TYPE_SELECT) echo 'hidden'; ?>" data-type="<?=$model::TYPE_SELECT;?>">
        <label>Список значений <span class="temp-vars__add-select"><i class="fa fa-plus mr5"></i>Добавить</span></label>
        <div class="temp-vars__select-items">
            <div class="temp-vars__select-placeholder <?php if (isset($model->extraData['values']) && $model->extraData['values']) echo 'hidden';?>">Нет значений</div>

            <?php if (isset($model->extraData['values'])) {
                foreach ($model->extraData['values'] as $value) {
                    echo $this->render('_select_row', [
                            'value' => $value
                    ]);
                }
            }?>
        </div>
    </div>

    <div class="">
        <button type="submit" class="btn btn-sm btn-primary">Сохранить</button>
        <span class="btn btn-sm ml10 btn-default js-add-cancel">Назад</span>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<style>
    .temp-vars__add-select {
        display: inline-block;
        border-bottom: 1px dashed;
        color: #4a6484;
        margin-left: 5px;
        cursor: pointer;
    }

    .temp-vars__select-placeholder {
        color: #666;
        padding: 5px 15px;
        background: #eee;
    }

    .temp-vars__select-item {
        padding-right: 35px;
        position: relative;
    }

    .temp-vars__select-item-remove {
        position: absolute;
        width: 30px;
        height: 30px;
        top: 0px;
        right: 0px;
        border: 1px #edeff3 solid;
        cursor: pointer;
        font: normal normal normal 14px/1 FontAwesome;
        color: #d4e0f0;
    }

    .temp-vars__select-item-remove:hover {
        color: #4a6484;
    }

    .temp-vars__select-item-remove:before {
        content: "\f00d";
        margin: 7px 0px 0px 8px;
        display: block;
    }
</style>

<script>
    $(document).ready(function () {

        $('#<?=$formId;?> .selectpicker').selectpicker();

        $('#<?=$formId;?>').on('submit', function (e) {
            e.stopPropagation();
        });

        $('#<?= $formId; ?>').on('afterValidate', function (event, attribute, messages) {
            event.stopPropagation();
        });

        $('#<?=$formId;?>').on('beforeSubmit', function (e) {
            var d = $(this).serialize() + '&_sended=1';
            $('.template-vars').addClass('block__loading');
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                data: d,
                success: function (html) {
                    $('.template-vars').html(html).removeClass('block__loading');
                }
            });
            e.stopPropagation();

            return false;
        });

        $('.js-add-cancel').on('click', function () {
            $('.template-vars').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['template/var-list', 'id' => $model->template_id, 'customTab' => true]);?>',
                success: function (html) {
                    $('.template-vars').html(html).removeClass('block__loading');
                }
            });
        });

        $('#templatevar-type').on('change', function () {
            var v = $(this).val();
            $('.temp-vars-type').addClass('hidden');
            var $ctr = $('.temp-vars-type[data-type="' + v + '"]');
            if ($ctr.length) {
                $ctr.removeClass('hidden');
            }
        });

        $('.temp-vars__add-select').on('click', function () {
            var $ctr = $(this).closest('.temp-vars-type');
            $ctr.addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['template/var-add-select']);?>',
                success: function (html) {
                    $('.temp-vars__select-placeholder').addClass('hidden');
                    $('.temp-vars__select-items').append(html);
                    $ctr.removeClass('block__loading');
                }
            })
        });

        $(document).on('click', '.temp-vars__select-item-remove', function(){
            $(this).closest('.temp-vars__select-item').remove();
        });
    });
</script>