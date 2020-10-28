<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var $model app\models\TemplateVarGroup */
?>

<div class="temp-vars-inner">
    <h3 class="mt10">
        <?= ($model->id) ? 'Редактировать' : 'Добавить'; ?> группу
    </h3>

    <?php
    $formId = uniqid();
    $form = ActiveForm::begin([
        //'action' => ['template/add-group', 'id' => $model->template_id],
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

    <div class="">
        <button type="submit" class="btn btn-sm btn-primary">Сохранить</button>
        <span class="btn btn-sm ml10 btn-default js-add-cancel">Назад</span>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {

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
    });
</script>