<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<div class="pl20 pr20 pb20" style="min-width: 500px;">
    <h1><?= $widget->title; ?></h1>

    <?php
    $formId = uniqid();
    $form = ActiveForm::begin([
                'method' => 'post',
                'id' => $formId,
                'options' => [
                    'class' => 'pb80',
                ],
                'validateOnType' => true,
                'enableAjaxValidation' => true,
    ]);
    ?>
    
    <div class="youtube-widget">
        <?= $form->field($widget, 'url'); ?>
        <small class="text-muted">
            Пример:<br/>
            https://www.youtube.com/embed/K59KKnIbIaM?autoplay=1&controls=0
        </small>
    </div>

    <input type="hidden" name="_from_form" value="1"/>

    <div class="form-end mt30" style="left:0;">
        <div class="btn btn-lg btn-primary js-submit-handler">Сохранить</div>
        <span class="ml10 mr10">или</span>
        <a href="#" class="btn btn-sm btn-default js-cancel-handler">Отменить</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {
        $('#<?= $formId; ?> .js-cancel-handler').on('click', function () {
            $(this).closest('.modal-wrap').trigger('close');
            return false;
        });

        $('#<?= $formId; ?> .js-submit-handler').on('click', function () {
            $(this).closest('form').submit();
            return false;
        });
        
        $('#<?= $formId; ?>').on('beforeSubmit', function () {
            var $f = $(this);
            $.ajax({
                url: $f.attr('action'),
                type: 'post',
                data: $f.serialize() + '&_sended=1',
                dataType: 'json',
                success: function (data) {
                    tvData.youtube = data;
                    $f.closest('.modal-wrap').trigger('close');
                }
            });
            return false;
        });
    });
</script>