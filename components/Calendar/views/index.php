<?php

use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
?>

<?php
$arConfig = [];
if ($model) {
    $arConfig = [
        'model' => $model,
        'attribute' => $attribute,
    ];
}

$showArrows = (isset($options['showArrows']) && $options['showArrows']);
$dropClass = isset($options['dropClass']) ? $options['dropClass'] : 'dropdown-menu-right';
?>
<div class="form-group">
    <?php if ($showArrows) { ?>
        <div class="range-picker-ctr">
            <div class="control control-left">←</div>
        <?php } ?>

        <div class="input-group input-datepicker">
            <?=
            MaskedInput::widget(\yii\helpers\ArrayHelper::merge($arConfig, [
                        'name' => 'phone',
                        'clientOptions' => [
                            'clearIncomplete' => true,
                            'random' => uniqid(),
                            'alias' =>  'dd.mm.yyyy'
                        ],
                        'options' => ArrayHelper::merge($options, [
                            'class' => 'form-control datepicker',
                            'value' => ($model->$attribute) ? date('d.m.Y', strtotime($model->$attribute)) : '',
                            'readonly' => isset($options['readonly']) && $options['readonly']
                        ]),
            ]));
            ?>

            <div class="dropdown-menu <?= $dropClass; ?>">
                <div class="input-datepicker-ui"></div>
            </div>
            <span class="input-group-addon dropdown-handler" data-toggle="dropdown"></span>
        </div>

        <?php if ($showArrows) { ?>
            <div class="control control-right">→</div>
        </div>
    <?php } ?>
</div>