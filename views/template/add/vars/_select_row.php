<?php

use yii\helpers\Html;

$uid = uniqid();
?>

<div class="temp-vars__select-item" id="item-<?= $uid; ?>">
    <div class="form-group mb10 field-extra-select-<?= $uid; ?>">
        <?=
        Html::input('text', 'extra[select][' . $uid . ']', isset($value) ? $value : '', [
            'class' => 'form-control input-sm',
            'placeholder' => 'введите значение',
            'id' => 'extra-select-' . $uid
        ]);
        ?>
        <p class="help-block help-block-error"></p>
    </div>
    <div class="temp-vars__select-item-remove" title="Удалить значение"></div>
</div>

<script>
    $(document).ready(function () {
        function <?= 'init_' . $uid; ?>() {
            var n = 'extra-select-<?= $uid; ?>';
            $form.yiiActiveForm('add', {
                id: n,
                name: n,
                container: '.field-' + n,
                input: '#' + n,
                error: '.help-block',
                enableAjaxValidation: true
            });
        }

        var $form = $('#item-<?= $uid; ?>').closest('form');
        if ($form.data('yiiActiveForm')) {
            <?= 'init_' . $uid; ?>();
        } else {
            $form.on('afterInit', function () {
                <?= 'init_' . $uid; ?>();
            });
        }
    });
</script>
